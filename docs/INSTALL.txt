ATELIER SALON 3.1.0 — INSTALL AND EDIT
Seller: Naznin Kamal
Support: najninkamalrimi@gmail.com
Source/updates: https://github.com/Nazninkamal/atelier-salon

INSTALL
Unzip the commercial package first. Upload the inner atelier-salon.zip under
Appearance > Themes > Add New > Upload Theme and activate it. Optionally upload
atelier-salon-child.zip and activate the child while retaining the parent.
Upload atelier-appointments.zip under Plugins > Add New > Upload Plugin when
appointment requests are wanted. Neither a paid builder nor Elementor Pro is
required. WordPress and plugins are separate dependencies.

WordPress BLOCK PAGES
The Create starter pages notice creates the block pages and menu. On its first
run it changes Reading settings, as disclosed in the notice. Existing pages are
preserved; reruns preserve edits, menu assignments and Reading settings. Privacy
copy stays draft until reviewed and published. The Warm editorial pattern/page
is named Editorial studio demo. Change visual direction in Appearance > Customize
> Atelier design. Edit pages in the native block editor and insert Atelier Salon
patterns from the pattern library. Changing the visual direction does not replace
page content. Back up content before replacing a layout.

ELEMENTOR FREE PAGES
Install and activate Elementor Free. Open Appearance > Atelier Elementor setup.
Click Create Elementor pages. This creates fourteen separate editable pages,
including Home, Editorial studio, About, Services, Our work, Team, Contact,
Book appointment and six service details. It retains all native block pages.
The homepage checkbox is optional and initially unchecked. Existing menus remain
assigned: choose your Elementor pages in Appearance > Menus when switching the
whole site. Direct Edit with Elementor links appear on the setup screen.

Use Navigator to select sections, headings, images and text. Change headings and
copy in Content, replace images with the Image widget, and use Style controls for
typography/colors. Appointment areas use the Free Shortcode widget. The theme
supplies layout defaults; retain its CSS classes to keep the original design.
Header/footer and shared salon details remain in the theme/Customizer, not an
Elementor Pro Theme Builder. Re-running setup reuses existing pages, including
non-public pages, without changing saved designs. Trashed pages must be restored
manually. Existing block pages provide a fallback if Elementor is inactive.

Translate interface strings with the supplied languages/*.pot files and a standard
PO editor/translation plugin. Save the resulting theme/plugin MO files under their
languages directory or the normal wp-content/languages/themes/plugins locations.
Starter copy translates when inserted; existing buyer content is never translated
or replaced automatically. Theme palette labels are included in the theme POT.

BUSINESS CONTENT
Set your site title/logo, phone, address and hours in Settings > General and
Appearance > Customize. Replace fictional demo models, copy, service durations
and contact instructions with accurate business information. Use your own salon
photos. Review/publish privacy and appointment/cancellation policies.

APPOINTMENT EMAIL
This plugin emails requests; it does not reserve a slot, take payment or maintain
a calendar/booking database. Set Settings > General > Appointment email. Blank
uses the WordPress administrator email. Configure a reputable SMTP/transactional
mail plugin with your provider's verified sending domain. Keep credentials in
WordPress/provider configuration, never in theme files or GitHub.
Exclude booking pages and pages containing the shortcode from full-page/CDN
caching. The plugin sets DONOTCACHEPAGE and no-cache headers, but an upstream
cache can serve a page before PHP runs. Exclude admin-post.php from caching.
Test a real request, confirmation/error messaging, spam-folder placement and
recipient delivery on the target hosting. A successful SMTP handoff is not proof
of inbox delivery. Do not launch a salon request workflow without that test.

UPDATES AND SUPPORT
Read SUPPORT.md, LICENSING.md and the current TEST-REPORT.md. Back up first, test
updates on staging, and retain the parent theme when using the child. No automatic
updater is included. No guaranteed support SLA or lifetime updates are promised.
The storefront's purchase/refund terms must be shown before payment.
