<?php
$login = false;
$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify reCAPTCHA
    $recaptchaSecretKey = "6LcjsM4nAAAAAKmuIm46RRreaUTqWnLEdjNIOie9"; // Replace with your reCAPTCHA secret key
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $recaptchaSecretKey,
        'response' => $recaptchaResponse
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $jsonResult = json_decode($result);


    if (!$jsonResult->success) {
        // reCAPTCHA verification failed
        $showError = "reCAPTCHA verification failed. Please try again.";
    } else {
        include "partials/_dbconnect.php";
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email ='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                if (password_verify($password, $row['password'])) {
                    $login = true;
                    session_start();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id'] =$row['id'];
                    header("location: index.php"); // Redirect to the desired page after successful login
                    exit();
                } else {
                    $showError = "Invalid credentials";
                }
            } else {
                $showError = "User not found";
            }
        } else {
            $showError = "Database error: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="">
    <?php require 'partials/_navbar.php' ?>
    <?php
    if ($login) {
        echo ' <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong>You are logged in successfully ' . $username . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>';
    }
    if ($showError) {
        echo ' <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong>' . $showError . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
    ?>

    <div class="container my-4 d-flex justify-content-center align-items-center ">
        <div class="bg-body-secondary shadow-sm  border">
            <h1 class="text-center">Account Login</h1>
            <form action="/Loginsystem/login.php" method="post" class="w-100 mx-auto p-5">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="g-recaptcha mt-2" data-sitekey="6LcjsM4nAAAAANH-OyWqRASgFhosQA6TcXPVhlIj"></div>

                <button type="submit" class="btn btn-primary mt-2 w-100">Submit</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
    <script>


    </script>
</body>

</html>