<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "encrypt_system";

// Change this to a strong, private key before using the project anywhere real.
$secret_key = 'change_this_secret_key';
$iv_length = openssl_cipher_iv_length('aes-256-cbc');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Work out the next hexadecimal file id (0x500, 0x501, ...).
$last_current_id = null;
$sql = "SELECT current_id FROM file_list ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_current_id = $row['current_id'];
    $new_current_id = "0x" . dechex(hexdec($last_current_id) + 1);
} else {
    $new_current_id = "0x500";
}
$previous_id = $last_current_id;

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_FILES["file"])) {
    header('Location: ../admin.html');
    exit();
}

$file_name = $_FILES["file"]["name"];
$file_tmp = $_FILES["file"]["tmp_name"];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if ($file_ext !== "jpg" && $file_ext !== "jpeg") {
    die("Only JPG files are allowed. <a href='../admin.html'>Go back</a>");
}

$max_file_size = 10 * 1024 * 1024; // 10 MB
if ($_FILES["file"]["size"] > $max_file_size) {
    die("File size exceeds the maximum limit of 10 MB. <a href='../admin.html'>Go back</a>");
}

$file_data = file_get_contents($file_tmp);

// AES-256-CBC: a fresh random IV is generated for every file.
$iv = openssl_random_pseudo_bytes($iv_length);
$encrypted_file_data = openssl_encrypt($file_data, 'aes-256-cbc', $secret_key, 0, $iv);
$iv_b64 = base64_encode($iv);

$sql = "INSERT INTO file_list (current_id, previous_id, file_name, file, iv)
        VALUES (?, ?, ?, ?, ?)";

$error = null;
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssss", $new_current_id, $previous_id, $file_name, $encrypted_file_data, $iv_b64);
    if (!$stmt->execute()) {
        $error = $stmt->error;
    }
    $stmt->close();
} else {
    $error = $conn->error;
}

$conn->close();

if ($error !== null) {
    die("Upload failed: " . htmlspecialchars($error) . " <a href='../admin.html'>Go back</a>");
}

header('Location: ../ceo.php?uploaded=1');
exit();
?>
