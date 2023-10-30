<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$username = $password = '';
// Initialize variables
$userID = ''; // Initialize with the specific user's ID you want to display
$errors = array();
$userData = array();

// Retrieve user data
if (isset($_GET['id'])) {
    $userID = $_GET['id'];
    $query = "SELECT u.UserID, u.Username, u.Email, r.RoleName, u.DateOfBirth, u.Gender, u.Address, u.PhoneNumber, u.RoleID, u.AccountCreationDate, u.LastLogin, u.AccountStatus, u.ActivationStatus
              FROM Users u
              LEFT JOIN Role r ON u.RoleID = r.RoleID
              WHERE u.UserID = $userID";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    } else {
        $errors[] = "User not found.";
    }
}

?>
<?php include_once('../components/header.php'); ?>
<!-- Main Content Height Menyesuaikan Hasil Kurang dari Header dan Footer -->
<div class="h-screen flex flex-col">
    <!-- Top Navbar -->
    <?php include('../components/navbar.php'); ?>
    <!-- End Top Navbar -->
    <!-- Main Content -->
    <div class="flex-grow bg-gray-50 flex flex-row shadow-md">
        <!-- Sidebar -->
        <?php include('../components/sidebar.php'); ?>
        <!-- End Sidebar -->
        <main class="bg-gray-50 flex flex-col flex-1 overflow-y-scroll h-screen flex-shrink-0 sc-hide pb-40">
            <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                <!-- Header Content -->
                <div class="flex flex-row justify-between items-center w-full border-b-2 border-gray-600 mb-2 pb-2">
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">User Details</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="../manage_users/manage_users_list.php" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <!-- End Header Content -->
                <!-- Content -->
                <div class="flex flex-col w-full">
                    <!-- Navigation -->
                    <div class="flex flex-row justify-between items-center w-full mb-2 pb-2">
                        <div>
                            <h2 class="text-lg text-gray-800 font-semibold">Welcome back, <?php echo $_SESSION['FullName']; ?>!</h2>
                            <p class="text-gray-600 text-sm">User information.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- User Details -->
                    <?php if (!empty($userData)) : ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white shadow-md p-4 rounded-md">
                                <h3 class="text-lg font-semibold text-gray-800">User Information</h3>
                                <p><strong>Username:</strong> <?php echo $userData['Username']; ?></p>
                                <p><strong>Email:</strong> <?php echo $userData['Email']; ?></p>
                                <p><strong>Role:</strong> <?php echo $userData['RoleName']; ?></p>
                                <p><strong>Date of Birth:</strong> <?php echo $userData['DateOfBirth']; ?></p>
                                <p><strong>Gender:</strong> <?php echo $userData['Gender']; ?></p>
                            </div>
                            <div class="bg-white shadow-md p-4 rounded-md">
                                <h3 class="text-lg font-semibold text-gray-800">Contact Information</h3>
                                <p><strong>Address:</strong> <?php echo $userData['Address']; ?></p>
                                <p><strong>Phone Number:</strong> <?php echo $userData['PhoneNumber']; ?></p>
                            </div>
                        </div>
                        <!-- Add Edit and Delete Buttons -->
                        <div class="mt-4">
                            <a href="manage_users_update.php?id=<?php echo $userID; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mr-2">
                                <i class="fas fa-edit mr-2"></i>
                                <span>Edit</span>
                            </a>
                            <a href="#" onclick="confirmDelete(<?php echo $userID; ?>)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <i class="fas fa-trash mr-2"></i>
                                <span>Delete</span>
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="bg-white shadow-md p-4 rounded-md">
                            <p>No user data available.</p>
                        </div>
                    <?php endif; ?>
                    <!-- End User Details -->
                </div>
                <!-- End Content -->
            </div>
        </main>
    </div>

    <!-- End Main Content -->
    <!-- Footer -->
    <?php include('../components/footer.php'); ?>
    <!-- End Footer -->
</div>

<script>
    // Function to show a confirmation dialog
    function confirmDelete(userID) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // If the user confirms, redirect to the delete page
                window.location.href = `manage_users_delete.php?id=${userID}`;
            }
        });
    }
</script>
</body>

</html>