<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QuizMaster</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .blob { position: absolute; filter: blur(80px); z-index: -1; opacity: 0.6; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center relative overflow-hidden font-sans">

    <div class="blob bg-purple-600 w-96 h-96 rounded-full top-0 left-0 mix-blend-multiply animate-pulse"></div>
    <div class="blob bg-blue-600 w-96 h-96 rounded-full bottom-0 right-0 mix-blend-multiply animate-pulse"></div>

    <div class="max-w-md w-full glass p-8 rounded-3xl shadow-2xl text-center border-t border-l border-white/10">
        
        <div class="w-24 h-24 mx-auto mb-6 flex items-center justify-center transform hover:scale-110 transition duration-300">
            <img src="{{ asset('logo.svg') }}" alt="QuizMaster Logo" class="w-full h-full drop-shadow-2xl">
        </div>

        <h1 class="text-3xl font-bold mb-2">Selamat Datang!</h1>
        <p class="text-slate-400 mb-8">Login untuk mulai bermain, simpan skor, dan raih prestasi.</p>

        @if(session('error'))
            <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-6 text-sm">{{ session('error') }}</div>
        @endif

        <a href="{{ route('auth.google') }}" class="w-full bg-white hover:bg-gray-100 text-gray-900 font-bold py-3.5 px-4 rounded-xl flex items-center justify-center gap-3 transition transform hover:scale-105 shadow-xl mb-4 group">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-6 h-6" alt="Google Logo">
            <span>Masuk dengan Google</span>
        </a>

        <div class="relative flex py-5 items-center">
            <div class="flex-grow border-t border-slate-600"></div>
            <span class="flex-shrink-0 mx-4 text-slate-500 text-xs">ATAU</span>
            <div class="flex-grow border-t border-slate-600"></div>
        </div>

        <a href="{{ route('menu') }}" class="block w-full py-3 rounded-xl border border-slate-600 text-slate-400 hover:text-white hover:border-slate-400 hover:bg-slate-800 transition text-sm font-semibold">
            Lanjut sebagai Tamu
        </a>
    </div>

</body>
</html>