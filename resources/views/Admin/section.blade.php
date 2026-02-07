<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $test->title }} - Bo'limlar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-gradient { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
        .table-row-hover:hover { background-color: #f8fafc; transform: scale(1.002); box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        #modalOverlay.active, #editModalOverlay.active { display: flex; animation: fadeIn 0.3s ease-out; }
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
                    <i class="fa-solid fa-list text-xl text-red-600"></i>
                    <span class="font-bold text-sm uppercase tracking-widest">Bo'limlar</span>
                </a>
            </nav>
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

    <main class="flex-1 overflow-y-auto bg-[#f1f5f9]">
        <header class="py-10 px-12 flex justify-between items-end">
            <div>
                <nav class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-2">Imtihonlar / {{ $test->title }}</nav>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight">Imtihon bo'limlari</h2>
            </div>
            <button onclick="toggleModal(true)" class="h-14 w-14 bg-slate-900 text-white rounded-2xl flex items-center justify-center hover:bg-red-600 shadow-xl shadow-slate-300 transition-all transform hover:scale-110">
                <i class="fa-solid fa-plus text-xl"></i>
            </button>
        </header>

        <section class="px-12 pb-12">

          @if(session('success'))
                <div id="success-alert" class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl font-bold shadow-sm transition-opacity duration-500 ease-out">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-2xl font-bold shadow-sm border border-red-200">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-white overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="p-6 pl-10">ID</th>
                            <th class="p-6">Nomi</th>
                            <th class="p-6 text-center">Bo'limlar</th>
                            <th class="p-6 text-right pr-10">Amallar</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($test->sections as $section)
                        <tr class="table-row-hover transition-all border-b border-slate-50 group">
                            <td class="p-6 pl-10 text-slate-400 font-bold">#{{ $section->id }}</td>
                            <td class="p-6 font-extrabold text-slate-800 uppercase italic tracking-wider">{{ $section->name }}</td>
                            <td class="p-6 text-center font-bold text-slate-600">
                                <span class="text-slate-400">{{ $section->total_questions }}</span> / <span class="text-indigo-600">{{ $section->questions_to_ask }}</span>
                            </td>
                            <td class="p-6 pr-10 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.questions.index', $section->id) }}"
                                        class="inline-block bg-white border-2 border-slate-100 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase hover:border-indigo-600 transition-all text-slate-500 hover:text-indigo-600">
                                        Savollar +
                                    </a>

                                    <button onclick="openEditModal('{{ $section->id }}', '{{ $section->name }}', '{{ $section->questions_to_ask }}')" class="bg-slate-100 p-2 rounded-xl text-slate-400 hover:bg-indigo-600 hover:text-white transition-all">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form action="{{ route('admin.sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('Rostdan ham o\'chirasizmi?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 p-2 rounded-xl text-white hover:bg-red-700 transition-all">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-slate-400 font-bold">
                                Hozircha bo'limlar yo'q. Yuqoridagi tugma orqali qo'shing.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-8">
                <a href="{{ route('admin.dashboard') }}" class="bg-slate-900 text-white px-10 py-4 rounded-2xl font-bold hover:bg-slate-800 transition-all shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Ortga
                </a>
            </div>
        </section>
    </main>

    <div id="modalOverlay" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 relative border border-white/20">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Yangi bo'lim yaratish</h3>

            <form action="{{ route('admin.sections.store', $test->id) }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Bo'lim Nomi</label>
                    <input type="text" name="name" placeholder="Masalan: Frontend" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-red-600 transition-all font-bold">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nechta savol tushsin?</label>
                    <input type="number" name="questions_to_ask" value="10" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-red-600 transition-all font-bold">
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="toggleModal(false)" class="px-6 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all">Bekor qilish</button>
                    <button type="submit" class="px-8 py-2.5 bg-[#00C853] text-white font-bold rounded-xl hover:shadow-lg transition-all">Yaratish</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModalOverlay" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 relative border border-white/20">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Bo'limni tahrirlash</h3>

            <form id="editForm" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Bo'lim Nomi</label>
                    <input type="text" id="edit_name" name="name" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 transition-all font-bold">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nechta savol tushsin?</label>
                    <input type="number" id="edit_questions_to_ask" name="questions_to_ask" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 transition-all font-bold">
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="toggleEditModal(false)" class="px-6 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all">Bekor qilish</button>
                    <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:shadow-lg transition-all">Saqlash</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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

        function openEditModal(id, name, q_count) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_questions_to_ask').value = q_count;
            document.getElementById('editForm').action = "/admin/sections/" + id;
            toggleEditModal(true);
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('modalOverlay')) toggleModal(false);
            if (event.target == document.getElementById('editModalOverlay')) toggleEditModal(false);
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
