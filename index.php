<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Iniciar Sesión - Sistema de Reciclaje</title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/img/logo.jpg" type="image/jpeg">
    <link rel="shortcut icon" href="assets/img/logo.jpg" type="image/jpeg">
    
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
            overflow: hidden;
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

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
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

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(44, 159, 95, 0.4);
            animation: pulse 2s ease-in-out infinite;
            padding: 10px;
            overflow: hidden;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }

        .login-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 25px;
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

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #2c9f5f;
        }

        .remember-me label {
            color: #666;
            cursor: pointer;
            user-select: none;
        }

        .forgot-password {
            color: #2c9f5f;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: #1e7e4a;
            text-decoration: underline;
        }

        .btn-login {
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

        .btn-login::before {
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

        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 159, 95, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login span {
            position: relative;
            z-index: 1;
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }

        .signup-link a {
            color: #2c9f5f;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .signup-link a:hover {
            color: #1e7e4a;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                border-radius: 15px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .logo-container {
                width: 90px;
                height: 90px;
                padding: 8px;
            }

            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }

        /* Animación de carga */
        .btn-login.loading {
            pointer-events: none;
        }

        .btn-login.loading::after {
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
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="assets/img/logo.jpg" alt="HNOSYÁNEZ S.A.">
            </div>
            <h1>Bienvenido</h1>
            <p>Inicia sesión para continuar</p>
        </div>

        <div id="alertMessage" class="alert"></div>

        <form id="loginForm" action="Dashboard.php" method="POST">
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
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Ingresa tu contraseña"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Recordarme</label>
                </div>
                <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-login">
                <span>Iniciar Sesión</span>
            </button>
        </form>

        <div class="signup-link">
            ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Form submission
        const loginForm = document.getElementById('loginForm');
        const alertMessage = document.getElementById('alertMessage');
        const submitButton = loginForm.querySelector('.btn-login');

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Validación básica
            if (!email || !password) {
                showAlert('Por favor, completa todos los campos', 'error');
                return;
            }

            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Por favor, ingresa un correo electrónico válido', 'error');
                return;
            }

            // Enviar datos al servidor
            submitButton.classList.add('loading');
            submitButton.disabled = true;

            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);

            fetch('config/login.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                // Leer la respuesta como texto primero
                const text = await response.text();
                
                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                        console.error('Respuesta recibida:', text.substring(0, 200));
                        throw new Error('Error al procesar la respuesta del servidor');
                    }
                } else {
                    // Si no es JSON, mostrar el error
                    console.error('Respuesta no es JSON. Content-Type:', contentType);
                    console.error('Respuesta recibida:', text.substring(0, 200));
                    throw new Error('El servidor devolvió una respuesta inválida');
                }
            })
            .then(data => {
                submitButton.classList.remove('loading');
                submitButton.disabled = false;
                
                if (data.success) {
                    showAlert(data.message || 'Inicio de sesión exitoso. Redirigiendo...', 'success');
                    setTimeout(() => {
                        // Redirigir según el rol
                        const rol = data.usuario?.rol || '';
                        if (rol === 'Administrador' || rol === 'Gerente') {
                            window.location.href = 'Dashboard.php';
                        } else {
                            window.location.href = 'Dashboard.php';
                        }
                    }, 1000);
                } else {
                    showAlert(data.message || 'Error al iniciar sesión', 'error');
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
            }, 3000);
        }

        // Forgot password handler
        document.querySelector('.forgot-password').addEventListener('click', function(e) {
            e.preventDefault();
            showAlert('Funcionalidad de recuperación de contraseña próximamente', 'success');
        });

        // Signup link handler - Removido para permitir navegación a register.php
    </script>
</body>
</html>

