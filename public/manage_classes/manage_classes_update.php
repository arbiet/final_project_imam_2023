<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$class_id = $class_name = $education_level = $homeroom_teacher = $curriculum = $academic_year = '';
$errors = array();

// Retrieve the class data to be updated (you might need to pass the class ID to this page)
if (isset($_GET['id'])) {
    $class_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch the existing class data
    $query = "SELECT * FROM Classes WHERE ClassID = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $class = $result->fetch_assoc();

    // Check if the class exists
    if (!$class) {
        // Class not found, handle accordingly (e.g., redirect to an error page)
    } else {
        // Populate variables with existing class data
        $class_name = $class['ClassName'];
        $education_level = $class['EducationLevel'];
        $homeroom_teacher = $class['HomeroomTeacher'];
        $curriculum = $class['Curriculum'];
        $academic_year = $class['AcademicYear'];
        // You can also retrieve other fields as needed
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data (similar to create class form)
    $class_name = mysqli_real_escape_string($conn, $_POST['class_name']);
    $education_level = mysqli_real_escape_string($conn, $_POST['education_level']);
    $homeroom_teacher = mysqli_real_escape_string($conn, $_POST['homeroom_teacher']);
    $curriculum = mysqli_real_escape_string($conn, $_POST['curriculum']);
    $academic_year = mysqli_real_escape_string($conn, $_POST['academic_year']);
    // You should validate the fields and handle errors as needed

    // Update class data in the database
    $query = "UPDATE Classes 
              SET ClassName = ?, EducationLevel = ?, HomeroomTeacher = ?, Curriculum = ?, AcademicYear = ? 
              WHERE ClassID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $class_name, $education_level, $homeroom_teacher, $curriculum, $academic_year, $class_id);

    if ($stmt->execute()) {
        // Class update successful
        // Log the activity for class update
        $activityDescription = "Class updated: $class_name, Academic Year: $academic_year";
        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);
        // Tampilkan notifikasi SweetAlert untuk sukses
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Class update successfully.",
        }).then(function() {
            window.location.href = "manage_classes_list.php";
        });
    </script>';
        exit();
    } else {
        // Class update failed
        $errors['db_error'] = "Class update failed.";

        // Tampilkan notifikasi SweetAlert untuk kegagalan
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Class update failed.",
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Update Class</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="manage_classes_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Update class information form.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Class Update Form -->
                    <form action="" method="POST" class="flex flex-col w-full space-x-2">
                        <!-- Class Name -->
                        <label for="class_name" class="block font-semibold text-gray-800 mt-2 mb-2">Class Name <span class="text-red-500">*</span></label>
                        <input type="text" id="class_name" name="class_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Class Name" value="<?php echo $class_name; ?>">
                        <?php if (isset($errors['class_name'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['class_name']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Education Level -->
                        <label for="education_level" class="block font-semibold text-gray-800 mt-2 mb-2">Education Level</label>
                        <input type="text" id="education_level" name="education_level" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Education Level" value="<?php echo $education_level; ?>">

                        <?php

                        // Fetch the list of teachers who are not homeroom teachers
                        $query = "SELECT Teachers.TeacherID, Users.FullName, Teachers.AcademicDegree
                        FROM Teachers
                        INNER JOIN Users ON Teachers.UserID = Users.UserID
                        WHERE Teachers.TeacherID NOT IN (SELECT HomeroomTeacher FROM Classes)";

                        $result = $conn->query($query);

                        // Check for errors in the database query
                        if (!$result) {
                            die("Database query failed: " . $conn->error);
                        }

                        // Close the database connection
                        $conn->close();

                        ?>

                        <!-- Homeroom Teacher -->
                        <label for="homeroom_teacher" class="block font-semibold text-gray-800 mt-2 mb-2">Homeroom Teacher</label>
                        <select id="homeroom_teacher" name="homeroom_teacher" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                            <?php
                            // Iterate through the retrieved teachers and populate the select field
                            while ($row = $result->fetch_assoc()) {
                                $teacherID = $row['TeacherID'];
                                $teacherName = $row['FullName'];
                                $AcademicDegree = $row['AcademicDegree'];
                                echo "<option value='$teacherID'>$teacherName, $AcademicDegree</option>";
                            }
                            ?>
                        </select>

                        <!-- Curriculum -->
                        <label for="curriculum" class="block font-semibold text-gray-800 mt-2 mb-2">Curriculum</label>
                        <input type="text" id="curriculum" name="curriculum" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Curriculum" value="<?php echo $curriculum; ?>">

                        <!-- Academic Year -->
                        <label for="academic_year" class="block font-semibold text-gray-800 mt-2 mb-2">Academic Year</label>
                        <input type="text" id="academic_year" name="academic_year" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Academic Year" value="<?php echo $academic_year; ?>">

                        <!-- Submit Button -->
                        <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Update Class</span>
                        </button>
                    </form>
                    <!-- End Class Update Form -->
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