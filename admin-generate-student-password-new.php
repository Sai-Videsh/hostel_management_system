<?php
include 'partials/_dbconnect.php';
include 'smtp_mail.php';

// Email function using our custom SMTP class
function sendPasswordEmail($to, $subject, $reg_no, $password) {
    // Gmail account credentials - REPLACE THESE WITH YOUR ACTUAL CREDENTIALS
    $gmail_user = 'gadambharat3833@gmail.com';
    $gmail_password = 'pibn cwfu fuda dena';
    
    // Email body in HTML format
    $message = "<!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .header { background-color: #f5f5f5; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { padding: 20px; }
            .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #777; }
            .highlight { background-color: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Hostel Management System</h2>
            </div>
            <div class='content'>
                <p>Dear Student,</p>
                <p>Your login credentials for the Hostel Management System have been created/reset:</p>
                <p><strong>Registration Number:</strong> $reg_no</p>
                <p><strong>Password:</strong> <span class='highlight'>$password</span></p>
                <p>Please login using these credentials and change your password immediately.</p>
                <p>Link: <a href='http://localhost/hostel-management-system/student-login.php'>Hostel Management System</a></p>
                <p>If you have any questions, please contact the hostel administration.</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>";
    
    try {
        // Create SMTP mailer instance
        $mailer = new SMTPMailer('smtp.gmail.com', 587, $gmail_user, $gmail_password);
        
        // Optional: Enable debug for troubleshooting
        $mailer->setDebug(true);
        
        // Set sender name
        $mailer->setFrom($gmail_user, 'Hostel Management System');
        
        // Send email
        $success = $mailer->send($to, $subject, $message, true);
        
        return $success;
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        return false;
    }
}

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
    $check_sql = "SELECT registration_no, emailid FROM userregistration WHERE registration_no = '$reg_no'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0) {
        $student_data = mysqli_fetch_assoc($check_result);
        $student_email = $student_data['emailid'];
        
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
                // Attempt to send email
                $success = sendPasswordEmail(
                    $student_email,
                    "Your Hostel Management System Password Has Been Reset",
                    $reg_no,
                    $new_password
                );
                
                if($success) {
                    $success_msg = "New password generated for student $reg_no: <strong>$new_password</strong><br>
                                   Password has been emailed to $student_email.";
                } else {
                    $success_msg = "New password generated for student $reg_no: <strong>$new_password</strong><br>
                                   <span class='text-warning'>Failed to send email. Please provide this password to the student manually.</span>";
                }
            }
            else {
                $error_msg = "Error updating password: " . mysqli_error($conn);
            }
        } else {
            // Create new login
            $new_password = generatePassword();
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $insert_sql = "INSERT INTO student_login (registration_no, password) 
                          VALUES ('$reg_no', '$hashed_password')";
            
            if(mysqli_query($conn, $insert_sql)) {
                // Attempt to send email
                $success = sendPasswordEmail(
                    $student_email,
                    "Your Hostel Management System Login Credentials",
                    $reg_no,
                    $new_password
                );
                
                if($success) {
                    $success_msg = "Login created for student $reg_no with password: <strong>$new_password</strong><br>
                                   Login credentials have been emailed to $student_email.";
                } else {
                    $success_msg = "Login created for student $reg_no with password: <strong>$new_password</strong><br>
                                   <span class='text-warning'>Failed to send email. Please provide this password to the student manually.</span>";
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