<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if student is logged in
function is_student_logged_in() {
    return isset($_SESSION['student_reg_no']) && $_SESSION['password_changed'] == 1;
}

// Redirect if not logged in
function check_student_login() {
    if(!is_student_logged_in()) {
        header("Location: student-login.php");
        exit();
    }
}
?>