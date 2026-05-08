# Forgeframe Studios — shirishagujja.me

Production-ready, responsive website for **Forgeframe Studios**.  
**Tagline:** *Crafting cinematic stories for brands and creators.*

- **Tech:** HTML5, CSS3, Bootstrap 5 (CDN), vanilla JS, PHP 7.4+, Font Awesome, Google Fonts (Poppins).
- **Data:** Hybrid storage - legacy text/JSON files under `/data` plus MySQL (PDO) for the User module.
- **Host:** Standard PHP host (PHP 7.4+).

## User module (MySQL + PDO)

This project now includes a full database-driven User module:

- `users.php` - User section dashboard
- `user-create.php` - create user form with server-side validation
- `user-search.php` - keyword search across name/email/phones
- `config.php` - database connection settings (PDO)
- `schema.sql` - database and `users` table schema
- `seed-users.sql` - sample data (20 users)

### 1) Configure database credentials

Open `config.php` in the project root and set:

- `host`
- `port`
- `dbname`
- `username`
- `password`

### 2) Create schema

Import `schema.sql` using phpMyAdmin **or** CLI:

```bash
mysql -u your_username -p < schema.sql
```

### 3) Seed sample users

Import `seed-users.sql`:

```bash
mysql -u your_username -p forgeframe_studios_site < seed-users.sql
```

### 4) Test the flow

- Open `/users.php` and confirm the User dashboard loads.
- Click **Create User** and submit a valid record.
- Try duplicate email submission and confirm friendly error appears.
- Open **Search User**, enter partial values (first name, email fragment, or phone), and verify matching rows display.

### 5) Notes

- All user input is validated server-side.
- Output is escaped with `htmlspecialchars`.
- Inserts and searches use prepared statements.
- Search uses partial matching (`LIKE`) across first name, last name, email, home phone, and cell phone.
- UI is integrated with the existing cinematic dark Forgeframe style.

---

## Deployment steps

1. **Upload** all project files to your web root (e.g. `public_html` or `www`) so that `index.php` is the site entry point.

2. **Make `/data` writable** so PHP can write contacts and (if you use it) update users:
   ```bash
   chmod 770 data
   # If your server runs as www-data:
   chown www-data:www-data data
   ```
   On shared hosting, often `chmod 755 data` or `775` is sufficient; ensure the web server user can write to `data`.

3. **Create placeholder images** (if not already present):
   ```bash
   php write_placeholders.php
   ```
   This creates minimal JPG placeholders in `assets/images/` for all product and hero/news images. For production, replace with real images (see **Images** below).

4. **Optional — bcrypt password:**  
   Default login is **admin** / **Admin@123** (plain text; for classroom use). For production:
   ```bash
   php hash_password.php YourNewPassword
   ```
   Copy the output hash and in `data/users.txt` set:
   ```
   admin:$2y$10$...paste_hash_here...
   ```

5. **Configure brand/domain** in `includes/config.php` if needed:
   - `SITE_DOMAIN` — set to `shirishagujja.me`
   - `SITE_NAME`, `SITE_TAGLINE` — adjust if you rebrand

---

## Default credentials

| Purpose   | Username | Password  |
|----------|----------|-----------|
| Admin    | `admin`  | `Admin@123` |

**Security note:** Plain-text passwords in `users.txt` are for classroom/lab use only. In production, use `hash_password.php` to generate a bcrypt hash and store that in `data/users.txt` as `admin:<hash>`.

---

## Changing the admin password (bcrypt)

1. Run: `php hash_password.php YourNewSecurePassword`
2. Open `data/users.txt` and replace the line so it reads:  
   `admin:<paste_the_hash_here>`
3. Save the file. Login will then use the new password.

---

## Data and seed files

| File            | Purpose |
|-----------------|--------|
| `data/users.txt`     | Admin credentials. Seeded with `admin:Admin@123`. |
| `data/products.json`| 10 services/products (slug, title, descriptions, images, tiers). |
| `data/news.json`    | 3 sample news posts. |
| `data/contacts.txt` | Contact form submissions (CSV appended). Initially empty. |
| `data/company-contacts.txt` | Five company contacts for the Contact page (format per line: `Name | Role | Email`, pipe-separated). |

**Example line in `data/contacts.txt`** (after a form submit):
```text
2025-03-14 12:00:00,Jane Doe,jane@example.com,555-1234,"Hello, I need a quote for a corporate video.",192.168.1.1
```

---

## Images

- **Placeholders:** Run `php write_placeholders.php` to generate small placeholder JPGs for:
  - Hero: `hero-bg.jpg`
  - About: `about-story.jpg`
  - News: `news-reel-2025.jpg`, `news-color-grading.jpg`, `news-event-bts.jpg`
  - Products: for each slug, `{slug}-1.jpg`, `{slug}-2.jpg`, `{slug}-3.jpg` (e.g. `commercial-ads-1.jpg`).

- **Real images (optional):** Use local files under `assets/images/` with the same filenames. Suggested Unsplash/Pexels search terms:
  - Hero: "cinematic film production", "video production studio"
  - Commercial: "commercial video production", "ad filming"
  - Corporate: "corporate video interview", "office b-roll"
  - Product: "product video", "product photography video"
  - YouTube: "youtube creator", "video editing"
  - Post-production: "video editing", "color grading"
  - Color: "color grading", "video color correction"
  - Motion: "motion graphics", "logo animation"
  - Explainer: "explainer animation", "infographic"
  - Event: "wedding videography", "conference event"
  - Drone: "drone videography", "aerial footage"

---

## Verification checklist

Use this list to confirm the site works after deployment:

- [ ] Contact form appends to `data/contacts.txt`
- [ ] Admin login works with `admin` / `Admin@123`
- [ ] `admin.php` lists current users (Mary Smith, John Wang, Alex Bington, Priya Rao, Omar Khalid)
- [ ] Product pages update `recent_products` cookie (max 5)
- [ ] Product pages increment `visit_counts` cookie
- [ ] `products.php` shows **Recently Viewed** and **Top 5 Most Visited** when cookie data exists
- [ ] Images load and have alt text

---

## Quick deployment checklist

1. Upload all files to the PHP host.
2. `chmod 770 data` (or 755/775 as required).
3. Run `php write_placeholders.php` or add real images to `assets/images/`.
4. Open the site; test contact form and admin login.
5. Walk through the verification checklist above.

---

## File structure

```text
/
├── index.php          # Home (hero, pitch, featured services, CTA)
├── about.php          # About (story, mission, team)
├── products.php       # Services grid + Recently Viewed + Top 5 Most Visited
├── product.php       # Single product (slug from query string)
├── news.php           # News listing (3 posts, modals)
├── contact.php        # Contact form → appends CSV to data/contacts.txt
├── contact-list.php   # Admin: view contacts (protected)
├── login.php          # Admin login
├── admin.php          # Admin area (users list, links, export CSV)
├── logout.php         # Destroy session, redirect to login
├── hash_password.php  # Generate bcrypt hash for users.txt
├── write_placeholders.php  # Create placeholder images
├── includes/
│   ├── config.php     # Brand, domain, paths (edit for production)
│   ├── header.php     # HTML head, nav, schema.org
│   └── footer.php     # Footer, scripts
├── assets/
│   ├── css/style.css  # Cinematic theme
│   ├── js/main.js     # Nav, modal, gallery
│   ├── js/cookies.js  # recent_products, visit_counts, render helpers
│   └── images/        # Hero, about, news, product images
├── data/
│   ├── users.txt      # admin:Admin@123
│   ├── products.json  # 10 products
│   ├── news.json      # 3 news posts
│   └── contacts.txt   # Appended CSV (writable)
└── README.md          # This file
```

---

## Contact form format (CSV)

Each submission appends one line to `data/contacts.txt`:

```text
YYYY-MM-DD HH:MM:SS,Name,Email,Phone,"Message (quotes escaped)",IP
```

Message is wrapped in double quotes; internal quotes escaped as `""`. Server-side validation: name, email, message required; email must be valid.

---

*Forgeframe Studios — Crafting cinematic stories for brands and creators.*
