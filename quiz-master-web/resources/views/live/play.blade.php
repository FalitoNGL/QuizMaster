<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIVE DUEL - {{ $room->room_code }}</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.3/echo.iife.js"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .blob { position: absolute; filter: blur(80px); z-index: -1; opacity: 0.6; animation: move 10s infinite alternate; }
        @keyframes move { from { transform: translate(0, 0) scale(1); } to { transform: translate(20px, -20px) scale(1.1); } }
        
        .correct-answer { background: linear-gradient(135deg, #059669, #10b981) !important; color: white !important; border-color: transparent !important; box-shadow: 0 0 20px rgba(16, 185, 129, 0.4); }
        .wrong-answer { background: linear-gradient(135deg, #dc2626, #ef4444) !important; color: white !important; border-color: transparent !important; box-shadow: 0 0 20px rgba(239, 68, 68, 0.4); }
        .selected { border: 2px solid #3b82f6 !important; background-color: rgba(59, 130, 246, 0.2) !important; }
        .correct-indicator { border: 2px solid #10b981 !important; box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); }
        .disabled-opt { pointer-events: none; opacity: 0.7; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.4s ease-out forwards; }
        @keyframes bounce-in { 0% { transform: scale(0.9); opacity: 0; } 70% { transform: scale(1.05); } 100% { transform: scale(1); opacity: 1; } }
        .animate-bounce-in { animation: bounce-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen relative overflow-hidden flex flex-col font-sans">
    
    <!-- Mesh Background -->
    <div class="blob bg-purple-600 w-96 h-96 rounded-full -top-20 -left-20 mix-blend-multiply opacity-40"></div>
    <div class="blob bg-cyan-600 w-96 h-96 rounded-full -bottom-20 -right-20 mix-blend-multiply opacity-40"></div>
    <div class="blob bg-pink-600 w-80 h-80 rounded-full top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 mix-blend-multiply opacity-30"></div>

    <div class="fixed top-0 w-full glass border-b border-white/10 py-3 z-50 shadow-2xl">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6">
            {{-- Brand Logo --}}
            <div class="absolute left-6 top-1/2 -translate-y-1/2 hidden xl:flex items-center gap-2 pointer-events-none opacity-50">
                <img src="{{ asset('logo.svg') }}" alt="Logo" class="w-6 h-6">
                <span class="font-bold text-sm tracking-tight"><span class="text-white">Quiz</span><span class="text-blue-400">Master</span></span>
            </div>

            <div class="flex items-center gap-4 w-1/3">
                <div class="relative">
                    <div class="absolute -inset-1 bg-blue-500 rounded-full blur opacity-50"></div>
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $room->host->name }}" class="relative w-12 h-12 rounded-full border-2 border-white bg-slate-800">
                </div>
                <div>
                    <div class="text-[10px] text-blue-400 font-black uppercase tracking-widest">HOST</div>
                    <div class="text-3xl font-black font-mono leading-none" id="score-host">{{ $room->host_score }}</div>
                </div>
            </div>
            
            <div class="text-center w-1/3">
                <div class="inline-flex flex-col items-center">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-1">Status</div>
                    <div class="bg-slate-800/80 px-4 py-1 rounded-full border border-white/10 text-xs font-black animate-pulse text-yellow-400" id="status-text">
                        {{ $room->status == 'waiting' ? 'WAITING' : ($room->status == 'playing' ? 'LIVE DUEL' : 'FINISHED') }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 w-1/3 justify-end text-right">
                <div>
                    <div class="text-[10px] text-pink-400 font-black uppercase tracking-widest">OPPONENT</div>
                    <div class="text-3xl font-black font-mono leading-none" id="score-challenger">{{ $room->challenger_score }}</div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-1 bg-pink-500 rounded-full blur opacity-50"></div>
                    <img id="p2-avatar-img" src="{{ $room->challenger ? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$room->challenger->name : 'https://api.dicebear.com/7.x/avataaars/svg?seed=waiting' }}" class="relative w-12 h-12 rounded-full border-2 border-white bg-slate-800 {{ !$room->challenger ? 'opacity-50' : '' }}">
                </div>
            </div>
        </div>
    </div>

    <div class="flex-grow flex items-center justify-center pt-32 pb-12 px-4 relative z-10">
        
        <div id="waiting-screen" class="{{ $room->status == 'waiting' ? '' : 'hidden' }} text-center max-w-md w-full glass p-10 rounded-[2.5rem] border border-white/10 shadow-2xl animate-bounce-in">
            <h2 class="text-3xl font-black text-white mb-6">Menunggu Lawan...</h2>
            <div class="bg-white/5 p-6 rounded-[2rem] border-2 border-dashed border-white/20 mb-8">
                <span class="text-5xl font-mono font-black text-yellow-400 tracking-[0.8rem] ml-3">{{ $room->room_code }}</span>
            </div>
            <p class="text-slate-400 font-medium mb-6">Bagikan kode di atas ke temanmu untuk bertarung!</p>
            <button onclick="window.location.reload()" class="text-sm font-bold text-blue-400 hover:text-blue-300 transition-colors flex items-center justify-center gap-2 mx-auto">
                <i class="fas fa-sync-alt animate-spin-slow"></i> Refresh Status
            </button>
        </div>

        <div id="game-screen" class="{{ $room->status == 'playing' ? '' : 'hidden' }} w-full max-w-3xl animate-bounce-in">
            <div class="glass p-8 md:p-12 rounded-[3rem] border border-white/10 shadow-2xl relative overflow-hidden bg-slate-800/40">
                
                <div class="flex justify-between items-center mb-10">
                    <span class="bg-blue-600/20 text-blue-400 px-4 py-1.5 rounded-full text-xs font-black border border-blue-500/30 uppercase tracking-widest">
                        QUESTION <span id="q-number">1</span>
                    </span>
                    <div class="flex flex-col items-center">
                        <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1">TIME</div>
                        <div class="w-16 h-16 rounded-full border-4 border-yellow-500/20 flex items-center justify-center relative">
                            <div class="absolute inset-0 border-4 border-yellow-500 rounded-full border-t-transparent animate-spin-slow opacity-20"></div>
                            <span class="text-3xl font-black font-mono text-yellow-400" id="timer">{{ $room->duration }}</span>
                        </div>
                    </div>
                </div>
                
                <div id="media-container" class="hidden mb-10 flex flex-col items-center justify-center">
                    <img id="q-image" class="max-h-64 w-full object-cover rounded-[2rem] border-2 border-white/10 mb-4 hidden shadow-2xl">
                    <audio id="q-audio" controls class="w-full hidden glass rounded-full p-2"></audio>
                </div>

                <div class="min-h-[100px] flex flex-col items-center justify-center text-center mb-10">
                    <h2 id="q-text" class="text-2xl md:text-4xl font-black leading-tight text-white tracking-tight">Memuat Pertanyaan...</h2>
                </div>
                
                <div id="options" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                
                <button id="btn-confirm" onclick="submitComplexAnswer()" disabled class="hidden w-full mt-8 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white py-5 rounded-2xl font-black text-xl transition-all shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] active:scale-95 uppercase tracking-widest">
                    SUBMIT ANSWER <i class="fas fa-paper-plane ml-2"></i>
                </button>

                <div id="feedback-area" class="hidden mt-10 p-6 glass rounded-2xl border-l-8 border-yellow-500 animate-slide-up relative z-20 shadow-2xl">
                    <h4 class="font-black text-yellow-400 text-sm mb-2 flex items-center gap-2 uppercase tracking-widest">
                        <i class="fas fa-bolt"></i> INSIGHTS:
                    </h4>
                    <p id="explanation-text" class="text-slate-200 font-medium leading-relaxed"></p>
                    <div id="reference-text" class="text-[10px] text-slate-500 font-bold italic mt-4 border-t border-white/5 pt-3 uppercase tracking-tighter"></div>
                    <div class="mt-4 flex items-center justify-end gap-2 text-[10px] font-black text-blue-400 uppercase tracking-widest">
                        <span class="animate-pulse">NEXT IN 3S</span>
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div id="result-screen" class="{{ $room->status == 'finished' ? '' : 'hidden' }} text-center max-w-lg w-full glass p-12 rounded-[3.5rem] border border-white/10 shadow-2xl animate-bounce-in">
            <div id="result-icon-container" class="mb-8 inline-block relative">
                <div class="absolute -inset-4 bg-yellow-500 rounded-full blur-2xl opacity-20 animate-pulse"></div>
                <div class="relative text-7xl" id="result-emoji">🏁</div>
            </div>
            
            <h1 id="result-title" class="text-5xl font-black mb-4 text-white tracking-tighter uppercase">GAME OVER</h1>
            <p id="result-message" class="text-slate-400 text-lg font-medium mb-10">Berikut hasil pertempuranmu!</p>
            
            <div class="bg-white/5 p-8 rounded-[2.5rem] mb-10 grid grid-cols-1 gap-6 border border-white/10 relative overflow-hidden">
                <div class="flex justify-between items-center px-4">
                    <div class="text-left">
                        <div class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-1">YOUR IQ</div>
                        <div class="text-5xl font-mono font-black text-white" id="final-my-score">0</div>
                    </div>
                    <div class="h-12 w-px bg-white/10 hidden md:block"></div>
                    <div class="text-right">
                        <div class="text-[10px] text-pink-400 font-black uppercase tracking-widest mb-1">ENEMY IQ</div>
                        <div class="text-5xl font-mono font-black text-white/50" id="final-enemy-score">0</div>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('menu') }}" class="w-full bg-white text-slate-900 border-2 border-white hover:bg-transparent hover:text-white py-4 rounded-2xl font-black text-lg transition-all shadow-xl block group">
                <i class="fas fa-home mr-2 group-hover:-translate-y-1 transition-transform inline-block"></i> BACK TO BASE 
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