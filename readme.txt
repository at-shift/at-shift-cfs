=== Custom Field Suite ===
Contributors: mgibbs189, at-shift
Tags: custom fields, fields, postmeta, relationship, repeater, file upload, categories, tags
Requires at least: 5.0
Tested up to: 7.0
Stable tag: trunk
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Add custom fields to posts, pages, and custom post types.

== Description ==

Custom Field Suite (CFS) is a lightweight WordPress plugin for adding custom fields to posts, pages, and custom post types.

This package is a maintenance build based on the upstream Custom Field Suite 2.6.7 release. It keeps the basic CFS data structure and API compatibility while adding security hardening, admin compatibility fixes, and practical field types for existing CFS sites.

= Things to know =

* This is a maintenance build, not an official upstream release.
* Always back up your files and database before replacing an existing CFS installation.
* Test on a local or staging site before using it on a production site.
* CFS returns stored values; theme templates should still escape output with WordPress escaping functions.
* Added field types are not available in the original upstream CFS 2.6.7 release.

= Field types =

* Text
* Textarea
* WYSIWYG
* Phone
* Email Address
* Hyperlink
* URL
* Number
* Select
* Checkbox
* True / False
* Radio Button
* Date
* Time
* File Upload
* Color
* Post Categories (native WordPress categories)
* Post Tags (native WordPress tags)
* Featured Image (native WordPress featured image)
* Term
* Relationship
* User
* Loop (repeatable fields)
* Tab
* Horizontal Group

= Added features in this maintenance build =

* Security hardening for known CFS 2.6.7 vulnerability classes.
* PHP 8.2+ and WordPress admin compatibility fixes.
* Checkbox and Radio Button fields.
* Phone, Email Address, Number, URL, and Time fields with format validation.
* Time field with hour and minute select menus.
* Native WordPress Post Categories, Post Tags, and Featured Image fields inside CFS field groups.
* Horizontal Group field for arranging multiple fields side by side, with evenly distributed and left-aligned layout options.
* Field Group editor button to add a new field directly below an existing field.
* Field type list ordering grouped by common editing workflows.
* Field Group parent / child synchronization to reduce cases where nested fields disappear from the post edit screen.
* Placement rule warnings for field groups that have no placement rules.
* GitHub release notice on the WordPress Plugins screen when a newer release is available.

= Usage =

* Browse to the "Field Groups" admin menu.
* Create a Field Group containing one or more custom fields.
* Choose where the Field Group should appear, using the Placement Rules box.
* Use the CFS get method in your theme templates to display custom field values.

Example:

`<?php echo esc_html( CFS()->get( 'my_text_field' ) ); ?>`

For rich text fields, use an appropriate HTML sanitizer such as `wp_kses_post()`.

= Horizontal Group notes =

Horizontal Group is a layout field for placing multiple child fields side by side on the post edit screen. On narrow screens, the fields stack vertically.

Horizontal Groups are intended to contain multiple normal fields. Tabs, Loops, and other Horizontal Groups cannot be placed inside a Horizontal Group.

= WordPress native field notes =

Post Categories, Post Tags, and Featured Image edit the native WordPress objects directly. They are not CFS-only post meta fields.

For Post Categories, child category selection can also select parent categories, and removing a parent selection removes its child selections. If all categories are removed, WordPress' default category is restored.

= Security maintenance notes =

This package includes local security and compatibility hardening on top of the upstream 2.6.7 codebase.

The maintenance work addresses known vulnerability classes around Loop field code execution, Term field SQL injection, CFS form title / content stored XSS, and existing post updates through CFS forms without normal edit capability checks.

The changes were verified locally against the built-in CFS field types, added field types, and an upgrade path from the original 2.6.7 codebase. These checks are local verification only and are not a third-party security audit.

= Redistribution and license =

This maintenance build is distributed under the GNU General Public License version 2 (GPLv2), the same license as the upstream plugin. You may use, copy, modify, and redistribute this package, including modified versions, under GPLv2.

When redistributing this package, keep the GPLv2 license notice, preserve the original author attribution, include the source code, and make clear that this is a maintenance build inherited by @shift Yoshiya Tsuchisaka.

= Links =

* Documentation: https://mgibbs189.github.io/custom-field-suite/
* Original Github: https://github.com/mgibbs189/custom-field-suite
* Maintenance Github: https://github.com/at-shift/custom-field-suite-maintenance

== Changelog ==

= 2.6.7.41.2 =
* Fixed an issue where required fields rendered inside Loop rows did not show the Required badge.
* Added and completed bundled translations for recently added admin strings across supported non-Japanese language files.
* Loop 内の必須フィールドに「必須」バッジが表示されない問題を修正しました。
* 追加済みの管理画面文字列について、日本語以外の同梱翻訳ファイルの不足分を補完しました。

= 2.6.7.41.1 =
* Hardened CFS field group saves with explicit post type and capability checks.
* Prevented duplicate field IDs across field groups from causing post edit values to be overwritten.
* CFSフィールドグループ保存時に投稿タイプと権限チェックを明示し、CSRF/認可防御を強化
* フィールドグループ間のフィールドID重複により投稿編集画面の値が上書きされる問題を防止

= 2.6.7.41 =
* タブ・ループ(複製フィールド)・横並びグループで発生した不具合を修正

= 2.6.7.40 =
* Reordered the Field Group field type selector into grouped, workflow-oriented sections.
* Clarified Japanese admin labels, including Phone as `電話番号` and native WordPress fields with `（標準）`.
* Added native WordPress Post Categories, Post Tags, and Featured Image fields inside CFS field groups.
* Improved category behavior: default category fallback, Japanese `未分類` display, parent auto-selection, descendant unchecking, and automatic removal / restoration of the default category.
* Added Horizontal Group alignment options: evenly distributed and left aligned.
* Added warnings when a Horizontal Group has fewer than two child fields.
* Prevented Tabs, Loops, and other Horizontal Groups from being placed inside a Horizontal Group.
* Hardened Field Group editor parent / child synchronization so hidden `parent_id` values follow the visible nested structure.
* Fixed a case where fields could appear nested in the Field Group editor but disappear from the post edit screen because their stored parent ID pointed to an old or missing field.
* Added the "Add new field below" button in the Field Group editor.
* Added placement rule warnings for Field Groups with no placement rules.
* Added a GitHub release notice on the WordPress Plugins screen when a newer release is available.

= 2.6.7.23 =
* Hardened Field Group type switching JavaScript so field type labels are written as text and generated option controls are updated through DOM attributes instead of string-rewritten HTML.
* Replaced an unnecessary jQuery object wrapper in the bundled datepicker parser with array filtering to reduce CodeQL unsafe jQuery plugin findings.
* Verified the updated Field Group field-type switching behavior directly in Safari, including option row insertion, field name replacement, and new field indexing.

= 2.6.7.22 =
* Verified WordPress 7.0 admin compatibility for Field Group editing, CFS meta boxes, WYSIWYG fields, and File media modal handling.
* Moved Field Group admin asset loading to WordPress enqueue APIs.

= 2.6.7.21 =
* Fixed PHP 8.2+ deprecated dynamic property notices in Checkbox, Radio Button, and Select field settings.
* Fixed Field Group Placement Rules layout overflow and select arrow overlap in the WordPress admin screen.
* Updated bundled translation files for added and changed admin strings.

= 2.6.7.20 =
* Removed Loop field `eval()` rendering and replaced it with structured lookup logic.
* Normalized Relationship, Term, and User field IDs before saving and querying.
* Sanitized CFS form `post_title` and `post_content` submissions to reduce stored XSS risk.
* Added capability checks before CFS form submissions update existing posts.
* Hardened session queries, import / export handling, reverse-relationship filtering, and serialized field data loading.
* Escaped admin field output across field settings, tabs, file links, selected Relationship / Term / User labels, and generated JSON.
* Stabilized PHP 8.2+ admin edit screen compatibility and TinyMCE code plugin loading.
* Added Checkbox and Radio Button field types.
* Documented safe front-end output patterns for CFS values.
* Verified replacement from the original 2.6.7 codebase to this maintenance build.

= 2.6.7 =
* Reverted some int casting to prevent errors.
* Refactor coming soon.

= 2.6.6 =
* Even more sanitization.

= 2.6.5 =
* Extra sanitization to prevent XSS via admin-imported field groups.

= 2.6.4 =
* Fixed PHP 8 deprecation notices.

= 2.6.3 =
* Fixed possible placement rules XSS.

= 2.6.2.1 =
* Confirmed WordPress 6.0.1 compatibility.

= 2.6.2 =
* Removed broken links and confirmed WordPress 5.9 compatibility.

= 2.6.1 =
* Fixed PHP 8 warnings.

= 2.6 =
* Moved CFS into the Settings menu.
* Improved relationship fields.
* Improved code modernization and styling.
* Fixed the Posts field group rule Ajax loading.
