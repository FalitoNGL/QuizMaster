{{-- Global Navbar Component --}}
{{-- Pass $navbarHidden = true to hide navbar initially (for menu page) --}}
@php $isHiddenByDefault = isset($navbarHidden) && $navbarHidden; @endphp
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 {{ $isHiddenByDefault ? '-translate-y-full opacity-0' : '' }}" data-hidden-default="{{ $isHiddenByDefault ? 'true' : 'false' }}">
    <div class="glass border-b border-gray-200 dark:border-white/10 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                
                {{-- Logo & Brand --}}
                <a href="{{ route('menu') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition">
                        <i class="fas fa-brain text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-xl text-slate-800 dark:text-white hidden sm:block">
                        Quiz<span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-blue-600">Master</span>
                    </span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('menu') }}" class="nav-link {{ request()->routeIs('menu') ? 'nav-active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Beranda</span>
                    </a>
                    <a href="{{ route('live.lobby') }}" class="nav-link {{ request()->routeIs('live.*') ? 'nav-active' : '' }}">
                        <i class="fas fa-gamepad"></i>
                        <span>Live Duel</span>
                        <span class="nav-badge bg-red-500 animate-pulse">LIVE</span>
                    </a>
                    <a href="{{ route('quiz.leaderboard') }}" class="nav-link {{ request()->routeIs('quiz.leaderboard') ? 'nav-active' : '' }}">
                        <i class="fas fa-trophy"></i>
                        <span>Peringkat</span>
                    </a>
                    <a href="{{ route('achievements') }}" class="nav-link {{ request()->routeIs('achievements') ? 'nav-active' : '' }}">
                        <i class="fas fa-medal"></i>
                        <span>Pencapaian</span>
                    </a>
                    @auth
                    <a href="{{ route('social.index') }}" class="nav-link {{ request()->routeIs('social.*') ? 'nav-active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Sosial</span>
                    </a>
                    @endauth
                </div>

                {{-- Right Side: Theme, Profile, etc --}}
                <div class="flex items-center gap-2">
                    {{-- Theme Toggle --}}
                    <button onclick="toggleTheme()" class="w-10 h-10 rounded-full flex items-center justify-center text-slate-600 dark:text-yellow-300 hover:bg-gray-100 dark:hover:bg-white/10 transition" title="Ganti Tema">
                        <i id="theme-icon" class="fas fa-moon"></i>
                    </button>

                    {{-- Admin Button --}}
                    @if(session('is_admin'))
                    <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-yellow-500 hover:bg-yellow-100 dark:hover:bg-yellow-500/20 transition" title="Dashboard Admin">
                        <i class="fas fa-database"></i>
                    </a>
                    @endif

                    {{-- Settings --}}
                    <a href="{{ route('settings') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-cyan-600 dark:text-cyan-400 hover:bg-cyan-100 dark:hover:bg-cyan-500/20 transition hover:rotate-90 duration-300" title="Pengaturan">
                        <i class="fas fa-cog"></i>
                    </a>

                    {{-- User Profile / Login --}}
                    @auth
                    <div class="flex items-center gap-2 ml-2 pl-2 border-l border-gray-200 dark:border-white/20">
                        <a href="{{ route('profile.show', Auth::id()) }}" class="flex items-center gap-2 hover:opacity-80 transition group" title="Lihat Profil">
                            <img src="{{ Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.Auth::user()->name }}" class="w-8 h-8 rounded-full border-2 border-gray-200 dark:border-white/20 bg-slate-200">
                            <span class="font-semibold text-sm hidden lg:block text-slate-700 dark:text-white group-hover:text-blue-500 transition">{{ Str::limit(Auth::user()->name, 10) }}</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center text-red-500 hover:bg-red-100 dark:hover:bg-red-500/20 transition" title="Keluar">
                                <i class="fas fa-power-off text-sm"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="ml-2 px-4 py-2 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold text-sm hover:shadow-lg hover:scale-105 transition flex items-center gap-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="hidden sm:inline">Masuk</span>
                    </a>
                    @endauth

                    {{-- Mobile Menu Button --}}
                    <button onclick="toggleMobileMenu()" class="md:hidden w-10 h-10 rounded-full flex items-center justify-center text-slate-600 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition ml-1">
                        <i id="mobile-menu-icon" class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="md:hidden hidden bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-b border-gray-200 dark:border-white/10">
        <div class="container mx-auto px-4 py-4 flex flex-col gap-2">
            <a href="{{ route('menu') }}" class="mobile-nav-link {{ request()->routeIs('menu') ? 'mobile-nav-active' : '' }}">
                <i class="fas fa-home w-6"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('live.lobby') }}" class="mobile-nav-link {{ request()->routeIs('live.*') ? 'mobile-nav-active' : '' }}">
                <i class="fas fa-gamepad w-6"></i>
                <span>Live Duel</span>
                <span class="ml-auto text-xs bg-red-500 text-white px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
            </a>
            <a href="{{ route('quiz.leaderboard') }}" class="mobile-nav-link {{ request()->routeIs('quiz.leaderboard') ? 'mobile-nav-active' : '' }}">
                <i class="fas fa-trophy w-6"></i>
                <span>Peringkat</span>
            </a>
            <a href="{{ route('achievements') }}" class="mobile-nav-link {{ request()->routeIs('achievements') ? 'mobile-nav-active' : '' }}">
                <i class="fas fa-medal w-6"></i>
                <span>Pencapaian</span>
            </a>
            <a href="{{ route('stats') }}" class="mobile-nav-link {{ request()->routeIs('stats') ? 'mobile-nav-active' : '' }}">
                <i class="fas fa-chart-pie w-6"></i>
                <span>Statistik</span>
            </a>
            @auth
            <a href="{{ route('social.index') }}" class="mobile-nav-link {{ request()->routeIs('social.*') ? 'mobile-nav-active' : '' }}">
                <i class="fas fa-users w-6"></i>
                <span>Sosial</span>
            </a>
            @endauth
        </div>
    </div>
</nav>

{{-- Navbar Styles --}}
<style>
    .nav-link {
        @apply px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-white/10 transition relative;
    }
    .nav-active {
        @apply bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400;
    }
    .nav-badge {
        @apply absolute -top-1 -right-1 text-[10px] text-white px-1.5 py-0.5 rounded-full font-bold;
    }
    .mobile-nav-link {
        @apply flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-white/10 transition font-medium;
    }
    .mobile-nav-active {
        @apply bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400;
    }
    
    /* Navbar scroll effect */
    .navbar-scrolled {
        @apply shadow-lg;
    }
</style>

{{-- Navbar Scripts --}}
<script>
    // Mobile menu toggle
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const icon = document.getElementById('mobile-menu-icon');
        menu.classList.toggle('hidden');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    }

    // Theme toggle
    const htmlTag = document.documentElement;
    const themeIcon = document.getElementById('theme-icon');
    
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlTag.classList.add('dark');
        if (themeIcon) themeIcon.classList.replace('fa-moon', 'fa-sun');
    } else {
        htmlTag.classList.remove('dark');
        if (themeIcon) themeIcon.classList.replace('fa-sun', 'fa-moon');
    }
    
    function toggleTheme() {
        if (htmlTag.classList.contains('dark')) {
            htmlTag.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        } else {
            htmlTag.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        }
    }

    // Navbar scroll effect + Smart show/hide for menu page
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        const isHiddenDefault = navbar.dataset.hiddenDefault === 'true';
        
        if (window.scrollY > 20) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
        
        // Smart show/hide for pages with hidden navbar by default
        if (isHiddenDefault) {
            const quickLinks = document.getElementById('quick-links');
            if (quickLinks) {
                const rect = quickLinks.getBoundingClientRect();
                const isOutOfView = rect.bottom < 0;
                
                if (isOutOfView) {
                    navbar.classList.remove('-translate-y-full', 'opacity-0');
                } else {
                    navbar.classList.add('-translate-y-full', 'opacity-0');
                }
            }
        }
    });
</script>
