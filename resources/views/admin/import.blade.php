<!DOCTYPE html>
<html lang="id">
<head>
    <title>Import Soal JSON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen font-sans">
    
    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="font-bold text-xl text-yellow-500"><i class="fas fa-file-code"></i> Import JSON</div>
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-white px-3 py-1 rounded hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left mr-1"></i> Dashboard
        </a>
    </nav>

    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div>
                <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 p-8">
                    <h2 class="text-2xl font-bold mb-6 text-blue-400">Upload File JSON</h2>

                    @if(session('error'))
                        <div class="bg-red-500/20 text-red-300 p-4 rounded-lg mb-6 border border-red-500/30">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-gray-400 mb-2 font-bold">Masukan ke Kategori:</label>
                            <select name="category_id" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 focus:border-blue-500 outline-none text-white">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-2">Semua soal di file JSON akan dimasukkan ke kategori ini.</p>
                        </div>

                        <div class="mb-8">
                            <label class="block text-gray-400 mb-2 font-bold">File JSON</label>
                            <div class="border-2 border-dashed border-gray-600 rounded-xl p-8 text-center hover:border-blue-500 transition cursor-pointer relative">
                                <input type="file" name="json_file" accept=".json,application/json" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-500 mb-3"></i>
                                <p class="text-gray-300">Klik atau Seret file .json di sini</p>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:scale-105">
                            <i class="fas fa-upload mr-2"></i> Mulai Import
                        </button>
                    </form>
                </div>
            </div>

            <div>
                <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 p-8">
                    <h3 class="text-xl font-bold mb-4 text-yellow-500"><i class="fas fa-info-circle"></i> Struktur File JSON</h3>
                    <p class="text-gray-400 text-sm mb-4">Pastikan file JSON Anda mengikuti format array seperti di bawah ini:</p>
                    
                    <div class="bg-gray-900 p-4 rounded-lg border border-gray-700 overflow-x-auto">
<pre class="text-xs text-green-400 font-mono">
[
  {
    "question": "Siapa penemu bola lampu?",
    "options": [
      "Nikola Tesla",
      "Thomas Edison",
      "Alexander Graham Bell",
      "Isaac Newton"
    ],
    "correct": 1,
    "explanation": "Edison mematenkan bola lampu pada 1879.",
    "reference": "Buku Sejarah Hal 10"
  },
  {
    "question": "Manakah bahasa pemrograman web?",
    "type": "multiple",
    "options": ["HTML", "Python", "CSS", "C++"],
    "correct": [0, 2],
    "explanation": "HTML dan CSS adalah teknologi inti web."
  }
]
</pre>
                    </div>

                    <div class="mt-6 text-sm text-gray-400 space-y-2">
                        <p><span class="text-blue-400 font-bold">question</span> : Teks pertanyaan (Wajib).</p>
                        <p><span class="text-blue-400 font-bold">options</span> : Array pilihan jawaban (Wajib).</p>
                        <p><span class="text-blue-400 font-bold">correct</span> : Index jawaban benar (Mulai dari 0). Jika tipe 'multiple', gunakan array `[0, 2]`.</p>
                        <p><span class="text-blue-400 font-bold">type</span> : (Opsional) 'single', 'multiple', 'ordering', 'matching'. Default 'single'.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>