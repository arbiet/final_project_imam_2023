<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$teacherID = '';
$errors = array();
$teacherData = array();

// Retrieve teacher data
if (isset($_GET['id'])) {
    $teacherID = $_GET['id'];
    $query = "SELECT t.TeacherID, t.NIP, t.AcademicDegree, t.EducationLevel, t.EmploymentStatus, u.Username, u.Email, u.FullName, u.DateOfBirth, u.Gender, u.Address, u.PhoneNumber, r.RoleName
              FROM Teachers t
              LEFT JOIN Users u ON t.UserID = u.UserID
              LEFT JOIN Roles r ON u.RoleID = r.RoleID
              WHERE t.TeacherID = $teacherID";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $teacherData = $result->fetch_assoc();
    } else {
        $errors[] = "Teacher not found.";
    }
}

?>
<?php include_once('../components/header.php'); ?>
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
        <main class="bg-gray-50 flex flex-col flex-1 overflow-y-scroll h-screen flex-shrink-0 sc-hide pb-40">
            <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                <!-- Header Content -->
                <div class="flex flex-row justify-between items-center w-full border-b-2 border-gray-600 mb-2 pb-2">
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Teacher Details</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="../manage_teachers/manage_teachers_list.php" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Teacher information.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Teacher Details -->
                    <?php if (!empty($teacherData)) : ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white shadow-md p-4 rounded-md">
                                <h3 class="text-lg font-semibold text-gray-800">Teacher Information</h3>
                                <p><strong>NIP:</strong> <?php echo $teacherData['NIP']; ?></p>
                                <p><strong>Academic Degree:</strong> <?php echo $teacherData['AcademicDegree']; ?></p>
                                <p><strong>Education Level:</strong> <?php echo $teacherData['EducationLevel']; ?></p>
                                <p><strong>Employment Status:</strong> <?php echo $teacherData['EmploymentStatus']; ?></p>
                                <p><strong>Username:</strong> <?php echo $teacherData['Username']; ?></p>
                                <p><strong>Email:</strong> <?php echo $teacherData['Email']; ?></p>
                            </div>
                            <div class="bg-white shadow-md p-4 rounded-md">
                                <h3 class="text-lg font-semibold text-gray-800">Personal Information</h3>
                                <p><strong>Full Name:</strong> <?php echo $teacherData['FullName']; ?></p>
                                <p><strong>Date of Birth:</strong> <?php echo $teacherData['DateOfBirth']; ?></p>
                                <p><strong>Gender:</strong> <?php echo $teacherData['Gender']; ?></p>
                                <p><strong>Address:</strong> <?php echo $teacherData['Address']; ?></p>
                                <p><strong>Phone Number:</strong> <?php echo $teacherData['PhoneNumber']; ?></p>
                                <p><strong>Role:</strong> <?php echo $teacherData['RoleName']; ?></p>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="bg-white shadow-md p-4 rounded-md">
                            <p>No teacher data available.</p>
                        </div>
                    <?php endif; ?>
                    <!-- End Teacher Details -->
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