<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - QuizMaster</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: { premium: { gold: '#FFD700', vibrant: '#F59E0B', dark: '#0F172A' } }
                }
            }
        }
    </script>
    <style>
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
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glow-button {
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.2);
            transition: all 0.3s ease;
        }
        .glow-button:hover {
            box-shadow: 0 0 30px rgba(245, 158, 11, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="text-slate-200 flex items-center justify-center min-h-screen p-6 font-sans">
    <div class="mesh-bg"></div>

    <div class="w-full max-w-md animate-fade-in">
        <!-- Logo Section -->
        <div class="text-center mb-10">
            <div class="inline-block p-4 bg-white/5 rounded-3xl backdrop-blur-md border border-white/10 mb-4 shadow-2xl">
                <img src="{{ asset('logo.svg') }}" class="w-16 h-16" alt="QuizMaster Logo">
            </div>
            <h1 class="text-4xl font-extrabold text-white tracking-tighter">Admin Panel</h1>
            <p class="text-slate-400 font-medium mt-2">Masukkan password untuk akses command center</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-vibrant/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-500/20 rounded-full blur-3xl"></div>

            @if(session('error'))
                <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ url('/admin/login') }}" method="POST">
                @csrf
                <div class="mb-8">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Password Akses</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <i class="fas fa-lock text-lg"></i>
                        </span>
                        <input type="password" name="password" 
                            class="w-full bg-slate-900/50 border border-white/5 rounded-2xl pl-12 pr-4 py-4 text-white placeholder-slate-600 focus:outline-none focus:border-vibrant/50 focus:ring-1 focus:ring-vibrant/50 transition duration-300"
                            placeholder="••••••••" required autofocus>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-gradient-to-r from-vibrant to-orange-600 text-white font-extrabold py-4 rounded-2xl uppercase tracking-widest glow-button flex items-center justify-center gap-3">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-white/5 text-center">
                <a href="{{ route('menu') }}" class="text-slate-500 hover:text-white text-sm font-bold transition flex items-center justify-center gap-2 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition"></i>
                    Kembali ke Menu Utama
                </a>
            </div>
        </div>

        <p class="text-center text-slate-600 text-[10px] mt-8 font-bold uppercase tracking-[0.2em]">
            Developed with Premium Hub &copy; 2026
        </p>
    </div>
</body>
</html>