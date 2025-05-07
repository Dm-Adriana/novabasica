<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$por_pagina = 6;
$pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

$total_stmt = $conn->query("SELECT COUNT(*) FROM ventas");
$total_ventas = $total_stmt->fetchColumn();
$total_paginas = ceil($total_ventas / $por_pagina);

$stmt = $conn->prepare("SELECT * FROM ventas ORDER BY fecha_venta DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Salud - Ventas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col bg-gray-100">
<main class="flex-grow">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Registro de Ventas</h1>
            <a href="nueva-venta.php" class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded-md shadow hover:bg-blue-700 transition">
                <i class="fas fa-plus"></i> Nueva Venta
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-gray-100 border-b text-left">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Cliente</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $venta['id']; ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($venta['cliente_nombre']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($venta['cliente_email']); ?></td>
                            <td class="py-3 px-4 text-right">S/ <?php echo number_format($venta['total'], 2); ?></td>
                            <td class="py-3 px-4"><?php echo date('d/m/Y H:i', strtotime($venta['fecha_venta'])); ?></td>
                            <td class="py-3 px-4 text-center">
                                <a href="detalle-venta.php?id=<?php echo $venta['id']; ?>" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> Ver detalles
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (count($ventas) === 0): ?>
                        <tr>
                            <td colspan="6" class="py-4 px-4 text-center text-gray-500">No hay ventas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_paginas > 1): ?>
            <div class="mt-6 flex justify-center space-x-1">
                <?php if ($pagina_actual > 1): ?>
                    <a href="?pagina=<?php echo $pagina_actual - 1; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-200">&laquo;</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?pagina=<?php echo $i; ?>" class="px-3 py-1 border rounded <?php echo $i == $pagina_actual ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-100'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?pagina=<?php echo $pagina_actual + 1; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-200">&raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
</body>
</html>
