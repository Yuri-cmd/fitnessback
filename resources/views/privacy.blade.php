<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad — Power Stack</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #F8F9FA;
            color: #121212;
            line-height: 1.7;
        }
        header {
            background: #fff;
            border-bottom: 1px solid #e8e8e8;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        header .logo {
            width: 36px; height: 36px;
            background: #A1CD35;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 18px; color: #fff;
        }
        header h1 { font-size: 18px; font-weight: 700; letter-spacing: 1px; }
        header span { font-size: 13px; color: #616161; }
        .container {
            max-width: 780px;
            margin: 40px auto;
            padding: 0 24px 60px;
        }
        .hero {
            background: #A1CD35;
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 40px;
            color: #fff;
        }
        .hero h2 { font-size: 28px; font-weight: 900; margin-bottom: 6px; }
        .hero p { opacity: .85; font-size: 14px; }
        h3 {
            font-size: 16px;
            font-weight: 700;
            margin: 32px 0 10px;
            color: #121212;
            letter-spacing: .5px;
        }
        h3::before {
            content: '';
            display: inline-block;
            width: 4px; height: 16px;
            background: #A1CD35;
            border-radius: 2px;
            margin-right: 8px;
            vertical-align: middle;
        }
        p, li { font-size: 14px; color: #444; margin-bottom: 8px; }
        ul { padding-left: 20px; }
        li { margin-bottom: 4px; }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            margin-top: 8px;
            border: 1px solid #f0f0f0;
        }
        .contact-box {
            background: #fff;
            border: 1px solid #A1CD35;
            border-radius: 16px;
            padding: 24px;
            margin-top: 40px;
            text-align: center;
        }
        .contact-box a {
            color: #A1CD35;
            font-weight: 600;
            text-decoration: none;
        }
        footer {
            text-align: center;
            color: #aaa;
            font-size: 12px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">P</div>
    <div>
        <h1>POWER STACK</h1>
        <span>Fitness Tracker</span>
    </div>
</header>

<div class="container">

    <div class="hero">
        <h2>Política de Privacidad</h2>
        <p>Última actualización: {{ date('d \d\e F \d\e Y') }}</p>
    </div>

    <div class="card">
        <p>
            En <strong>Power Stack</strong> nos comprometemos a proteger tu privacidad.
            Esta política explica qué información recopilamos, cómo la usamos y cuáles son tus derechos
            al usar nuestra aplicación de seguimiento de fitness.
        </p>
    </div>

    <h3>1. Información que recopilamos</h3>
    <div class="card">
        <p>Recopilamos únicamente la información necesaria para brindarte el servicio:</p>
        <ul>
            <li><strong>Datos de cuenta:</strong> nombre, correo electrónico y contraseña (almacenada de forma cifrada).</li>
            <li><strong>Datos de salud y fitness:</strong> peso corporal, altura, registros de entrenamientos, series, repeticiones y pesos utilizados.</li>
            <li><strong>Metas y progreso:</strong> objetivos de peso, frecuencia de entrenamiento y logros obtenidos.</li>
            <li><strong>Hidratación:</strong> registros de consumo de agua diario.</li>
            <li><strong>Datos técnicos:</strong> tipo de dispositivo y versión del sistema operativo (para mejorar la app), sin información de identificación personal adicional.</li>
        </ul>
    </div>

    <h3>2. Cómo usamos tu información</h3>
    <div class="card">
        <p>Utilizamos tus datos exclusivamente para:</p>
        <ul>
            <li>Mostrarte tu progreso, estadísticas y récords personales dentro de la app.</li>
            <li>Calcular tu IMC y estado físico.</li>
            <li>Sincronizar tus entrenamientos entre dispositivos.</li>
            <li>Otorgarte logros y mantener tu racha de entrenamiento.</li>
            <li>Mejorar el rendimiento y la experiencia de la aplicación.</li>
        </ul>
        <p><strong>No vendemos ni compartimos tu información con terceros para fines comerciales.</strong></p>
    </div>

    <h3>3. Almacenamiento y seguridad</h3>
    <div class="card">
        <ul>
            <li>Tus datos se almacenan en servidores seguros con acceso restringido.</li>
            <li>Las contraseñas se almacenan con hash bcrypt y nunca en texto plano.</li>
            <li>La comunicación entre la app y el servidor se realiza mediante HTTPS (cifrado TLS).</li>
            <li>Los tokens de autenticación expiran y se invalidan al cerrar sesión.</li>
        </ul>
    </div>

    <h3>4. Retención de datos</h3>
    <div class="card">
        <p>
            Conservamos tus datos mientras tu cuenta esté activa. Puedes solicitar la eliminación
            de tu cuenta y todos sus datos asociados en cualquier momento escribiéndonos al correo
            de contacto indicado al final de este documento. Los datos serán eliminados en un
            plazo máximo de 30 días.
        </p>
    </div>

    <h3>5. Tus derechos</h3>
    <div class="card">
        <p>Tienes derecho a:</p>
        <ul>
            <li><strong>Acceder</strong> a todos los datos que tenemos sobre ti.</li>
            <li><strong>Rectificar</strong> información incorrecta desde el perfil de la app.</li>
            <li><strong>Eliminar</strong> tu cuenta y toda tu información personal.</li>
            <li><strong>Exportar</strong> tus datos (funcionalidad disponible desde la plataforma web).</li>
            <li><strong>Oponerte</strong> al procesamiento de tus datos en cualquier momento.</li>
        </ul>
    </div>

    <h3>6. Permisos de la aplicación</h3>
    <div class="card">
        <p>La aplicación móvil puede solicitar los siguientes permisos:</p>
        <ul>
            <li><strong>Internet:</strong> para sincronizar tus datos con el servidor.</li>
            <li><strong>Notificaciones:</strong> para recordatorios de entrenamiento e hidratación (opcional).</li>
        </ul>
        <p>No accedemos a tu cámara, micrófono, contactos ni ubicación.</p>
    </div>

    <h3>7. Menores de edad</h3>
    <div class="card">
        <p>
            Power Stack está dirigida a personas mayores de 13 años. No recopilamos
            intencionalmente información de menores de 13 años. Si eres padre o tutor y
            crees que tu hijo ha proporcionado datos personales, contáctanos para
            eliminarlos de inmediato.
        </p>
    </div>

    <h3>8. Cambios a esta política</h3>
    <div class="card">
        <p>
            Podemos actualizar esta política de privacidad ocasionalmente. Te notificaremos
            sobre cambios significativos a través de la aplicación o por correo electrónico.
            La fecha de última actualización siempre estará visible en la parte superior de
            este documento.
        </p>
    </div>

    <div class="contact-box">
        <p style="font-size:16px; font-weight:700; margin-bottom:8px;">¿Tienes preguntas?</p>
        <p>Contáctanos en <a href="mailto:privacidad@magusemail.com">privacidad@magusemail.com</a></p>
        <p style="margin-top:8px; font-size:12px; color:#aaa;">Power Stack — Fitness Tracker</p>
    </div>

    <footer>
        &copy; {{ date('Y') }} Power Stack. Todos los derechos reservados.
    </footer>

</div>
</body>
</html>
