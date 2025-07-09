<?php
// Include security configuration first to get session settings function
require_once('security.php');

// Configure session settings before starting session
configure_session_settings();

// Start session
session_start();

// Include tools after session is started
require_once('tools.php');

// Generate CSRF token
$csrf_token = SecurityManager::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TransExpress Guatemala</title>

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            min-height: auto;
            display: block;
            padding: 40px;
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .form-label .required {
            color: #dc3545;
        }

        .right-panel {
            width: 100%;
            padding: 0;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo img {
            max-height: 60px;
        }

        .social-login {
            text-align: center;
            margin-bottom: 30px;
        }

        .social-login h3 {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-btn:hover {
            transform: translateY(-2px);
        }

        .facebook { background: #3b5998; }
        .twitter { background: #1da1f2; }
        .linkedin { background: #0077b5; }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
            color: #666;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e5e9;
        }

        .divider span {
            background: white;
            padding: 0 20px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #4285f4;
            box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.1);
        }

        .form-control::placeholder {
            color: #999;
        }

        .form-control.error {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .form-row {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 25px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #4285f4;
        }

        .checkbox-group label {
            font-size: 14px;
            color: #666;
            cursor: pointer;
        }

        .forgot-password {
            font-size: 14px;
            color: #666;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-password:hover {
            color: #4285f4;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #4285f4;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 20px;
        }

        .login-btn:hover {
            background: #3367d6;
        }

        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .register-link {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .register-link a {
            color: #4285f4;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .footer {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-text {
            font-size: 14px;
        }

        .footer-social {
            display: flex;
            gap: 15px;
        }

        .footer-social a {
            color: white;
            font-size: 18px;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .footer-social a:hover {
            opacity: 0.8;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .alert-info {
            background: #cce7ff;
            border: 1px solid #99d3ff;
            color: #0c5460;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .login-wrapper {
                max-width: 350px;
                padding: 30px;
            }
            
            .footer {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>

</head>
<body>
    <div class="main-container">
        <div class="login-wrapper">
            <div class="right-panel">
                <div class="logo">
                    <img src="imagenes/logo.png" alt="TransExpress Logo">
                </div>
                
                <!-- Error Messages -->
                <?php
                if (isset($_GET['error'])) {
                    $alertClass = 'alert-danger';
                    $message = '';
                    
                    switch($_GET['error']) {
                        case 'invalid_credentials':
                            $message = "Credenciales inválidas. Verifique su usuario y contraseña.";
                            break;
                        case 'empty_fields':
                            $alertClass = 'alert-warning';
                            $message = "Todos los campos son obligatorios.";
                            break;
                        case 'locked':
                            $remaining = isset($_GET['remaining']) ? (int)$_GET['remaining'] : 0;
                            $minutes = floor($remaining / 60);
                            $seconds = $remaining % 60;
                            $timeText = $minutes > 0 ? "{$minutes}m {$seconds}s" : "{$seconds}s";
                            $message = "Cuenta bloqueada temporalmente. Intente nuevamente en {$timeText}.";
                            break;
                        case 'service_unavailable':
                            $message = "Servicio no disponible temporalmente. Por favor, intente más tarde.";
                            break;
                        case 'invalid_token':
                            $message = "Token de seguridad inválido. Recargue la página e intente nuevamente.";
                            break;
                        case 'timeout':
                            $alertClass = 'alert-info';
                            $message = "Su sesión ha expirado por seguridad.";
                            break;
                        case 'system_error':
                            $message = "Error del sistema. Contacte al administrador si el problema persiste.";
                            break;
                        default:
                            $message = "Se ha producido un error inesperado.";
                    }
                    
                    echo "<div class='alert $alertClass'>$message</div>";
                }
                
                // Legacy support
                if (isset($_REQUEST['login'])) {
                    if ($_REQUEST['login'] == "0") {
                        echo "<div class='alert alert-warning'>Usuario no registrado en el sistema.</div>";
                    }
                    if ($_REQUEST['login'] == "2") {
                        echo "<div class='alert alert-warning'>Usuario registrado pero sin permisos de acceso.</div>";
                    }
                }
                ?>
                
                <form id="login" name="login" method="post" action="login.php" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label class="form-label" for="usuarioLogin">Usuario <span class="required">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="usuarioLogin" 
                               name="usuarioLogin" 
                               placeholder="usuario"
                               maxlength="50" 
                               required
                               autocomplete="username">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="passwordLogin">Password <span class="required">*</span></label>
                        <input type="password" 
                               class="form-control" 
                               id="passwordLogin" 
                               name="passwordLogin" 
                               placeholder="Password"
                               maxlength="100" 
                               required
                               autocomplete="current-password">
                    </div>
                    
                    <div class="form-row">
                        <div class="checkbox-group">
                            <input type="checkbox" id="rememberMe" name="rememberMe">
                            <label for="rememberMe">Keep me logged in</label>
                        </div>
                    </div>
                    
                    <button type="submit" id="cmdLogin" class="login-btn">
                        Log in now
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <div class="footer-text">
            Copyright © 2025. All rights reserved.
        </div>
        <div class="footer-social">
            <a href="#" onclick="alert('Social link not implemented')"><i class="bi bi-facebook"></i></a>
            <a href="#" onclick="alert('Social link not implemented')"><i class="bi bi-twitter"></i></a>
            <a href="#" onclick="alert('Social link not implemented')"><i class="bi bi-google"></i></a>
            <a href="#" onclick="alert('Social link not implemented')"><i class="bi bi-linkedin"></i></a>
        </div>
    </div>

    <!-- Bootstrap 5.3.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        $(function() {
            // Enhanced form validation and submission
            $("#cmdLogin").click(function(e) {
                e.preventDefault();
                
                const usuario = $("#usuarioLogin");
                const password = $("#passwordLogin");
                const btn = $(this);
                let isValid = true;
                
                // Reset validation states
                $(".form-control").removeClass("error");
                
                // Simple validation
                if (usuario.val().trim() === "") {
                    usuario.addClass("error");
                    alert("Por favor ingrese su usuario");
                    usuario.focus();
                    isValid = false;
                } else if (password.val().trim() === "") {
                    password.addClass("error");
                    alert("Por favor ingrese su contraseña");
                    password.focus();
                    isValid = false;
                }
                
                if (isValid) {
                    btn.addClass('loading');
                    btn.prop('disabled', true);
                    btn.text('LOGGING IN...');
                    
                    setTimeout(() => {
                        $("#login").submit();
                    }, 500);
                }
            });
            
            // Enter key support
            $(".form-control").keydown(function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#cmdLogin").click();
                }
            });
            
            // Auto-focus first input
            $("#usuarioLogin").focus();
            
            // Remove error styling on input
            $(".form-control").on("input", function() {
                $(this).removeClass("error");
            });
            
            // Animate elements on load
            setTimeout(() => {
                $(".login-wrapper").css("opacity", "1");
            }, 100);
        });
    </script>
</body>
</html>
