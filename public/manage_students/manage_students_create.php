<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$student_number = $religion = $parent_guardian_full_name = $parent_guardian_address = $parent_guardian_phone = $parent_guardian_email = $class_id = '';
$errors = array();
// Query untuk mengambil data pengguna
$query = "SELECT u.UserID, u.FullName
FROM Users u
LEFT JOIN Students s ON u.UserID = s.UserID
WHERE u.RoleID = (SELECT RoleID FROM Roles WHERE RoleName = 'student')
  AND s.StudentID IS NULL;
";
$result = $conn->query($query);

// Buat array asosiatif untuk menyimpan data pengguna
$users = array();

while ($row = $result->fetch_assoc()) {
  $users[$row['UserID']] = $row['FullName'];
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitize and validate the common input data
  $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
  $religion = mysqli_real_escape_string($conn, $_POST['religion']);
  $parent_guardian_full_name = mysqli_real_escape_string($conn, $_POST['parent_guardian_full_name']);
  $parent_guardian_address = mysqli_real_escape_string($conn, $_POST['parent_guardian_address']);
  $parent_guardian_phone = mysqli_real_escape_string($conn, $_POST['parent_guardian_phone']);
  $parent_guardian_email = mysqli_real_escape_string($conn, $_POST['parent_guardian_email']);
  $class_id = mysqli_real_escape_string($conn, $_POST['class_id']);

  // Check for errors
  if (empty($student_number)) {
    $errors['student_number'] = "Student Number is required.";
  }
  // Add validation rules for other common fields as needed...

  // Check "Account Selection" to determine the processing
  $account_selection = mysqli_real_escape_string($conn, $_POST['account_selection']);

  if ($account_selection === "new") {
    // If "Create a New Account" is selected, process the new account creation
    // Additional input fields for new account
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 3;
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

      // Insert data into the 'User' table, including AccountStatus and ActivationStatus
      $query = "INSERT INTO Users (UserID, Username, Password, Email, FullName, DateOfBirth, Gender, Address, PhoneNumber, RoleID, AccountStatus, ActivationStatus)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("ssssssssssss", $user_id, $username, $hashed_password, $email, $full_name, $date_of_birth, $gender, $address, $phone_number, $role, $account_status, $activation_status);

      if ($stmt->execute()) {
        // Get the auto-generated User ID
        $newUserID = $stmt->insert_id;
        // Insert the student data with the newly created User ID
        $insertStudentQuery = "INSERT INTO Students (StudentNumber, Religion, ParentGuardianFullName, ParentGuardianAddress, ParentGuardianPhoneNumber, ParentGuardianEmail, ClassID, UserID)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertStudentQuery);
        $stmt->bind_param("ssssssii", $student_number, $religion, $parent_guardian_full_name, $parent_guardian_address, $parent_guardian_phone, $parent_guardian_email, $class_id, $newUserID);

        // Jika student dan user berhasil dibuat
        if ($stmt->execute()) {
          // Class creation successful
          // Log the activity for class creation
          $activityDescription = "Student created: Student Number: $student_number, Parent/Guardian: $parent_guardian_full_name";
          $currentUserID = $_SESSION['UserID'];
          insertLogActivity($conn, $currentUserID, $activityDescription);

          // Tampilkan notifikasi SweetAlert untuk sukses
          echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Student created successfully.",
        }).then(function() {
            window.location.href = "manage_students_list.php";
        });
    </script>';
          exit();
        } else {
          // Student creation failed
          $errors['db_error'] = "Student creation failed.";

          // Tampilkan notifikasi SweetAlert untuk kegagalan
          echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Student creation failed.",
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
      $errors['user_id'] = "User ID is required for existing account.";
    }

    // Check for errors related to existing account selection
    if (empty($errors)) {
      // Insert the student data with the selected User ID
      $insertStudentQuery = "INSERT INTO Students (StudentNumber, Religion, ParentGuardianFullName, ParentGuardianAddress, ParentGuardianPhoneNumber, ParentGuardianEmail, ClassID, UserID)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($insertStudentQuery);
      $stmt->bind_param("ssssssii", $student_number, $religion, $parent_guardian_full_name, $parent_guardian_address, $parent_guardian_phone, $parent_guardian_email, $class_id, $user_id);

      // Jika student dan user berhasil dibuat
      if ($stmt->execute()) {
        // Class creation successful
        // Log the activity for class creation
        $activityDescription = "Student created: Student Number: $student_number, Parent/Guardian: $parent_guardian_full_name";
        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);

        // Tampilkan notifikasi SweetAlert untuk sukses
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Student created successfully.",
        }).then(function() {
            window.location.href = "manage_students_list.php";
        });
    </script>';
        exit();
      } else {
        // Student creation failed
        $errors['db_error'] = "Student creation failed.";

        // Tampilkan notifikasi SweetAlert untuk kegagalan
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Student creation failed.",
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
          <h1 class="text-3xl text-gray-800 font-semibold w-full">Create Student</h1>
          <div class="flex flex-row justify-end items-center">
            <a href="manage_students_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
              <p class="text-gray-600 text-sm">Student creation form.</p>
            </div>
          </div>
          <!-- End Navigation -->
          <!-- Student Creation Form -->
          <form action="" method="POST" class="flex flex-col w-full space-x-2">
            <!-- Student Number -->
            <label for="student_number" class="block font-semibold text-gray-800 mt-2 mb-2">Student Number <span class="text-red-500">*</span></label>
            <input type="text" id="student_number" name="student_number" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Student Number" value="<?php echo $student_number; ?>">
            <?php if (isset($errors['student_number'])) : ?>
              <p class="text-red-500 text-sm">
                <?php echo $errors['student_number']; ?>
              </p>
            <?php endif; ?>

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
                // Loop melalui data pengguna dan buat opsi dropdown
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

            <!-- Religion -->
            <label for="religion" class="block font-semibold text-gray-800 mt-2 mb-2">Religion</label>
            <input type="text" id="religion" name="religion" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Religion" value="<?php echo $religion; ?>">

            <!-- Parent/Guardian Full Name -->
            <label for="parent_guardian_full_name" class="block font-semibold text-gray-800 mt-2 mb-2">Parent/Guardian Full Name</label>
            <input type="text" id="parent_guardian_full_name" name="parent_guardian_full_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Parent/Guardian Full Name" value="<?php echo $parent_guardian_full_name; ?>">

            <!-- Parent/Guardian Address -->
            <label for="parent_guardian_address" class="block font-semibold text-gray-800 mt-2 mb-2">Parent/Guardian Address</label>
            <input type="text" id="parent_guardian_address" name="parent_guardian_address" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Parent/Guardian Address" value="<?php echo $parent_guardian_address; ?>">

            <!-- Parent/Guardian Phone -->
            <label for="parent_guardian_phone" class="block font-semibold text-gray-800 mt-2 mb-2">Parent/Guardian Phone</label>
            <input type="text" id="parent_guardian_phone" name="parent_guardian_phone" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Parent/Guardian Phone" value="<?php echo $parent_guardian_phone; ?>">

            <!-- Parent/Guardian Email -->
            <label for="parent_guardian_email" class="block font-semibold text-gray-800 mt-2 mb-2">Parent/Guardian Email</label>
            <input type="text" id="parent_guardian_email" name="parent_guardian_email" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Parent/Guardian Email" value="<?php echo $parent_guardian_email; ?>">

            <!-- Class ID -->
            <label for="class_id" class="block font-semibold text-gray-800 mt-2 mb-2">Class</label>
            <select id="class_id" name="class_id" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
              <option value="">Select a Class</option>
              <?php
              // Fetch class data from the database
              $query = "SELECT ClassID, ClassName FROM Classes";
              $result = $conn->query($query);

              while ($row = $result->fetch_assoc()) {
                $classID = $row['ClassID'];
                $className = $row['ClassName'];

                // Populate the select list with class options
                echo "<option value=\"$classID\">$className</option>";
              }
              ?>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
              <i class="fas fa-check mr-2"></i>
              <span>Create Student</span>
            </button>
          </form>
          <!-- End Student Creation Form -->
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