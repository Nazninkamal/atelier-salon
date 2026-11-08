=== Atelier Appointments ===
Contributors: Naznin Kamal
Requires at least: 6.4
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Emails appointment requests; does not reserve a time, process payments or keep a
booking database. Install via Plugins > Upload Plugin. Set Settings > General >
Appointment email and use [atelier_booking]. Keep the book-appointment page slug
for success/error redirects. Configure SMTP and test recipient inbox delivery.
Exclude shortcode pages and admin-post.php from full-page/CDN caching. The plugin
sets DONOTCACHEPAGE/no-cache headers; upstream caching needs explicit exclusions.

Data: name, email, optional phone/message, requested service/date/time and consent
are emailed to the configured recipient. WordPress/mail-provider logs and mailbox
retention policies apply. A hashed direct-peer IP transient is held for one minute
to throttle requests; it is not a complete spam prevention system. Tell visitors
how your salon processes requests in its privacy notice. No external font or
analytics service is called by this plugin. Honeypot and nonce validation included.

Translate using languages/atelier-appointments.pot and standard WordPress MO files.
Support: Naznin Kamal — najninkamalrimi@gmail.com
Source and updates: https://github.com/Nazninkamal/atelier-salon
