<?php


$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$user_profile = isset($_SESSION['user_profile']) ? $_SESSION['user_profile'] : null;
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <?php if ($user_name): ?>
        <li class="nav-item">
          <a class="nav-link" href="/client/profile.php">
          <img src="http://localhost/projectBack/public/<?php echo $user_profile; ?>" alt="Profile Image" class="rounded-circle" width="30" height="30">
          <?php echo htmlspecialchars($user_name); ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/projectBack/auth/logout.php">Logout</a>
        </li>
      <?php else: ?>
        <li class="nav-item">
        <a class="nav-link" href="/projectBack/auth/login.php">Login</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="/projectBack/auth/register.php">Register</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
