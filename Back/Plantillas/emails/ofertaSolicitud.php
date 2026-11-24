<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <img src="cid:logo_cid" alt="TruequeApp Logo" style="max-width: 150px; margin-bottom: 20px;">
        <h2 style="color: #2c3e50;">¡Tu propuesta ha sido solicitada!</h2>
        <p>Hola <strong><?= htmlspecialchars($solicitante->getNombre()) ?></strong>,</p>
        <p>El cliente <strong><?= htmlspecialchars($profesional->getNombre()) ?></strong> ha solicitado la realización de la tarea:</p>
        <blockquote style="background: #f9f9f9; padding: 10px; border-left: 4px solid #3498db;">
            <strong><?= htmlspecialchars($solicitud->getOferta()->getServicio()->getTitulo()) ?></strong><br>
            <?= htmlspecialchars($solicitud->getDescripcion()) ?>
        </blockquote>
    </div>
</body>
</html>