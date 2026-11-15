<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <img src="cid:badge_cid" alt="Cuenta Verificada" style="max-width: 120px;">
        <h2 style="color: #27ae60;">¡Cuenta Aprobada!</h2>
        <p>Hola <strong><?= htmlspecialchars($user->getNombre()) ?></strong>,</p>
        <p>¡Buenas noticias! El administrador ha confirmado tu registro. Ya puedes acceder a la plataforma, publicar solicitudes de servicios e intercambiar conocimientos con otros profesionales.</p>
        <p style="text-align: center; margin-top: 30px;">
            <a href="<?= htmlspecialchars($actionUrl) ?>" style="background-color: #2980b9; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">Iniciar Sesión Ahora</a>
        </p>
    </div>
</body>
</html>
