<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registro - Sistema de Reciclaje</title>
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2c9f5f 0%, #1e7e4a 50%, #0d5a2f 100%);
            min-height: 100vh;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow-y: auto;
        }

        /* Efectos de fondo animado */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(76, 175, 80, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(46, 125, 50, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(27, 94, 32, 0.2) 0%, transparent 50%);
            background-size: 100% 100%;
            animation: moveBackground 15s ease-in-out infinite alternate;
            top: 0;
            left: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,0.03) 35px, rgba(255,255,255,0.03) 70px),
                repeating-linear-gradient(-45deg, transparent, transparent 35px, rgba(255,255,255,0.03) 35px, rgba(255,255,255,0.03) 70px);
            top: 0;
            left: 0;
            pointer-events: none;
        }

        @keyframes moveBackground {
            0% { 
                transform: translate(0, 0) scale(1);
                opacity: 0.8;
            }
            100% { 
                transform: translate(20px, 20px) scale(1.1);
                opacity: 1;
            }
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
            margin: 20px 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #2c9f5f 0%, #1e7e4a 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(44, 159, 95, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .logo-container i {
            font-size: 40px;
            color: white;
        }

        .register-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            background: #f9f9f9;
            outline: none;
        }

        .form-control:focus {
            border-color: #2c9f5f;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(44, 159, 95, 0.1);
        }

        .form-control:focus + i,
        .input-wrapper:has(.form-control:focus) i {
            color: #2c9f5f;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #2c9f5f;
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2c9f5f 0%, #1e7e4a 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(44, 159, 95, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-register:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 159, 95, 0.5);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-register span {
            position: relative;
            z-index: 1;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #2c9f5f;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: #1e7e4a;
            text-decoration: underline;
        }

        /* Mensajes de error/éxito */
        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .alert.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert.success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .alert.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animación de carga */
        .btn-register.loading {
            pointer-events: none;
        }

        .btn-register.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
                border-radius: 15px;
            }

            .register-header h1 {
                font-size: 24px;
            }

            .logo-container {
                width: 70px;
                height: 70px;
            }

            .logo-container i {
                font-size: 35px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="logo-container">
                <i class="fas fa-recycle"></i>
            </div>
            <h1>Crear Cuenta</h1>
            <p>Regístrate para comenzar</p>
        </div>

        <div id="alertMessage" class="alert"></div>

        <form id="registerForm">
            <div class="form-group">
                <label for="nombre">Nombre completo</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        class="form-control" 
                        placeholder="Ingresa tu nombre completo"
                        required
                        autocomplete="name"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="cedula">Cédula</label>
                <div class="input-wrapper">
                    <i class="fas fa-id-card"></i>
                    <input 
                        type="text" 
                        id="cedula" 
                        name="cedula" 
                        class="form-control" 
                        placeholder="Ingresa tu cédula"
                        required
                        pattern="[0-9]{10,20}"
                        title="Solo números, entre 10 y 20 dígitos"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="tu@email.com"
                        required
                        autocomplete="email"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <div class="input-wrapper">
                    <i class="fas fa-phone"></i>
                    <input 
                        type="tel" 
                        id="telefono" 
                        name="telefono" 
                        class="form-control" 
                        placeholder="Ingresa tu teléfono"
                        autocomplete="tel"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Mínimo 8 caracteres"
                        required
                        minlength="8"
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirmar contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        name="confirmPassword" 
                        class="form-control" 
                        placeholder="Confirma tu contraseña"
                        required
                        minlength="8"
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" id="toggleConfirmPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <span>Registrarse</span>
            </button>
        </form>

        <div class="login-link">
            ¿Ya tienes una cuenta? <a href="index.php">Inicia sesión aquí</a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Form submission
        const registerForm = document.getElementById('registerForm');
        const alertMessage = document.getElementById('alertMessage');
        const submitButton = registerForm.querySelector('.btn-register');

        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nombre = document.getElementById('nombre').value.trim();
            const cedula = document.getElementById('cedula').value.trim();
            const email = document.getElementById('email').value.trim();
            const telefono = document.getElementById('telefono').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            // Validación básica
            if (!nombre || !cedula || !email || !password || !confirmPassword) {
                showAlert('Por favor, completa todos los campos obligatorios', 'error');
                return;
            }

            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Por favor, ingresa un correo electrónico válido', 'error');
                return;
            }

            // Validar cédula (solo números)
            const cedulaRegex = /^[0-9]{10,20}$/;
            if (!cedulaRegex.test(cedula)) {
                showAlert('La cédula debe contener solo números (10-20 dígitos)', 'error');
                return;
            }

            // Validar contraseña
            if (password.length < 8) {
                showAlert('La contraseña debe tener al menos 8 caracteres', 'error');
                return;
            }

            // Validar que las contraseñas coincidan
            if (password !== confirmPassword) {
                showAlert('Las contraseñas no coinciden', 'error');
                return;
            }

            // Enviar datos al servidor
            submitButton.classList.add('loading');
            submitButton.disabled = true;

            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('cedula', cedula);
            formData.append('email', email);
            formData.append('telefono', telefono);
            formData.append('password', password);

            fetch('config/register.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const text = await response.text();
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                        throw new Error('Error al procesar la respuesta del servidor');
                    }
                } else {
                    console.error('Respuesta no es JSON. Content-Type:', contentType);
                    console.error('Respuesta recibida:', text.substring(0, 200));
                    throw new Error('El servidor devolvió una respuesta inválida');
                }
            })
            .then(data => {
                submitButton.classList.remove('loading');
                submitButton.disabled = false;
                
                if (data.success) {
                    showAlert(data.message || 'Registro exitoso. Redirigiendo...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    showAlert(data.message || 'Error al registrarse', 'error');
                }
            })
            .catch(error => {
                submitButton.classList.remove('loading');
                submitButton.disabled = false;
                console.error('Error:', error);
                showAlert('Error de conexión. Por favor, intenta nuevamente.', 'error');
            });
        });

        function showAlert(message, type) {
            alertMessage.textContent = message;
            alertMessage.className = `alert ${type} show`;
            
            setTimeout(() => {
                alertMessage.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>

