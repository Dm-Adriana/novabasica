<?php
session_start();
include 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = md5($_POST['contrasena']);

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = :correo AND contrasena = :contrasena");
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':contrasena', $contrasena);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_role'] = $user['rol'];

        header('Location: index.php');
        exit;
    } else {
        $error = 'Correo electrónico o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Salud - Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 via-blue-300 to-cyan-200">
    <div class="bg-white/10 border border-white/30 rounded-3xl shadow-[0_8px_32px_0_rgba(31,38,135,0.37)] backdrop-blur-md p-8 w-full max-w-sm"
        style="background: rgba(255, 255, 255, 0.1); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.18);">
        <div class="text-center mb-8">
            <div
                class="bg-white/20 p-4 rounded-full w-32 h-32 mx-auto flex items-center justify-center shadow-inner backdrop-blur-md">
                <img src="img/logo.png" alt="Nova Salud" class="w-28 h-auto drop-shadow-lg">
            </div>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-200/30 border-l-4 border-red-500 text-white p-4 mb-6 rounded-md text-sm shadow">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <input type="email" id="correo" name="correo" placeholder="Correo electrónico" required
                    class="w-full px-4 py-2 rounded-full text-white bg-white/20 placeholder-white/80 backdrop-blur-sm border border-white/40 focus:outline-none focus:ring-2 focus:ring-cyan-300 shadow-inner">
            </div>

            <div class="mb-6">
                <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña" required
                    class="w-full px-4 py-2 rounded-full text-white bg-white/20 placeholder-white/80 backdrop-blur-sm border border-white/40 focus:outline-none focus:ring-2 focus:ring-cyan-300 shadow-inner">
            </div>

            <div class="flex items-center justify-between gap-4">
                <button type="reset"
                    class="w-full bg-red-500/60 hover:bg-red-500 text-white py-2 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-times"></i>
                </button>
                <button type="submit"
                    class="w-full bg-green-500/60 hover:bg-green-500 text-white py-2 rounded-full shadow-md transition duration-200">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </form>
    </div>

</body>

</html>