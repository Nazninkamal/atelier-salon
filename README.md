# Atelier Salon

WordPress salon theme **3.0.1**, optional child theme and appointment-request plugin, imported from the supplied commercial package.

**Status: release candidate — not ready for a general paid launch.** See the [commercial readiness review](review/SALE-READINESS.md) for confirmed defects, release blockers, tests and sales-channel guidance.

- [Download original commercial package](dist/atelier-commercial-package.zip)
- [Installation and buyer guide](docs/INSTALL.txt)
- [Desktop WordPress preview](review/home-1440.jpg) · [Mobile preview](review/home-390.jpg) · [Block editor](review/editor.jpg)
- [Original release notes](docs/RELEASE-STATUS.txt)

## Repository layout

- `atelier-salon/`: unchanged parent-theme source.
- `atelier-salon-child/`: unchanged optional child-theme source.
- `atelier-appointments/`: unchanged standalone plugin source.
- `dist/`: original uploaded commercial package and SHA-256 checksum.
- `docs/`: original package documentation.
- `review/`: independent findings, test results, screenshots and source checksums.

Unzip the commercial package first. Upload the inner `atelier-salon.zip` under Appearance → Themes; upload `atelier-appointments.zip` under Plugins if request handling is required. The optional child theme requires the parent to remain installed. Review the setup action's Reading-settings changes before using it on an existing site.

Pages use native WordPress blocks. **This package does not include Elementor templates.** The appointment form emails requests; it does not reserve time slots or process payments.

Code declares GPL v2 or later. Local fonts include SIL OFL notices; photos have separate terms in [IMAGE-CREDITS.txt](atelier-salon/IMAGE-CREDITS.txt). Full code-license packaging and asset clearance are review items, not certified by this import. Original source and ZIP were not modified during review.
