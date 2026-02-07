<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sizning Natijangiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 md:p-6">

    @php
        $bestDetail = $result->details->sortByDesc('score_percentage')->first();
        $bestSectionName = $bestDetail ? strtolower($bestDetail->section->name) : '';

        $recommendation = [
            'text' => "Siz barcha yo'nalishlarda yaxshi natija ko'rsatdingiz!",
            'role' => "IT MUTAXASSISI",
            'color' => "bg-indigo-600",
            'icon' => "fa-rocket"
        ];

        if (str_contains($bestSectionName, 'front')) {
            $recommendation = [
                'text' => "Sizda vizual mantiq va kreativlik juda yuqori! Dizayn va kodni birlashtirish sizga oson kechadi.",
                'role' => "FRONTEND DASTURCHI",
                'color' => "bg-emerald-600",
                'icon' => "fa-code"
            ];
        } elseif (str_contains($bestSectionName, 'back')) {
            $recommendation = [
                'text' => "Sizda kuchli mantiq va algoritmlar bilan ishlash qobiliyati bor! Murakkab tizimlar yaratish sizga mos.",
                'role' => "BACKEND DASTURCHI",
                'color' => "bg-slate-900",
                'icon' => "fa-server"
            ];
        } elseif (str_contains($bestSectionName, 'iq') || str_contains($bestSectionName, 'mantiq')) {
             $recommendation = [
                'text' => "Sizning intellektual salohiyatingiz va muammolarni hal qilish qobiliyatingiz tahsinga sazovor!",
                'role' => "PROJECT MANAGER",
                'color' => "bg-orange-500",
                'icon' => "fa-brain"
            ];
        }
    @endphp

    <div class="bg-white w-full max-w-2xl rounded-[2rem] md:rounded-[3rem] shadow-2xl p-6 md:p-12 text-center border border-white relative overflow-hidden">

        <div class="w-16 h-16 md:w-20 md:h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-2xl md:text-3xl mx-auto mb-6 shadow-lg shadow-emerald-100">
            <i class="fa-solid fa-check-double"></i>
        </div>

        <h1 class="text-2xl md:text-3xl font-black text-slate-900 mb-2">Tabriklaymiz, {{ explode(' ', $result->student_name)[0] }}!</h1>
        <p class="text-slate-400 font-medium text-sm md:text-base mb-8 md:mb-10">Siz testni muvaffaqiyatli yakunladingiz.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-10 items-end">
            @foreach($result->details as $detail)
                @php
                    $isBest = $bestDetail && $detail->id === $bestDetail->id;
                    // Telefonda scale bo'lmaydi, faqat kompyuterda (sm:scale-110)
                    $cardClass = $isBest
                        ? 'bg-emerald-50 border border-emerald-100 sm:transform sm:scale-110 shadow-xl shadow-emerald-100 z-10 order-first sm:order-none'
                        : 'bg-slate-50 border border-transparent opacity-90';
                    $textClass = $isBest ? 'text-emerald-600' : 'text-slate-400';
                    $scoreClass = $isBest ? 'text-emerald-700' : 'text-slate-900';
                @endphp

                <div class="p-5 md:p-6 rounded-2xl md:rounded-[2rem] transition-all duration-500 {{ $cardClass }}">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-1 {{ $textClass }}">
                        {{ $detail->section->name }}
                    </p>
                    <p class="text-xl md:text-2xl font-black {{ $scoreClass }}">
                        {{ $detail->correct_answers }} ta
                        <span class="text-xs block font-bold opacity-60">{{ $detail->score_percentage }}%</span>
                    </p>
                </div>
            @endforeach
        </div>

        <div class="{{ $recommendation['color'] }} rounded-2xl md:rounded-[2rem] p-6 md:p-8 text-white text-left relative overflow-hidden shadow-xl">
            <div class="relative z-10">
                <h4 class="text-base md:text-lg font-black mb-2 uppercase tracking-tighter opacity-90">Mutaxassis tavsiyasi:</h4>
                <p class="text-white/90 leading-relaxed font-medium text-xs md:text-sm">
                    {{ $recommendation['text'] }}
                    <br class="hidden md:block"> Natijalarga ko'ra siz
                    <span class="text-yellow-300 font-black italic underline uppercase">{{ $recommendation['role'] }}</span>
                    yo'nalishiga mos kelasiz.
                </p>
            </div>
            <i class="fa-solid {{ $recommendation['icon'] }} absolute right-[-10px] bottom-[-20px] text-8xl md:text-9xl text-white opacity-10 rotate-12"></i>
        </div>

        <div class="mt-8 md:mt-10">
            <a href="{{ url('/') }}" class="text-slate-400 font-bold hover:text-red-600 transition-all uppercase text-xs tracking-[0.3em]">
                Chiqish
            </a>
        </div>
    </div>

</body>
</html>
