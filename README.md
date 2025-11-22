# web_ukim

A lightweight PHP website for managing articles, events, works (karya) and achievements (prestasi). Built as a simple PHP / MySQL app and intended to run on a local web server such as Laragon, XAMPP or similar.

---

## Table of contents

- About
- Prerequisites
- Installation & setup
- Database
- Important files and folders
- Running the site (quick)
- Admin area
- Troubleshooting
- Security & recommended improvements
- License

## About

This repository contains a simple PHP website with an admin panel to manage content (articles, events, karya, prestasi). The project follows a straight-forward file-based PHP approach and stores data in a MySQL database.

## Prerequisites

- PHP 7.4 / 8.x (web server with PHP enabled)
- MySQL or MariaDB
- A local web server environment such as Laragon, XAMPP, WAMP, or Docker
- phpMyAdmin or another tool to import SQL

On Windows the repository was used under Laragon (project path example: `C:\laragon\www\web_ukim`).

## Installation & setup

1. Place the project inside your web root. Example with Laragon:

```
# copy project folder to Laragon www directory
# e.g. C:\laragon\www\web_ukim
```

2. Create a MySQL database and import the provided SQL file `web_ukim_new.sql` (located in the project root):

Using phpMyAdmin: import the `web_ukim_new.sql` file.

Or using MySQL CLI (PowerShell):

```
mysql -u root -p < web_ukim_new.sql
```

3. Configure the database connection by editing `conn/koneksi.php`. Set your DB host, name, user and password according to your environment.

4. Start your web server and open the site in a browser, e.g.:

```
http://localhost/web_ukim/
```

## Database

- The project includes `web_ukim_new.sql` which contains the database schema and initial data.
- If you need to create an admin user manually, check the `users` table in the imported database and insert a record (or use the site's registration if available).

## Important files and folders

- `index.php` — front page
- `artikel.php`, `event.php`, `karya.php`, `prestasi.php` — front-end pages
- `list-*.php` — listing pages
- `conn/koneksi.php` — database connection (edit this to configure DB)
- `conn/controller.php` — central controller for some actions
- `admin/` — admin panel and CRUD pages for content
- `asset/gbr_blog/`, `asset/gbr_event/` — image folders
- `layout/header_index.php`, `layout/footer_index.php` — front-end layout includes
- `style/`, `layout/style/` — CSS for front and admin pages

## Admin area

- Admin pages are under the `admin/` folder. A typical admin URL is:

```
http://localhost/web_ukim/admin/admin.php
```

- There is no built-in default admin password included in this README. To gain admin access either:
  - use the site's registration flow (if enabled), or
  - create a user directly in the database (`users` table) after importing `web_ukim_new.sql`.

## Troubleshooting

- If you see database connection errors, open `conn/koneksi.php` and verify host, username, password and database name.
- If images are missing, confirm files exist under `asset/` and that file/folder permissions allow the web server to read them.
- If CSS doesn't load, ensure paths in the layout files match the deployed location and that your web server is serving `.css` files.

## Security & recommended improvements

This project is a simple PHP app. Consider the following improvements before using it in production:

- Move DB credentials out of plain PHP files and into environment variables or a `.env` file.
- Use prepared statements (PDO or MySQLi with parameter binding) to avoid SQL injection.
- Ensure passwords are securely hashed (password_hash / password_verify).
- Add input validation and CSRF protection on forms.
- Limit file upload types and size; store uploads outside the web root or use secure folder permissions.
- Regular backups of the database and uploaded assets.

## Contributing

Small fixes, documentation updates and security improvements are welcome. Open an issue or submit a pull request.

## License

This project does not have a license file in the repository. If you want to publish it publicly, add a `LICENSE` file with the license you prefer (MIT, Apache-2.0, etc.).

---

If you want, I can also:

- add a minimal `CONTRIBUTING.md` and `LICENSE` file,
- add a short script or instructions to auto-import the SQL on Windows,
- or scan key PHP files and suggest concrete security fixes (e.g., convert `conn/koneksi.php` to use PDO with prepared statements).

If you'd like any of those, tell me which one to do next.
