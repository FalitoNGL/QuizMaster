<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembahasan - {{ $result->category->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        /* --- 1. ATMOSFER & LATAR BELAKANG (Konsisten dengan Play) --- */
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
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3); 
            position: relative; 
        }

        /* --- 2. STYLE ELEMENT VISUAL --- */
        /* Tombol 3D (Non-Clickable untuk Review) */
        .box-3d {
            border-bottom-width: 4px;
            border-radius: 0.75rem; /* rounded-xl */
            position: relative;
            transition: transform 0.1s;
        }
        
        /* Tombol 3D (Clickable) */
        .btn-3d {
            transition: all 0.1s;
            border-bottom-width: 4px;
            transform: translateY(0);
        }
        .btn-3d:active {
            transform: translateY(4px);
            border-bottom-width: 0px;
            margin-bottom: 4px; 
        }

        /* Varian Warna Box */
        .box-default { background-color: #334155; border-color: #1e293b; color: #94a3b8; } /* Slate 700 */
        .box-correct { background-color: #10b981; border-color: #047857; color: white; } /* Hijau */
        .box-wrong { background-color: #ef4444; border-color: #b91c1c; color: white; } /* Merah */
        .box-info { background-color: #3b82f6; border-color: #1d4ed8; color: white; } /* Biru */
        
        .correct-indicator { border: 2px solid #34d399; box-shadow: 0 0 10px rgba(52, 211, 153, 0.3); }

        /* Animasi Modal */
        @keyframes popIn { 0% { transform: scale(0.9); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .modal-animate { animation: popIn 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen font-sans">
    @include('partials.loading-screen')
    
    <div class="ambient-light">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    <div class="noise-overlay"></div>

    <div class="relative z-10 max-w-4xl mx-auto p-4 pb-32">
        
        <div class="flex items-center justify-between mb-8 pt-4">
            <a href="{{ route('menu') }}" class="glass w-12 h-12 rounded-full flex items-center justify-center hover:bg-white/10 transition text-white">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="text-center">
                <h1 class="text-xl font-bold text-white drop-shadow-md">Pembahasan Soal</h1>
                <span class="text-xs text-blue-300 font-mono tracking-wider uppercase">{{ $result->category->name }}</span>
            </div>
            <div class="w-12"></div> </div>

        <div class="glass rounded-3xl p-6 mb-8 border-t border-white/10">
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-3 rounded-2xl bg-slate-800/50 border border-slate-700/50">
                    <div class="text-[10px] uppercase text-slate-400 font-bold mb-1 tracking-wider">Skor</div>
                    <div class="text-3xl font-bold text-yellow-400 font-mono shadow-yellow-500/20 drop-shadow-sm">{{ $result->score }}</div>
                </div>
                <div class="text-center p-3 rounded-2xl bg-slate-800/50 border border-slate-700/50">
                    <div class="text-[10px] uppercase text-slate-400 font-bold mb-1 tracking-wider">Akurasi</div>
                    <div class="text-3xl font-bold text-blue-400 font-mono">
                        {{ $result->total_questions > 0 ? round(($result->correct_answers / $result->total_questions) * 100) : 0 }}<span class="text-sm">%</span>
                    </div>
                </div>
                <div class="text-center p-3 rounded-2xl bg-slate-800/50 border border-slate-700/50">
                    <div class="text-[10px] uppercase text-slate-400 font-bold mb-1 tracking-wider">Benar</div>
                    <div class="text-3xl font-bold text-green-400 font-mono">
                        {{ $result->correct_answers }}<span class="text-sm text-slate-500 font-bold text-lg">/{{ $result->total_questions }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-white/5 flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="text-xs text-slate-400 italic">
                    <i class="fas fa-trophy text-yellow-500 mr-1"></i> Bagikan hasil hebatmu!
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button onclick="generateShareImage()" id="btn-share-img" class="flex-1 sm:flex-none btn-3d px-4 py-2 rounded-xl text-xs font-bold text-white bg-purple-600 hover:bg-purple-500 flex items-center justify-center gap-2" style="border-bottom-color: #581c87;">
                        <i class="fas fa-camera"></i> Gambar
                    </button>
                    <button onclick="shareToWa()" class="flex-1 sm:flex-none btn-3d px-4 py-2 rounded-xl text-xs font-bold text-white bg-green-600 hover:bg-green-500 flex items-center justify-center gap-2" style="border-bottom-color: #15803d;">
                        <i class="fab fa-whatsapp text-lg"></i> WA
                    </button>
                    <button onclick="copyText()" class="btn-3d px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-700 hover:bg-slate-600" style="border-bottom-color: #1e293b;">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-3 mb-6 sticky top-4 z-40">
            <div class="glass p-1.5 rounded-full flex gap-1 shadow-xl backdrop-blur-xl">
                <button onclick="filterQuestions('all')" id="btn-all" class="px-5 py-2 rounded-full font-bold text-xs bg-blue-600 text-white shadow-lg transition-all">
                    Semua Soal
                </button>
                <button onclick="filterQuestions('wrong')" id="btn-wrong" class="px-5 py-2 rounded-full font-bold text-xs bg-transparent hover:bg-white/5 text-slate-300 transition-all flex items-center gap-2">
                    Salah Saja <span class="bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-full">{{ $result->total_questions - $result->correct_answers }}</span>
                </button>
            </div>
        </div>

        <div class="space-y-6" id="questions-list">
            @foreach($result->answers as $index => $ans)
            <div class="question-card glass rounded-3xl p-6 md:p-8 {{ $ans->is_correct ? 'correct-card' : 'wrong-card' }} relative overflow-hidden group">
                
                <div class="absolute top-0 right-0 px-4 py-2 text-[10px] font-bold uppercase tracking-widest rounded-bl-2xl shadow-sm {{ $ans->is_correct ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                    {{ $ans->is_correct ? 'BENAR' : 'SALAH' }}
                </div>

                <div class="mb-6">
                    <div class="flex gap-4 items-start">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg shadow-inner {{ $ans->is_correct ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow pt-1">
                            <h3 class="text-lg font-bold leading-relaxed text-white">{{ $ans->question->question_text }}</h3>
                            
                            @if($ans->question->image_path)
                                <div class="mt-4 rounded-xl overflow-hidden border border-white/10 shadow-lg inline-block">
                                    <img src="{{ asset('storage/' . $ans->question->image_path) }}" class="max-h-56 object-cover">
                                </div>
                            @endif
                            @if($ans->question->audio_path)
                                <div class="mt-3">
                                    <audio controls class="w-full max-w-md h-10 rounded-lg"><source src="{{ asset('storage/' . $ans->question->audio_path) }}"></audio>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-3 pl-0 md:pl-14">
                    @foreach($ans->question->options as $opt)
                        @php
                            $isSelected = $ans->option_id == $opt->id;
                            $isCorrectKey = $opt->is_correct;
                            
                            // Style Logic
                            $boxClass = 'box-default'; // Default abu-abu
                            $icon = '';
                            
                            if ($isCorrectKey) {
                                // Kunci Jawaban (Selalu Hijau)
                                $boxClass = 'box-correct'; 
                                $icon = '<i class="fas fa-check-circle text-xl"></i>';
                            } elseif ($isSelected && !$isCorrectKey) {
                                // Jawaban User Salah (Merah)
                                $boxClass = 'box-wrong';
                                $icon = '<i class="fas fa-times-circle text-xl"></i>';
                            } elseif (!$isSelected && !$isCorrectKey) {
                                // Opsi Netral
                                $boxClass = 'box-default opacity-60';
                            }
                        @endphp

                        <div class="box-3d {{ $boxClass }} p-0 flex items-stretch overflow-hidden">
                            <div class="w-14 flex-shrink-0 flex items-center justify-center bg-black/20 border-r border-black/10">
                                @if($icon) {!! $icon !!} @else <span class="text-slate-400 text-sm"><i class="far fa-circle"></i></span> @endif
                            </div>
                            <div class="p-4 flex-grow font-medium flex items-center justify-between">
                                <span>
                                    {{ $opt->option_text }}
                                    @if($ans->question->type == 'matching' && $opt->matching_pair)
                                        <span class="text-xs opacity-80 block mt-1"><i class="fas fa-link mr-1"></i> Pasangan: {{ $opt->matching_pair }}</span>
                                    @endif
                                </span>
                                @if($isSelected && $isCorrectKey)
                                    <span class="text-[10px] font-bold bg-white/20 px-2 py-1 rounded uppercase">Jawabanmu</span>
                                @elseif($isSelected && !$isCorrectKey)
                                    <span class="text-[10px] font-bold bg-white/20 px-2 py-1 rounded uppercase">Jawabanmu</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($ans->question->explanation)
                    <div class="mt-6 ml-0 md:ml-14 p-5 bg-slate-900/60 rounded-2xl border-l-4 border-yellow-500 relative">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-lightbulb text-yellow-500 text-lg"></i>
                            <span class="text-yellow-500 font-bold text-sm uppercase tracking-wider">Pembahasan</span>
                        </div>
                        <p class="text-slate-300 text-sm leading-relaxed">{{ $ans->question->explanation }}</p>
                        @if($ans->question->reference)
                            <div class="mt-3 pt-3 border-t border-white/5 text-[10px] text-slate-500 flex items-center gap-1 font-mono">
                                <i class="fas fa-book"></i> Sumber: {{ $ans->question->reference }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="fixed bottom-0 left-0 w-full p-4 z-50 pointer-events-none">
            <div class="max-w-3xl mx-auto flex gap-3 pointer-events-auto">
                <a href="{{ route('menu') }}" class="flex-1 btn-3d bg-slate-700 text-white py-3.5 rounded-xl font-bold text-center shadow-2xl border-slate-900 flex items-center justify-center gap-2 group" style="border-bottom-color: #0f172a;">
                    <i class="fas fa-home group-hover:-translate-x-1 transition"></i> <span class="hidden sm:inline">Menu</span>
                </a>
                <a href="{{ route('quiz.play', $result->category->slug) }}" class="flex-[2] btn-3d bg-blue-600 text-white py-3.5 rounded-xl font-bold shadow-2xl flex items-center justify-center gap-2 text-center group" style="border-bottom-color: #1e3a8a;">
                    <i class="fas fa-redo-alt group-hover:rotate-180 transition duration-500"></i> Main Lagi
                </a>
            </div>
        </div>
    </div>

    @php
        $percentage = $result->total_questions > 0 ? round(($result->correct_answers / $result->total_questions) * 100) : 0;
        $gradeTitle = "Good Job!";
        $gradeColor = "text-blue-400";
        if($percentage == 100) { $gradeTitle = "LEGENDARY!"; $gradeColor = "text-yellow-400"; }
        elseif($percentage >= 80) { $gradeTitle = "EXCELLENT!"; $gradeColor = "text-purple-400"; }
        elseif($percentage < 50) { $gradeTitle = "KEEP LEARNING!"; $gradeColor = "text-slate-400"; }
    @endphp

    <div id="shareable-ticket" class="fixed left-[-9999px] top-0 w-[400px] bg-gradient-to-br from-slate-800 to-slate-900 p-8 rounded-3xl border-4 border-blue-500/50 text-center shadow-2xl text-white font-sans relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="relative z-10">
            <div class="text-sm uppercase tracking-widest text-blue-400 mb-2 font-bold">Quiz Master Result</div>
            <h1 class="text-3xl font-extrabold mb-1 text-white">{{ $result->category->name }}</h1>
            <h2 class="text-2xl font-bold {{ $gradeColor }} mb-6 mt-4">{{ $gradeTitle }}</h2>
            
            <div class="bg-slate-900/80 rounded-2xl p-6 border border-slate-700 mb-6 relative">
                <div class="text-sm text-slate-400 uppercase font-bold">Skor Kamu</div>
                <div class="text-6xl font-extrabold text-white mt-2">{{ $result->score }}</div>
            </div>

            <div class="grid grid-cols-3 gap-2 mb-6 text-center">
                <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700">
                    <div class="text-blue-400 font-bold text-lg">{{ $percentage }}%</div>
                    <div class="text-[9px] text-slate-500 uppercase font-bold">Akurasi</div>
                </div>
                <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700">
                    <div class="text-green-400 font-bold text-lg">{{ $result->correct_answers }}</div>
                    <div class="text-[9px] text-slate-500 uppercase font-bold">Benar</div>
                </div>
                <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700">
                    <div class="text-red-400 font-bold text-lg">{{ $result->total_questions - $result->correct_answers }}</div>
                    <div class="text-[9px] text-slate-500 uppercase font-bold">Salah</div>
                </div>
            </div>

            <div class="mt-4 text-[10px] text-slate-500">
                Pemain: <span class="font-bold text-white">{{ $result->player_name }}</span> • {{ date('d M Y') }}
            </div>
        </div>
    </div>

    <div id="share-modal" class="fixed inset-0 bg-black/90 z-[60] hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-slate-800 p-6 rounded-3xl max-w-sm w-full text-center border border-slate-700 modal-animate shadow-2xl relative">
            <h3 class="text-xl font-bold text-white mb-2">Siap Dibagikan! 📸</h3>
            <p class="text-xs text-slate-400 mb-4">Tekan tahan gambar untuk menyimpan.</p>
            <div id="generated-image-container" class="mb-5 rounded-xl overflow-hidden border-2 border-slate-600 shadow-lg bg-black"></div>
            <button onclick="document.getElementById('share-modal').classList.add('hidden')" class="btn-3d bg-slate-700 text-white py-3 px-6 rounded-xl font-bold w-full" style="border-bottom-color: #1e293b;">
                Tutup
            </button>
        </div>
    </div>

    <script>
        function filterQuestions(mode) {
            const allCards = document.querySelectorAll('.question-card');
            const btnAll = document.getElementById('btn-all');
            const btnWrong = document.getElementById('btn-wrong');

            if (mode === 'wrong') {
                allCards.forEach(card => {
                    if (card.classList.contains('correct-card')) card.style.display = 'none';
                    else card.style.display = 'block';
                });
                // Update Button Style
                btnAll.className = "px-5 py-2 rounded-full font-bold text-xs bg-transparent hover:bg-white/5 text-slate-300 transition-all";
                btnWrong.className = "px-5 py-2 rounded-full font-bold text-xs bg-red-600 text-white shadow-lg transition-all flex items-center gap-2";
            } else {
                allCards.forEach(card => card.style.display = 'block');
                // Update Button Style
                btnAll.className = "px-5 py-2 rounded-full font-bold text-xs bg-blue-600 text-white shadow-lg transition-all";
                btnWrong.className = "px-5 py-2 rounded-full font-bold text-xs bg-transparent hover:bg-white/5 text-slate-300 transition-all flex items-center gap-2";
            }
        }

        function generateShareImage() {
            const ticket = document.getElementById('shareable-ticket');
            const modal = document.getElementById('share-modal');
            const container = document.getElementById('generated-image-container');
            const btn = document.getElementById('btn-share-img');

            btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            html2canvas(ticket, { backgroundColor: null, scale: 2 }).then(canvas => {
                const img = new Image();
                img.src = canvas.toDataURL("image/png");
                img.className = "w-full h-auto object-contain";
                container.innerHTML = ''; container.appendChild(img);
                modal.classList.remove('hidden');
                btn.disabled = false; btn.innerHTML = '<i class="fas fa-camera"></i> Gambar';
            }).catch(err => {
                alert("Gagal membuat gambar.");
                btn.disabled = false; btn.innerHTML = '<i class="fas fa-camera"></i> Gambar';
            });
        }

        function copyText() {
            const text = "Skor saya di QuizMaster ({{ $result->category->name }}): {{ $result->score }}! ({{ $percentage }}% Akurasi). Bisa kalahkan saya?";
            navigator.clipboard.writeText(text).then(() => alert("Teks disalin!"));
        }

        function shareToWa() {
            const text = "Cek skor saya di QuizMaster! Kategori: {{ $result->category->name }} | Skor: {{ $result->score }} ({{ $percentage }}% Benar) 🔥";
            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
        }
    </script>
</body>
</html>