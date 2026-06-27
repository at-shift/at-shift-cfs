# Notice

at-shift CFS is an unofficial maintenance and extension build compatible with Custom Field Suite.

## Original Project

- Name: Custom Field Suite
- Original author: Matt Gibbs
- WordPress.org project: https://wordpress.org/plugins/custom-field-suite/
- Original GitHub repository: https://github.com/mgibbs189/custom-field-suite
- License: GPLv2

## Maintenance Build

- Name: at-shift CFS
- WordPress.org slug: at-shift-cfs
- Maintainer: @shift Yoshiya Tsuchisaka
- GitHub account: https://github.com/at-shift
- Project website: https://cfs.at-shift.net/
- GitHub repository: https://github.com/at-shift/at-shift-cfs
- Base version: Custom Field Suite 2.6.7
- Maintenance version: 2.6.7.42.0.1

This maintenance build preserves the original GPLv2 licensing and author
attribution. It adds local security and compatibility hardening for continued
use where a maintained build is required.

## Summary of Changes

- Security hardening for known 2024 CFS vulnerability classes.
- Additional output escaping and input sanitization.
- Safer handling of Relationship, Term, and User ID values.
- Removal of Loop field `eval()` execution.
- Safer session, import/export, serialized data, and reverse relationship
  handling.
- PHP 8.2+ compatibility fixes for the WordPress admin post edit screen.
- TinyMCE code plugin loading fix for CFS WYSIWYG fields.
- Field Group editor and post edit screen fixes for nested Tabs, Loops,
  Horizontal Groups, Accordion Groups, and Conditional Groups.
- Front-end and admin validation improvements for required and formatted
  fields.

This maintenance build is provided without warranty, to the extent permitted by
law.
