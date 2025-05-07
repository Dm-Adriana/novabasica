<header class="bg-gradient-to-r from-blue-300 via-red-400 to-blue-500 bg-opacity-30 backdrop-blur-lg shadow-xl rounded-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <img src="img/logo.png" alt="Nova Salud" class="w-20 h-auto mr-4">
            </div>

            <nav class="hidden md:flex space-x-6">
                <a href="index.php" class="text-white hover:text-blue-200 transition">Dashboard</a>
                <a href="productos.php" class="text-white hover:text-blue-200 transition">Productos</a>
                <a href="ventas.php" class="text-white hover:text-blue-200 transition">Ventas</a>
                <a href="nueva-venta.php" class="text-white hover:text-blue-200 transition">Nueva Venta</a>
                <a href="clientes.php" class="text-white hover:text-blue-200 transition">Clientes</a>
            </nav>

            <div class="flex items-center space-x-4">
                <span class="text-white hidden sm:inline">Hola, <?php echo $_SESSION['user_name']; ?></span>
                <a href="logout.php" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200">
                    Salir
                </a>

                <button id="mobile-menu-button" class="text-white md:hidden focus:outline-none">
                    <i id="menu-icon" class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="md:hidden max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div class="flex flex-col bg-white rounded-lg shadow-lg mt-2 px-4 py-4 space-y-2">
                <a href="index.php" class="block text-blue-700 font-medium hover:text-blue-900 transition">Dashboard</a>
                <hr>
                <a href="productos.php" class="block text-blue-700 font-medium hover:text-blue-900 transition">Productos</a>
                <hr>
                <a href="ventas.php" class="block text-blue-700 font-medium hover:text-blue-900 transition">Ventas</a>
                <hr>
                <a href="nueva-venta.php" class="block text-blue-700 font-medium hover:text-blue-900 transition">Nueva Venta</a>
                <hr>
                <a href="clientes.php" class="block text-blue-700 font-medium hover:text-blue-900 transition">Clientes</a>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const menuButton = document.getElementById("mobile-menu-button");
        const mobileMenu = document.getElementById("mobile-menu");
        const menuIcon = document.getElementById("menu-icon");

        let isOpen = false;

        menuButton.addEventListener("click", () => {
            isOpen = !isOpen;

            if (isOpen) {
                mobileMenu.classList.remove("max-h-0");
                mobileMenu.classList.add("max-h-screen");
                menuIcon.classList.remove("fa-bars");
                menuIcon.classList.add("fa-times");
            } else {
                mobileMenu.classList.add("max-h-0");
                mobileMenu.classList.remove("max-h-screen");
                menuIcon.classList.remove("fa-times");
                menuIcon.classList.add("fa-bars");
            }
        });
    });
</script>
