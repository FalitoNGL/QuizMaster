<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Duel Live - QuizMaster</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .blob { position: absolute; filter: blur(80px); z-index: -1; opacity: 0.6; animation: move 10s infinite alternate; }
        @keyframes move { from { transform: translate(0, 0) scale(1); } to { transform: translate(20px, -20px) scale(1.1); } }
        @keyframes bounce-in { 0% { transform: scale(0.9); opacity: 0; } 70% { transform: scale(1.05); } 100% { transform: scale(1); opacity: 1; } }
        .animate-bounce-in { animation: bounce-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    {{-- Top Left Brand Logo --}}
    <div class="absolute top-6 left-6 z-40">
        <a href="{{ route('menu') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 group-hover:scale-110 transition-transform duration-300">
                <img src="{{ asset('logo.svg') }}" alt="QM" class="w-full h-full">
            </div>
            <span class="font-bold text-xl hidden sm:block">
                <span class="text-white transition-colors">Quiz</span><span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">Master</span>
            </span>
        </a>
    </div>
    
    <!-- Mesh Background -->
    <div class="blob bg-purple-600 w-96 h-96 rounded-full -top-20 -left-20 mix-blend-multiply"></div>
    <div class="blob bg-cyan-600 w-96 h-96 rounded-full -bottom-20 -right-20 mix-blend-multiply"></div>
    <div class="blob bg-pink-600 w-80 h-80 rounded-full top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 mix-blend-multiply"></div>

    <div class="max-w-md w-full glass p-8 rounded-[2.5rem] text-center shadow-2xl relative z-10 animate-bounce-in">
        <div class="mb-6 relative inline-block">
            <div class="absolute -inset-1 bg-gradient-to-r from-pink-500 to-yellow-500 rounded-full blur opacity-75 animate-pulse"></div>
            <div class="relative bg-slate-800 w-20 h-20 rounded-full flex items-center justify-center text-3xl border-2 border-white/20">
                <i class="fas fa-bolt text-yellow-400"></i>
            </div>
        </div>

        <h1 class="text-4xl font-black mb-2 text-transparent bg-clip-text bg-gradient-to-r from-pink-400 via-yellow-400 to-pink-500 tracking-tight">
            LIVE DUEL ARENA
        </h1>
        <p class="text-slate-400 mb-10 font-medium">Uji kecepatanmu melawan pemain lain!</p>

        @if(session('error'))
            <div class="bg-red-500/20 text-red-300 p-4 rounded-2xl mb-8 text-sm border border-red-500/30 flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="space-y-4">
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 py-5 rounded-2xl font-black text-xl shadow-[0_0_20px_rgba(37,99,235,0.4)] transform hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center justify-center gap-3">
                <i class="fas fa-plus-circle"></i> BUAT ROOM
            </button>

            <div class="flex items-center gap-4 py-4">
                <div class="h-px bg-slate-700/50 flex-1"></div>
                <span class="text-slate-500 text-xs font-bold tracking-widest uppercase">ATAU</span>
                <div class="h-px bg-slate-700/50 flex-1"></div>
            </div>

            <form action="{{ route('live.join') }}" method="POST" class="space-y-4">
                @csrf
                <div class="relative group">
                    <input type="text" name="room_code" placeholder="KODE ROOM" class="w-full bg-slate-800/50 border-2 border-slate-700 rounded-2xl px-4 py-4 text-center text-white tracking-[0.5rem] font-black uppercase focus:border-yellow-500/50 focus:ring-0 focus:outline-none transition-colors placeholder:tracking-normal placeholder:font-bold" required maxlength="5">
                </div>
                <button class="w-full bg-slate-800 hover:bg-slate-700 border border-slate-700 py-4 rounded-2xl font-black transform hover:scale-[1.02] active:scale-95 transition-all">
                    JOIN SEKARANG
                </button>
            </form>
        </div>

        <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 mt-10 text-slate-500 hover:text-white transition font-bold text-sm">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Menu
        </a>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="fixed inset-0 bg-black/60 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
        <div class="glass w-full max-w-sm rounded-[2rem] shadow-2xl overflow-hidden animate-bounce-in">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl font-black text-white tracking-tight">Atur Permainan</h3>
                    <button onclick="document.getElementById('createModal').classList.add('hidden')" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white/10 text-slate-400 hover:text-white transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('live.create') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-xs text-slate-500 mb-3 font-black uppercase tracking-widest">Kategori Duel</label>
                        <select name="category_id" class="w-full bg-slate-800 border-2 border-slate-700 rounded-2xl px-4 py-3 focus:border-blue-500/50 outline-none text-white font-bold transition">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-500 mb-3 font-black uppercase tracking-widest">Jumlah Soal</label>
                        <div class="flex gap-2">
                            @foreach([5, 10, 15, 20] as $num)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="total_questions" value="{{ $num }}" class="peer sr-only" {{ $num==10 ? 'checked' : '' }}>
                                <div class="text-center py-3 bg-slate-800 rounded-xl border-2 border-transparent peer-checked:border-blue-500 peer-checked:bg-blue-600/20 peer-checked:text-blue-300 hover:bg-slate-700 transition font-bold">
                                    {{ $num }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-500 mb-3 font-black uppercase tracking-widest">Waktu Per Soal</label>
                        <div class="flex gap-2">
                            @foreach([10, 20, 30, 60] as $sec)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="duration" value="{{ $sec }}" class="peer sr-only" {{ $sec==30 ? 'checked' : '' }}>
                                <div class="text-center py-3 bg-slate-800 rounded-xl border-2 border-transparent peer-checked:border-yellow-500 peer-checked:bg-yellow-600/20 peer-checked:text-yellow-400 hover:bg-slate-700 transition font-bold text-xs uppercase">
                                    {{ $sec }}s
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-500 hover:to-green-500 py-4 rounded-2xl font-black text-white shadow-xl transform hover:scale-[1.02] active:scale-95 transition-all mt-4">
                        🚀 GAS DUEL!
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
