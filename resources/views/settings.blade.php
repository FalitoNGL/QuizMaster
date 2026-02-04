<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan - Quiz Master</title>
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
        .dark .glass { background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen transition-colors duration-300">

    @include('partials.navbar')

    <div class="min-h-screen flex items-center justify-center px-4 pt-20 pb-8">
        <div class="max-w-md w-full glass rounded-3xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-cog text-slate-400"></i> Pengaturan
                </h1>
                <p class="text-slate-500 dark:text-slate-400">Sesuaikan pengalaman bermainmu</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 p-3 rounded-lg mb-6 text-sm text-center border border-green-300 dark:border-green-500/30">
                    <i class="fas fa-check"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 p-3 rounded-lg mb-6 text-sm text-center border border-red-300 dark:border-red-500/30">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="space-y-6 mb-8">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-slate-800 flex items-center justify-center">
                            <i class="fas fa-music text-blue-500 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <div class="font-bold">Musik Latar</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Suara background saat main</div>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="toggle-bgm" class="sr-only peer" onchange="toggleAudio('bgm')">
                        <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-slate-800 flex items-center justify-center">
                            <i class="fas fa-volume-up text-yellow-500 dark:text-yellow-400"></i>
                        </div>
                        <div>
                            <div class="font-bold">Efek Suara</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Bunyi Benar/Salah/Klik</div>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="toggle-sfx" class="sr-only peer" onchange="toggleAudio('sfx')">
                        <div class="w-11 h-6 bg-gray-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                    </label>
                </div>
            </div>

            <hr class="border-gray-300 dark:border-slate-700 my-6">

            <div class="mb-8">
                <h3 class="text-red-500 dark:text-red-400 font-bold mb-4 text-sm uppercase tracking-wider">Zona Data</h3>
                
                <form action="{{ route('settings.reset') }}" method="POST" onsubmit="return confirm('Yakin hapus semua riwayat main? Statistik kamu akan jadi 0 lagi!')">
                    @csrf
                    <button type="submit" class="w-full bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-600 dark:text-red-300 border border-red-300 dark:border-red-800 py-3 rounded-xl font-bold transition flex items-center justify-center gap-2 group">
                        <i class="fas fa-trash-alt group-hover:animate-bounce"></i> Reset Statistik Saya
                    </button>
                </form>
                <p class="text-xs text-slate-500 mt-2 text-center">Menghapus skor & pencapaian milik: <strong>{{ session('current_player') ?? 'Belum ada sesi' }}</strong></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bgmOn = localStorage.getItem('qm_bgm') !== 'false';
            const sfxOn = localStorage.getItem('qm_sfx') !== 'false';

            document.getElementById('toggle-bgm').checked = bgmOn;
            document.getElementById('toggle-sfx').checked = sfxOn;
        });

        function toggleAudio(type) {
            if (type === 'bgm') {
                const isOn = document.getElementById('toggle-bgm').checked;
                localStorage.setItem('qm_bgm', isOn);
            } else if (type === 'sfx') {
                const isOn = document.getElementById('toggle-sfx').checked;
                localStorage.setItem('qm_sfx', isOn);
            }
        }
    </script>
</body>
</html>