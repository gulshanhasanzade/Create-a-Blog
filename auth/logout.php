
<?php
include_once "../index.php";
include_once "../helper/helper.php";
session_start();
$_SESSION = [];
session_destroy();
view(route('auth/login.php'));
