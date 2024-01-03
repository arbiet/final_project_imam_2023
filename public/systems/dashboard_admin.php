<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Count total number of students in the class
$queryTotalStudents = "SELECT COUNT(*) AS TotalStudents FROM Students";
$resultTotalStudents = mysqli_query($conn, $queryTotalStudents);
$totalStudents = mysqli_fetch_assoc($resultTotalStudents)['TotalStudents'];

// Count number of male students in the class
$queryMaleStudents = "SELECT COUNT(*) AS MaleStudents FROM Students
                     INNER JOIN Users ON Students.UserID = Users.UserID
                     WHERE Users.Gender = 'Male'";
$resultMaleStudents = mysqli_query($conn, $queryMaleStudents);
$maleStudents = mysqli_fetch_assoc($resultMaleStudents)['MaleStudents'];

// Count number of female students in the class
$queryFemaleStudents = "SELECT COUNT(*) AS FemaleStudents FROM Students
                       INNER JOIN Users ON Students.UserID = Users.UserID
                       WHERE Users.Gender = 'Female'";
$resultFemaleStudents = mysqli_query($conn, $queryFemaleStudents);
$femaleStudents = mysqli_fetch_assoc($resultFemaleStudents)['FemaleStudents'];

?>

<?php include('../components/header.php'); ?>
<div class="h-screen flex flex-col">
    <?php include('../components/navbar.php'); ?>
    <div class="bg-gray-50 flex flex-row shadow-md">
        <?php include('../components/sidebar.php'); ?>
        <main class="bg-gray-50 flex flex-col flex-1 overflow-y-scroll h-screen flex-shrink-0 sc-hide pb-40">
            <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                <h1 class="text-3xl text-gray-800 font-semibold border-b border-gray-200 w-full">Dashboard</h1>
                <h2 class="text-xl text-gray-800 font-semibold">
                    Welcome back, <?php echo $_SESSION['FullName']; ?>!
                    <?php
                    if ($_SESSION['RoleID'] === 'admin') {
                        echo " (Admin)";
                    } elseif ($_SESSION['RoleID'] === 'student') {
                        echo " (Student)";
                    }
                    ?>
                </h2>
                <p class="text-gray-600">Here's what's happening with your achievements and violations today.</p>

                <div class="grid grid-rows-4 gap-4 mt-4 w-full">
                    <div class="bg-blue-500 p-4 text-white rounded-md">
                        <i class="fas fa-users fa-2x"></i>
                        <p>Total Students</p>
                        <p class="text-2xl font-semibold"><?php echo $totalStudents; ?></p>
                    </div>

                    <div class="bg-green-500 p-4 text-white rounded-md">
                        <i class="fas fa-male fa-2x"></i>
                        <p>Male Students</p>
                        <p class="text-2xl font-semibold"><?php echo $maleStudents; ?></p>
                    </div>
                    <div class="bg-pink-500 p-4 text-white rounded-md">
                        <i class="fas fa-female fa-2x"></i>
                        <p>Female Students</p>
                        <p class="text-2xl font-semibold"><?php echo $femaleStudents; ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include('../components/footer.php'); ?>
</div>
</body>

</html>