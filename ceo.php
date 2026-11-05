<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEO - Download Files</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <h1>CEO File Download</h1>

    <?php if (isset($_GET['uploaded'])): ?>
        <p class="success">File uploaded and encrypted successfully.</p>
    <?php endif; ?>

    <form action="php/download.php" method="GET">
        <label for="current_id">Select File Current ID:</label>
        <select name="id" id="current_id" required>
            <option value="">--Select a File--</option>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "encrypt_system";

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT current_id, file_name FROM file_list ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = htmlspecialchars($row['current_id']);
                    $name = htmlspecialchars($row['file_name']);
                    echo '<option value="' . $id . '">' . $id . ' — ' . $name . '</option>';
                }
            } else {
                echo '<option value="">No files available</option>';
            }

            $conn->close();
            ?>
        </select>
        <br><br>
        <button type="submit">Download File</button>
    </form>

    <p><a href="admin.html">Upload another file</a> · <a href="index.html">Home</a></p>
</body>
</html>
