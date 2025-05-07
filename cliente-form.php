<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cliente = [
    'id' => '',
    'nombre' => '',
    'correo' => '',
    'telefono' => '',
    'direccion' => ''
];

$isEdit = false;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $isEdit = true;
    
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        header('Location: clientes.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    
    try {
        if ($isEdit) {
            $stmt = $conn->prepare("UPDATE clientes SET nombre = :nombre, correo = :correo, telefono = :telefono, direccion = :direccion WHERE id = :id");
            $stmt->bindParam(':id', $cliente['id']);
        } else {
            $stmt = $conn->prepare("INSERT INTO clientes (nombre, correo, telefono, direccion) VALUES (:nombre, :correo, :telefono, :direccion)");
        }
        
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->execute();
        
        header('Location: clientes.php?success=1');
        exit;
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Salud - <?php echo $isEdit ? 'Editar' : 'Nuevo'; ?> Cliente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto px-6 py-8">
        <div class="flex items-center mb-6">
            <a href="clientes.php" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <h1 class="text-4xl font-bold text-gray-900"><?php echo $isEdit ? 'Editar' : 'Nuevo'; ?> Cliente</h1>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
            <p><?php echo $error; ?></p>
        </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-xl shadow-xl p-8 border border-gray-200">
            <form method="POST" action="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-6">
                        <label for="nombre" class="block text-gray-700 font-medium mb-2">Nombre</label>
                        <div class="relative">
                            <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($cliente['nombre']); ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 transition ease-in-out duration-300">
                            <i class="fas fa-user absolute left-3 top-3 text-gray-500"></i>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="correo" class="block text-gray-700 font-medium mb-2">Correo Electrónico</label>
                        <div class="relative">
                            <input type="email" id="correo" name="correo" required value="<?php echo htmlspecialchars($cliente['correo']); ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 transition ease-in-out duration-300">
                            <i class="fas fa-envelope absolute left-3 top-3 text-gray-500"></i>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="telefono" class="block text-gray-700 font-medium mb-2">Teléfono</label>
                        <div class="relative">
                            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 transition ease-in-out duration-300">
                            <i class="fas fa-phone-alt absolute left-3 top-3 text-gray-500"></i>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2 mb-6">
                        <label for="direccion" class="block text-gray-700 font-medium mb-2">Dirección</label>
                        <textarea id="direccion" name="direccion" rows="4" placeholder="Ingrese la dirección del cliente"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 transition ease-in-out duration-300"><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-6">
                    <a href="clientes.php" class="bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                        <?php echo $isEdit ? 'Actualizar' : 'Guardar'; ?> Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
