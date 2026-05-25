@extends('layouts.app')

@section('title', 'Entrar')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="text-4xl mb-2">📚</div>
                <h1 class="text-2xl font-bold text-gray-900">Biblioteca</h1>
                <p class="text-gray-500 text-sm mt-1">Faça login para continuar</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror"
                           required autofocus>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input type="password" id="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror"
                           required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition text-sm">
                    Entrar
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">
                    Esqueceu sua senha?
                </a>
            </div>

            <div class="mt-3 text-center text-sm text-gray-500">
                Não tem uma conta?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">Criar conta</a>
            </div>
        </div>
    </div>
</div>
@endsection
