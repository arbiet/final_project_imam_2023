<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$classID = '';
$errors = array();
$classData = array();
$studentsData = array();

// Retrieve class data
if (isset($_GET['id'])) {
  $classID = $_GET['id'];
  $query = "SELECT c.ClassID, c.ClassName, c.EducationLevel, t.AcademicDegree, u.FullName AS HomeroomTeacher, c.Curriculum, c.AcademicYear, c.ClassCode
              FROM Classes c
              LEFT JOIN Teachers t ON c.HomeroomTeacher = t.TeacherID
              LEFT JOIN Users u ON t.UserID = u.UserID
              WHERE c.ClassID = $classID";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    $classData = $result->fetch_assoc();
  } else {
    $errors[] = "Class not found.";
  }

  // Retrieve students in the class
  $query = "SELECT u.FullName, u.PhoneNumber, u.Email, c.ClassName, s.StudentID
              FROM Students s
              INNER JOIN Users u ON s.UserID = u.UserID
              LEFT JOIN Classes c ON s.ClassID = c.ClassID
              WHERE s.ClassID = $classID";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $studentsData[] = $row;
    }
  }
  // Query to retrieve the associated subjects for the selected class
  $query = "SELECT * FROM ClassSubjects
          INNER JOIN Subjects ON ClassSubjects.SubjectID = Subjects.SubjectID
          WHERE ClassSubjects.ClassID = $classID";

  $result = $conn->query($query);

  $subjectsData = array();

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $subjectsData[] = $row;
    }
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
          <h1 class="text-3xl text-gray-800 font-semibold w-full">Class Details</h1>
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
          <!-- Navigation -->
          <div class="flex flex-row justify-between items-center w-full mb-2 pb-2">
            <div>
              <h2 class="text-lg text-gray-800 font-semibold">Welcome back, <?php echo $_SESSION['FullName']; ?>!</h2>
              <p class="text-gray-600 text-sm">Class information.</p>
            </div>
          </div>
          <!-- End Navigation -->
          <!-- Class Details -->
          <?php if (!empty($classData)) : ?>
            <div class="bg-white shadow-md p-4 rounded-md">
              <h3 class="text-lg font-semibold text-gray-800">Class Information</h3>
              <p><strong>Class Name:</strong> <?php echo $classData['ClassName']; ?></p>
              <p><strong>Education Level:</strong> <?php echo $classData['EducationLevel']; ?></p>
              <p><strong>Homeroom Teacher:</strong> <?php echo $classData['HomeroomTeacher']; ?></p>
              <p><strong>Academic Degree:</strong> <?php echo $classData['AcademicDegree']; ?></p>
              <p><strong>Curriculum:</strong> <?php echo $classData['Curriculum']; ?></p>
              <p><strong>Academic Year:</strong> <?php echo $classData['AcademicYear']; ?></p>
              <p><strong>Class Code:</strong> <?php echo $classData['ClassCode']; ?></p>
            </div>

            <!-- Associated Subjects -->
            <?php if (!empty($subjectsData)) : ?>
              <div class="mt-4 bg-white shadow-md p-4 rounded-md">
                <h3 class="text-lg font-semibold text-gray-800">Subjects in this Class</h3>
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Name</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty Level</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teaching Method</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration Hours</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curriculum Framework</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($subjectsData as $subject) : ?>
                      <tr>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $subject['SubjectName']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $subject['DifficultyLevel']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $subject['TeachingMethod']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $subject['DurationHours']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $subject['CurriculumFramework']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <a href="manage_classes_remove_subject.php?subject_id=<?php echo $subject['SubjectID']; ?>&class_id=<?php echo $classID; ?>" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                            <span>Delete</span>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else : ?>
              <div class="bg-white shadow-md p-4 rounded-md mt-4">
                <p>No subjects in this class.</p>
              </div>
            <?php endif; ?>

            <!-- List of Students in the Class -->
            <?php if (!empty($studentsData)) : ?>
              <div class="mt-4 bg-white shadow-md p-4 rounded-md">
                <h3 class="text-lg font-semibold text-gray-800">Students in this Class</h3>
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                      <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($studentsData as $student) : ?>
                      <tr>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $student['FullName']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $student['PhoneNumber']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $student['Email']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <?php echo $student['ClassName']; ?>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap">
                          <a href="manage_classses_delete_student_from_class.php?student_id=<?php echo $student['StudentID']; ?>&class_id=<?php echo $classID; ?>" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                            <span>Delete</span>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else : ?>
              <div class="bg-white shadow-md p-4 rounded-md mt-4">
                <p>No students in this class.</p>
              </div>
            <?php endif; ?>

          <?php else : ?>
            <div class="bg-white shadow-md p-4 rounded-md">
              <p>No class data available.</p>
            </div>
          <?php endif; ?>
          <!-- End Class Details -->
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