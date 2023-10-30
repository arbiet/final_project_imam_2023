<nav class="flex items-center justify-between flex-wrap bg-gray-800 p-6 shadow-md sticky top-0">
    <div class="flex items-center flex-shrink-0 text-white mr-6 ">
        <a href="<?php echo $baseUrl; ?>public/index.php" class="flex items-center space-x-2">
            <img src="<?php echo $baseLogoUrl; ?>" alt="Logo" class="w-12" /> <!-- Tambahkan kelas w-40 di sini -->
            <span class="font-semibold text-xl tracking-tight"><?php echo $baseTitle; ?></span>
        </a>
    </div>
    <div class="block lg:hidden">
        <i class="fas fa-bars text-white"></i>
    </div>
    <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto">
        <div class="text-sm lg:flex-grow">
            <a href="#responsive-header" class="block mt-4 lg:inline-block lg:mt-0 text-gray-400 hover:text-white mr-4">
                Docs
            </a>
            <a href="#responsive-header" class="block mt-4 lg:inline-block lg:mt-0 text-gray-400 hover:text-white">
                Blog
            </a>
        </div>
        <div>
            <?php
            if (isset($_SESSION['UserID'])) {
                // Jika pengguna sudah login, tampilkan tombol Logout
                echo '<a href="javascript:void(0);" onclick="confirmLogout()" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-gray-500 hover:bg-white mt-4 lg:mt-0">Logout</a>';
            } else {
                // Jika pengguna belum login, tampilkan tombol Login
                echo '<a href="login.php" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-gray-500 hover:bg-white mt-4 lg:mt-0">Login</a>';
            }
            ?>
        </div>
    </div>
</nav>