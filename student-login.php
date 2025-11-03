<!DOCTYPE html>
<html>
<head>
    <title>Student Preferences Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
            color: #5a5a5a;
        }
        .btn-login {
            background-color: rgb(111, 202, 203);
            border: none;
            width: 100%;
            padding: 10px;
            color: white;
            font-weight: bold;
        }
        .btn-login:hover {
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
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="login-container">
                    <div class="logo">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="login-header">
                        <h3>Student Preferences Login</h3>
                        <p>Enter your registration number and password to access your preferences</p>
                    </div>
                    
                    <?php
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    // Check if already logged in
                    if(isset($_SESSION['student_reg_no'])) {
                        header("Location: student-preferences.php");
                        exit();
                    }
                    
                    // Check for error message
                    if(isset($_SESSION['login_error'])) {
                        echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
                        unset($_SESSION['login_error']);
                    }
                    
                    // Check for success message
                    if(isset($_GET['success'])) {
                        echo '<div class="alert alert-success">' . $_GET['success'] . '</div>';
                    }
                    
                    // Handle login form submission
                    if($_SERVER['REQUEST_METHOD'] == 'POST') {
                        include 'partials/_dbconnect.php';
                        
                        $reg_no = mysqli_real_escape_string($conn, $_POST['reg_no']);
                        $password = mysqli_real_escape_string($conn, $_POST['password']);
                        
                        // Check if student exists
                        $check_student = "SELECT registration_no FROM userregistration WHERE registration_no = '$reg_no'";
                        $check_result = mysqli_query($conn, $check_student);
                        
                        if(mysqli_num_rows($check_result) > 0) {
                            // Check login credentials
                            $login_sql = "SELECT * FROM student_login WHERE registration_no = '$reg_no'";
                            $login_result = mysqli_query($conn, $login_sql);
                            
                            if(mysqli_num_rows($login_result) > 0) {
                                $login_data = mysqli_fetch_assoc($login_result);
                                
                                // Verify password
                                if(password_verify($password, $login_data['password'])) {
                                    // Login successful
                                    $_SESSION['student_reg_no'] = $reg_no;
                                    $_SESSION['password_changed'] = $login_data['password_changed'];
                                    
                                    // Update last login
                                    $update_login = "UPDATE student_login SET last_login = NOW() WHERE registration_no = '$reg_no'";
                                    mysqli_query($conn, $update_login);
                                    
                                    // Redirect based on password status
                                    if($login_data['password_changed'] == 0) {
                                        header("Location: student-change-password.php");
                                    } else {
                                        header("Location: student-preferences.php");
                                    }
                                    exit();
                                } else {
                                    $_SESSION['login_error'] = "Invalid password. If using first-time password, ensure it's entered correctly.";
                                }
                            } else {
                                $_SESSION['login_error'] = "No login account found. Please contact administrator.";
                            }
                        } else {
                            $_SESSION['login_error'] = "Registration number not found in system.";
                        }
                        
                        // Redirect to show error
                        header("Location: student-login.php");
                        exit();
                    }
                    ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label><i class="fas fa-id-card"></i> Registration Number</label>
                            <input type="text" class="form-control" name="reg_no" required placeholder="Enter your registration number">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" class="form-control" name="password" required placeholder="Enter your password">
                        </div>
                        <button type="submit" class="btn btn-login">Login</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="index.php">Back to Main Site</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>