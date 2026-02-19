<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Kategori - Quiz Master</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen font-sans">
    
    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-lg">
        <div class="font-bold text-xl text-yellow-500 flex items-center gap-2">
            <i class="fas fa-tags"></i> Kelola Kategori
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-white px-3 py-1 rounded hover:bg-gray-700 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </nav>

    <div class="container mx-auto px-6 py-8">
        
        @if(session('success'))
            <div class="bg-green-500/20 text-green-300 p-4 rounded-lg mb-6 border border-green-500/30 flex items-center gap-2 animate-fade-in">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-xl overflow-hidden shadow-xl border border-gray-700">
                    <div class="p-4 bg-gray-750 border-b border-gray-700 font-bold text-lg text-blue-400 flex items-center gap-2">
                        <i class="fas fa-list"></i> Daftar Kategori Aktif
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-900/50 text-gray-400 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Info Kategori</th>
                                    <th class="px-6 py-4">Deskripsi</th>
                                    <th class="px-6 py-4 text-center">Total Soal</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @forelse($categories as $cat)
                                <tr class="hover:bg-gray-700/30 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-gray-700 flex items-center justify-center text-yellow-500 text-xl shadow-md">
                                                @if(str_starts_with($cat->icon_class, 'fa'))
                                                    <i class="{{ $cat->icon_class }}"></i>
                                                @else
                                                    <i class="fas fa-book"></i> @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-white">{{ $cat->name }}</div>
                                                <div class="text-xs text-gray-500 font-mono">{{ $cat->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-400 leading-snug">
                                        {{ Str::limit($cat->description, 60) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-block bg-blue-900/50 text-blue-300 py-1 px-3 rounded-full text-xs font-bold border border-blue-800">
                                            {{ $cat->questions_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin.categories.delete', $cat->id) }}" onclick="return confirm('PERINGATAN KERAS!\n\nMenghapus kategori ini akan MENGHAPUS SEMUA SOAL di dalamnya secara permanen.\n\nApakah Anda yakin ingin melanjutkan?')" class="text-red-400 hover:text-red-200 transition p-2 rounded hover:bg-red-900/20" title="Hapus Permanen">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                        Belum ada kategori. Silakan buat di sebelah kanan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 p-6 sticky top-24">
                    <h3 class="text-xl font-bold text-yellow-500 mb-6 border-b border-gray-700 pb-4 flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i> Buat Kategori Baru
                    </h3>
                    
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-400 mb-2">Nama Kategori</label>
                            <input type="text" name="name" id="name" oninput="generateSlug()" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition" required placeholder="Contoh: Sejarah Dunia">
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-400 mb-2">Slug URL (Otomatis)</label>
                            <input type="text" name="slug" id="slug" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-3 text-gray-500 text-sm font-mono cursor-not-allowed" readonly required>
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-400 mb-2">Deskripsi Singkat</label>
                            <textarea name="description" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition" required placeholder="Apa yang dipelajari di kategori ini?"></textarea>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-400 mb-3">Pilih Ikon</label>
                            <div class="grid grid-cols-5 gap-3">
                                @php
                                    $icons = [
                                        'fas fa-book', 'fas fa-flask', 'fas fa-globe-asia', 'fas fa-calculator', 'fas fa-laptop-code', 
                                        'fas fa-music', 'fas fa-palette', 'fas fa-running', 'fas fa-brain', 'fas fa-heartbeat',
                                        'fas fa-comments', 'fas fa-history', 'fas fa-coins', 'fas fa-gavel', 'fas fa-tree'
                                    ];
                                @endphp
                                @foreach($icons as $icon)
                                <label class="cursor-pointer group">
                                    <input type="radio" name="icon_class" value="{{ $icon }}" class="peer sr-only" {{ $loop->first ? 'checked' : '' }}>
                                    <div class="w-10 h-10 rounded-lg bg-gray-700 flex items-center justify-center text-gray-400 peer-checked:bg-yellow-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:scale-110 hover:bg-gray-600 transition duration-200">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-500 hover:to-orange-500 text-white font-bold py-3 rounded-lg transition shadow-lg transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Simpan Kategori
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Script Auto Generate Slug
        function generateSlug() {
            const name = document.getElementById('name').value;
            const slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '') // Hapus karakter spesial
                .replace(/\s+/g, '-')     // Ganti spasi dengan -
                .replace(/-+/g, '-');     // Hapus - berlebih
            document.getElementById('slug').value = slug;
        }
    </script>
</body>
</html>