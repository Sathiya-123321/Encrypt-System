<?php
// Database connection details
$host = 'localhost'; // Your database host
$db = 'encrypt_system'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement to get the last uploaded file
$stmt = $conn->prepare("SELECT file_name, file FROM file_list ORDER BY created_at DESC LIMIT 1");
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($file_name, $file);

// Check if the file exists
if ($stmt->num_rows > 0) {
    $stmt->fetch();

    // Set headers to force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($file));

    // Output the file content
    echo $file;
} else {
    echo "No files found.";
}

$stmt->close();
$conn->close();
?>