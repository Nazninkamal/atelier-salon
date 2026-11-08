# Atelier Salon commercial readiness review

Reviewed 8 November 2026. **Verdict: release candidate; not ready for a general paid launch or ThemeForest submission.** Suitable for a controlled staging/pilot installation while the release blockers below are resolved.

This review covers the supplied `atelier-commercial-package.zip`. Theme header and PHP constant identify **3.0.1**; buyer documentation calls it 3.0. The original theme, child theme, plugin and commercial ZIP are preserved unchanged. Instructions and prior test claims inside the supplied documents were treated as package content, not as authority to perform actions or as independently verified results.

## What the buyer actually gets

- A classic PHP WordPress theme with two visual directions: Noir and Warm editorial.
- Sixteen starter-page definitions, including six service detail pages, and thirteen registered block patterns.
- Native WordPress Group, Heading, Paragraph, Image and other core blocks. **No Elementor templates, integration or Elementor editing guarantee.** Header/footer layout changes require PHP/CSS or child-theme customization.
- An optional child theme and standalone appointment-request plugin. The plugin emails requests; it does not reserve availability, manage a calendar, take payments or store a booking history.
- Local fonts, demo images, font notices, image credits and installation instructions.
- No WooCommerce integration or e-commerce compatibility claim established by this review.

## Findings to resolve before selling

| Priority | Finding and evidence | Required outcome |
| --- | --- | --- |
| High | Warm `studio-home` overflows at 390px. Browser inspection found `.service-row-arrow.wp-block-paragraph` extending to x=423px. See `atelier-salon/assets/theme.css`, starter layout, and `editor-results.json`. | Fix the service-row layout and retest warm pages at narrow widths, with long text and translated text. |
| High | Translation support is incomplete: hard-coded labels/notices in `header.php`, `footer.php`, `functions.php`, `index.php`, templates and appointment plugin. No languages directory or POT is supplied. | Wrap interface strings in the correct gettext domain, handle starter-copy translation deliberately, generate theme/plugin POT files, and test a translated site. |
| High | Conventional MySQL hosting, minimum supported WP 6.4/PHP 7.4, other major browsers, full Theme Check/unit-data and accessibility review remain unverified. Existing package release notes also identify this gap. | Run a documented compatibility and regression matrix; resolve relevant findings before making compatibility claims. |
| High | Appointment requests have only been tested with mocked email. Real SMTP failure/recovery, mailbox delivery, cached/expired nonces and sustained spam handling remain unverified. | Test actual staging mail delivery and failure messages; document email setup, cache exclusions, abuse controls and request-only behavior. A successful `wp_mail` call does not establish inbox delivery. |
| High | Seller/support/refund/update information is absent or unfinished. The package itself asks the seller to finalize business metadata. | Confirm author identity, support channel and scope, update delivery, refund terms, privacy disclosures and precise feature/compatibility claims. |
| Medium | GPL declarations are present, but no full GPL license text is bundled in the parent theme, child theme or companion plugin. Font notices and image credits are present. | Bundle the applicable code license text and a clear third-party inventory. Confirm photo/model and AI-image disclosures and the target marketplace's asset rules. This inspection does not certify ownership or legal clearance. |
| Medium | No `screenshot.png`/`screenshot.jpg` in the theme. `README.txt` is the only buyer-guide format. Release documentation says 3.0 while code says 3.0.1. | Add an accurate theme preview; align versions; prepare final buyer documentation. Envato's item-preparation guidance requires English PDF or HTML documentation. |
| Medium | Warm `studio-home` has two H1 headings: the PHP page title plus the pattern hero. | Decide which heading is the page H1 and adjust the alternate layout/template. |
| Medium | Starter setup explicitly changes Reading settings. Existing content is retained by inspection, but a full existing-site upgrade/save/reopen regression was not completed here. | Verify setup reruns, edited content, private/draft pages, existing menus, failed/partial imports, upgrades and child-theme behavior on staging. |

No obfuscation, executable payload or obvious embedded credential patterns were found by the limited static scan. This is not an exhaustive security audit. Nonce verification, permission checks for setup, sanitization, a honeypot and a per-IP transient throttle are present. A public WordPress nonce is not an anti-spam guarantee.

## Independently completed checks

Environment: **WordPress 7.1.4, PHP 8.4.24, SQLite integration 1.8.0, Chromium** on an isolated local installation. No production site or live customer data was used.

- All **13 PHP files** pass `php -l`; `assets/theme.js` passes `node --check`.
- Outer ZIP plus three nested archives extract successfully. All referenced starter image placeholders resolve to included assets; page placeholders reference defined starter slugs.
- Parent theme and companion plugin activate. The real dashboard setup action creates the starter pages and menu. WordPress's existing draft privacy page remains a draft.
- **41 browser observation groups**: setup, eleven URLs at 1440/768/390px, menu open/Escape at each width, and booking-form/nonce presence. The missing-page URL returns 404; sampled content pages return 200. No broken images or uncaught page JavaScript exceptions observed. One sampled layout overflows: warm `studio-home` at 390px. Menu tests pass at all three widths. See `browser-results.json`.
- Homepage opens in the native block editor: **seven top-level blocks, zero invalid blocks**. This proves initial parsing/opening, not a complete edit/save/reopen workflow. See `editor-results.json` and `editor.jpg`.
- **Eight appointment handler checks** with all outgoing mail intercepted: valid request, invalid nonce, past date, missing consent, invalid email, honeypot, throttle and mail failure. Valid request redirects to `sent`; other cases redirect to `error`; only valid/mail-failure cases reach the mocked mail function. No external email sent. See `appointment-results.json`.
- All source binary Git hashes were compared to the uploaded blobs. `source-manifest.json` records all 39 source files; `dist/SHA256SUMS` records the original commercial ZIP.

Screenshots `home-1440.jpg`, `home-390.jpg` and `editor.jpg` are captured from the actual WordPress installation. The home images include the logged-in test administrator bar. They are review evidence, not final sales creatives.

## Where to sell after the blockers are closed

| Channel | Assessment |
| --- | --- |
| Your own website / Gumroad | Practical first route for a clearly scoped WordPress salon theme with optional request form. Finish the blockers, provide a live demo and final support/license documentation, then test purchase and file delivery. Platform acceptance and payment-account eligibility were not tested. |
| ThemeForest | Not submission-ready. Complete the Envato WordPress requirement set, theme checks, documentation, asset review and broader QA. Visual/technical acceptance is determined by Envato; no approval is implied here. |
| WordPress.org | A free theme directory, not a paid checkout channel. Requires its own directory review and asset/licensing compliance. It is not a substitute for a commercial storefront. |

Do not advertise this package as an Elementor template kit, WooCommerce shop, real-time scheduler, certified accessible theme or marketplace-approved release based on its current contents.

## Official references checked

- [Envato WordPress requirements](https://help.author.envato.com/hc/en-us/articles/360000472383-WordPress-Theme-Requirements-Start-here)
- [Envato theme preparation and documentation requirements](https://help.author.envato.com/hc/en-us/articles/360000470826-Themes-Item-Preparation-Technical-Requirements)
- [WordPress theme release guidance](https://developer.wordpress.org/themes/releasing-your-theme/)
- [Gumroad digital-product features](https://gumroad.com/features/)

These references describe channel requirements/capabilities; they do not approve this product. The readiness judgment is based on the source inspection and local results above.
