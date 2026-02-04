<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz Master - Main Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.3/echo.iife.js"></script>

    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .dark .glass { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); }
        .blob { position: absolute; filter: blur(80px); z-index: -1; opacity: 0.6; animation: move 10s infinite alternate; }
        @keyframes move { from { transform: translate(0, 0) scale(1); } to { transform: translate(20px, -20px) scale(1.1); } }
        
        /* Slider Custom */
        input[type=range] { -webkit-appearance: none; width: 100%; background: transparent; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; height: 20px; width: 20px; border-radius: 50%; background: #eab308; cursor: pointer; margin-top: -8px; box-shadow: 0 0 10px rgba(234,179,8,0.5); }
        input[type=range]::-webkit-slider-runnable-track { width: 100%; height: 4px; cursor: pointer; background: #4b5563; border-radius: 2px; }
        
        /* Animasi Toast */
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .toast-enter { animation: slideIn 0.5s forwards; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen relative overflow-x-hidden transition-colors duration-300 selection:bg-pink-500 selection:text-white">

    <div class="blob bg-purple-600 w-96 h-96 rounded-full top-0 left-0 mix-blend-multiply hidden dark:block"></div>
    <div class="blob bg-cyan-600 w-96 h-96 rounded-full bottom-0 right-0 mix-blend-multiply animation-delay-2000 hidden dark:block"></div>
    <div class="blob bg-pink-600 w-80 h-80 rounded-full top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 mix-blend-multiply animation-delay-4000 hidden dark:block"></div>

    @include('partials.navbar')

    <div class="container mx-auto px-4 py-12 pt-24 relative z-10">
        <div class="text-center mb-12">
            @php
                $displayName = Auth::check() ? Auth::user()->name : (session('current_player') ?? 'Tamu');
                $seed = Auth::check() ? Auth::user()->name : $displayName;
                $avatarUrl = Auth::check() && Auth::user()->avatar ? Auth::user()->avatar : "https://api.dicebear.com/7.x/avataaars/svg?seed=" . $seed;
            @endphp
            
            <div class="inline-block relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-pink-600 to-purple-600 rounded-full blur opacity-75 hidden dark:block group-hover:opacity-100 transition duration-1000"></div>
                <img src="{{ $avatarUrl }}" alt="Avatar" class="relative w-28 h-28 rounded-full border-4 border-white dark:border-slate-900 shadow-xl bg-slate-200 dark:bg-slate-800">
                @auth <div class="absolute bottom-1 right-1 bg-green-500 w-6 h-6 rounded-full border-4 border-white dark:border-slate-900" title="Online"></div> @endauth
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold mt-6 tracking-tight text-slate-800 dark:text-white">
                Halo, <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-blue-600 dark:from-cyan-400 dark:to-purple-500">{{ $displayName }}</span>!
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-lg">Siap untuk menguji pengetahuanmu hari ini?</p>
        </div>



        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            @foreach($categories as $category)
                @php
                    if (str_starts_with($category->icon_class, 'fa')) {
                        $faIcon = $category->icon_class;
                    } else {
                        // Mapping manual dari nama icon Seeder ke FontAwesome
                        $iconMap = [
                            'FiKey' => 'fas fa-key',
                            'GiDna1' => 'fas fa-dna',
                            'GiBrain' => 'fas fa-brain',
                            'FiCpu' => 'fas fa-microchip',
                            
                            // --- TAMBAHAN KATEGORI BARU ---
                            'FiGlobe'  => 'fas fa-globe',             // Pemrograman Jaringan
                            'FiShield' => 'fas fa-shield-alt',        // Etos Sandi III
                            'FiLock'   => 'fas fa-lock',              // Kriptografi Terapan
                            'FiRadio'  => 'fas fa-broadcast-tower',   // Sistem Telekomunikasi
                            'FiCode'   => 'fas fa-code',              // Pemrograman Lanjutan
                            'FiServer' => 'fas fa-server',            // Sistem Operasi & Virtualisasi
                        ];
                        
                        // Default ke 'fas fa-star' jika tidak ditemukan di map
                        $faIcon = $iconMap[$category->icon_class] ?? 'fas fa-star';
                    }

                    // Mapping Warna Gradient berdasarkan Slug Kategori
                    $colors = [
                        'fundamental-keamanan' => 'from-blue-500 to-cyan-500',
                        'biologi-dasar'        => 'from-green-500 to-emerald-500',
                        'intelijen-dasar'      => 'from-purple-500 to-pink-500',
                        'elektronika-dasar'    => 'from-orange-500 to-red-500',
                        
                        // --- WARNA UNTUK KATEGORI BARU ---
                        'pemrograman-jaringan' => 'from-cyan-500 to-blue-600',
                        'etos-sandi-iii'       => 'from-yellow-500 to-orange-500',
                        'kriptografi-terapan'  => 'from-slate-600 to-slate-800',
                        'sistem-telekomunikasi'=> 'from-indigo-500 to-purple-600',
                        'pemrograman-lanjutan' => 'from-rose-500 to-pink-600',
                        'sistem-operasi-virtualisasi' => 'from-emerald-600 to-teal-600',
                    ];
                    
                    $gradient = $colors[$category->slug] ?? 'from-indigo-500 to-blue-500';
                @endphp

                <div onclick="openSetupModal('{{ $category->slug }}', '{{ $category->name }}', {{ $category->questions_count ?? 10 }})" class="glass glass-card rounded-3xl p-6 h-full flex flex-col items-center text-center transition-all duration-300 relative overflow-hidden group bg-white/60 dark:bg-white/5 border border-gray-200 dark:border-white/10 cursor-pointer">
                    <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} opacity-0 group-hover:opacity-10 dark:group-hover:opacity-20 transition duration-500 blur-xl"></div>
                    <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-3xl shadow-lg mb-6 group-hover:scale-110 transition duration-300 ring-4 ring-white/50 dark:ring-white/5">
                        <i class="{{ $faIcon }} text-white drop-shadow-md"></i>
                    </div>
                    <div class="relative z-10 flex-grow flex flex-col w-full">
                        <h3 class="text-xl font-bold mb-2 text-slate-800 dark:text-white transition group-hover:text-blue-600 dark:group-hover:text-blue-300">{{ $category->name }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 flex-grow border-t border-gray-200 dark:border-white/5 pt-3 mt-1 leading-relaxed">
                            {{ Str::limit($category->description, 80) }}
                        </p>
                        <div class="mt-auto w-full py-3 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-sm font-bold text-slate-700 dark:text-white group-hover:bg-blue-600 group-hover:text-white transition flex items-center justify-center gap-2 uppercase tracking-wider shadow-sm group-hover:shadow-lg">
                            <span>Mulai</span> <i class="fas fa-play text-xs"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-20 pb-8 text-slate-500 dark:text-slate-500 text-sm border-t border-gray-300 dark:border-white/5 pt-8 max-w-2xl mx-auto flex flex-col items-center gap-2">
            <p>&copy; {{ date('Y') }} Quiz Master App.</p>
            <div id="ws-status" class="text-xs flex items-center gap-2 opacity-50"><span class="w-2 h-2 rounded-full bg-red-500"></span> WebSocket: Connecting...</div>
        </div>
    </div>

    <div id="setupModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 w-full max-w-sm rounded-2xl border border-gray-200 dark:border-slate-600 shadow-2xl p-6 animate-bounce-in">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Atur Kuis</h3>
                <button onclick="document.getElementById('setupModal').classList.add('hidden')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>

            <h4 id="modalCatName" class="text-lg text-blue-600 dark:text-blue-400 font-bold mb-4 text-center">Nama Kategori</h4>

            <form id="setupForm" method="GET">
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-slate-500 dark:text-gray-400 mb-2">
                        <span class="font-bold">Jumlah Soal</span>
                        <span id="qCountDisplay" class="text-yellow-600 dark:text-yellow-400 font-bold">10</span>
                    </div>
                    <input type="range" name="limit" id="qRange" min="1" max="50" value="10" oninput="document.getElementById('qCountDisplay').innerText = this.value">
                </div>

                <div class="mb-8">
                    <label class="block text-sm text-slate-500 dark:text-gray-400 mb-3 font-bold">Mode Waktu</label>
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="timer" value="30" checked class="peer sr-only">
                            <div class="text-center py-2 bg-gray-100 dark:bg-slate-700 rounded-lg peer-checked:bg-blue-600 peer-checked:text-white hover:bg-gray-200 dark:hover:bg-slate-600 transition text-sm border border-gray-200 dark:border-slate-600">
                                <i class="fas fa-clock mr-1"></i> 30 Detik
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="timer" value="0" class="peer sr-only">
                            <div class="text-center py-2 bg-gray-100 dark:bg-slate-700 rounded-lg peer-checked:bg-green-600 peer-checked:text-white hover:bg-gray-200 dark:hover:bg-slate-600 transition text-sm border border-gray-200 dark:border-slate-600">
                                <i class="fas fa-mug-hot mr-1"></i> Santai (∞)
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 py-3 rounded-xl font-bold text-white shadow-lg transition transform hover:scale-105">
                    MULAI BELAJAR
                </button>
            </form>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

    <script>
        // Modal Logic
        function openSetupModal(slug, name, maxQuestions) {
            document.getElementById('modalCatName').innerText = name;
            document.getElementById('setupForm').action = "/quiz/" + slug;
            const range = document.getElementById('qRange');
            range.max = maxQuestions > 0 ? maxQuestions : 10;
            range.value = Math.min(10, range.max);
            document.getElementById('qCountDisplay').innerText = range.value;
            document.getElementById('setupModal').classList.remove('hidden');
        }


        // WebSocket
        const userId = "{{ Auth::id() }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const statusEl = document.getElementById('ws-status');

        if (userId) {
            try {
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '{{ env("REVERB_APP_KEY", "app-key") }}',
                    wsHost: '{{ env("REVERB_HOST", "localhost") }}',
                    wsPort: {{ env("REVERB_PORT", 8080) }},
                    wssPort: {{ env("REVERB_PORT", 8080) }},
                    forceTLS: {{ env("REVERB_SCHEME", "http") === "https" ? "true" : "false" }},
                    enabledTransports: ['ws', 'wss'],
                    cluster: 'mt1',
                    auth: { headers: { 'X-CSRF-TOKEN': csrfToken } }
                });

                window.Echo.connector.pusher.connection.bind('connected', () => {
                    statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-green-500"></span> WebSocket: Connected';
                    statusEl.classList.remove('opacity-50');
                });

                window.Echo.private('user.' + userId).listen('NewChallengeReceived', (e) => {
                    showToast(e);
                    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                    audio.play().catch(()=>{});
                });

            } catch (err) { console.log("WebSocket Error:", err); }
        }

        function showToast(data) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'bg-slate-800 border-l-4 border-yellow-500 text-white p-4 rounded shadow-2xl flex flex-col gap-2 min-w-[300px] toast-enter pointer-events-auto';
            toast.innerHTML = `
                <div class="font-bold text-yellow-400 flex justify-between items-center">
                    <span class="flex items-center gap-2"><i class="fas fa-bolt"></i> TANTANGAN BARU!</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-slate-400 hover:text-white">&times;</button>
                </div>
                <div class="text-sm"><b class="text-white">${data.sender_name}</b> menantangmu di kuis <b>${data.category}</b>!</div>
                <a href="/social?tab=challenges" class="bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-500 hover:to-orange-500 text-center py-2 rounded text-sm font-bold mt-2 transition shadow-lg">LIHAT TANTANGAN</a>
            `;
            container.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 500); }, 10000);
        }
    </script>
</body>
</html>