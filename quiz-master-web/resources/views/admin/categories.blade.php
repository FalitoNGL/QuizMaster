@extends('layouts.admin')

@section('title', 'Kelola Kategori')
@section('header_title', 'Master Kategori')
@section('header_subtitle', 'Kelola topik kuis dan ikonografi untuk pengalaman pengguna yang kaya.')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
    
    <!-- Category List -->
    <div class="xl:col-span-2 space-y-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-3">
                <i class="fas fa-list-ul text-vibrant"></i>
                Daftar Kategori Aktif
            </h3>
            <span class="px-4 py-1 bg-white/5 rounded-full text-[10px] font-black text-slate-500 uppercase">{{ count($categories) }} Total</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($categories as $cat)
            <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 relative group transition-all duration-500 hover:scale-[1.02] hover:bg-white/[0.05] overflow-hidden shadow-2xl">
                <!-- Premium Background Glow -->
                <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-vibrant/5 rounded-full blur-[80px] group-hover:bg-vibrant/20 transition-all duration-700"></div>

                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-5">
                         <!-- Icon Container - SECURE VIBRANCY -->
                        <div class="w-16 h-16 rounded-[1.25rem] bg-vibrant flex items-center justify-center text-3xl text-white shadow-lg shadow-vibrant/40 group-hover:rotate-6 transition-transform">
                            <i class="{{ $cat->fa_icon }}"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xl text-white group-hover:text-vibrant transition-colors">{{ $cat->name }}</h4>
                            <p class="text-[10px] font-mono text-slate-400 uppercase tracking-widest mt-1">{{ $cat->slug }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 relative z-10">
                    <p class="text-sm text-slate-400 line-clamp-2 leading-relaxed mb-8 opacity-80">{{ $cat->description }}</p>
                    
                    <div class="flex items-center justify-between pt-6 border-t border-white/10">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-vibrant">
                                <i class="fas fa-layer-group text-[10px]"></i>
                            </div>
                            <span class="text-[11px] font-black text-slate-300 uppercase tracking-widest">{{ $cat->questions_count }} Soal Terdaftar</span>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2">
                            <button onclick="openEditModal({{ $cat->id }})" 
                                class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all shadow-lg hover:shadow-blue-500/20">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <a href="{{ route('admin.categories.delete', $cat->id) }}" 
                               onclick="return confirm('PERINGATAN: Menghapus kategori ini akan menghapus semua soal di dalamnya!')"
                               class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-lg hover:shadow-rose-500/20">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="md:col-span-2 glass-card rounded-[2.5rem] p-24 text-center border-dashed border-2 border-white/10 opacity-40">
                <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-folder-open text-4xl text-slate-500"></i>
                </div>
                <p class="font-black uppercase tracking-[0.4em] text-slate-400">Belum ada kategori aktif</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Create Form -->
    <div class="xl:col-span-1">
        <div class="glass-card rounded-[2.5rem] p-10 border border-white/10 sticky top-28 shadow-2xl overflow-hidden group">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all duration-700"></div>

            <h3 class="text-2xl font-black text-white mb-10 flex items-center gap-4">
                <i class="fas fa-plus-circle text-emerald-500 shadow-xl shadow-emerald-500/20"></i>
                Kategori Baru
            </h3>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-emerald-500 uppercase tracking-widest ml-1">Nama Kategori</label>
                    <input type="text" name="name" id="name" oninput="generateSlug('name', 'slug')" 
                        class="w-full bg-slate-900/80 border border-white/10 rounded-2xl px-6 py-5 text-white placeholder-slate-700 focus:outline-none focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 transition-all text-sm font-bold" 
                        required placeholder="Misal: Teknologi Masa Depan">
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Slug URL (Otomatis)</label>
                    <input type="text" name="slug" id="slug" 
                        class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-slate-600 font-mono text-xs cursor-not-allowed italic" 
                        readonly required>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-emerald-500 uppercase tracking-widest ml-1">Deskripsi Strategis</label>
                    <textarea name="description" rows="3" 
                        class="w-full bg-slate-900/80 border border-white/10 rounded-2xl px-6 py-5 text-white placeholder-slate-700 focus:outline-none focus:border-emerald-500/50 focus:ring-4 focus:ring-emerald-500/5 transition-all text-sm font-medium leading-relaxed" 
                        required placeholder="Jelaskan cakupan topik kuis ini..."></textarea>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-emerald-500 uppercase tracking-widest ml-1">Simbol Ikonografi</label>
                    <div class="grid grid-cols-5 gap-3 p-5 bg-slate-950/50 rounded-3xl border border-white/5 shadow-inner">
                        @php
                            $icons = [
                                'fas fa-book', 'fas fa-flask', 'fas fa-globe-asia', 'fas fa-calculator', 'fas fa-laptop-code', 
                                'fas fa-music', 'fas fa-palette', 'fas fa-running', 'fas fa-brain', 'fas fa-heartbeat',
                                'fas fa-comments', 'fas fa-history', 'fas fa-coins', 'fas fa-gavel', 'fas fa-tree'
                            ];
                        @endphp
                        @foreach($icons as $icon)
                        <label class="cursor-pointer group/icon">
                            <input type="radio" name="icon_class" value="{{ $icon }}" class="peer sr-only" {{ $loop->first ? 'checked' : '' }}>
                            <div class="w-11 h-11 rounded-2xl bg-slate-900 flex items-center justify-center text-slate-600 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:shadow-xl peer-checked:shadow-emerald-500/30 peer-checked:scale-110 hover:bg-slate-800 transition-all duration-300">
                                <i class="{{ $icon }} text-xs group-hover/icon:scale-110 transition"></i>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black py-5 rounded-[1.5rem] uppercase tracking-widest shadow-2xl shadow-emerald-900/30 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-4">
                    <i class="fas fa-save shadow-lg"></i>
                    Simpan Kategori
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-6 bg-slate-950/80 backdrop-blur-xl animate-fade-in">
    <div class="glass-card rounded-[3rem] p-10 max-w-xl w-full border border-white/20 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-60 h-60 bg-blue-500/10 rounded-full blur-[100px]"></div>
        
        <h3 class="text-3xl font-black text-white mb-10 flex items-center gap-4">
            <i class="fas fa-edit text-blue-400"></i>
            Edit Kategori
        </h3>

        <form id="editForm" method="POST" class="space-y-8">
            @csrf
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-widest ml-1">Nama Kategori</label>
                <input type="text" name="name" id="edit_name" oninput="generateSlug('edit_name', 'edit_slug')" 
                    class="w-full bg-slate-900 border border-white/10 rounded-2xl px-6 py-5 text-white focus:outline-none focus:border-blue-500/50 transition-all font-bold" required>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Slug URL (Otomatis)</label>
                <input type="text" name="slug" id="edit_slug" class="w-full bg-slate-950 border border-white/5 rounded-2xl px-6 py-5 text-slate-600 font-mono text-xs" readonly>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-widest ml-1">Deskripsi</label>
                <textarea name="description" id="edit_description" rows="3" 
                    class="w-full bg-slate-900 border border-white/10 rounded-2xl px-6 py-5 text-white focus:outline-none focus:border-blue-500/50 transition-all font-medium leading-relaxed" required></textarea>
            </div>

            <div class="space-y-4">
                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-widest ml-1">Pilih Ikon</label>
                <div class="grid grid-cols-5 gap-3 p-5 bg-slate-950/50 rounded-3xl border border-white/5">
                    @foreach($icons as $icon)
                    <label class="cursor-pointer group/icon">
                        <input type="radio" name="icon_class" value="{{ $icon }}" class="peer sr-only edit-icon-radio">
                        <div class="w-11 h-11 rounded-2xl bg-slate-900 flex items-center justify-center text-slate-600 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:shadow-xl peer-checked:shadow-blue-500/30 peer-checked:scale-110 hover:bg-slate-800 transition-all duration-300">
                            <i class="{{ $icon }} text-xs group-hover/icon:scale-110 transition"></i>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4">
                <button type="button" onclick="closeEditModal()" class="w-full py-5 bg-white/5 hover:bg-white/10 text-slate-400 font-black rounded-2xl uppercase tracking-widest transition-all">Batal</button>
                <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl uppercase tracking-widest shadow-xl shadow-blue-500/20 transition-all transform hover:-translate-y-1">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function generateSlug(sourceId, targetId) {
        const name = document.getElementById(sourceId).value;
        const slug = name.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById(targetId).value = slug;
    }

    async function openEditModal(id) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        // Fetch data
        try {
            const response = await fetch(`/admin/categories/edit/${id}`);
            const data = await response.json();
            
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_slug').value = data.slug;
            document.getElementById('edit_description').value = data.description;
            
            // Set Radio
            document.querySelectorAll('.edit-icon-radio').forEach(radio => {
                if (radio.value === data.icon_class) radio.checked = true;
            });

            form.action = `/admin/categories/update/${id}`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } catch (error) {
            alert('Gagal mengambil data kategori.');
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
</style>
@endsection