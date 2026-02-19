<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Soal</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen p-8 font-sans">
    <div class="max-w-4xl mx-auto bg-gray-800 rounded-xl shadow-2xl border border-gray-700 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-500"><i class="fas fa-edit mr-2"></i>Edit Soal</h1>
            <span class="px-3 py-1 bg-gray-700 rounded text-xs text-gray-300 font-mono uppercase">Tipe: {{ $question->type }}</span>
        </div>

        <form action="{{ route('admin.update', $question->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-400 mb-2 font-bold">Kategori</label>
                <select name="category_id" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $cat->id == $question->category_id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-400 mb-2 font-bold">Pertanyaan</label>
                <textarea name="question_text" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none" required>{{ $question->question_text }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-700/30 p-4 rounded-lg border border-gray-600">
                    <label class="block text-sm text-blue-400 mb-2 font-bold"><i class="fas fa-image mr-1"></i> Gambar</label>
                    
                    @if($question->image_path)
                        <div class="mb-3 relative group inline-block">
                            <img src="{{ asset('storage/' . $question->image_path) }}" class="h-32 rounded border border-gray-500">
                            <div class="mt-2">
                                <label class="inline-flex items-center text-xs text-red-400 hover:text-red-300 cursor-pointer">
                                    <input type="checkbox" name="remove_image" value="1" class="mr-1"> Hapus Gambar
                                </label>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="image" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                </div>

                <div class="bg-gray-700/30 p-4 rounded-lg border border-gray-600">
                    <label class="block text-sm text-purple-400 mb-2 font-bold"><i class="fas fa-volume-up mr-1"></i> Audio</label>
                    
                    @if($question->audio_path)
                        <div class="mb-3">
                            <audio controls src="{{ asset('storage/' . $question->audio_path) }}" class="h-8 w-full mb-2"></audio>
                            <label class="inline-flex items-center text-xs text-red-400 hover:text-red-300 cursor-pointer">
                                <input type="checkbox" name="remove_audio" value="1" class="mr-1"> Hapus Audio
                            </label>
                        </div>
                    @endif
                    <input type="file" name="audio" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                </div>
            </div>

            <div class="mb-8 bg-gray-700/30 p-6 rounded-xl border border-gray-600">
                <div class="flex justify-between items-center mb-4">
                    <label class="text-gray-300 font-bold text-lg">Opsi Jawaban</label>
                    <button type="button" onclick="addOption()" class="bg-green-600 hover:bg-green-500 text-white px-3 py-1 rounded text-xs font-bold transition">
                        <i class="fas fa-plus mr-1"></i> Tambah Opsi
                    </button>
                </div>

                <div id="options-wrapper" class="space-y-3">
                    @if($question->type == 'single')
                        @foreach($question->options as $index => $opt)
                        <div class="flex items-center gap-3 option-row">
                            <input type="radio" name="correct_single" value="{{ $index }}" class="w-5 h-5 accent-green-500 cursor-pointer" {{ $opt->is_correct ? 'checked' : '' }} required>
                            <input type="text" name="options_single[]" value="{{ $opt->option_text }}" class="flex-grow bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-green-500 focus:outline-none" placeholder="Isi jawaban..." required>
                            <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-400 p-2"><i class="fas fa-trash"></i></button>
                        </div>
                        @endforeach

                    @elseif($question->type == 'multiple')
                        @foreach($question->options as $index => $opt)
                        <div class="flex items-center gap-3 option-row">
                            <input type="checkbox" name="correct_multiple[]" value="{{ $index }}" class="w-5 h-5 accent-blue-500 cursor-pointer" {{ $opt->is_correct ? 'checked' : '' }}>
                            <input type="text" name="options_multiple[]" value="{{ $opt->option_text }}" class="flex-grow bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-blue-500 focus:outline-none" placeholder="Isi jawaban..." required>
                            <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-400 p-2"><i class="fas fa-trash"></i></button>
                        </div>
                        @endforeach

                    @elseif($question->type == 'ordering')
                        @php $sortedOptions = $question->options->sortBy('correct_order'); @endphp
                        @foreach($sortedOptions as $index => $opt)
                        <div class="flex items-center gap-3 option-row">
                            <span class="bg-gray-600 text-white w-8 h-8 flex items-center justify-center rounded font-mono font-bold order-num">{{ $index + 1 }}</span>
                            <input type="text" name="options_ordering[]" value="{{ $opt->option_text }}" class="flex-grow bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" placeholder="Isi urutan..." required>
                            <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-400 p-2"><i class="fas fa-trash"></i></button>
                        </div>
                        @endforeach

                    @elseif($question->type == 'matching')
                        @foreach($question->options as $index => $opt)
                        <div class="flex items-center gap-3 option-row">
                            <input type="text" name="options_matching_left[]" value="{{ $opt->option_text }}" class="w-1/2 bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-purple-500 focus:outline-none" placeholder="Pertanyaan (Kiri)" required>
                            <span class="text-gray-500"><i class="fas fa-arrow-right"></i></span>
                            <input type="text" name="options_matching_right[]" value="{{ $opt->matching_pair }}" class="w-1/2 bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-purple-500 focus:outline-none" placeholder="Jawaban (Kanan)" required>
                            <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-400 p-2"><i class="fas fa-trash"></i></button>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-400 mb-2 font-bold">Penjelasan / Pembahasan</label>
                <textarea name="explanation" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">{{ $question->explanation }}</textarea>
            </div>
            <div class="mb-8">
                <label class="block text-gray-400 mb-2 font-bold">Referensi</label>
                <input type="text" name="reference" value="{{ $question->reference }}" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:border-blue-500 focus:outline-none">
            </div>

            <div class="flex justify-end gap-4 border-t border-gray-700 pt-6">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-bold transition">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:shadow-blue-500/30 transition transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        const type = "{{ $question->type }}";
        const wrapper = document.getElementById('options-wrapper');

        function addOption() {
            const count = wrapper.children.length;
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 option-row mb-3';

            if (type === 'single') {
                div.innerHTML = `
                    <input type="radio" name="correct_single" value="${count}" class="w-5 h-5 accent-green-500 cursor-pointer" required>
                    <input type="text" name="options_single[]" class="flex-grow bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-green-500 focus:outline-none" placeholder="Opsi Baru..." required>
                    <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-400 p-2"><i class="fas fa-trash"></i></button>
                `;
            } else if (type === 'multiple') {
                div.innerHTML = `
                    <input type="checkbox" name="correct_multiple[]" value="${count}" class="w-5 h-5 accent-blue-500 cursor-pointer">
                    <input type="text" name="options_multiple[]" class="flex-grow bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-blue-500 focus:outline-none" placeholder="Opsi Baru..." required>
                    <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-400 p-2"><i class="fas fa-trash"></i></button>
                `;
            } else if (type === 'ordering') {
                div.innerHTML = `
                    <span class="bg-gray-600 text-white w-8 h-8 flex items-center justify-center rounded font-mono font-bold order-num">${count + 1}</span>
                    <input type="text" name="options_ordering[]" class="flex-grow bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-yellow-500 focus:outline-none" placeholder="Item Urutan Baru..." required>
                    <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-400 p-2"><i class="fas fa-trash"></i></button>
                `;
            } else if (type === 'matching') {
                div.innerHTML = `
                    <input type="text" name="options_matching_left[]" class="w-1/2 bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-purple-500 focus:outline-none" placeholder="Pertanyaan (Kiri)" required>
                    <span class="text-gray-500"><i class="fas fa-arrow-right"></i></span>
                    <input type="text" name="options_matching_right[]" class="w-1/2 bg-gray-800 border border-gray-600 rounded px-4 py-2 text-white focus:border-purple-500 focus:outline-none" placeholder="Jawaban (Kanan)" required>
                    <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-400 p-2"><i class="fas fa-trash"></i></button>
                `;
            }
            wrapper.appendChild(div);
            reindex();
        }

        function removeOption(btn) {
            btn.parentElement.remove();
            reindex();
        }

        function reindex() {
            const rows = wrapper.querySelectorAll('.option-row');
            rows.forEach((row, idx) => {
                if (type === 'single') {
                    row.querySelector('input[type="radio"]').value = idx;
                } else if (type === 'multiple') {
                    row.querySelector('input[type="checkbox"]').value = idx;
                } else if (type === 'ordering') {
                    row.querySelector('.order-num').innerText = idx + 1;
                }
            });
        }
    </script>
</body>
</html>