<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Lobby Duel Live</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center font-sans p-4">
    
    <div class="max-w-md w-full bg-slate-800 p-8 rounded-2xl border border-slate-700 text-center shadow-2xl relative">
        
        <h1 class="text-3xl font-bold mb-2 text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-yellow-500">
            LIVE DUEL ARENA
        </h1>
        <p class="text-slate-400 mb-8">Bertanding secara real-time melawan teman!</p>

        @if(session('error'))
            <div class="bg-red-500/20 text-red-300 p-3 rounded-lg mb-6 text-sm border border-red-500/30">
                {{ session('error') }}
            </div>
        @endif

        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 py-4 rounded-xl font-bold text-lg shadow-lg transform hover:scale-105 transition mb-6">
            <i class="fas fa-plus-circle mr-2"></i> Buat Room Baru
        </button>

        <div class="flex items-center gap-4 mb-6">
            <div class="h-px bg-slate-600 flex-1"></div>
            <span class="text-slate-500 text-sm">ATAU</span>
            <div class="h-px bg-slate-600 flex-1"></div>
        </div>

        <form action="{{ route('live.join') }}" method="POST">
            @csrf
            <input type="text" name="room_code" placeholder="Kode Room (A1B2)" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-3 mb-4 text-center text-white tracking-widest font-mono uppercase focus:border-yellow-500 focus:outline-none" required maxlength="5">
            <button class="w-full bg-slate-700 hover:bg-slate-600 py-3 rounded-xl font-bold transition">
                Masuk Room
            </button>
        </form>

        <a href="{{ route('menu') }}" class="block mt-6 text-slate-500 hover:text-white text-sm">Kembali ke Menu</a>
    </div>

    <div id="createModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-slate-800 w-full max-w-sm rounded-2xl border border-slate-600 shadow-2xl overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Atur Permainan</h3>
                    <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
                </div>

                <form action="{{ route('live.create') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm text-gray-400 mb-2 font-bold">Pilih Kategori</label>
                        <select name="category_id" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 focus:border-blue-500 outline-none text-white">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-400 mb-2 font-bold">Jumlah Soal</label>
                        <div class="flex gap-2">
                            @foreach([5, 10, 15, 20] as $num)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="total_questions" value="{{ $num }}" class="peer sr-only" {{ $num==10 ? 'checked' : '' }}>
                                <div class="text-center py-2 bg-slate-700 rounded-lg peer-checked:bg-blue-600 peer-checked:text-white hover:bg-slate-600 transition text-sm">
                                    {{ $num }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm text-gray-400 mb-2 font-bold">Waktu Per Soal</label>
                        <div class="flex gap-2">
                            @foreach([10, 20, 30, 60] as $sec)
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="duration" value="{{ $sec }}" class="peer sr-only" {{ $sec==30 ? 'checked' : '' }}>
                                <div class="text-center py-2 bg-slate-700 rounded-lg peer-checked:bg-yellow-600 peer-checked:text-white hover:bg-slate-600 transition text-sm">
                                    {{ $sec }}s
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-500 py-3 rounded-xl font-bold text-white shadow-lg transition">
                        🚀 Mulai Live
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>