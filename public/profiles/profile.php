<?php
session_start();
// Include the connection file
require_once('../../database/connection.php');
include_once('../components/header.php');
// Initialize variables
$errors = array();
// Retrieve user information from the database
$user_id = $_SESSION['UserID']; // Assuming 'UserID' is the correct session key
$select_query = "SELECT Users.*, Role.RoleName FROM Users
                LEFT JOIN Role ON Users.RoleID = Role.RoleID
                WHERE Users.UserID = ?";
$select_stmt = $conn->prepare($select_query);
$select_stmt->bind_param('i', $user_id);
$select_stmt->execute();
$result = $select_stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    // Handle the case where the user is not found
    // You can redirect or display an error message here
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user inputs
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $changes = array(); // Array untuk menyimpan perubahan yang dilakukan

    if ($user['FullName'] !== $fullname) {
        $changes[] = 'Full Name';
    }

    if ($user['Email'] !== $email) {
        $changes[] = 'Email';
    }

    if ($user['Username'] !== $username) {
        $changes[] = 'Username';
    }

    if (
        $user['DateOfBirth'] !== $dob
    ) {
        $changes[] = 'Date of Birth';
    }

    if ($user['Gender'] !== $gender) {
        $changes[] = 'Gender';
    }

    if ($user['Address'] !== $address) {
        $changes[] = 'Address';
    }

    if (
        $user['PhoneNumber'] !== $phone
    ) {
        $changes[] = 'Phone Number';
    }


    // Perform basic validation
    if (empty($username) || empty($fullname) || empty($email)) {
        $errors['missing_fields'] = 'Username, fullname, and email are required.';
    }

    // If no errors, proceed with updating profile
    if (empty($errors)) {
        // Prepare and execute a query to update user profile
        $user_id = $_SESSION['UserID']; // Assuming 'UserID' is the correct session key
        $update_query = "UPDATE Users SET FullName = ?, Email = ?, Username = ?, DateOfBirth = ?, Gender = ?, Address = ?, PhoneNumber = ? WHERE UserID = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('sssssssi', $fullname, $email, $username, $dob, $gender, $address, $phone, $user_id);

        if ($update_stmt->execute()) {
            // Deskripsi aktivitas
            $activityDescription = 'User updated profile. Changes: ' . implode(', ', $changes);
            $currentUserID = $_SESSION['UserID'];
            insertLogActivity($conn, $currentUserID, $activityDescription);


            // Update successful, show success alert
            echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Profile updated successfully.',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = 'profile.php'; // Redirect to profile page
        });
    </script>";
            exit();
        } else {
            // Update failed, show error alert
            echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to update profile.',
            showConfirmButton: false,
            timer: 1500
        });
    </script>";
        }
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
    <div class="flex-grow bg-gray-50 flex flex-row shadow-md">
        <!-- Sidebar -->
        <?php include('../components/sidebar.php'); ?>
        <!-- End Sidebar -->

        <!-- Main Content -->
        <main class=" bg-gray-50 flex flex-col flex-1 overflow-y-scroll h-screen flex-shrink-0 sc-hide pb-40">
            <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                <h1 class="text-3xl text-gray-800 font-semibold border-b border-gray-200 w-full">Profile</h1>
                <h2 class="text-xl text-gray-800 font-semibold">Welcome back, <?php echo $_SESSION['FullName']; ?>!</h2>
                <p class="text-gray-600">Profile information.</p>
                <!-- Form -->
                <div class="flex flex-row w-full space-x-2 space-y-2 mt-4 mb-4">
                    <!-- Image Profile -->
                    <div class="flex flex-col w-96 items-center justify-top pt-4">
                        <img src="<?php echo $baseUrl; ?>public/static/image/profile/<?php echo $_SESSION['ProfilePictureURL']; ?>" alt="Profile Image" class="rounded-md object-cover mb-4">
                        <!-- Form untuk mengunggah gambar profil baru -->
                        <form id="image-upload-form" action="upload_image.php" method="POST" enctype="multipart/form-data" class="w-full">
                            <input type="file" name="profile_image" accept="image/*">
                            <button type="button" onclick="confirmImageUpload()" class="bg-blue-500 hover-bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4 w-full">
                                Upload Image
                            </button>
                        </form>
                    </div>
                    <!-- End Image -->
                    <!-- Form Profile -->
                    <form action="profile.php" method="POST" class="flex flex-col w-full space-x-2 mt-4 mb-4" id="profile-update-form">
                        <!-- Full Name -->
                        <label for="fullname" class="block font-semibold text-gray-800 mt-2 mb-2">Full Name</label>
                        <input type="text" id="fullname" name="fullname" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border" value="<?php echo $user['FullName']; ?>">

                        <!-- Email -->
                        <label for="email" class="block font-semibold text-gray-800 mt-2 mb-2">Email</label>
                        <input type="email" id="email" name="email" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border" value="<?php echo $user['Email']; ?>">

                        <!-- Username -->
                        <label for="username" class="block font-semibold text-gray-800 mt-2 mb-2">Username</label>
                        <input type="text" id="username" name="username" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border" value="<?php echo $user['Username']; ?>">

                        <!-- Date of Birth -->
                        <label for="dob" class="block font-semibold text-gray-800 mt-2 mb-2">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border" value="<?php echo $user['DateOfBirth']; ?>">

                        <!-- Gender -->
                        <label for="gender" class="block font-semibold text-gray-800 mt-2 mb-2">Gender</label>
                        <select id="gender" name="gender" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border">
                            <option value="Male" <?php if ($user['Gender'] === 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($user['Gender'] === 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if ($user['Gender'] === 'Other') echo 'selected'; ?>>Other</option>
                        </select>

                        <!-- Address -->
                        <label for="address" class="block font-semibold text-gray-800 mt-2 mb-2">Address</label>
                        <textarea id="address" name="address" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border"><?php echo $user['Address']; ?></textarea>

                        <!-- Phone Number -->
                        <label for="phone" class="block font-semibold text-gray-800 mt-2 mb-2">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border" value="<?php echo $user['PhoneNumber']; ?>">

                        <!-- Role ID -->
                        <label for="role" class="block font-semibold text-gray-800 mt-2 mb-2">Role</label>
                        <input type="text" id="role" name="role" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border" value="<?php echo $user['RoleName']; ?>" disabled>

                        <!-- Account Status -->
                        <label for="accountStatus" class="block font-semibold text-gray-800 mt-2 mb-2">Account Status</label>
                        <input type="text" id="accountStatus" name="accountStatus" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border" value="<?php echo ($user['AccountStatus'] !== null) ? $user['AccountStatus'] : 'Account Status Not Set'; ?>" disabled>

                        <!-- Activation Status -->
                        <label for="activationStatus" class="block font-semibold text-gray-800 mt-2 mb-2">Activation Status</label>
                        <input type="text" id="activationStatus" name="activationStatus" class="w-full rounded-md border-gray-300 focus-border-blue-500 focus-ring focus-ring-blue-500 focus-ring-opacity-50 focus-outline-none px-2 py-2 border" value="<?php echo ($user['ActivationStatus'] !== null) ? $user['ActivationStatus'] : 'Activation Status Not Set'; ?>" disabled>

                        <!-- Update Button -->
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-4">
                            Update Profile
                        </button>
                    </form>
                    <!-- End Form Profile -->
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
<script>
    function confirmImageUpload() {
        Swal.fire({
            title: 'Upload Image',
            text: 'Anda yakin ingin mengunggah gambar profil baru?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna memilih "Ya," submit form untuk mengunggah gambar
                document.getElementById('image-upload-form').submit();
            }
        });
    }
</script>
<script>
    function confirmProfileUpdate() {
        Swal.fire({
            title: 'Update Profile',
            text: 'Anda yakin ingin menyimpan perubahan profil?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna memilih "Ya," submit form pembaruan profil
                document.getElementById('profile-update-form').submit();
            }
        });
    }
</script>

</html>