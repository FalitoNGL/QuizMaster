<div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-md border border-gray-200 dark:border-slate-700 flex items-center justify-between group hover:border-pink-500 transition">
    <a href="{{ route('profile.show', $user->id) }}" class="flex items-center gap-3">
        <img src="{{ $user->avatar ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$user->name }}" class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-700 border border-gray-300 dark:border-gray-600">
        <div>
            <div class="font-bold text-slate-800 dark:text-white group-hover:text-pink-500 transition">{{ $user->name }}</div>
            <div class="text-xs text-slate-500">{{ $user->title ?? 'Pemain' }}</div>
        </div>
    </a>
    
    <div class="flex gap-2">
        <button onclick="openDuelModal('{{ $user->id }}', '{{ $user->name }}')" class="w-8 h-8 rounded-full bg-yellow-100 dark:bg-yellow-500/20 text-yellow-600 dark:text-yellow-400 flex items-center justify-center hover:bg-yellow-500 hover:text-white transition shadow-sm" title="Tantang Duel">
            <i class="fas fa-bolt"></i>
        </button>

        <form action="{{ route('profile.follow', $user->id) }}" method="POST">
            @csrf
            @if(Auth::user()->isFollowing($user->id))
                <button class="w-8 h-8 rounded-full bg-gray-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition" title="Unfollow">
                    <i class="fas fa-user-minus"></i>
                </button>
            @else
                <button class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition" title="Follow">
                    <i class="fas fa-user-plus"></i>
                </button>
            @endif
        </form>
    </div>
</div>