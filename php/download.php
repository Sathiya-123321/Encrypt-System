<?php
$host = 'localhost';
$db = 'encrypt_system';
$user = 'root';
$pass = '';

// Must match the key used in upload.php.
$secret_key = 'change_this_secret_key';

$current_id = $_GET['id'] ?? '';
if ($current_id === '') {
    die('No file selected. <a href="../ceo.php">Go back</a>');
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT file_name, file, iv FROM file_list WHERE current_id = ? LIMIT 1");
$stmt->bind_param("s", $current_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($file_name, $file, $iv_b64);

if ($stmt->num_rows === 0) {
    $stmt->close();
    $conn->close();
    die('No file found for id ' . htmlspecialchars($current_id) . '. <a href="../ceo.php">Go back</a>');
}

$stmt->fetch();

$decrypted = openssl_decrypt($file, 'aes-256-cbc', $secret_key, 0, base64_decode($iv_b64));

$stmt->close();
$conn->close();

if ($decrypted === false) {
    die('Could not decrypt the selected file.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($decrypted));

echo $decrypted;
exit();
?>
