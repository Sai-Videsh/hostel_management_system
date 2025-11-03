<?php
// Script to check and fix the img directory

// Define the path to the img directory
$imgDir = __DIR__ . '/img';

// Check if directory exists
if (!file_exists($imgDir)) {
    echo "Creating img directory...<br>";
    if (mkdir($imgDir, 0777, true)) {
        echo "Successfully created img directory.<br>";
    } else {
        echo "Failed to create img directory!<br>";
        echo "Error: " . error_get_last()['message'] . "<br>";
    }
} else {
    echo "img directory exists.<br>";
}

// Make directory writable
echo "Setting permissions on img directory...<br>";
if (chmod($imgDir, 0777)) {
    echo "Successfully set permissions.<br>";
} else {
    echo "Failed to set permissions!<br>";
}

// Test file creation
$testFile = $imgDir . '/test.txt';
echo "Testing file creation...<br>";
$content = "Test file created at " . date('Y-m-d H:i:s');
if (file_put_contents($testFile, $content)) {
    echo "Successfully created test file.<br>";
    
    // Test file deletion
    if (unlink($testFile)) {
        echo "Successfully deleted test file.<br>";
    } else {
        echo "Failed to delete test file!<br>";
    }
} else {
    echo "Failed to create test file!<br>";
}

// Display directory info
echo "<hr>";
echo "Directory path: " . $imgDir . "<br>";
echo "Directory permissions: " . substr(sprintf('%o', fileperms($imgDir)), -4) . "<br>";
echo "Directory owner: " . getmyuid() . "<br>";
echo "PHP process owner: " . posix_getuid() . "<br>";
?>