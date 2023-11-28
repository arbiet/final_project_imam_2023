<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$student_id = $student_number = $religion = $parent_full_name = $parent_address = $parent_phone_number = $parent_email = $class_id = $user_id = '';
$errors = array();

// Retrieve the student's data to be updated (you might need to pass the student ID to this page)
if (isset($_GET['id'])) {
  $student_id = mysqli_real_escape_string($conn, $_GET['id']);

  // Query to fetch the existing student data
  $query = "SELECT s.StudentID, s.StudentNumber, s.Religion, s.ParentGuardianFullName, s.ParentGuardianAddress, s.ParentGuardianPhoneNumber, s.ParentGuardianEmail, s.ClassID, s.UserID, u.Username, u.Email, u.FullName, u.DateOfBirth, u.Gender, u.Address, u.PhoneNumber, u.RoleID
              FROM Students s
              INNER JOIN Users u ON s.UserID = u.UserID
              WHERE s.StudentID = ? LIMIT 1";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('s', $student_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $student = $result->fetch_assoc();

  // Check if the student exists
  if (!$student) {
    // Student not found, handle accordingly (e.g., redirect to an error page)
  } else {
    // Populate variables with existing student data
    $student_number = $student['StudentNumber'];
    $email = $student['Email'];
    $full_name = $student['FullName'];
    $religion = $student['Religion'];
    $date_of_birth = $student['DateOfBirth'];
    $address = $student['Address'];
    $username = $student['Username'];
    $gender = $student['Gender'];
    $phone_number = $student['PhoneNumber'];
    $parent_full_name = $student['ParentGuardianFullName'];
    $parent_address = $student['ParentGuardianAddress'];
    $parent_phone_number = $student['ParentGuardianPhoneNumber'];
    $parent_email = $student['ParentGuardianEmail'];
    $class_id = $student['ClassID'];
    $user_id = $student['UserID'];

    // You can also retrieve other fields as needed, such as $user_id, $username, $email, etc.
  }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitize and validate the input data
  $new_username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
  $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']) ?? null;
  $gender = mysqli_real_escape_string($conn, $_POST['gender']) ?? null;
  $address = mysqli_real_escape_string($conn, $_POST['address']) ?? null;
  $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']) ?? null;
  $religion = mysqli_real_escape_string($conn, $_POST['religion']);
  $parent_full_name = mysqli_real_escape_string($conn, $_POST['parent_full_name']);
  $parent_address = mysqli_real_escape_string($conn, $_POST['parent_address']);
  $parent_phone_number = mysqli_real_escape_string($conn, $_POST['parent_phone_number']);
  $parent_email = mysqli_real_escape_string($conn, $_POST['parent_email']);
  $class_id = mysqli_real_escape_string($conn, $_POST['class_id']);

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
      // Now, update the corresponding data in the Students table
      $update_students_query = "UPDATE Students 
                              SET StudentNumber = ?, Religion = ?, ParentGuardianFullName = ?, ParentGuardianAddress = ?, ParentGuardianPhoneNumber = ?, ParentGuardianEmail = ?, ClassID = ?
                              WHERE UserID = ?";
      $stmt_students = $conn->prepare($update_students_query);
      $stmt_students->bind_param("ssssssss", $student_number, $religion, $parent_full_name, $parent_address, $parent_phone_number, $parent_email, $class_id, $user_id);

      if ($stmt_students->execute()) {
        // Registration successful
        $activityDescription = "Student with Username: $new_username has been updated.";

        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);

        // Tampilkan notifikasi SweetAlert untuk sukses
        echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Student update successfully.",
                }).then(function() {
                    window.location.href = "manage_students_list.php";
                });
            </script>';
        exit();
      } else {
        // Registration failed
        $errors['db_error'] = "Student update failed.";

        // Tampilkan notifikasi SweetAlert untuk kegagalan
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Student update failed.",
                });
            </script>';
      }
    }
  }
}

// Close the database connection
?>

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
          <h1 class="text-3xl text-gray-800 font-semibold w-full">Update Student</h1>
          <div class="flex flex-row justify-end items-center">
            <a href="manage_students_list.php" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
              <p class="text-gray-600 text-sm">Update student information form.</p>
            </div>
          </div>
          <!-- End Navigation -->
          <!-- Student Update Form -->
          <form action="" method="POST" class="flex flex-col w-full space-x-2">
            <!-- Student Number -->
            <label for="student_number" class="block font-semibold text-gray-800 mt-2 mb-2">Student Number <span class="text-red-500">*</span></label>
            <input type="text" id="student_number" name="student_number" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Student Number" value="<?php echo $student_number; ?>">
            <?php if (isset($errors['student_number'])) : ?>
              <p class="text-red-500 text-sm">
                <?php echo $errors['student_number']; ?>
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

            <!-- Gender -->
            <label for="gender" class="block font-semibold text-gray-800 mt-2 mb-2">Gender</label>
            <select id="gender" name="gender" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
              <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
              <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
              <option value="Other" <?php if ($gender === 'Other') echo 'selected'; ?>>Other</option>
            </select>

            <!-- Address -->
            <label for="address" class="block font-semibold text-gray-800 mt-2 mb-2">Address</label>
            <textarea id="address" name="address" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Address"><?php echo $address; ?></textarea>

            <!-- Phone Number -->
            <label for="phone_number" class="block font-semibold text-gray-800 mt-2 mb-2">Phone Number</label>
            <input type="tel" id="phone_number" name="phone_number" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Phone Number" value="<?php echo $phone_number; ?>">

            <!-- Religion -->
            <label for="religion" class="block font-semibold text-gray-800 mt-2 mb-2">Religion</label>
            <input type="text" id="religion" name="religion" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Religion" value="<?php echo $religion; ?>">

            <!-- Parent/Guardian Full Name -->
            <label for="parent_full_name" class="block font-semibold text-gray-800 mt-2 mb-2">Parent/Guardian Full Name</label>
            <input type="text" id="parent_full_name" name="parent_full_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Parent/Guardian Full Name" value="<?php echo $parent_full_name; ?>">

            <!-- Parent/Guardian Address -->
            <label for="parent_address" class="block font-semibold text-gray-800 mt-2 mb-2">Parent/Guardian Address</label>
            <input type="text" id="parent_address" name="parent_address" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Parent/Guardian Address" value="<?php echo $parent_address; ?>">

            <!-- Parent/Guardian Phone Number -->
            <label for="parent_phone_number" class="block font-semibold text-gray-800 mt-2 mb-2">Parent/Guardian Phone Number</label>
            <input type="text" id="parent_phone_number" name="parent_phone_number" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Parent/Guardian Phone Number" value="<?php echo $parent_phone_number; ?>">

            <!-- Parent/Guardian Email -->
            <label for="parent_email" class="block font-semibold text-gray-800 mt-2 mb-2">Parent/Guardian Email</label>
            <input type="email" id="parent_email" name="parent_email" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Parent/Guardian Email" value="<?php echo $parent_email; ?>">

            <!-- Class ID -->
            <label for="class_id" class="block font-semibold text-gray-800 mt-2 mb-2">Class ID</label>
            <select id="class_id" name="class_id" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
              <?php
              // Query untuk mengambil daftar kelas yang tersedia
              $query = "SELECT ClassID, ClassName FROM Classes";
              $result = $conn->query($query);

              // Loop melalui hasil query dan membuat pilihan untuk setiap kelas
              while ($row = $result->fetch_assoc()) {
                $classID = $row['ClassID'];
                $className = $row['ClassName'];
                $selected = ($class_id == $classID) ? 'selected' : '';

                echo "<option value=\"$classID\" $selected>$className</option>";
              }
              ?>
            </select>


            <!-- Submit Button -->
            <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
              <i class="fas fa-check mr-2"></i>
              <span>Update Student</span>
            </button>
          </form>
          <!-- End Student Update Form -->
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