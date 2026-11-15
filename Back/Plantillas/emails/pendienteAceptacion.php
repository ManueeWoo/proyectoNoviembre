<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <img src="cid:banner_cid" alt="Bienvenido a TruequeApp" style="width: 100%; height: auto; border-radius: 4px;">
        <h2>Registro Recibido</h2>
        <p>Hola <strong><?= htmlspecialchars($user->getNombre()) ?></strong>,</p>
        <p>Gracias por registrarte en nuestra plataforma de trueque profesional. Tu solicitud de registro se encuentra en proceso de revisión por parte de nuestro equipo de administración.</p>
        <p>Te enviaremos una notificación cuando tu cuenta sea aprobada.</p>
        <p style="margin-top: 25px;">
            <a href="<?= htmlspecialchars($actionUrl) ?>" style="color: #3498db; text-decoration: underline;">Consultar el estado de tu cuenta</a>
        </p>
    </div>
</body>
</html>