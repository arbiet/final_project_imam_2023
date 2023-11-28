<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$classID = $_GET['id'];
$subjectID = '';
$errors = array();

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data
    $subjectID = mysqli_real_escape_string($conn, $_POST['subjectID']);

    // Check if the subject is already added to the class
    $checkQuery = "SELECT * FROM ClassSubjects WHERE ClassID = ? AND SubjectID = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ss", $classID, $subjectID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $errors['subjectID'] = "This subject is already added to the class.";
    }

    // If there are no errors, insert the data into the ClassSubjects table
    if (empty($errors)) {
        $query = "INSERT INTO ClassSubjects (ClassID, SubjectID) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $classID, $subjectID);

        if ($stmt->execute()) {
            // Subject added to the class successfully
            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Subject added to the class successfully.',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location.href = 'manage_classes_detail.php?id=" . $classID . "'; // Redirect to class details page
            });
        </script>";
            exit();
        } else {
            // Subject addition failed
            $errors['db_error'] = "Subject addition failed.";
        }
    }
}


// Query to retrieve the list of available subjects
$querySubjects = "SELECT * FROM Subjects
                 WHERE SubjectID NOT IN (SELECT SubjectID FROM ClassSubjects WHERE ClassID = ?)";
$stmtSubjects = $conn->prepare($querySubjects);
$stmtSubjects->bind_param("s", $classID);
$stmtSubjects->execute();
$resultSubjects = $stmtSubjects->get_result();

// Close the statement
$stmtSubjects->close();
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Add Subject to Class</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="../manage_classes/manage_classes_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <!-- End Header Content -->
                <!-- Content -->
                <div class="flex flex-col w-full">
                    <!-- Subject Addition Form -->
                    <form action="" method="POST" class="flex flex-col w-full space-x-2">
                        <!-- Select Subject -->
                        <label for="subjectID" class="block font-semibold text-gray-800 mt-2 mb-2">Select Subject <span class="text-red-500">*</span></label>
                        <select id="subjectID" name="subjectID" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                            <option value="">-- Select a Subject --</option>
                            <?php
                            while ($subject = $resultSubjects->fetch_assoc()) {
                                echo "<option value='" . $subject['SubjectID'] . "'>" . $subject['SubjectName'] . "</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['subjectID'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['subjectID']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Submit Button -->
                        <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Add Subject to Class</span>
                        </button>
                    </form>
                    <!-- End Subject Addition Form -->
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