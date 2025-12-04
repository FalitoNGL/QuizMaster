<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>LIVE DUEL - {{ $room->room_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- LIBRARY WEBSOCKET -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.3/echo.iife.js"></script>

    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        .correct-answer { background-color: #10b981 !important; border-color: #059669 !important; color: white !important; }
        .wrong-answer { background-color: #ef4444 !important; border-color: #dc2626 !important; color: white !important; opacity: 0.9; }
        .correct-indicator { border: 2px solid #10b981 !important; box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); position: relative; }
        .correct-indicator::after { content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; right: 15px; color: #10b981; }
        .disabled-opt { pointer-events: none; opacity: 0.7; }
        
        /* Animasi */
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.4s ease-out forwards; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen font-sans overflow-hidden flex flex-col">

    <!-- HEADER STATUS -->
    <div class="fixed top-0 w-full bg-slate-800/90 backdrop-blur border-b border-slate-700 p-4 z-50 shadow-lg">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            
            <!-- Player 1 (Host) -->
            <div class="flex items-center gap-3 w-1/3">
                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-xl font-bold border-2 border-white shadow-lg relative">
                    {{ substr($room->host->name, 0, 1) }}
                </div>
                <div>
                    <div class="text-[10px] text-blue-400 font-bold uppercase tracking-wider">HOST {{ Auth::id() == $room->host_id ? '(ANDA)' : '' }}</div>
                    <div class="text-2xl font-bold font-mono leading-none" id="score-host">{{ $room->host_score }}</div>
                </div>
            </div>

            <!-- Info Tengah -->
            <div class="text-center w-1/3">
                <div class="inline-block bg-slate-900/50 px-4 py-1 rounded-full text-xs font-mono text-slate-400 mb-1 border border-slate-700">
                    KODE: <span class="text-yellow-400 font-bold select-all cursor-pointer">{{ $room->room_code }}</span>
                </div>
                <div class="text-white font-bold text-sm md:text-base animate-pulse" id="status-text">
                    {{ $room->status == 'waiting' ? 'MENUNGGU...' : ($room->status == 'playing' ? 'LIVE' : 'SELESAI') }}
                </div>
            </div>

            <!-- Player 2 (Challenger) -->
            <div class="flex items-center gap-3 w-1/3 justify-end text-right">
                <div>
                    <div class="text-[10px] text-red-400 font-bold uppercase tracking-wider">LAWAN {{ Auth::id() == $room->challenger_id ? '(ANDA)' : '' }}</div>
                    <div class="text-2xl font-bold font-mono leading-none" id="score-challenger">{{ $room->challenger_score }}</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-xl font-bold border-2 border-white shadow-lg">
                    <span id="p2-avatar">{{ $room->challenger ? substr($room->challenger->name, 0, 1) : '?' }}</span>
                </div>
            </div>

        </div>
    </div>

    <!-- AREA UTAMA -->
    <div class="flex-grow flex items-center justify-center pt-24 pb-8 px-4">
        
        <!-- 1. LAYAR MENUNGGU (LOBBY) -->
        <div id="waiting-screen" class="{{ $room->status == 'waiting' ? '' : 'hidden' }} text-center max-w-md w-full bg-slate-800 p-8 rounded-2xl border border-slate-700 shadow-2xl">
            <div class="relative w-24 h-24 mx-auto mb-6">
                <div class="absolute inset-0 border-4 border-slate-600 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-t-yellow-500 rounded-full animate-spin"></div>
                <i class="fas fa-gamepad absolute inset-0 flex items-center justify-center text-3xl text-slate-500"></i>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Menunggu Lawan...</h2>
            <div class="bg-slate-900 p-4 rounded-xl border border-dashed border-slate-600 mb-6">
                <span class="text-4xl font-mono font-bold text-yellow-400 tracking-widest">{{ $room->room_code }}</span>
            </div>
            <div class="text-sm text-gray-400 border-t border-gray-700 pt-4">
                Info: {{ $room->total_questions }} Soal | {{ $room->duration > 0 ? $room->duration . ' Detik' : 'Tanpa Waktu' }}
            </div>
            <button onclick="window.location.reload()" class="mt-4 text-xs text-blue-400 underline hover:text-blue-300">Refresh jika macet</button>
        </div>

        <!-- 2. LAYAR GAMEPLAY -->
        <div id="game-screen" class="{{ $room->status == 'playing' ? '' : 'hidden' }} w-full max-w-3xl">
            <div class="bg-slate-800 p-6 md:p-10 rounded-3xl border border-slate-700 shadow-2xl relative overflow-hidden">
                
                <!-- Info Soal & Timer -->
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <span class="bg-blue-600/20 text-blue-300 px-3 py-1 rounded-lg text-xs font-bold border border-blue-500/30">
                        SOAL <span id="q-number">1</span> / {{ $questions->count() }}
                    </span>
                    <div class="text-center">
                        <div class="text-xs text-slate-400 mb-1">WAKTU</div>
                        <span class="text-3xl font-mono font-bold text-yellow-400" id="timer">
                            {{ $room->duration > 0 ? $room->duration : '∞' }}
                        </span>
                    </div>
                </div>
                
                <!-- Teks Soal & Media -->
                <div class="min-h-[100px] flex flex-col items-center justify-center text-center mb-8 relative z-10">
                    <div id="media-container" class="mb-4 hidden w-full">
                        <img id="q-image" class="w-full h-48 object-cover rounded-xl border border-slate-600 mb-2 hidden shadow-md">
                        <audio id="q-audio" controls class="w-full hidden"></audio>
                    </div>
                    <h2 id="q-text" class="text-xl md:text-3xl font-bold leading-relaxed text-white">Memuat Soal...</h2>
                </div>
                
                <!-- Opsi Jawaban -->
                <div id="options" class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10"></div>

                <!-- AREA PEMBAHASAN (Muncul setelah jawab) -->
                <div id="feedback-area" class="hidden mt-6 p-4 bg-slate-900 rounded-xl border-l-4 border-yellow-500 animate-slide-up relative z-20 shadow-inner">
                    <h4 class="font-bold text-yellow-400 mb-1 flex items-center gap-2">
                        <i class="fas fa-lightbulb"></i> Pembahasan:
                    </h4>
                    <p id="explanation-text" class="text-sm text-slate-300 mb-2 leading-relaxed"></p>
                    <div id="reference-text" class="text-xs text-slate-500 italic border-t border-slate-800 pt-2"></div>
                    <div class="mt-2 text-xs text-gray-500 text-right">Lanjut otomatis dalam 3 detik...</div>
                </div>
            </div>
        </div>

        <!-- 3. LAYAR HASIL -->
        <div id="result-screen" class="{{ $room->status == 'finished' ? '' : 'hidden' }} text-center max-w-lg w-full bg-slate-800 p-10 rounded-3xl border border-slate-600 shadow-2xl animate-bounce-in">
            <div id="result-icon" class="text-6xl mb-4">🏆</div>
            <h1 id="result-title" class="text-4xl font-extrabold mb-2 text-yellow-400">GAME SELESAI</h1>
            <p id="result-message" class="text-slate-300 text-lg mb-8">Menunggu hasil...</p>
            
            <div class="bg-slate-900/50 p-4 rounded-xl mb-6 grid grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-slate-500 uppercase">Skor Anda</div>
                    <div class="text-2xl font-mono font-bold text-white" id="final-my-score">0</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">Skor Lawan</div>
                    <div class="text-2xl font-mono font-bold text-white" id="final-enemy-score">0</div>
                </div>
            </div>

            <a href="{{ route('menu') }}" class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 px-8 py-3 rounded-xl font-bold text-white shadow-lg transition block">
                Kembali ke Menu
            </a>
        </div>

    </div>

    <!-- LOGIKA JAVASCRIPT -->
    <script>
        const roomCode = "{{ $room->room_code }}";
        const myId = {{ Auth::id() }};
        const questions = @json($questions ?? []); 
        const durationPerQuestion = {{ $room->duration }}; // Ambil dari DB
        
        let currentIdx = 0;
        let timeLeft = durationPerQuestion;
        let timerInterval;
        let isAnswered = false;

        // --- 1. SETUP WEBSOCKET ---
        try {
            if (typeof Echo !== 'undefined') {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '{{ env("REVERB_APP_KEY", "app-key") }}',
                    wsHost: window.location.hostname,
                    wsPort: 8080, wssPort: 8080, forceTLS: false,
                    enabledTransports: ['ws', 'wss'],
                    cluster: 'mt1'
                });

                window.Echo.channel('game.' + roomCode).listen('.GameUpdated', (e) => {
                    console.log('Update:', e);
                    document.getElementById('score-host').innerText = e.host_score;
                    document.getElementById('score-challenger').innerText = e.challenger_score;

                    if (e.status === 'playing') {
                        if(e.challenger_id) document.getElementById('p2-avatar').innerText = "P2";
                        if (!document.getElementById('waiting-screen').classList.contains('hidden')) startGame();
                    }
                    if (e.status === 'finished') showResultScreen(e);
                });
            }
        } catch (error) { console.error("WebSocket error", error); }

        // --- 2. GAME LOGIC ---

        function startGame() {
            document.getElementById('waiting-screen').classList.add('hidden');
            document.getElementById('game-screen').classList.remove('hidden');
            document.getElementById('status-text').innerText = "DUEL SEDANG BERLANGSUNG!";
            
            if (!questions || questions.length === 0) {
                document.getElementById('q-text').innerText = "Error: Kategori ini tidak memiliki soal! Admin harus input soal dulu.";
                return;
            }
            loadQuestion();
        }

        function loadQuestion() {
            if (currentIdx >= questions.length) {
                finishGameTrigger();
                return;
            }

            const q = questions[currentIdx];
            isAnswered = false;
            
            // Reset UI
            document.getElementById('q-text').innerText = q.question_text;
            document.getElementById('q-number').innerText = currentIdx + 1;
            document.getElementById('feedback-area').classList.add('hidden');
            document.getElementById('options').classList.remove('disabled-opt');

            // Reset Media
            const imgEl = document.getElementById('q-image');
            const audioEl = document.getElementById('q-audio');
            const mediaCont = document.getElementById('media-container');
            imgEl.classList.add('hidden'); audioEl.classList.add('hidden'); mediaCont.classList.add('hidden');
            
            if(q.image_path) { 
                imgEl.src = "/storage/" + q.image_path; 
                imgEl.classList.remove('hidden'); 
                mediaCont.classList.remove('hidden'); 
            }
            if(q.audio_path) { 
                audioEl.src = "/storage/" + q.audio_path; 
                audioEl.classList.remove('hidden'); 
                mediaCont.classList.remove('hidden'); 
            }

            // Reset Timer
            if (durationPerQuestion > 0) {
                timeLeft = durationPerQuestion;
                document.getElementById('timer').innerText = timeLeft;
                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    timeLeft--;
                    document.getElementById('timer').innerText = timeLeft;
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        handleTimeUp(); // Waktu Habis
                    }
                }, 1000);
            } else {
                document.getElementById('timer').innerText = "∞";
                clearInterval(timerInterval);
            }

            // Render Opsi
            const optsDiv = document.getElementById('options');
            optsDiv.innerHTML = '';

            if (q.options) {
                [...q.options].sort(() => Math.random() - 0.5).forEach(opt => {
                    const btn = document.createElement('button');
                    btn.className = 'w-full text-left p-5 rounded-xl bg-slate-700 hover:bg-slate-600 border-2 border-transparent transition font-bold text-lg flex justify-between items-center group option-btn shadow-sm';
                    btn.dataset.correct = opt.is_correct;
                    btn.innerHTML = `<span>${opt.option_text}</span>`;
                    
                    btn.onclick = () => {
                        if (!isAnswered) submitAnswer(opt.is_correct, btn);
                    };
                    optsDiv.appendChild(btn);
                });
            }
        }

        function submitAnswer(isCorrect, btnElement) {
            isAnswered = true;
            clearInterval(timerInterval);

            const q = questions[currentIdx];
            const allBtns = document.querySelectorAll('.option-btn');

            // 1. Feedback Visual
            if (isCorrect) {
                if (btnElement) btnElement.classList.add('correct-answer');
                
                // Kirim Skor (100 + Sisa Waktu)
                const points = 100 + (durationPerQuestion > 0 ? timeLeft : 0);
                sendScore(points);
            } else {
                if (btnElement) btnElement.classList.add('wrong-answer');
                
                // Tunjukkan Jawaban Benar
                allBtns.forEach(b => {
                    if (b.dataset.correct == "1") b.classList.add('correct-indicator');
                });
            }

            // Disable tombol
            allBtns.forEach(b => b.disabled = true);
            document.getElementById('options').classList.add('disabled-opt');

            // 2. Tampilkan Penjelasan
            document.getElementById('explanation-text').innerText = q.explanation || "Tidak ada pembahasan khusus.";
            document.getElementById('reference-text').innerText = q.reference ? "📚 Sumber: " + q.reference : "";
            document.getElementById('feedback-area').classList.remove('hidden');
            
            // Scroll agar terlihat
            document.getElementById('feedback-area').scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            // 3. Auto Next dalam 3 Detik (Agar Duel tetap cepat)
            setTimeout(() => {
                currentIdx++;
                loadQuestion();
            }, 3000);
        }

        function handleTimeUp() {
            isAnswered = true;
            const allBtns = document.querySelectorAll('.option-btn');
            allBtns.forEach(b => {
                if (b.dataset.correct == "1") b.classList.add('correct-indicator');
                b.disabled = true;
            });
            document.getElementById('options').classList.add('disabled-opt');

            const q = questions[currentIdx];
            document.getElementById('explanation-text').innerText = "Waktu Habis! " + (q.explanation || "");
            document.getElementById('feedback-area').classList.remove('hidden');

            setTimeout(() => {
                currentIdx++;
                loadQuestion();
            }, 3000);
        }

        function nextQuestion() {
            currentIdx++;
            loadQuestion();
        }

        function sendScore(points) {
            fetch("{{ route('live.score') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ room_code: roomCode, points: points })
            }).catch(err => {});
        }

        function finishGameTrigger() {
            document.getElementById('game-screen').classList.add('hidden');
            document.getElementById('result-screen').classList.remove('hidden');
            document.getElementById('result-message').innerText = "Menunggu server menghitung hasil...";
            
            fetch("{{ route('live.finish') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ room_code: roomCode })
            });
        }

        function showResultScreen(data) {
            document.getElementById('waiting-screen').classList.add('hidden');
            document.getElementById('game-screen').classList.add('hidden');
            document.getElementById('result-screen').classList.remove('hidden');

            const amIHost = myId == {{ $room->host_id }};
            const myScore = amIHost ? data.host_score : data.challenger_score;
            const enemyScore = amIHost ? data.challenger_score : data.host_score;

            document.getElementById('final-my-score').innerText = myScore;
            document.getElementById('final-enemy-score').innerText = enemyScore;

            const title = document.getElementById('result-title');
            const msg = document.getElementById('result-message');
            const icon = document.getElementById('result-icon');

            if (data.winner_id == myId) {
                icon.innerText = "🏆";
                title.innerText = "KAMU MENANG!";
                title.className = "text-4xl font-extrabold mb-2 text-green-400";
                msg.innerText = "Selamat! Kamu mengalahkan lawanmu.";
            } else if (data.winner_id === null) {
                icon.innerText = "🤝";
                title.innerText = "SERI!";
                title.className = "text-4xl font-extrabold mb-2 text-blue-400";
                msg.innerText = "Skor kalian sama kuat.";
            } else {
                icon.innerText = "💀";
                title.innerText = "KAMU KALAH";
                title.className = "text-4xl font-extrabold mb-2 text-red-500";
                msg.innerText = "Jangan menyerah, coba lagi!";
            }
        }

        if ("{{ $room->status }}" === 'playing') startGame();
        if ("{{ $room->status }}" === 'finished') finishGameTrigger(); 
    </script>
</body>
</html>