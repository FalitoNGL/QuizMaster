<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        .glass { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
        /* Feedback Warna */
        .correct { background-color: #10b981 !important; border-color: #059669 !important; color: white !important; }
        .wrong { background-color: #ef4444 !important; border-color: #dc2626 !important; color: white !important; opacity: 0.8; }
        /* Indikator jawaban benar saat user salah */
        .correct-indicator { border: 2px solid #10b981 !important; box-shadow: 0 0 10px #10b981; position: relative; }
        .correct-indicator::after { content: "✔"; position: absolute; right: 15px; font-weight: bold; color: #10b981; }
        .disabled-opt { pointer-events: none; }
        /* Animasi muncul */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-up { animation: fadeUp 0.5s ease-out; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen flex items-center justify-center font-sans p-4 transition-colors duration-300">
    
    <div class="w-full max-w-2xl relative z-10">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6 glass p-4 rounded-full shadow-lg bg-white/80 dark:bg-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center border border-slate-300 dark:border-slate-500">
                    @if($timer > 0) <i class="fas fa-clock text-yellow-500 dark:text-yellow-400"></i> @else <i class="fas fa-mug-hot text-green-500"></i> @endif
                </div>
                <div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">WAKTU</div>
                    <div id="timer-display" class="font-mono text-xl font-bold text-slate-700 dark:text-white">{{ $timer > 0 ? $timer : '∞' }}</div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 border border-gray-300 dark:border-slate-500 flex items-center justify-center text-slate-600 dark:text-yellow-300 transition">
                    <i id="theme-icon" class="fas fa-moon"></i>
                </button>

                <div class="text-right">
                    <div class="text-xs text-slate-500 dark:text-slate-400">SKOR</div>
                    <div id="score-display" class="font-mono text-xl font-bold text-blue-600 dark:text-blue-400">0</div>
                </div>
            </div>
        </div>

        <!-- Kartu Soal -->
        <div id="quiz-card" class="glass rounded-3xl p-8 shadow-2xl relative overflow-hidden bg-white/80 dark:bg-slate-800/90">
            <div class="mb-6">
                <span class="inline-block px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-300 text-xs font-bold border border-blue-200 dark:border-blue-500/30 mb-4">
                    SOAL <span id="q-number">1</span> / {{ $questions->count() }}
                </span>
                
                <!-- Media -->
                <div id="media-container" class="hidden mb-4">
                    <img id="q-image" class="w-full h-48 object-cover rounded-xl border border-slate-300 dark:border-slate-600 mb-2 hidden shadow-md">
                    <audio id="q-audio" controls class="w-full hidden"></audio>
                </div>

                <h2 id="question-text" class="text-xl md:text-2xl font-bold leading-relaxed text-slate-800 dark:text-white">Loading...</h2>
            </div>

            <!-- Opsi Jawaban -->
            <div id="options-container" class="grid gap-3"></div>

            <!-- AREA PEMBAHASAN (MUNCUL SETELAH JAWAB) -->
            <div id="feedback-area" class="hidden mt-6 p-5 bg-slate-100 dark:bg-slate-900 rounded-xl border-l-4 border-yellow-500 animate-fade-up shadow-inner">
                <h4 class="font-bold text-yellow-600 dark:text-yellow-400 mb-2 flex items-center gap-2">
                    <i class="fas fa-lightbulb"></i> Pembahasan
                </h4>
                <p id="explanation-text" class="text-sm text-slate-600 dark:text-slate-300 mb-3 leading-relaxed"></p>
                <div class="text-xs text-slate-500 italic border-t border-slate-300 dark:border-slate-700 pt-2" id="reference-text"></div>
                
                <button onclick="nextQuestion()" class="w-full mt-4 bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-lg font-bold transition shadow-lg flex items-center justify-center gap-2">
                    Lanjut Soal Berikutnya <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Layar Hasil -->
        <div id="result-card" class="hidden glass rounded-3xl p-10 text-center shadow-2xl bg-white/95 dark:bg-slate-800/80">
            <div class="w-24 h-24 bg-yellow-100 dark:bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-yellow-500">
                <i class="fas fa-crown text-4xl text-yellow-500"></i>
            </div>
            <h2 class="text-3xl font-bold mb-2 text-slate-800 dark:text-white">Selesai!</h2>
            <p class="text-slate-500 dark:text-slate-400 mb-6">Kamu telah menyelesaikan kuis ini.</p>
            <div class="text-5xl font-bold text-blue-600 dark:text-blue-500 mb-8" id="final-score">0</div>
            
            <div id="save-section">
                <input type="text" id="player-name" placeholder="Ketik Nama Kamu" class="w-full bg-slate-100 dark:bg-slate-700 px-4 py-3 rounded-xl mb-4 text-center text-slate-800 dark:text-white border border-slate-300 dark:border-slate-600 focus:border-blue-500 outline-none">
                <button onclick="saveScore()" id="btn-save" class="w-full bg-green-600 hover:bg-green-500 py-3 rounded-xl font-bold text-white shadow-lg mb-3 transition">Simpan Skor</button>
            </div>

            <div id="after-save-actions" class="hidden flex flex-col gap-3">
                <button onclick="shareToWa()" class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-bold transition shadow-md flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp text-xl"></i> Share ke WhatsApp
                </button>
                <a id="btn-review-link" href="#" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold transition shadow-md">
                    <i class="fas fa-book-open"></i> Lihat Pembahasan
                </a>
                <a href="{{ route('menu') }}" class="w-full bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-700 dark:text-white py-3 rounded-xl font-bold transition">
                    Kembali ke Menu
                </a>
            </div>
        </div>
    </div>

    <audio id="sfx-correct"><source src="https://assets.mixkit.co/active_storage/sfx/2000/2000-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-wrong"><source src="https://assets.mixkit.co/active_storage/sfx/2003/2003-preview.mp3" type="audio/mpeg"></audio>
    <audio id="sfx-finish"><source src="https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3" type="audio/mpeg"></audio>

    <script>
        // 1. SETUP DARK MODE
        const htmlTag = document.documentElement;
        const themeIcon = document.getElementById('theme-icon');
        
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlTag.classList.add('dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            htmlTag.classList.remove('dark');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }

        function toggleTheme() {
            if (htmlTag.classList.contains('dark')) {
                htmlTag.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
            } else {
                htmlTag.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            }
        }

        // 2. GAME VARIABLES
        const questions = @json($questions);
        const configTimer = {{ $timer }}; // Ambil dari Controller
        
        let currentIdx = 0;
        let score = 0;
        let correctAnswers = 0;
        let timeLeft = configTimer;
        let timerInterval;
        let userAnswers = [];
        let isAnswered = false; // Mencegah klik ganda
        
        // Cek Audio Setting
        const isSfxOn = localStorage.getItem('qm_sfx') !== 'false';
        const sfxCorrect = document.getElementById('sfx-correct');
        const sfxWrong = document.getElementById('sfx-wrong');
        const sfxFinish = document.getElementById('sfx-finish');

        function playSound(audioElement) {
            if (isSfxOn) { audioElement.currentTime = 0; audioElement.play().catch(e => {}); }
        }

        // Init
        if(questions.length > 0) loadQuestion();
        else document.getElementById('question-text').innerText = "Tidak ada soal.";

        function loadQuestion() {
            if (currentIdx >= questions.length) { finishQuiz(); return; }

            const q = questions[currentIdx];
            isAnswered = false;
            
            // Reset UI
            document.getElementById('question-text').innerText = q.question_text;
            document.getElementById('q-number').innerText = currentIdx + 1;
            document.getElementById('feedback-area').classList.add('hidden');
            document.getElementById('options-container').classList.remove('disabled-opt');

            // Media
            const imgEl = document.getElementById('q-image');
            const audioEl = document.getElementById('q-audio');
            const mediaCont = document.getElementById('media-container');
            imgEl.classList.add('hidden'); audioEl.classList.add('hidden'); mediaCont.classList.add('hidden');
            if(q.image_path) { imgEl.src = "/storage/" + q.image_path; imgEl.classList.remove('hidden'); mediaCont.classList.remove('hidden'); }
            if(q.audio_path) { audioEl.src = "/storage/" + q.audio_path; audioEl.classList.remove('hidden'); mediaCont.classList.remove('hidden'); }

            // Timer Logic
            if (configTimer > 0) {
                timeLeft = configTimer;
                document.getElementById('timer-display').innerText = timeLeft;
                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    timeLeft--;
                    document.getElementById('timer-display').innerText = timeLeft;
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        handleTimeUp(); // Waktu habis
                    }
                }, 1000);
            } else {
                document.getElementById('timer-display').innerText = "∞";
                clearInterval(timerInterval);
            }

            // Render Opsi
            const container = document.getElementById('options-container');
            container.innerHTML = '';
            
            const shuffledOptions = [...q.options].sort(() => Math.random() - 0.5);

            shuffledOptions.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = 'w-full text-left p-4 rounded-xl bg-white dark:bg-slate-700 hover:bg-blue-50 dark:hover:bg-slate-600 border-2 border-transparent transition font-medium text-lg flex justify-between items-center group option-btn text-slate-800 dark:text-white shadow-sm';
                btn.dataset.correct = opt.is_correct; // Simpan kunci di data attribute
                btn.innerHTML = `<span>${opt.option_text}</span>`;
                btn.onclick = () => submitAnswer(opt, btn);
                container.appendChild(btn);
            });
        }

        function submitAnswer(option, btnElement) {
            if(isAnswered) return;
            isAnswered = true;
            clearInterval(timerInterval);

            const q = questions[currentIdx];
            const isCorrect = option.is_correct == 1;
            const allBtns = document.querySelectorAll('.option-btn');

            // 1. VISUAL FEEDBACK
            if (isCorrect) {
                // Jika Benar: Hijau
                btnElement.classList.add('correct');
                score += 100 + (configTimer > 0 ? timeLeft : 0);
                correctAnswers++;
                playSound(sfxCorrect);
            } else {
                // Jika Salah: Merah
                btnElement.classList.add('wrong');
                playSound(sfxWrong);
                
                // DAN Tunjukkan Jawaban Benar (Border Hijau)
                allBtns.forEach(b => {
                    if (b.dataset.correct == "1") b.classList.add('correct-indicator');
                });
            }

            // Kunci Tombol
            allBtns.forEach(b => b.disabled = true);
            document.getElementById('options-container').classList.add('disabled-opt');
            document.getElementById('score-display').innerText = score;

            // Rekam Data
            userAnswers.push({
                question_id: q.id,
                option_id: option.id,
                is_correct: isCorrect
            });

            // 2. TAMPILKAN PEMBAHASAN
            document.getElementById('explanation-text').innerText = q.explanation || "Tidak ada pembahasan khusus untuk soal ini.";
            document.getElementById('reference-text').innerText = q.reference ? "📚 Sumber: " + q.reference : "";
            document.getElementById('feedback-area').classList.remove('hidden');
            
            // Auto scroll agar pembahasan terlihat di HP
            document.getElementById('feedback-area').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function handleTimeUp() {
            isAnswered = true;
            playSound(sfxWrong);
            const allBtns = document.querySelectorAll('.option-btn');
            
            // Tunjukkan jawaban benar
            allBtns.forEach(b => {
                if (b.dataset.correct == "1") b.classList.add('correct-indicator');
                b.disabled = true;
            });
            document.getElementById('options-container').classList.add('disabled-opt');

            // Tampilkan pesan waktu habis
            const q = questions[currentIdx];
            document.getElementById('explanation-text').innerText = "Waktu Habis! " + (q.explanation || "");
            document.getElementById('feedback-area').classList.remove('hidden');
        }

        function nextQuestion() {
            currentIdx++;
            loadQuestion();
        }

        function finishQuiz() {
            document.getElementById('quiz-card').classList.add('hidden');
            document.getElementById('result-card').classList.remove('hidden');
            document.getElementById('final-score').innerText = score;
            playSound(sfxFinish);
        }

        function saveScore() {
            const name = document.getElementById('player-name').value;
            if(!name) { alert("Isi nama dulu!"); return; }
            
            const btnSave = document.getElementById('btn-save');
            btnSave.disabled = true;
            btnSave.innerText = "Menyimpan...";

            fetch("{{ route('quiz.submit') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({
                    category_id: {{ $category->id }},
                    player_name: name,
                    score: score, correct: correctAnswers, total: questions.length,
                    answers: userAnswers
                })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('save-section').classList.add('hidden');
                document.getElementById('after-save-actions').classList.remove('hidden');
                document.getElementById('after-save-actions').classList.add('flex');
                document.getElementById('btn-review-link').href = "/review/" + data.result_id;
            })
            .catch(err => {
                alert("Gagal menyimpan.");
                btnSave.disabled = false;
            });
        }

        function shareToWa() {
            const name = document.getElementById('player-name').value;
            const text = `Halo! Saya ${name} baru saja mencetak skor *${score}* di kuis *{{ $category->name }}*! 🏆`;
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }
    </script>
</body>
</html>