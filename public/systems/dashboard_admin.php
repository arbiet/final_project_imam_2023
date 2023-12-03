<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$username = $password = '';
$errors = array();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user inputs
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Perform basic validation
    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    // If no errors, proceed with login
    if (empty($errors)) {
        // Hash the password for comparison
        $hashed_password = hash('sha256', $password);

        // Prepare and execute a query to check user credentials
        $query = "SELECT * FROM Users WHERE Username = ? AND Password = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $username, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            // Perform login actions (e.g., set sessions, redirect, etc.)
            $row = $result->fetch_assoc();
            $_SESSION['UserID'] = $row['UserID'];
            $_SESSION['Username'] = $row['Username'];
            $_SESSION['RoleID'] = $row['RoleID'];
            $_SESSION['Email'] = $row['Email'];
            $_SESSION['FullName'] = $row['FullName'];

            // Redirect to a dashboard or homepage
            header('Location: systems/dashboard.php');
            exit();
        } else {
            $errors['login_failed'] = 'Invalid username or password.';
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<?php include('../components/header.php'); ?>
<!-- Main Content Height Menyesuaikan Hasil Kurang dari Header dan Footer -->
<div class="h-screen flex flex-col">
    <!-- Top Navbar -->
    <?php include('../components/navbar.php'); ?>
    <!-- End Top Navbar -->
    <!-- Main Content -->
    <div class="bg-gray-50 flex flex-row shadow-md">
        <!-- Sidebar -->
        <?php include('../components/sidebar.php'); ?>
        <!-- End Sidebar -->
        <!-- Main Content -->
        <main class=" bg-gray-50 flex flex-col flex-1">
            <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                <h1 class="text-3xl text-gray-800 font-semibold border-b border-gray-200 w-full">Dashboard</h1>
                <h2 class="text-xl text-gray-800 font-semibold">
                    Welcome back, <?php echo $_SESSION['FullName']; ?>!
                    <?php
                    if ($_SESSION['RoleID'] === 'admin') {
                        echo " (Admin)";
                    } elseif ($_SESSION['RoleID'] === 'student') {
                        echo " (Student)";
                    }
                    ?>
                </h2>
                <p class="text-gray-600">Here's what's happening with your projects today.</p>
                <!-- Grafik -->
                <div class="flex flex-row flex-wrap w-full space-x-2 mt-4 mb-4">
                    <div class="w-full lg:w-1/2 xl:w-1/3 flex flex-1">
                        <div class="bg-white border rounded shadow p-2 w-full">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded p-3 bg-green-600"><i class="fa fa-wallet fa-2x fa-fw fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-500">Total Revenue</h5>
                                    <h3 class="font-bold text-3xl">$3249 <span class="text-green-500"><i class="fas fa-caret-up"></i></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2 xl:w-1/3 flex flex-1">
                        <div class="bg-white border rounded shadow p-2 w-full">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded p-3 bg-gray-800"><i class="fas fa-users fa-2x fa-fw fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-500">Total Users</h5>
                                    <h3 class="font-bold text-3xl">249 <span class="text-orange-500"><i class="fas fa-exchange-alt"></i></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2 xl:w-1/3 flex flex-1">
                        <div class="bg-white border rounded shadow p-2 w-full">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded p-3 bg-yellow-600"><i class="fas fa-user-plus fa-2x fa-fw fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-500">New Users</h5>
                                    <h3 class="font-bold text-3xl">2 <span class="text-yellow-600"><i class="fas fa-caret-up"></i></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row flex-wrap w-full space-x-2">
                    <div class="w-full lg:w-1/2 xl:w-1/3 flex flex-1">
                        <div class="bg-white border rounded shadow p-2 w-full">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded p-3 bg-blue-600"><i class="fas fa-server fa-2x fa-fw fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-500">Server Uptime</h5>
                                    <h3 class="font-bold text-3xl">152 days</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2 xl:w-1/3 flex flex-1">
                        <div class="bg-white border rounded shadow p-2 w-full">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded p-3 bg-indigo-600"><i class="fas fa-tasks fa-2x fa-fw fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-500">To Do List</h5>
                                    <h3 class="font-bold text-3xl">7 tasks</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2 xl:w-1/3 flex flex-1">
                        <div class="bg-white border rounded shadow p-2 w-full">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded p-3 bg-red-600"><i class="fas fa-inbox fa-2x fa-fw fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-500">Issues</h5>
                                    <h3 class="font-bold text-3xl">3 <span class="text-red-500"><i class="fas fa-caret-up"></i></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- End Main Content -->
    </div>
    <!-- End Main Content -->
    <!-- Footer -->
    <?php include('../components/footer.php'); ?>
    <!-- End Footer -->
</div>
<!-- End Main Content -->
</body>

</html>