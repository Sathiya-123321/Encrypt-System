USE encrypt_system;

CREATE TABLE IF NOT EXISTS file_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    current_id VARCHAR(20) NOT NULL,      -- Public file id (e.g. 0x500)
    previous_id VARCHAR(20),              -- Previous file id (chain reference)
    file_name VARCHAR(255) NOT NULL,      -- Original file name
    file LONGBLOB NOT NULL,               -- AES-256-CBC encrypted file content (base64)
    iv VARCHAR(64),                       -- Base64 encoded initialisation vector
    description TEXT,                     -- Optional description
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
