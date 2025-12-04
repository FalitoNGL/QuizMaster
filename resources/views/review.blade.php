<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pembahasan Soal - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 text-white min-h-screen p-4 font-sans">
    <div class="max-w-3xl mx-auto">
        
        <div class="text-center mb-8 pt-8">
            <h1 class="text-3xl font-bold mb-2 text-blue-400">Pembahasan Hasil Kuis</h1>
            <p class="text-slate-400">
                Pemain: <span class="text-white font-bold">{{ $result->player_name }}</span> | 
                Skor: <span class="text-yellow-400 font-bold">{{ $result->score }}</span>
            </p>
        </div>

        <div class="space-y-6">
            @foreach($result->answers as $index => $ans)
            <div class="bg-slate-800 rounded-2xl p-6 border {{ $ans->is_correct ? 'border-green-500/30' : 'border-red-500/30' }}">
                
                <div class="flex gap-4 mb-4">
                    <div class="w-8 h-8 flex-shrink-0 rounded-full flex items-center justify-center font-bold {{ $ans->is_correct ? 'bg-green-500' : 'bg-red-500' }}">
                        {{ $index + 1 }}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">{{ $ans->question->question_text }}</h3>
                    </div>
                </div>

                <div class="ml-12 space-y-2">
                    @foreach($ans->question->options as $opt)
                        @php
                            $isSelected = $ans->option_id == $opt->id;
                            $isCorrectKey = $opt->is_correct;
                            
                            $bgClass = 'bg-slate-700/50';
                            $borderClass = 'border-slate-600';
                            $icon = '';

                            if ($isSelected && $isCorrectKey) {
                                // User Benar
                                $bgClass = 'bg-green-900/30';
                                $borderClass = 'border-green-500';
                                $icon = '<i class="fas fa-check text-green-500"></i>';
                            } elseif ($isSelected && !$isCorrectKey) {
                                // User Salah
                                $bgClass = 'bg-red-900/30';
                                $borderClass = 'border-red-500';
                                $icon = '<i class="fas fa-times text-red-500"></i>';
                            } elseif (!$isSelected && $isCorrectKey) {
                                // Kunci Jawaban (yang tidak dipilih user)
                                $bgClass = 'bg-green-900/10';
                                $borderClass = 'border-green-500/50 border-dashed';
                                $icon = '<i class="fas fa-check text-green-500/50"></i> (Jawaban Benar)';
                            }
                        @endphp

                        <div class="p-3 rounded-lg border {{ $borderClass }} {{ $bgClass }} flex justify-between items-center text-sm">
                            <span>{{ $opt->option_text }}</span>
                            <span>{!! $icon !!}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-10 mb-10 pb-10">
            <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 px-8 py-3 rounded-full font-bold transition">
                <i class="fas fa-home"></i> Kembali ke Menu
            </a>
        </div>
    </div>
</body>
</html>