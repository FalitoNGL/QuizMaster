<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen font-sans">
    
    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-lg">
        <div class="font-bold text-xl text-yellow-500 flex items-center gap-2">
            <i class="fas fa-chart-line"></i> Admin Panel
        </div>
        <div class="flex gap-4 text-sm font-semibold">
            <a href="{{ route('menu') }}" class="text-gray-400 hover:text-white px-3 py-1 rounded hover:bg-gray-700 transition">
                <i class="fas fa-play mr-1"></i> Lihat Aplikasi
            </a>
            <a href="{{ route('admin.logout') }}" class="text-red-400 hover:text-red-300 px-3 py-1 rounded hover:bg-red-900/20 transition">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center text-xl"><i class="fas fa-gamepad"></i></div>
                <div><div class="text-2xl font-bold">{{ number_format($totalGames) }}</div><div class="text-xs text-gray-400 uppercase">Total Main</div></div>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center text-xl"><i class="fas fa-users"></i></div>
                <div><div class="text-2xl font-bold">{{ number_format($totalPlayers) }}</div><div class="text-xs text-gray-400 uppercase">Pemain</div></div>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center text-xl"><i class="fas fa-question"></i></div>
                <div><div class="text-2xl font-bold">{{ number_format($totalQuestions) }}</div><div class="text-xs text-gray-400 uppercase">Bank Soal</div></div>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-xl"><i class="fas fa-star"></i></div>
                <div><div class="text-2xl font-bold">{{ number_format($avgScore) }}</div><div class="text-xs text-gray-400 uppercase">Rata-rata Skor</div></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                <h3 class="font-bold text-lg mb-4 text-gray-200 flex items-center gap-2"><i class="fas fa-chart-pie text-pink-500"></i> Statistik Kategori</h3>
                <div class="relative h-64 w-full"><canvas id="categoryChart"></canvas></div>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-lg">
                <h3 class="font-bold text-lg mb-4 text-gray-200 flex items-center gap-2"><i class="fas fa-history text-cyan-500"></i> Aktivitas Terbaru</h3>
                <div class="space-y-4">
                    @forelse($recentActivities as $act)
                    <div class="flex items-center justify-between border-b border-gray-700 pb-2 last:border-0">
                        <div><div class="font-bold text-sm text-white">{{ $act->player_name }}</div><div class="text-xs text-gray-500">{{ $act->category->name }}</div></div>
                        <div class="text-right"><div class="font-bold text-yellow-400">+{{ $act->score }}</div><div class="text-[10px] text-gray-500">{{ $act->created_at->diffForHumans() }}</div></div>
                    </div>
                    @empty
                    <div class="text-gray-500 text-sm text-center py-4">Belum ada data.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4 mt-10">
            <h2 class="text-xl font-bold border-l-4 border-yellow-500 pl-3">Manajemen Bank Soal</h2>
            
            <div class="flex gap-3">
                <a href="{{ route('admin.categories') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold shadow-lg flex items-center gap-2 transition transform hover:scale-105">
                    <i class="fas fa-tags"></i> Kategori
                </a>

                <a href="{{ route('admin.import') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-bold shadow-lg flex items-center gap-2 transition transform hover:scale-105">
                    <i class="fas fa-file-upload"></i> Import JSON
                </a>

                <a href="{{ route('admin.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold shadow-lg flex items-center gap-2 transition transform hover:scale-105">
                    <i class="fas fa-plus"></i> Tambah Soal
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 text-green-300 p-4 rounded-lg mb-6 border border-green-500/30 flex items-center gap-2"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-xl border border-gray-700">
            <table class="w-full text-left">
                <thead class="bg-gray-700 text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Pertanyaan</th>
                        <th class="px-6 py-4 text-center">Tipe</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($questions as $q)
                    <tr class="hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4"><span class="inline-block px-2 py-1 rounded bg-blue-900/50 text-blue-300 text-xs border border-blue-800 font-bold">{{ $q->category->name }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ Str::limit($q->question_text, 60) }}</td>
                        <td class="px-6 py-4 text-center text-xs text-gray-500 uppercase">{{ $q->type }}</td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('admin.edit', $q->id) }}" class="text-blue-400 hover:text-blue-300 mr-3 transition"><i class="fas fa-edit text-lg"></i></a>
                            <a href="{{ route('admin.delete', $q->id) }}" onclick="return confirm('Yakin hapus soal ini?')" class="text-red-400 hover:text-red-300 transition"><i class="fas fa-trash-alt text-lg"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 bg-gray-800 border-t border-gray-700">{{ $questions->links() }}</div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('categoryChart').getContext('2d');
        const labels = @json($labels);
        const data = @json($data);
        if (labels.length === 0) { labels.push('Belum Ada Data'); data.push(1); }
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Main',
                    data: data,
                    backgroundColor: ['rgba(59, 130, 246, 0.6)', 'rgba(16, 185, 129, 0.6)', 'rgba(236, 72, 153, 0.6)', 'rgba(245, 158, 11, 0.6)'],
                    borderColor: ['rgba(59, 130, 246, 1)', 'rgba(16, 185, 129, 1)', 'rgba(236, 72, 153, 1)', 'rgba(245, 158, 11, 1)'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.1)' }, ticks: { color: '#ccc' } }, x: { grid: { display: false }, ticks: { color: '#ccc' } } } }
        });
    </script>
</body>
</html>