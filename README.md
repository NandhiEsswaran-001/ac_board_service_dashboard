# AC Board Service Dashboard

A lightweight, local-first AC service management dashboard for board repairs and field service jobs. Built with PHP + MySQL, designed for fast daily use by owners, staff, and technicians.

## Highlights
- Board service workflow with status tracking and WhatsApp deep links
- Field service workflow with technician assignment and payment status
- Dashboard KPIs for pending, in-process, completed, and revenue summary
- Technician report with attendance-style breakdowns
- Responsive UI for desktop, tablet, and mobile

## Tech Stack
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx (XAMPP, WAMP, or LAMP)

## Quick Start
1. Create database and tables
   - Open `database_setup.sql` and run it in phpMyAdmin (SQL tab)
   - Or run: `mysql -u root -p < database_setup.sql`
2. Configure DB connection in `includes/config.php`
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
3. Serve the project
   - XAMPP: `C:\xampp\htdocs\ac_service\`
   - WAMP: `C:\wamp64\www\ac_service\`
   - Linux: `/var/www/html/ac_service/`
4. Open in browser
   - `http://localhost/ac_service/`

## Access
For security, default credentials are not stored in the repository. Create your own users in the database after setup.

## WhatsApp Notes
WhatsApp buttons use the app deep link (no API). Clicking opens WhatsApp Desktop/App with a pre-filled message; the staff member sends it manually.

## Structure
- `index.php` login
- `pages/` app pages (dashboard, board, field, technician report, users)
- `includes/` shared config and layout
- `css/` and `js/` front-end assets
- `database_setup.sql` database schema

## License
Internal/business use. Add a license if you plan to distribute.
