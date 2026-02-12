<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <title>Edit Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="bg-gray-100 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen flex items-center justify-center font-sans transition-colors duration-300">

    <div class="max-w-md w-full bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-700">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600 dark:text-blue-400">Edit Identitas</h2>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            
            <div class="text-center mb-6">
                <img src="{{ $user->avatar ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$user->name }}" class="w-24 h-24 rounded-full border-4 border-gray-300 dark:border-slate-600 mx-auto bg-slate-200 dark:bg-slate-700 object-cover">
                <p class="text-xs text-slate-500 mt-2">Avatar diambil dari Google / Auto-generated</p>
            </div>

            <div class="mb-4">
                <label class="block text-slate-600 dark:text-slate-400 text-sm mb-1 font-bold">Nama Tampilan</label>
                <input type="text" name="name" value="{{ $user->name }}" class="w-full bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white transition" required>
            </div>

            <div class="mb-4">
                <label class="block text-slate-600 dark:text-slate-400 text-sm mb-1 font-bold">Gelar / Title (Maks 30 huruf)</label>
                <input type="text" name="title" value="{{ $user->title }}" class="w-full bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white transition" placeholder="Contoh: Sang Penakluk Kuis">
            </div>

            <div class="mb-6">
                <label class="block text-slate-600 dark:text-slate-400 text-sm mb-1 font-bold">Bio Singkat</label>
                <textarea name="bio" rows="3" class="w-full bg-gray-50 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 text-slate-800 dark:text-white transition" placeholder="Ceritakan sedikit tentang dirimu...">{{ $user->bio }}</textarea>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('profile.show', $user->id) }}" class="flex-1 bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white py-2 rounded-lg font-bold text-center transition border border-gray-300 dark:border-slate-600">Batal</a>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-bold transition shadow-lg">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>