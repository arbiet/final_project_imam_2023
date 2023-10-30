<?php
session_start();
require_once('../../database/connection.php');
include_once('../components/header.php');

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
  header('Location: login.php');
  exit();
}

$errors = array();

// Check if a file has been uploaded
if (isset($_FILES['profile_image'])) {
  // Define the target directory for storing images
  $target_dir = '../static/image/profile/';
  $target_file = $target_dir . basename($_FILES['profile_image']['name']);
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  // Check if the uploaded file is an image
  $check = getimagesize($_FILES['profile_image']['tmp_name']);
  if ($check === false) {
    $errors[] = 'File yang diunggah bukan gambar.';
  }

  // Limit the image file size (e.g., maximum 2MB)
  if ($_FILES['profile_image']['size'] > 2000000) {
    $errors[] = 'Ukuran gambar terlalu besar (maksimal 2MB).';
  }

  // Generate a unique name for the new image file
  $new_image_name = uniqid() . '.' . $imageFileType;
  $new_image_path = $target_dir . $new_image_name;

  // Move the uploaded image to the new location
  if (empty($errors)) {
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $new_image_path)) {
      // Update the profile URL in the database
      $user_id = $_SESSION['UserID'];
      $profile_url = $new_image_name;

      $update_query = "UPDATE Users SET ProfilePictureURL = ? WHERE UserID = ?";
      $update_stmt = $conn->prepare($update_query);
      $update_stmt->bind_param('si', $profile_url, $user_id);

      if ($update_stmt->execute()) {
        // Get the old profile picture URL from the session
        $oldProfilePictureURL = $_SESSION['ProfilePictureURL'];
        $_SESSION['ProfilePictureURL'] = $profile_url;
        $activityDescription = 'Changed profile picture from ' . $oldProfilePictureURL . ' to ' . $profile_url;
        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);

        // Display a success message using SweetAlert2
        echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Gambar profil berhasil diunggah.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = 'profile.php'; // Redirect to the profile page
                    });
                </script>";
        exit();
      } else {
        $errors[] = 'Gagal mengupdate URL gambar profil.';
      }
    } else {
      $errors[] = 'Gagal mengunggah gambar.';
    }
  }
}

// If there are errors, display an error message
if (!empty($errors)) {
  echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengunggah gambar.',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = 'profile.php'; // Redirect to the profile page
        });
    </script>";
}

include('../components/footer.php');
