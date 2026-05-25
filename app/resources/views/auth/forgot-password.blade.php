@extends('layouts.app')

@section('title', 'Recuperar Senha')

@section('content')
<div class="min-h-screen flex items-center justify-center -mt-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-xl font-bold text-gray-900 mb-2">Recuperar Senha</h1>
            <p class="text-gray-500 text-sm mb-6">Informe seu e-mail e enviaremos um link para redefinir sua senha.</p>

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition text-sm">
                    Enviar Link de Recuperação
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline">Voltar ao login</a>
            </div>
        </div>
    </div>
</div>
@endsection
