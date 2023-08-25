<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
  header("location: login.php");
  exit;
}
include "partials/_dbconnect.php";

// Fetch user data based on the logged-in user's email (assuming email is unique)
$id = $_SESSION['id'];
$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $sql);

// Check if user data was found
if (mysqli_num_rows($result) == 1) {
  $row = mysqli_fetch_assoc($result);

  // Now, you can access user data like $row['columnName']
  $email = $row['email'];
  $username = $row['username'];
 // Update the $profilePicture variable with the new image path
 $profilePicture = $row['profilePicture'];
  $bio = $row['bio'];
  // echo $profilePicture;
}
?>


<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

  <title>Welcome -
    <?php $_SESSION['id'] ?>
  </title>
</head>

<body>
  <?php require 'partials/_navbar.php' ?>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class=" w-75  bg-body-secondary shadow-sm border ">
          <div class="card-body">
            <!-- User Profile Image -->
            <div class="d-flex justify-content-between">
            <div class="text-left">
          <?php
          if (file_exists("upload/" . $profilePicture)) {
              echo '<img src="upload/' . $profilePicture . '" alt="User Profile Image" class="img-fluid rounded-circle" style="max-width: 40%;">';
          } else {
              echo '<img src="avatar.png" alt="Avatar" class="img-fluid rounded-circle" style="max-width: 150px;">';
          }
          ?>
      </div>
              <div>
                <button class="edit btn btn-outline-warning btn-md" id="editProfileButton">Edit</button>
              </div>
            </div>
            <h2 class="card-title text-left mt-3 ">
              <?php echo $username; ?>
            </h2>
            <div class="row">
              <div class="col-md-6">
                <ul class="list-group">
                  <li class="list-group">
                    <strong class="d-inline text-primary !fs-4">Email</strong>
                    <?php echo $email; ?>
                  </li>
                </ul>
              </div>
            </div>
            <div class="mt-3 ">
              <h5 class="mt-3 text-primary fs-4">Bio</h5>
              <?php
              if (!empty($bio)) {
                echo '<div><p>' . $bio . '</p></div>';
              } else {
                echo '<div><p>no bio </p></div>';
              }
              ?>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
    integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
    integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
    crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
    integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
    crossorigin="anonymous"></script>
  <script>

    document.getElementById("editProfileButton").addEventListener("click", function () {
      // Redirect to the edit profile page
      window.location.href = "editProfile.php";
    });
  </script>

</body>

</html>