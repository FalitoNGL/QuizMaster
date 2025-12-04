<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Soal Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen p-8 font-sans">
    <div class="max-w-4xl mx-auto bg-gray-800 rounded-xl shadow-2xl border border-gray-700 p-8">
        <h1 class="text-2xl font-bold mb-6 text-yellow-500">Tambah Soal Baru</h1>

        @if($errors->any())
            <div class="bg-red-500/20 text-red-300 p-4 rounded mb-6 border border-red-500/30">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-400 mb-2 font-bold">Kategori</label>
                    <select name="category_id" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 mb-2 font-bold">Tipe Soal</label>
                    <select id="type-select" name="type" onchange="changeType()" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 font-bold text-blue-300">
                        <option value="single">Pilihan Ganda (Single Choice)</option>
                        <option value="multiple">Pilihan Ganda Majemuk (Multiple)</option>
                        <option value="ordering">Mengurutkan (Ordering)</option>
                        <option value="matching">Menjodohkan (Matching)</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-400 mb-2 font-bold">Pertanyaan</label>
                <textarea name="question_text" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500" required placeholder="Tulis instruksi soal..."></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-400 mb-2 font-bold">Penjelasan / Pembahasan</label>
                    <textarea name="explanation" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500" placeholder="Kenapa jawabannya itu? (Muncul setelah menjawab)"></textarea>
                </div>
                <div>
                    <label class="block text-gray-400 mb-2 font-bold">Referensi / Sumber</label>
                    <input type="text" name="reference" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500" placeholder="Contoh: Modul Bab 3 Hal 12">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-8 bg-gray-700/30 p-4 rounded-lg border border-gray-600">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Gambar (Opsional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Audio (Opsional)</label>
                    <input type="file" name="audio" accept="audio/*" class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                </div>
            </div>

            <hr class="border-gray-700 mb-6">

            <div id="form-single" class="type-form">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-blue-400">Opsi Jawaban (Pilih 1 Benar)</h3>
                    <button type="button" onclick="addOption('single')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-bold shadow-lg"><i class="fas fa-plus"></i> Tambah Opsi</button>
                </div>
                <div id="container-single" class="space-y-3">
                    @for($i=0; $i<2; $i++)
                    <div class="flex items-center gap-3 option-row">
                        <input type="radio" name="correct_single" value="{{ $i }}" class="w-5 h-5 accent-green-500 cursor-pointer dynamic-radio" {{ $i==0 ? 'checked' : '' }}>
                        <input type="text" name="options_single[]" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-blue-500 outline-none" placeholder="Opsi Jawaban" required>
                        <button type="button" onclick="removeOption(this, 'single')" class="remove-btn text-gray-500 text-lg"><i class="fas fa-trash"></i></button>
                    </div>
                    @endfor
                </div>
            </div>

            <div id="form-multiple" class="type-form hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-purple-400">Opsi Jawaban (Centang yang Benar)</h3>
                    <button type="button" onclick="addOption('multiple')" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm font-bold shadow-lg"><i class="fas fa-plus"></i> Tambah Opsi</button>
                </div>
                <div id="container-multiple" class="space-y-3">
                    @for($i=0; $i<2; $i++)
                    <div class="flex items-center gap-3 option-row">
                        <input type="checkbox" name="correct_multiple[]" value="{{ $i }}" class="w-5 h-5 accent-purple-500 cursor-pointer dynamic-check">
                        <input type="text" name="options_multiple[]" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-purple-500 outline-none" placeholder="Opsi Jawaban" required>
                        <button type="button" onclick="removeOption(this, 'multiple')" class="remove-btn text-gray-500 text-lg"><i class="fas fa-trash"></i></button>
                    </div>
                    @endfor
                </div>
            </div>

            <div id="form-ordering" class="type-form hidden">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-orange-400">Urutan Benar</h3>
                        <p class="text-xs text-gray-400">Masukkan urutan dari langkah 1 (atas) sampai akhir.</p>
                    </div>
                    <button type="button" onclick="addOption('ordering')" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-sm font-bold shadow-lg"><i class="fas fa-plus"></i> Tambah Langkah</button>
                </div>
                <div id="container-ordering" class="space-y-3">
                    @for($i=0; $i<2; $i++)
                    <div class="flex items-center gap-3 option-row">
                        <span class="bg-gray-600 w-8 h-8 flex items-center justify-center rounded-full font-bold step-number">{{ $i+1 }}</span>
                        <input type="text" name="options_ordering[]" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-orange-500 outline-none" placeholder="Langkah..." required>
                        <button type="button" onclick="removeOption(this, 'ordering')" class="remove-btn text-gray-500 text-lg"><i class="fas fa-trash"></i></button>
                    </div>
                    @endfor
                </div>
            </div>

            <div id="form-matching" class="type-form hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-pink-400">Pasangan Jawaban</h3>
                    <button type="button" onclick="addOption('matching')" class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1 rounded text-sm font-bold shadow-lg"><i class="fas fa-plus"></i> Tambah Pasangan</button>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-2 font-bold text-sm text-gray-400 px-8">
                    <div>Sisi Kiri (Premis)</div>
                    <div>Sisi Kanan (Jawaban)</div>
                </div>
                <div id="container-matching" class="space-y-3">
                    @for($i=0; $i<2; $i++)
                    <div class="flex items-center gap-3 option-row">
                        <span class="text-gray-500 font-bold step-letter">{{ chr(65+$i) }}</span>
                        <input type="text" name="options_matching_left[]" class="w-1/2 bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-pink-500 outline-none" placeholder="Kiri" required>
                        <i class="fas fa-arrow-right text-gray-500"></i>
                        <input type="text" name="options_matching_right[]" class="w-1/2 bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-pink-500 outline-none" placeholder="Kanan" required>
                        <button type="button" onclick="removeOption(this, 'matching')" class="remove-btn text-gray-500 text-lg"><i class="fas fa-trash"></i></button>
                    </div>
                    @endfor
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-700">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-bold transition">Batal</a>
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transform hover:scale-105 transition">
                    <i class="fas fa-save mr-2"></i> Simpan Soal
                </button>
            </div>
        </form>
    </div>

    <script>
        function changeType() {
            const type = document.getElementById('type-select').value;
            document.querySelectorAll('.type-form').forEach(el => el.classList.add('hidden'));
            document.getElementById('form-' + type).classList.remove('hidden');
        }

        function addOption(type) {
            const container = document.getElementById('container-' + type);
            const rowCount = container.children.length;
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 option-row';

            if (type === 'single') {
                div.innerHTML = `
                    <input type="radio" name="correct_single" value="${rowCount}" class="w-5 h-5 accent-green-500 cursor-pointer dynamic-radio">
                    <input type="text" name="options_single[]" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-blue-500 outline-none" placeholder="Opsi Jawaban" required>
                    <button type="button" onclick="removeOption(this, 'single')" class="remove-btn text-gray-500 text-lg"><i class="fas fa-trash"></i></button>`;
            } else if (type === 'multiple') {
                div.innerHTML = `
                    <input type="checkbox" name="correct_multiple[]" value="${rowCount}" class="w-5 h-5 accent-purple-500 cursor-pointer dynamic-check">
                    <input type="text" name="options_multiple[]" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-purple-500 outline-none" placeholder="Opsi Jawaban" required>
                    <button type="button" onclick="removeOption(this, 'multiple')" class="remove-btn text-gray-500 text-lg"><i class="fas fa-trash"></i></button>`;
            } else if (type === 'ordering') {
                div.innerHTML = `
                    <span class="bg-gray-600 w-8 h-8 flex items-center justify-center rounded-full font-bold step-number">${rowCount + 1}</span>
                    <input type="text" name="options_ordering[]" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-orange-500 outline-none" placeholder="Langkah..." required>
                    <button type="button" onclick="removeOption(this, 'ordering')" class="remove-btn text-gray-500 text-lg"><i class="fas fa-trash"></i></button>`;
            } else if (type === 'matching') {
                const letter = String.fromCharCode(65 + rowCount);
                div.innerHTML = `
                    <span class="text-gray-500 font-bold step-letter">${letter}</span>
                    <input type="text" name="options_matching_left[]" class="w-1/2 bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-pink-500 outline-none" placeholder="Kiri" required>
                    <i class="fas fa-arrow-right text-gray-500"></i>
                    <input type="text" name="options_matching_right[]" class="w-1/2 bg-gray-700 border border-gray-600 rounded px-4 py-2 focus:border-pink-500 outline-none" placeholder="Kanan" required>
                    <button type="button" onclick="removeOption(this, 'matching')" class="remove-btn text-gray-500 text-lg"><i class="fas fa-trash"></i></button>`;
            }
            container.appendChild(div);
        }

        function removeOption(btn, type) {
            const container = document.getElementById('container-' + type);
            if (container.children.length <= 2) { alert("Minimal 2 opsi!"); return; }
            btn.parentElement.remove();
            const rows = container.querySelectorAll('.option-row');
            rows.forEach((row, index) => {
                const radio = row.querySelector('.dynamic-radio'); if(radio) radio.value = index;
                const check = row.querySelector('.dynamic-check'); if(check) check.value = index;
                const num = row.querySelector('.step-number'); if(num) num.innerText = index + 1;
                const letter = row.querySelector('.step-letter'); if(letter) letter.innerText = String.fromCharCode(65 + index);
            });
        }
    </script>
</body>
</html>