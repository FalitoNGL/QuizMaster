<!DOCTYPE html>
<html lang="id">
<head>
    <title>Import Soal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen font-sans">
    
    <nav class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="font-bold text-xl text-yellow-500"><i class="fas fa-file-import"></i> Import Soal</div>
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-white px-3 py-1 rounded hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left mr-1"></i> Dashboard
        </a>
    </nav>

    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div>
                <div class="bg-gray-800 rounded-xl shadow-xl border border-gray-700 p-8">
                    <h2 class="text-2xl font-bold mb-6 text-blue-400">Upload File</h2>

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
                        </div>

                        <div class="mb-8">
                            <label class="block text-gray-400 mb-2 font-bold">File (JSON atau Excel)</label>
                            <div class="border-2 border-dashed border-gray-600 rounded-xl p-8 text-center hover:border-blue-500 transition cursor-pointer relative">
                                <input type="file" name="file_import" accept=".json,application/json,.xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-500 mb-3"></i>
                                <p class="text-gray-300">Upload file <b>.JSON</b> atau <b>.XLSX (Excel)</b></p>
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
                    <h3 class="text-xl font-bold mb-4 text-yellow-500"><i class="fas fa-info-circle"></i> Struktur File</h3>
                    
                    <div class="mb-6">
                        <h4 class="font-bold text-green-400 mb-2 text-sm uppercase">Format Excel (.xlsx)</h4>
                        <div class="bg-gray-900 p-3 rounded border border-gray-700 overflow-x-auto">
                            <table class="w-full text-xs text-gray-300">
                                <thead class="bg-gray-700 text-white font-bold">
                                    <tr>
                                        <td class="p-2 border border-gray-600">question</td>
                                        <td class="p-2 border border-gray-600">option_a</td>
                                        <td class="p-2 border border-gray-600">option_b</td>
                                        <td class="p-2 border border-gray-600">correct</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="p-2 border border-gray-600">Ibukota Jabar?</td>
                                        <td class="p-2 border border-gray-600">Bandung</td>
                                        <td class="p-2 border border-gray-600">Jakarta</td>
                                        <td class="p-2 border border-gray-600">A</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">*Kolom opsional: option_c, option_d, explanation, reference.</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-blue-400 mb-2 text-sm uppercase">Format JSON (.json)</h4>
                        <div class="bg-gray-900 p-4 rounded border border-gray-700 overflow-x-auto max-h-40">
<pre class="text-xs text-blue-300 font-mono">
[
  {
    "question": "Soal Pilihan Ganda?",
    "options": ["A", "B", "C", "D"],
    "correct": 0
  },
  {
    "question": "Soal Multiple?",
    "type": "multiple",
    "options": ["Benar", "Salah", "Benar"],
    "correct": [0, 2]
  }
]
</pre>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</body>
</html>