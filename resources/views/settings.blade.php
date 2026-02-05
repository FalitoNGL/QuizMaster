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

            <!-- 1. AUDIO SETTINGS (Sliders) -->
            <div class="space-y-6 mb-8">
                <h3 class="font-bold text-lg mb-4 border-b border-gray-200 dark:border-slate-700 pb-2">Suara & Audio</h3>
                
                <!-- BGM Slider -->
                <div>
                    <div class="flex justify-between mb-2">
                        <label for="bgm-slider" class="flex items-center gap-2 font-bold text-sm text-slate-700 dark:text-slate-300">
                            <i class="fas fa-music text-blue-500"></i> Musik Latar
                        </label>
                        <span id="bgm-val" class="text-xs font-mono bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">50%</span>
                    </div>
                    <input type="range" id="bgm-slider" min="0" max="100" value="50" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700 accent-blue-500">
                </div>

                <!-- SFX Slider -->
                <div>
                    <div class="flex justify-between mb-2">
                        <label for="sfx-slider" class="flex items-center gap-2 font-bold text-sm text-slate-700 dark:text-slate-300">
                            <i class="fas fa-volume-up text-yellow-500"></i> Efek Suara
                        </label>
                        <span id="sfx-val" class="text-xs font-mono bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">80%</span>
                    </div>
                    <input type="range" id="sfx-slider" min="0" max="100" value="80" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700 accent-yellow-500">
                </div>
            </div>

            <!-- 2. AVATAR STUDIO -->
            <div class="mb-8">
                <h3 class="font-bold text-lg mb-4 border-b border-gray-200 dark:border-slate-700 pb-2 flex items-center gap-2">
                    <i class="fas fa-magic text-purple-500"></i> Avatar Studio
                </h3>
                
                <form action="{{ route('settings.update-profile') }}" method="POST">
                    @csrf
                    
                    <div class="flex flex-col items-center mb-6">
                        <!-- Large Preview -->
                        <div class="relative w-32 h-32 mb-4 group cursor-pointer" onclick="randomizeSeed()">
                            <div class="absolute inset-0 bg-gradient-to-tr from-blue-500 to-purple-500 rounded-full blur-lg opacity-50 group-hover:opacity-75 transition"></div>
                            <img id="avatar-preview-lg" src="https://api.dicebear.com/7.x/{{ session('avatar_style', 'avataaars') }}/svg?seed={{ session('current_player') }}" class="w-full h-full rounded-full bg-slate-100 dark:bg-slate-800 border-4 border-white dark:border-slate-700 shadow-xl relative z-10">
                            <div class="absolute bottom-0 right-0 z-20 bg-slate-800 text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-slate-700 shadow-lg" title="Acak Wajah">
                                <i class="fas fa-dice"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">Klik gambar untuk acak wajah!</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Style Picker -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Pilih Gaya</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach(['avataaars' => 'Kartun', 'bottts' => 'Robot', 'pixel-art' => 'Pixel', 'lorelei' => 'Artistik'] as $style => $label)
                                <label class="cursor-pointer text-center group">
                                    <input type="radio" name="avatar_style" value="{{ $style }}" class="peer sr-only" {{ session('avatar_style', 'avataaars') == $style ? 'checked' : '' }} onchange="updatePreview()">
                                    <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 border-2 border-transparent peer-checked:border-purple-500 peer-checked:bg-purple-500/10 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                        <img src="https://api.dicebear.com/7.x/{{ $style }}/svg?seed=Felix" class="w-8 h-8 mx-auto mb-1">
                                        <div class="text-[10px] font-bold text-slate-600 dark:text-slate-300">{{ $label }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Inputs -->
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Nama Tampilan</label>
                                <input type="text" id="player-name" name="player_name" value="{{ session('current_player') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2.5 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 outline-none transition font-bold" required oninput="updatePreview()">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Bio / Motto (Opsional)</label>
                                <input type="text" name="bio" value="{{ session('player_bio') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg px-4 py-2.5 focus:border-purple-500 outline-none transition text-sm" placeholder="Contoh: Raja Kuis Sejarah">
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-2 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white py-3 rounded-xl font-bold transition shadow-lg transform active:scale-95">
                            Simpan Profil Baru
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. DATA & CACHE -->
            <div>
                <h3 class="font-bold text-lg mb-4 border-b border-gray-200 dark:border-slate-700 pb-2 text-red-500">Zona Bahaya</h3>
                
                <div class="grid grid-cols-1 gap-3">
                    <button onclick="clearAppCache()" class="w-full text-left flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                <i class="fas fa-broom"></i>
                            </div>
                            <div>
                                <div class="font-bold text-sm text-slate-700 dark:text-slate-300">Bersihkan Cache</div>
                                <div class="text-[10px] text-slate-500">Hapus data lokal (pengaturan audio, tema)</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-400 group-hover:translate-x-1 transition"></i>
                    </button>

                    <form action="{{ route('settings.reset') }}" method="POST" onsubmit="return confirm('Yakin hapus semua riwayat main? Statistik kamu akan jadi 0 lagi!')">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center justify-between p-3 rounded-xl border border-red-200 dark:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/20 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                                    <i class="fas fa-trash-alt"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-red-600 dark:text-red-400">Reset Statistik</div>
                                    <div class="text-[10px] text-red-400/70">Kembalikan level & exp ke 0</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-red-400 group-hover:translate-x-1 transition"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- Avatar Studio Logic ---
        function updatePreview() {
            const nameCheck = document.getElementById('player-name').value || 'Felix';
            // Get selected radio
            const style = document.querySelector('input[name="avatar_style"]:checked').value;
            
            const url = `https://api.dicebear.com/7.x/${style}/svg?seed=${encodeURIComponent(nameCheck)}`;
            document.getElementById('avatar-preview-lg').src = url;
        }

        function randomizeSeed() {
            const randomName = 'Player' + Math.floor(Math.random() * 10000);
            document.getElementById('player-name').value = randomName;
            updatePreview();
        }

        // --- Audio Logic with Sliders ---
        const bgmSlider = document.getElementById('bgm-slider');
        const sfxSlider = document.getElementById('sfx-slider');
        const bgmVal = document.getElementById('bgm-val');
        const sfxVal = document.getElementById('sfx-val');

        // Init values from localStorage
        bgmSlider.value = localStorage.getItem('bgm_volume') || 50;
        sfxSlider.value = localStorage.getItem('sfx_volume') || 80;
        updateLabels();

        function updateLabels() {
            bgmVal.innerText = bgmSlider.value + '%';
            sfxVal.innerText = sfxSlider.value + '%';
        }

        bgmSlider.addEventListener('input', (e) => {
            const val = e.target.value;
            localStorage.setItem('bgm_volume', val);
            updateLabels();
            // Realtime preview (optional) if BGM is playing
        });

        sfxSlider.addEventListener('input', (e) => {
            const val = e.target.value;
            localStorage.setItem('sfx_volume', val);
            updateLabels();
            // Test SFX
            if(Math.random() > 0.8) { /* maybe play a sound occasionally while dragging? nah too noisy */ }
        });
        
        sfxSlider.addEventListener('change', () => {
             // Play a test 'pop' sound when releasing slider handle?
             // Not implemented globally yet, but logic is ready
        });

        // --- Clear Cache Logic ---
        function clearAppCache() {
            if(confirm('Aplikasi akan direfresh. Yakin?')) {
                localStorage.clear();
                window.location.reload();
            }
        }
    </script>
</body>
</html>
</body>
</html>