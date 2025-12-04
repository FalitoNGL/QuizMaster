<!DOCTYPE html>
<html lang="id">
<head>
    <title>Statistik Pemain - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        
        /* CSS KHUSUS PRINT (Agar Rapor Rapi saat dicetak) */
        @media print {
            body { background: white !important; color: black !important; padding: 0 !important; }
            .glass { background: white !important; border: 1px solid #ccc !important; box-shadow: none !important; color: black !important; backdrop-filter: none !important; }
            .no-print { display: none !important; } /* Sembunyikan tombol saat print */
            .text-white { color: black !important; }
            .text-slate-400 { color: #555 !important; }
            .bg-slate-900 { background: white !important; }
            .bg-slate-800 { background: #f0f0f0 !important; color: black !important; border: 1px solid #ddd; }
            .bg-slate-700 { background: #e0e0e0 !important; }
            .text-blue-400 { color: #0000AA !important; }
            .text-green-400 { color: #008800 !important; }
            /* Sembunyikan scrollbar di print */
            .overflow-y-auto { overflow: visible !important; max-height: none !important; }
        }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen p-4 font-sans">
    
    <div class="max-w-5xl mx-auto">
        <div class="glass rounded-3xl p-8 mb-8 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-purple-500 no-print"></div>
            
            <div class="relative">
                <div class="w-32 h-32 rounded-full border-4 border-blue-500 p-1">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $playerName }}" alt="Avatar" class="w-full h-full rounded-full bg-slate-700">
                </div>
                <div class="absolute -bottom-2 -right-2 bg-yellow-500 text-slate-900 font-bold w-10 h-10 flex items-center justify-center rounded-full border-4 border-slate-900">
                    {{ $level }}
                </div>
            </div>

            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-bold mb-2">{{ $playerName }}</h1>
                <p class="text-slate-400 mb-4"><i class="fas fa-medal text-yellow-500"></i> Spesialis {{ $bestCategory ?? 'Pemula' }}</p>
                
                <div class="w-full bg-slate-700 h-4 rounded-full overflow-hidden relative">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-full" style="width: {{ $progressToNextLevel }}%"></div>
                    <span class="absolute inset-0 flex items-center justify-center text-[10px] font-bold tracking-wider text-white">EXP KE LEVEL {{ $level + 1 }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="bg-slate-800 p-4 rounded-xl">
                    <div class="text-2xl font-bold text-blue-400">{{ number_format($totalScore) }}</div>
                    <div class="text-xs text-slate-500 uppercase">Total Skor</div>
                </div>
                <div class="bg-slate-800 p-4 rounded-xl">
                    <div class="text-2xl font-bold text-green-400">{{ $accuracy }}%</div>
                    <div class="text-xs text-slate-500 uppercase">Akurasi</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="glass rounded-2xl p-6">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-400"></i> Performa Kategori
                </h3>
                <div class="space-y-4">
                    @foreach($categoryStats as $name => $stat)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $name }}</span>
                            <span class="font-bold {{ $stat['accuracy'] > 70 ? 'text-green-400' : 'text-yellow-400' }}">{{ $stat['accuracy'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-700 h-2 rounded-full overflow-hidden">
                            <div class="bg-purple-500 h-full" style="width: {{ $stat['accuracy'] }}%"></div>
                        </div>
                        <div class="text-xs text-slate-500 mt-1">{{ $stat['played'] }} kali main</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="glass rounded-2xl p-6">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-history text-blue-400"></i> Riwayat Terakhir
                </h3>
                <div class="space-y-4 overflow-y-auto max-h-[300px] pr-2">
                    @foreach($results->take(5) as $res)
                    <div class="flex items-center justify-between bg-slate-800/50 p-3 rounded-lg border border-slate-700">
                        <div>
                            <div class="font-bold text-sm">{{ $res->category->name }}</div>
                            <div class="text-xs text-slate-500">{{ $res->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-yellow-400">+{{ $res->score }}</div>
                            <div class="text-xs {{ $res->score > 500 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $res->correct_answers }}/{{ $res->total_questions }} Benar
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="text-center mt-8 mb-8 no-print">
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-bold transition mr-4">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
            
            <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white px-8 py-3 rounded-full font-bold transition">
                <i class="fas fa-home"></i> Kembali ke Menu
            </a>
        </div>
    </div>
</body>
</html>