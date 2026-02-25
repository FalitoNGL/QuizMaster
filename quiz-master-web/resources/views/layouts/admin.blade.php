<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - QuizMaster</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        vibrant: '#F59E0B', // Promoted to top-level
                        premium: {
                            gold: '#FFD700',
                            vibrant: '#F59E0B',
                            dark: '#0F172A',
                            surface: '#1E293B',
                        }
                    },
                    backdropBlur: {
                        xs: '2px',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            min-height: 100-screen;
        }

        /* Mesh Gradient Background */
        .mesh-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-glass {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .nav-link-active {
            background: linear-gradient(to right, rgba(245, 158, 11, 0.2), transparent);
            border-left: 4px solid #F59E0B;
            color: #F59E0B !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>
    @yield('styles')
</head>
<body class="text-slate-200">
    <div class="mesh-bg"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-72 sidebar-glass flex flex-col fixed h-full z-50">
            <!-- Brand -->
            <div class="p-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.svg') }}" class="w-10 h-10 shadow-lg shadow-vibrant/20" alt="Logo">
                    <div>
                        <h1 class="font-extrabold text-xl tracking-tighter text-white">QuizMaster</h1>
                        <p class="text-[10px] text-vibrant font-bold uppercase tracking-widest opacity-80">Admin Command</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-grow px-4 space-y-2 pt-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition group {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                    <i class="fas fa-th-large text-lg group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.categories') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition group {{ request()->routeIs('admin.categories*') ? 'nav-link-active' : '' }}">
                    <i class="fas fa-tags text-lg group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">Kelola Kategori</span>
                </a>

                <a href="{{ route('admin.import') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition group {{ request()->routeIs('admin.import*') ? 'nav-link-active' : '' }}">
                    <i class="fas fa-file-import text-lg group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">Import Soal</span>
                </a>

                <a href="{{ route('admin.create') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition group {{ request()->routeIs('admin.create') ? 'nav-link-active' : '' }}">
                    <i class="fas fa-plus-circle text-lg group-hover:scale-110 transition"></i>
                    <span class="font-semibold text-sm">Tambah Soal</span>
                </a>

                <div class="pt-6 pb-2 px-4 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Sistem</div>
                
                <a href="{{ route('admin.cleanup') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg text-slate-400 hover:text-orange-400 hover:bg-orange-500/5 transition group">
                    <i class="fas fa-broom text-lg group-hover:rotate-12 transition"></i>
                    <span class="font-semibold text-sm">Bersihkan Duplikat</span>
                </a>
            </nav>

            <!-- Bottom Action -->
            <div class="p-6 border-t border-white/5">
                <a href="{{ route('menu') }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition mb-3">
                    <i class="fas fa-external-link-alt"></i> Lihat Aplikasi
                </a>
                <a href="{{ route('admin.logout') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-xl text-xs font-bold transition">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow ml-72 p-10">
            <!-- Header Toolbar -->
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">@yield('header_title', 'Dashboard')</h2>
                    <p class="text-slate-400 text-sm mt-1">@yield('header_subtitle', 'Selamat datang kembali, Admin!')</p>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="glass-card flex items-center gap-3 px-4 py-2 rounded-2xl">
                        <div class="w-8 h-8 rounded-full bg-vibrant/20 text-vibrant flex items-center justify-center font-bold">A</div>
                        <span class="text-xs font-bold text-slate-300">Administrator</span>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="mb-6 animate-bounce">
                    <div class="bg-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl border border-emerald-500/30 flex items-center gap-3 shadow-lg shadow-emerald-500/10">
                        <i class="fas fa-check-circle text-xl"></i>
                        <span class="font-bold text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6">
                    <div class="bg-rose-500/20 text-rose-400 px-6 py-4 rounded-2xl border border-rose-500/30 flex items-center gap-3 shadow-lg shadow-rose-500/10">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                        <span class="font-bold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
