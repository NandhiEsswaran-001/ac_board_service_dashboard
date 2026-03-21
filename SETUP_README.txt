============================================================
  AC SERVICE MANAGEMENT SOFTWARE
  Setup & Installation Guide
============================================================

REQUIREMENTS
------------
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache / Nginx web server (XAMPP, WAMP, or LAMP)
- A web browser


STEP 1 — SET UP THE DATABASE
------------------------------
1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Click "SQL" tab at the top
3. Copy the entire contents of: database_setup.sql
4. Paste it into the SQL window and click "Go"
5. This will create the database "ac_service_db" and all tables

OR using MySQL command line:
  mysql -u root -p < database_setup.sql


STEP 2 — CONFIGURE DATABASE CONNECTION
-----------------------------------------
Open the file: includes/config.php

Change these values to match YOUR server:
  define('DB_HOST', 'localhost');    ← usually keep as localhost
  define('DB_NAME', 'ac_service_db');  ← keep as-is
  define('DB_USER', 'root');         ← your MySQL username
  define('DB_PASS', '');             ← your MySQL password


STEP 3 — COPY FILES TO SERVER
--------------------------------
Copy the entire "ac_service" folder to your web server root:
  - XAMPP: C:\xampp\htdocs\ac_service\
  - WAMP:  C:\wamp64\www\ac_service\
  - Linux: /var/www/html/ac_service/


STEP 4 — ACCESS THE SOFTWARE
------------------------------
Open your browser and go to:
  http://localhost/ac_service/

Default login credentials:
  Username: admin      Password: password
  Username: staff1     Password: password

⚠ IMPORTANT: Change these passwords after first login!
   Go to: Manage Users → Change Password


============================================================
  DEFAULT ACCOUNTS
============================================================

  Role: Owner
  Username: admin
  Password: password
  (Can manage users, see all data)

  Role: Staff
  Username: staff1
  Password: password


============================================================
  FEATURES
============================================================

✅ Board Service (Shop Repair)
   - New board entry with customer details
   - Status tracking: Pending → In Process → Completed → Delivered
   - WhatsApp notification buttons (opens WhatsApp with pre-filled message)
   - Full edit and update workflow

✅ Field Service (Home/Office Visit)
   - New field service entry
   - Technician assignment
   - Status: Scheduled → In Progress → Completed
   - Payment tracking: Pending / Partial / Paid
   - WhatsApp notification buttons

✅ Dashboard
   - Live counts of pending, in-process, completed boards
   - Field services today
   - Total revenue summary

✅ Technician Report (NEW)
   - Filter by Month, Year, and specific Technician
   - Shows how many days each technician worked in the selected month
   - Visual attendance bar (days worked / total days in month)
   - Per-technician: total jobs, completed jobs, revenue collected
   - Daily breakdown: which dates they worked and how many jobs per day
   - Summary table with attendance % for all technicians

✅ User Management (Owner only)
   - Add/delete users
   - Change passwords
   - Roles: Owner, Staff, Technician

✅ WhatsApp
   - WhatsApp uses the desktop/app deep link (no API needed)
   - Clicking the button opens WhatsApp Desktop/App with pre-filled message
   - Staff sends the message manually (one click)

✅ Responsive Design (NEW)
   - Works on Desktop, Tablet, and Mobile
   - Hamburger menu on tablet/mobile screens
   - Touch-friendly layout


============================================================
  FOLDER STRUCTURE
============================================================

ac_service/
├── index.php               ← Login page
├── logout.php              ← Logout
├── database_setup.sql      ← Run this to set up the database
├── SETUP_README.txt        ← This file
├── css/
│   └── style.css           ← All styles (responsive included)
├── js/
│   └── app.js              ← JavaScript (hamburger menu included)
├── includes/
│   ├── config.php          ← Database config (EDIT THIS)
│   ├── header.php          ← Page header/sidebar
│   └── footer.php          ← Page footer
└── pages/
    ├── dashboard.php           ← Main dashboard
    ├── board_list.php          ← Board service list
    ├── board_new.php           ← New board entry
    ├── board_view.php          ← View board details
    ├── board_edit.php          ← Edit/update board
    ├── field_list.php          ← Field service list
    ├── field_new.php           ← New field service
    ├── field_view.php          ← View field service
    ├── field_edit.php          ← Edit/update field service
    ├── technician_report.php   ← Technician work days report (NEW)
    └── users.php               ← User management


============================================================
  SUPPORT NOTES
============================================================

- All data is stored in your local MySQL database
- No internet connection required (except for WhatsApp)
- Works on local network — staff can access via your PC's IP
  e.g. http://192.168.1.100/ac_service/
- Mobile browsers supported — use the hamburger menu to navigate

============================================================
