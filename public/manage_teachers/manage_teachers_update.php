<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$teacher_id = $teacher_number = $academic_degree = $education_level = $employment_status = $user_id = '';
$errors = array();

// Retrieve the teacher's data to be updated (you might need to pass the teacher ID to this page)
if (isset($_GET['id'])) {
    $teacher_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch the existing teacher data
    $query = "SELECT t.TeacherID, t.NIP, t.AcademicDegree, t.EducationLevel, t.EmploymentStatus, t.UserID, u.Username, u.Email, u.FullName, u.DateOfBirth, u.Gender, u.Address, u.PhoneNumber, u.RoleID
              FROM Teachers t
              INNER JOIN Users u ON t.UserID = u.UserID
              WHERE t.TeacherID = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();

    // Check if the teacher exists
    if (!$teacher) {
        // Teacher not found, handle accordingly (e.g., redirect to an error page)
    } else {
        // Populate variables with existing teacher data
        $teacher_number = $teacher['NIP'];
        $academic_degree = $teacher['AcademicDegree'];
        $education_level = $teacher['EducationLevel'];
        $employment_status = $teacher['EmploymentStatus'];
        $user_id = $teacher['UserID'];
        $username = $teacher['Username'];
        $email = $teacher['Email'];
        $full_name = $teacher['FullName'];
        $gender = $teacher['Gender'];
        $address = $teacher['Address'];
        $phone_number = $teacher['PhoneNumber'];
        $date_of_birth = $teacher['DateOfBirth'];


        // You can also retrieve other fields as needed, such as $date_of_birth, $gender, $address, $phone_number, etc.
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $academic_degree = mysqli_real_escape_string($conn, $_POST['academic_degree']);
    $education_level = mysqli_real_escape_string($conn, $_POST['education_level']);
    $employment_status = mysqli_real_escape_string($conn, $_POST['employment_status']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

    // Check if the new username is the same as the old username
    if ($new_username !== $username) {
        // New username is different, check if it already exists in the database
        $check_query = "SELECT UserID FROM Users WHERE Username = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $new_username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Username is already taken, show an error message
            $errors['username'] = "Username is already taken. Please choose another username.";
        }
    }

    if (empty($errors)) {
        // Update user data in the database
        $query = "UPDATE Users 
                SET Username = ?, Email = ?, FullName = ?, DateOfBirth = ?, Gender = ?, Address = ?, PhoneNumber = ?
                WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $new_username, $email, $full_name, $date_of_birth, $gender, $address, $phone_number, $user_id);

        if ($stmt->execute()) {
            // Now, update the corresponding data in the Teachers table
            $update_teachers_query = "UPDATE Teachers 
                              SET NIP = ?, AcademicDegree = ?, EducationLevel = ?, EmploymentStatus = ?
                              WHERE TeacherID = ?";
            $stmt_teachers = $conn->prepare($update_teachers_query);
            $stmt_teachers->bind_param("sssss", $teacher_number, $academic_degree, $education_level, $employment_status, $teacher_id);

            if ($stmt_teachers->execute()) {
                // Registration successful
                $activityDescription = "Teacher with Username: $new_username has been updated.";

                $currentUserID = $_SESSION['UserID'];
                insertLogActivity($conn, $currentUserID, $activityDescription);

                // Tampilkan notifikasi SweetAlert untuk sukses
                echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Teacher update successfully.",
                }).then(function() {
                    window.location.href = "manage_teachers_list.php";
                });
            </script>';
                exit();
            } else {
                // Registration failed
                $errors['db_error'] = "Teacher update failed.";

                // Tampilkan notifikasi SweetAlert untuk kegagalan
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Teacher update failed.",
                });
            </script>';
            }
        }
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Update Teacher</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="manage_teachers_list.php" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Update teacher information form.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Teacher Update Form -->
                    <form action="" method="POST" class="flex flex-col w-full space-x-2">
                        <!-- Teacher Number -->
                        <label for="teacher_number" class="block font-semibold text-gray-800 mt-2 mb-2">Teacher Number <span class="text-red-500">*</span></label>
                        <input type="text" id="teacher_number" name="teacher_number" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Teacher Number" value="<?php echo $teacher_number; ?>">
                        <?php if (isset($errors['teacher_number'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['teacher_number']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Academic Degree -->
                        <label for="academic_degree" class="block font-semibold text-gray-800 mt-2 mb-2">Academic Degree <span class="text-red-500">*</span></label>
                        <input type="text" id="academic_degree" name="academic_degree" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Academic Degree" value="<?php echo $academic_degree; ?>">
                        <?php if (isset($errors['academic_degree'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['academic_degree']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Education Level -->
                        <label for="education_level" class="block font-semibold text-gray-800 mt-2 mb-2">Education Level <span class="text-red-500">*</span></label>
                        <input type="text" id="education_level" name="education_level" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Education Level" value="<?php echo $education_level; ?>">
                        <?php if (isset($errors['education_level'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['education_level']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Employment Status -->
                        <label for="employment_status" class="block font-semibold text-gray-800 mt-2 mb-2">Employment Status <span class="text-red-500">*</span></label>
                        <input type="text" id="employment_status" name="employment_status" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Employment Status" value="<?php echo $employment_status; ?>">
                        <?php if (isset($errors['employment_status'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['employment_status']; ?>
                            </p>
                        <?php endif; ?>

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
                        <?php if (isset($errors['date_of_birth'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['date_of_birth']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Gender -->
                        <label for="gender" class="block font-semibold text-gray-800 mt-2 mb-2">Gender</label>
                        <select id="gender" name="gender" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                            <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if ($gender === 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                        <?php if (isset($errors['gender'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['gender']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Address -->
                        <label for="address" class="block font-semibold text-gray-800 mt-2 mb-2">Address</label>
                        <textarea id="address" name="address" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Address"><?php echo $address; ?></textarea>
                        <?php if (isset($errors['address'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['address']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Phone Number -->
                        <label for="phone_number" class="block font-semibold text-gray-800 mt-2 mb-2">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Phone Number" value="<?php echo $phone_number; ?>">
                        <?php if (isset($errors['phone_number'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['phone_number']; ?>
                            </p>
                        <?php endif; ?>


                        <!-- Submit Button -->
                        <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Update Teacher</span>
                        </button>
                    </form>
                    <!-- End Teacher Update Form -->
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