<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página Não Encontrada · Sistema de Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<nav class="bg-indigo-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="text-xl font-bold tracking-tight">📚 Biblioteca</a>
        </div>
    </div>
</nav>

<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-md">
        <p class="text-8xl font-extrabold text-indigo-200 select-none leading-none">404</p>
        <h1 class="mt-4 text-2xl font-bold text-gray-800">Página não encontrada</h1>
        <p class="mt-3 text-gray-500 text-sm leading-relaxed">
            O endereço que você acessou não existe ou foi removido.<br>
            Verifique o link e tente novamente.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </a>
            <a href="/"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Ir para a página inicial
            </a>
        </div>
    </div>
</main>

<footer class="text-center text-xs text-gray-400 py-6">
    Sistema de Biblioteca
</footer>

</body>
</html>
