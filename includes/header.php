<header class="rounded-2xl shadow-xl backdrop-blur-lg bg-[rgba(0,60,180,0.45)] border border-blue-300/30" style="box-shadow: 0 8px 32px rgba(0, 150, 255, 0.5);">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
                <img src="img/logo.png" alt="Nova Salud" class="w-20 h-auto mr-4 drop-shadow-lg">
            </div>

            <nav class="hidden md:flex space-x-6">
                <a href="index.php" class="text-white font-medium hover:text-cyan-300 transition">Dashboard</a>
                <a href="productos.php" class="text-white font-medium hover:text-cyan-300 transition">Productos</a>
                <a href="ventas.php" class="text-white font-medium hover:text-cyan-300 transition">Ventas</a>
                <a href="nueva-venta.php" class="text-white font-medium hover:text-cyan-300 transition">Nueva Venta</a>
                <a href="clientes.php" class="text-white font-medium hover:text-cyan-300 transition">Clientes</a>
            </nav>

            <div class="flex items-center space-x-4">
                <span class="text-white hidden sm:inline">Hola, <?php echo $_SESSION['user_name']; ?></span>
                <a href="logout.php" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition duration-200 shadow-md">
                    Salir
                </a>

                <button id="mobile-menu-button" class="text-white md:hidden focus:outline-none">
                    <i id="menu-icon" class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="md:hidden max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div class="flex flex-col bg-blue-100/80 backdrop-blur-sm rounded-lg shadow-lg mt-2 px-4 py-4 space-y-2">
                <a href="index.php" class="block text-blue-800 font-medium hover:text-blue-900 transition">Dashboard</a>
                <hr>
                <a href="productos.php" class="block text-blue-800 font-medium hover:text-blue-900 transition">Productos</a>
                <hr>
                <a href="ventas.php" class="block text-blue-800 font-medium hover:text-blue-900 transition">Ventas</a>
                <hr>
                <a href="nueva-venta.php" class="block text-blue-800 font-medium hover:text-blue-900 transition">Nueva Venta</a>
                <hr>
                <a href="clientes.php" class="block text-blue-800 font-medium hover:text-blue-900 transition">Clientes</a>
            </div>
        </div>
    </div>
</header>
