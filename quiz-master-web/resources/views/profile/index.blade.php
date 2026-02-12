<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pusat Sosial - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { darkMode: 'class' };
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(0,0,0,0.1); }
        .dark .glass { background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255,255,255,0.1); }
        .tab-active { background: rgba(236, 72, 153, 0.2); border-color: #ec4899; color: #ec4899; }
        .tab-inactive { background: rgba(0, 0, 0, 0.05); border-color: rgba(0, 0, 0, 0.1); color: #64748b; }
        .dark .tab-inactive { background: rgba(255, 255, 255, 0.05); border-color: rgba(255, 255, 255, 0.1); color: #94a3b8; }
        .tab-inactive:hover { background: rgba(0, 0, 0, 0.1); color: #0f172a; }
        .dark .tab-inactive:hover { background: rgba(255, 255, 255, 0.1); color: white; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen font-sans transition-colors duration-300 flex flex-col">

    @include('partials.navbar')

    <div class="max-w-4xl mx-auto p-4 w-full flex-grow pt-20">
        
        @if(session('success'))
            <div class="bg-green-500/20 text-green-600 dark:text-green-300 p-3 rounded-lg mb-4 text-sm text-center border border-green-500/30">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex overflow-x-auto gap-3 mb-6 pb-2 no-scrollbar">
            <button onclick="switchTab('challenges')" id="tab-btn-challenges" class="tab-active px-5 py-2 rounded-full font-bold border transition whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-bolt"></i> Duel
                @if($challenges->count() > 0) <span class="bg-yellow-500 text-black text-xs px-2 py-0.5 rounded-full animate-pulse">{{ $challenges->count() }}</span> @endif
            </button>

            <button onclick="switchTab('history')" id="tab-btn-history" class="tab-inactive px-5 py-2 rounded-full font-bold border transition whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-history"></i> Riwayat
            </button>

            <button onclick="switchTab('following')" id="tab-btn-following" class="tab-inactive px-5 py-2 rounded-full font-bold border transition whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-user-check"></i> Teman
            </button>
            
            <button onclick="switchTab('search')" id="tab-btn-search" class="tab-inactive px-5 py-2 rounded-full font-bold border transition whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-search"></i> Cari
            </button>
        </div>

        <div id="content-challenges" class="tab-content">
            @if($challenges->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl text-yellow-500"><i class="fas fa-crown"></i></div>
                    <h3 class="text-lg font-bold mb-2">Tidak Ada Tantangan</h3>
                    <p class="text-slate-500">Belum ada yang berani menantangmu.</p>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($challenges as $ch)
                        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl shadow-lg border-2 border-yellow-500/30 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $ch->sender->avatar ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$ch->sender->name }}" class="w-12 h-12 rounded-full bg-slate-700">
                                <div>
                                    <div class="text-xs text-yellow-500 font-bold uppercase tracking-wider">PENANTANG</div>
                                    <div class="font-bold text-lg">{{ $ch->sender->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $ch->room->category->name ?? 'Kuis' }} • {{ $ch->room->total_questions }} Soal</div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('live.challenge.accept', $ch->id) }}" class="bg-green-600 hover:bg-green-500 text-white px-4 py-1 rounded-lg text-sm font-bold shadow text-center"><i class="fas fa-play mr-1"></i> TERIMA</a>
                                <a href="{{ route('live.challenge.reject', $ch->id) }}" class="bg-slate-200 dark:bg-slate-700 hover:bg-red-500 text-slate-500 hover:text-white px-4 py-1 rounded-lg text-sm font-bold transition text-center">TOLAK</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="content-history" class="tab-content hidden">
            @if($history->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700">
                    <p class="text-slate-500">Belum ada riwayat pertandingan.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($history as $h)
                        @php
                            $isSender = $h->sender_id == Auth::id();
                            $opponent = $isSender ? $h->target : $h->sender;
                            $isWin = $h->winner_id == Auth::id();
                            $isDraw = $h->winner_id == null;
                            
                            // Warna Status
                            $statusColor = $isWin ? 'text-green-500' : ($isDraw ? 'text-blue-500' : 'text-red-500');
                            $statusBg = $isWin ? 'bg-green-500/10 border-green-500' : ($isDraw ? 'bg-blue-500/10 border-blue-500' : 'bg-red-500/10 border-red-500');
                            $statusText = $isWin ? 'MENANG' : ($isDraw ? 'SERI' : 'KALAH');
                        @endphp

                        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border-l-4 {{ $statusBg }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 transition hover:shadow-md">
                            
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-700 flex-shrink-0 overflow-hidden">
                                     <img src="{{ $opponent->avatar ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$opponent->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="text-sm text-slate-500 dark:text-slate-400 font-semibold">VS <span class="text-slate-800 dark:text-white text-base">{{ $opponent->name }}</span></div>
                                    <div class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                        <span class="bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded">{{ $h->room->category->name }}</span>
                                        <span>• {{ $h->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 border-slate-100 dark:border-slate-700 pt-3 sm:pt-0 mt-2 sm:mt-0">
                                <div class="text-right">
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">SKOR</div>
                                    <div class="font-mono font-bold text-lg text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                        <span class="{{ $isSender && $h->room->host_score > $h->room->challenger_score ? 'text-green-500' : '' }}">
                                            {{ $isSender ? $h->room->host_score : $h->room->challenger_score }}
                                        </span>
                                        <span class="text-slate-400 mx-1">-</span>
                                        <span class="{{ !$isSender && $h->room->host_score < $h->room->challenger_score ? 'text-red-500' : '' }}">
                                            {{ $isSender ? $h->room->challenger_score : $h->room->host_score }}
                                        </span>
                                    </div>
                                </div>

                                <div class="font-black text-xl tracking-wider {{ $statusColor }} bg-white dark:bg-slate-900 px-3 py-1 rounded-lg shadow-sm border border-slate-100 dark:border-slate-700 min-w-[80px] text-center">
                                    {{ $statusText }}
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="content-following" class="tab-content hidden">
            @if($following->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700">
                    <p class="text-slate-500 mb-4">Kamu belum mengikuti siapapun.</p>
                    <button onclick="switchTab('search')" class="text-pink-500 hover:underline">Cari Teman</button>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($following as $f)
                        @include('profile.partials.user-card', ['user' => $f])
                    @endforeach
                </div>
            @endif
        </div>

        <div id="content-search" class="tab-content hidden">
            <form action="{{ route('social.index') }}" method="GET" class="relative mb-6">
                <input type="hidden" name="tab" value="search">
                <input type="text" name="search" value="{{ $query }}" placeholder="Ketik nama atau email..." 
                    class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-xl px-5 py-4 pl-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 text-lg transition text-slate-800 dark:text-white">
                <i class="fas fa-search absolute left-4 top-5 text-gray-400"></i>
                <button type="submit" class="absolute right-3 top-2.5 bg-pink-600 hover:bg-pink-500 text-white px-6 py-2 rounded-lg font-bold transition">Cari</button>
            </form>
            @if($query)
                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($searchResults as $res)
                        @include('profile.partials.user-card', ['user' => $res])
                    @empty
                        <div class="col-span-2 text-center text-slate-500 py-8">Tidak ditemukan.</div>
                    @endforelse
                </div>
            @endif
        </div>

    </div>

    <div id="duelModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 w-full max-w-sm rounded-2xl border border-gray-200 dark:border-slate-600 shadow-2xl p-6 relative animate-bounce-in">
            <button onclick="closeDuelModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Tantang <span id="duelTargetName" class="text-yellow-500">Teman</span></h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Pilih aturan main untuk duel ini.</p>
            <form action="{{ route('live.challenge.send') }}" method="POST">
                @csrf
                <input type="hidden" name="target_id" id="duelTargetId">
                <div class="mb-4">
                    <label class="block text-sm text-slate-500 dark:text-gray-400 mb-2 font-bold">Kategori</label>
                    <select name="category_id" class="w-full bg-gray-100 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-slate-800 dark:text-white">
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm text-slate-500 dark:text-gray-400 mb-2 font-bold">Soal</label>
                        <select name="total_questions" class="w-full bg-gray-100 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-slate-800 dark:text-white">
                            <option value="5">5 Soal</option>
                            <option value="10" selected>10 Soal</option>
                            <option value="15">15 Soal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-500 dark:text-gray-400 mb-2 font-bold">Waktu</label>
                        <select name="duration" class="w-full bg-gray-100 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg px-3 py-2 text-slate-800 dark:text-white">
                            <option value="10">10 Detik</option>
                            <option value="30" selected>30 Detik</option>
                            <option value="60">60 Detik</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 py-3 rounded-xl font-bold text-white shadow-lg transition">Kirim Tantangan</button>
            </form>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || "{{ $query ? 'search' : 'challenges' }}";
        switchTab(activeTab);

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('[id^="tab-btn-"]').forEach(btn => {
                btn.classList.remove('tab-active');
                btn.classList.add('tab-inactive');
            });
            const content = document.getElementById('content-' + tabName);
            const btn = document.getElementById('tab-btn-' + tabName);
            if (content && btn) {
                content.classList.remove('hidden');
                btn.classList.remove('tab-inactive');
                btn.classList.add('tab-active');
            }
        }

        function openDuelModal(id, name) {
            document.getElementById('duelTargetId').value = id;
            document.getElementById('duelTargetName').innerText = name;
            document.getElementById('duelModal').classList.remove('hidden');
        }
        function closeDuelModal() {
            document.getElementById('duelModal').classList.add('hidden');
        }
    </script>
</body>
</html>