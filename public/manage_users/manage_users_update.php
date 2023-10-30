<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');
// Initialize variables
$user_id = $username = $email = $full_name = $date_of_birth = $gender = $address = $phone_number = $role = '';
$errors = array();

// Retrieve the user's data to be updated (you might need to pass the user ID to this page)
if (isset($_GET['id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch the existing user data
    $query = "SELECT * FROM Users WHERE UserID = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if the user exists
    if (!$user) {
        // User not found, handle accordingly (e.g., redirect to an error page)
    } else {
        // Populate variables with existing user data
        $username = $user['Username'];
        $email = $user['Email'];
        $full_name = $user['FullName'];
        $date_of_birth = $user['DateOfBirth'];
        $gender = $user['Gender'];
        $address = $user['Address'];
        $phone_number = $user['PhoneNumber'];
        $role = $user['RoleID'];
        // You can also retrieve other fields as needed
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data (similar to create user form)
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']) ?? null;
    $gender = mysqli_real_escape_string($conn, $_POST['gender']) ?? null;
    $address = mysqli_real_escape_string($conn, $_POST['address']) ?? null;
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']) ?? null;
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    // You should validate the fields and handle errors as needed

    // Update user data in the database
    $query = "UPDATE Users 
              SET Username = ?, Email = ?, FullName = ?, DateOfBirth = ?, Gender = ?, Address = ?, PhoneNumber = ?, RoleID = ? 
              WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", $username, $email, $full_name, $date_of_birth, $gender, $address, $phone_number, $role, $user_id);

    if ($stmt->execute()) {
        // Registration successful
        $activityDescription = "User with Username: $username has been update.";

        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);

        // Tampilkan notifikasi SweetAlert untuk sukses
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "User update successfully.",
        }).then(function() {
            window.location.href = "manage_users_list.php";
        });
    </script>';
        exit();
    } else {
        // Registration failed
        $errors['db_error'] = "User update failed.";

        // Tampilkan notifikasi SweetAlert untuk kegagalan
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "User update failed.",
        });
    </script>';
    }
}

// Close the database connection
?>



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

        <!-- Main Content -->
        <main class="bg-gray-50 flex flex-col flex-1 overflow-y-scroll h-screen flex-shrink-0 sc-hide pb-40">
            <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                <!-- Header Content -->
                <div class="flex flex-row justify-between items-center w-full border-b-2 border-gray-600 mb-2 pb-2">
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Update User</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="manage_users_list.php" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <!-- End Header Content -->
                <!-- Content -->
                <div class="flex flex-col w-full">
                    <!-- Navigation -->
                    <div class="flex flex-row justify-between items-center w-full pb-2">
                        <div>
                            <h2 class="text-lg text-gray-800 font-semibold">Welcome back, <?php echo $_SESSION['FullName']; ?>!</h2>
                            <p class="text-gray-600 text-sm">Update user information form.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- User Update Form -->
                    <form action="" method="POST" class="flex flex-col w-full space-x-2">
                        <!-- Username -->
                        <label for="username" class="block font-semibold text-gray-800 mt-2 mb-2">Username <span class="text-red-500">*</span></label>
                        <input type="text" id="username" name="username" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Username" value="<?php echo $username; ?>">
                        <?php if (isset($errors['username'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['username']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Email -->
                        <label for="email" class="block font-semibold text-gray-800 mt-2 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Email" value="<?php echo $email; ?>">
                        <?php if (isset($errors['email'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['email']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Full Name -->
                        <label for="full_name" class="block font-semibold text-gray-800 mt-2 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Full Name" value="<?php echo $full_name; ?>">
                        <?php if (isset($errors['full_name'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['full_name']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Date of Birth -->
                        <label for="date_of_birth" class="block font-semibold text-gray-800 mt-2 mb-2">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Date of Birth" value="<?php echo $date_of_birth; ?>">

                        <!-- Gender -->
                        <label for="gender" class="block font-semibold text-gray-800 mt-2 mb-2">Gender</label>
                        <select id="gender" name="gender" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                            <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
                        </select>

                        <!-- Address -->
                        <label for="address" class="block font-semibold text-gray-800 mt-2 mb-2">Address</label>
                        <textarea id="address" name="address" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Address"><?php echo $address; ?></textarea>

                        <!-- Phone Number -->
                        <label for="phone_number" class="block font-semibold text-gray-800 mt-2 mb-2">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Phone Number" value="<?php echo $phone_number; ?>">
                        <?php
                        function getRoles($conn)
                        {
                            $roles = array();
                            $query = "SELECT RoleID, RoleName FROM Role";
                            $result = $conn->query($query);

                            if (
                                $result->num_rows > 0
                            ) {
                                while ($row = $result->fetch_assoc()) {
                                    $roles[$row['RoleID']] = $row['RoleName'];
                                }
                            }

                            return $roles;
                        }
                        $roles = getRoles($conn);
                        ?>
                        <!-- Role -->
                        <label for="role" class="block font-semibold text-gray-800 mt-2 mb-2">Role</label>
                        <select id="role" name="role" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                            <?php
                            foreach ($roles as $roleId => $roleName) {
                                $selected = ($role == $roleId) ? 'selected' : '';
                                echo "<option value=\"$roleId\" $selected>$roleName</option>";
                            }
                            ?>
                        </select>

                        <!-- Submit Button -->
                        <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Update User</span>
                        </button>
                    </form>
                    <!-- End User Update Form -->
                </div>
                <!-- End Content -->
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