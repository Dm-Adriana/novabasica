<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM productos WHERE stock < 5");
$stmt->execute();
$lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Salud - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">

    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                        <i class="fas fa-pills text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Productos</p>
                        <?php
                        $stmt = $conn->query("SELECT COUNT(*) FROM productos");
                        $productCount = $stmt->fetchColumn();
                        ?>
                        <p class="text-3xl font-bold"><?php echo $productCount; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Ventas</p>
                        <?php
                        $stmt = $conn->query("SELECT COUNT(*) FROM ventas");
                        $salesCount = $stmt->fetchColumn();
                        ?>
                        <p class="text-3xl font-bold"><?php echo $salesCount; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-500 text-white mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Clientes</p>
                        <?php
                        $stmt = $conn->query("SELECT COUNT(*) FROM clientes");
                        $clientCount = $stmt->fetchColumn();
                        ?>
                        <p class="text-3xl font-bold"><?php echo $clientCount; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (count($lowStockProducts) > 0): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-lg shadow-md">
            <h3 class="font-semibold mb-2">¡Alerta de stock bajo!</h3>
            <p>Los siguientes productos tienen menos de 5 unidades en stock:</p>
            <ul class="list-disc ml-5 mt-2">
                <?php foreach ($lowStockProducts as $product): ?>
                <li><?php echo $product['nombre']; ?> - Stock actual: <?php echo $product['stock']; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Ventas recientes</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Cliente</th>
                                <th class="py-2 px-4 border-b text-left">Fecha</th>
                                <th class="py-2 px-4 border-b text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("SELECT * FROM ventas ORDER BY fecha_venta DESC LIMIT 5");
                            $recentSales = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($recentSales as $sale):
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-2 px-4 border-b"><?php echo $sale['cliente_nombre']; ?></td>
                                <td class="py-2 px-4 border-b"><?php echo date('d/m/Y H:i', strtotime($sale['fecha_venta'])); ?></td>
                                <td class="py-2 px-4 border-b text-right">S/ <?php echo number_format($sale['total'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="ventas.php" class="text-blue-500 hover:underline">Ver todas las ventas</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-2xl transition-shadow duration-300">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Productos populares</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Producto</th>
                                <th class="py-2 px-4 border-b text-right">Precio</th>
                                <th class="py-2 px-4 border-b text-right">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("SELECT p.*, COUNT(vp.producto_id) as venta_count 
                                                FROM productos p 
                                                LEFT JOIN venta_productos vp ON p.id = vp.producto_id 
                                                GROUP BY p.id 
                                                ORDER BY venta_count DESC 
                                                LIMIT 5");
                            $popularProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($popularProducts as $product):
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-2 px-4 border-b"><?php echo $product['nombre']; ?></td>
                                <td class="py-2 px-4 border-b text-right">S/ <?php echo number_format($product['precio'], 2); ?></td>
                                <td class="py-2 px-4 border-b text-right <?php echo $product['stock'] < 5 ? 'text-red-500 font-bold' : ''; ?>">
                                    <?php echo $product['stock']; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="productos.php" class="text-blue-500 hover:underline">Ver todos los productos</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
