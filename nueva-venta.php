<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $conn->query("SELECT * FROM productos WHERE stock > 0 ORDER BY nombre");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        $cliente_nombre = $_POST['cliente_nombre'];
        $cliente_email = $_POST['cliente_email'];
        $total = $_POST['total'];
        $productos_ids = $_POST['producto_id'];
        $cantidades = $_POST['cantidad'];
        $precios = $_POST['precio'];

        $stmt = $conn->prepare("INSERT INTO ventas (cliente_nombre, cliente_email, total) VALUES (:cliente_nombre, :cliente_email, :total)");
        $stmt->bindParam(':cliente_nombre', $cliente_nombre);
        $stmt->bindParam(':cliente_email', $cliente_email);
        $stmt->bindParam(':total', $total);
        $stmt->execute();

        $venta_id = $conn->lastInsertId();

        for ($i = 0; $i < count($productos_ids); $i++) {
            if (empty($productos_ids[$i]) || empty($cantidades[$i]))
                continue;

            $producto_id = $productos_ids[$i];
            $cantidad = $cantidades[$i];
            $precio = $precios[$i];

            $stmt = $conn->prepare("INSERT INTO venta_productos (venta_id, producto_id, cantidad, precio_unitario) VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario)");
            $stmt->bindParam(':venta_id', $venta_id);
            $stmt->bindParam(':producto_id', $producto_id);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':precio_unitario', $precio);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id = :id");
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':id', $producto_id);
            $stmt->execute();
        }

        $conn->commit();
        header('Location: ventas.php?success=1');
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Salud - Nueva Venta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen flex flex-col bg-gray-100">
    <main class="flex-grow">
        <?php include 'includes/header.php'; ?>
        <div class="container mx-auto px-4 py-8">
            <div class="flex items-center mb-6">
                <a href="ventas.php" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <h1 class="text-3xl font-bold text-gray-800"> Nueva Venta</h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="ventaForm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">
                        <h2 class="text-2xl font-semibold text-gray-800">🡭 Información del Cliente</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="cliente_nombre" class="block text-sm font-medium text-gray-700">Nombre del
                                    Cliente</label>
                                <input type="text" id="cliente_nombre" name="cliente_nombre" required
                                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label for="cliente_email" class="block text-sm font-medium text-gray-700">Email del
                                    Cliente</label>
                                <input type="email" id="cliente_email" name="cliente_email"
                                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold text-gray-800">🧾 Productos</h2>
                            <button type="button" id="addProductBtn"
                                class="text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-plus mr-1"></i> Agregar Producto
                            </button>
                        </div>

                        <div id="productosContainer" class="space-y-3">
                            <div class="producto-row grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-5">
                                    <select name="producto_id[]" required
                                        class="producto-select w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <option value="">Seleccionar producto</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?= $producto['id']; ?>"
                                                data-precio="<?= $producto['precio']; ?>"
                                                data-stock="<?= $producto['stock']; ?>">
                                                <?= $producto['nombre']; ?> (Stock: <?= $producto['stock']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <input type="number" name="cantidad[]" min="1" value="1" required
                                        class="cantidad-input w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" name="precio[]" step="0.01" readonly
                                        class="precio-input w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-50 text-gray-600">
                                </div>
                                <div class="col-span-2">
                                    <input type="text" readonly
                                        class="subtotal-input w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100 text-gray-600">
                                </div>
                                <div class="col-span-1 text-center">
                                    <button type="button"
                                        class="remove-producto text-red-500 hover:text-red-700 transition">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-right">
                            <div class="inline-block bg-gray-50 px-6 py-3 rounded-xl shadow-sm">
                                <div class="text-lg font-semibold text-gray-800">
                                    Total: <span id="totalDisplay" class="text-green-600">S/ 0.00</span>
                                </div>
                                <input type="hidden" id="total" name="total" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-900 text-white text-lg px-6 py-2 rounded-xl transition shadow-md">
                        <i class="fas fa-save mr-2"></i> Registrar Venta
                    </button>
                </div>
            </form>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productosContainer = document.getElementById('productosContainer');
            const addProductBtn = document.getElementById('addProductBtn');
            const totalDisplay = document.getElementById('totalDisplay');
            const totalInput = document.getElementById('total');

            updatePriceAndSubtotal(document.querySelector('.producto-select'));

            addProductBtn.addEventListener('click', function () {
                const firstRow = productosContainer.querySelector('.producto-row');
                const newRow = firstRow.cloneNode(true);

                newRow.querySelector('.producto-select').value = '';
                newRow.querySelector('.cantidad-input').value = 1;
                newRow.querySelector('.precio-input').value = '';
                newRow.querySelector('.subtotal-input').value = '';

                addRowEventListeners(newRow);

                productosContainer.appendChild(newRow);
            });

            addRowEventListeners(document.querySelector('.producto-row'));

            function calculateTotal() {
                let total = 0;
                const subtotalInputs = document.querySelectorAll('.subtotal-input');

                subtotalInputs.forEach(function (input) {
                    if (input.value) {
                        total += parseFloat(input.value);
                    }
                });

                totalDisplay.textContent = 'S/ ' + total.toFixed(2);
                totalInput.value = total.toFixed(2);
            }

            function updatePriceAndSubtotal(selectElement) {
                const row = selectElement.closest('.producto-row');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const precioInput = row.querySelector('.precio-input');
                const cantidadInput = row.querySelector('.cantidad-input');
                const subtotalInput = row.querySelector('.subtotal-input');

                if (selectedOption && selectedOption.dataset.precio) {
                    const precio = parseFloat(selectedOption.dataset.precio);
                    const cantidad = parseInt(cantidadInput.value);
                    const stock = parseInt(selectedOption.dataset.stock);

                    cantidadInput.max = stock;

                    precioInput.value = precio.toFixed(2);
                    subtotalInput.value = (precio * cantidad).toFixed(2);
                } else {
                    precioInput.value = '';
                    subtotalInput.value = '';
                }

                calculateTotal();
            }

            function addRowEventListeners(row) {
                const selectElement = row.querySelector('.producto-select');
                const cantidadInput = row.querySelector('.cantidad-input');
                const removeBtn = row.querySelector('.remove-producto');

                selectElement.addEventListener('change', function () {
                    updatePriceAndSubtotal(this);
                });

                cantidadInput.addEventListener('input', function () {
                    updatePriceAndSubtotal(selectElement);
                });

                removeBtn.addEventListener('click', function () {
                    if (productosContainer.querySelectorAll('.producto-row').length > 1) {
                        row.remove();
                        calculateTotal();
                    }
                });
            }

            document.getElementById('ventaForm').addEventListener('submit', function (e) {
                const rows = productosContainer.querySelectorAll('.producto-row');
                let valid = false;

                rows.forEach(function (row) {
                    const select = row.querySelector('.producto-select');
                    if (select.value) {
                        valid = true;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('Por favor, agregue al menos un producto a la venta.');
                }
            });
        });
    </script>
</body>

</html>