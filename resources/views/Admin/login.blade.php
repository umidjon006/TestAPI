<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>iTLive - Admin Kirish</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#0f172a] flex items-center justify-center min-h-screen p-4">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-slate-900 italic">iT<span class="text-red-600">Live</span></h1>
            <p class="text-slate-400 font-bold text-xs uppercase tracking-widest mt-2">Admin boshqaruv paneli</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm font-bold p-4 rounded-2xl mb-6 text-center border border-red-100">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-6">
            @csrf <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Email manzil</label>
                <input type="email" name="email" required
                       value="{{ old('email') }}"
                       placeholder="admin@company.com"
                       class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-red-600 outline-none transition-all font-bold text-slate-800">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Parol</label>
                <input type="password" name="password" required
                       placeholder="••••••••"
                       class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl focus:border-red-600 outline-none transition-all font-bold text-slate-800">
            </div>

            <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-2xl font-black uppercase text-sm tracking-widest hover:bg-red-600 transition-all shadow-xl cursor-pointer active:scale-95">
                Tizimga kirish
            </button>
        </form>
    </div>
</body>
</html>
