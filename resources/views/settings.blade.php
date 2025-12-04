<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pengaturan - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass { background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        /* Toggle Switch Custom */
        .toggle-checkbox:checked { right: 0; border-color: #22c55e; }
        .toggle-checkbox:checked + .toggle-label { background-color: #22c55e; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen p-4 font-sans flex items-center justify-center">

    <div class="max-w-md w-full glass rounded-3xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold mb-2 text-slate-200">
                <i class="fas fa-cog text-slate-400"></i> Pengaturan
            </h1>
            <p class="text-slate-400">Sesuaikan pengalaman bermainmu</p>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 text-green-300 p-3 rounded-lg mb-6 text-sm text-center border border-green-500/30">
                <i class="fas fa-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-6 text-sm text-center border border-red-500/30">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6 mb-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center">
                        <i class="fas fa-music text-blue-400"></i>
                    </div>
                    <div>
                        <div class="font-bold">Musik Latar</div>
                        <div class="text-xs text-slate-400">Suara background saat main</div>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="toggle-bgm" class="sr-only peer" onchange="toggleAudio('bgm')">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center">
                        <i class="fas fa-volume-up text-yellow-400"></i>
                    </div>
                    <div>
                        <div class="font-bold">Efek Suara</div>
                        <div class="text-xs text-slate-400">Bunyi Benar/Salah/Klik</div>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="toggle-sfx" class="sr-only peer" onchange="toggleAudio('sfx')">
                    <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                </label>
            </div>
        </div>

        <hr class="border-slate-700 my-6">

        <div class="mb-8">
            <h3 class="text-red-400 font-bold mb-4 text-sm uppercase tracking-wider">Zona Data</h3>
            
            <form action="{{ route('settings.reset') }}" method="POST" onsubmit="return confirm('Yakin hapus semua riwayat main? Statistik kamu akan jadi 0 lagi!')">
                @csrf
                <button type="submit" class="w-full bg-red-900/30 hover:bg-red-900/50 text-red-300 border border-red-800 py-3 rounded-xl font-bold transition flex items-center justify-center gap-2 group">
                    <i class="fas fa-trash-alt group-hover:animate-bounce"></i> Reset Statistik Saya
                </button>
            </form>
            <p class="text-xs text-slate-500 mt-2 text-center">Menghapus skor & pencapaian milik: <strong>{{ session('current_player') ?? 'Belum ada sesi' }}</strong></p>
        </div>

        <div class="text-center">
            <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 px-8 py-3 rounded-full font-bold transition text-slate-200">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu
            </a>
        </div>
    </div>

    <script>
        // LOGIKA SIMPAN PENGATURAN DI BROWSER
        document.addEventListener('DOMContentLoaded', () => {
            // Cek settingan lama
            const bgmOn = localStorage.getItem('qm_bgm') !== 'false'; // Default True
            const sfxOn = localStorage.getItem('qm_sfx') !== 'false'; // Default True

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