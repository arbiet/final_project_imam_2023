<?php
session_start();
// Include the connection file
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$username = $password = '';
$errors = array();

// Check if the user is already logged in
if (isset($_SESSION['UserID'])) {
    // If user is already logged in, redirect to dashboard
    header('Location: systems/dashboard.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user inputs
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Perform basic validation
    if (empty($username) || empty($password)) {
        $errors['login_failed'] = 'Username and password are required.';
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
            $_SESSION['ProfilePictureURL'] = $row['ProfilePictureURL'];

            // Update LastLogin in the Users table
            $activityDescription = 'User logged in';
            $currentUserID = $_SESSION['UserID'];
            insertLogActivity($conn, $currentUserID, $activityDescription);

            $updateLastLoginQuery = "UPDATE Users SET LastLogin = NOW() WHERE UserID = ?";
            $updateLastLoginStmt = $conn->prepare($updateLastLoginQuery);
            $updateLastLoginStmt->bind_param('i', $currentUserID);
            $updateLastLoginStmt->execute();
            $updateLastLoginStmt->close();

            // Trigger a SweetAlert for successful login
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "Login successful.",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function(){
                    window.location.href = "../systems/dashboard.php";
                });
            </script>';
            exit();
        } else {
            $errors['login_failed'] = 'Invalid username or password.';

            // Trigger a SweetAlert for failed login
            echo '<script>
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Invalid username or password.",
                showConfirmButton: false,
                timer: 1500
            });
          </script>';
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!-- Main Content Height Menyesuaikan Hasil Kurang dari Header dan Footer -->
<div class="h-screen flex flex-col">
    <!-- Top Navbar -->
    <?php include('../components/navbar.php'); ?>
    <!-- End Top Navbar -->
    <!-- Main Content -->
    <main class="flex-grow bg-gray-50 flex flex-col">
        <!-- Login Form -->
        <div class="flex-grow bg-gray-50">
            <div class="flex justify-center items-center h-full">
                <div class="text-center px-40">
                    <h1 class="text-6xl font-bold text-gray-700 mb-10">Login</h1>
                    <?php if (isset($errors['login_failed'])) : ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <strong class="font-bold">Login failed!</strong>
                            <span class="block sm:inline"><?php echo $errors['login_failed']; ?></span>
                        </div>
                    <?php endif; ?>
                    <!-- success -->
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline"><?php echo $_SESSION['success']; ?></span>
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST" class="mb-6">
                        <label for="username" class="block text-left text-gray-600 mb-2">Username</label>
                        <input type="text" id="username" name="username" class="border border-gray-300 rounded-full px-4 py-2 w-full mb-2" required>
                        <?php if (isset($errors['username'])) : ?>
                            <span class="text-red-500 text-sm"><?php echo $errors['username']; ?></span>
                        <?php endif; ?>
                        <label for="password" class="block text-left text-gray-600 mb-2">Password</label>
                        <input type="password" id="password" name="password" class="border border-gray-300 rounded-full px-4 py-2 w-full mb-6" required>
                        <?php if (isset($errors['password'])) : ?>
                            <span class="text-red-500 text-sm"><?php echo $errors['password']; ?></span>
                        <?php endif; ?>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full w-full">
                            Log In
                        </button>
                    </form>
                    <p class="text-gray-500 text-sm">Forgot your password? <a href="<?php echo $baseUrl; ?>public/systems/forgot_password.php" class="text-blue-500">Click here</a></p>
                    <p class="text-gray-500 text-sm">Don't have an account? <a href="<?php echo $baseUrl; ?>public/systems/register.php" class="text-blue-500">Register here</a></p>
                </div>
            </div>
        </div>
        <!-- End Login Form -->

    </main>
    <!-- End Main Content -->
    <!-- Footer -->
    <?php include('../components/footer.php'); ?>
    <!-- End Footer -->
</div>
<!-- End Main Content -->
</body>

</html>