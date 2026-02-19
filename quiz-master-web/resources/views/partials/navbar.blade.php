{{-- Global Navbar Component - Premium Design --}}
{{-- Pass $navbarHidden = true to hide navbar initially (for menu page) --}}
@php $isHiddenByDefault = isset($navbarHidden) && $navbarHidden; @endphp
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 {{ $isHiddenByDefault ? '-translate-y-full opacity-0' : '' }}" data-hidden-default="{{ $isHiddenByDefault ? 'true' : 'false' }}">
    <div class="navbar-glass">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                {{-- Logo & Brand --}}
                <a href="{{ route('menu') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('logo.svg') }}" alt="QM" class="w-full h-full">
                    </div>
                    <span class="font-bold text-xl hidden sm:block">
                        <span class="text-white">Quiz</span><span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">Master</span>
                    </span>
                </a>

                {{-- Desktop Navigation - Pill Style --}}
                <div class="hidden md:flex items-center">
                    <div class="nav-container">
                        <a href="{{ route('menu') }}" class="nav-item {{ request()->routeIs('menu') ? 'nav-item-active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Beranda</span>
                        </a>
                        <a href="{{ route('live.lobby') }}" class="nav-item {{ request()->routeIs('live.*') ? 'nav-item-active' : '' }}">
                            <i class="fas fa-gamepad"></i>
                            <span>Live Duel</span>
                            <span class="live-badge">LIVE</span>
                        </a>
                        <a href="{{ route('quiz.leaderboard') }}" class="nav-item {{ request()->routeIs('quiz.leaderboard') ? 'nav-item-active' : '' }}">
                            <i class="fas fa-trophy"></i>
                            <span>Peringkat</span>
                        </a>
                        <a href="{{ route('achievements') }}" class="nav-item {{ request()->routeIs('achievements') ? 'nav-item-active' : '' }}">
                            <i class="fas fa-medal"></i>
                            <span>Pencapaian</span>
                        </a>
                        <a href="{{ route('stats') }}" class="nav-item {{ request()->routeIs('stats') ? 'nav-item-active' : '' }}">
                            <i class="fas fa-chart-pie"></i>
                            <span>Statistik</span>
                        </a>
                        @auth
                        <a href="{{ route('social.index') }}" class="nav-item {{ request()->routeIs('social.*') ? 'nav-item-active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Sosial</span>
                        </a>
                        @endauth
                    </div>
                </div>

                {{-- Right Side Actions --}}
                <div class="flex items-center gap-1">
                    {{-- Theme Toggle --}}
                    <button onclick="toggleTheme()" class="action-btn" title="Ganti Tema">
                        <i id="theme-icon" class="fas fa-moon"></i>
                    </button>

                    {{-- Admin Button --}}
                    @if(session('is_admin'))
                    <a href="{{ route('admin.dashboard') }}" class="action-btn action-btn-gold" title="Dashboard Admin">
                        <i class="fas fa-crown"></i>
                    </a>
                    @endif

                    {{-- Settings --}}
                    <a href="{{ route('settings') }}" class="action-btn hover:rotate-90" title="Pengaturan">
                        <i class="fas fa-cog"></i>
                    </a>

                    {{-- User Profile / Login --}}
                    @auth
                    <div class="flex items-center gap-2 ml-2 pl-3 border-l border-white/20">
                        <a href="{{ route('profile.show', Auth::id()) }}" class="user-profile-link" title="Lihat Profil">
                            <div class="user-avatar-ring">
                                <img src="{{ Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.Auth::user()->name }}" class="w-8 h-8 rounded-full bg-slate-700">
                            </div>
                            <span class="font-semibold text-sm hidden lg:block text-white/90">{{ Str::limit(Auth::user()->name, 10) }}</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="action-btn text-red-400 hover:text-red-300 hover:bg-red-500/20" title="Keluar">
                                <i class="fas fa-power-off text-sm"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="hidden sm:inline">Masuk</span>
                    </a>
                    @endauth

                    {{-- Mobile Menu Button - Only visible on mobile --}}
                    <button onclick="toggleMobileMenu()" class="mobile-menu-btn">
                        <i id="mobile-menu-icon" class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobile-menu" class="md:hidden hidden mobile-menu-glass">
        <div class="container mx-auto px-4 py-4 flex flex-col gap-2">
            <a href="{{ route('menu') }}" class="mobile-nav-item {{ request()->routeIs('menu') ? 'mobile-nav-active' : '' }}">
                <div class="mobile-icon bg-gradient-to-br from-cyan-500 to-blue-500"><i class="fas fa-home"></i></div>
                <span>Beranda</span>
            </a>
            <a href="{{ route('live.lobby') }}" class="mobile-nav-item {{ request()->routeIs('live.*') ? 'mobile-nav-active' : '' }}">
                <div class="mobile-icon bg-gradient-to-br from-red-500 to-pink-500"><i class="fas fa-gamepad"></i></div>
                <span>Live Duel</span>
                <span class="ml-auto text-xs bg-red-500 text-white px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
            </a>
            <a href="{{ route('quiz.leaderboard') }}" class="mobile-nav-item {{ request()->routeIs('quiz.leaderboard') ? 'mobile-nav-active' : '' }}">
                <div class="mobile-icon bg-gradient-to-br from-yellow-500 to-orange-500"><i class="fas fa-trophy"></i></div>
                <span>Peringkat</span>
            </a>
            <a href="{{ route('achievements') }}" class="mobile-nav-item {{ request()->routeIs('achievements') ? 'mobile-nav-active' : '' }}">
                <div class="mobile-icon bg-gradient-to-br from-purple-500 to-pink-500"><i class="fas fa-medal"></i></div>
                <span>Pencapaian</span>
            </a>
            <a href="{{ route('stats') }}" class="mobile-nav-item {{ request()->routeIs('stats') ? 'mobile-nav-active' : '' }}">
                <div class="mobile-icon bg-gradient-to-br from-blue-500 to-indigo-500"><i class="fas fa-chart-pie"></i></div>
                <span>Statistik</span>
            </a>
            @auth
            <a href="{{ route('social.index') }}" class="mobile-nav-item {{ request()->routeIs('social.*') ? 'mobile-nav-active' : '' }}">
                <div class="mobile-icon bg-gradient-to-br from-pink-500 to-rose-500"><i class="fas fa-users"></i></div>
                <span>Sosial</span>
            </a>
            @endauth
        </div>
    </div>
</nav>

{{-- Premium Navbar Styles --}}
<style>
    /* Main navbar glass effect */
    .navbar-glass {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 50%, rgba(51, 65, 85, 0.85) 100%);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
    }

    /* Logo icon with gradient and glow */
    .logo-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #06b6d4, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 20px rgba(6, 182, 212, 0.4), inset 0 1px 0 rgba(255,255,255,0.2);
        transition: all 0.3s ease;
    }
    .logo-icon:hover {
        transform: scale(1.1) rotate(-5deg);
        box-shadow: 0 0 30px rgba(139, 92, 246, 0.5);
    }

    /* Navigation container with pill background */
    .nav-container {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 6px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Navigation items */
    .nav-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
        position: relative;
    }
    .nav-item:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }
    .nav-item-active {
        color: white;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.3), rgba(139, 92, 246, 0.3));
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.2);
    }
    .nav-item i {
        font-size: 14px;
    }

    /* Live badge */
    .live-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        font-size: 9px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 50px;
        background: linear-gradient(135deg, #ef4444, #f97316);
        color: white;
        animation: pulse 2s infinite;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    /* Action buttons */
    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }

    /* Mobile menu button - hidden on desktop */
    .mobile-menu-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
        margin-left: 4px;
    }
    .mobile-menu-btn:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }
    @media (min-width: 768px) {
        .mobile-menu-btn {
            display: none !important;
        }
    }
    .action-btn-gold {
        color: #fbbf24;
    }
    .action-btn-gold:hover {
        color: #fcd34d;
        background: rgba(251, 191, 36, 0.15);
    }

    /* User profile link */
    .user-profile-link {
        display: flex;
        align-items: center;
        gap: 10px;
        transition: opacity 0.3s ease;
    }
    .user-profile-link:hover {
        opacity: 0.8;
    }

    /* User avatar ring */
    .user-avatar-ring {
        padding: 2px;
        border-radius: 50%;
        background: linear-gradient(135deg, #06b6d4, #8b5cf6);
    }

    /* Login button */
    .login-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        margin-left: 8px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        color: white;
        background: linear-gradient(135deg, #06b6d4, #8b5cf6);
        box-shadow: 0 4px 20px rgba(6, 182, 212, 0.3);
        transition: all 0.3s ease;
    }
    .login-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 25px rgba(139, 92, 246, 0.4);
    }

    /* Mobile menu glass */
    .mobile-menu-glass {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.95) 100%);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Mobile nav items */
    .mobile-nav-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        border-radius: 16px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
    }
    .mobile-nav-item:hover {
        background: rgba(255, 255, 255, 0.05);
    }
    .mobile-nav-active {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(139, 92, 246, 0.2));
        color: white;
    }

    /* Mobile icon */
    .mobile-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }

    /* Navbar scroll effect */
    .navbar-scrolled .navbar-glass {
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4);
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Light mode adjustments */
    html:not(.dark) .navbar-glass {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(241, 245, 249, 0.9) 100%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    html:not(.dark) .nav-item {
        color: rgba(51, 65, 85, 0.8);
    }
    html:not(.dark) .nav-item:hover {
        color: #0f172a;
        background: rgba(0, 0, 0, 0.05);
    }
    html:not(.dark) .nav-item-active {
        color: #0891b2;
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.15), rgba(139, 92, 246, 0.15));
    }
    html:not(.dark) .nav-container {
        background: rgba(0, 0, 0, 0.03);
        border-color: rgba(0, 0, 0, 0.08);
    }
    html:not(.dark) .action-btn {
        color: rgba(51, 65, 85, 0.7);
    }
    html:not(.dark) .action-btn:hover {
        color: #0f172a;
        background: rgba(0, 0, 0, 0.05);
    }
    html:not(.dark) .user-profile-link span {
        color: #334155;
    }
    html:not(.dark) .mobile-menu-glass {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(241, 245, 249, 0.95) 100%);
    }
    html:not(.dark) .mobile-nav-item {
        color: #334155;
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
