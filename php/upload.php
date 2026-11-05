<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "encrypt_system";

// Encryption parameters
$secret_key = 'your_secret_key';  // Change this to a secure key
$iv_length = openssl_cipher_iv_length('aes-256-cbc');  // AES-256-CBC requires a 16-byte IV

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the last current_id from the database and increment it
$sql = "SELECT current_id FROM file_list ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Get the last current_id (hex format)
    $row = $result->fetch_assoc();
    $last_current_id = $row['current_id'];

    // Convert hex to decimal, increment by 1, then convert back to hex with '0x' prefix
    $new_current_id_decimal = hexdec($last_current_id) + 1;
    $new_current_id = "0x" . dechex($new_current_id_decimal);  // Convert back to hex
} else {
    // If no records are present, start with 0x500
    $new_current_id = "0x500";
}

// Get the previous current_id (this will be the one we reference as previous_id)
$previous_id = $last_current_id ?? NULL;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    // Get the file name and tmp name
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];

    // Validate file extension (only JPG files allowed)
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if ($file_ext != "jpg") {
        echo "Only JPG files are allowed.";
        exit();
    }

    // Check file size (example: 10 MB limit)
    $max_file_size = 10 * 1024 * 1024; // 10 MB
    if ($_FILES["file"]["size"] > $max_file_size) {
        echo "File size exceeds the maximum limit of 10 MB.";
        exit();
    }

    // Get the file content
    $file_data = file_get_contents($file_tmp);

    // Encrypt the file data using AES-256-CBC
    $iv = openssl_random_pseudo_bytes($iv_length);  // Generate a random IV
    $encrypted_file_data = openssl_encrypt($file_data, 'aes-256-cbc', $secret_key, 0, $iv);

    // Prepare the SQL query
    $sql = "INSERT INTO file_list (current_id, previous_id, file_name, file, iv) 
            VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the query
        $stmt->bind_param("sssss", $new_current_id, $previous_id, $file_name, $encrypted_file_data, $iv);

        // Execute the query
        if ($stmt->execute()) {
            echo "File uploaded and encrypted successfully with ID: $new_current_id";
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}

header('Location: ./download.php', true, 302);
?>




