<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTLive - Natijalar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-gradient { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
        #showModalOverlay.active { display: flex; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-80 sidebar-gradient text-slate-300 flex flex-col justify-between shadow-2xl z-20">
        <div>
            <div class="p-10 border-b border-slate-700/50 mb-6">
                <h1 class="text-5xl font-extrabold tracking-tighter flex items-center group">
                    <span class="text-white">iT</span><span class="text-red-600 group-hover:animate-pulse">Live</span>
                </h1>
                <p class="text-[10px] text-slate-500 uppercase tracking-[0.3em] mt-2 font-bold italic">Academy</p>
            </div>
            <nav class="px-6 space-y-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-4 p-4 hover:bg-slate-800 rounded-xl transition-all group">
                    <i class="fa-solid fa-layer-group text-xl group-hover:text-red-500"></i>
                    <span class="font-semibold text-sm uppercase tracking-widest">Imtihonlar</span>
                </a>
                <a href="#" class="flex items-center space-x-4 p-4 bg-red-600/10 border-l-4 border-red-600 text-white rounded-r-xl transition-all">
                    <i class="fa-solid fa-chart-pie text-xl text-red-600"></i>
                    <span class="font-bold text-sm uppercase tracking-widest">Natijalar</span>
                </a>
            </nav>
        </div>
        <div class="p-8">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-3 p-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl shadow-lg transition-all font-black uppercase text-xs tracking-widest cursor-pointer">
                    <i class="fa-solid fa-power-off"></i> <span>Chiqish</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto bg-[#f1f5f9]">
        <header class="py-10 px-12 flex justify-between items-end">
            <div>
                <nav class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-2">Boshqaruv / Natijalar</nav>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight">Imtihon Natijalari</h2>
            </div>

            <a href="{{ route('admin.analytics.index') }}" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition-all flex items-center gap-3">
                <i class="fa-solid fa-chart-line text-lg"></i>
                Yo'nalishlar tahlili
            </a>
        </header>

        <section class="px-12 pb-12">

            @if(session('success'))
                <div id="success-alert" class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl font-bold shadow-sm transition-opacity duration-500 ease-out">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-white overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="p-6 pl-10">O'quvchi</th>
                            <th class="p-6">Telefon</th>
                            <th class="p-6">Eng Kuchli Yo'nalish</th>
                            <th class="p-6 text-center">To'g'ri Javob</th>
                            <th class="p-6 text-right">Sana</th>
                            <th class="p-6 text-right pr-10">Amallar</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($results as $result)
                            @php
                                // Eng yuqori ball to'plagan bo'limni aniqlash
                                $bestSection = $result->details->sortByDesc('score_percentage')->first();
                                $sectionName = $bestSection ? $bestSection->section->name : '-';
                                $sectionPercent = $bestSection ? $bestSection->score_percentage : 0;

                                // Rang tanlash
                                $badgeColor = 'bg-slate-100 text-slate-600';
                                if(str_contains(strtolower($sectionName), 'front')) $badgeColor = 'bg-emerald-100 text-emerald-600';
                                elseif(str_contains(strtolower($sectionName), 'back')) $badgeColor = 'bg-indigo-100 text-indigo-600';
                                elseif(str_contains(strtolower($sectionName), 'iq')) $badgeColor = 'bg-orange-100 text-orange-600';
                            @endphp

                        <tr class="hover:bg-slate-50 transition-all border-b border-slate-50 group">
                            <td class="p-6 pl-10 font-extrabold text-slate-800">
                                {{ $result->student_name }}
                                <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">{{ $result->test->title ?? 'O\'chirilgan test' }}</div>
                            </td>
                            <td class="p-6 text-slate-500 font-bold font-mono text-xs">
                                {{ $result->phone }}
                            </td>

                            <td class="p-6">
                              @if(session('success'))
                                    <div id="success-alert" class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl font-bold shadow-sm transition-opacity duration-500 ease-out">
                                        {{ session('success') }}
                                    </div>
                                @endif
                            </td>

                            <td class="p-6 text-center">
                                <span class="bg-slate-900 text-white py-1 px-3 rounded-lg font-black text-xs">
                                    {{ $result->correct_answers }} / {{ $result->total_questions }}
                                </span>
                            </td>
                            <td class="p-6 text-right text-slate-400 font-bold text-xs">
                                {{ $result->created_at->format('d.m.Y H:i') }}
                            </td>

                            <td class="p-6 pr-10 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick='openShowModal(@json($result))' class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all p-2 rounded-lg" title="Batafsil ko'rish">
                                        <i class="fa-solid fa-eye text-lg"></i>
                                    </button>

                                    <form action="{{ route('admin.results.destroy', $result->id) }}" method="POST" onsubmit="return confirm('Haqiqatan ham bu natijani o\'chirmoqchimisiz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all p-2 rounded-lg" title="O'chirish">
                                            <i class="fa-solid fa-trash-can text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-slate-400 font-bold">
                                Hozircha natijalar yo'q.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="showModalOverlay" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-3xl rounded-[2rem] shadow-2xl overflow-hidden relative">

            <div class="bg-slate-900 p-8 flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-black text-white tracking-tight" id="modalStudentName">Student Name</h3>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1" id="modalPhone">Phone</p>
                </div>
                <button onclick="toggleShowModal(false)" class="text-white/50 hover:text-white transition-all text-2xl">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Imtihon</p>
                        <p class="text-lg font-black text-slate-800" id="modalTestName">-</p>
                    </div>
                    <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100">
                        <p class="text-[10px] font-black text-emerald-600/60 uppercase tracking-widest mb-1">Umumiy Natija</p>
                        <p class="text-3xl font-black text-emerald-600">
                            <span id="modalCorrect">0</span> <span class="text-base text-emerald-600/50">/ <span id="modalTotal">0</span></span>
                        </p>
                    </div>
                </div>

                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-indigo-600"></i> Yo'nalishlar kesimida
                </h4>

                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] uppercase font-black text-slate-400 tracking-widest">
                            <tr>
                                <th class="p-4">Bo'lim</th>
                                <th class="p-4 text-center">Javoblar</th>
                                <th class="p-4 text-right">Foiz</th>
                            </tr>
                        </thead>
                        <tbody id="modalDetailsBody" class="text-sm font-bold text-slate-700">
                            </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-end">
                    <button onclick="toggleShowModal(false)" class="bg-slate-100 text-slate-600 px-6 py-3 rounded-xl font-bold hover:bg-slate-200 transition-all text-xs uppercase tracking-widest">
                        Yopish
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleShowModal(show) {
            const modal = document.getElementById('showModalOverlay');
            if(show) { modal.classList.remove('hidden'); setTimeout(() => modal.classList.add('active'), 10); }
            else { modal.classList.remove('active'); setTimeout(() => modal.classList.add('hidden'), 300); }
        }

        function openShowModal(result) {
            // Asosiy ma'lumotlarni to'ldirish
            document.getElementById('modalStudentName').textContent = result.student_name;
            document.getElementById('modalPhone').textContent = result.phone;
            document.getElementById('modalTestName').textContent = result.test ? result.test.title : 'O\'chirilgan test';
            document.getElementById('modalCorrect').textContent = result.correct_answers;
            document.getElementById('modalTotal').textContent = result.total_questions;

            // Bo'limlar ro'yxatini to'ldirish
            const tbody = document.getElementById('modalDetailsBody');
            tbody.innerHTML = ''; // Tozalash

            // Natijalarni saralash (Eng yuqori foiz tepada)
            const details = result.details.sort((a, b) => b.score_percentage - a.score_percentage);

            details.forEach(detail => {
                // Foizga qarab rang tanlash
                let percentColor = 'text-slate-600';
                if(detail.score_percentage >= 80) percentColor = 'text-emerald-600';
                else if(detail.score_percentage < 50) percentColor = 'text-red-500';

                const row = `
                    <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50">
                        <td class="p-4">
                            <span class="block">${detail.section.name}</span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-lg text-xs font-black">
                                ${detail.correct_answers} / ${detail.total_questions}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <span class="${percentColor} text-lg font-black">${detail.score_percentage}%</span>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });

            toggleShowModal(true);
        }

        // Modal tashqarisiga bosganda yopish
        window.onclick = function(event) {
            const modal = document.getElementById('showModalOverlay');
            if (event.target == modal) toggleShowModal(false);
        }

        // Sahifa to'liq yuklangach ishga tushadi
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');

            // Agar xabar mavjud bo'lsa
            if (alert) {
                // 3000 millisekund (3 sekund) kutadi
                setTimeout(() => {
                    // 1. Opacity ni 0 qilamiz (sekin yo'qolish effekti uchun)
                    alert.classList.add('opacity-0');

                    // 2. Effekt tugagach (0.5s dan keyin) uni umuman olib tashlaymiz
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>
