<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 — Serviço Indisponível · Sistema de Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<nav class="bg-indigo-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <span class="text-xl font-bold tracking-tight">📚 Biblioteca</span>
        </div>
    </div>
</nav>

<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-md">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-100 mb-6">
            <svg class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>
            </svg>
        </div>
        <p class="text-5xl font-extrabold text-indigo-200 select-none leading-none">503</p>
        <h1 class="mt-4 text-2xl font-bold text-gray-800">Sistema em manutenção</h1>
        <p class="mt-3 text-gray-500 text-sm leading-relaxed">
            O sistema está temporariamente indisponível para manutenção.<br>
            Voltaremos em breve. Agradecemos sua compreensão.
        </p>

        @if(isset($exception) && $exception->getMessage())
            @php $retryAfter = null; @endphp
        @endif

        <div class="mt-8 flex justify-center">
            <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 transition shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Verificar novamente
            </button>
        </div>
    </div>
</main>

<footer class="text-center text-xs text-gray-400 py-6">
    Sistema de Biblioteca
</footer>

</body>
</html>
