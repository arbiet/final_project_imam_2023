<aside class="bg-gray-800 text-white w-64 overflow-y-scroll h-screen flex-shrink-0 sc-hide">
    <ul class="text-gray-400">
        <li class="px-6 py-4 hover:bg-gray-700 cursor-pointer space-x-2 flex items-center">
            <i class="fas fa-tachometer-alt mr-3"></i>
            <?php
            if (isset($_SESSION['RoleID'])) {
                $roleID = $_SESSION['RoleID'];

                if ($roleID == 1) {
                    // Admin
                    echo '<a href="../systems/dashboard_admin.php">Dashboard</a>';
                } elseif ($roleID == 2) {
                    // Teacher
                    echo '<a href="../systems/dashboard_teacher.php">Dashboard</a>';
                } elseif ($roleID == 3) {
                    // Student
                    echo '<a href="../systems/dashboard_student.php">Dashboard</a>';
                }
            }
            ?>
        </li>
        <li class="px-6 py-4 hover-bg-gray-700 cursor-pointer space-x-2 flex items-center">
            <i class="fas fa-user mr-3"></i>
            <a href="../profiles/profile.php">Profile</a>
        </li>
        <?php
        if ($_SESSION['RoleID'] === 1) {
            // Menu "Manage Users" hanya ditampilkan jika peran pengguna adalah "Admin"
            echo '
            <li class="px-6 py-4 hover-bg-gray-700 cursor-pointer space-x-2 flex items-center">
                <i class="fas fa-user-cog mr-3"></i>
                <a href="../manage_users/manage_users_list.php">Users</a>
            </li>
            ';
        }
        ?>
        <?php
        if ($_SESSION['RoleID'] === 1) {
            // Menu "Manage Users" hanya ditampilkan jika peran pengguna adalah "Admin"
            echo '
            <li class="px-6 py-4 hover-bg-gray-700 cursor-pointer space-x-2 flex items-center">
                <i class="fa-solid fa-door-closed mr-3"></i>
                <a href="../manage_classes/manage_classes_list.php">Classes</a>
            </li>
            ';
        }
        ?>
        <?php
        if ($_SESSION['RoleID'] === 10) {
            // Menu "Manage Users" hanya ditampilkan jika peran pengguna adalah "Admin"
            echo '
            <li class="px-6 py-4 hover-bg-gray-700 cursor-pointer space-x-2 flex items-center">
                <i class="fa-solid fa-shield-halved mr-3"></i>
                <a href="../manage_roles/manage_roles_list.php">Roles</a>
            </li>
            ';
        }
        ?>
        <?php
        if ($_SESSION['RoleID'] === 1) {
            // Menu "Manage Users" hanya ditampilkan jika peran pengguna adalah "Admin"
            echo '
            <li class="px-6 py-4 hover-bg-gray-700 cursor-pointer space-x-2 flex items-center">
                <i class="fa-solid fa-chalkboard-user mr-3"></i>
                <a href="../manage_teachers/manage_teachers_list.php">Teacher</a>
            </li>
            ';
        }
        ?>
        <?php
        if ($_SESSION['RoleID'] === 1 or $_SESSION['RoleID'] === 2) {
            // Menu "Manage Users" hanya ditampilkan jika peran pengguna adalah "Admin"
            echo '
            <li class="px-6 py-4 hover-bg-gray-700 cursor-pointer space-x-2 flex items-center">
                <i class="fa-solid fa-users mr-3"></i>
                <a href="../manage_students/manage_students_list.php">Students</a>
            </li>
            ';
        }
        ?>
        <?php
        if ($_SESSION['RoleID'] === 1) {
            // Menu "Manage Users" hanya ditampilkan jika peran pengguna adalah "Admin"
            echo '
        <li class="px-6 py-4 hover-bg-gray-700 cursor-pointer space-x-2 flex items-center">
            <i class="fas fa-cog mr-3"></i>
            <a href="../systems/settingsList.php">Settings</a>
        </li>
        ';
        }
        ?>

    </ul>
    <hr class="mt-60 border-transparent">
</aside>
<script>
    // Mendapatkan halaman saat ini
    var currentPage = window.location.href;

    // Mengambil semua tautan dalam daftar
    var links = document.querySelectorAll("aside ul li a");

    // Loop melalui tautan dan periksa jika URL cocok
    links.forEach(function(link) {
        var currentPathParts = currentPage.split("/");
        var linkPathParts = link.href.split("/");
        if (linkPathParts[linkPathParts.length - 2] === currentPathParts[currentPathParts.length - 2]) {
            if (currentPathParts[currentPathParts.length - 2] != "systems") {
                link.parentElement.classList.add("bg-gray-700");
            } else if (currentPathParts[currentPathParts.length - 2] == "systems" && link.href === currentPage) {
                link.parentElement.classList.add("bg-gray-700");
            }
        }
    });
</script>