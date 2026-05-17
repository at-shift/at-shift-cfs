# Custom Field Suite Maintenance Build

This repository contains a security and compatibility maintenance build of
Custom Field Suite, based on the upstream Custom Field Suite 2.6.7 codebase.

Custom Field Suite is a WordPress plugin for adding custom fields to posts,
pages, and custom post types.

## Attribution

- Original plugin: Custom Field Suite
- Original author: Matt Gibbs
- Original project: https://wordpress.org/plugins/custom-field-suite/
- Original source: https://github.com/mgibbs189/custom-field-suite
- Maintenance build: @shift Yoshiya Tsuchisaka
- GitHub account: https://github.com/at-shift

The original author attribution and GPLv2 license are preserved. This repository
is a maintenance build, not an official upstream release by the original author.

## License

This maintenance build is distributed under the GNU General Public License
version 2 (GPLv2), the same license as the upstream plugin.

You may use, copy, modify, and redistribute this package, including modified
versions, under the terms of GPLv2. When redistributing, keep the GPLv2 license
notice, preserve the original author attribution, include the source code, and
make clear that this is a maintenance build.

See [LICENSE](LICENSE) for the full GPLv2 license text.

## Maintenance Version

Current maintenance version: 2.6.7.11

Plugin download:
https://github.com/at-shift/custom-field-suite-maintenance/archive/refs/heads/main.zip

Versioning follows the upstream 2.6.7 base version with an additional maintenance
suffix. Future stabilization updates should increment the final number, for
example 2.6.7.12, 2.6.7.13, and so on.

## Security and Compatibility Changes

This build includes security and compatibility hardening on top of upstream
2.6.7.

Main changes:

- Removed Loop field `eval()` rendering and replaced it with structured lookup
  logic.
- Normalized Relationship, Term, and User field IDs before saving and querying.
- Sanitized CFS form `post_title` and `post_content` submissions to reduce
  stored XSS risk.
- Added capability checks before CFS form submissions update existing posts.
- Hardened session queries, import/export handling, reverse-relationship
  filtering, and serialized field data loading.
- Escaped admin field output across field settings, tabs, file links, selected
  Relationship / Term / User labels, and generated JSON.
- Stabilized PHP 8.2+ admin edit screen compatibility.
- Fixed TinyMCE code plugin loading for CFS WYSIWYG fields.

## Verification

The maintenance build was locally verified against:

- PHP syntax checks across the plugin.
- Standard CFS save/read behavior.
- All built-in CFS field types:
  Text, Textarea, WYSIWYG, Hyperlink, Date, Color, True / False, Select,
  Relationship, Term, User, File, Loop, and Tab.
- XSS and SQL hardening checks for the modified areas.
- Replacement from the original upstream 2.6.7 plugin to this maintenance build.
- WordPress admin post edit screen compatibility on PHP 8.3.

These checks are local verification only and are not a third-party security
audit.

## Installation

Copy the `custom-field-suite` directory to:

```text
wp-content/plugins/custom-field-suite
```

Then activate Custom Field Suite from the WordPress admin Plugins screen.

When replacing an existing Custom Field Suite 2.6.7 installation, back up the
site files and database first.
