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

$total_stmt = $conn->query("SELECT COUNT(*) FROM productos");
$total_productos = $total_stmt->fetchColumn();
$total_paginas = ceil($total_productos / $por_pagina);

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('Location: productos.php?success=1');
        exit;
    } catch (PDOException $e) {
        $error = "Error al eliminar el producto: " . $e->getMessage();
    }
}

$stmt = $conn->prepare("SELECT * FROM productos ORDER BY nombre LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Salud - Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen flex flex-col bg-gray-100">
    <main class="flex-grow">
        <?php include 'includes/header.php'; ?>

        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Productos</h1>
                <a href="producto-form.php"
                    class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded-md shadow hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i> Nuevo
                </a>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded mb-4 shadow-sm">
                    Producto eliminado correctamente.
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded mb-4 shadow-sm">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="text-left py-3 px-4">ID</th>
                            <th class="text-left py-3 px-4">Nombre</th>
                            <th class="text-left py-3 px-4">Descripción</th>
                            <th class="text-right py-3 px-4">Precio</th>
                            <th class="text-right py-3 px-4">Stock</th>
                            <th class="text-center py-3 px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4"><?php echo $producto['id']; ?></td>
                                <td class="py-2 px-4"><?php echo $producto['nombre']; ?></td>
                                <td class="py-2 px-4"><?php echo $producto['descripcion']; ?></td>
                                <td class="py-2 px-4 text-right">S/ <?php echo number_format($producto['precio'], 2); ?>
                                </td>
                                <td
                                    class="py-2 px-4 text-right <?php echo $producto['stock'] < 5 ? 'text-red-600 font-semibold' : ''; ?>">
                                    <?php echo $producto['stock']; ?>
                                </td>
                                <td class="py-2 px-4 text-center space-x-2">
                                    <a href="producto-form.php?id=<?php echo $producto['id']; ?>"
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $producto['id']; ?>)"
                                        class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (count($productos) === 0): ?>
                            <tr>
                                <td colspan="6" class="py-4 px-4 text-center text-gray-500">No hay productos registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_paginas > 1): ?>
                <div class="mt-6 flex justify-center space-x-1">
                    <?php if ($pagina_actual > 1): ?>
                        <a href="?pagina=<?php echo $pagina_actual - 1; ?>"
                            class="px-3 py-1 bg-white border rounded hover:bg-gray-200">&laquo;</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?pagina=<?php echo $i; ?>"
                            class="px-3 py-1 border rounded <?php echo $i == $pagina_actual ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-100'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina_actual + 1; ?>"
                            class="px-3 py-1 bg-white border rounded hover:bg-gray-200">&raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>

    <script>
        function confirmDelete(id) {
            if (confirm('¿Está seguro de que desea eliminar este producto?')) {
                window.location.href = 'productos.php?delete=' + id;
            }
        }
    </script>
</body>

</html>