<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha</title>
</head>
<body style="font-family: sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h2 style="color: #4338ca;">📚 Biblioteca — Recuperação de Senha</h2>

    <p>Você solicitou a redefinição da sua senha.</p>

    <p>Clique no botão abaixo para criar uma nova senha:</p>

    <div style="margin: 24px 0;">
        <a href="{{ $resetUrl }}"
           style="background: #4338ca; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
            Redefinir Senha
        </a>
    </div>

    <p>Ou copie e cole o link abaixo no seu navegador:</p>
    <p style="word-break: break-all; color: #4338ca; font-size: 12px;">{{ $resetUrl }}</p>

    <p>Este link expira em 60 minutos.</p>

    <p>Se você não solicitou a redefinição de senha, ignore este e-mail.</p>

    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
    <p style="font-size: 12px; color: #64748b;">Sistema de Biblioteca — notificação automática.</p>
</body>
</html>
