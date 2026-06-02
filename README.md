# Exam_datesheet_final

A web‑based exam scheduling system built with PHP that allows administrators and exam coordinators to manage courses, exam halls, schedules, and user accounts. The application stores exam data in a MySQL database and provides separate dashboards for each role.

---

## Overview

The **Exam_datesheet_final** project streamlines the creation and distribution of exam date‑sheets. It offers:

* Secure login for **Admin** and **Exam Coordinator** roles.  
* CRUD operations for users, courses, exam halls, and schedules.  
* Automatic generation of printable date‑sheets (e.g., `CS201.txt`, `CS301.txt`).  
* A clean, responsive UI built with plain CSS.

---

## Features

| Feature | Description |
|---------|-------------|
| **Role‑based access** | Admin can manage users; Exam Coordinator can manage courses, halls, schedules, and superintendents. |
| **Exam schedule management** | Create, edit, and delete exam entries; view the full schedule in a table format. |
| **Course & hall administration** | Add, update, or remove courses and exam halls. |
| **Superintendent credentials** | Assign login details to superintendents via `assign_superintendent_credentials.php`. |
| **Exportable date‑sheets** | Generate plain‑text files (`CS201.txt`, `CS301.txt`, …) for each course. |
| **Responsive UI** | Simple, mobile‑friendly layout using `css/style.css`. |
| **SQL dump** | Database schema provided in `Database/exam_db.sql`. |

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL / MariaDB |
| **Frontend** | HTML5, CSS3 (custom stylesheet) |
| **Server** | Apache / Nginx (any LAMP/LEMP stack) |
| **Version Control** | Git (GitHub) |

---

## Installation

> **Prerequisites**: PHP, MySQL, and a web server (Apache/Nginx) installed on your machine.

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/Exam_datesheet_final.git
   cd Exam_datesheet_final
   ```

2. **Create the database**

   ```bash
   mysql -u root -p < Database/exam_db.sql
   ```

   *The dump creates a database named `exam_db` with all required tables.*

3. **Configure database connection**

   Edit the two `config.php` files (root and `admin/`/`exam_coordinator/`) and replace the placeholder values with your own credentials:

   ```php
   // Example (admin/config.php)
   $db_host = 'localhost';
   $db_name = 'exam_db';
   $db_user = 'YOUR_DB_USERNAME';
   $db_pass = 'YOUR_DB_PASSWORD';
   ```

4. **Set up the web root**

   - Move the project folder into your web server’s document root (e.g., `/var/www/html/Exam_datesheet_final`).
   - Ensure the server has read/write permissions for the project files.

5. **Optional: Secure the `admin/` and `exam_coordinator/` directories**

   - Add `.htaccess` rules or configure your server to restrict direct access if needed.

6. **Start the server**

   ```bash
   # For built‑in PHP server (development only)
   php -S localhost:8000
   ```

   Navigate to `http://localhost:8000/admin/admin