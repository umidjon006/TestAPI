<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $test->title }} - Imtihon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .question-card { transition: all 0.3s ease; border: 2px solid transparent; }
        .question-card:hover { border-color: #f1f5f9; }
        .custom-radio:checked + label { border-color: #ef4444; background-color: #fef2f2; }
        .sticky-header { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9); }
        /* Javob belgilanmagan kartani qizil qilish uchun klass */
        .unanswered-card { border-color: #fecaca !important; background-color: #fff1f2; }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen pb-24">

    <header class="sticky top-0 z-50 sticky-header border-b border-slate-100 px-4 py-3 md:px-6 md:py-4">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <h1 class="text-xl md:text-2xl font-black italic tracking-tighter text-slate-900">iT<span class="text-red-600">Live</span></h1>
                <div class="hidden md:flex">
                    <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[10px] font-black uppercase">{{ $test->title }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right mr-2">
                     <span class="block text-xs font-bold text-slate-800">{{ Session::get('student_name') }}</span>
                </div>

                <div class="flex items-center gap-2 md:gap-3 pl-3 md:pl-6 border-l border-slate-200">
                    <div class="flex flex-col items-end">
                        <span class="hidden md:block text-[10px] text-slate-400 font-black uppercase tracking-widest">Qolgan vaqt</span>
                        <span id="timer" class="text-lg md:text-xl font-black text-red-600 tabular-nums">35:00</span>
                    </div>
                    <div class="h-8 w-8 md:h-10 md:w-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600 text-sm md:text-base">
                        <i class="fa-solid fa-clock animate-pulse"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto pt-6 px-4 md:pt-10">

        <form action="{{ route('student.submit', $test->unique_link) }}" method="POST" id="examForm">
            @csrf

            @foreach($test->sections as $sectionIndex => $section)
                @php
                    $colors = ['bg-red-600', 'bg-indigo-600', 'bg-emerald-600', 'bg-orange-600'];
                    $color = $colors[$sectionIndex % count($colors)];
                @endphp

                <div class="mb-8 md:mb-12">
                    <h2 class="text-lg md:text-xl font-black text-slate-800 mb-4 md:mb-6 flex items-center gap-3">
                        <span class="w-1.5 h-6 md:w-2 md:h-8 {{ $color }} rounded-full"></span>
                        {{ $section->name }}
                    </h2>

                    <div class="space-y-4 md:space-y-6">
                        @foreach($section->questions as $qIndex => $question)
                        <div class="bg-white rounded-2xl md:rounded-[2rem] p-5 md:p-8 shadow-sm border border-slate-100 question-card" id="card-{{ $question->id }}">
                            <p class="text-base md:text-lg font-bold text-slate-800 mb-4 md:mb-6">
                                <span class="text-slate-400 mr-1">{{ $qIndex + 1 }}.</span> {{ $question->question_text }}
                            </p>

                            <div class="grid grid-cols-1 gap-2 md:gap-3">
                                @foreach(['a', 'b', 'c', 'd'] as $variant)
                                <div class="relative">
                                    <input type="radio"
                                           name="answers[{{ $question->id }}]"
                                           id="q{{ $question->id }}_{{ $variant }}"
                                           value="{{ $variant }}"
                                           onchange="removeError('card-{{ $question->id }}')"
                                           class="hidden custom-radio">

                                    <label for="q{{ $question->id }}_{{ $variant }}"
                                           class="flex items-center p-3 md:p-4 border-2 border-slate-50 rounded-xl md:rounded-2xl cursor-pointer hover:bg-slate-50 transition-all font-semibold text-slate-600 text-sm md:text-base">
                                        <span class="w-6 h-6 md:w-8 md:h-8 rounded-full border-2 border-slate-200 mr-3 flex items-center justify-center text-[10px] md:text-xs font-black uppercase text-slate-400 shrink-0">{{ $variant }}</span>
                                        {{ $question->{'option_' . $variant} }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="mt-10 mb-6">
                <button type="button" onclick="validateAndSubmit()" class="w-full py-4 md:py-6 bg-slate-900 hover:bg-red-600 text-white rounded-2xl md:rounded-[2rem] font-black uppercase text-xs md:text-sm tracking-[0.2em] shadow-xl active:scale-95 transition-all">
                    Javoblarni Yuborish
                </button>
            </div>
        </form>
    </main>

    <script>
       // --- 1. TIMER MANTIQI ---
        let time = 2100; // 35 daqiqa (35 * 60 = 2100)

        const timerElement = document.getElementById('timer');

        const countdown = setInterval(() => {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;

            // Chiroyli ko'rinish uchun 0 qo'shish (Masalan: 5 emas, 05)
            seconds = seconds < 10 ? '0' + seconds : seconds;
            minutes = minutes < 10 ? '0' + minutes : minutes;

            timerElement.innerHTML = `${minutes}:${seconds}`;

            if(time <= 0) {
                clearInterval(countdown);
                // Vaqt tugaganda majburiy yuborish
                document.getElementById('examForm').submit();
            } else {
                time--;
            }
        }, 1000);

        // --- 2. TEKSHIRUV (VALIDATION) MANTIQI ---
        function validateAndSubmit() {
            // Hamma savol kartalarini olamiz
            const allCards = document.querySelectorAll('.question-card');
            let unansweredCount = 0;
            let firstUnanswered = null;

            allCards.forEach(card => {
                // Shu karta ichida checked input bormi?
                const hasAnswer = card.querySelector('input[type="radio"]:checked');

                if (!hasAnswer) {
                    unansweredCount++;
                    // Javob berilmagan kartani qizil qilamiz
                    card.classList.add('unanswered-card');
                    if (!firstUnanswered) firstUnanswered = card;
                } else {
                    card.classList.remove('unanswered-card');
                }
            });

            if (unansweredCount > 0) {
                // Xato xabari
                alert(`Siz ${unansweredCount} ta savolga javob bermadingiz!\nIltimos, barchasini belgilang.`);

                // Javob berilmagan birinchi savolga olib borish
                firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                // Hammasi joyida bo'lsa, tasdiqlash so'raymiz
                if (confirm('Testni yakunlaysizmi?')) {
                    document.getElementById('examForm').submit();
                }
            }
        }

        // Javob belgilaganda qizil rangni olish funksiyasi
        function removeError(cardId) {
            const card = document.getElementById(cardId);
            if (card) {
                card.classList.remove('unanswered-card');
            }
        }
    </script>
</body>
</html>
