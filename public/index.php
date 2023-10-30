<?php
// get file connection.php
require_once('../database/connection.php');
// Initialize the session
session_start();

if (isset($_SESSION['UserID'])) {
    // Redirect to dashboard
    header('Location: systems/dashboard.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $baseTitle; ?></title>
    <!-- Tailwind CSS -->
    <link rel="icon" href="<?php echo $baseUrl; ?>/static/logo.ico" type="image/png">
    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/static/logo.ico" type="image/png">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>dist/output.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
    <!-- SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.all.min.js"></script>

</head>

<body class="overflow-hidden">
    <!-- Main Content Height Menyesuaikan Hasil Kurang dari Header dan Footer -->
    <div class="h-screen flex flex-col">
        <!-- Top Navbar -->
        <nav class="flex items-center justify-between flex-wrap bg-gray-800 p-6">
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
                        echo '<a href="' . $baseUrl . 'public/systems/login.php" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-gray-500 hover:bg-white mt-4 lg:mt-0">Login</a>';
                    }
                    ?>
                </div>
            </div>
        </nav>
        <!-- End Top Navbar -->
        <!-- Main Content -->
        <main class="flex-grow bg-gray-50">
            <div class=" flex justify-center items-center h-full">
                <div class="text-center px-40">
                    <h1 class="text-6xl font-bold text-gray-700 mb-10"><?php echo $baseTitle; ?></h1>
                    <p class="text-gray-500 mb-10 text-xl"><?php echo $baseDescription; ?></p>
                    <a href="<?php echo $baseUrl; ?>public/systems/login.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                        Get Started
                    </a>
                </div>
            </div>
        </main>
        <!-- Footer -->
        <footer class="bg-gray-800 text-gray-400 py-4">
            <div class="container mx-auto text-center text-sm">
                <p>&copy; 2023 <?php echo $baseTitle; ?>. All rights reserved.</p>
            </div>
        </footer>
        <!-- End Footer -->
    </div>
    <!-- End Main Content -->
</body>

</html>