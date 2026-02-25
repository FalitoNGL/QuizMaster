<!DOCTYPE html>
<html lang="id">
<head>
    <title>Belum Ada Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center text-center p-4">
    <div>
        <div class="text-6xl mb-4">📉</div>
        <h2 class="text-2xl font-bold mb-2">Halo, {{ $playerName }}!</h2>
        <p class="text-slate-400 mb-8">Kamu belum memiliki riwayat permainan. Yuk main dulu!</p>
        <a href="{{ route('menu') }}" class="bg-green-600 hover:bg-green-700 px-8 py-3 rounded-full font-bold">
            Mulai Main Kuis
        </a>
    </div>
</body>
</html>