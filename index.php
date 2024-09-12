<?php
session_start();

// Logout y reinicio de sesión
if (isset($_GET['logout'])) {
    session_destroy();
    session_start();
}

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = "";

// Manejar registro
if (isset($_POST['register'])) {
    $user = $_POST['user'];
    $username = $_POST['username'];
    $passwd = $_POST['passwd'];

    // Validar si el usuario ya existe
    $existing_users = file('usuarios.txt', FILE_IGNORE_NEW_LINES);
    foreach ($existing_users as $existing_user) {
        list($existing_user_name, $existing_username) = explode(',', $existing_user);
        if ($existing_user_name === $user || $existing_username === $username) {
            $error_message = "El usuario o nombre de usuario ya existe.";
            break;
        }
    }

    // Si no existe, guardar el nuevo usuario
    if (empty($error_message)) {
        $new_user_data = "$user,$username,$passwd\n";
        file_put_contents('usuarios.txt', $new_user_data, FILE_APPEND);
        echo '<p>Registro exitoso. Ahora puedes iniciar sesión.</p>';
    }
}

// Manejar login
if (isset($_POST['login'])) {
    $user = $_POST['user'];
    $username = $_POST['username'];
    $passwd = $_POST['passwd'];

    // Leer usuarios desde el archivo
    $existing_users = file('usuarios.txt', FILE_IGNORE_NEW_LINES);
    foreach ($existing_users as $existing_user) {
        list($existing_user_name, $existing_username, $existing_password) = explode(',', $existing_user);
        if ($existing_user_name === $user && $existing_username === $username && $existing_password === $passwd) {
            $_SESSION['id_user'] = $user;
            break;
        }
    }

    // Mensaje de error si no coincide
    if (!isset($_SESSION['id_user'])) {
        $error_message = "Usuario, nombre de usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            cursor: url('https://cdn-icons-png.flaticon.com/512/6096/6096136.png'), auto; /* Cursor de perro */
        }

        body {
            font-family: 'Montserrat', sans-serif; /* Cambiar la fuente a Montserrat */
            background: linear-gradient(to bottom right, #4a90e2, #8e44ad);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
            animation: fadeIn 1s ease;
            overflow: hidden; /* Para evitar scroll */
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes roll {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }

        @keyframes flash {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 16px; /* Mayor radio de borde para el contenedor */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 800px; /* Cambiado para que sea fijo y centrado */
            display: flex;
            flex-direction: column; /* Cambiar la dirección a vertical */
            align-items: center; /* Centrar contenido horizontalmente */
            transition: transform 0.3s;
        }

        .form-container {
            padding: 20px;
            text-align: center;
            width: 100%;
        }

        h2 {
            color: #4a90e2;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            margin: 20px 0;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            text-align: left;
        }

        input {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px; /* Radio de borde para los inputs */
            font-size: 16px;
            transition: border 0.3s;
        }

        input:focus {
            border: 1px solid #4a90e2;
            outline: none;
        }

        button {
            padding: 12px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 8px; /* Radio de borde para los botones */
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
            position: relative;
        }

        button:hover {
            background-color: #357ABD;
            transform: translateY(-2px) rotate(2deg);
        }

        button:active {
            animation: roll 0.6s ease;
            background-color: #2980b9;
        }

        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .logout {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #4a90e2;
            transition: color 0.3s;
        }

        .logout:hover {
            color: #357ABD;
            text-decoration: underline;
        }

        .image-container {
            text-align: center;
            margin-top: 20px;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 12px; /* Radio de borde para la imagen */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: bounceIn 0.5s ease; /* Nueva animación de rebote */
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
            }
        }

        .reg-button {
            margin-top: 20px;
            text-align: center;
        }

        .reg-button button {
            background-color: #28a745;
        }

        .reg-button button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container" onclick="handleClick(event)">
    <div class="form-container">
        <?php
        // Mostrar mensaje de error específico
        if ($error_message != "") {
            echo '<p class="error-message">' . $error_message . '</p>';
        }

        // Si no se ha iniciado sesión
        if (!isset($_SESSION['id_user'])) {
            echo '<h2>Iniciar sesión</h2>';
            echo '<form method="post">'
                . '<label for="user">Usuario:</label>'
                . '<input id="user" name="user" type="text" required>'
                . '<label for="username">Nombre de usuario:</label>'
                . '<input id="username" name="username" type="text" required>'
                . '<label for="passwd">Contraseña:</label>'
                . '<input id="passwd" name="passwd" type="password" required>'
                . '<button name="login" type="submit">Iniciar sesión</button>'
                . '</form>';

            echo '<h2>Registrarse</h2>';
            echo '<form method="post">'
                . '<label for="user">Usuario:</label>'
                . '<input id="user" name="user" type="text" required>'
                . '<label for="username">Nombre de usuario:</label>'
                . '<input id="username" name="username" type="text" required>'
                . '<label for="passwd">Contraseña:</label>'
                . '<input id="passwd" name="passwd" type="password" required>'
                . '<button name="register" type="submit">Registrarse</button>'
                . '</form>';
        } else {
            // Mostrar la imagen correspondiente al usuario
            $nombre_usuario = $_SESSION['id_user'];
            echo '<div class="image-container">'
                . '<img src="https://via.placeholder.com/300x200.png?text=' . urlencode($nombre_usuario) . '" alt="Imagen del usuario"/>'
                . '</div>';
            echo '<a class="logout" href="?logout=1">Cerrar sesión</a>';
        }
        ?>
    </div>

    <div class="reg-button">
        <a href="registro.php"><button>Ver Registros</button></a>
    </div>
</div>

<script>
    function handleClick(event) {
        const container = event.currentTarget;
        container.style.animation = 'flash 0.5s ease';
        setTimeout(() => {
            container.style.animation = '';
        }, 500);
    }

    // Agregar un perro que sigue el cursor
    document.addEventListener('mousemove', (event) => {
        let dog = document.getElementById('dog');
        if (!dog) {
            dog = document.createElement('img');
            dog.src = 'https://cdn-icons-png.flaticon.com/512/6096/6096136.png'; // Imagen de perro
            dog.style.position = 'absolute';
            dog.style.width = '50px';
            dog.style.height = '50px';
            dog.style.pointerEvents = 'none'; // Para que el perro no interfiera con otros eventos
            dog.id = 'dog';
            document.body.appendChild(dog);
        }
        dog.style.left = event.pageX + 'px';
        dog.style.top = event.pageY + 'px';
    });
</script>

</body>
</html>
