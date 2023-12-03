<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$handlingID = '';
$errors = array();
$violationHandlingData = array();

// Retrieve violation handling data
if (isset($_GET['id'])) {
    $handlingID = $_GET['id'];
    $query = "SELECT * FROM MasterViolationHandlings WHERE HandlingID = $handlingID";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $violationHandlingData = $result->fetch_assoc();
    } else {
        $errors[] = "Violation handling not found.";
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Violation Handling Details</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="../manage_master_violationhandlings/manage_master_violationhandlings_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Violation handling information.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Violation Handling Details -->
                    <?php if (!empty($violationHandlingData)) : ?>
                        <div class="bg-white shadow-md p-4 rounded-md">
                            <h3 class="text-lg font-semibold text-gray-800">Violation Handling Information</h3>
                            <p><strong>Violation Category:</strong> <?php echo $violationHandlingData['ViolationCategory']; ?></p>
                            <p><strong>Score Range Bottom:</strong> <?php echo $violationHandlingData['ScoreRangeBottom']; ?></p>
                            <p><strong>Score Range Top:</strong> <?php echo $violationHandlingData['ScoreRangeTop']; ?></p>
                            <p><strong>Follow-Up Action:</strong> <?php echo $violationHandlingData['FollowUpAction']; ?></p>
                        </div>
                    <?php else : ?>
                        <div class="bg-white shadow-md p-4 rounded-md">
                            <p>No violation handling data available.</p>
                        </div>
                    <?php endif; ?>
                    <!-- End Violation Handling Details -->
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