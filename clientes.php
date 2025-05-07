<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM clientes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header('Location: clientes.php?success=1');
        exit;
    } catch(PDOException $e) {
        $error = "Error al eliminar el cliente: " . $e->getMessage();
    }
}

$porPagina = 6;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $porPagina;

$totalStmt = $conn->query("SELECT COUNT(*) FROM clientes");
$totalClientes = $totalStmt->fetchColumn();
$totalPaginas = ceil($totalClientes / $porPagina);

$stmt = $conn->prepare("SELECT * FROM clientes ORDER BY nombre LIMIT :inicio, :porPagina");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
$stmt->execute();
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Salud - Clientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col bg-gray-100">
<main class="flex-grow">
    <?php include 'includes/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Gestión de Clientes</h1>
            <a href="cliente-form.php" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-plus mr-2"></i> Nuevo Cliente
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>Operación realizada con éxito.</p>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?php echo $error; ?></p>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Nombre</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">Teléfono</th>
                            <th class="py-3 px-4 text-left">Dirección</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-3 px-4"><?php echo $cliente['id']; ?></td>
                            <td class="py-3 px-4"><?php echo $cliente['nombre']; ?></td>
                            <td class="py-3 px-4"><?php echo $cliente['correo']; ?></td>
                            <td class="py-3 px-4"><?php echo $cliente['telefono']; ?></td>
                            <td class="py-3 px-4"><?php echo $cliente['direccion']; ?></td>
                            <td class="py-3 px-4 text-center">
                                <a href="cliente-form.php?id=<?php echo $cliente['id']; ?>" class="text-blue-500 hover:text-blue-700 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $cliente['id']; ?>)" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (count($clientes) === 0): ?>
                        <tr class="border-t">
                            <td colspan="6" class="py-4 px-4 text-center text-gray-500">No hay clientes registrados.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-center space-x-2">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?>" class="px-3 py-1 rounded-md border <?php echo ($i == $pagina) ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 border-blue-600 hover:bg-blue-100'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>

<script>
    function confirmDelete(id) {
        if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
            window.location.href = 'clientes.php?delete=' + id;
        }
    }
</script>
</body>
</html>