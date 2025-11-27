<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Restablecer Contraseña - Sistema de Reciclaje</title>
    
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
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
            padding: 10px;
            overflow: hidden;
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
        }

        .password-toggle:hover {
            color: #2c9f5f;
        }

        .btn-submit {
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
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 159, 95, 0.5);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #2c9f5f;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

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

        .btn-submit.loading::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-left: 10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .password-strength {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
        }

        .password-strength.weak {
            color: #d32f2f;
        }

        .password-strength.medium {
            color: #f57c00;
        }

        .password-strength.strong {
            color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="assets/img/logo.jpg" alt="HNOSYÁNEZ S.A.">
            </div>
            <h1>Restablecer Contraseña</h1>
            <p>Ingresa tu nueva contraseña</p>
        </div>

        <div id="alertMessage" class="alert"></div>

        <form id="resetPasswordForm">
            <input type="hidden" id="token" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
            
            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Mínimo 8 caracteres"
                        required
                        autocomplete="new-password"
                        minlength="8"
                    >
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="passwordStrength" class="password-strength"></div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        class="form-control" 
                        placeholder="Confirma tu contraseña"
                        required
                        autocomplete="new-password"
                        minlength="8"
                    >
                    <button type="button" class="password-toggle" id="togglePasswordConfirm">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                Restablecer Contraseña
            </button>
        </form>

        <div class="back-link">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Volver al inicio de sesión</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('resetPasswordForm');
        const alertMessage = document.getElementById('alertMessage');
        const submitBtn = document.getElementById('submitBtn');
        const token = document.getElementById('token').value;
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirm');
        const passwordStrength = document.getElementById('passwordStrength');

        // Verificar token al cargar la página
        if (!token) {
            showAlert('Token inválido o faltante', 'error');
            form.style.display = 'none';
        } else {
            // Verificar que el token sea válido
            fetch(`config/password-reset.php?action=verificar&token=${encodeURIComponent(token)}`)
                .then(async response => {
                    const text = await response.text();
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return JSON.parse(text);
                    } else {
                        throw new Error('Respuesta inválida del servidor');
                    }
                })
                .then(data => {
                    if (!data.success) {
                        showAlert(data.message || 'Token inválido o expirado', 'error');
                        form.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al verificar el token', 'error');
                    form.style.display = 'none';
                });
        }

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Validar fortaleza de contraseña
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = '';
            let strengthClass = '';
            
            if (password.length === 0) {
                strength = '';
            } else if (password.length < 8) {
                strength = 'Muy débil (mínimo 8 caracteres)';
                strengthClass = 'weak';
            } else if (password.length < 12 && !/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                strength = 'Débil (usa mayúsculas, minúsculas y números)';
                strengthClass = 'medium';
            } else if (password.length >= 12 && /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                strength = 'Fuerte';
                strengthClass = 'strong';
            } else {
                strength = 'Media';
                strengthClass = 'medium';
            }
            
            passwordStrength.textContent = strength;
            passwordStrength.className = 'password-strength ' + strengthClass;
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = passwordInput.value;
            const passwordConfirm = passwordConfirmInput.value;

            if (!password || !passwordConfirm) {
                showAlert('Por favor, completa todos los campos', 'error');
                return;
            }

            if (password.length < 8) {
                showAlert('La contraseña debe tener al menos 8 caracteres', 'error');
                return;
            }

            if (password !== passwordConfirm) {
                showAlert('Las contraseñas no coinciden', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Restableciendo...';

            const formData = new FormData();
            formData.append('token', token);
            formData.append('password', password);
            formData.append('action', 'restablecer');

            fetch('config/password-reset.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const text = await response.text();
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return JSON.parse(text);
                } else {
                    throw new Error('Respuesta inválida del servidor');
                }
            })
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.textContent = 'Restablecer Contraseña';
                
                if (data.success) {
                    showAlert(data.message + '. Redirigiendo al inicio de sesión...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    showAlert(data.message || 'Error al restablecer la contraseña', 'error');
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.textContent = 'Restablecer Contraseña';
                console.error('Error:', error);
                showAlert('Error de conexión. Por favor, intenta nuevamente.', 'error');
            });
        });

        function showAlert(message, type) {
            alertMessage.textContent = message;
            alertMessage.className = `alert ${type} show`;
            
            if (type === 'success') {
                setTimeout(() => {
                    alertMessage.classList.remove('show');
                }, 5000);
            } else {
                setTimeout(() => {
                    alertMessage.classList.remove('show');
                }, 5000);
            }
        }
    </script>
</body>
</html>

