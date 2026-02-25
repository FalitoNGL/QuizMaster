<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pencapaian - Quiz Master</title>
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
        .dark .glass { background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); }
        .locked { filter: grayscale(100%) opacity(50%); }
    </style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen transition-colors duration-300">
    
    @include('partials.navbar')

    <div class="max-w-5xl mx-auto px-4 pt-24 pb-8">

        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold mb-2 text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                <i class="fas fa-medal"></i> Ruang Piala
            </h1>
            @if($playerName)
                <p class="text-slate-500 dark:text-slate-400">Koleksi milik <strong>{{ $playerName }}</strong></p>
            @else
                <p class="text-slate-500 dark:text-slate-400 text-sm bg-red-100 dark:bg-red-900/50 inline-block px-3 py-1 rounded">
                    Mainkan kuis dulu untuk membuka koleksi ini!
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($allBadges as $badge)
                @php
                    $isUnlocked = in_array($badge->id, $myBadgeIds);
                @endphp

                <div class="glass rounded-2xl p-6 text-center relative overflow-hidden group hover:bg-white/80 dark:hover:bg-slate-800/80 transition {{ $isUnlocked ? '' : 'locked' }}">

                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center mb-4 text-3xl shadow-lg border-2 {{ $isUnlocked ? 'border-yellow-500' : 'border-gray-300 dark:border-slate-600' }}">
                        <i class="{{ $badge->icon_class }} {{ $badge->color_class }}"></i>
                    </div>

                    <h3 class="text-xl font-bold mb-2">{{ $badge->name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 h-10">{{ $badge->description }}</p>

                    <div class="mt-4">
                        @if($isUnlocked)
                            <span class="bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 text-xs font-bold px-3 py-1 rounded-full border border-green-300 dark:border-green-500/50">
                                TERBUKA
                            </span>
                        @else
                            <span class="bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-slate-500 text-xs font-bold px-3 py-1 rounded-full">
                                TERKUNCI
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>