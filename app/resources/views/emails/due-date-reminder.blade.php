<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembrete de Devolução</title>
</head>
<body style="font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h2 style="color: #4338ca;">📚 Biblioteca — Lembrete de Devolução</h2>

    <p>Olá, <strong>{{ $loan->user->name }}</strong>!</p>

    <p>Este é um lembrete de que o prazo de devolução do livro abaixo é <strong>amanhã</strong>:</p>

    <div style="background: #f1f5f9; border-left: 4px solid #4338ca; padding: 16px; border-radius: 4px; margin: 16px 0;">
        <p style="margin: 0 0 4px;"><strong>Livro:</strong> {{ $loan->book->title }}</p>
        <p style="margin: 0 0 4px;"><strong>Autor:</strong> {{ $loan->book->author }}</p>
        <p style="margin: 0;"><strong>Prazo:</strong> {{ $loan->due_date->format('d/m/Y') }}</p>
    </div>

    <p>Por favor, devolva o livro até a data limite para evitar multas.</p>

    <p>Caso haja devolução em atraso, uma multa de <strong>R$ {{ number_format(config('library.fine_amount', 100), 2, ',', '.') }}</strong> será aplicada.</p>

    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
    <p style="font-size: 12px; color: #64748b;">Sistema de Biblioteca — notificação automática.</p>
</body>
</html>
