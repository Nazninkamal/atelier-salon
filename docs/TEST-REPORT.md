# Atelier Salon 3.1.0 verification

8 November 2026. Implementation fixes from the 3.0.1 audit are complete. Ready for staging/customer customization with native blocks or Elementor Free. **Production inbox delivery still requires the buyer's real hosting/mail-provider configuration and an inbox test.** No production site was changed and no external email was sent.

## Completed

| Check | Result |
| --- | --- |
| Layouts | Warm-home overflow and duplicate template H1 fixed; service arrows no longer stretch/rotate tall grid cells. Long adjacent-post titles and comment fields bounded. |
| Main browser matrix | 312 observations across native and Elementor layouts, Chromium/Firefox, 1440/768/390/320px, WP 7.1.4 with SQLite and MariaDB, and WP 6.4.7 with MariaDB. Zero status/H1/image/overflow failures and zero uncaught page JS exceptions. |
| Final Elementor matrix | 112 further observations over all 14 Elementor layouts, both browsers/four widths, after decorative-text refinements. Zero failures or uncaught page JS exceptions. |
| WordPress core content | Official Theme Unit Test Data imported into an isolated MariaDB installation; remote attachments intentionally skipped. 30 representative observations covering markup, sticky/long/empty-title posts, pagination, password form, comments, search, archive and 404 at 1440/390/320px. Template heading and overflow checks pass. The markup fixture deliberately includes a content H1; its author content was not rewritten. No full attachment/gallery or exhaustive unit-data certification is claimed. |
| PHP | Native PHP 8.4.24 lint/runtime; additional WP 6.4.7 smoke checks under PHP-WASM 7.4.34-dev and 8.2.35: 16-page setup, homepage block rendering and mocked appointment send pass. WASM smoke tests are not native-hosting/browser certification for those PHP versions. |
| WordPress editing | Native block heading edit, save, reopen, frontend verification and restoration pass. |
| Elementor Free editing | Elementor 4.3.6: heading Content text, Style color and 52px font size save and render correctly; saved heading reopens; Image replacement saves/renders; original demo restored. No uncaught editor JS exceptions. No Pro installed. |
| Setup safety | MariaDB WP 7.1.4 and 6.4.7: 16 definitions, no duplicate pages, buyer text preserved, rerun preserves Reading settings, privacy remains draft, unauthorized helper blocked. Elementor rerun preserves all 14 layouts, menu/home settings and metadata; unauthorized setup blocked; invalid HTTP nonce returns 403; 14 direct edit links verified. |
| Email validation | 13 checks: valid, invalid nonce, past date, exact consent, invalid email, honeypot, array input, long phone, invalid date/service/time, throttle and mocked mail failure. All pass. |
| SMTP transport | Actual WordPress/PHPMailer SMTP against a local capture server: acceptance and rejection paths pass. A guest browser posts the real shortcode form through admin-post.php, receives `appointment=sent`, sees success messaging, and the receiver captures one message. No `pre_wp_mail` mock in transport tests. Addresses use reserved .test domains; nothing forwarded externally. |
| Translation | Theme/plugin POT catalogs generated with WP-CLI, including theme.json labels. Real MO fixture verifies theme menu label, starter title and service translation; submitted service values remain stable and existing post content stays unchanged. No complete foreign-language localization is claimed. |
| Packaging | Full GPL v2 license in all three installables; OFL notices retained; earlier stock files replaced by the supplied illustrative AI assets; accurate 1200×900 screenshot; seller/support information and HTML buyer guide included. Source/package checksums and ZIP validation included. |
| Dependencies/child | Elementor-off pages retain native fallback content and headings; warm fallback stays warm. Child activates on WP 6.4.7/MariaDB and its stylesheet loads. |
| Theme Check | No REQUIRED or WARNING findings. One text-domain INFO and three optional recommendations remain: custom-header, custom-background and sidebar/widget areas. This theme intentionally uses its own page layouts rather than those optional features. |

## Scope and remaining deployment work

- Tested builder combination: WordPress 7.1.4 / PHP 8.4.24 / Elementor Free 4.3.6. WordPress 6.4.7 tests cover native/child themes and companion plugin; current Elementor's own WP/PHP requirements still apply.
- Elementor 4.3.6 emitted its own PHP 8.4 implicit-nullability deprecation in `modules/global-classes/atomic-global-styles.php:410`; WP 6.4.7 emitted a core `wp_getimagesize` deprecation on PHP 8.4. These did not prevent the tested flows. Keep dependencies patched and use their supported hosting combinations. No theme/plugin fatal was found in the successful final checks.
- External SMTP authentication/TLS, DNS sender authentication, recipient inbox/spam placement and real provider outage behavior are not verified without a configured provider. Exclude shortcode pages from upstream caches; PHP no-cache flags alone cannot invalidate a CDN response served before PHP runs.
- Replace fictional demo imagery/copy and finalize salon privacy/appointment details. The selling storefront must display purchase/refund terms before payment. No payment service, real-time scheduler or booking database is included.
- Safari/WebKit, screen-reader certification, exhaustive RTL/multilingual testing, every plugin combination, performance scores and marketplace acceptance are not claimed. Theme Check is a static check, not marketplace approval.

Evidence JSON and screenshots are in the repository’s `review/` directory. Earlier 3.0.1 review files are historical; use this report and the versioned 3.1.0 ZIP for the current release.
