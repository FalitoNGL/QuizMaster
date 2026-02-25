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
        /* --- 1. ATMOSFER & LATAR BELAKANG --- */
        body { overflow-x: hidden; background-color: #0f172a; color: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .ambient-light {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; overflow: hidden;
        }
        .blob {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.6;
            animation: float 10s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
        }
        .blob-1 { top: -10%; left: -10%; width: 500px; height: 500px; background: #4f46e5; animation-delay: 0s; } /* Indigo */
        .blob-2 { bottom: -10%; right: -10%; width: 400px; height: 400px; background: #06b6d4; animation-delay: -5s; } /* Cyan */
        .blob-3 { top: 40%; left: 40%; width: 300px; height: 300px; background: #7c3aed; animation-delay: -2s; animation-duration: 15s; } /* Violet */
        
        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 50px) scale(1.1); }
        }
        .noise-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;
            opacity: 0.05; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        /* GLASS CARD STYLE */
        .glass { 
            background: rgba(30, 41, 59, 0.7); 
            backdrop-filter: blur(16px); 
            border: 1px solid rgba(255,255,255,0.1); 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); 
            z-index: 10; position: relative; 
        }

        /* --- 2. TOMBOL 3D & OPSI --- */
        .btn-3d {
            transition: all 0.1s;
            border-bottom-width: 4px;
            transform: translateY(0);
            position: relative;
        }
        .btn-3d:active, .btn-3d.pressed {
            transform: translateY(4px);
            border-bottom-width: 0px;
            margin-bottom: 4px; /* Menjaga layout tidak lompat */
        }
        
        /* Warna Button Default */
        .btn-default { background-color: #334155; border-color: #1e293b; color: white; }
        .btn-default:hover { background-color: #475569; }
        
        /* Warna Jawaban Benar (Hijau) */
        .correct { 
            background-color: #10b981 !important; 
            border-color: #047857 !important;     
            color: white !important; 
        }
        /* Warna Jawaban Salah (Merah) */
        .wrong { 
            background-color: #ef4444 !important; 
            border-color: #b91c1c !important;     
            color: white !important; opacity: 1;
        }
        
        /* Style untuk Multiple Choice */
        .selected { border: 2px solid #60a5fa !important; background-color: rgba(59, 130, 246, 0.3) !important; }
        
        /* Penanda Kunci Jawaban (jika user salah) */
        .correct-indicator { border: 2px solid #34d399 !important; box-shadow: 0 0 15px rgba(52, 211, 153, 0.4); }

        /* --- 3. VISUALISASI TIMER & PROGRESS --- */
        .timer-circle { transition: stroke-dashoffset 1s linear; transform: rotate(-90deg); transform-origin: 50% 50%; }
        
        .segment-bar { display: flex; gap: 4px; height: 8px; width: 100%; margin-top: 10px; }
        .segment { flex: 1; background: rgba(255,255,255,0.1); border-radius: 4px; transition: background 0.3s; }
        .segment.active { background: #facc15; box-shadow: 0 0 10px rgba(250, 204, 21, 0.5); }
        .segment.done-correct { background: #10b981; } /* Hijau */
        .segment.done-wrong { background: #ef4444; }   /* Merah */

        /* UTILS */
        .disabled-opt { pointer-events: none; opacity: 0.9; }
        button:disabled { cursor: not-allowed; opacity: 0.6; filter: grayscale(100%); }
        
        /* Animasi Masuk Halaman */
        .fade-enter { opacity: 0; transform: translateY(10px); }
        .fade-enter-active { opacity: 1; transform: translateY(0); transition: all 0.4s ease-out; }
        
        /* Badge Anim */
        .badge-pop { animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }

        /* --- 4. NEW FEATURES (Streak, Transitions) --- */
        /* Streak Badge */
        .streak-container {
            position: absolute; top: 0px; left: 50%; transform: translateX(-50%) scale(0.8);
            background: linear-gradient(135deg, #f59e0b, #ea580c);
            padding: 6px 18px; border-radius: 30px;
            font-weight: 900; font-size: 1rem; color: white;
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.6); border: 2px solid #fbbf24;
            z-index: 50; opacity: 0; pointer-events: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .streak-visible { top: -20px; opacity: 1; transform: translateX(-50%) scale(1.1) rotate(-2deg); }
        .streak-pop { animation: streakPop 0.3s; }
        @keyframes streakPop { 50% { transform: translateX(-50%) scale(1.4) rotate(5deg); } }

        /* Smooth Slide Transitions */
        .slide-container { position: relative; min-height: 400px; } /* Ensure height for absolute positioning if needed, or stick to flow */
        
        .transition-enter { opacity: 0; transform: translateX(60px); }
        .transition-enter-active { opacity: 1; transform: translateX(0); transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1); }
        
        .transition-exit { opacity: 1; transform: translateX(0); }
        .transition-exit-active { opacity: 0; transform: translateX(-60px); pointer-events: none; position: absolute; width: 100%; top: 0; transition: all 0.4s ease-in; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center font-sans p-4">
    @include('partials.loading-screen')
    
    <div class="ambient-light">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    <div class="noise-overlay"></div>

    <div class="w-full max-w-2xl relative z-10">
        
        <div class="flex justify-between items-center mb-6">
            <div class="glass px-4 py-2 rounded-2xl flex items-center gap-3">
                <div class="relative w-12 h-12 flex items-center justify-center">
                    <svg class="absolute top-0 left-0 w-full h-full" width="48" height="48" viewBox="0 0 48 48">
                        <circle cx="24" cy="24" r="20" fill="none" stroke="#334155" stroke-width="4"></circle>
                        <circle id="timer-circle-svg" cx="24" cy="24" r="20" fill="none" stroke="#facc15" stroke-width="4" stroke-dasharray="125.6" stroke-dashoffset="0" stroke-linecap="round" class="timer-circle"></circle>
                    </svg>
                    <div id="timer-text" class="font-mono font-bold text-lg relative z-10 text-white">{{ $timer > 0 ? $timer : '∞' }}</div>
                </div>
                <div class="leading-tight">
                    <div class="text-[10px] text-slate-400 font-bold tracking-wider uppercase">Sisa Waktu</div>
                    <div class="text-xs text-yellow-400 font-bold">Detik</div>
                </div>
            </div>

            <div class="glass px-5 py-2 rounded-2xl text-right">
                <div class="text-[10px] text-slate-400 font-bold tracking-wider uppercase">Skor Sementara</div>
                <div id="score-display" class="font-mono text-2xl font-bold text-blue-400 shadow-blue-500/50 drop-shadow-sm">0</div>
            </div>
        </div>

        <div id="quiz-card" class="glass rounded-3xl p-6 md:p-8 relative overflow-visible transition-all duration-500">
            <!-- Streak Badge -->
            <div id="streak-badge" class="streak-container">
                <i class="fas fa-fire-alt animate-pulse mr-1 text-yellow-200"></i> COMBO x<span id="streak-count">0</span>
            </div>
            
            <div class="mb-8">
                <div class="flex justify-between items-end mb-1 px-1">
                    <span class="text-sm font-bold text-slate-300">Soal <span id="q-number" class="text-white text-lg">1</span> <span class="text-slate-500">/ {{ $questions->count() }}</span></span>
                    <span class="text-xs text-slate-500 font-mono" id="progress-percent">0%</span>
                </div>
                <div id="segmented-bar" class="segment-bar">
                    </div>
            </div>

            <div id="skeleton-loader" class="animate-pulse space-y-4">
                <div class="h-6 bg-slate-700/50 rounded w-3/4"></div>
                <div class="h-4 bg-slate-700/30 rounded w-1/2"></div>
                <div class="grid gap-3 mt-8">
                    <div class="h-14 bg-slate-700/40 rounded-xl"></div>
                    <div class="h-14 bg-slate-700/40 rounded-xl"></div>
                    <div class="h-14 bg-slate-700/40 rounded-xl"></div>
                </div>
            </div>

            <div id="question-wrapper" class="relative">
                <div id="question-content" class="transition-enter opacity-0">
                    <div class="mb-6">
                        <div id="media-container" class="hidden mb-4 rounded-xl overflow-hidden border border-slate-600 shadow-lg">
                        <img id="q-image" class="w-full h-56 object-cover hidden">
                        <audio id="q-audio" controls class="w-full hidden bg-slate-800"></audio>
                    </div>

                    <h2 id="question-text" class="text-xl md:text-2xl font-bold leading-snug text-white drop-shadow-md"></h2>
                    <p id="instruction-text" class="text-sm text-slate-400 mt-3 italic flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> <span id="instruction-content"></span>
                    </p>
                </div>

                <div id="options-container" class="grid gap-4"></div>

                <button id="btn-confirm" onclick="submitComplexAnswer()" disabled class="hidden w-full mt-8 bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-xl font-bold transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transform active:scale-95 btn-3d" style="border-bottom-color: #1e40af;">
                    JAWAB SEKARANG <i class="fas fa-check ml-2"></i>
                </button>

                <div id="feedback-area" class="hidden mt-8 p-5 bg-slate-800/80 rounded-xl border-l-4 border-yellow-500 shadow-inner backdrop-blur-sm">
                    <h4 class="font-bold text-yellow-400 mb-2 flex items-center gap-2">
                        <i class="fas fa-lightbulb"></i> Pembahasan
                    </h4>
                    <p id="explanation-text" class="text-sm text-slate-300 mb-3 leading-relaxed"></p>
                    <div class="text-xs text-slate-500 italic border-t border-slate-700/50 pt-2" id="reference-text"></div>
                    
                    <button onclick="nextQuestion()" class="w-full mt-4 bg-slate-700 hover:bg-slate-600 text-white py-3 rounded-lg font-bold transition shadow-lg flex items-center justify-center gap-2 border border-slate-600 group btn-3d" style="border-bottom-color: #334155;">
                        Lanjut Soal Berikutnya <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition"></i>
                    </button>
                </div>
                </div> <!-- End of question-content -->
            </div> <!-- End of question-wrapper -->
        </div>

        <div id="result-card" class="hidden glass rounded-3xl p-8 text-center shadow-2xl bg-slate-800 border border-slate-700 relative z-20">
            <div class="relative inline-block mb-4">
                <div class="absolute inset-0 bg-yellow-500 blur-3xl opacity-20 rounded-full"></div>
                <div class="text-7xl animate-bounce relative z-10" id="grade-emoji">🎉</div>
            </div>

            <h2 class="text-3xl font-bold mb-1 text-white" id="grade-title">Selesai!</h2>
            <p class="text-slate-400 text-sm mb-6" id="grade-subtitle">Kamu telah menyelesaikan kuis ini.</p>

            <div class="grid grid-cols-4 gap-2 mb-8 bg-slate-900/50 p-4 rounded-xl border border-slate-700 text-center">
                <div>
                    <div class="text-[10px] uppercase text-slate-500 font-bold">Skor Akhir</div>
                    <div class="text-2xl font-bold text-yellow-400" id="final-score">0</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase text-slate-500 font-bold">Akurasi</div>
                    <div class="text-2xl font-bold text-blue-400" id="final-percentage">0%</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase text-slate-500 font-bold">Benar</div>
                    <div class="text-2xl font-bold text-green-400" id="final-correct">0</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase text-slate-500 font-bold">Salah</div>
                    <div class="text-2xl font-bold text-red-400" id="final-wrong">0</div>
                </div>
            </div>

            <div id="badge-notification" class="hidden mb-6 bg-gradient-to-r from-yellow-900/20 to-yellow-600/20 p-4 rounded-xl border border-yellow-500/30">
                <h4 class="text-yellow-400 font-bold text-sm mb-2"><i class="fas fa-medal mr-1"></i> Achievement Unlocked!</h4>
                <div id="badge-list" class="flex flex-wrap justify-center gap-2"></div>
            </div>

            <div id="save-section">
                <input type="text" id="player-name" value="{{ session('current_player', '') }}" placeholder="Ketik Nama Kamu" class="w-full bg-slate-700 px-4 py-3 rounded-xl mb-4 text-center text-white border-b-4 border-slate-900 focus:border-blue-500 outline-none transition placeholder-slate-400 font-bold">
                <button onclick="saveScore()" id="btn-save" class="w-full bg-green-600 hover:bg-green-500 py-3 rounded-xl font-bold text-white shadow-lg mb-3 transition transform btn-3d" style="border-bottom-color: #15803d;">
                    <i class="fas fa-save mr-2"></i> Simpan & Lihat Hasil
                </button>
            </div>
            
            <div id="after-save-actions" class="hidden flex flex-col gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <a id="btn-review-link" href="#" class="bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-bold transition text-center shadow-lg flex items-center justify-center btn-3d" style="border-bottom-color: #1d4ed8;">
                        <i class="fas fa-list-check mr-2"></i> Pembahasan
                    </a>
                    <button onclick="generateShareImage()" id="btn-share-img" class="bg-purple-600 hover:bg-purple-500 text-white py-3 rounded-xl font-bold transition shadow-lg flex items-center justify-center btn-3d" style="border-bottom-color: #7e22ce;">
                        <i class="fas fa-camera mr-2"></i> Share Gambar
                    </button>
                </div>
                <a href="{{ route('menu') }}" class="w-full bg-slate-700 hover:bg-slate-600 text-white py-3 rounded-xl font-bold transition text-center btn-3d" style="border-bottom-color: #334155;">
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
        const labels = ['A', 'B', 'C', 'D', 'E', 'F']; 
        
        let currentIdx = 0;
        let estimatedScore = 0;
        let timeLeft = configTimer;
        let timerInterval;
        let isAnswered = false;
        
        // Penampung Jawaban User
        let userAnswers = []; 
        let estimatedCorrect = 0; 
        
        // --- Feature Variables ---
        let streakCount = 0;
        let maxStreak = 0;
        const streakEl = document.getElementById('streak-badge');
        const streakCountEl = document.getElementById('streak-count'); 

        const sfxCorrect = document.getElementById('sfx-correct');
        const sfxWrong = document.getElementById('sfx-wrong');
        const sfxFinish = document.getElementById('sfx-finish');
        const sfxBadge = document.getElementById('sfx-badge');
        
        // Timer Elements
        const timerCircle = document.getElementById('timer-circle-svg');
        const totalDash = 125.6; // 2 * PI * R (R=20)

        // Init Progress Bar
        initSegmentBar();

        window.onbeforeunload = function(e) {
            const isPlaying = !document.getElementById('result-card') || document.getElementById('result-card').classList.contains('hidden');
            if (isPlaying && (typeof currentIdx !== 'undefined' && currentIdx < questions.length)) {
                e.preventDefault();
                e.returnValue = ''; 
                return "Yakin ingin keluar? Progres kuis akan hilang.";
            }
        };

        // --- HELPER FUNCTIONS ---
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

        // --- NEW: Haptic Feedback ---
        function triggerHaptic(type) {
            if (!navigator.vibrate) return;
            if (type === 'success') navigator.vibrate([15, 30, 15]); // Quick double tap
            if (type === 'error') navigator.vibrate([50, 50, 50]); // Longer buzz
            if (type === 'tap') navigator.vibrate(10); // Light tap
        }

        // --- NEW: Streak Logic ---
        function updateStreak(isCorrect) {
            if (isCorrect) {
                streakCount++;
                if(streakCount > maxStreak) maxStreak = streakCount;
                streakCountEl.innerText = streakCount;
                
                if (streakCount >= 2) {
                    streakEl.classList.add('streak-visible');
                    streakEl.classList.remove('streak-pop');
                    void streakEl.offsetWidth; // trigger reflow
                    streakEl.classList.add('streak-pop');
                    
                    // Extra confetti for big streaks
                    if(streakCount % 3 === 0) confetti({ particleCount: 30, spread: 40, origin: { y: 0.5 }, colors: ['#f59e0b', '#ef4444'] });
                }
            } else {
                streakEl.classList.remove('streak-visible');
                streakCount = 0;
            }
        }

        // Logic Segmented Bar
        function initSegmentBar() {
            const bar = document.getElementById('segmented-bar');
            bar.innerHTML = '';
            for(let i=0; i<questions.length; i++) {
                const seg = document.createElement('div');
                seg.className = 'segment';
                seg.id = 'seg-'+i;
                bar.appendChild(seg);
            }
        }
        
        function updateSegmentStatus(idx, status) {
            const seg = document.getElementById('seg-'+idx);
            if(status === 'active') seg.classList.add('active');
            if(status === 'correct') { seg.classList.remove('active'); seg.classList.add('done-correct'); }
            if(status === 'wrong') { seg.classList.remove('active'); seg.classList.add('done-wrong'); }
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
            
            // UI Resets
            const wrapper = document.getElementById('question-wrapper');
            const contentDiv = document.getElementById('question-content');
            
            // Remove exit classes if exist from previous
            contentDiv.classList.remove('transition-exit', 'transition-exit-active');
            
            // Add enter classes
            document.getElementById('skeleton-loader').classList.add('hidden');
            contentDiv.classList.remove('hidden', 'opacity-0');
            contentDiv.classList.add('transition-enter-active');
            
            // Reset position after animation (timeout matching CSS)
            setTimeout(() => {
                contentDiv.classList.remove('transition-enter', 'transition-enter-active');
            }, 500);

            // Gunakan textContent untuk soal juga
            document.getElementById('question-text').textContent = q.question_text;
            document.getElementById('q-number').innerText = currentIdx + 1;
            document.getElementById('feedback-area').classList.add('hidden');
            document.getElementById('options-container').classList.remove('disabled-opt');
            document.getElementById('instruction-content').innerText = "";
            
            // Update Visuals
            updateSegmentStatus(currentIdx, 'active');
            document.getElementById('progress-percent').innerText = Math.round((currentIdx / questions.length) * 100) + "%";

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
                updateTimerVisual(timeLeft);
                clearInterval(timerInterval);
                timerInterval = setInterval(() => {
                    timeLeft--;
                    updateTimerVisual(timeLeft);
                    if (timeLeft <= 0) { clearInterval(timerInterval); handleTimeUp(); }
                }, 1000);
            } else {
                document.getElementById('timer-text').innerText = "∞";
                timerCircle.style.strokeDashoffset = 0;
            }

            // Render Options
            const container = document.getElementById('options-container');
            container.innerHTML = '';
            const btnConfirm = document.getElementById('btn-confirm');
            btnConfirm.classList.add('hidden'); btnConfirm.disabled = true;
            
            const displayOptions = shuffleArray([...q.options]);

            if (q.type === 'single') {
                document.getElementById('instruction-content').innerText = "Pilih satu jawaban yang benar.";
                displayOptions.forEach((opt, idx) => {
                    // Tombol 3D & Label Structure via DOM
                    const btn = document.createElement('button');
                    btn.className = 'w-full text-left p-0 rounded-xl btn-3d btn-default flex items-stretch overflow-hidden group shadow-sm';
                    
                    // Label A/B/C/D
                    const labelDiv = document.createElement('div');
                    labelDiv.className = 'bg-black/20 px-5 flex items-center justify-center font-bold text-slate-300 border-r border-black/10 text-xl group-hover:bg-black/30 transition';
                    labelDiv.textContent = labels[idx] || '?';

                    // Konten Opsi
                    const textDiv = document.createElement('div');
                    textDiv.className = 'p-4 flex-grow font-medium text-lg relative z-10 flex items-center';
                    textDiv.textContent = opt.option_text; // INI KUNCINYA (textContent)

                    btn.appendChild(labelDiv);
                    btn.appendChild(textDiv);

                    btn.dataset.isCorrect = opt.is_correct;
                    btn.onclick = () => {
                        btn.classList.add('pressed'); // Efek tekan manual
                        setTimeout(() => submitSingle(opt, btn), 150); // Delay sedikit biar animasi terlihat
                    };
                    container.appendChild(btn);
                });
            }
            else if (q.type === 'multiple') {
                document.getElementById('instruction-content').innerText = "Pilih SEMUA jawaban benar.";
                btnConfirm.classList.remove('hidden');
                displayOptions.forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'option-item w-full text-left p-4 rounded-xl border-2 border-slate-700 bg-slate-800 hover:bg-slate-700 transition mb-2 cursor-pointer flex items-center gap-3 shadow-sm group';
                    
                    const iconDiv = document.createElement('div');
                    iconDiv.className = 'w-6 h-6 border-2 border-slate-500 rounded flex items-center justify-center group-hover:border-blue-400';
                    iconDiv.innerHTML = '<i class="fas fa-check text-transparent transition check-icon"></i>';

                    const textSpan = document.createElement('span');
                    textSpan.className = 'text-lg';
                    textSpan.textContent = opt.option_text; // Safe Text

                    div.appendChild(iconDiv);
                    div.appendChild(textSpan);

                    div.dataset.id = opt.id; div.dataset.correct = opt.is_correct;
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
                document.getElementById('instruction-content').innerText = "Urutkan jawaban dengan panah ▲ ▼.";
                btnConfirm.classList.remove('hidden'); btnConfirm.disabled = false;
                displayOptions.forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'ordering-item w-full p-4 rounded-xl border-2 border-slate-700 bg-slate-800 mb-2 flex justify-between items-center text-white shadow-sm';
                    div.dataset.id = opt.id; div.dataset.order = opt.correct_order;
                    
                    const textSpan = document.createElement('span');
                    textSpan.className = 'text-lg';
                    textSpan.textContent = opt.option_text; // Safe Text

                    const controls = document.createElement('div');
                    controls.className = 'flex flex-col gap-1';
                    controls.innerHTML = `<button onclick="moveItem(this, -1)" class="p-2 bg-slate-700 rounded hover:bg-slate-600 text-xs">▲</button><button onclick="moveItem(this, 1)" class="p-2 bg-slate-700 rounded hover:bg-slate-600 text-xs">▼</button>`;

                    div.appendChild(textSpan);
                    div.appendChild(controls);
                    container.appendChild(div);
                });
            }
            else if (q.type === 'matching') {
                document.getElementById('instruction-content').innerText = "Pasangkan sisi kiri dengan kanan.";
                btnConfirm.classList.remove('hidden');
                const rightOptions = shuffleArray(q.options.map(o => ({id: o.id, text: o.matching_pair})));
                displayOptions.forEach(opt => {
                    const row = document.createElement('div');
                    row.className = 'p-4 rounded-xl border-2 border-slate-700 bg-slate-800 mb-2 shadow-sm text-white';
                    
                    const qText = document.createElement('div');
                    qText.className = 'font-bold mb-1 border-b border-slate-700 pb-2 text-lg';
                    qText.textContent = opt.option_text; // Safe Text

                    const select = document.createElement('select');
                    select.className = 'matching-select w-full mt-2 bg-slate-900 p-3 rounded-lg border border-slate-600 focus:outline-none text-white focus:border-blue-400 transition';
                    select.dataset.leftId = opt.id;
                    select.dataset.correctPair = opt.matching_pair; // Pair value is raw
                    select.onchange = validateComplexAnswer;

                    const defaultOpt = document.createElement('option');
                    defaultOpt.value = ""; defaultOpt.text = "-- Pilih --";
                    select.appendChild(defaultOpt);

                    rightOptions.forEach(ro => {
                        const optEl = document.createElement('option');
                        optEl.value = ro.text;
                        optEl.textContent = ro.text; // Safe Text for Options
                        select.appendChild(optEl);
                    });

                    row.appendChild(qText);
                    row.appendChild(select);
                    container.appendChild(row);
                });
            }
        }

        function updateTimerVisual(val) {
            document.getElementById('timer-text').innerText = val;
            const offset = totalDash - (val / configTimer) * totalDash;
            timerCircle.style.strokeDashoffset = offset;
            
            // Warna berubah saat kritis
            if(val <= 5) { timerCircle.setAttribute('stroke', '#ef4444'); } 
            else if(val <= 10) { timerCircle.setAttribute('stroke', '#f97316'); } 
            else { timerCircle.setAttribute('stroke', '#facc15'); }
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

        // --- SUBMIT LOGIC ---
        function submitSingle(opt, btn) {
            if(isAnswered) return; isAnswered = true; clearInterval(timerInterval);
            
            const isCorrect = opt.is_correct == 1; 
            const oldScore = estimatedScore;
            
            // Update Segment Color
            updateSegmentStatus(currentIdx, isCorrect ? 'correct' : 'wrong');

            if (isCorrect) { 
                btn.classList.remove('btn-default'); btn.classList.add('correct'); 
                estimatedScore += 100; estimatedCorrect++; 
                playSound(sfxCorrect); 
                // Particle Effect
                confetti({ particleCount: 50, spread: 60, origin: { y: 0.7 } }); 
            } else { 
                btn.classList.remove('btn-default'); btn.classList.add('wrong'); 
                playSound(sfxWrong); 
                
                // Show Key
                document.querySelectorAll('#options-container button').forEach(b => {
                    if(b.dataset.isCorrect == 1) {
                        b.classList.remove('btn-default'); b.classList.add('correct'); b.classList.add('correct-indicator');
                    }
                });
            }
            
            // Update Streak & Haptic
            updateStreak(isCorrect);
            triggerHaptic(isCorrect ? 'success' : 'error');
            
            animateValue(document.getElementById("score-display"), oldScore, estimatedScore, 600);
            userAnswers.push({ question_id: questions[currentIdx].id, answer: opt.id, time_left: timeLeft });
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

            // Update Segment Color
            updateSegmentStatus(currentIdx, isCorrect ? 'correct' : 'wrong');

            const oldScore = estimatedScore;
            if (isCorrect) { 
                estimatedScore += 150; estimatedCorrect++; playSound(sfxCorrect); 
                confetti({ particleCount: 50, spread: 60, origin: { y: 0.7 } });
            } else { 
                playSound(sfxWrong); 
            }
            
            // Update Streak & Haptic
            updateStreak(isCorrect);
            triggerHaptic(isCorrect ? 'success' : 'error');
            
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
            document.getElementById('explanation-text').textContent = q.explanation || "Tidak ada pembahasan.";
            document.getElementById('reference-text').textContent = q.reference ? "📚 Sumber: " + q.reference : "";
            
            const feedbackArea = document.getElementById('feedback-area');
            feedbackArea.classList.remove('hidden');
            feedbackArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function handleTimeUp() {
            isAnswered = true; playSound(sfxWrong); triggerHaptic('error'); updateStreak(false);
            document.getElementById('options-container').classList.add('disabled-opt');
            document.getElementById('btn-confirm').classList.add('hidden');
            
            // Set Red Segment
            updateSegmentStatus(currentIdx, 'wrong');

            userAnswers.push({ 
                question_id: questions[currentIdx].id, 
                answer: null, 
                time_left: 0 
            });

            document.getElementById('explanation-text').innerText = "Waktu Habis!";
            document.getElementById('feedback-area').classList.remove('hidden');
        }

        function nextQuestion() { 
            const contentDiv = document.getElementById('question-content');
            
            // Start Exit Animation
            contentDiv.classList.add('transition-exit');
            void contentDiv.offsetWidth; // Force reflow
            contentDiv.classList.add('transition-exit-active');

            // Wait for anim then load next
            setTimeout(() => { 
                currentIdx++; 
                contentDiv.classList.add('hidden'); // Hide momentarily
                loadQuestion(); 
            }, 350); 
        }

        function finishQuiz() {
            document.getElementById('quiz-card').classList.add('hidden');
            document.getElementById('result-card').classList.remove('hidden');
            playSound(sfxFinish);
            
            const totalVal = questions.length;
            const wrongVal = totalVal - estimatedCorrect;
            const percentage = totalVal > 0 ? Math.round((estimatedCorrect / totalVal) * 100) : 0;
            
            animateValue(document.getElementById('final-score'), 0, estimatedScore, 1500);
            
            document.getElementById('final-correct').innerText = estimatedCorrect;
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

            document.getElementById('share-score').innerText = estimatedScore;
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
                // Update final UI with server real data
                const totalVal = questions.length;
                const percentage = totalVal > 0 ? Math.round((data.correct_count / totalVal) * 100) : 0;
                
                document.getElementById('final-score').innerText = data.real_score;
                document.getElementById('final-correct').innerText = data.correct_count;
                document.getElementById('final-wrong').innerText = totalVal - data.correct_count;
                document.getElementById('final-percentage').innerText = percentage + "%";
                document.getElementById('share-score').innerText = data.real_score;

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