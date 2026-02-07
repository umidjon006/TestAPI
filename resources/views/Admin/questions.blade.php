<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $section->name }} - Savollar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-gradient { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
        .table-row-hover:hover { background-color: #f8fafc; }
        #modalOverlay.active, #editModalOverlay.active { display: flex; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-80 sidebar-gradient text-slate-300 flex flex-col justify-between shadow-2xl z-20">
        <div>
            <div class="p-10 border-b border-slate-700/50 mb-6 text-center">
                <h1 class="text-4xl font-extrabold tracking-tighter"><span class="text-white">iT</span><span class="text-red-600">Live</span></h1>
            </div>
            <div class="px-6">
                <nav class="space-y-2">
                    <a href="{{ route('admin.sections.index', $section->test_id) }}" class="flex items-center space-x-4 p-4 hover:bg-slate-800 rounded-xl transition-all">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span class="font-semibold text-sm tracking-widest">Bo'limlarga qaytish</span>
                    </a>
                </nav>
            </div>
        </div>
        <div class="p-8">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf <button type="submit" class="w-full flex items-center justify-center space-x-3 p-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl shadow-lg transition-all font-black uppercase text-xs tracking-widest cursor-pointer">
                    <i class="fa-solid fa-power-off"></i>
                    <span>Chiqish</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto bg-white">
        <header class="py-6 px-12 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ $section->name }} savollari</h2>
            <button onclick="toggleModal(true)" class="h-10 w-10 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-red-600 transition-all shadow-lg shadow-slate-200">
                <i class="fa-solid fa-plus text-sm"></i>
            </button>
        </header>

        <section class="p-8">

          @if(session('success'))
                <div id="success-alert" class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl font-bold shadow-sm transition-opacity duration-500 ease-out">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-xl font-bold border border-red-200">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100">
                            <th class="p-4 pl-6 w-16">T/r</th>
                            <th class="p-4">Savol matni</th>
                            <th class="p-4 text-center">A</th>
                            <th class="p-4 text-center">B</th>
                            <th class="p-4 text-center">C</th>
                            <th class="p-4 text-center">D</th>
                            <th class="p-4 text-right pr-6">Amallar</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($section->questions as $index => $question)
                        <tr class="table-row-hover border-b border-slate-50 transition-all">
                            <td class="p-4 pl-6 text-slate-400 font-bold">{{ $index + 1 }}</td>
                            <td class="p-4 font-bold text-slate-700">{{ $question->question_text }}</td>

                            <td class="p-4 text-center text-xs {{ $question->correct_answer == 'a' ? 'text-emerald-600 font-bold bg-emerald-50 rounded' : 'text-slate-400' }}">
                                {{ $question->option_a }}
                            </td>
                            <td class="p-4 text-center text-xs {{ $question->correct_answer == 'b' ? 'text-emerald-600 font-bold bg-emerald-50 rounded' : 'text-slate-400' }}">
                                {{ $question->option_b }}
                            </td>
                            <td class="p-4 text-center text-xs {{ $question->correct_answer == 'c' ? 'text-emerald-600 font-bold bg-emerald-50 rounded' : 'text-slate-400' }}">
                                {{ $question->option_c }}
                            </td>
                            <td class="p-4 text-center text-xs {{ $question->correct_answer == 'd' ? 'text-emerald-600 font-bold bg-emerald-50 rounded' : 'text-slate-400' }}">
                                {{ $question->option_d }}
                            </td>

                            <td class="p-4 pr-6 text-right">
                                <div class="flex justify-end gap-2">
                                    {{--
                                        DIQQAT! BU YERDA ENG MUHIM O'ZGARISH:
                                        onclick ichida qo'shtirnoq (") o'rniga bittalik tirnoq (') ishlatildi.
                                        Bu @json ichidagi qo'shtirnoqlar bilan to'qnashuvni oldini oladi.
                                    --}}
                                    <button onclick='openEditModal(@json($question))' class="p-2 bg-slate-50 rounded-lg text-slate-400 hover:text-indigo-600 transition-all">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('O\'chirilsinmi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-slate-50 rounded-lg text-slate-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 font-bold">Hozircha savollar yo'q</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-10">
                <a href="{{ route('admin.sections.index', $section->test_id) }}" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-slate-800 transition-all shadow-xl">Bo'limga qaytish</a>
            </div>
        </section>
    </main>

    <div id="modalOverlay" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl p-8 border border-white/20">
            <h3 class="text-2xl font-black text-slate-800 mb-8 tracking-tight flex items-center gap-3">
                    Yangi savol qo'shish
                    <span class="bg-indigo-100 text-indigo-600 py-1 px-3 rounded-lg text-lg border border-indigo-200">
                        #{{ $section->questions->count() + 1 }}
                    </span>
                </h3>

            <form action="{{ route('admin.questions.store', $section->id) }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Savol matni</label>
                    <textarea name="question_text" required rows="3" placeholder="Savolni kiriting..." class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-600 focus:outline-none transition-all font-semibold resize-none"></textarea>
                </div>

               <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Variant A</span>
                            <input type="radio" name="correct_answer" value="a" class="w-4 h-4 accent-emerald-500" checked>
                        </label>
                        <input type="text" name="option_a" required placeholder="Javobni kiriting" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-600 outline-none transition-all text-sm font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Variant B</span>
                            <input type="radio" name="correct_answer" value="b" class="w-4 h-4 accent-emerald-500">
                        </label>
                        <input type="text" name="option_b" required placeholder="Javobni kiriting" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-600 outline-none transition-all text-sm font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Variant C</span>
                            <input type="radio" name="correct_answer" value="c" class="w-4 h-4 accent-emerald-500">
                        </label>
                        <input type="text" name="option_c" required placeholder="Javobni kiriting" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-600 outline-none transition-all text-sm font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Variant D</span>
                            <input type="radio" name="correct_answer" value="d" class="w-4 h-4 accent-emerald-500">
                        </label>
                        <input type="text" name="option_d" required placeholder="Javobni kiriting" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-600 outline-none transition-all text-sm font-bold">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="toggleModal(false)" class="px-6 py-3 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-all uppercase text-[10px] tracking-widest">Bekor qilish</button>
                    <button type="submit" class="px-10 py-3 bg-emerald-500 text-white font-black rounded-xl hover:bg-emerald-600 transition-all uppercase text-[10px] tracking-widest shadow-lg shadow-emerald-200">Saqlash</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModalOverlay" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl p-8 border border-white/20">
            <h3 class="text-2xl font-black text-slate-800 mb-8 tracking-tight">Savolni tahrirlash</h3>

            <form id="editForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Savol matni</label>
                    <textarea id="edit_question_text" name="question_text" required rows="3" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-600 focus:outline-none transition-all font-semibold resize-none"></textarea>
                </div>

               <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Variant A</span>
                            <input type="radio" name="correct_answer" value="a" class="w-4 h-4 accent-emerald-500" id="edit_radio_a">
                        </label>
                        <input type="text" id="edit_option_a" name="option_a" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-600 outline-none transition-all text-sm font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Variant B</span>
                            <input type="radio" name="correct_answer" value="b" class="w-4 h-4 accent-emerald-500" id="edit_radio_b">
                        </label>
                        <input type="text" id="edit_option_b" name="option_b" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-600 outline-none transition-all text-sm font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Variant C</span>
                            <input type="radio" name="correct_answer" value="c" class="w-4 h-4 accent-emerald-500" id="edit_radio_c">
                        </label>
                        <input type="text" id="edit_option_c" name="option_c" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-600 outline-none transition-all text-sm font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="flex justify-between items-center px-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Variant D</span>
                            <input type="radio" name="correct_answer" value="d" class="w-4 h-4 accent-emerald-500" id="edit_radio_d">
                        </label>
                        <input type="text" id="edit_option_d" name="option_d" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-600 outline-none transition-all text-sm font-bold">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="toggleEditModal(false)" class="px-6 py-3 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-all uppercase text-[10px] tracking-widest">Bekor qilish</button>
                    <button type="submit" class="px-10 py-3 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 transition-all uppercase text-[10px] tracking-widest shadow-lg shadow-indigo-200">Yangilash</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modalni ochish/yopish funksiyalari
        function toggleModal(show) {
            const modal = document.getElementById('modalOverlay');
            if(show) { modal.classList.remove('hidden'); setTimeout(() => modal.classList.add('active'), 10); }
            else { modal.classList.remove('active'); setTimeout(() => modal.classList.add('hidden'), 300); }
        }

        function toggleEditModal(show) {
            const modal = document.getElementById('editModalOverlay');
            if(show) { modal.classList.remove('hidden'); setTimeout(() => modal.classList.add('active'), 10); }
            else { modal.classList.remove('active'); setTimeout(() => modal.classList.add('hidden'), 300); }
        }

        // Tahrirlash oynasini ochish
        function openEditModal(question) {
            // 1. Inputlarni to'ldirish
            document.getElementById('edit_question_text').value = question.question_text;
            document.getElementById('edit_option_a').value = question.option_a;
            document.getElementById('edit_option_b').value = question.option_b;
            document.getElementById('edit_option_c').value = question.option_c;
            document.getElementById('edit_option_d').value = question.option_d;

            // 2. To'g'ri javobni belgilash
            let correct = question.correct_answer.toLowerCase();
            let radioBtn = document.getElementById('edit_radio_' + correct);
            if(radioBtn) radioBtn.checked = true;

            // 3. URL yasash (MUHIM TUZATISH)
            // Biz ID o'rniga "PLACEHOLDER_ID" so'zini qo'yamiz va keyin uni JavaScriptda almashtiramiz.
            // Bu usul xavfsiz va aniq ishlaydi.
            let url = "{{ route('admin.questions.update', 'PLACEHOLDER_ID') }}";

            // "PLACEHOLDER_ID" so'zini haqiqiy ID ga almashtiramiz
            url = url.replace('PLACEHOLDER_ID', question.id);

            document.getElementById('editForm').action = url;

            toggleEditModal(true);
        }

        // Modaldan tashqariga bosganda yopish
        window.onclick = function(event) {
            const createModal = document.getElementById('modalOverlay');
            const editModal = document.getElementById('editModalOverlay');
            if (event.target == createModal) toggleModal(false);
            if (event.target == editModal) toggleEditModal(false);
        }

        // Xabarni avtomatik o'chirish
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.classList.add('opacity-0');
                    setTimeout(() => { alert.remove(); }, 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>
