
USE encrypt_system;

CREATE TABLE IF NOT EXISTS file_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    current_id VARCHAR(20) NOT NULL,      -- Column to store the current_id (e.g., 0x500)
    previous_id VARCHAR(20),              -- Column to store the previous_id (optional)
    file_name VARCHAR(255) NOT NULL,       -- Column to store the file name
    file LONGBLOB NOT NULL,               -- Column to store the file as a BLOB
    iv CHAR(16),                          -- Column to store the Initialization Vector (IV) for encryption
    description TEXT,                     -- Optional column for file description
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp for when the file was uploaded
);
