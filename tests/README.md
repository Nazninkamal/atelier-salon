# Integration checks

Run only on a disposable WordPress test site with the current parent theme active. These checks create/reuse layouts and rate-limit transients. Never use a live shop/salon database. Browser, SMTP and compatibility evidence is recorded in `review/`; the test databases, credentials, mail receiver and dependency binaries are not distributed.

With WordPress CLI and Atelier Appointments active:

```sh
wp eval-file /path/to/repository/tests/appointments.php
```

The appointment check intercepts `wp_mail`; it does not send external email. Thirteen assertions cover validation, throttling and mail failure.

With Elementor Free also active and the fourteen generated local pages already created:

```sh
wp eval-file /path/to/repository/tests/elementor-preservation.php
```

This verifies rerun preservation, page counts and capability checks. It may create missing layouts on the disposable installation.

Build current installables using `python3 tools/package.py`. The builder reads the three source directories, adds documentation, tests ZIP integrity and writes versioned artifacts plus `dist/SHA256SUMS`. External SMTP delivery must still be tested on the buyer's configured hosting; see `docs/INSTALL.html`.
