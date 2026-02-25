@extends('layouts.admin')

@section('title', 'Edit Soal')
@section('header_title', 'Editor Soal')
@section('header_subtitle', 'Modifikasi soal yang sudah ada untuk menjaga kualitas konten.')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-xl font-extrabold text-white flex items-center gap-3">
            <i class="fas fa-edit text-blue-400"></i>
            Update Materi Kuis
        </h3>
        <span class="px-4 py-1 bg-white/5 rounded-full text-[10px] font-black text-slate-500 uppercase italic border border-white/5">Question ID: #{{ $question->id }}</span>
    </div>

    <form action="{{ route('admin.update', $question->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left Side -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Question Text -->
                <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 shadow-xl transition hover:bg-white/[0.03]">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4 ml-1">Pertanyaan</label>
                    <textarea name="question_text" rows="4" 
                        class="w-full bg-slate-900/50 border border-white/5 rounded-2xl px-6 py-5 text-white placeholder-slate-600 focus:outline-none focus:border-vibrant/50 transition text-lg font-bold leading-relaxed" 
                        required>{{ $question->question_text }}</textarea>
                    
                    <!-- Media Manager -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <div class="bg-slate-900/30 p-6 rounded-3xl border border-white/5 space-y-4">
                            <label class="block text-[10px] font-black text-blue-400 uppercase tracking-widest">Manajemen Gambar</label>
                            @if($question->image_path)
                                <div class="relative group w-full aspect-video rounded-2xl overflow-hidden border border-white/10 shadow-lg">
                                    <img src="{{ asset('storage/' . $question->image_path) }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <label class="flex items-center gap-2 text-rose-400 font-black text-xs cursor-pointer bg-black/40 px-4 py-2 rounded-full border border-rose-500/30">
                                            <input type="checkbox" name="remove_image" value="1" class="w-4 h-4 accent-rose-500"> Hapus Gambar
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-blue-600/20 file:text-blue-400">
                        </div>

                        <div class="bg-slate-900/30 p-6 rounded-3xl border border-white/5 space-y-4">
                            <label class="block text-[10px] font-black text-purple-400 uppercase tracking-widest">Manajemen Audio</label>
                            @if($question->audio_path)
                                <div class="p-3 bg-white/5 rounded-2xl border border-white/10">
                                    <audio controls src="{{ asset('storage/' . $question->audio_path) }}" class="w-full h-8 mb-3"></audio>
                                    <label class="flex items-center gap-2 text-rose-400 font-black text-xs cursor-pointer">
                                        <input type="checkbox" name="remove_audio" value="1" class="w-4 h-4 accent-rose-500"> Tandai untuk Hapus
                                    </label>
                                </div>
                            @endif
                            <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-purple-600/20 file:text-purple-400">
                        </div>
                    </div>
                </div>

                <!-- Answer Options -->
                <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 shadow-2xl overflow-hidden">
                    <div class="flex justify-between items-center mb-8">
                        <h4 class="font-extrabold text-white flex items-center gap-3">
                            <i class="fas fa-list-check text-vibrant"></i>
                            Struktur Jawaban ({{ strtoupper($question->type) }})
                        </h4>
                        <button type="button" onclick="addOption()" class="px-3 py-1 bg-vibrant/10 hover:bg-vibrant/20 text-vibrant text-[10px] font-black rounded-lg border border-vibrant/20 transition uppercase">
                            <i class="fas fa-plus mr-1"></i> Tambah Opsi
                        </button>
                    </div>

                    <div id="options-wrapper" class="space-y-4">
                        @if($question->type == 'single')
                            @foreach($question->options as $index => $opt)
                            <div class="flex items-center gap-4 group option-row">
                                <input type="radio" name="correct_single" value="{{ $index }}" class="w-6 h-6 accent-vibrant cursor-pointer" {{ $opt->is_correct ? 'checked' : '' }} required>
                                <input type="text" name="options_single[]" value="{{ $opt->option_text }}" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white hover:bg-slate-900/60 transition" required>
                                <button type="button" onclick="removeOption(this)" class="text-slate-600 hover:text-rose-500 px-2 transition"><i class="fas fa-trash-alt"></i></button>
                            </div>
                            @endforeach

                        @elseif($question->type == 'multiple')
                            @foreach($question->options as $index => $opt)
                            <div class="flex items-center gap-4 group option-row">
                                <input type="checkbox" name="correct_multiple[]" value="{{ $index }}" class="w-6 h-6 accent-blue-500 cursor-pointer rounded" {{ $opt->is_correct ? 'checked' : '' }}>
                                <input type="text" name="options_multiple[]" value="{{ $opt->option_text }}" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white hover:bg-slate-900/60 transition" required>
                                <button type="button" onclick="removeOption(this)" class="text-slate-600 hover:text-rose-500 px-2 transition"><i class="fas fa-trash-alt"></i></button>
                            </div>
                            @endforeach

                        @elseif($question->type == 'ordering')
                            @php $sortedOptions = $question->options->sortBy('correct_order'); @endphp
                            @foreach($sortedOptions as $index => $opt)
                            <div class="flex items-center gap-4 group option-row">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-white/5 flex items-center justify-center font-black text-orange-500 text-sm order-num">{{ $index + 1 }}</div>
                                <input type="text" name="options_ordering[]" value="{{ $opt->option_text }}" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white hover:bg-slate-900/60 transition" required>
                                <button type="button" onclick="removeOption(this)" class="text-slate-600 hover:text-rose-500 px-2 transition"><i class="fas fa-trash-alt"></i></button>
                            </div>
                            @endforeach

                        @elseif($question->type == 'matching')
                            @foreach($question->options as $index => $opt)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 group option-row relative">
                                <input type="text" name="options_matching_left[]" value="{{ $opt->option_text }}" class="bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white hover:bg-slate-900/60 transition" required>
                                <div class="relative">
                                    <input type="text" name="options_matching_right[]" value="{{ $opt->matching_pair }}" class="w-full bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white hover:bg-slate-900/60 transition" required>
                                    <button type="button" onclick="removeOption(this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-rose-500 transition"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                            @endforeach
                        @endif
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
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Pembahasan Update</label>
                            <textarea name="explanation" rows="2" 
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl px-6 py-4 text-white focus:outline-none focus:border-vibrant/30 transition shadow-inner">{{ $question->explanation }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Refrensi Terkait</label>
                            <input type="text" name="reference" value="{{ $question->reference }}" 
                                class="w-full bg-slate-900/50 border border-white/5 rounded-2xl px-6 py-4 text-white focus:border-vibrant/30 transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="lg:col-span-4 space-y-8">
                <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 sticky top-28 space-y-10">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4 ml-1">Target Kategori</label>
                        <select name="category_id" class="w-full bg-slate-900 border border-white/10 rounded-2xl px-5 py-4 text-white font-bold focus:border-vibrant/50 transition">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == $question->category_id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-6 bg-blue-500/5 border border-blue-500/20 rounded-2xl text-[11px] text-blue-400 font-bold italic leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i> Perubahan tipe soal tidak diizinkan di halaman edit untuk menjaga integritas data relasional.
                    </div>

                    <div class="pt-6 border-t border-white/5 space-y-4">
                        <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-5 rounded-2xl uppercase tracking-widest shadow-2xl shadow-blue-900/20 transition transform hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> Update Soal
                        </button>
                        <a href="{{ route('admin.dashboard') }}" 
                            class="block w-full text-center bg-white/5 hover:bg-white/10 text-slate-400 font-bold py-4 rounded-2xl text-xs transition uppercase tracking-widest border border-white/5">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const type = "{{ $question->type }}";
    const wrapper = document.getElementById('options-wrapper');

    function addOption() {
        const count = wrapper.querySelectorAll('.option-row').length;
        const div = document.createElement('div');
        div.className = (type === 'matching') ? 'grid grid-cols-1 md:grid-cols-2 gap-4 group option-row relative mb-4 animate-fade-in' : 'flex items-center gap-4 group option-row mb-4 animate-fade-in';

        if (type === 'single') {
            div.innerHTML = `
                <input type="radio" name="correct_single" value="${count}" class="w-6 h-6 accent-vibrant">
                <input type="text" name="options_single[]" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-800" placeholder="Jawaban Baru..." required>
                <button type="button" onclick="removeOption(this)" class="text-slate-600 hover:text-rose-500 px-2 transition"><i class="fas fa-trash-alt"></i></button>
            `;
        } else if (type === 'multiple') {
            div.innerHTML = `
                <input type="checkbox" name="correct_multiple[]" value="${count}" class="w-6 h-6 accent-blue-500 rounded">
                <input type="text" name="options_multiple[]" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-800" placeholder="Jawaban Baru..." required>
                <button type="button" onclick="removeOption(this)" class="text-slate-600 hover:text-rose-500 px-2 transition"><i class="fas fa-trash-alt"></i></button>
            `;
        } else if (type === 'ordering') {
            div.innerHTML = `
                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-white/5 flex items-center justify-center font-black text-orange-500 text-sm order-num">${count + 1}</div>
                <input type="text" name="options_ordering[]" class="flex-grow bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-800" placeholder="Urutan Baru..." required>
                <button type="button" onclick="removeOption(this)" class="text-slate-600 hover:text-rose-500 px-2 transition"><i class="fas fa-trash-alt"></i></button>
            `;
        } else if (type === 'matching') {
            div.innerHTML = `
                <input type="text" name="options_matching_left[]" class="bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-800" placeholder="Soal Baru..." required>
                <div class="relative">
                    <input type="text" name="options_matching_right[]" class="w-full bg-slate-900/40 border border-white/5 rounded-2xl px-6 py-4 text-white placeholder-slate-800" placeholder="Jawaban Baru..." required>
                    <button type="button" onclick="removeOption(this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-600 hover:text-rose-500 transition"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
        }
        wrapper.appendChild(div);
    }

    function removeOption(btn) {
        if(confirm('Hapus opsi ini?')) {
            btn.closest('.option-row').remove();
            reindex();
        }
    }

    function reindex() {
        const rows = wrapper.querySelectorAll('.option-row');
        rows.forEach((row, idx) => {
            if (type === 'single') row.querySelector('input[type="radio"]').value = idx;
            if (type === 'multiple') row.querySelector('input[type="checkbox"]').value = idx;
            if (type === 'ordering') row.querySelector('.order-num').innerText = idx + 1;
        });
    }
</script>
@endsection