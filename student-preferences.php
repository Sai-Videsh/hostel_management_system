<?php
include 'student-session.php';
check_student_login();

// Store the student reg_no in a GET parameter
$reg_no = $_SESSION['student_reg_no'];

// Redirect to the preferences tab with the student's reg_no pre-selected
header("Location: index.php?page=roommate&tab=preferences&reg_no=$reg_no&student_view=1");
exit();
?>