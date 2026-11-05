# Encrypt System — Secure File Sharing (PHP + MySQL)

A web-based secure file sharing prototype built with **PHP**, **MySQL** and **AES-256-CBC** encryption. An admin uploads a file, the file content is encrypted before it is stored in the database, and a CEO/consumer panel can select a file ID and download the decrypted original.

## Features

- Admin upload panel with JPG validation and a 10 MB size limit
- AES-256-CBC encryption using OpenSSL before storing file data in MySQL
- Random IV generated for every uploaded file and stored with the record
- Auto-incrementing hexadecimal file IDs (`0x500`, `0x501`, …)
- CEO download panel populated from the database, with on-the-fly decryption
- MySQL schema included (`file_list.sql`)
- Simple, responsive HTML/CSS interface

## Tech Stack

`PHP` · `MySQL` · `HTML5` · `CSS3` · `OpenSSL` · `XAMPP`

## Project Structure

```text
Encrypt-System/
├── index.html          # Landing page
├── admin.html          # Admin upload form
├── ceo.php             # CEO download panel (queries MySQL)
├── css/style.css       # Styling
├── js/script.js        # Client-side helpers
├── php/upload.php      # Encrypt + store uploaded file
├── php/download.php    # Decrypt + download the selected file
├── file_list.sql       # MySQL schema
├── images/
└── uploads/
```

## Database Schema

`file_list.sql` creates the `encrypt_system` database and the `file_list` table:

| Column | Type | Description |
| --- | --- | --- |
| `id` | INT (PK) | Auto-increment row ID |
| `current_id` | VARCHAR(20) | Public hexadecimal file ID |
| `previous_id` | VARCHAR(20) | Previous file ID (chain reference) |
| `file_name` | VARCHAR(255) | Original file name |
| `file` | LONGBLOB | Encrypted file content |
| `iv` | VARCHAR(64) | Base64 encoded initialisation vector |
| `created_at` | TIMESTAMP | Upload timestamp |

## Setup

1. Install **XAMPP** (or WAMP) and start **Apache** and **MySQL**.
2. Import the schema:

```sql
SOURCE file_list.sql;
```

3. Copy the project folder into `htdocs/`:

```text
C:\xampp\htdocs\Encrypt-System
```

4. Update the database credentials in `php/upload.php` and `php/download.php` if your MySQL user is not `root` with an empty password.
5. Open:

```text
http://localhost/Encrypt-System/
```

6. Use `admin.html` to upload a JPG and `ceo.php` to select and download it.

## Security Notes

- Change `$secret_key` in `php/upload.php` before using this outside a local demo.
- Use a dedicated MySQL user instead of `root`.
- This is an academic prototype; add authentication, HTTPS and stronger key management before production use.

## Author

**Sathiya** — [GitHub](https://github.com/Sathiya-123321)
