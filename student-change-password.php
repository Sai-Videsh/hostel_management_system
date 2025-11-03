<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if student is logged in
if(!isset($_SESSION['student_reg_no'])) {
    header("Location: student-login.php");
    exit();
}

// Check if password already changed
if($_SESSION['password_changed'] == 1) {
    header("Location: student-preferences.php");
    exit();
}

include 'partials/_dbconnect.php';

$error_msg = "";
$success_msg = "";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    if(strlen($new_password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } elseif($new_password != $confirm_password) {
        $error_msg = "Passwords do not match.";
    } else {
        // Hash password and update database
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $reg_no = $_SESSION['student_reg_no'];
        
        $update_sql = "UPDATE student_login SET password = '$hashed_password', password_changed = 1 WHERE registration_no = '$reg_no'";
        if(mysqli_query($conn, $update_sql)) {
            $_SESSION['password_changed'] = 1;
            $success_msg = "Password changed successfully!";
            
            // Redirect after brief delay
            header("Refresh: 2; URL=student-preferences.php");
        } else {
            $error_msg = "Error updating password: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .password-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #5a5a5a;
        }
        .btn-submit {
            background-color: rgb(111, 202, 203);
            border: none;
            width: 100%;
            padding: 10px;
            color: white;
            font-weight: bold;
        }
        .btn-submit:hover {
            background-color: rgb(91, 182, 183);
            color: white;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo i {
            font-size: 50px;
            color: rgb(111, 202, 203);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="password-container">
                    <div class="logo">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="header">
                        <h3>Change Your Password</h3>
                        <p>You must change your password before proceeding</p>
                    </div>
                    
                    <?php if($error_msg): ?>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>
                    
                    <?php if($success_msg): ?>
                        <div class="alert alert-success"><?php echo $success_msg; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> New Password</label>
                            <input type="password" class="form-control" name="new_password" required 
                                   placeholder="Enter new password (min. 6 characters)">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" required 
                                   placeholder="Confirm new password">
                        </div>
                        <button type="submit" class="btn btn-submit">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>