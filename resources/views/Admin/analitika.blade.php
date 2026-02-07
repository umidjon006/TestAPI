<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTLive - Yo'nalishlar Tahlili</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-gradient { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-80 sidebar-gradient text-slate-300 flex flex-col justify-between shadow-2xl z-20">
        <div>
            <div class="p-10 border-b border-slate-700/50 mb-6">
                <h1 class="text-5xl font-extrabold tracking-tighter flex items-center group">
                    <span class="text-white">iT</span><span class="text-red-600">Live</span>
                </h1>
                <p class="text-[10px] text-slate-500 uppercase tracking-[0.3em] mt-2 font-bold italic">Academy</p>
            </div>
            <nav class="px-6 space-y-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-4 p-4 hover:bg-slate-800 rounded-xl transition-all group">
                    <i class="fa-solid fa-layer-group text-xl group-hover:text-red-500"></i>
                    <span class="font-semibold text-sm uppercase tracking-widest">Imtihonlar</span>
                </a>
                <a href="{{ route('admin.results.index') }}" class="flex items-center space-x-4 p-4 bg-red-600/10 border-l-4 border-red-600 text-white rounded-r-xl transition-all">
                    <i class="fa-solid fa-chart-pie text-xl text-red-600"></i>
                    <span class="font-bold text-sm uppercase tracking-widest">Natijalar</span>
                </a>
            </nav>
        </div>
        <div class="p-8">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-3 p-4 bg-red-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-red-700 transition-all cursor-pointer">
                    <i class="fa-solid fa-power-off"></i> <span>Chiqish</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto bg-[#f8fafc]">
        <header class="py-10 px-12">
            <nav class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-2">Natijalar / Analitika</nav>
            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Yo'nalishlar bo'yicha tahlil</h2>
        </header>

        <section class="px-12 pb-12 grid grid-cols-1 md:grid-cols-3 gap-8">

            @forelse($analytics as $index => $item)
                @php
                    $color = $item['color']; // emerald, indigo, orange...
                    // Tailwind klasslarini dinamik yasash uchun oldindan o'zgaruvchi olamiz
                    $borderClass = "border-{$color}-500";
                    $shadowClass = "shadow-{$color}-100/50";
                    $bgIconClass = "bg-{$color}-50";
                    $textIconClass = "text-{$color}-600";
                    $bgBarClass = "bg-{$color}-500";
                @endphp

                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl {{ $shadowClass }} border-t-8 {{ $borderClass }}">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-4 {{ $bgIconClass }} rounded-2xl {{ $textIconClass }} text-2xl">
                            <i class="fa-solid {{ $item['icon'] }}"></i>
                        </div>

                        @if($index === 0)
                            <span class="{{ $textIconClass }} font-black text-xl">Top #1</span>
                        @endif
                    </div>

                    <h3 class="text-2xl font-black text-slate-800 mb-2">{{ $item['name'] }}</h3>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-6">Umumiy ballar yig'indisi</p>

                    <div class="text-5xl font-black text-slate-900 mb-4">{{ number_format($item['score']) }}</div>

                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                        <div class="{{ $bgBarClass }} h-full" style="width: {{ $item['percent'] }}%"></div>
                    </div>

                    @if($index === 0)
                    <p class="mt-4 text-[11px] text-slate-500 font-bold italic">
                        Ushbu yo'nalish bo'yicha eng yuqori natijalar qayd etilmoqda ({{ $item['percent'] }}%).
                    </p>
                    @endif
                </div>
            @empty
                <div class="col-span-3 text-center p-10 text-slate-400 font-bold">
                    Hozircha ma'lumotlar yetarli emas.
                </div>
            @endforelse

        </section>

        <div class="px-12 flex justify-end">
            <a href="{{ route('admin.results.index') }}" class="bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold hover:bg-slate-800 transition-all shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Ortga qaytish
            </a>
        </div>
    </main>
</body>
</html>
