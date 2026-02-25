@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header_title', 'Command Center')
@section('header_subtitle', 'Monitoring performa ekosistem QuizMaster secara real-time.')

@section('styles')
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.05);
    }
    .status-badge {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 99px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endsection

@section('content')
<div class="space-y-10">
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card rounded-[2rem] p-6 stat-card relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition"></div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-2xl">
                    <i class="fas fa-gamepad"></i>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">{{ number_format($totalGames) }}</div>
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Total Play</div>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-[2rem] p-6 stat-card relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition"></div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-2xl">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">{{ number_format($totalPlayers) }}</div>
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Pemain Unik</div>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-[2rem] p-6 stat-card relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition"></div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-2xl">
                    <i class="fas fa-brain"></i>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">{{ number_format($totalQuestions) }}</div>
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Bank Soal</div>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-[2rem] p-6 stat-card relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-vibrant/10 rounded-full blur-2xl group-hover:bg-vibrant/20 transition"></div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-vibrant/10 text-vibrant flex items-center justify-center text-2xl">
                    <i class="fas fa-star text-vibrant"></i>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">{{ number_format($avgScore) }}</div>
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Rata-rata Skor</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Chart -->
        <div class="lg:col-span-8 glass-card rounded-[2.5rem] p-8 border border-white/5 relative overflow-hidden">
            <div class="flex justify-between items-center mb-8">
                <h3 class="font-bold text-lg text-white flex items-center gap-3">
                    <span class="w-2 h-8 bg-pink-500 rounded-full"></span>
                    Statistik Kategori
                </h3>
                <div class="px-3 py-1 bg-white/5 rounded-full text-[10px] text-slate-500 font-bold uppercase tracking-tight">Bar Chart</div>
            </div>
            <div class="h-80 w-full relative">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="lg:col-span-4 glass-card rounded-[2.5rem] p-8 border border-white/5">
            <h3 class="font-bold text-lg text-white mb-8 flex items-center gap-3">
                <span class="w-2 h-8 bg-cyan-500 rounded-full"></span>
                Aktivitas Terbaru
            </h3>
            <div class="space-y-6">
                @forelse($recentActivities as $act)
                <div class="flex items-center justify-between group transition">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 border border-white/5 flex items-center justify-center text-sm font-bold text-slate-300">
                            {{ substr($act->player_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-sm text-white group-hover:text-vibrant transition leading-tight">{{ $act->player_name }}</div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold tracking-tight">{{ $act->category->name }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-black text-vibrant text-sm">+{{ $act->score }}</div>
                        <div class="text-[9px] text-slate-600 font-bold uppercase">{{ $act->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 opacity-30">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-xs font-bold uppercase tracking-widest">Belum ada aktivitas</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Management Section -->
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-2xl font-black text-white tracking-tight border-l-4 border-vibrant pl-4">Manajemen Bank Soal</h2>
            
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.create') }}" class="px-6 py-3 bg-vibrant hover:bg-vibrant/90 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-vibrant/20 transition transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Soal
                </a>
                <a href="{{ route('admin.categories') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-600/20 transition transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-tags"></i> Kategori
                </a>
                <a href="{{ route('admin.import') }}" class="px-6 py-3 bg-purple-600 hover:bg-purple-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-purple-600/20 transition transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-file-export"></i> Import JSON
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden border border-white/5 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/5">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Kategori</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Pertanyaan</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-center">Tipe</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($questions as $q)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-vibrant/10 text-vibrant text-[10px] font-black rounded-lg border border-vibrant/20 lowercase tracking-tight">
                                    {{ $q->category->name }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-slate-200 line-clamp-1 max-w-md">{{ $q->question_text }}</p>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="status-badge bg-slate-700/50 text-slate-400 border border-white/5">{{ $q->type }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex justify-center items-center gap-4">
                                    <a href="{{ route('admin.edit', $q->id) }}" class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center hover:bg-blue-500 hover:text-white transition shadow-lg shadow-blue-500/10">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.delete', $q->id) }}" onclick="return confirm('Hapus soal ini selamanya?')" class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition shadow-lg shadow-rose-500/10">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-8 bg-white/5 border-t border-white/5">
                <div class="pagination-container">
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('categoryChart').getContext('2d');
        const labels = @json($labels);
        const data = @json($data);
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
        gradient.addColorStop(1, 'rgba(245, 158, 11, 0.05)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Partisipasi',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: '#F59E0B',
                    borderWidth: 3,
                    borderRadius: 12,
                    borderSkipped: false,
                    barThickness: 30,
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        titleFont: { weight: 'bold', family: 'Plus Jakarta Sans' },
                        bodyFont: { family: 'Plus Jakarta Sans' },
                        padding: 12,
                        borderRadius: 12,
                    }
                }, 
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false }, 
                        ticks: { color: '#64748b', font: { weight: 'bold', size: 10 } } 
                    }, 
                    x: { 
                        grid: { display: false }, 
                        ticks: { color: '#64748b', font: { weight: 'bold', size: 10 } } 
                    } 
                } 
            }
        });
    });
</script>
@endsection