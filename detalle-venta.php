<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ventas.php');
    exit;
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM ventas WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    header('Location: ventas.php');
    exit;
}

$venta = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("
    SELECT vp.*, p.nombre as producto_nombre 
    FROM venta_productos vp 
    JOIN productos p ON vp.producto_id = p.id 
    WHERE vp.venta_id = :venta_id
");
$stmt->bindParam(':venta_id', $id);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Salud - Detalle de Venta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center mb-6">
            <a href="ventas.php" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Detalle de Venta #<?php echo $venta['id']; ?></h1>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información de la Venta</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cliente:</span>
                        <span class="font-medium"><?php echo $venta['cliente_nombre']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium"><?php echo $venta['cliente_email']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha:</span>
                        <span class="font-medium"><?php echo date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-medium">S/ <?php echo number_format($venta['total'], 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="md:col-span-2 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Productos</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-4 text-left">Producto</th>
                                <th class="py-2 px-4 text-right">Precio Unitario</th>
                                <th class="py-2 px-4 text-right">Cantidad</th>
                                <th class="py-2 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr class="border-t">
                                <td class="py-2 px-4"><?php echo $item['producto_nombre']; ?></td>
                                <td class="py-2 px-4 text-right">S/ <?php echo number_format($item['precio_unitario'], 2); ?></td>
                                <td class="py-2 px-4 text-right"><?php echo $item['cantidad']; ?></td>
                                <td class="py-2 px-4 text-right">S/ <?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr class="border-t">
                                <td colspan="3" class="py-2 px-4 text-right font-bold">Total:</td>
                                <td class="py-2 px-4 text-right font-bold">S/ <?php echo number_format($venta['total'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>