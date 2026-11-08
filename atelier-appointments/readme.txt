=== Atelier Appointments ===
Requires at least: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Install via Plugins > Add New > Upload Plugin. Set recipient under Settings >
General > Appointment email. Place [atelier_booking] on a page. Emails requests,
not confirmed bookings. Configure email delivery and test before launch.
The booking page slug should be book-appointment for success/error redirects.
No appointment database is created. Uses a one-minute hashed-IP transient for
rate limiting. Follow your email provider's retention and privacy requirements.
