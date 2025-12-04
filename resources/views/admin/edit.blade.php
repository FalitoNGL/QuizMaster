<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Soal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen p-8 font-sans">
    <div class="max-w-2xl mx-auto bg-gray-800 rounded-xl shadow-2xl border border-gray-700 p-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-500">Edit Soal</h1>

        <form action="{{ route('admin.update', $question->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-400 mb-2">Kategori</label>
                <select name="category_id" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $cat->id == $question->category_id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-400 mb-2">Pertanyaan</label>
                <textarea name="question_text" rows="3" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white" required>{{ $question->question_text }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-400 mb-2">Penjelasan / Pembahasan</label>
                <textarea name="explanation" rows="2" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">{{ $question->explanation }}</textarea>
            </div>
            <div class="mb-6">
                <label class="block text-gray-400 mb-2">Referensi</label>
                <input type="text" name="reference" value="{{ $question->reference }}" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-3 text-white">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Ganti Gambar</label>
                    <input type="file" name="image" class="text-sm text-gray-300">
                    @if($question->image_path) <p class="text-xs text-green-400 mt-1">Gambar terpasang</p> @endif
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Ganti Audio</label>
                    <input type="file" name="audio" class="text-sm text-gray-300">
                    @if($question->audio_path) <p class="text-xs text-green-400 mt-1">Audio terpasang</p> @endif
                </div>
            </div>

            @if($question->type == 'single')
                <div class="mb-8">
                    <label class="block text-gray-400 mb-4">Pilihan Jawaban</label>
                    @foreach($question->options as $index => $opt)
                    <div class="flex items-center gap-3 mb-3">
                        <input type="radio" name="correct_index" value="{{ $index }}" class="w-5 h-5 accent-green-500" {{ $opt->is_correct ? 'checked' : '' }}>
                        <input type="text" name="options[]" value="{{ $opt->option_text }}" class="w-full bg-gray-700 border border-gray-600 rounded px-4 py-2 text-white" required>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="mb-8 p-4 bg-yellow-900/20 border border-yellow-700 rounded text-yellow-500 text-sm">
                    Maaf, mode edit untuk tipe soal kompleks ({{ $question->type }}) belum tersedia di versi ini. Silakan hapus dan buat baru jika ingin mengubah struktur.
                </div>
            @endif

            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-bold">Batal</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold">Update Soal</button>
            </div>
        </form>
    </div>
</body>
</html>