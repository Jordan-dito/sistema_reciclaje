<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registro - Sistema de Reciclaje</title>
    
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
            background: linear-gradient(135deg, rgba(44, 159, 95, 0.85) 0%, rgba(30, 126, 74, 0.85) 50%, rgba(13, 90, 47, 0.85) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-y: auto;
        }

        /* Efectos de fondo animado */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(76, 175, 80, 0.25) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(46, 125, 50, 0.25) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(27, 94, 32, 0.15) 0%, transparent 50%);
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
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,0.02) 35px, rgba(255,255,255,0.02) 70px),
                repeating-linear-gradient(-45deg, transparent, transparent 35px, rgba(255,255,255,0.02) 35px, rgba(255,255,255,0.02) 70px);
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
            backdrop-filter: blur(25px);
            border-radius: 24px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.6) inset;
            width: 100%;
            max-width: 420px;
            padding: 35px 30px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
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
            margin-bottom: 30px;
        }

        .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 15px 35px rgba(102, 126, 234, 0.4),
                0 5px 15px rgba(0, 0, 0, 0.1);
            animation: floatLogo 3s ease-in-out infinite;
            position: relative;
            padding: 8px;
            overflow: hidden;
        }

        .logo-container::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 24px;
            background: linear-gradient(135deg, #2c9f5f, #1e7e4a, #0d5a2f);
            opacity: 0.3;
            filter: blur(10px);
            z-index: -1;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 16px;
        }

        .register-header h1 {
            background: linear-gradient(135deg, #2c9f5f 0%, #1e7e4a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .register-header p {
            color: #6b7280;
            font-size: 14px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper .form-control,
        .input-wrapper .password-toggle,
        .input-wrapper i {
            pointer-events: auto;
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 17px;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .form-control {
            width: 100%;
            padding: 13px 13px 13px 45px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f9fafb;
            outline: none;
            color: #111827;
        }
        
        #cedula.form-control {
            padding-right: 80px;
        }
        
        #password.form-control,
        #confirmPassword.form-control {
            padding-right: 80px;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-control:focus {
            border-color: #2c9f5f;
            background: #ffffff;
            box-shadow: 
                0 0 0 4px rgba(44, 159, 95, 0.1),
                0 4px 12px rgba(44, 159, 95, 0.15);
            transform: translateY(-1px);
        }

        .form-control:focus ~ i,
        .input-wrapper:has(.form-control:focus) i {
            color: #2c9f5f;
            transform: translateY(-50%) scale(1.1);
        }

        .form-control.valid {
            border-color: #10b981;
        }

        .form-control.invalid {
            border-color: #ef4444;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 17px;
            padding: 8px;
            margin: 0;
            transition: all 0.3s ease;
            border-radius: 8px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            min-width: 35px;
            min-height: 35px;
            pointer-events: auto;
            -webkit-tap-highlight-color: transparent;
        }
        
        .password-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(44, 159, 95, 0.2);
        }
        
        .password-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }

        .password-toggle:hover {
            color: #2c9f5f;
            background: rgba(44, 159, 95, 0.1);
            transform: translateY(-50%) scale(1.1);
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            display: none;
        }

        .password-strength.show {
            display: block;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #ef4444;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: #f59e0b;
        }

        .password-strength-bar.strong {
            width: 100%;
            background: #10b981;
        }

        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2c9f5f 0%, #1e7e4a 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 10px 25px rgba(44, 159, 95, 0.4),
                0 0 0 0 rgba(44, 159, 95, 0.5);
            position: relative;
            overflow: hidden;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-register:hover::before {
            width: 400px;
            height: 400px;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 15px 35px rgba(44, 159, 95, 0.5),
                0 0 0 8px rgba(44, 159, 95, 0.1);
        }

        .btn-register:active {
            transform: translateY(-1px);
        }

        .btn-register span {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-link {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }

        .login-link a {
            color: #2c9f5f;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #2c9f5f, #1e7e4a);
            transition: width 0.3s ease;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .login-link a:hover {
            color: #1e7e4a;
        }

        /* Mensajes de error/éxito */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: none;
            font-weight: 500;
            position: relative;
            animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .alert.error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
            border: 2px solid #fca5a5;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        }

        .alert.success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #059669;
            border: 2px solid #6ee7b7;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
        }

        .alert.show {
            display: block;
        }

        .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 12px 0 0 12px;
        }

        .alert.error::before {
            background: #dc2626;
        }

        .alert.success::before {
            background: #059669;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-15px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
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

        /* Indicador de validación */
        .validation-icon {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 3;
            pointer-events: none;
        }
        
        .input-wrapper:has(#cedula) .validation-icon {
            right: 50px;
        }
        
        .input-wrapper:has(#password) .validation-icon,
        .input-wrapper:has(#confirmPassword) .validation-icon {
            right: 50px;
        }

        .validation-icon.show {
            opacity: 1;
        }

        .validation-icon.valid {
            color: #10b981;
        }

        .validation-icon.invalid {
            color: #ef4444;
        }
        
        .error-message {
            display: block;
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            min-height: 18px;
            font-weight: 500;
        }
        
        .error-message:empty {
            display: none;
        }
        
        .success-message {
            display: block;
            color: #10b981;
            font-size: 12px;
            margin-top: 5px;
            min-height: 18px;
            font-weight: 500;
        }
        
        .success-message:empty {
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .register-container {
                padding: 30px 25px;
                border-radius: 20px;
                max-width: 100%;
            }

            .register-header h1 {
                font-size: 24px;
            }

            .logo-container {
                width: 85px;
                height: 85px;
                padding: 6px;
            }
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 28px 20px;
            }

            .form-control {
                padding: 12px 12px 12px 42px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="logo-container">
                <img src="assets/img/logo.jpg" alt="HNOSYÁNEZ S.A.">
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
                    <span class="validation-icon" id="nombreValidation"></span>
                </div>
                <small class="error-message" id="nombreError"></small>
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
                        placeholder="Ingresa tu cédula (10 dígitos)"
                        required
                        maxlength="10"
                        pattern="[0-9]{10}"
                        title="Solo números, exactamente 10 dígitos"
                    >
                    <span class="validation-icon" id="cedulaValidation"></span>
                </div>
                <small class="error-message" id="cedulaError"></small>
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
                    <span class="validation-icon" id="passwordValidation"></span>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Mostrar/Ocultar contraseña" tabindex="0">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div class="password-strength-bar"></div>
                </div>
                <small class="error-message" id="passwordError"></small>
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
                    <span class="validation-icon" id="confirmPasswordValidation"></span>
                    <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="Mostrar/Ocultar confirmación de contraseña" tabindex="0">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <small class="error-message" id="confirmPasswordError"></small>
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
        // Función para validar cédula ecuatoriana
        function validarCedulaEcuatoriana(cedula) {
            cedula = cedula.replace(/[^0-9]/g, '');
            
            if (cedula.length !== 10) {
                return { valid: false, message: 'La cédula debe tener exactamente 10 dígitos' };
            }
            
            if (/^(\d)\1{9}$/.test(cedula)) {
                return { valid: false, message: 'La cédula no puede tener todos los dígitos iguales' };
            }
            
            var digitos = cedula.substring(0, 9).split('').map(Number);
            var verificador = parseInt(cedula.charAt(9));
            var coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
            var suma = 0;
            
            for (var i = 0; i < 9; i++) {
                var producto = digitos[i] * coeficientes[i];
                if (producto > 9) {
                    producto = Math.floor(producto / 10) + (producto % 10);
                }
                suma += producto;
            }
            
            var residuo = suma % 10;
            var digitoCalculado = (residuo === 0) ? 0 : (10 - residuo);
            
            if (digitoCalculado === verificador) {
                return { valid: true, message: 'Cédula válida' };
            } else {
                return { valid: false, message: 'La cédula no es válida' };
            }
        }

        // Función para mostrar mensaje de error
        function mostrarError(campoId, mensaje) {
            const errorElement = document.getElementById(campoId + 'Error');
            if (errorElement) {
                errorElement.textContent = mensaje;
                errorElement.className = 'error-message';
            }
        }

        // Función para mostrar mensaje de éxito
        function mostrarExito(campoId, mensaje) {
            const errorElement = document.getElementById(campoId + 'Error');
            if (errorElement) {
                errorElement.textContent = mensaje;
                errorElement.className = 'success-message';
            }
        }

        // Función para limpiar mensaje
        function limpiarMensaje(campoId) {
            const errorElement = document.getElementById(campoId + 'Error');
            if (errorElement) {
                errorElement.textContent = '';
            }
        }

        // Todo el código cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility - ENFOQUE SIMPLE Y DIRECTO
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');

            // Toggle para contraseña principal
            if (togglePassword) {
                togglePassword.onclick = function() {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.querySelector('i').classList.remove('fa-eye');
                        this.querySelector('i').classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        this.querySelector('i').classList.remove('fa-eye-slash');
                        this.querySelector('i').classList.add('fa-eye');
                    }
                };
            }

            // Toggle para confirmar contraseña
            if (toggleConfirmPassword) {
                toggleConfirmPassword.onclick = function() {
                    if (confirmPasswordInput.type === 'password') {
                        confirmPasswordInput.type = 'text';
                        this.querySelector('i').classList.remove('fa-eye');
                        this.querySelector('i').classList.add('fa-eye-slash');
                    } else {
                        confirmPasswordInput.type = 'password';
                        this.querySelector('i').classList.remove('fa-eye-slash');
                        this.querySelector('i').classList.add('fa-eye');
                    }
                };
            }

            // Validación en tiempo real
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordStrengthBar = passwordStrength ? passwordStrength.querySelector('.password-strength-bar') : null;
            const passwordValidation = document.getElementById('passwordValidation');
            const confirmPasswordValidation = document.getElementById('confirmPasswordValidation');
            const cedulaInput = document.getElementById('cedula');
            const cedulaValidation = document.getElementById('cedulaValidation');

            // Validar fuerza de contraseña
            function checkPasswordStrength(password) {
                if (!passwordStrength || !passwordStrengthBar) return;
                
                let strength = 0;
                if (password.length >= 8) strength++;
                if (password.length >= 12) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                passwordStrength.classList.add('show');
                
                if (strength <= 2) {
                    passwordStrengthBar.className = 'password-strength-bar weak';
                } else if (strength <= 3) {
                    passwordStrengthBar.className = 'password-strength-bar medium';
                } else {
                    passwordStrengthBar.className = 'password-strength-bar strong';
                }
            }

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    limpiarMensaje('password');
                    
                    if (password.length > 0) {
                        checkPasswordStrength(password);
                        if (password.length >= 8) {
                            this.classList.add('valid');
                            this.classList.remove('invalid');
                            if (passwordValidation) {
                                passwordValidation.className = 'validation-icon show valid';
                                passwordValidation.innerHTML = '<i class="fas fa-check-circle"></i>';
                            }
                            mostrarExito('password', '✓ Contraseña válida');
                        } else {
                            this.classList.remove('valid');
                            this.classList.add('invalid');
                            if (passwordValidation) {
                                passwordValidation.className = 'validation-icon show invalid';
                                passwordValidation.innerHTML = '<i class="fas fa-times-circle"></i>';
                            }
                            mostrarError('password', 'La contraseña debe tener al menos 8 caracteres');
                        }
                    } else {
                        this.classList.remove('valid', 'invalid');
                        if (passwordValidation) {
                            passwordValidation.className = 'validation-icon';
                        }
                        if (passwordStrength) {
                            passwordStrength.classList.remove('show');
                        }
                    }
                    
                    // Validar confirmación si hay texto
                    if (confirmPasswordInput && confirmPasswordInput.value.length > 0) {
                        validatePasswordMatch();
                    }
                });
            }

            function validatePasswordMatch() {
                if (!passwordInput || !confirmPasswordInput) return;
                
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                limpiarMensaje('confirmPassword');
                
                if (confirmPassword.length > 0) {
                    if (password === confirmPassword && password.length >= 8) {
                        confirmPasswordInput.classList.add('valid');
                        confirmPasswordInput.classList.remove('invalid');
                        if (confirmPasswordValidation) {
                            confirmPasswordValidation.className = 'validation-icon show valid';
                            confirmPasswordValidation.innerHTML = '<i class="fas fa-check-circle"></i>';
                        }
                        mostrarExito('confirmPassword', '✓ Las contraseñas coinciden');
                    } else if (password !== confirmPassword) {
                        confirmPasswordInput.classList.remove('valid');
                        confirmPasswordInput.classList.add('invalid');
                        if (confirmPasswordValidation) {
                            confirmPasswordValidation.className = 'validation-icon show invalid';
                            confirmPasswordValidation.innerHTML = '<i class="fas fa-times-circle"></i>';
                        }
                        mostrarError('confirmPassword', '✗ Las contraseñas no coinciden');
                    } else {
                        confirmPasswordInput.classList.remove('valid');
                        confirmPasswordInput.classList.add('invalid');
                        if (confirmPasswordValidation) {
                            confirmPasswordValidation.className = 'validation-icon show invalid';
                            confirmPasswordValidation.innerHTML = '<i class="fas fa-times-circle"></i>';
                        }
                        mostrarError('confirmPassword', 'La contraseña debe tener al menos 8 caracteres');
                    }
                } else {
                    confirmPasswordInput.classList.remove('valid', 'invalid');
                    if (confirmPasswordValidation) {
                        confirmPasswordValidation.className = 'validation-icon';
                    }
                }
            }

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', validatePasswordMatch);
            }

            // Validación de cédula en tiempo real
            if (cedulaInput) {
                cedulaInput.addEventListener('input', function() {
                    let cedula = this.value.replace(/[^0-9]/g, '');
                    this.value = cedula;
                    
                    limpiarMensaje('cedula');
                    
                    if (cedula.length === 0) {
                        this.classList.remove('valid', 'invalid');
                        if (cedulaValidation) {
                            cedulaValidation.className = 'validation-icon';
                            cedulaValidation.innerHTML = '';
                        }
                        return;
                    }
                    
                    if (cedula.length === 10) {
                        const resultado = validarCedulaEcuatoriana(cedula);
                        
                        if (resultado.valid) {
                            this.classList.add('valid');
                            this.classList.remove('invalid');
                            if (cedulaValidation) {
                                cedulaValidation.className = 'validation-icon show valid';
                                cedulaValidation.innerHTML = '<i class="fas fa-check-circle"></i>';
                            }
                            mostrarExito('cedula', '✓ Cédula válida');
                        } else {
                            this.classList.remove('valid');
                            this.classList.add('invalid');
                            if (cedulaValidation) {
                                cedulaValidation.className = 'validation-icon show invalid';
                                cedulaValidation.innerHTML = '<i class="fas fa-times-circle"></i>';
                            }
                            mostrarError('cedula', '✗ ' + resultado.message);
                        }
                    } else {
                        this.classList.remove('valid');
                        this.classList.add('invalid');
                        if (cedulaValidation) {
                            cedulaValidation.className = 'validation-icon show invalid';
                            cedulaValidation.innerHTML = '<i class="fas fa-times-circle"></i>';
                        }
                        mostrarError('cedula', 'La cédula debe tener 10 dígitos');
                    }
                });
            }

            // Validación de nombre (solo letras y espacios)
            const nombreInput = document.getElementById('nombre');
            const nombreValidation = document.getElementById('nombreValidation');
            
            if (nombreInput) {
                nombreInput.addEventListener('input', function() {
                    // Solo permitir letras, espacios y caracteres especiales del español (á, é, í, ó, ú, ñ, etc.)
                    let valor = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '');
                    this.value = valor;
                    
                    limpiarMensaje('nombre');
                    
                    if (valor.length > 0) {
                        // Validar que no sean solo espacios
                        if (valor.trim().length === 0) {
                            this.classList.remove('valid');
                            this.classList.add('invalid');
                            if (nombreValidation) {
                                nombreValidation.className = 'validation-icon show invalid';
                                nombreValidation.innerHTML = '<i class="fas fa-times-circle"></i>';
                            }
                            mostrarError('nombre', 'El nombre no puede contener solo espacios');
                            return;
                        }
                        
                        // Validar que tenga al menos 2 caracteres
                        if (valor.trim().length < 2) {
                            this.classList.remove('valid');
                            this.classList.add('invalid');
                            if (nombreValidation) {
                                nombreValidation.className = 'validation-icon show invalid';
                                nombreValidation.innerHTML = '<i class="fas fa-times-circle"></i>';
                            }
                            mostrarError('nombre', 'El nombre debe tener al menos 2 caracteres');
                            return;
                        }
                        
                        // Validar que solo contenga letras y espacios
                        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(valor)) {
                            this.classList.remove('valid');
                            this.classList.add('invalid');
                            if (nombreValidation) {
                                nombreValidation.className = 'validation-icon show invalid';
                                nombreValidation.innerHTML = '<i class="fas fa-times-circle"></i>';
                            }
                            mostrarError('nombre', 'El nombre solo puede contener letras y espacios');
                            return;
                        }
                        
                        // Nombre válido
                        this.classList.add('valid');
                        this.classList.remove('invalid');
                        if (nombreValidation) {
                            nombreValidation.className = 'validation-icon show valid';
                            nombreValidation.innerHTML = '<i class="fas fa-check-circle"></i>';
                        }
                        mostrarExito('nombre', '✓ Nombre válido');
                    } else {
                        this.classList.remove('valid', 'invalid');
                        if (nombreValidation) {
                            nombreValidation.className = 'validation-icon';
                            nombreValidation.innerHTML = '';
                        }
                    }
                });
            }

            // Validación de otros campos
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                if (input.id !== 'password' && input.id !== 'confirmPassword' && input.id !== 'cedula' && input.id !== 'nombre') {
                    input.addEventListener('blur', function() {
                        if (this.value.trim().length > 0) {
                            this.classList.add('valid');
                            this.classList.remove('invalid');
                        } else {
                            this.classList.remove('valid', 'invalid');
                        }
                    });
                }
            });
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
            
            // Validar nombre (solo letras y espacios)
            const nombreLimpio = nombre.trim();
            if (nombreLimpio.length < 2) {
                mostrarError('nombre', 'El nombre debe tener al menos 2 caracteres');
                document.getElementById('nombre').focus();
                return;
            }
            
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(nombreLimpio)) {
                mostrarError('nombre', 'El nombre solo puede contener letras y espacios');
                document.getElementById('nombre').focus();
                return;
            }

            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showAlert('Por favor, ingresa un correo electrónico válido', 'error');
                return;
            }

            // Validar cédula ecuatoriana
            const cedulaLimpia = cedula.replace(/[^0-9]/g, '');
            if (cedulaLimpia.length !== 10) {
                showAlert('La cédula debe tener exactamente 10 dígitos', 'error');
                return;
            }
            
            // Validar con algoritmo
            const resultadoCedula = validarCedulaEcuatoriana(cedulaLimpia);
            if (!resultadoCedula.valid) {
                showAlert(resultadoCedula.message, 'error');
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
            formData.append('cedula', cedula.replace(/[^0-9]/g, ''));
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

