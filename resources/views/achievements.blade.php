<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pencapaian - Quiz Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .locked { filter: grayscale(100%) opacity(50%); }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen p-4 font-sans">
    <div class="max-w-5xl mx-auto">

        <div class="text-center mb-10 mt-8">
            <h1 class="text-4xl font-bold mb-2 text-yellow-400">
                <i class="fas fa-medal"></i> Ruang Piala
            </h1>
            @if($playerName)
                <p class="text-slate-400">Koleksi milik <strong>{{ $playerName }}</strong></p>
            @else
                <p class="text-slate-400 text-sm bg-red-900/50 inline-block px-3 py-1 rounded">
                    Mainkan kuis dulu untuk membuka koleksi ini!
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($allBadges as $badge)
                @php
                    $isUnlocked = in_array($badge->id, $myBadgeIds);
                @endphp

                <div class="glass rounded-2xl p-6 text-center relative overflow-hidden group hover:bg-slate-800/80 transition {{ $isUnlocked ? '' : 'locked' }}">

                    <div class="w-20 h-20 mx-auto rounded-full bg-slate-800 flex items-center justify-center mb-4 text-3xl shadow-lg border-2 {{ $isUnlocked ? 'border-yellow-500' : 'border-slate-600' }}">
                        <i class="{{ $badge->icon_class }} {{ $badge->color_class }}"></i>
                    </div>

                    <h3 class="text-xl font-bold mb-2">{{ $badge->name }}</h3>
                    <p class="text-xs text-slate-400 h-10">{{ $badge->description }}</p>

                    <div class="mt-4">
                        @if($isUnlocked)
                            <span class="bg-green-500/20 text-green-400 text-xs font-bold px-3 py-1 rounded-full border border-green-500/50">
                                TERBUKA
                            </span>
                        @else
                            <span class="bg-slate-700 text-slate-500 text-xs font-bold px-3 py-1 rounded-full">
                                TERKUNCI
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 px-8 py-3 rounded-full font-bold transition">
                <i class="fas fa-home"></i> Kembali ke Menu
            </a>
        </div>
    </div>
</body>
</html>