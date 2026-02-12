<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Statistik Pemain - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen transition-colors duration-300">
    
    @include('partials.navbar')

    <div class="max-w-6xl mx-auto px-4 pt-24 pb-8">
        <!-- Profile Header -->
        <div class="glass rounded-3xl p-8 mb-8 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden shadow-lg">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-purple-500 no-print"></div>
            
            <div class="relative group">
                <div class="w-32 h-32 rounded-full border-4 border-blue-500 p-1 transition transform group-hover:scale-105">
                    <img src="https://api.dicebear.com/7.x/{{ session('avatar_style', 'avataaars') }}/svg?seed={{ $playerName }}" alt="Avatar" class="w-full h-full rounded-full bg-gray-200 dark:bg-slate-700">
                </div>
                <div class="absolute -bottom-2 -right-2 bg-yellow-500 text-slate-900 font-bold w-10 h-10 flex items-center justify-center rounded-full border-4 border-white dark:border-slate-900">
                    {{ $level }}
                </div>
            </div>

            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-bold mb-2">{{ $playerName }}</h1>
                <p class="text-slate-500 dark:text-slate-400 mb-4"><i class="fas fa-medal text-yellow-500"></i> Spesialis {{ $bestCategory ?? 'Pemula' }}</p>
                
                <div class="w-full bg-gray-200 dark:bg-slate-700 h-4 rounded-full overflow-hidden relative">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-full relative" style="width: {{ $progressToNextLevel }}%">
                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                    </div>
                    <span class="absolute inset-0 flex items-center justify-center text-[10px] font-bold tracking-wider text-slate-600 dark:text-white mix-blend-difference">EXP KE LEVEL {{ $level + 1 }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="bg-white/50 dark:bg-slate-800/50 p-4 rounded-xl backdrop-blur-sm border border-gray-200 dark:border-slate-700">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($totalScore) }}</div>
                    <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Skor</div>
                </div>
                <div class="bg-white/50 dark:bg-slate-800/50 p-4 rounded-xl backdrop-blur-sm border border-gray-200 dark:border-slate-700">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $accuracy }}%</div>
                    <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Akurasi Global</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Radar Chart: Keahlian -->
            <div class="glass rounded-2xl p-6 shadow-lg">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-spider text-purple-500 dark:text-purple-400"></i> Peta Keahlian
                </h3>
                <div class="h-64 relative">
                    <canvas id="skillRadar"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Frekuensi Main -->
            <div class="glass rounded-2xl p-6 shadow-lg">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-gamepad text-blue-500 dark:text-blue-400"></i> Aktivitas Kategori
                </h3>
                <div class="h-64 relative">
                    <canvas id="activityBar"></canvas>
                </div>
            </div>
        </div>

        <!-- Detail Table (Collapsible optionally, but kept visible for detail lovers) -->
        <div class="glass rounded-2xl p-6 mb-8">
            <h3 class="text-xl font-bold mb-4 flex items-center gap-2">
                <i class="fas fa-list text-slate-500"></i> Detail Statistik
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 border-b border-gray-200 dark:border-slate-700">
                            <th class="p-3">Kategori</th>
                            <th class="p-3 text-center">Akurasi</th>
                            <th class="p-3 text-center">Dimainkan</th>
                            <th class="p-3 text-center">Performa</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($categoryStats as $name => $stat)
                        <tr class="border-b border-gray-100 dark:border-slate-800 hover:bg-white/5 transition">
                            <td class="p-3 font-medium">{{ $name }}</td>
                            <td class="p-3 text-center font-bold {{ $stat['accuracy'] > 70 ? 'text-green-500' : ($stat['accuracy'] > 40 ? 'text-yellow-500' : 'text-red-500') }}">{{ $stat['accuracy'] }}%</td>
                            <td class="p-3 text-center">{{ $stat['played'] }}x</td>
                            <td class="p-3">
                                <div class="w-full bg-gray-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                    <div class="{{ $stat['accuracy'] > 70 ? 'bg-green-500' : 'bg-yellow-500' }} h-full" style="width: {{ $stat['accuracy'] }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent History -->
        <div class="glass rounded-2xl p-6 mb-8">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                <i class="fas fa-history text-blue-500 dark:text-blue-400"></i> Riwayat Terakhir
            </h3>
            <div class="space-y-4 overflow-y-auto max-h-[300px] pr-2">
                @foreach($results->take(5) as $res)
                <div class="flex items-center justify-between bg-gray-100 dark:bg-slate-800/50 p-3 rounded-lg border border-gray-200 dark:border-slate-700">
                    <div>
                        <div class="font-bold text-sm">{{ $res->category->name }}</div>
                        <div class="text-xs text-slate-500">{{ $res->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-yellow-600 dark:text-yellow-400">+{{ $res->score }}</div>
                        <div class="text-xs {{ $res->score > 500 ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                            {{ $res->correct_answers }}/{{ $res->total_questions }} Benar
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-center mt-8 mb-8 no-print">
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-bold transition">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <script>
        // Prepare Data
        const statsData = @json($categoryStats);
        const labels = Object.keys(statsData);
        const accuracyData = labels.map(l => statsData[l].accuracy);
        const playedData = labels.map(l => statsData[l].played);

        // Chart Config
        Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#94a3b8' : '#475569';
        Chart.defaults.borderColor = document.documentElement.classList.contains('dark') ? '#334155' : '#e2e8f0';

        // 1. Radar Chart (Skill)
        new Chart(document.getElementById('skillRadar'), {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Akurasi (%)',
                    data: accuracyData,
                    backgroundColor: 'rgba(139, 92, 246, 0.2)', // Purple transparent
                    borderColor: 'rgba(139, 92, 246, 1)',
                    pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: document.documentElement.classList.contains('dark') ? '#334155' : '#e2e8f0' },
                        grid: { color: document.documentElement.classList.contains('dark') ? '#334155' : '#e2e8f0' },
                        pointLabels: {
                            font: { size: 11, weight: 'bold' },
                            color: document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#1e293b'
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // 2. Bar Chart (Activity)
        new Chart(document.getElementById('activityBar'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kali Dimainkan',
                    data: playedData,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)', // Blue
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>