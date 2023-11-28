<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$nip = $academic_degree = $education_level = $employment_status = $username = $password = $email = $full_name = $date_of_birth = $gender = $address = $phone_number = '';
$errors = array();
// Query untuk mengambil data pengguna
$query = "SELECT u.UserID, u.FullName
FROM Users u
LEFT JOIN Teachers t ON u.UserID = t.UserID
WHERE u.RoleID = (SELECT RoleID FROM Roles WHERE RoleName = 'teacher')
  AND t.TeacherID IS NULL;
";
$result = $conn->query($query);

// Buat array asosiatif untuk menyimpan data pengguna
$users = array();

while ($row = $result->fetch_assoc()) {
    $users[$row['UserID']] = $row['FullName'];
}
// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data for Teachers
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $academic_degree = mysqli_real_escape_string($conn, $_POST['academic_degree']);
    $education_level = mysqli_real_escape_string($conn, $_POST['education_level']);
    $employment_status = mysqli_real_escape_string($conn, $_POST['employment_status']);

    // Check for errors
    if (empty($nip)) {
        $errors['nip'] = "NIP is required.";
    }

    // Check "Account Selection" to determine the processing
    $account_selection = mysqli_real_escape_string($conn, $_POST['account_selection']);

    if ($account_selection === "new") {
        // If "Create a New Account" is selected, process the new account creation
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 2; // Assuming the role ID for Teachers is 2
        $account_status = "active";
        $activation_status = "active";

        // Generate User ID and check if it already exists in the database
        $user_id = generateRandomUserID();

        $query = "SELECT UserID FROM Users WHERE UserID = ? LIMIT 1";

        // Loop until a unique User ID is generated
        while (true) {
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $existing_user = $result->fetch_assoc();

            if (!$existing_user) {
                // If User ID doesn't exist, break out of the loop
                break;
            } else {
                // User ID already exists, generate a new one
                $user_id = generateRandomUserID();
            }
        }

        // Validate and process the new account data
        if (empty($username)) {
            $errors['username'] = "Username is required for new account.";
        }
        // Add validation rules for other new account fields as needed...

        // Check for errors related to new account creation
        if (empty($errors)) {
            // Insert data into the 'Users' table, including AccountStatus and ActivationStatus
            $query = "INSERT INTO Users (UserID, Username, Password, Email, FullName, DateOfBirth, Gender, Address, PhoneNumber, RoleID, AccountStatus, ActivationStatus)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssssssss", $user_id, $username, $hashed_password, $email, $full_name, $date_of_birth, $gender, $address, $phone_number, $role, $account_status, $activation_status);

            if ($stmt->execute()) {
                // Get the auto-generated User ID
                $newUserID = $user_id;
                // Insert the teacher data with the newly created User ID
                $insertTeacherQuery = "INSERT INTO Teachers (NIP, AcademicDegree, EducationLevel, EmploymentStatus, UserID)
                    VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertTeacherQuery);
                $stmt->bind_param("ssssi", $nip, $academic_degree, $education_level, $employment_status, $newUserID);

                // If teacher and user are created successfully
                if ($stmt->execute()) {
                    // Teacher creation successful
                    // Log the activity for teacher creation
                    $activityDescription = "Teacher created: NIP: $nip, Full Name: $full_name";
                    $currentUserID = $_SESSION['UserID'];
                    insertLogActivity($conn, $currentUserID, $activityDescription);

                    // Display a success SweetAlert notification
                    echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Teacher created successfully.",
        }).then(function() {
            window.location.href = "manage_teachers_list.php";
        });
    </script>';
                    exit();
                } else {
                    // Teacher creation failed
                    $errors['db_error'] = "Teacher creation failed.";

                    // Display an error SweetAlert notification
                    echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Teacher creation failed.",
        });
    </script>';
                }
            } else {
                $errors['db_error'] = "User creation failed.";
            }
        }
    } elseif ($account_selection === "existing") {
        // If "Use an Existing Account" is selected, process the existing user account
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);

        // Validate the User ID selection
        if (empty($user_id)) {
            $errors['user_id'] = "User ID is required for an existing account.";
        }

        // Check for errors related to existing account selection
        if (empty($errors)) {
            // Insert the teacher data with the selected User ID
            $insertTeacherQuery = "INSERT INTO Teachers (NIP, AcademicDegree, EducationLevel, EmploymentStatus, UserID)
                VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertTeacherQuery);
            $stmt->bind_param("ssssi", $nip, $academic_degree, $education_level, $employment_status, $user_id);

            // If teacher and user are created successfully
            if ($stmt->execute()) {
                // Teacher creation successful
                // Log the activity for teacher creation
                $activityDescription = "Teacher created: NIP: $nip, Full Name: $full_name";
                $currentUserID = $_SESSION['UserID'];
                insertLogActivity($conn, $currentUserID, $activityDescription);

                // Display a success SweetAlert notification
                echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Teacher created successfully.",
        }).then(function() {
            window.location.href = "manage_teachers_list.php";
        });
    </script>';
                exit();
            } else {
                // Teacher creation failed
                $errors['db_error'] = "Teacher creation failed.";

                // Display an error SweetAlert notification
                echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Teacher creation failed.",
        });
    </script>';
            }
        }
    }
}

// Close the database connection
?>
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Create Teacher</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="manage_teachers_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Teacher creation form.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Teacher Creation Form -->
                    <form action="" method="POST" class="flex flex-col w-full space-x-2">
                        <!-- NIP -->
                        <label for="nip" class="block font-semibold text-gray-800 mt-2 mb-2">NIP <span class="text-red-500">*</span></label>
                        <input type="text" id="nip" name="nip" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="NIP" value="<?php echo $nip; ?>">
                        <?php if (isset($errors['nip'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['nip']; ?>
                            </p>
                        <?php endif; ?>
                        <!-- Academic Degree Field -->
                        <div id="academic_degree_field">
                            <label for="academic_degree" class="block font-semibold text-gray-800 mt-2 mb-2">Academic Degree</label>
                            <input type="text" id="academic_degree" name="academic_degree" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Academic Degree" value="<?php if (isset($academic_degree)) {
                                                                                                                                                                                                                echo $academic_degree;
                                                                                                                                                                                                            } ?>">
                        </div>

                        <!-- Education Level Field -->
                        <div id="education_level_field">
                            <label for="education_level" class="block font-semibold text-gray-800 mt-2 mb-2">Education Level</label>
                            <input type="text" id="education_level" name="education_level" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Education Level" value="<?php if (isset($education_level)) {
                                                                                                                                                                                                                echo $education_level;
                                                                                                                                                                                                            } ?>">
                        </div>

                        <!-- Employment Status Field -->
                        <div id="employment_status_field">
                            <label for="employment_status" class="block font-semibold text-gray-800 mt-2 mb-2">Employment Status</label>
                            <input type="text" id="employment_status" name="employment_status" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Employment Status" value="<?php if (isset($employment_status)) {
                                                                                                                                                                                                                    echo $employment_status;
                                                                                                                                                                                                                } ?>">
                        </div>

                        <!-- Account Selection Dropdown -->
                        <div>
                            <label for="account_selection" class="block font-semibold text-gray-800 mt-2 mb-2">Account Selection</label>
                            <select id="account_selection" name="account_selection" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                                <option value="existing">Use an Existing Account</option>
                                <option value="new">Create a New Account</option>
                            </select>
                        </div>

                        <!-- User ID Search Field (Dropdown) -->
                        <div id="user_id_search">
                            <label for="user_id" class="block font-semibold text-gray-800 mt-2 mb-2">User ID</label>
                            <select id="user_id" name="user_id" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                                <option value="">Select User ID</option>
                                <?php
                                // Loop through user data and create dropdown options
                                foreach ($users as $userID => $fullName) {
                                    echo "<option value=\"$userID\">$fullName</option>";
                                }
                                ?>
                            </select>
                        </div>


                        <!-- Username Field -->
                        <div id="username_field" style="display: none">
                            <label for="username" class="block font-semibold text-gray-800 mt-2 mb-2">Username *</label>
                            <input type="text" id="username" name="username" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Username">
                        </div>

                        <!-- Password Field -->
                        <div id="password_field" style="display: none">
                            <label for="password" class="block font-semibold text-gray-800 mt-2 mb-2">Password *</label>
                            <input type="password" id="password" name="password" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Password">
                        </div>

                        <!-- Email Field -->
                        <div id="email_field" style="display: none">
                            <label for="email" class="block font-semibold text-gray-800 mt-2 mb-2">Email *</label>
                            <input type="email" id="email" name="email" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Email">
                        </div>

                        <!-- Full Name Field -->
                        <div id="full_name_field" style="display: none">
                            <label for="full_name" class="block font-semibold text-gray-800 mt-2 mb-2">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Full Name">
                        </div>

                        <!-- Date of Birth Field -->
                        <div id="date_of_birth_field" style="display: none">
                            <label for="date_of_birth" class="block font-semibold text-gray-800 mt-2 mb-2">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                        </div>

                        <!-- Gender Field -->
                        <div id="gender_field" style="display: none">
                            <label class="block font-semibold text-gray-800 mt-2 mb-2">Gender</label>
                            <div class="mt-2">
                                <label for="male" class="inline-flex items-center">
                                    <input type="radio" id="male" name="gender" value="Male" class="form-radio text-indigo-600">
                                    <span class="ml-2">Male</span>
                                </label>
                                <label for="female" class="inline-flex items-center">
                                    <input type="radio" id="female" name="gender" value="Female" class="form-radio text-indigo-600">
                                    <span class="ml-2">Female</span>
                                </label>
                                <label for="other" class="inline-flex items-center">
                                    <input type="radio" id="other" name="gender" value="Other" class="form-radio text-indigo-600">
                                    <span class="ml-2">Other</span>
                                </label>
                            </div>
                        </div>

                        <!-- Address Field -->
                        <div id="address_field" style="display: none">
                            <label for="address" class="block font-semibold text-gray-800 mt-2 mb-2">Address</label>
                            <input type="text" id="address" name="address" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Address">
                        </div>

                        <!-- Phone Number Field -->
                        <div id="phone_number_field" style="display: none">
                            <label for="phone_number" class="block font-semibold text-gray-800 mt-2 mb-2">Phone Number</label>
                            <input type="text" id="phone_number" name="phone_number" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Phone Number">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Create Teacher</span>
                        </button>
                    </form>
                    <!-- End Teacher Creation Form -->
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
</body>
<script>
    const accountSelection = document.getElementById("account_selection");
    const userIdSearchField = document.getElementById("user_id_search");
    const fieldsToToggle = ["username_field", "password_field", "email_field", "full_name_field", "date_of_birth_field", "gender_field", "address_field", "phone_number_field"];

    accountSelection.addEventListener("change", function() {
        const selectionValue = accountSelection.value;
        if (selectionValue === "existing") {
            userIdSearchField.style.display = "block"; // Tampilkan User ID Search Field
        } else {
            userIdSearchField.style.display = "none"; // Sembunyikan User ID Search Field
        }

        // Toggle field visibility based on Account Selection
        fieldsToToggle.forEach(function(fieldName) {
            const field = document.getElementById(fieldName);
            if (selectionValue === "existing") {
                field.style.display = "none"; // Sembunyikan field jika Use an Existing Account dipilih
            } else {
                field.style.display = "block"; // Tampilkan field jika Create a New Account dipilih
            }
        });
    });
</script>

</html>