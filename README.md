# VulnBookshop — Sensitive Data Exposure Lab (admin.php)

Objective:
1. Find the unlinked debug endpoint `/admin.php`.
2. Retrieve the flag shown on that page (PUBLIC_LAB_FLAG).

Rules:
- This is a controlled lab environment. Do not attack other servers.
- Tools: browsing, simple recon, directory enumeration (low-rate).
- Do not attempt destructive actions. Be polite.

Hints:
- Check `/robots.txt` and HTML source comments.
- Try common paths: `/admin.php`, `/debug.php`, `/old-admin/`.

Submission:
- The flag string (exact) and the URL where you found it.
- One-line explanation of how you discovered the page.

Instructor note: the public flag is provided via the environment variable `PUBLIC_LAB_FLAG`.
