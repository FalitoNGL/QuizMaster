@extends('layouts.admin')

@section('title', 'Tambah Soal Baru')
@section('header_title', 'Pembuat Soal')
@section('header_subtitle', 'Gunakan form ini untuk menyusun soal kuis secara presisi.')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-xl font-extrabold text-white flex items-center gap-3">
            <i class="fas fa-magic text-vibrant"></i>
            Konfigurasi Soal Baru
        </h3>
        <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-white font-bold text-sm transition flex items-center gap-2">
            <i class="fas fa-times-circle"></i> Batal & Kembali
        </a>
    </div>

    <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Side: Basic Info -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Question Text -->
                <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 shadow-xl">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4 ml-1">Pertanyaan Utama</label>
                    <textarea name="question_text" rows="4" 
                        class="w-full bg-slate-900/50 border border-white/5 rounded-2xl px-6 py-5 text-white placeholder-slate-600 focus:outline-none focus:border-vibrant/50 transition text-lg font-bold leading-relaxed" 
                        required placeholder="Apa yang ingin Anda tanyakan?"></textarea>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Media Gambar (Baru)</label>
                            <div class="bg-slate-900/50 rounded-2xl p-4 border border-white/5">
                                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-vibrant/20 file:text-vibrant hover:file:bg-vibrant/30">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Media Audio (Baru)</label>
                            <div class="bg-slate-900/50 rounded-2xl p-4 border border-white/5">
                                <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-blue-500/20 file:text-blue-400 hover:file:bg-blue-500/30">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answer Options -->
                <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 shadow-2xl relative overflow-hidden">
                    <div class="flex justify-between items-center mb-8">
                        <h4 class="font-extrabold text-white flex items-center gap-3">
                            <i class="fas fa-list-check text-emerald-400"></i>
                            Struktur Jawaban
                        </h4>
                        <div id="type-badge" class="px-3 py-1 bg-white/5 rounded-lg text-[10px] font-black text-vibrant uppercase border border-vibrant/20 italic tracking-tighter">Pilihan Ganda</div>
                    </div>

                    <div class="space-y-4">
                        <!-- Dynamic Section: Single Choice -->
                        <div id="section-single" class="space-y-4">
                            @for($i=0; $i<4; $i++)
                            <div class="flex items-center gap-4 group">
                                <input type="radio" name="correct_single" value="{{ $i }}" {{ $i==0 ? 'checked' : '' }} class="w-6 h-6 accent-emerald-500 cursor-pointer">
                                <input type="text" name="options_single[]" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-700 focus:outline-none focus:border-emerald-500/30 transition group-hover:bg-slate-900/60" placeholder="Pilihan Ke-{{ $i+1 }}..." {{ $i<2 ? 'required' : '' }}>
                            </div>
                            @endfor
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mt-4 flex items-center gap-2"><i class="fas fa-info-circle"></i> Pilih radio button sebagai kunci jawaban benar.</p>
                        </div>

                        <!-- Dynamic Section: Multiple Choice -->
                        <div id="section-multiple" class="hidden space-y-4">
                            @for($i=0; $i<4; $i++)
                            <div class="flex items-center gap-4 group">
                                <input type="checkbox" name="correct_multiple[]" value="{{ $i }}" class="w-6 h-6 accent-blue-500 cursor-pointer rounded">
                                <input type="text" name="options_multiple[]" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-700 focus:outline-none focus:border-blue-500/30 transition" placeholder="Opsi Ke-{{ $i+1 }}...">
                            </div>
                            @endfor
                            <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mt-4 flex items-center gap-2"><i class="fas fa-info-circle"></i> Klik centang pada semua jawaban yang benar.</p>
                        </div>

                        <!-- Dynamic Section: Ordering -->
                        <div id="section-ordering" class="hidden space-y-4">
                            <div class="p-4 bg-orange-500/5 border border-orange-500/20 rounded-2xl text-[11px] text-orange-400 font-bold mb-4 italic">
                                Masukkan urutan yang BENAR dari atas ke bawah. Sistem akan mengacaknya secara otomatis saat permainan berlangsung.
                            </div>
                            @for($i=0; $i<4; $i++)
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-white/5 flex items-center justify-center font-black text-orange-500 text-sm italic">{{ $i+1 }}</div>
                                <input type="text" name="options_ordering[]" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-700 focus:border-orange-500/30 transition" placeholder="Urutan {{ $i+1 }}...">
                            </div>
                            @endfor
                        </div>

                        <!-- Dynamic Section: Matching -->
                        <div id="section-matching" class="hidden space-y-4">
                            <div class="p-4 bg-purple-500/5 border border-purple-500/20 rounded-2xl text-[11px] text-purple-400 font-bold mb-4 italic">
                                Pasangkan Kiri (Soal) dengan Kanan (Jawaban) yang benar. Sistem akan menyediakan dalam format interaktif acak.
                            </div>
                            @for($i=0; $i<4; $i++)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="options_matching_left[]" class="bg-slate-900/40 border border-white/5 rounded-2xl px-5 py-4 text-white placeholder-slate-700 focus:border-purple-500/30 transition" placeholder="Sisi Kiri (Tanya)">
                                <input type="text" name="options_matching_right[]" class="bg-slate-900/40 border border-white/5 rounded-2xl px-5 py-4 text-white placeholder-slate-700 focus:border-purple-500/30 transition shadow-inner" placeholder="Sisi Kanan (Jawaban)">
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Explanation -->
                <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 shadow-xl">
                    <h4 class="font-extrabold text-white mb-6 flex items-center gap-3">
                        <i class="fas fa-lightbulb text-yellow-400"></i>
                        Edukasi & Sumber
                    </h4>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Penjelasan Detail (Muncul Setelah Menjawab)</label>
                            <textarea name="explanation" rows="2" 
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-600 focus:outline-none focus:border-vibrant/30 transition" 
                                placeholder="Mengapa jawaban ini benar? Berikan insight edukatif..."></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Referensi / Sumber (Opsional)</label>
                            <input type="text" name="reference" 
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-600 focus:border-vibrant/30 transition" 
                                placeholder="Contoh: Buku Biologi Hal 12 atau Link Artikel">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Configuration -->
            <div class="lg:col-span-4 space-y-8">
                <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 sticky top-28 space-y-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 ml-1">Target Kategori</label>
                        <select name="category_id" class="w-full bg-slate-900 border border-white/10 rounded-2xl px-5 py-4 text-white font-bold focus:border-vibrant/50 transition cursor-pointer">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 ml-1">Tipe Mekanisme</label>
                        <select name="type" id="type-select" onchange="changeType()" 
                            class="w-full bg-slate-900 border border-white/10 rounded-2xl px-5 py-4 text-white font-bold focus:border-vibrant/50 transition cursor-pointer">
                            <option value="single">Pilihan Ganda (Single)</option>
                            <option value="multiple">Pilihan Ganda (Multiple)</option>
                            <option value="ordering">Mengurutkan (Ordering)</option>
                            <option value="matching">Menjodohkan (Matching)</option>
                        </select>
                    </div>

                    <div class="pt-6 border-t border-white/5">
                        <button type="submit" 
                            class="w-full bg-vibrant hover:bg-vibrant/90 text-white font-black py-5 rounded-2xl uppercase tracking-widest shadow-2xl shadow-vibrant/20 transition transform hover:-translate-y-1 active:scale-95">
                            <i class="fas fa-save mr-2"></i> Publish Soal
                        </button>
                    </div>

                    <div class="p-4 bg-blue-500/5 border border-blue-500/10 rounded-2xl">
                        <p class="text-[10px] text-blue-400 font-bold leading-relaxed italic">
                            <i class="fas fa-shield-halved mr-1"></i> Data akan divalidasi oleh sistem sebelum disimpan ke bank soal utama.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function changeType() {
        const typeSelect = document.getElementById('type-select');
        const type = typeSelect.value;
        const textLabel = typeSelect.options[typeSelect.selectedIndex].text;
        
        // Update badge
        document.getElementById('type-badge').innerText = textLabel;

        // Toggle sections
        ['single', 'multiple', 'ordering', 'matching'].forEach(t => {
            const el = document.getElementById('section-' + t);
            if (t === type) {
                el.classList.remove('hidden');
                el.querySelectorAll('input').forEach(i => i.disabled = false);
            } else {
                el.classList.add('hidden');
                el.querySelectorAll('input').forEach(i => i.disabled = true);
            }
        });
    }
    // Initialize
    document.addEventListener('DOMContentLoaded', changeType);
</script>
@endsection