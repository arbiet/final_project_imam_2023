<?php
include_once('../components/header.php');
session_start();
require_once('../../database/connection.php');

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}
// Save a log activity for the logout
$activityDescription = 'User logged out';
insertLogActivity($conn, $_SESSION['UserID'], $activityDescription);

// Logout the user by unsetting and destroying the session
session_unset();
session_destroy();
// Redirect to the login page with a success message
echo '<script>
    Swal.fire({
        icon: "success",
        title: "Logged Out Successfully!",
        showConfirmButton: false,
        timer: 2000
    }).then(function(){
        window.location.href = "login.php";
    });
</script>';
exit();
?>
</body>

</html>