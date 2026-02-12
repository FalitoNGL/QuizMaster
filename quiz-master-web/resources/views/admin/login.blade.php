<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-xl shadow-lg w-full max-w-sm border border-gray-700">
        <h2 class="text-2xl font-bold mb-6 text-center text-yellow-500">Admin Panel</h2>
        
        @if(session('error'))
            <div class="bg-red-500/20 text-red-300 p-3 rounded mb-4 text-sm text-center">{{ session('error') }}</div>
        @endif

        <form action="{{ url('/admin/login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-gray-400 mb-2">Password Akses</label>
                <input type="password" name="password" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:outline-none focus:border-yellow-500" placeholder="Masukkan password...">
            </div>
            <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 rounded transition">MASUK</button>
        </form>
        <div class="text-center mt-4">
            <a href="{{ route('menu') }}" class="text-gray-500 text-sm hover:text-white">Kembali ke Menu Utama</a>
        </div>
    </div>
</body>
</html>