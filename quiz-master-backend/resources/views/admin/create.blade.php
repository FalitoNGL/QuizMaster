<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Soal Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen">
    
    <div class="container mx-auto px-6 py-8 max-w-4xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-yellow-500">Tambah Soal Baru</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-white"><i class="fas fa-arrow-left"></i> Batal</a>
        </div>

        <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="bg-gray-800 p-8 rounded-xl shadow-xl border border-gray-700">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold mb-2">Kategori</label>
                    <select name="category_id" class="w-full bg-gray-700 border border-gray-600 rounded p-3 text-white focus:outline-none focus:border-blue-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2">Tipe Soal</label>
                    <select name="type" id="type-select" onchange="changeType()" class="w-full bg-gray-700 border border-gray-600 rounded p-3 text-white focus:outline-none focus:border-blue-500">
                        <option value="single">Pilihan Ganda (Single)</option>
                        <option value="multiple">Pilihan Ganda (Multiple)</option>
                        <option value="ordering">Mengurutkan (Ordering)</option>
                        <option value="matching">Menjodohkan (Matching)</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2">Pertanyaan</label>
                <textarea name="question_text" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded p-3 text-white focus:border-blue-500" required placeholder="Tulis soal di sini..."></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold mb-2">Gambar (Opsional)</label>
                    <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2">Audio (Opsional)</label>
                    <input type="file" name="audio" accept="audio/*" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                </div>
            </div>

            <div class="bg-gray-900 p-6 rounded-lg border border-gray-700 mb-6">
                <h3 class="font-bold text-blue-400 mb-4 border-b border-gray-700 pb-2">Opsi Jawaban</h3>
                
                <div id="section-single" class="space-y-3">
                    @for($i=0; $i<4; $i++)
                    <div class="flex items-center gap-3">
                        <input type="radio" name="correct_single" value="{{ $i }}" {{ $i==0 ? 'checked' : '' }} class="w-5 h-5 accent-green-500">
                        <input type="text" name="options_single[]" class="flex-1 bg-gray-700 border border-gray-600 rounded p-2 text-white" placeholder="Pilihan {{ $i+1 }}" {{ $i<2 ? 'required' : '' }}>
                    </div>
                    @endfor
                    <p class="text-xs text-gray-500 mt-2">*Pilih radio button untuk kunci jawaban benar.</p>
                </div>

                <div id="section-multiple" class="hidden space-y-3">
                    @for($i=0; $i<4; $i++)
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="correct_multiple[]" value="{{ $i }}" class="w-5 h-5 accent-blue-500">
                        <input type="text" name="options_multiple[]" class="flex-1 bg-gray-700 border border-gray-600 rounded p-2 text-white" placeholder="Pilihan {{ $i+1 }}">
                    </div>
                    @endfor
                    <p class="text-xs text-gray-500 mt-2">*Centang kotak untuk jawaban benar (bisa lebih dari satu).</p>
                </div>

                <div id="section-ordering" class="hidden space-y-3">
                    <p class="text-sm text-yellow-500 mb-2">Masukkan urutan yang BENAR (dari atas ke bawah). Aplikasi akan mengacaknya saat dimainkan.</p>
                    @for($i=0; $i<4; $i++)
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center font-bold text-sm">{{ $i+1 }}</span>
                        <input type="text" name="options_ordering[]" class="flex-1 bg-gray-700 border border-gray-600 rounded p-2 text-white" placeholder="Urutan ke-{{ $i+1 }}">
                    </div>
                    @endfor
                </div>

                <div id="section-matching" class="hidden space-y-3">
                    <p class="text-sm text-yellow-500 mb-2">Masukkan pasangan yang BENAR (Kiri = Soal, Kanan = Jawaban). Aplikasi akan mengacaknya.</p>
                    @for($i=0; $i<4; $i++)
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="options_matching_left[]" class="bg-gray-700 border border-gray-600 rounded p-2 text-white" placeholder="Sisi Kiri (Pertanyaan)">
                        <input type="text" name="options_matching_right[]" class="bg-gray-700 border border-gray-600 rounded p-2 text-white" placeholder="Sisi Kanan (Jawaban)">
                    </div>
                    @endfor
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2">Pembahasan & Referensi</label>
                <textarea name="explanation" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded p-3 text-white mb-3" placeholder="Penjelasan jawaban (muncul setelah menjawab)..."></textarea>
                <input type="text" name="reference" class="w-full bg-gray-700 border border-gray-600 rounded p-3 text-white" placeholder="Sumber/Referensi (contoh: Hal 20)">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-lg shadow-lg transition">Simpan Soal</button>
        </form>
    </div>

    <script>
        function changeType() {
            const type = document.getElementById('type-select').value;
            // Sembunyikan semua section
            ['single', 'multiple', 'ordering', 'matching'].forEach(t => {
                document.getElementById('section-' + t).classList.add('hidden');
                // Matikan input agar tidak dikirim dan menyebabkan error validasi backend
                const container = document.getElementById('section-' + t);
                container.querySelectorAll('input').forEach(i => i.disabled = true);
            });

            // Tampilkan section yang dipilih
            const active = document.getElementById('section-' + type);
            active.classList.remove('hidden');
            active.querySelectorAll('input').forEach(i => i.disabled = false);
        }
        // Jalankan saat load
        changeType();
    </script>
</body>
</html>