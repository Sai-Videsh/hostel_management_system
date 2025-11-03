<?php
// Force error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors to file
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

include '_dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Debugging (optional - can be commented out later)
    // echo "Handler reached<br>";
    // echo '<pre>'; print_r($_POST); echo '</pre>';

    /* =====================================================
       DELETE USER
    ===================================================== */
    if (isset($_POST['removeUser'])) {
        $Id = $_POST["Id"];
        $sql = "DELETE FROM `userregistration` WHERE `id`='$Id'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>
                    alert('User removed successfully');
                    window.location.href='/hostel-management-system/index.php?page=userManage';
                  </script>";
        } else {
            echo "<script>
                    alert('Failed to remove user');
                    window.location.href='/hostel-management-system/index.php?page=userManage';
                  </script>";
        }
    }

    /* =====================================================
       CREATE NEW USER
    ===================================================== */
    if (isset($_POST['createUser'])) {
        try {
            $regno = $_POST["registration"];
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $gender = $_POST["gender"];
            
            // Debug data
            error_log("Registration data: " . print_r($_POST, true));
            
            // Check if this registration number already exists
            $check_sql = "SELECT COUNT(*) as count FROM userregistration WHERE registration_no = '" . mysqli_real_escape_string($conn, $regno) . "'";
            $check_result = mysqli_query($conn, $check_sql);
            $check_row = mysqli_fetch_assoc($check_result);
            
            if ($check_row['count'] > 0) {
                // Registration number already exists, generate a new one
                $count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM userregistration");
                $count_row = mysqli_fetch_assoc($count_query);
                $next_number = $count_row['count'] + 1;
                $regno = "REG-" . str_pad($next_number, 3, '0', STR_PAD_LEFT);
                error_log("Duplicate registration number detected. Generated new number: " . $regno);
            }
            
            // Clean and sanitize inputs
            $regno = mysqli_real_escape_string($conn, $regno);
            $firstName = mysqli_real_escape_string($conn, $firstName);
            $lastName = mysqli_real_escape_string($conn, $lastName);
            $email = mysqli_real_escape_string($conn, $email);
            $phone = mysqli_real_escape_string($conn, $phone);
            $gender = mysqli_real_escape_string($conn, $gender);
            
            $sql = "INSERT INTO `userregistration` 
                    (`registration_no`, `first_name`, `last_name`, `emailid`, `contact_no`, `gender`) 
                    VALUES ('$regno', '$firstName', '$lastName', '$email', '$phone', '$gender')";
            
            error_log("SQL query: " . $sql);
            
            $result = mysqli_query($conn, $sql);
            
            if ($result) {
                echo "<script>
                        alert('Registration successful with ID: $regno');
                        window.location=document.referrer;
                      </script>";
            } else {
                $error = mysqli_error($conn);
                error_log("SQL error: " . $error);
                echo "<script>
                        alert('Failed to register new user. Error: " . $error . "');
                        window.location=document.referrer;
                      </script>";
            }
        } catch (Exception $e) {
            error_log("Exception: " . $e->getMessage());
            echo "<script>
                    alert('An error occurred: " . $e->getMessage() . "');
                    window.location=document.referrer;
                  </script>";
        }
    }

    /* =====================================================
       EDIT USER DETAILS
    ===================================================== */
    if (isset($_POST['editUser'])) {
        // 🔧 FIXED: Use 'userId' (matches your form hidden input)
        $id = $_POST['userId'];
        $firstname = $_POST['firstName'];
        $lastname = $_POST['lastName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $gender = $_POST['gender'];

        $sql = "UPDATE `userregistration` 
                SET `first_name`='$firstname', 
                    `last_name`='$lastname', 
                    `emailid`='$email', 
                    `contact_no`='$phone', 
                    `gender`='$gender' 
                WHERE `id`='$id'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>
                    alert('User updated successfully.');
                    window.location=document.referrer;
                  </script>";
        } else {
            echo "<script>
                    alert('Update failed: " . mysqli_error($conn) . "');
                    window.location=document.referrer;
                  </script>";
        }
    }

    /* =====================================================
       UPDATE PROFILE PHOTO
    ===================================================== */
    if (isset($_POST['updateProfilePhoto'])) {
        error_log("Photo upload started for user ID: " . $_POST["userId"]);
        
        // Check if any file was uploaded
        if (!isset($_FILES["userimage"]) || $_FILES["userimage"]["error"] != 0) {
            $upload_error = $_FILES["userimage"]["error"];
            error_log("File upload error code: " . $upload_error);
            echo "<script>
                    alert('File upload error: " . $upload_error . "');
                    window.location=document.referrer;
                  </script>";
            exit();
        }
        
        $id = $_POST["userId"];
        
        // Check file type
        $check = getimagesize($_FILES["userimage"]["tmp_name"]);
        error_log("Image check result: " . print_r($check, true));
        
        if ($check !== false) {
            // Create directory if it doesn't exist
            $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/hostel-management-system/img/';
            if (!file_exists($uploaddir)) {
                error_log("Creating img directory: " . $uploaddir);
                mkdir($uploaddir, 0777, true);
            }
            
            $newfilename = "person-" . $id . ".jpg";
            $uploadfile = $uploaddir . $newfilename;

            error_log("Attempting to move file to: " . $uploadfile);
            
            // Try to move the uploaded file
            if (move_uploaded_file($_FILES['userimage']['tmp_name'], $uploadfile)) {
                error_log("File uploaded successfully");
                echo "<script>
                        alert('Profile photo updated successfully.');
                        window.location=document.referrer;
                      </script>";
            } else {
                $error = error_get_last();
                error_log("Move failed: " . ($error ? $error['message'] : 'Unknown error'));
                echo "<script>
                        alert('Failed to upload image. Please check server permissions.');
                        window.location=document.referrer;
                      </script>";
            }
        } else {
            error_log("Invalid image file");
            echo "<script>
                    alert('Please select a valid .jpg image file.');
                    window.location=document.referrer;
                  </script>";
        }
    }

    /* =====================================================
       REMOVE PROFILE PHOTO
    ===================================================== */
    if (isset($_POST['removeProfilePhoto'])) {
        $id = $_POST["userId"];
        $filename = $_SERVER['DOCUMENT_ROOT'] . "/hostel-management-system/img/person-" . $id . ".jpg";
        
        error_log("Attempting to remove photo: " . $filename);

        if (file_exists($filename)) {
            error_log("File exists, attempting to delete");
            if (unlink($filename)) {
                error_log("File deleted successfully");
                echo "<script>
                        alert('Profile photo removed.');
                        window.location=document.referrer;
                      </script>";
            } else {
                $error = error_get_last();
                error_log("Failed to delete file: " . ($error ? $error['message'] : 'Unknown error'));
                echo "<script>
                        alert('Failed to remove photo. Permission denied.');
                        window.location=document.referrer;
                      </script>";
            }
        } else {
            error_log("File does not exist: " . $filename);
            echo "<script>
                    alert('No photo found to remove.');
                    window.location=document.referrer;
                  </script>";
        }
    }
}
?>
