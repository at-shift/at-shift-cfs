<div align="center">
  <img src="assets/plugin-icons/atshift-fields-icon-256.png" width="128" height="128" alt="atshift Fields">
  <h1>atshift Fields</h1>
  <p><strong>Turn WordPress custom fields into clear, practical editing screens.</strong></p>
  <p>
    <a href="https://plugins.at-shift.net/en/fields/">Official Website</a> ·
    <a href="https://plugins.at-shift.net/en/fields/guide/">Setup Guide</a> ·
    <a href="https://plugins.at-shift.net/en/fields/output/">Reference</a> ·
    <a href="https://plugins.at-shift.net/en/fields/api/">API</a> ·
    <a href="https://plugins.at-shift.net/en/fields/examples/">Examples</a> ·
    <a href="https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/">WordPress.org</a> ·
    <a href="https://plugins.at-shift.net/fields/">日本語</a>
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
- Administration-only User fields that are omitted from front-end forms and ignored on public submission
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

For setup details, see the [field group setup guide](https://plugins.at-shift.net/en/fields/guide/).

## Compatibility Notes

atshift Fields is an unofficial maintenance build based on Custom Field Suite 2.6.7. It is not an official upstream release by the original author.

Existing CFS field data and common template calls such as `CFS()->get()` are preserved. Always test replacement on a staging site before using it in production.

This package includes local security and compatibility hardening for known CFS 2.6.7 vulnerability classes. WordPress.org publication is not a substitute for an independent third-party security audit.

## Safe Front-End Output

Do not output CFS values directly in theme templates without escaping them. Although this maintenance build hardens plugin-side handling, theme output should still be escaped according to where the value is rendered.

```php
echo esc_html( CFS()->get( 'my_text_field' ) );
```

For field-specific examples and context-appropriate escaping, see the [field output reference](https://plugins.at-shift.net/en/fields/output/).

## Documentation

| Topic | English | 日本語 |
| --- | --- | --- |
| Official website | [plugins.at-shift.net/en/fields](https://plugins.at-shift.net/en/fields/) | [plugins.at-shift.net/fields](https://plugins.at-shift.net/fields/) |
| Adding and arranging fields | [Setup guide](https://plugins.at-shift.net/en/fields/guide/) | [設定ガイド](https://plugins.at-shift.net/fields/guide/) |
| Retrieving and displaying values | [Output reference](https://plugins.at-shift.net/en/fields/output/) | [出力リファレンス](https://plugins.at-shift.net/fields/output/) |
| Compatibility API | [API reference](https://plugins.at-shift.net/en/fields/api/) | [APIリファレンス](https://plugins.at-shift.net/fields/api/) |
| Implementation examples | [Examples](https://plugins.at-shift.net/en/fields/examples/) | [実装例](https://plugins.at-shift.net/fields/examples/) |
| WordPress.org | [Plugin page](https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/) | [プラグインページ](https://ja.wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/) |

## Related Projects

- [atshift Feed Builder](https://wordpress.org/plugins/atshift-feed-builder/) builds flexible feeds from WordPress content and supported atshift data sources.
- [atshift User Profile Fields](https://wordpress.org/plugins/atshift-user-profile-fields/) organizes WordPress user profile fields with a similar field-building experience.
- [atshift Freeform Login](https://wordpress.org/plugins/atshift-freeform-login/) designs the WordPress login screen and provides a matching login form shortcode for site pages.

## Maintenance Release Notes

### 3.0.6.3

- Made User fields unavailable in front-end forms and protected existing values from forged public submissions.
- Prevented users without publishing capability from making scheduled posts public by changing the publication date.

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
