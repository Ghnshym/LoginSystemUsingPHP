<?php
$showAlert = false;
$showError = false;
$showuser = false;
$uesrvalid = false;
$emailvalid = false;
$passwordvalid = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "partials/_dbconnect.php";
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    
    if ($username !==" ") {
        $uesrvalid = "Username requried.";
    }elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $showError = "*Username can only contain letters, numbers, and underscores.";
    }    
    elseif (strlen($username) > 20) {
        $uesrvalid = "*Username cannot exceed 20 characters.";
    }

    // Validate email using filter_var function
    if($email !==" "){
        $emailvalid="*email requried.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailvalid = "*Invalid email address.";
    }

    // Validate password length
    if($password !==" "){
        $passwordvalid="*password requried.";
    }elseif (strlen($password) < 6) {
        $passwordvalid = "*Password must be at least 6 characters long.";
    } 

    $profilePictureName = $_FILES['profilePicture']['name'];
    $profilePictureTmpName = $_FILES['profilePicture']['tmp_name'];
    $profilePicturePath = "upload/" . $profilePictureName;

    $existSql = "SELECT * FROM `users` WHERE username = ?";
    $stmt = $conn->prepare($existSql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $numExitRows = $result->num_rows;

    if ($numExitRows > 0) {
        $showuser = "Username already exists";
    } else {
        if ($password == $cpassword) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            if (move_uploaded_file($profilePictureTmpName, $profilePicturePath)) {

                // Remove the path and store only the filename with extension
                $profilePictureFilename = $profilePictureName;
            
                $sql = "INSERT INTO `users` (`username`, `password`, `dt`, `email`, `profilePicture`) VALUES (?, ?, current_timestamp(), ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $username, $hashedPassword, $email, $profilePictureFilename);
                $stmt->execute();
            
                if ($stmt->affected_rows === 1) {
                    $showAlert = true;
                    session_start();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['email'] = $email;
                    header("location: login.php");
                }
            } else {
                $showError = "Error moving uploaded image.";
            }
        } else {
            $showError = "*Passwords do not match";
        }
    }
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>
    <?php require 'partials/_navbar.php' ?>
    <?php
    if ($showAlert) {
        echo ' <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong>Your account has been created successfully<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    if ($showuser) {
        echo ' <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong>' . $showuser . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    ?>

    <div class="container my-4 d-flex justify-content-center align-items-center">
        <div class=" bg-body-secondary shadow-sm  border">
            <h1 class="text-center mb-2">Signup Form</h1>
            <form action="/Loginsystem/signup.php" method="post" class="w-100 mx-auto p-5" enctype="multipart/form-data"
                onsubmit="return validateUsername();">
                <div class="form-group mb-2">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp">
                </div>
                <div>
                    <?php
                    if ($uesrvalid) {
                        echo "<p class='text-danger'>$uesrvalid<p>";
                    }
                    ?>
                </div>
                <div class="form-group mb-2">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"
                        >
                </div>
                <div>
                    <?php
                    if ($emailvalid) {
                        echo "<p class='text-danger'>$emailvalid<p>";
                    }
                    ?>
                </div>

                <div class="form-group mb-2">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div>
                    <?php
                    if ($passwordvalid) {
                        echo "<p class='text-danger'>$passwordvalid<p>";
                    }
                    ?>
                </div>

                <div class="form-group  mb-2">
                    <label for="cpassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="cpassword" name="cpassword">
                </div>
                <div>
                    <?php
                    if ($showError) {
                        echo "<p class='text-danger'>$showError<p>";
                    }
                    ?>
                </div>

                <div class="form-group mb-2">
                    <label for="profilePicture">Profile Picture</label>
                    <input type="file" id="profilePicture" name="profilePicture" class="form-control-file">
                </div>
                <button type="submit" class="btn btn-primary mt-3">Submit</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
</body>

</html>