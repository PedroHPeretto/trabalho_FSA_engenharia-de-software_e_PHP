<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Biblioteca') - Sistema de Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-indigo-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-6">
                <a href="{{ route('books.index') }}" class="text-xl font-bold tracking-tight">📚 Biblioteca</a>
                @auth
                    <a href="{{ route('books.index') }}" class="text-sm hover:text-indigo-200 transition">Livros</a>
                    @if(auth()->user()->role !== 'admin')
                        <a href="{{ route('reservations.index') }}" class="text-sm hover:text-indigo-200 transition">Minhas Reservas</a>
                    @endif
                    <a href="{{ route('fines.index') }}" class="text-sm hover:text-indigo-200 transition">Multas</a>
                    @if(in_array(auth()->user()->role, ['librarian', 'admin']))
                        <a href="{{ route('loans.index') }}" class="text-sm hover:text-indigo-200 transition">Empréstimos</a>
                    @endif
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('users.index') }}" class="text-sm hover:text-indigo-200 transition">Usuários</a>
                        <a href="{{ route('logs.index') }}" class="text-sm hover:text-indigo-200 transition">Logs</a>
                    @endif
                @endauth
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <span class="text-sm text-indigo-200">{{ auth()->user()->name }}</span>
                    <span class="text-xs bg-indigo-500 px-2 py-0.5 rounded-full">{{ auth()->user()->role }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm hover:text-indigo-200 transition">Sair</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm hover:text-indigo-200 transition">Entrar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @if(session('success'))
        <div id="flash-success" class="mb-6 bg-green-50 border border-green-400 text-green-800 px-4 py-3 rounded-lg flex items-start gap-3 shadow-sm transition-opacity duration-500">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="flex-1 text-sm font-medium">{{ session('success') }}</span>
            <button type="button" onclick="dismissFlash('flash-success')" class="text-green-600 hover:text-green-800 transition ml-auto" aria-label="Fechar">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error') || $errors->has('error'))
        <div id="flash-error" class="mb-6 bg-red-50 border border-red-400 text-red-800 px-4 py-3 rounded-lg flex items-start gap-3 shadow-sm transition-opacity duration-500">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span class="flex-1 text-sm font-medium">{{ session('error') ?? $errors->first('error') }}</span>
            <button type="button" onclick="dismissFlash('flash-error')" class="text-red-600 hover:text-red-800 transition ml-auto" aria-label="Fechar">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if($errors->any() && !$errors->has('error'))
        <div id="flash-validation" class="mb-6 bg-red-50 border border-red-400 text-red-800 px-4 py-3 rounded-lg flex items-start gap-3 shadow-sm transition-opacity duration-500">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium mb-1">Por favor, corrija os erros abaixo:</p>
                <ul class="list-disc list-inside text-sm space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" onclick="dismissFlash('flash-validation')" class="text-red-600 hover:text-red-800 transition ml-auto" aria-label="Fechar">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <script>
    function dismissFlash(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    }
    (function () {
        const successId = 'flash-success';
        const el = document.getElementById(successId);
        if (el) setTimeout(() => dismissFlash(successId), 4000);
    })();
    </script>

    @yield('content')
</main>

</body>
</html>
