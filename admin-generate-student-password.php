<?php
include 'partials/_dbconnect.php';

// Random password generator
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}

$success_msg = '';
$error_msg = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generate_password'])) {
    $reg_no = mysqli_real_escape_string($conn, $_POST['reg_no']);
    
    // Verify student exists and get email
    $check_sql = "SELECT registration_no, emailid, CONCAT(first_name, ' ', last_name) as full_name FROM userregistration WHERE registration_no = '$reg_no'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0) {
        // Get student details
        $student_data = mysqli_fetch_assoc($check_result);
        $student_email = $student_data['emailid'];
        $student_name = $student_data['full_name'];
        
        // Check if login already exists
        $exists_sql = "SELECT id FROM student_login WHERE registration_no = '$reg_no'";
        $exists_result = mysqli_query($conn, $exists_sql);
        
        if(mysqli_num_rows($exists_result) > 0) {
            // Update existing login
            $new_password = generatePassword();
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update_sql = "UPDATE student_login 
                          SET password = '$hashed_password', password_changed = 0, last_login = NULL 
                          WHERE registration_no = '$reg_no'";
            
            if(mysqli_query($conn, $update_sql)) {
                // Send email to student
                $to = $student_email;
                $subject = "Your Hostel Portal Login Password";
                $message = "Dear $student_name,\n\n";
                $message .= "Your login credentials for the Hostel Management Portal:\n\n";
                $message .= "Registration Number: $reg_no\n";
                $message .= "Password: $new_password\n\n";
                $message .= "Please login at: " . $_SERVER['HTTP_HOST'] . "/hostel-management-system/student-login.php\n\n";
                $message .= "IMPORTANT: You will be required to change this password on first login.\n\n";
                $message .= "Best Regards,\nHostel Administration";
                $headers = "From: hostel@iiitdmkurnool.edu.in\r\n";
                $headers .= "Reply-To: hostel@iiitdmkurnool.edu.in\r\n";
                
                // Send email
                $email_sent = mail($to, $subject, $message, $headers);
                
                if($email_sent) {
                    $success_msg = "New password generated for student $reg_no: <strong>$new_password</strong><br>
                                   Email sent successfully to: <strong>$student_email</strong>";
                } else {
                    $success_msg = "New password generated for student $reg_no: <strong>$new_password</strong><br>
                                   <span class='text-warning'>Warning: Email could not be sent to $student_email</span>";
                }
            } else {
                $error_msg = "Error updating password: " . mysqli_error($conn);
            }
        } else {
            // Create new login
            $new_password = generatePassword();
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $insert_sql = "INSERT INTO student_login (registration_no, password) 
                          VALUES ('$reg_no', '$hashed_password')";
            
            if(mysqli_query($conn, $insert_sql)) {
                // Send email to student
                $to = $student_email;
                $subject = "Your Hostel Portal Login Password";
                $message = "Dear $student_name,\n\n";
                $message .= "Your login credentials for the Hostel Management Portal:\n\n";
                $message .= "Registration Number: $reg_no\n";
                $message .= "Password: $new_password\n\n";
                $message .= "Please login at: " . $_SERVER['HTTP_HOST'] . "/hostel-management-system/student-login.php\n\n";
                $message .= "IMPORTANT: You will be required to change this password on first login.\n\n";
                $message .= "Best Regards,\nHostel Administration";
                $headers = "From: hostel@iiitdmkurnool.edu.in\r\n";
                $headers .= "Reply-To: hostel@iiitdmkurnool.edu.in\r\n";
                
                // Send email
                $email_sent = mail($to, $subject, $message, $headers);
                
                if($email_sent) {
                    $success_msg = "Login created for student $reg_no with password: <strong>$new_password</strong><br>
                                   Email sent successfully to: <strong>$student_email</strong>";
                } else {
                    $success_msg = "Login created for student $reg_no with password: <strong>$new_password</strong><br>
                                   <span class='text-warning'>Warning: Email could not be sent to $student_email</span>";
                }
            } else {
                $error_msg = "Error creating login: " . mysqli_error($conn);
            }
        }
    } else {
        $error_msg = "Student with registration number $reg_no not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Student Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fa fa-key"></i> Generate Student Login Password</h4>
                    </div>
                    <div class="card-body">
                        <?php if($success_msg): ?>
                            <div class="alert alert-success">
                                <?php echo $success_msg; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($error_msg): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_msg; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label>Select Student</label>
                                <select name="reg_no" class="form-control" required>
                                    <option value="">Choose a student</option>
                                    <?php
                                        $student_sql = "SELECT registration_no, CONCAT(first_name, ' ', last_name) as full_name 
                                                       FROM userregistration 
                                                       ORDER BY first_name";
                                        $student_result = mysqli_query($conn, $student_sql);
                                        if($student_result) {
                                            while($student = mysqli_fetch_assoc($student_result)) {
                                                echo '<option value="'.$student['registration_no'].'">'.$student['registration_no'].' - '.$student['full_name'].'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="generate_password" class="btn btn-primary">
                                <i class="fa fa-key"></i> Generate Password
                            </button>
                        </form>
                        
                        <hr>
                        
                        <!-- Login Status -->
                        <h5>Student Login Status</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Registration No</th>
                                        <th>Student Name</th>
                                        <th>Password Changed</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $logins_sql = "SELECT sl.*, CONCAT(u.first_name, ' ', u.last_name) as student_name 
                                                      FROM student_login sl 
                                                      JOIN userregistration u ON sl.registration_no = u.registration_no 
                                                      ORDER BY sl.created_at DESC";
                                        $logins_result = mysqli_query($conn, $logins_sql);
                                        
                                        if($logins_result && mysqli_num_rows($logins_result) > 0) {
                                            while($login = mysqli_fetch_assoc($logins_result)) {
                                                echo '<tr>';
                                                echo '<td>'.$login['registration_no'].'</td>';
                                                echo '<td>'.$login['student_name'].'</td>';
                                                echo '<td>'.($login['password_changed'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>').'</td>';
                                                echo '<td>'.($login['last_login'] ? date('M d, Y H:i', strtotime($login['last_login'])) : 'Never').'</td>';
                                                echo '<td>
                                                        <form method="post" class="d-inline">
                                                            <input type="hidden" name="reg_no" value="'.$login['registration_no'].'">
                                                            <button type="submit" name="generate_password" class="btn btn-sm btn-warning">
                                                                <i class="fa fa-redo"></i> Reset
                                                            </button>
                                                        </form>
                                                      </td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center">No student logins created yet</td></tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="index.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>