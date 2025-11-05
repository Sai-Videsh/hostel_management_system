<?php
// Check-in/Check-out Management Backend Handler
include '_dbconnect.php';
session_start();

// Check if admin is logged in
if(!isset($_SESSION['adminloggedin']) || $_SESSION['adminloggedin'] != true) {
    header("location: /hostel-management-system/login.php");
    exit;
}

// Handle form submission for new entry
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_entry'])) {
    $reg_no = mysqli_real_escape_string($conn, $_POST['reg_no']);
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no_hidden']);
    $action_type = mysqli_real_escape_string($conn, $_POST['action_type']);
    $action_date = mysqli_real_escape_string($conn, $_POST['action_date']);
    $action_time = mysqli_real_escape_string($conn, $_POST['action_time']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    $recorded_by = $_SESSION['adminuserId'];
    
    // Validate required fields
    if(empty($reg_no) || empty($student_name) || empty($action_type) || empty($action_date) || empty($action_time)) {
        $_SESSION['error_msg'] = "All required fields must be filled!";
        header("location: /hostel-management-system/index.php?page=checkinManage");
        exit;
    }
    
    // Insert into database
    $insert_sql = "INSERT INTO hostel_checkin_checkout 
                   (reg_no, student_name, room_no, action_type, action_date, action_time, remarks, recorded_by) 
                   VALUES ('$reg_no', '$student_name', ".($room_no ? "'$room_no'" : "NULL").", '$action_type', '$action_date', '$action_time', '$remarks', '$recorded_by')";
    
    if(mysqli_query($conn, $insert_sql)) {
        $action_label = $action_type == 'check-in' ? 'Check-in' : 'Check-out';
        $_SESSION['success_msg'] = "$action_label recorded successfully for $student_name ($reg_no)!";
    } else {
        $_SESSION['error_msg'] = "Error recording entry: " . mysqli_error($conn);
    }
    
    header("location: /hostel-management-system/index.php?page=checkinManage");
    exit;
}

// Handle delete request
if(isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    $delete_sql = "DELETE FROM hostel_checkin_checkout WHERE id = '$delete_id'";
    
    if(mysqli_query($conn, $delete_sql)) {
        $_SESSION['success_msg'] = "Record deleted successfully!";
    } else {
        $_SESSION['error_msg'] = "Error deleting record: " . mysqli_error($conn);
    }
    
    header("location: /hostel-management-system/index.php?page=checkinManage");
    exit;
}

// If no valid action, redirect back
header("location: /hostel-management-system/index.php?page=checkinManage");
exit;
?>
