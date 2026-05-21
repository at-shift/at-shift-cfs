=== Custom Field Suite ===
Contributors: mgibbs189, at-shift
Tags: custom fields, fields, postmeta, relationship, repeater, file upload
Requires at least: 5.0
Tested up to: 7.0
Stable tag: trunk
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Add custom fields to your post types

== Description ==

Custom Field Suite (CFS) lets you add custom fields to your posts. It's lightweight and battle-tested (there's not much to break).

= Things to know =
* We do not provide support.
* This is a free plugin. We're not selling anything.
* The upstream CFS 2.6.7 release includes 14 field types. This maintenance build adds Checkbox and Radio Button fields to help migration from existing CFS sites.
* If you want all the bells-and-whistles, use ACF.

= Field types =
* Text
* Textarea
* WYSIWYG
* Date
* Color
* True / False
* Select
* Checkbox
* Radio Button
* File Upload
* Relationship
* Term
* User
* Loop (repeatable fields)
* Hyperlink
* Tab (group fields)

= Usage =
* Browse to the "Field Groups" admin menu
* Create a Field Group containing one or more custom fields
* Choose where the Field Group should appear, using the Placement Rules box
* Use the [get](https://mgibbs189.github.io/custom-field-suite/api/get.html) method in your template files to display custom fields

= Security maintenance notes =
This package includes local security and compatibility hardening on top of the upstream 2.6.7 codebase.

This maintenance build is inherited and maintained by @shift Yoshiya Tsuchisaka. The original Custom Field Suite copyright, authorship, and GPLv2 license notices are preserved.

This maintenance build addresses the known 2024 CFS vulnerability classes around Loop field code execution, Term field SQL injection, and CFS form title/content stored XSS. It also keeps the existing public-form behavior where new posts may be created, while requiring normal WordPress edit capabilities before an existing post can be updated.

The changes were verified against the built-in CFS field types, the added Checkbox and Radio Button fields, and an upgrade path from the original 2.6.7 codebase. These checks are local verification only and are not a third-party security audit.

= Redistribution and license =
This maintenance build is distributed under the GNU General Public License version 2 (GPLv2), the same license as the upstream plugin. You may use, copy, modify, and redistribute this package, including modified versions, under GPLv2.

When redistributing this package, keep the GPLv2 license notice, preserve the original author attribution, include the source code, and make clear that this is a maintenance build inherited by @shift Yoshiya Tsuchisaka. This software is provided without warranty, to the extent permitted by law.

= Links =
* [Documentation →](https://mgibbs189.github.io/custom-field-suite/)
* [Github →](https://github.com/mgibbs189/custom-field-suite)

== Changelog ==

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
* Hardened session queries, import/export handling, reverse-relationship filtering, and serialized field data loading.
* Escaped admin field output across field settings, tabs, file links, selected Relationship / Term / User labels, and generated JSON.
* Stabilized PHP 8.2+ admin edit screen compatibility and TinyMCE code plugin loading.
* Added Checkbox and Radio Button field types.
* Documented safe front-end output patterns for CFS values.
* Verified Text, Textarea, WYSIWYG, Hyperlink, Date, Color, True / False, Select, Checkbox, Radio Button, Relationship, Term, User, File, Loop, and Tab field round-trips.
* Verified replacement from the original 2.6.7 codebase to this maintenance build.

= 2.6.7.3 =
* Hardened admin field output escaping and reverse relationship SQL filtering

= 2.6.7.1 =
* Added a capability check before CFS form submissions update existing posts
* Normalized Relationship, Term, and User field IDs before saving and querying

= 2.6.7 =
* Reverted some int casting to prevent errors
* Refactor coming soon (hopefully)

= 2.6.6 =
* Even more sanitization (props wp.org team)

= 2.6.5 =
* Extra sanitization to prevent XSS via admin-imported field groups (props WordFence)

= 2.6.4 =
* Fixed: cleared PHP8 deprecation notices

= 2.6.3 =
* Fixed: possible placement rules XSS (props Patchstack)

= 2.6.2.1 =
* Confirmed 6.0.1 compatibility

= 2.6.2 =
* Removed broken links, confirmed 5.9 compatibility

= 2.6.1 =
* Fixed: PHP8 warnings

= 2.6 =
New: moved CFS into "Settings" menu
Improved: relationship fields now only run 1 query to retrieve related posts
Improved: code modernization
Improved: styling tweaks
Fix: "Posts" field group rule ajax wasn't loading
