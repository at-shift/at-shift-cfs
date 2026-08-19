<div align="center">
  <img src="assets/plugin-icons/atshift-fields-icon-256.png" width="128" height="128" alt="atshift Fields">
  <h1>atshift Fields</h1>
  <p><strong>Turn WordPress custom fields into clear, practical editing screens.</strong></p>
  <p>
    <a href="https://cfs.at-shift.net/en/">Official Website</a> ·
    <a href="https://cfs.at-shift.net/en/guide/">Setup Guide</a> ·
    <a href="https://cfs.at-shift.net/en/output/">Reference</a> ·
    <a href="https://cfs.at-shift.net/en/examples/">Examples</a> ·
    <a href="https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/">WordPress.org</a> ·
    <a href="https://cfs.at-shift.net/">日本語</a>
  </p>
</div>

## Overview

atshift Fields is a maintained and extended Custom Field Suite-compatible plugin for WordPress sites that need structured content editing without losing existing CFS data or template compatibility.

It preserves the familiar CFS API, including `CFS()->get()`, while adding security hardening, current WordPress and PHP compatibility fixes, native WordPress fields, layout groups, multilingual admin labels, and a clearer Field Group editor.

Formal WordPress.org name: **atshift Fields (Maintenance for Custom Field Suite)**. Short name: **atshift Fields**.

## Features

- CFS-compatible data structures and template APIs
- Text, textarea, WYSIWYG, email, URL, phone, number, date, time, file, gallery, color, code, shortcode, embed, term, relationship, and user fields
- Select, checkbox, true / false, and radio button fields
- Tab, loop, horizontal, accordion, and conditional groups
- Native WordPress fields for post title, content, save / publish, categories, tags, and featured image
- Classic meta box placement for moving compatible meta boxes into field groups
- Role-aware editing controls and edit-screen display settings
- Drag-and-drop field organization with clearer nested group editing
- Validation and safer front-end output guidance
- Bundled translations inherited from the maintained CFS package

## Requirements

- WordPress 5.0 or later
- PHP 7.4 or later

Verified locally with WordPress 7.0, PHP 8.3, and MySQL 8.4. These are verification versions, not strict minimum requirements.

## Installation

1. Install the public release from [WordPress.org](https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/).
2. If replacing the original Custom Field Suite plugin, back up your files and database first.
3. Deactivate the original Custom Field Suite plugin.
4. Activate **atshift Fields** from the WordPress Plugins screen.
5. Open **Field Groups** in the WordPress admin menu and review your field groups.

For setup details, see the [field group setup guide](https://cfs.at-shift.net/en/guide/).

## Compatibility Notes

atshift Fields is an unofficial maintenance build based on Custom Field Suite 2.6.7. It is not an official upstream release by the original author.

Existing CFS field data and common template calls such as `CFS()->get()` are preserved. Always test replacement on a staging site before using it in production.

This package includes local security and compatibility hardening for known CFS 2.6.7 vulnerability classes. WordPress.org publication is not a substitute for an independent third-party security audit.

## Safe Front-End Output

Do not output CFS values directly in theme templates without escaping them. Although this maintenance build hardens plugin-side handling, theme output should still be escaped according to where the value is rendered.

```php
echo esc_html( CFS()->get( 'my_text_field' ) );
```

For field-specific examples and context-appropriate escaping, see the [field output reference](https://cfs.at-shift.net/en/output/).

## Documentation

| Topic | English | 日本語 |
| --- | --- | --- |
| Official website | [cfs.at-shift.net/en](https://cfs.at-shift.net/en/) | [cfs.at-shift.net](https://cfs.at-shift.net/) |
| Adding and arranging fields | [Setup guide](https://cfs.at-shift.net/en/guide/) | [設定ガイド](https://cfs.at-shift.net/guide/) |
| Retrieving and displaying values | [Output reference](https://cfs.at-shift.net/en/output/) | [出力リファレンス](https://cfs.at-shift.net/output/) |
| Implementation examples | [Examples](https://cfs.at-shift.net/en/examples/) | [実装例](https://cfs.at-shift.net/examples/) |
| WordPress.org | [Plugin page](https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/) | [プラグインページ](https://ja.wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/) |

## Related Projects

- [atshift User Profile Fields](https://wordpress.org/plugins/atshift-user-profile-fields/) organizes WordPress user profile fields with a similar field-building experience.
- [atshift Freeform Login](https://wordpress.org/plugins/atshift-freeform-login/) designs the WordPress login screen and provides a matching login form shortcode for site pages.

## Maintenance Release Notes

### 3.0.5.2

- Standardized the plugin metadata links shown on the Plugins screen.

For full release history, see [GitHub Releases](https://github.com/at-shift/at-shift-cfs/releases).

## Attribution

- Original: Custom Field Suite (CFS) / Matt Gibbs
- Original WordPress.org plugin: <https://wordpress.org/plugins/custom-field-suite/>
- Original source: <https://github.com/mgibbs189/custom-field-suite>
- Extension and maintenance: @shift

The original author attribution and GPLv2 license are preserved. This repository is an independent GPLv2 maintenance redistribution based on the upstream 2.6.7 source code.

## License

This maintenance build is distributed under the GNU General Public License version 2 (GPLv2), the same license as the upstream plugin.

See [LICENSE](LICENSE) for the full GPLv2 license text.
