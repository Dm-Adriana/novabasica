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
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-blue-600">Nova Salud</h1>
                <p class="text-gray-600">Sistema de Gestión de Farmacia</p>
            </div>
            
            <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p><?php echo $error; ?></p>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="correo" class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-6">
                    <label for="contrasena" class="block text-gray-700 font-medium mb-2">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>Usuario de prueba: adriana_17@novasalud.com</p>
                <p>Contraseña: admin123</p>
            </div>
        </div>
    </div>
</body>
</html>