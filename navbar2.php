<?php
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest'; 
$user_profile = isset($_SESSION['user_profile']) ? $_SESSION['user_profile'] : 'default.png'; 
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark-blue">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/projectBack/client/profile.php">All Blogs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/projectBack/client/blog/createblog.php">Create Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/projectBack/client/blog/homeblog.php">My Blogs</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item d-flex align-items-center">
                    <img src="http://localhost/projectBack/public/<?php echo htmlspecialchars($user_profile); ?>" alt="Profile Image" class="rounded-circle me-2" width="30" height="30">
                    <a class="nav-link" href="http://localhost/projectBack/client/edit_profile.php"><?php echo htmlspecialchars($user_name); ?>'s Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../auth/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<style>
    .bg-dark-blue {
        background-color: #00008B; 
    }
    .navbar-nav .nav-item .nav-link {
        color: #ffffff; 
    }
    .navbar-nav .nav-item .rounded-circle {
        margin-right: 5px; 
    }
</style>
