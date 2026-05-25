<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Sessão Expirada · Sistema de Biblioteca</title>
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
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-yellow-100 mb-6">
            <svg class="w-10 h-10 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-5xl font-extrabold text-yellow-200 select-none leading-none">419</p>
        <h1 class="mt-4 text-2xl font-bold text-gray-800">Sessão expirada</h1>
        <p class="mt-3 text-gray-500 text-sm leading-relaxed">
            Sua sessão expirou por inatividade ou o formulário ficou aberto por muito tempo.<br>
            Recarregue a página e tente novamente.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.history.back()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </button>
            <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 transition shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Recarregar página
            </button>
        </div>
    </div>
</main>

<footer class="text-center text-xs text-gray-400 py-6">
    Sistema de Biblioteca
</footer>

</body>
</html>
