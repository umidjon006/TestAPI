<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>iTLive - Testga kirish</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#0f172a] flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-md rounded-[2rem] md:rounded-[2.5rem] shadow-2xl p-6 md:p-10">
        <div class="text-center mb-8 md:mb-10">
            <h1 class="text-3xl font-black text-slate-900 italic">iT<span class="text-red-600">Live</span></h1>
            <p class="text-slate-400 font-bold text-xs uppercase tracking-widest mt-2">{{ $test->title }}</p>
        </div>

       <form action="{{ route('student.start', $test->unique_link) }}" method="POST" class="space-y-4 md:space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Ism / Familiya</label>
                <input type="text" name="full_name" required placeholder="Ali Valiyev" class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-red-600 outline-none transition-all font-bold text-slate-800">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Telefon raqam</label>
                <input type="tel" name="phone" required placeholder="+998 90 123 45 67" class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-red-600 outline-none transition-all font-bold text-slate-800">
            </div>
            <button type="submit" class="w-full py-4 md:py-5 bg-slate-900 text-white rounded-2xl font-black uppercase text-xs md:text-sm tracking-widest hover:bg-red-600 transition-all shadow-xl active:scale-95">
                Testni boshlash
            </button>
        </form>
    </div>
</body>
</html>
