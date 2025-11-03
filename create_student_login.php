<?php
include 'partials/_dbconnect.php';

// Create student_login table
$sql = "CREATE TABLE IF NOT EXISTS `student_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_no` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_changed` tinyint(1) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration_no` (`registration_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

if (mysqli_query($conn, $sql)) {
    echo "Student login table created successfully!";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?>