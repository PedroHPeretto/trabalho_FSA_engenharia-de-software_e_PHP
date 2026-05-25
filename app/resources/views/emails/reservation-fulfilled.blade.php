<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Disponível</title>
</head>
<body style="font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h2 style="color: #4338ca;">📚 Biblioteca — Sua Reserva Está Disponível!</h2>

    <p>Olá, <strong>{{ $reservation->user->name }}</strong>!</p>

    <p>Boas notícias! O livro que você reservou já está disponível:</p>

    <div style="background: #f1f5f9; border-left: 4px solid #4338ca; padding: 16px; border-radius: 4px; margin: 16px 0;">
        <p style="margin: 0 0 4px;"><strong>Livro:</strong> {{ $reservation->book->title }}</p>
        <p style="margin: 0;"><strong>Autor:</strong> {{ $reservation->book->author }}</p>
    </div>

    <p>Dirija-se à biblioteca para realizar o empréstimo. Sua reserva expira em <strong>{{ config('library.reservation_expiry_days', 3) }} dias</strong>.</p>

    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
    <p style="font-size: 12px; color: #64748b;">Sistema de Biblioteca — notificação automática.</p>
</body>
</html>
