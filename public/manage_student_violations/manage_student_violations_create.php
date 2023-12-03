<?php
// Start a session to manage user data across pages
session_start();

// Include the database connection file
require_once('../../database/connection.php');

// Include the header component
include_once('../components/header.php');

// Initialize variables
$violation_type = $violation_name = $date = $time = '';
$errors = array();

// Fetch student data for the dropdown
$queryStudents = "SELECT s.StudentID, u.FullName
                  FROM Students s
                  JOIN Users u ON s.UserID = u.UserID";
$resultStudents = $conn->query($queryStudents);

// Fetch master violations data for the dropdown
$queryMasterViolations = "SELECT ViolationID, CONCAT(ViolationType, ' - ', ViolationName) AS ViolationInfo
                          FROM MasterViolations";
$resultMasterViolations = $conn->query($queryMasterViolations);

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
    $violation_id = isset($_POST['violation_id']) ? (int)$_POST['violation_id'] : 0;

    // Check for required fields
    if (empty($date) || empty($time) || empty($student_id) || empty($violation_id)) {
        $errors['required_fields'] = "All fields are required.";
    }

    // Additional validations for date and time
    if (!validateDate($date)) {
        $errors['date'] = "Invalid date format.";
    }

    if (!validateTime($time)) {
        $errors['time'] = "Invalid time format.";
    }

    // If there are no errors, insert the data into the database
    if (empty($errors)) {
        $query = "INSERT INTO StudentViolations (StudentID, ViolationID, Date, Time)
                  VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiss", $student_id, $violation_id, $date, $time);

        if ($stmt->execute()) {
            // Violation creation successful
            // Log the activity for violation creation
            $activityDescription = "Student Violation created: $student_id and violation $violation_id";
            $currentUserID = $_SESSION['UserID'];
            insertLogActivity($conn, $currentUserID, $activityDescription);

            // Display success message and redirect
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Student Violation created successfully.",
                }).then(function() {
                    window.location.href = "manage_student_violations_list.php";
                });
            </script>';
            exit();
        } else {
            // Violation creation failed
            $errors['db_error'] = "Student Violation creation failed.";

            // Display error message
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Student Violation creation failed.",
                });
            </script>';
        }
    }
}

// Function to validate date format
function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Function to validate time format
function validateTime($time)
{
    $t = DateTime::createFromFormat('H:i', $time);
    return $t && $t->format('H:i') === $time;
}

?>

<body class="bg-gray-50">
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
                        <h1 class="text-3xl text-gray-800 font-semibold w-full">Create Student Violation</h1>
                        <div class="flex flex-row justify-end items-center">
                            <a href="manage_student_violations_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                                <p class="text-gray-600 text-sm">Student Violation creation form.</p>
                            </div>
                        </div>
                        <!-- End Navigation -->

                        <!-- Student Violation Creation Form -->
                        <form action="" method="POST" class="flex flex-col w-full space-x-2">
                            <!-- Student Selector -->
                            <label for="student_id" class="block font-semibold text-gray-800 mt-2 mb-2">Select Student <span class="text-red-500">*</span></label>
                            <select id="student_id" name="student_id" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                                <?php while ($rowStudent = $resultStudents->fetch_assoc()) : ?>
                                    <option value="<?php echo $rowStudent['StudentID']; ?>"><?php echo $rowStudent['FullName']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <?php if (isset($errors['student_id'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['student_id']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Master Violation Selector -->
                            <label for="violation_id" class="block font-semibold text-gray-800 mt-2 mb-2">Select Master Violation <span class="text-red-500">*</span></label>
                            <select id="violation_id" name="violation_id" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                                <?php while ($rowViolation = $resultMasterViolations->fetch_assoc()) : ?>
                                    <option value="<?php echo $rowViolation['ViolationID']; ?>"><?php echo $rowViolation['ViolationInfo']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <?php if (isset($errors['violation_id'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['violation_id']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Date -->
                            <label for="date" class="block font-semibold text-gray-800 mt-2 mb-2">Date <span class="text-red-500">*</span></label>
                            <input type="date" id="date" name="date" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" value="<?php echo $date; ?>">
                            <?php if (isset($errors['date'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['date']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Time -->
                            <label for="time" class="block font-semibold text-gray-800 mt-2 mb-2">Time <span class="text-red-500">*</span></label>
                            <input type="time" id="time" name="time" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" value="<?php echo $time; ?>">
                            <?php if (isset($errors['time'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['time']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Submit Button -->
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                                <i class="fas fa-check mr-2"></i>
                                <span>Create Student Violation</span>
                            </button>
                        </form>
                        <!-- End Student Violation Creation Form -->
                    </div>
                    <!-- End Content -->
                </div>
            </main>
            <!-- End Main Content -->
        </div>
        <!-- End Main Content -->

        <!-- Include the footer component -->
        <?php include('../components/footer.php'); ?>
        <!-- End Footer -->
    </div>
    <!-- End Main Content -->
</body>

</html>