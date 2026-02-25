@extends('layouts.admin')

@section('title', 'Import Soal')
@section('header_title', 'Data Importer')
@section('header_subtitle', 'Migrasi data soal secara massal dari format JSON atau Excel.')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
    
    <!-- Upload Section -->
    <div class="lg:col-span-7">
        <div class="glass-card rounded-[2.5rem] p-10 border border-white/5 relative overflow-hidden shadow-2xl">
            <!-- Decorative Light -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>

            <h3 class="text-xl font-extrabold text-white mb-8 flex items-center gap-3">
                <i class="fas fa-file-upload text-blue-400"></i>
                Unggah File Sumber
            </h3>

            <form action="{{ route('admin.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Pilih Kategori Tujuan</label>
                    <div class="relative">
                        <select name="category_id" class="w-full bg-slate-900/50 border border-white/5 rounded-2xl px-6 py-4 text-white appearance-none focus:outline-none focus:border-blue-500/50 transition">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">File (JSON / XLSX / CSV)</label>
                    <div class="group relative border-2 border-dashed border-white/10 rounded-3xl p-12 text-center hover:border-blue-500/50 hover:bg-white/[0.02] transition cursor-pointer overflow-hidden">
                        <input type="file" name="file_import" accept=".json,application/json,.xlsx,.xls,.csv" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" required>
                        
                        <div class="relative z-10">
                            <div class="w-20 h-20 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center text-3xl mx-auto mb-4 group-hover:scale-110 group-hover:bg-blue-500/20 transition duration-300">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <h4 class="text-lg font-bold text-slate-300">Tarik berkas ke sini</h4>
                            <p class="text-xs text-slate-500 mt-2 font-medium uppercase tracking-wider">Mendukung format JSON dan Excel (.xlsx)</p>
                        </div>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-black py-4 rounded-2xl uppercase tracking-widest shadow-xl shadow-blue-900/20 transition transform hover:-translate-y-1 flex items-center justify-center gap-3">
                    <i class="fas fa-bolt"></i>
                    Eksekusi Import
                </button>
            </form>
        </div>
    </div>

    <!-- Guide Section -->
    <div class="lg:col-span-5">
        <div class="glass-card rounded-[2.5rem] p-10 border border-white/5 space-y-10">
            <div>
                <h3 class="text-lg font-extrabold text-vibrant mb-6 flex items-center gap-3 uppercase tracking-tighter">
                    <i class="fas fa-info-circle"></i>
                    Panduan Struktur
                </h3>
                
                <div class="space-y-8">
                    <!-- Excel Format -->
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span>
                            <h4 class="font-black text-xs text-emerald-400 uppercase tracking-widest">Format Excel (.xlsx)</h4>
                        </div>
                        <div class="bg-slate-900/50 rounded-2xl border border-white/5 overflow-hidden">
                            <table class="w-full text-left text-[10px] font-bold">
                                <thead class="bg-white/5 text-slate-500 uppercase tracking-tighter">
                                    <tr>
                                        <th class="px-4 py-3">question</th>
                                        <th class="px-4 py-3">option_a</th>
                                        <th class="px-4 py-3">correct</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5 text-slate-400">
                                    <tr>
                                        <td class="px-4 py-3 italic">"Teks Soal..."</td>
                                        <td class="px-4 py-3 italic text-xs">Jawaban 1</td>
                                        <td class="px-4 py-3 text-emerald-500">A</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-[9px] text-slate-600 mt-3 font-bold uppercase tracking-tight">KOLOM OPSIONAL: option_c, option_d, explanation, reference.</p>
                    </div>

                    <!-- JSON Format -->
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>
                            <h4 class="font-black text-xs text-blue-400 uppercase tracking-widest">Format JSON (.json)</h4>
                        </div>
                        <div class="bg-slate-900 rounded-2xl border border-white/5 p-5">
                            <pre class="text-[11px] text-blue-300 font-mono leading-relaxed line-clamp-6 bg-transparent border-0 p-0">
[
  {
    "question": "Soal Pilihan Ganda?",
    "options": ["A", "B", "C", "D"],
    "correct": 0
  },
  {
    "question": "Soal Multiple?",
    "type": "multiple",
    "correct": [0, 2]
  }
]
                            </pre>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-vibrant/5 rounded-2xl border border-vibrant/20">
                <p class="text-xs text-vibrant/80 leading-relaxed font-bold italic">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Sistem akan otomatis mendeteksi soal duplikat dan melewati proses import pada soal yang sudah terdaftar di database.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection