<?php
session_start();
$update = false;

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("location: login.php");
    exit;
}

include "partials/_dbconnect.php";

$id = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $username = $_POST["username"];
    $email = $_POST["email"];
    $bio = $_POST["bio"];    
    $old_image = $_POST['old_image'];
    $new_image = $_FILES['stud_image']['name'];
    $id = $_POST['stud_id'];
    
    $update_filename = ($new_image != '') ? $_FILES['stud_image']['name'] : $old_image;

    $sql = "UPDATE users SET username = '$username', email = '$email', bio = '$bio', profilePicture = '$update_filename' WHERE `users`.`id` = $id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        //if new file found then move to the folder
        if ($_FILES['stud_image']['name'] != '') {
            move_uploaded_file($_FILES["stud_image"]["tmp_name"], "upload/" . $_FILES["stud_image"]["name"]);
            //if new image found than unlink or delete old image file
            unlink("upload/" . $old_image);
        }
        
        // Update $profilePicture with the new filename and file path
        $profilePicture = "upload/" . $update_filename;

        $_SESSION['status'] = "updated successfully";
        header("Location: index.php");
    } else {
        $_SESSION['status'] = "error while updating";
        header("Location: index.php");
    }
}

$sql = "SELECT * FROM users WHERE id= $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $id = $row['id'];
    $username = $row['username'];
    $email = $row['email'];
    $bio = $row['bio'];
    $profilePicture = "upload/" . $row['profilePicture'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>
    <?php require 'partials/_navbar.php' ?>
    <?php
    if ($update) {
        echo ' <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> Your data updated successfully<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>';
    }

    ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2>Edit Profile</h2>
                        <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="stud_id" value="<?php echo $row['id']; ?>">
                            <div class="form-group mb-2">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" class="form-control"
                                    value="<?php echo $username; ?>">
                            </div>
                            <div class="form-group mb-2">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    aria-describedby="emailHelp" value="<?php echo $email; ?>">
                            </div>
                            <div class="form-group mb-2">
                                <label for="bio">Bio</label>
                                <textarea id="bio" name="bio" class="form-control"><?php echo $bio; ?></textarea>
                            </div>

                            <div class="form-group mb-2">
                                <label for="stud_image">Profile Picture</label>
                                <input type="file" class="form-control" id="stud_image" name="stud_image">
                                <input type="hidden" name="old_image" value="<?php echo $row['profilePicture']; ?>">
                            </div>
                            <div class="form-group mb-2">
                                <img id="profilePicturePreview" src="<?php echo $profilePicture; ?>"
                                    alt="Current Profile Picture" style="max-width: 150px;">
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Include necessary JavaScript files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
        crossorigin="anonymous"></script>
    <script>
        // JavaScript function to update the profile picture preview
        function updateProfilePicturePreview(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    // Update the src attribute of the img tag with the uploaded image data
                    document.getElementById("profilePicturePreview").src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Attach an event listener to the file input to trigger the preview update
        document.getElementById("stud_image").addEventListener("change", function () {
            updateProfilePicturePreview(this);
        });
    </script>

</body>

</html>