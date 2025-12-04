<!DOCTYPE html>
<html lang="id">
<head>
    <title>Masuk - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-slate-800 p-8 rounded-2xl shadow-2xl max-w-md w-full text-center border border-slate-700">
        <h2 class="text-2xl font-bold mb-4">Siapa Kamu?</h2>
        <p class="text-slate-400 mb-6">Masukkan nama yang biasa kamu gunakan saat bermain kuis untuk melihat statistikmu.</p>
        
        <form action="{{ route('stats.login') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Nama Pemain" required 
                class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg border border-slate-600 mb-4 focus:outline-none focus:border-blue-500 text-center">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded-lg font-bold transition">
                Lihat Statistik Saya
            </button>
        </form>
        <a href="{{ route('menu') }}" class="block mt-4 text-slate-500 hover:text-white text-sm">Kembali ke Menu</a>
    </div>
</body>
</html>