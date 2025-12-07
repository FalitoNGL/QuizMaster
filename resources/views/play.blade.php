<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        .glass { background: rgba(30, 41, 59, 0.95); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); }
        .correct { background-color: #10b981 !important; border-color: #059669 !important; color: white !important; }
        .wrong { background-color: #ef4444 !important; border-color: #dc2626 !important; color: white !important; opacity: 0.9; }
        .selected { border: 2px solid #3b82f6 !important; background-color: rgba(59, 130, 246, 0.2) !important; }
        .correct-indicator { border: 2px solid #10b981 !important; position: relative; }
        .disabled-opt { pointer-events: none; opacity: 0.8; }
        button:disabled { cursor: not-allowed; opacity: 0.6; filter: grayscale(100%); }
        
        /* Animasi Transisi Halaman */
        .fade-enter { opacity: 0; transform: translateY(10px); }
        .fade-enter-active { opacity: 1; transform: translateY(0); transition: all 0.4s ease-out; }
        .fade-exit { opacity: 1; transform: scale(1); }
        .fade-exit-active { opacity: 0; transform: scale(0.98); transition: all 0.2s ease-in; }

        /* Badge Pop Animation */
        .badge-pop { animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center font-sans p-4 overflow-x-hidden">
    
    <div class="w-full max-w-3xl relative z-10">
        
        <div class="flex justify-between items-center mb-6 glass p-4 rounded-full shadow-lg bg-white/5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center border border-slate-500 shadow-inner">
                    @if($timer > 0) <i class="fas fa-clock text-yellow-400"></i> @else <i class="fas fa-infinity text-blue-400"></i> @endif
                </div>
                <div>
                    <div class="text-[10px] text-slate-400 font-bold tracking-wider">WAKTU</div>
                    <div id="timer-display" class="font-mono text-xl font-bold">{{ $timer > 0 ? $timer : '∞' }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[10px] text-slate-400 font-bold tracking-wider">ESTIMASI SKOR</div>
                <div id="score-display" class="font-mono text-xl font-bold text-blue-400">0</div>
            </div>
        </div>

        <div id="quiz-card" class="glass rounded-3xl p-8 shadow-2xl relative overflow-hidden bg-slate-800/90 border border-slate-700">
            
            <div class="mb-6">
                <div class="flex justify-between items-end mb-2">
                    <span class="inline-block px-3 py-1 rounded-lg bg-blue-500/20 text-blue-300 text-xs font-bold border border-blue-500/30">
                        SOAL <span id="q-number">1</span> / {{ $questions->count() }}
                    </span>
                    <span class="text-xs text-slate-500 font-mono" id="progress-percent">0%</span>
                </div>
                <div class="w-full bg-slate-700/50 h-2 rounded-full overflow-hidden">
                    <div id="progress-bar" class="bg-gradient-to-r from-blue-500 to-cyan-400 h-full rounded-full transition-all duration-700 ease-out shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="width: 0%"></div>
                </div>
            </div>

            <div id="skeleton-loader" class="animate-pulse space-y-4">
                <div class="h-6 bg-slate-700/50 rounded w-3/4"></div>
                <div class="h-4 bg-slate-700/30 rounded w-1/2"></div>
                <div class="grid gap-3 mt-8">
                    <div class="h-16 bg-slate-700/40 rounded-xl"></div>
                    <div class="h-16 bg-slate-700/40 rounded-xl"></div>
                    <div class="h-16 bg-slate-700/40 rounded-xl"></div>
                    <div class="h-16 bg-slate-700/40 rounded-xl"></div>
                </div>
            </div>

            <div id="question-content" class="hidden opacity-0 transform translate-y-4 transition-all duration-500">
                <div class="mb-6">
                    <div id="media-container" class="hidden mb-4 rounded-xl overflow-hidden border border-slate-600 shadow-lg">
                        <img id="q-image" class="w-full h-56 object-cover hidden">
                        <audio id="q-audio" controls class="w-full hidden bg-slate-800"></audio>
                    </div>

                    <h2 id="question-text" class="text-xl md:text-2xl font-bold leading-relaxed text-white"></h2>
                    <p id="instruction-text" class="text-sm text-slate-400 mt-2 italic border-l-4 border-yellow-500 pl-3"></p>
                </div>

                <div id="options-container" class="grid gap-3"></div>

                <button id="btn-confirm" onclick="submitComplexAnswer()" disabled class="hidden w-full mt-8 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white py-3.5 rounded-xl font-bold transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transform active:scale-95">
                    JAWAB SEKARANG <i class="fas fa-check ml-2"></i>
                </button>

                <div id="feedback-area" class="hidden mt-8 p-5 bg-slate-900/80 rounded-xl border-l-4 border-yellow-500 shadow-inner backdrop-blur-sm">
                    <h4 class="font-bold text-yellow-400 mb-2 flex items-center gap-2">
                        <i class="fas fa-lightbulb"></i> Pembahasan
                    </h4>
                    <p id="explanation-text" class="text-sm text-slate-300 mb-3 leading-relaxed"></p>
                    <div class="text-xs text-slate-500 italic border-t border-slate-700/50 pt-2" id="reference-text"></div>
                    
                    <button onclick="nextQuestion()" class="w-full mt-4 bg-slate-700 hover:bg-slate-600 text-white py-3 rounded-lg font-bold transition shadow-lg flex items-center justify-center gap-2 border border-slate-600 group">
                        Lanjut Soal Berikutnya <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="result-card" class="hidden glass rounded-3xl p-8 text-center shadow-2xl bg-slate-800 border border-slate-700">
            <div class="relative inline-block mb-4">
                <div class="absolute inset-0 bg-yellow-500 blur-2xl opacity-20 rounded-full"></div>
                <div class="text-7xl animate-bounce relative z-10" id="grade-emoji">🎉</div>
            </div>

            <h2 class="text-3xl font-bold mb-1 text-white" id="grade-title">Selesai!</h2>
            <p class="text-slate-400 text-sm mb-6" id="grade-subtitle">Kamu telah menyelesaikan kuis ini.</p>

            <div class="grid grid-cols-4 gap-2 mb-8 bg-slate-900/50 p-4 rounded-xl border border-slate-700 text-center">
                <div>
                    <div class="text-[10px] uppercase text-slate-500 font-bold">Skor Akhir</div>
                    <div class="text-xl md:text-2xl font-bold text-yellow-400" id="final-score">0</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase text-slate-500 font-bold">Akurasi</div>
                    <div class="text-xl md:text-2xl font-bold text-blue-400" id="final-percentage">0%</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase text-slate-500 font-bold">Benar</div>
                    <div class="text-xl md:text-2xl font-bold text-green-400" id="final-correct">0</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase text-slate-500 font-bold">Salah</div>
                    <div class="text-xl md:text-2xl font-bold text-red-400" id="final-wrong">0</div>
                </div>
            </div>

            <div id="badge-notification" class="hidden mb-6 bg-gradient-to-r from-yellow-900/20 to-yellow-600/20 p-4 rounded-xl border border-yellow-500/30">
                <h4 class="text-yellow-400 font-bold text-sm mb-2"><i class="fas fa-medal mr-1"></i> Achievement Unlocked!</h4>
                <div id="badge-list" class="flex flex-wrap justify-center gap-2"></div>
            </div>

            <div id="save-section">
                <input type="text" id="player-name" value="{{ session('current_player', '') }}" placeholder="Ketik Nama Kamu" class="w-full bg-slate-700 px-4 py-3 rounded-xl mb-4 text-center text-white border border-slate-600 focus:border-blue-500 outline-none transition placeholder-slate-400">
                <button onclick="saveScore()" id="btn-save" class="w-full bg-green-600 hover:bg-green-500 py-3 rounded-xl font-bold text-white shadow-lg mb-3 transition transform hover:scale-105">
                    <i class="fas fa-save mr-2"></i> Simpan & Lihat Hasil
                </button>
            </div>
            
            <div id="after-save-actions" class="hidden flex flex-col gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <a id="btn-review-link" href="#" class="bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-bold transition text-center shadow-lg flex items-center justify-center">
                        <i class="fas fa-list-check mr-2"></i> Pembahasan
                    </a>
                    <button onclick="generateShareImage()" id="btn-share-img" class="bg-purple-600 hover:bg-purple-500 text-white py-3 rounded-xl font-bold transition shadow-lg flex items-center justify-center">
                        <i class="fas fa-camera mr-2"></i> Share Gambar
                    </button>
                </div>
                <a href="{{ route('menu') }}" class="w-full bg-slate-700 hover:bg-slate-600 text-white py-3 rounded-xl font-bold transition text-center border border-slate-600">
                    Kembali ke Menu
                </a>
            </div>
        </div>
    </div>

    <div id="shareable-ticket" class="fixed left-[-9999px] top-0 w-[400px] bg-gradient-to-br from-slate-800 to-slate-900 p-8 rounded-3xl border-4 border-blue-500/50 text-center shadow-2xl text-white font-sans relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="relative z-10">
            <div class="text-sm uppercase tracking-widest text-blue-400 mb-2 font-bold">Quiz Master Result</div>
            <h1 class="text-3xl font-extrabold mb-1 text-white">{{ $category->name }}</h1>
            <div class="text-6xl mb-4 animate-bounce mt-4" id="share-emoji">🎉</div>
            <h2 class="text-2xl font-bold text-yellow-400 mb-6" id="share-grade-title">LEGENDARY!</h2>
            <div class="bg-slate-900/80 rounded-2xl p-6 border border-slate-700 mb-6">
                <div class="text-sm text-slate-400 uppercase font-bold">Total Skor</div>
                <div class="text-5xl font-extrabold text-white mb-4" id="share-score">0</div>
            </div>
            <div class="text-slate-400 text-sm">
                Pemain: <span class="font-bold text-white" id="share-player-name">-</span>
            </div>
            <div class="mt-4 text-[10px] text-slate-500">
                @ {{ date('d M Y') }} • QuizMaster App
            </div>
        </div>
    </div>

    <div id="share-modal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm animate-fade-up">
        <div class="bg-slate-800 p-6 rounded-3xl max-w-sm w-full text-center border border-slate-700 relative">
            <h3 class="text-xl font-bold text-white mb-4">Siap Dibagikan!</h3>
            <div id="generated-image-container" class="mb-4 rounded-2xl overflow-hidden border-2 border-blue-500/50 shadow-lg"></div>
            <button onclick="closeShareModal()" class="bg-slate-700 hover:bg-slate-600 text-white py-2 px-6 rounded-full font-bold transition w-full">Tutup</button>
        </div>
    </div>

    <audio id="sfx-correct"><source src="https://assets.mixkit.co/active_storage/sfx/2000/2000-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-wrong"><source src="https://assets.mixkit.co/active_storage/sfx/2003/2003-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-finish"><source src="https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-badge"><source src="https://assets.mixkit.co/active_storage/sfx/2019/2019-preview.mp3" type="audio/mpeg"></audio>

    <script>
        const questions = @json($questions);
        const configTimer = {{ $timer }};
        let currentIdx = 0;
        let estimatedScore = 0;
        let timeLeft = configTimer;
        let timerInterval;
        let isAnswered = false;
        
        // Penampung Jawaban User (Untuk dikirim ke Server)
        let userAnswers = []; 
        let estimatedCorrect = 0; 

        const sfxCorrect = document.getElementById('sfx-correct');
        const sfxWrong = document.getElementById('sfx-wrong');
        const sfxFinish = document.getElementById('sfx-finish');
        const sfxBadge = document.getElementById('sfx-badge');
        
        // Helper Animation Value (Untuk Fitur Counting Effect)
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Helper Update Progress Bar
        function updateProgressBar() {
            const percent = ((currentIdx + 1) / questions.length) * 100;
            document.getElementById('progress-bar').style.width = percent + "%";
            document.getElementById('progress-percent').innerText = Math.round(percent) + "%";
        }

        function playSound(el) { el.currentTime = 0; el.play().catch(()=>{}); }
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        if(questions.length > 0) {
            // Delay sedikit untuk menampilkan efek skeleton di awal
            setTimeout(loadQuestion, 800);
        } else {
            document.getElementById('skeleton-loader').classList.add('hidden');
            document.getElementById('question-content').classList.remove('hidden');
            document.getElementById('question-text').innerText = "Tidak ada soal.";
        }

        function loadQuestion() {
            if (currentIdx >= questions.length) { finishQuiz(); return; }
            const q = questions[currentIdx];
            isAnswered = false;
            
            // TRANSITION & SKELETON HANDLING
            const contentDiv = document.getElementById('question-content');
            const skeletonDiv = document.getElementById('skeleton-loader');
            
            // Fade In Effect
            contentDiv.classList.remove('hidden');
            skeletonDiv.classList.add('hidden');
            // Trigger reflow/animation
            void contentDiv.offsetWidth; 
            contentDiv.classList.add('fade-enter-active');
            contentDiv.classList.remove('opacity-0', 'translate-y-4');

            // Reset UI Content
            document.getElementById('question-text').innerText = q.question_text;
            document.getElementById('q-number').innerText = currentIdx + 1;
            document.getElementById('feedback-area').classList.add('hidden');
            document.getElementById('options-container').classList.remove('disabled-opt');
            document.getElementById('instruction-text').innerText = "";
            
            updateProgressBar();

            // Media Handle
            const imgEl = document.getElementById('q-image');
            const audioEl = document.getElementById('q-audio');
            const mediaCont = document.getElementById('media-container');
            imgEl.classList.add('hidden'); audioEl.classList.add('hidden'); mediaCont.classList.add('hidden');
            if(q.image_path) { imgEl.src = "/storage/" + q.image_path; imgEl.classList.remove('hidden'); mediaCont.classList.remove('hidden'); }
            if(q.audio_path) { audioEl.src = "/storage/" + q.audio_path; audioEl.classList.remove('hidden'); mediaCont.classList.remove('hidden'); }

            // Timer
            if (configTimer > 0) {
                timeLeft = configTimer;
                document.getElementById('timer-display').innerText = timeLeft;
                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    timeLeft--;
                    document.getElementById('timer-display').innerText = timeLeft;
                    if (timeLeft <= 0) { clearInterval(timerInterval); handleTimeUp(); }
                }, 1000);
            } else {
                document.getElementById('timer-display').innerText = "∞";
            }

            // Options Render
            const container = document.getElementById('options-container');
            container.innerHTML = '';
            const btnConfirm = document.getElementById('btn-confirm');
            btnConfirm.classList.add('hidden'); btnConfirm.disabled = true;
            
            const displayOptions = shuffleArray([...q.options]);

            if (q.type === 'single') {
                document.getElementById('instruction-text').innerText = "Pilih satu jawaban yang benar.";
                displayOptions.forEach(opt => {
                    const btn = document.createElement('button');
                    btn.className = 'w-full text-left p-4 rounded-xl bg-slate-700/50 hover:bg-slate-600 border border-slate-600 hover:border-blue-400 transition-all duration-200 font-medium text-lg flex justify-between items-center group option-btn shadow-sm text-white relative overflow-hidden';
                    btn.innerHTML = `<span class="relative z-10">${opt.option_text}</span>`;
                    btn.onclick = () => submitSingle(opt, btn);
                    container.appendChild(btn);
                });
            }
            else if (q.type === 'multiple') {
                document.getElementById('instruction-text').innerText = "Pilih SEMUA jawaban benar, lalu klik 'JAWAB SEKARANG'.";
                btnConfirm.classList.remove('hidden');
                displayOptions.forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'option-item w-full text-left p-4 rounded-xl border border-slate-600 bg-slate-700/50 hover:bg-slate-600 transition mb-2 cursor-pointer flex items-center gap-3 shadow-sm text-white';
                    div.innerHTML = `<div class="w-6 h-6 border-2 border-slate-400 rounded flex items-center justify-center"><i class="fas fa-check text-transparent transition check-icon"></i></div> <span>${opt.option_text}</span>`;
                    div.dataset.id = opt.id; 
                    div.dataset.correct = opt.is_correct;
                    div.onclick = () => {
                        div.classList.toggle('selected');
                        div.querySelector('.check-icon').classList.toggle('text-transparent');
                        div.querySelector('.check-icon').classList.toggle('text-blue-500');
                        validateComplexAnswer();
                    };
                    container.appendChild(div);
                });
            }
            else if (q.type === 'ordering') {
                document.getElementById('instruction-text').innerText = "Urutkan jawaban dengan panah ▲ ▼.";
                btnConfirm.classList.remove('hidden'); btnConfirm.disabled = false;
                displayOptions.forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'ordering-item w-full p-3 rounded-xl border border-slate-600 bg-slate-700/50 mb-2 flex justify-between items-center text-white shadow-sm';
                    div.dataset.id = opt.id; div.dataset.order = opt.correct_order;
                    div.innerHTML = `<span>${opt.option_text}</span><div class="flex flex-col gap-1"><button onclick="moveItem(this, -1)" class="p-1 bg-slate-600 rounded hover:bg-slate-500 text-xs">▲</button><button onclick="moveItem(this, 1)" class="p-1 bg-slate-600 rounded hover:bg-slate-500 text-xs">▼</button></div>`;
                    container.appendChild(div);
                });
            }
            else if (q.type === 'matching') {
                document.getElementById('instruction-text').innerText = "Pasangkan sisi kiri dengan kanan.";
                btnConfirm.classList.remove('hidden');
                const rightOptions = shuffleArray(q.options.map(o => ({id: o.id, text: o.matching_pair})));
                displayOptions.forEach(opt => {
                    const row = document.createElement('div');
                    row.className = 'p-3 rounded-xl border border-slate-600 bg-slate-700/50 mb-2 shadow-sm text-white';
                    let selectHtml = `<select class="matching-select w-full mt-2 bg-slate-800 p-2 rounded border border-slate-500 focus:outline-none text-white focus:border-blue-400 transition" data-left-id="${opt.id}" data-correct-pair="${opt.matching_pair}" onchange="validateComplexAnswer()"><option value="">-- Pilih --</option>`;
                    rightOptions.forEach(ro => { selectHtml += `<option value="${ro.text}">${ro.text}</option>`; });
                    selectHtml += `</select>`;
                    row.innerHTML = `<div class="font-bold mb-1 border-b border-slate-600 pb-1">${opt.option_text}</div> ${selectHtml}`;
                    container.appendChild(row);
                });
            }
        }

        function validateComplexAnswer() {
            const q = questions[currentIdx];
            const btn = document.getElementById('btn-confirm');
            let isValid = false;
            if (q.type === 'multiple') { if (document.querySelectorAll('.option-item.selected').length > 0) isValid = true; }
            else if (q.type === 'matching') { const selects = document.querySelectorAll('.matching-select'); isValid = Array.from(selects).every(sel => sel.value !== ""); }
            else if (q.type === 'ordering') { isValid = true; }
            btn.disabled = !isValid;
        }

        function moveItem(btn, direction) {
            const item = btn.closest('.ordering-item');
            const parent = item.parentNode;
            if (direction === -1 && item.previousElementSibling) parent.insertBefore(item, item.previousElementSibling);
            if (direction === 1 && item.nextElementSibling) parent.insertBefore(item.nextElementSibling, item);
        }

        // --- SUBMIT JAWABAN (KUMPULKAN DATA) ---
        function submitSingle(opt, btn) {
            if(isAnswered) return; isAnswered = true; clearInterval(timerInterval);
            
            const isCorrect = opt.is_correct == 1; // Visual Only
            const oldScore = estimatedScore;
            
            if (isCorrect) { 
                btn.classList.add('correct'); 
                estimatedScore += 100; estimatedCorrect++; 
                playSound(sfxCorrect); 
            } else { 
                btn.classList.add('wrong'); 
                playSound(sfxWrong); 
            }
            
            // ANIMATE SCORE
            animateValue(document.getElementById("score-display"), oldScore, estimatedScore, 600);
            
            // SIMPAN DATA UTK DIKIRIM KE SERVER
            userAnswers.push({ 
                question_id: questions[currentIdx].id, 
                answer: opt.id, 
                time_left: timeLeft 
            });
            
            finishTurn();
        }

        function submitComplexAnswer() {
            if(isAnswered) return; isAnswered = true; clearInterval(timerInterval);
            const q = questions[currentIdx];
            let isCorrect = false;
            let answerData = null; 

            if (q.type === 'multiple') {
                const selected = document.querySelectorAll('.option-item.selected');
                const selectedIds = Array.from(selected).map(d => parseInt(d.dataset.id));
                const correctIds = q.options.filter(o => o.is_correct).map(o => o.id);
                if (selectedIds.length === correctIds.length && selectedIds.every(id => correctIds.includes(id))) isCorrect = true;
                
                document.querySelectorAll('.option-item').forEach(div => {
                    const id = parseInt(div.dataset.id);
                    const isKey = correctIds.includes(id);
                    if(isKey) div.classList.add('correct-indicator');
                });
                answerData = selectedIds; 
            } 
            else if (q.type === 'ordering') {
                const items = document.querySelectorAll('.ordering-item');
                const userOrder = Array.from(items).map(d => parseInt(d.dataset.id));
                const correctOrder = [...q.options].sort((a,b) => a.correct_order - b.correct_order).map(o => o.id);
                if (JSON.stringify(userOrder) === JSON.stringify(correctOrder)) isCorrect = true;
                answerData = userOrder;
            } 
            else if (q.type === 'matching') {
                const selects = document.querySelectorAll('.matching-select');
                let allCorrect = true;
                const matches = [];
                selects.forEach(sel => {
                    matches.push({ left_id: parseInt(sel.dataset.leftId), pair_text: sel.value });
                    if (sel.value === sel.dataset.correctPair) sel.parentElement.classList.add('correct');
                    else { sel.parentElement.classList.add('wrong'); allCorrect = false; }
                });
                if (allCorrect) isCorrect = true;
                answerData = matches;
            }

            const oldScore = estimatedScore;
            if (isCorrect) { 
                estimatedScore += 150; estimatedCorrect++; playSound(sfxCorrect); 
            } else { 
                playSound(sfxWrong); 
            }
            // ANIMATE SCORE
            animateValue(document.getElementById("score-display"), oldScore, estimatedScore, 600);

            userAnswers.push({ 
                question_id: q.id, 
                answer: answerData, 
                time_left: timeLeft 
            });

            finishTurn();
        }

        function finishTurn() {
            document.getElementById('options-container').classList.add('disabled-opt');
            document.getElementById('btn-confirm').classList.add('hidden');
            const q = questions[currentIdx];
            document.getElementById('explanation-text').innerText = q.explanation || "Tidak ada pembahasan.";
            document.getElementById('reference-text').innerText = q.reference ? "📚 Sumber: " + q.reference : "";
            
            // Tampilkan Feedback dengan transisi halus
            const feedbackArea = document.getElementById('feedback-area');
            feedbackArea.classList.remove('hidden');
            feedbackArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function handleTimeUp() {
            isAnswered = true; playSound(sfxWrong);
            document.getElementById('options-container').classList.add('disabled-opt');
            document.getElementById('btn-confirm').classList.add('hidden');
            
            userAnswers.push({ 
                question_id: questions[currentIdx].id, 
                answer: null, 
                time_left: 0 
            });

            document.getElementById('explanation-text').innerText = "Waktu Habis!";
            document.getElementById('feedback-area').classList.remove('hidden');
        }

        // FUNGSI TRANSISI NEXT QUESTION (POINT 6)
        function nextQuestion() { 
            const contentDiv = document.getElementById('question-content');
            
            // 1. Animasi Keluar (Fade Out)
            contentDiv.classList.add('opacity-0', 'translate-y-4'); // Tailwind utility or custom css
            contentDiv.classList.remove('fade-enter-active');

            // 2. Tunggu animasi selesai (300ms), baru ganti data
            setTimeout(() => {
                currentIdx++; 
                loadQuestion();
            }, 300); 
        }

        // --- FINISH & SAVE ---
        function finishQuiz() {
            document.getElementById('quiz-card').classList.add('hidden');
            document.getElementById('result-card').classList.remove('hidden');
            playSound(sfxFinish);
            updateResultUI(estimatedScore, estimatedCorrect, questions.length);
        }

        function updateResultUI(scoreVal, correctVal, totalVal) {
            const wrongVal = totalVal - correctVal;
            const percentage = totalVal > 0 ? Math.round((correctVal / totalVal) * 100) : 0;
            
            // Animate Final Score
            animateValue(document.getElementById('final-score'), 0, scoreVal, 1500);
            
            document.getElementById('final-correct').innerText = correctVal;
            document.getElementById('final-wrong').innerText = wrongVal;
            document.getElementById('final-percentage').innerText = percentage + "%";
            
            let title, subtitle, emoji;
            if (percentage === 100) { title = "LEGENDARY!"; subtitle = "Sempurna!"; emoji = "👑"; }
            else if (percentage >= 85) { title = "LUAR BIASA!"; subtitle = "Hampir sempurna!"; emoji = "🔥"; }
            else if (percentage >= 70) { title = "HEBAT!"; subtitle = "Hasil bagus."; emoji = "😎"; }
            else if (percentage >= 55) { title = "BAGUS"; subtitle = "Tingkatkan lagi."; emoji = "👍"; }
            else { title = "SEMANGAT!"; subtitle = "Jangan menyerah!"; emoji = "💪"; }

            document.getElementById('grade-title').innerText = title;
            document.getElementById('grade-subtitle').innerText = subtitle;
            document.getElementById('grade-emoji').innerText = emoji;

            document.getElementById('share-score').innerText = scoreVal;
            document.getElementById('share-grade-title').innerText = title;
            document.getElementById('share-emoji').innerText = emoji;

            if (percentage >= 55) confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
        }
        
        function saveScore() {
            const name = document.getElementById('player-name').value;
            if(!name) { alert("Isi nama dulu!"); return; }
            document.getElementById('share-player-name').innerText = name;
            
            const btnSave = document.getElementById('btn-save');
            btnSave.disabled = true; btnSave.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memvalidasi...';

            fetch("{{ route('quiz.submit') }}", {
                method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ 
                    category_id: {{ $category->id }}, 
                    player_name: name, 
                    answers: userAnswers 
                })
            }).then(res => res.json()).then(data => {
                updateResultUI(data.real_score, data.correct_count, questions.length);
                document.getElementById('save-section').classList.add('hidden');
                document.getElementById('after-save-actions').classList.remove('hidden');
                document.getElementById('btn-review-link').href = "/review/" + data.result_id;

                if(data.new_badges && data.new_badges.length > 0) {
                    const badgeContainer = document.getElementById('badge-notification');
                    const badgeList = document.getElementById('badge-list');
                    badgeContainer.classList.remove('hidden');
                    playSound(sfxBadge);
                    badgeList.innerHTML = '';
                    data.new_badges.forEach(badgeName => {
                        const badgeEl = document.createElement('span');
                        badgeEl.className = "bg-yellow-500 text-slate-900 px-3 py-1 rounded-full font-bold text-xs shadow badge-pop flex items-center";
                        badgeEl.innerHTML = `<i class="fas fa-trophy mr-1"></i> ${badgeName}`;
                        badgeList.appendChild(badgeEl);
                    });
                }
            }).catch(err => {
                console.error(err); alert("Gagal menyimpan."); btnSave.disabled = false; btnSave.innerText = 'Simpan & Lihat Hasil';
            });
        }

        // --- SHARE IMAGE GENERATOR ---
        function generateShareImage() {
            const ticket = document.getElementById('shareable-ticket');
            const modal = document.getElementById('share-modal');
            const container = document.getElementById('generated-image-container');
            const btnShare = document.getElementById('btn-share-img');

            btnShare.disabled = true; btnShare.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Membuat...';

            html2canvas(ticket, { backgroundColor: null, scale: 2 }).then(canvas => {
                const img = new Image();
                img.src = canvas.toDataURL("image/png");
                img.className = "w-full h-auto object-contain";
                container.innerHTML = ''; container.appendChild(img);
                modal.classList.remove('hidden');
                btnShare.disabled = false; btnShare.innerHTML = '<i class="fas fa-camera mr-2"></i> Share Gambar';
            });
        }

        function closeShareModal() { document.getElementById('share-modal').classList.add('hidden'); }
    </script>
</body>
</html>