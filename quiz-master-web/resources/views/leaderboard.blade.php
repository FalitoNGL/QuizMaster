<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Papan Peringkat - Quiz Master</title>
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
    </style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen transition-colors duration-300">

    @include('partials.navbar')

    <div class="container mx-auto px-4 py-8 pt-24 max-w-4xl">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold mb-2 text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-orange-500">
                <i class="fas fa-trophy mr-2"></i> Papan Peringkat
            </h1>
            <p class="text-slate-500 dark:text-slate-400">Para master kuis terbaik sepanjang masa</p>
        </div>

        <div class="glass rounded-2xl overflow-hidden shadow-2xl bg-white/50 dark:bg-slate-800/50">
            <table class="w-full text-left">
                <thead class="bg-gray-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase text-sm border-b border-gray-300 dark:border-slate-700">
                    <tr>
                        <th class="py-4 px-6 text-center w-16">#</th>
                        <th class="py-4 px-6">Pemain</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6 text-right">Skor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 dark:divide-slate-700">
                    @forelse($topScores as $index => $score)
                    <tr class="hover:bg-gray-100 dark:hover:bg-slate-700/50 transition duration-150">
                        <td class="py-4 px-6 text-center font-bold text-lg 
                            {{ $index == 0 ? 'text-yellow-500' : ($index == 1 ? 'text-gray-500 dark:text-slate-300' : ($index == 2 ? 'text-orange-500' : 'text-slate-600 dark:text-slate-500')) }}">
                            @if($index < 3) <i class="fas fa-crown"></i> @endif
                            {{ $index + 1 }}
                        </td>
                        
                        <td class="py-4 px-6">
                            @php
                                $isMe = session('current_player') === $score->player_name;
                                $style = $isMe ? session('avatar_style', 'avataaars') : 'avataaars';
                            @endphp
                            
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <img src="https://api.dicebear.com/7.x/{{ $style }}/svg?seed={{ $score->player_name }}" class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 border {{ $isMe ? 'border-purple-500' : 'border-transparent' }}">
                                    @if($isMe) <div class="absolute -bottom-1 -right-1 bg-purple-500 rounded-full w-3 h-3 border-2 border-white dark:border-slate-800"></div> @endif
                                </div>

                                <div>
                                    @if($score->user_id)
                                        <a href="{{ route('profile.show', $score->user_id) }}" class="font-bold text-lg text-blue-600 dark:text-blue-400 hover:underline decoration-dotted flex items-center gap-2 group" title="Lihat Profil">
                                            {{ $score->player_name }}
                                            <i class="fas fa-external-link-alt text-xs opacity-0 group-hover:opacity-100 transition"></i>
                                        </a>
                                    @else
                                        <div class="font-bold text-lg text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                            {{ $score->player_name }}
                                            @if($isMe) 
                                                <span class="text-[10px] bg-purple-500 text-white px-2 py-0.5 rounded-full uppercase tracking-wider">You</span>
                                            @else
                                                <span class="text-xs font-normal text-slate-400 border border-slate-400 rounded px-1">Tamu</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="text-xs text-slate-500 mt-1">{{ $score->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-6">
                            <span class="inline-block px-2 py-1 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 text-xs font-semibold border border-blue-200 dark:border-blue-800">
                                {{ $score->category->name }}
                            </span>
                        </td>

                        <td class="py-4 px-6 text-right font-mono text-xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ number_format($score->score) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-500 italic">
                            Belum ada data permainan. Jadilah yang pertama!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>