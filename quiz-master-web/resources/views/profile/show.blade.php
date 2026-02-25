<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Profil Pemain</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen font-sans transition-colors duration-300">

    <nav class="p-4 flex justify-between items-center max-w-4xl mx-auto">
        <a href="{{ route('menu') }}" class="flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-blue-500 dark:hover:text-white transition font-bold">
            <i class="fas fa-arrow-left"></i> Menu Utama
        </a>
        
        @if(Auth::check() && Auth::id() == $user->id)
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition font-bold">
                <i class="fas fa-edit"></i> Edit Profil
            </a>
        @endif
    </nav>

    <div class="max-w-4xl mx-auto p-4">
        
        @if(session('success'))
            <div class="bg-green-500/20 text-green-600 dark:text-green-300 p-3 rounded-lg mb-4 text-center font-bold border border-green-500/30">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 mb-6 border border-gray-200 dark:border-slate-700 text-center relative overflow-hidden shadow-xl">
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-blue-500 to-purple-600 opacity-20"></div>
            
            <div class="relative z-10 mt-10">
                <img src="{{ $user->avatar ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$user->name }}" class="w-32 h-32 rounded-full border-4 border-white dark:border-slate-900 bg-slate-200 dark:bg-slate-700 mx-auto mb-4 object-cover shadow-2xl">
                
                <h1 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $user->name }}</h1>
                <p class="text-yellow-600 dark:text-yellow-400 font-bold text-sm tracking-widest uppercase mt-1">{{ $user->title ?? 'Pemain Kuis' }}</p>
                <p class="text-slate-600 dark:text-slate-400 mt-4 max-w-lg mx-auto italic">"{{ $user->bio ?? 'Pengguna ini belum menulis bio.' }}"</p>

                <div class="flex justify-center gap-8 mt-6 text-sm">
                    <div class="text-center">
                        <div class="font-bold text-xl text-slate-800 dark:text-white">{{ $user->followers_count }}</div>
                        <div class="text-slate-500">Pengikut</div>
                    </div>
                    <div class="text-center">
                        <div class="font-bold text-xl text-slate-800 dark:text-white">{{ $user->following_count }}</div>
                        <div class="text-slate-500">Mengikuti</div>
                    </div>
                    <div class="text-center">
                        <div class="font-bold text-xl text-slate-800 dark:text-white">{{ $totalGames }}</div>
                        <div class="text-slate-500">Game</div>
                    </div>
                </div>

                <div class="mt-8">
                    @if(Auth::check() && Auth::id() != $user->id)
                        <form action="{{ route('profile.follow', $user->id) }}" method="POST">
                            @csrf
                            @if($isFollowing)
                                <button class="bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-slate-800 dark:text-white px-8 py-2 rounded-full font-bold transition border border-gray-300 dark:border-slate-600 shadow-sm">
                                    <i class="fas fa-user-check mr-2"></i> Mengikuti
                                </button>
                            @else
                                <button class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-2 rounded-full font-bold transition shadow-lg shadow-blue-500/30">
                                    <i class="fas fa-user-plus mr-2"></i> Ikuti
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 h-fit shadow-lg">
                <h3 class="font-bold text-lg mb-4 text-slate-700 dark:text-slate-300"><i class="fas fa-chart-bar mr-2"></i> Statistik</h3>
                <div class="space-y-4">
                    <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                        <span class="text-slate-500 dark:text-slate-400">Total Skor</span>
                        <span class="font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($totalScore) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-200 dark:border-slate-700 pb-2">
                        <span class="text-slate-500 dark:text-slate-400">Level</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ floor($totalScore/1000) + 1 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Bergabung</span>
                        <span class="font-bold text-slate-700 dark:text-white">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700 shadow-lg">
                <h3 class="font-bold text-lg mb-4 text-slate-700 dark:text-slate-300"><i class="fas fa-medal mr-2"></i> Koleksi Piala</h3>
                
                @if($achievements->isEmpty())
                    <div class="text-center py-10 text-slate-400 dark:text-slate-500">
                        <i class="fas fa-ghost text-4xl mb-2 opacity-50"></i>
                        <p>Belum ada piala yang diraih.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($achievements as $ach)
                        <div class="bg-gray-100 dark:bg-slate-700/50 p-4 rounded-xl text-center border border-gray-200 dark:border-slate-600 hover:bg-gray-200 dark:hover:bg-slate-700 transition group">
                            <div class="text-3xl mb-2 {{ $ach->color_class }} transform group-hover:scale-110 transition">
                                <i class="{{ $ach->icon_class }}"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-800 dark:text-white">{{ $ach->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $ach->description }}</div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
    
    <script>
        // Cek Dark Mode preference
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>