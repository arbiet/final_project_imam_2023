<?php
// Initialize the session
session_start();

// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$studentViolationID = '';
$errors = array();
$studentViolationData = array();

// Retrieve student violation data
if (isset($_GET['id'])) {
    $studentViolationID = $_GET['id'];
    $query = "SELECT sv.*, u.FullName AS StudentName, mv.ViolationType, mv.Points, mv.ViolationName
              FROM StudentViolations sv
              JOIN Students s ON sv.StudentID = s.StudentID
              JOIN Users u ON s.UserID = u.UserID
              JOIN MasterViolations mv ON sv.ViolationID = mv.ViolationID
              WHERE StudentViolationID = $studentViolationID";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $studentViolationData = $result->fetch_assoc();
    } else {
        $errors[] = "Student violation not found.";
    }
}
?>

<?php include_once('../components/header.php'); ?>
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Student Violation Details</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="../manage_student_violations/manage_student_violations_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Student violation information.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Student Violation Details -->
                    <?php if (!empty($studentViolationData)) : ?>
                        <div class="bg-white shadow-md p-4 rounded-md">
                            <h3 class="text-lg font-semibold text-gray-800">Student Violation Information</h3>
                            <p><strong>Student Name:</strong> <?php echo $studentViolationData['StudentName']; ?></p>
                            <p><strong>Violation Type:</strong> <?php echo $studentViolationData['ViolationType']; ?></p>
                            <p><strong>Violation Name:</strong> <?php echo $studentViolationData['ViolationName']; ?></p>
                            <p><strong>Points:</strong> <?php echo $studentViolationData['Points']; ?></p>
                            <p><strong>Date:</strong> <?php echo $studentViolationData['Date']; ?></p>
                            <p><strong>Time:</strong> <?php echo $studentViolationData['Time']; ?></p>
                        </div>
                    <?php else : ?>
                        <div class="bg-white shadow-md p-4 rounded-md">
                            <p>No student violation data available.</p>
                        </div>
                    <?php endif; ?>
                    <!-- End Student Violation Details -->
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
</body>

</html>