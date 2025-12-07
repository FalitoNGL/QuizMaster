<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembahasan Soal - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <style>
        /* Animasi Modal */
        @keyframes popIn { 0% { transform: scale(0.9); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .modal-animate { animation: popIn 0.3s ease-out; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen font-sans">
    
    <a href="{{ route('menu') }}" class="fixed top-4 left-4 z-50 bg-slate-800/80 backdrop-blur hover:bg-slate-700 text-white w-10 h-10 rounded-full flex items-center justify-center border border-slate-600 shadow-lg transition">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="max-w-3xl mx-auto p-4 pb-32"> <div class="text-center mb-6 pt-8">
            <h1 class="text-3xl font-bold mb-1 text-blue-400">Pembahasan Hasil</h1>
            <p class="text-slate-400 text-sm">
                Quiz: <span class="text-white font-bold">{{ $result->category->name }}</span>
            </p>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 text-center shadow-lg relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition"><i class="fas fa-star text-4xl"></i></div>
                <div class="text-[10px] uppercase text-slate-400 font-bold mb-1">Skor</div>
                <div class="text-2xl font-extrabold text-yellow-400">{{ $result->score }}</div>
            </div>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 text-center shadow-lg relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition"><i class="fas fa-percent text-4xl"></i></div>
                <div class="text-[10px] uppercase text-slate-400 font-bold mb-1">Akurasi</div>
                <div class="text-2xl font-extrabold text-blue-400">
                    {{ $result->total_questions > 0 ? round(($result->correct_answers / $result->total_questions) * 100) : 0 }}%
                </div>
            </div>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 text-center shadow-lg relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition"><i class="fas fa-check-circle text-4xl"></i></div>
                <div class="text-[10px] uppercase text-slate-400 font-bold mb-1">Benar</div>
                <div class="text-2xl font-extrabold text-green-400">
                    {{ $result->correct_answers }}<span class="text-sm text-slate-500 font-normal">/{{ $result->total_questions }}</span>
                </div>
            </div>
        </div>

        <div class="bg-slate-800/50 rounded-xl p-4 mb-8 border border-slate-700 flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="text-sm text-slate-300">
                <i class="fas fa-share-alt text-purple-400 mr-2"></i> Bagikan pencapaianmu:
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button onclick="generateShareImage()" id="btn-share-img" class="flex-1 sm:flex-none bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-lg font-bold text-sm transition shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-camera"></i> <span class="hidden sm:inline">Gambar</span>
                </button>
                <button onclick="shareToWa()" class="flex-1 sm:flex-none bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg font-bold text-sm transition shadow-lg flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp text-lg"></i> <span class="hidden sm:inline">WhatsApp</span>
                </button>
                <button onclick="copyText()" class="flex-1 sm:flex-none bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg font-bold text-sm transition shadow-lg flex items-center justify-center gap-2">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>

        <div class="flex justify-center gap-3 mb-6 sticky top-4 z-40 bg-gray-900/95 p-2 rounded-full border border-slate-700 backdrop-blur shadow-xl w-max mx-auto">
            <button onclick="filterQuestions('all')" id="btn-all" class="px-5 py-1.5 rounded-full font-bold text-xs bg-blue-600 text-white transition shadow-lg">
                Semua
            </button>
            <button onclick="filterQuestions('wrong')" id="btn-wrong" class="px-5 py-1.5 rounded-full font-bold text-xs bg-slate-700 hover:bg-red-600/80 text-gray-300 hover:text-white transition">
                Salah Saja <span class="ml-1 bg-red-500 text-white text-[9px] px-1.5 rounded-full">{{ $result->total_questions - $result->correct_answers }}</span>
            </button>
        </div>

        <div class="space-y-5" id="questions-list">
            @foreach($result->answers as $index => $ans)
            <div class="question-card {{ $ans->is_correct ? 'correct-card' : 'wrong-card' }} bg-slate-800 rounded-2xl p-5 border {{ $ans->is_correct ? 'border-green-500/30' : 'border-red-500/30' }} relative overflow-hidden group">
                
                <div class="absolute top-0 right-0 px-3 py-1 text-[9px] font-bold uppercase tracking-wider rounded-bl-xl border-l border-b border-slate-700 {{ $ans->is_correct ? 'bg-green-900/20 text-green-400' : 'bg-red-900/20 text-red-400' }}">
                    {{ $ans->question->type }}
                </div>

                <div class="flex gap-4 mb-3">
                    <div class="w-8 h-8 flex-shrink-0 rounded-full flex items-center justify-center font-bold text-sm {{ $ans->is_correct ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="w-full">
                        <h3 class="text-base font-semibold pr-8 leading-snug">{{ $ans->question->question_text }}</h3>
                        
                        @if($ans->question->image_path)
                            <img src="{{ asset('storage/' . $ans->question->image_path) }}" class="mt-3 rounded-lg max-h-40 border border-slate-600 object-cover">
                        @endif
                        @if($ans->question->audio_path)
                            <audio controls class="mt-3 w-full h-8"><source src="{{ asset('storage/' . $ans->question->audio_path) }}"></audio>
                        @endif

                        @if($ans->question->explanation)
                            <div class="text-sm text-slate-300 mt-4 p-3 bg-slate-900/60 rounded-lg border-l-2 border-yellow-500">
                                <strong class="text-yellow-500 block mb-1 text-xs uppercase tracking-wide"><i class="fas fa-lightbulb mr-1"></i> Pembahasan</strong>
                                {{ $ans->question->explanation }}
                                @if($ans->question->reference)
                                    <div class="mt-2 pt-2 border-t border-slate-700/50 text-[10px] text-slate-500 italic flex items-center gap-1">
                                        <i class="fas fa-book"></i> {{ $ans->question->reference }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ml-12 space-y-2">
                    @foreach($ans->question->options as $opt)
                        @php
                            $isSelected = $ans->option_id == $opt->id;
                            $isCorrectKey = $opt->is_correct;
                            $bgClass = 'bg-slate-700/30'; $borderClass = 'border-slate-700'; $icon = ''; $textClass = 'text-slate-400';

                            if ($isSelected && $isCorrectKey) {
                                $bgClass = 'bg-green-900/30'; $borderClass = 'border-green-500'; 
                                $icon = '<i class="fas fa-check text-green-500"></i>'; $textClass = 'text-green-100 font-bold';
                            } elseif ($isSelected && !$isCorrectKey) {
                                $bgClass = 'bg-red-900/30'; $borderClass = 'border-red-500'; 
                                $icon = '<i class="fas fa-times text-red-500"></i>'; $textClass = 'text-red-100 font-bold';
                            } elseif (!$isSelected && $isCorrectKey) {
                                $bgClass = 'bg-green-900/10'; $borderClass = 'border-green-500/50 border-dashed'; 
                                $icon = '<i class="fas fa-check text-green-500/50"></i>'; 
                            }
                        @endphp
                        <div class="p-2.5 rounded-lg border {{ $borderClass }} {{ $bgClass }} flex justify-between items-center text-sm transition">
                            <span class="{{ $textClass }}">
                                {{ $opt->option_text }}
                                @if($ans->question->type == 'matching' && $opt->matching_pair)
                                    <span class="text-xs text-slate-500 ml-2">➡️ {{ $opt->matching_pair }}</span>
                                @endif
                            </span>
                            <span>{!! $icon !!}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="fixed bottom-0 left-0 w-full bg-slate-900/90 backdrop-blur border-t border-slate-700 p-4 z-50">
            <div class="max-w-3xl mx-auto flex gap-3">
                <a href="{{ route('menu') }}" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white py-3.5 rounded-xl font-bold transition text-center border border-slate-600 shadow-md">
                    <i class="fas fa-home mr-2"></i> Kembali ke Menu
                </a>
                <a href="{{ route('quiz.play', $result->category->slug) }}" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white py-3.5 rounded-xl font-bold shadow-lg transition text-center flex items-center justify-center gap-2">
                    <i class="fas fa-redo-alt"></i> Main Lagi
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

    <div id="shareable-ticket" class="fixed left-[-9999px] top-0 w-[400px] bg-gradient-to-b from-slate-800 to-slate-900 p-8 rounded-none text-center text-white font-sans relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl"></div>
        <div class="absolute top-20 -left-10 w-32 h-32 bg-purple-600/20 rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <div class="flex items-center justify-center gap-2 mb-2 text-slate-400">
                <i class="fas fa-gamepad"></i> <span class="text-xs font-bold tracking-widest uppercase">Quiz Master Result</span>
            </div>
            
            <h1 class="text-2xl font-bold text-white mb-4 leading-tight">{{ $result->category->name }}</h1>
            
            <div class="bg-slate-800/80 rounded-2xl p-6 border border-slate-600 shadow-2xl mb-6 relative">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-slate-900 px-3 py-1 rounded-full border border-slate-600 text-[10px] font-bold text-slate-400 uppercase">
                    Skor Kamu
                </div>
                <div class="text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mt-2">
                    {{ $result->score }}
                </div>
                <div class="text-sm font-bold {{ $gradeColor }} mt-1 tracking-widest">{{ $gradeTitle }}</div>
            </div>

            <div class="grid grid-cols-3 gap-2 mb-6">
                <div class="bg-slate-800 p-3 rounded-lg border border-slate-700">
                    <div class="text-blue-400 font-bold text-lg">{{ $percentage }}%</div>
                    <div class="text-[9px] text-slate-500 uppercase">Akurasi</div>
                </div>
                <div class="bg-slate-800 p-3 rounded-lg border border-slate-700">
                    <div class="text-green-400 font-bold text-lg">{{ $result->correct_answers }}</div>
                    <div class="text-[9px] text-slate-500 uppercase">Benar</div>
                </div>
                <div class="bg-slate-800 p-3 rounded-lg border border-slate-700">
                    <div class="text-red-400 font-bold text-lg">{{ $result->total_questions - $result->correct_answers }}</div>
                    <div class="text-[9px] text-slate-500 uppercase">Salah</div>
                </div>
            </div>

            <div class="border-t border-slate-700 pt-4 flex items-center justify-between text-xs text-slate-400">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-slate-700 flex items-center justify-center font-bold text-white">
                        {{ substr($result->player_name, 0, 1) }}
                    </div>
                    <span>{{ $result->player_name }}</span>
                </div>
                <div>{{ date('d M Y') }}</div>
            </div>
        </div>
    </div>

    <div id="share-modal" class="fixed inset-0 bg-black/90 z-[60] hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-slate-800 p-6 rounded-3xl max-w-sm w-full text-center border border-slate-700 modal-animate shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-2">Siap Dibagikan! 📸</h3>
            <p class="text-xs text-slate-400 mb-4">Tekan tahan gambar untuk menyimpan.</p>
            <div id="generated-image-container" class="mb-5 rounded-xl overflow-hidden border-2 border-slate-600 shadow-lg bg-black"></div>
            <button onclick="document.getElementById('share-modal').classList.add('hidden')" class="bg-slate-700 hover:bg-slate-600 text-white py-3 px-6 rounded-xl font-bold w-full transition">
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
                btnAll.className = "px-5 py-1.5 rounded-full font-bold text-xs bg-slate-700 hover:bg-blue-600/80 text-gray-300 hover:text-white transition";
                btnWrong.className = "px-5 py-1.5 rounded-full font-bold text-xs bg-red-600 text-white transition shadow-lg";
            } else {
                allCards.forEach(card => card.style.display = 'block');
                btnAll.className = "px-5 py-1.5 rounded-full font-bold text-xs bg-blue-600 text-white transition shadow-lg";
                btnWrong.className = "px-5 py-1.5 rounded-full font-bold text-xs bg-slate-700 hover:bg-red-600/80 text-gray-300 hover:text-white transition";
            }
        }

        // Generate Gambar Tiket
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
                btn.disabled = false; btn.innerHTML = '<i class="fas fa-camera"></i> <span class="hidden sm:inline">Gambar</span>';
            }).catch(err => {
                alert("Gagal membuat gambar.");
                btn.disabled = false; btn.innerHTML = '<i class="fas fa-camera"></i>';
            });
        }

        // Copy Text
        function copyText() {
            const text = "Skor saya di QuizMaster ({{ $result->category->name }}): {{ $result->score }}! ({{ $percentage }}% Akurasi). Bisa kalahkan saya?";
            navigator.clipboard.writeText(text).then(() => alert("Teks disalin!"));
        }

        // Share WhatsApp Direct
        function shareToWa() {
            const text = "Cek skor saya di QuizMaster! Kategori: {{ $result->category->name }} | Skor: {{ $result->score }} ({{ $percentage }}% Benar) 🔥";
            window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
        }
    </script>
</body>
</html>