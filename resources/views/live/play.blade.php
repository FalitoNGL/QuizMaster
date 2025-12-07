<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>LIVE DUEL - {{ $room->room_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.3/echo.iife.js"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        .correct-answer { background-color: #10b981 !important; border-color: #059669 !important; color: white !important; }
        .wrong-answer { background-color: #ef4444 !important; border-color: #dc2626 !important; color: white !important; opacity: 0.9; }
        .selected { border: 2px solid #3b82f6 !important; background-color: rgba(59, 130, 246, 0.2) !important; }
        .correct-indicator { border: 2px solid #10b981 !important; box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); position: relative; }
        .disabled-opt { pointer-events: none; opacity: 0.7; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.4s ease-out forwards; }
        button:disabled { cursor: not-allowed; opacity: 0.5; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen font-sans overflow-hidden flex flex-col">

    <div class="fixed top-0 w-full bg-slate-800/90 backdrop-blur border-b border-slate-700 p-4 z-50 shadow-lg">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3 w-1/3">
                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-xl font-bold border-2 border-white shadow-lg">{{ substr($room->host->name, 0, 1) }}</div>
                <div>
                    <div class="text-[10px] text-blue-400 font-bold uppercase">HOST</div>
                    <div class="text-2xl font-bold font-mono" id="score-host">{{ $room->host_score }}</div>
                </div>
            </div>
            
            <div class="text-center w-1/3">
                <div class="text-white font-bold text-sm animate-pulse" id="status-text">
                    {{ $room->status == 'waiting' ? 'MENUNGGU...' : ($room->status == 'playing' ? 'LIVE' : 'SELESAI') }}
                </div>
            </div>

            <div class="flex items-center gap-3 w-1/3 justify-end text-right">
                <div>
                    <div class="text-[10px] text-red-400 font-bold uppercase">LAWAN</div>
                    <div class="text-2xl font-bold font-mono" id="score-challenger">{{ $room->challenger_score }}</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-xl font-bold border-2 border-white shadow-lg">
                    <span id="p2-avatar">{{ $room->challenger ? substr($room->challenger->name, 0, 1) : '?' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-grow flex items-center justify-center pt-24 pb-8 px-4">
        
        <div id="waiting-screen" class="{{ $room->status == 'waiting' ? '' : 'hidden' }} text-center max-w-md w-full bg-slate-800 p-8 rounded-2xl border border-slate-700 shadow-2xl">
            <h2 class="text-2xl font-bold text-white mb-2">Menunggu Lawan...</h2>
            <div class="bg-slate-900 p-4 rounded-xl border border-dashed border-slate-600 mb-6">
                <span class="text-4xl font-mono font-bold text-yellow-400 tracking-widest">{{ $room->room_code }}</span>
            </div>
            <p class="text-slate-400 text-sm mb-4">Bagikan kode ini ke temanmu.</p>
            <button onclick="window.location.reload()" class="text-sm text-blue-400 underline hover:text-blue-300">Refresh Status</button>
        </div>

        <div id="game-screen" class="{{ $room->status == 'playing' ? '' : 'hidden' }} w-full max-w-3xl">
            <div class="bg-slate-800 p-6 md:p-10 rounded-3xl border border-slate-700 shadow-2xl relative overflow-hidden">
                
                <div class="flex justify-between items-start mb-6">
                    <span class="bg-blue-600/20 text-blue-300 px-3 py-1 rounded-lg text-xs font-bold border border-blue-500/30">
                        SOAL <span id="q-number">1</span>
                    </span>
                    <div class="text-center">
                        <div class="text-xs text-slate-400 mb-1">WAKTU</div>
                        <span class="text-3xl font-mono font-bold text-yellow-400" id="timer">{{ $room->duration }}</span>
                    </div>
                </div>
                
                <div id="media-container" class="hidden mb-6 flex flex-col items-center justify-center">
                    <img id="q-image" class="max-h-56 w-auto object-cover rounded-xl border border-slate-600 mb-3 hidden shadow-md">
                    <audio id="q-audio" controls class="w-full hidden"></audio>
                </div>

                <div class="min-h-[60px] flex flex-col items-center justify-center text-center mb-8">
                    <h2 id="q-text" class="text-xl md:text-3xl font-bold leading-relaxed text-white">Memuat...</h2>
                </div>
                
                <div id="options" class="grid gap-3"></div>
                
                <button id="btn-confirm" onclick="submitComplexAnswer()" disabled class="hidden w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                    JAWAB SEKARANG <i class="fas fa-check ml-2"></i>
                </button>

                <div id="feedback-area" class="hidden mt-6 p-4 bg-slate-900 rounded-xl border-l-4 border-yellow-500 animate-slide-up relative z-20 shadow-inner">
                    <h4 class="font-bold text-yellow-400 mb-1 flex items-center gap-2">
                        <i class="fas fa-lightbulb"></i> Pembahasan:
                    </h4>
                    <p id="explanation-text" class="text-sm text-slate-300 mb-2 leading-relaxed"></p>
                    <div id="reference-text" class="text-xs text-slate-500 italic border-t border-slate-800 pt-2"></div>
                    <div class="mt-2 text-xs text-gray-500 text-right animate-pulse">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Lanjut otomatis dalam 3 detik...
                    </div>
                </div>
            </div>
        </div>

        <div id="result-screen" class="{{ $room->status == 'finished' ? '' : 'hidden' }} text-center max-w-lg w-full bg-slate-800 p-10 rounded-3xl border border-slate-600 shadow-2xl">
            <h1 id="result-title" class="text-4xl font-extrabold mb-2 text-yellow-400">GAME SELESAI</h1>
            <p id="result-message" class="text-slate-300 text-lg mb-8">Menunggu hasil...</p>
            
            <div class="bg-slate-900/50 p-6 rounded-xl mb-6 grid grid-cols-2 gap-4 border border-slate-700">
                <div class="border-r border-slate-700 pr-2">
                    <div class="text-xs text-slate-500 uppercase font-bold mb-1">Skor Anda</div>
                    <div class="text-4xl font-mono font-bold text-blue-400" id="final-my-score">0</div>
                </div>
                <div class="pl-2">
                    <div class="text-xs text-slate-500 uppercase font-bold mb-1">Skor Lawan</div>
                    <div class="text-4xl font-mono font-bold text-red-400" id="final-enemy-score">0</div>
                </div>
            </div>
            
            <a href="{{ route('menu') }}" class="bg-blue-600 hover:bg-blue-500 px-8 py-3 rounded-xl font-bold text-white transition block shadow-lg">
                <i class="fas fa-home mr-2"></i> Kembali ke Menu
            </a>
        </div>
    </div>

    <audio id="sfx-correct"><source src="https://assets.mixkit.co/active_storage/sfx/2000/2000-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-wrong"><source src="https://assets.mixkit.co/active_storage/sfx/2003/2003-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-finish"><source src="https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3" type="audio/mpeg"></audio>

    <script>
        const roomCode = "{{ $room->room_code }}";
        const myId = {{ Auth::id() }};
        const questions = @json($questions ?? []); 
        const durationPerQuestion = {{ $room->duration }};
        
        let currentIdx = 0;
        let timeLeft = durationPerQuestion;
        let timerInterval;
        let isAnswered = false;

        // SFX ELEMENTS
        const sfxCorrect = document.getElementById('sfx-correct');
        const sfxWrong = document.getElementById('sfx-wrong');
        const sfxFinish = document.getElementById('sfx-finish');

        function playSound(el) { 
            el.currentTime = 0; 
            el.play().catch(e => console.log("Audio play failed", e)); 
        }

        // FUNGSI PENGACAK (Shuffle)
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        // WEBSOCKET CONFIGURATION
        try {
            if (typeof Echo !== 'undefined') {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '{{ config("broadcasting.connections.pusher.key") }}',
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                    forceTLS: true
                });

                window.Echo.channel('game.' + roomCode)
                    .listen('.GameUpdated', (e) => {
                        // Update Header Score
                        document.getElementById('score-host').innerText = e.host_score;
                        document.getElementById('score-challenger').innerText = e.challenger_score;
                        
                        // Cek status game
                        if (e.status === 'playing') {
                            if(e.challenger_id) document.getElementById('p2-avatar').innerText = "P2";
                            // Jika layar menunggu masih aktif, mulai game!
                            if (!document.getElementById('waiting-screen').classList.contains('hidden')) {
                                startGame();
                            }
                        }
                        
                        // Cek jika finish
                        if (e.status === 'finished') {
                            showResultScreen(e);
                        }
                    });
            }
        } catch (error) {
            console.error("WebSocket Error:", error);
        }

        function startGame() {
            document.getElementById('waiting-screen').classList.add('hidden');
            document.getElementById('game-screen').classList.remove('hidden');
            document.getElementById('status-text').innerText = "LIVE DUEL!";
            
            if (!questions || questions.length === 0) {
                alert("Soal tidak ditemukan!");
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
            
            // 1. Reset UI
            document.getElementById('q-number').innerText = currentIdx + 1;
            document.getElementById('q-text').innerText = q.question_text;
            document.getElementById('feedback-area').classList.add('hidden');
            document.getElementById('options').classList.remove('disabled-opt');
            document.getElementById('btn-confirm').classList.add('hidden');
            document.getElementById('btn-confirm').disabled = true;

            // 2. Handle Media
            const imgEl = document.getElementById('q-image');
            const audioEl = document.getElementById('q-audio');
            const mediaCont = document.getElementById('media-container');
            
            imgEl.classList.add('hidden'); 
            audioEl.classList.add('hidden'); 
            mediaCont.classList.add('hidden');

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

            // 3. Reset Timer
            timeLeft = durationPerQuestion;
            document.getElementById('timer').innerText = timeLeft;
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                timeLeft--;
                document.getElementById('timer').innerText = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    handleTimeUp();
                }
            }, 1000);

            // 4. Render Options
            const optsDiv = document.getElementById('options');
            optsDiv.innerHTML = '';
            const displayOptions = shuffleArray([...q.options]);

            // --- A. SINGLE CHOICE ---
            if (q.type === 'single') {
                displayOptions.forEach(opt => {
                    const btn = document.createElement('button');
                    btn.className = 'w-full text-left p-5 rounded-xl bg-slate-700 hover:bg-slate-600 border-2 border-transparent transition font-bold text-lg flex justify-between items-center group option-btn shadow-md';
                    btn.innerHTML = `<span>${opt.option_text}</span>`;
                    btn.onclick = () => submitSingle(opt, btn);
                    optsDiv.appendChild(btn);
                });
            } 
            
            // --- B. MULTIPLE CHOICE ---
            else if (q.type === 'multiple') {
                document.getElementById('btn-confirm').classList.remove('hidden');
                displayOptions.forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'option-item w-full text-left p-4 rounded-xl border border-slate-600 bg-slate-800 hover:bg-slate-700 transition cursor-pointer flex items-center gap-3 shadow-sm';
                    div.innerHTML = `<div class="w-6 h-6 border-2 border-slate-400 rounded flex items-center justify-center"><i class="fas fa-check text-transparent transition check-icon"></i></div> <span>${opt.option_text}</span>`;
                    div.dataset.id = opt.id; 
                    div.dataset.correct = opt.is_correct;
                    div.onclick = () => {
                        div.classList.toggle('selected');
                        div.querySelector('.check-icon').classList.toggle('text-transparent');
                        div.querySelector('.check-icon').classList.toggle('text-blue-500');
                        validateComplexAnswer();
                    };
                    optsDiv.appendChild(div);
                });
            } 
            
            // --- C. ORDERING ---
            else if (q.type === 'ordering') {
                document.getElementById('btn-confirm').classList.remove('hidden');
                displayOptions.forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'ordering-item w-full p-3 rounded-xl border border-slate-600 bg-slate-800 mb-2 flex justify-between items-center shadow-sm';
                    div.dataset.id = opt.id; 
                    div.dataset.order = opt.correct_order;
                    div.innerHTML = `<span>${opt.option_text}</span><div class="flex flex-col gap-1"><button onclick="moveItem(this, -1)" class="p-1 bg-slate-700 rounded hover:bg-slate-600 text-xs">▲</button><button onclick="moveItem(this, 1)" class="p-1 bg-slate-700 rounded hover:bg-slate-600 text-xs">▼</button></div>`;
                    optsDiv.appendChild(div);
                });
                validateComplexAnswer(); 
            } 
            
            // --- D. MATCHING ---
            else if (q.type === 'matching') {
                document.getElementById('btn-confirm').classList.remove('hidden');
                const rightOptions = shuffleArray(q.options.map(o => ({id: o.id, text: o.matching_pair})));
                
                displayOptions.forEach(opt => {
                    const row = document.createElement('div');
                    row.className = 'p-3 rounded-xl border border-slate-600 bg-slate-800 mb-2 shadow-sm';
                    let selectHtml = `<select class="matching-select w-full mt-2 bg-slate-700 p-2 rounded border border-slate-500 focus:outline-none text-white" data-left-id="${opt.id}" data-correct-pair="${opt.matching_pair}" onchange="validateComplexAnswer()"><option value="">-- Pilih --</option>`;
                    rightOptions.forEach(ro => { selectHtml += `<option value="${ro.text}">${ro.text}</option>`; });
                    selectHtml += `</select>`;
                    row.innerHTML = `<div class="font-bold mb-1 border-b border-slate-700 pb-1">${opt.option_text}</div> ${selectHtml}`;
                    optsDiv.appendChild(row);
                });
            }
        }

        // VALIDASI INPUT KOMPLEKS (Frontend Only - For Button Enable)
        function validateComplexAnswer() {
            const q = questions[currentIdx];
            const btn = document.getElementById('btn-confirm');
            let isValid = false;
            
            if (q.type === 'multiple') {
                if (document.querySelectorAll('.option-item.selected').length > 0) isValid = true;
            } else if (q.type === 'ordering') {
                isValid = true;
            } else if (q.type === 'matching') {
                const selects = document.querySelectorAll('.matching-select');
                isValid = Array.from(selects).every(sel => sel.value !== "");
            }
            btn.disabled = !isValid;
        }

        function moveItem(btn, direction) {
            const item = btn.closest('.ordering-item');
            const parent = item.parentNode;
            if (direction === -1 && item.previousElementSibling) parent.insertBefore(item, item.previousElementSibling);
            if (direction === 1 && item.nextElementSibling) parent.insertBefore(item.nextElementSibling, item);
        }

        // SUBMIT SINGLE ANSWER
        function submitSingle(opt, btn) {
            if(isAnswered) return;
            isAnswered = true;
            clearInterval(timerInterval);
            
            // Visual Feedback Lokal
            if (opt.is_correct) {
                btn.classList.add('correct-answer');
                playSound(sfxCorrect); 
                // Kirim Jawaban ke Backend (ID saja)
                submitAnswerToBackend(opt.id);
            } else {
                btn.classList.add('wrong-answer');
                playSound(sfxWrong);
                // Opsional: Kirim juga kalau salah
            }
            finishTurn();
        }

        // SUBMIT COMPLEX ANSWER
        function submitComplexAnswer() {
            if(isAnswered) return;
            isAnswered = true;
            clearInterval(timerInterval);
            
            const q = questions[currentIdx];
            let isCorrect = false;
            let answerData = null; // Data yang akan dikirim ke backend

            // Logic Pengecekan Visual Lokal & Persiapan Data Backend
            if (q.type === 'multiple') {
                const selected = document.querySelectorAll('.option-item.selected');
                const selectedIds = Array.from(selected).map(d => parseInt(d.dataset.id));
                const correctIds = q.options.filter(o => o.is_correct).map(o => o.id);
                
                // Visual Check
                if (selectedIds.length === correctIds.length && selectedIds.every(id => correctIds.includes(id))) isCorrect = true;
                
                document.querySelectorAll('.option-item').forEach(div => {
                    const id = parseInt(div.dataset.id);
                    const isKey = correctIds.includes(id);
                    if(isKey) div.classList.add('correct-indicator');
                });

                answerData = selectedIds; // Kirim array ID
            } 
            else if (q.type === 'ordering') {
                const items = document.querySelectorAll('.ordering-item');
                const userOrder = Array.from(items).map(d => parseInt(d.dataset.id));
                const correctOrder = [...q.options].sort((a,b) => a.correct_order - b.correct_order).map(o => o.id);
                
                if (JSON.stringify(userOrder) === JSON.stringify(correctOrder)) isCorrect = true;
                
                answerData = userOrder; // Kirim array ID terurut
            } 
            else if (q.type === 'matching') {
                const selects = document.querySelectorAll('.matching-select');
                let allCorrect = true;
                const matches = [];

                selects.forEach(sel => {
                    matches.push({
                        left_id: parseInt(sel.dataset.leftId),
                        pair_text: sel.value
                    });

                    if (sel.value === sel.dataset.correctPair) {
                        sel.parentElement.classList.add('correct-answer');
                    } else {
                        sel.parentElement.classList.add('wrong-answer');
                        allCorrect = false;
                    }
                });
                if (allCorrect) isCorrect = true;

                answerData = matches; // Kirim pasangan
            }

            // Kirim ke Backend HANYA JIKA Benar (untuk menambah skor)
            // Atau kirim selalu jika ingin mencatat statistik salah
            if (isCorrect) {
                submitAnswerToBackend(answerData);
                playSound(sfxCorrect);
            } else {
                playSound(sfxWrong);
            }
            finishTurn();
        }

        function finishTurn() {
            document.getElementById('options').classList.add('disabled-opt');
            document.getElementById('btn-confirm').classList.add('hidden');

            const q = questions[currentIdx];
            document.getElementById('explanation-text').innerText = q.explanation || "Tidak ada pembahasan.";
            document.getElementById('reference-text').innerText = q.reference ? "Sumber: " + q.reference : "";
            document.getElementById('feedback-area').classList.remove('hidden');
            document.getElementById('feedback-area').scrollIntoView({ behavior: 'smooth' });

            // Auto Next
            setTimeout(() => {
                currentIdx++;
                loadQuestion();
            }, 3000);
        }

        function handleTimeUp() {
            isAnswered = true;
            playSound(sfxWrong); 
            document.getElementById('options').classList.add('disabled-opt');
            document.getElementById('btn-confirm').classList.add('hidden');
            document.getElementById('explanation-text').innerText = "Waktu Habis!";
            document.getElementById('feedback-area').classList.remove('hidden');
            setTimeout(() => { currentIdx++; loadQuestion(); }, 3000);
        }

        // --- CORE FUNCTION BARU: KIRIM JAWABAN KE BACKEND ---
        function submitAnswerToBackend(answerData) {
            const payload = {
                room_code: roomCode,
                question_id: questions[currentIdx].id,
                time_left: timeLeft,
                answer: answerData 
            };

            fetch("{{ route('live.score') }}", {
                method: "POST", 
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify(payload)
            }).catch(err => console.error("Gagal kirim jawaban:", err));
        }

        function finishGameTrigger() {
            document.getElementById('game-screen').classList.add('hidden');
            document.getElementById('result-screen').classList.remove('hidden');
            playSound(sfxFinish); 
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
            if (data.winner_id == myId) { 
                title.innerText = "🏆 KAMU MENANG!"; 
                title.className = "text-4xl font-extrabold mb-2 text-green-400 animate-bounce"; 
                playSound(sfxFinish); 
            } else if (data.winner_id === null) { 
                title.innerText = "🤝 SERI!"; 
                title.className = "text-4xl font-extrabold mb-2 text-blue-400"; 
            } else { 
                title.innerText = "💀 KAMU KALAH"; 
                title.className = "text-4xl font-extrabold mb-2 text-red-500"; 
            }
        }

        // Init Check
        if ("{{ $room->status }}" === 'playing') startGame();
        if ("{{ $room->status }}" === 'finished') finishGameTrigger(); 
    </script>
</body>
</html>