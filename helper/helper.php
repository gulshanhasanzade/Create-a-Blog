<?php
function post($key) {
    return isset($_POST[$key]) ? $_POST[$key] : '';
}


function validation($keys) {
    $errors = [];
    foreach ($keys as $key) {
        if (!isset($_POST[$key]) || empty($_POST[$key])) {
            $errors[$key] = "$key field is required";
        }
    }
    return $errors;
}

function fileUpload($directory, $file) {
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
$file=$_FILES['profile'];
    $name = $file['name'];
    $tmpName = $file['tmp_name'];

    $allowedExtensions = ['jpg', 'png', 'jpeg', 'gif'];
    $fileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $destination = $directory . $newFileName;

        if (move_uploaded_file($tmpName, $destination)) {
            return $newFileName;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
function auth()
{
    if (isset($_SESSION['id'])) {
        return true;
    }
    return false;
}


function view($view) {
    header("Location: $view");
    exit();
}

function route($path) {
    return "http://localhost/projectBack/" . $path;
}
