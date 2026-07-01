=== at-shift CFS ===
Contributors: mgibbs189, at-shift
Tags: custom fields, postmeta, relationship, repeater, fields
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
* Accordion Group (collapsible group)

= Added features in this maintenance build =

* Security hardening for known CFS 2.6.7 vulnerability classes.
* PHP 8.2+ and WordPress admin compatibility fixes.
* Checkbox and Radio Button fields.
* Phone, Email Address, Number, URL, and Time fields with format validation.
* Time field with hour and minute select menus.
* Native WordPress Post Categories, Post Tags, and Featured Image fields inside CFS field groups.
* Horizontal Group field for arranging multiple fields side by side, with evenly distributed and left-aligned layout options.
* Accordion Group field for organizing child fields into collapsible sections on post edit screens.
* Field Group editor buttons to add a new field directly below an existing field or inside a Loop, Horizontal Group, or Accordion Group.
* Color-coded structure badges and matching range backgrounds for Tabs, Loops, Horizontal Groups, and Accordion Groups.
* Improved drag-and-drop and Tab boundary handling in the Field Group editor.
* Inline validation messages and an error summary that opens the containing Tab, Loop, or Accordion Group and scrolls to the selected invalid field.
* Field type list ordering grouped by common editing workflows.
* Field Group parent / child synchronization to reduce cases where nested fields disappear from the post edit screen.
* Placement rule warnings for field groups that have no placement rules.
* Configurable placeholders for Text, Phone, Email Address, Hyperlink, and URL fields.

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

Horizontal Groups are intended to contain multiple normal fields. Tabs, Loops, Accordions, Conditional Groups, and other Horizontal Groups cannot be placed inside a Horizontal Group.

= Accordion Group notes =

Accordion Group is a layout field for placing child fields inside a collapsible section on the post edit screen. It can be configured to open by default.

Tabs cannot be placed inside an Accordion Group.

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

* Documentation: https://cfs.at-shift.net/
* Original Github: https://github.com/mgibbs189/custom-field-suite
* Maintenance Github: https://github.com/at-shift/at-shift-cfs

== Changelog ==

= 2.6.7.42.1.2 =
* Improved file upload previews and remove button alignment on post edit screens.
* Adjusted spacing inside Loop fields.
* 投稿編集画面のファイルアップロードプレビューと削除ボタン位置を調整しました。
* Loop内フィールドの余白を調整しました。

= 2.6.7.42.1.1 =
* Adjusted post edit screen spacing and typography.
* Improved the at-shift CFS Tools UI and multilingual text.
* 投稿編集画面の余白と文字サイズを調整しました。
* at-shift CFS ツールのUIと多言語テキストを改善しました。

= 2.6.7.42.1.0 =
* Fixed issues and improved field drag-and-drop movement in the Field Group editor.
* フィールドグループ設定でのフィールドのドラッグによる移動に関連する不具合と改善を行いました。

= 2.6.7.42.0.4 =
* Adjusted post edit screen typography for Tabs, field headings, descriptions, group headings, and Loop rows.
* Improved description text contrast on post edit screens.
* Localized JavaScript confirmation messages for resetting at-shift CFS data and removing Loop rows.
* Added duplicate field name warnings in the Field Group editor and blocked saving until duplicate field names are resolved.
* 投稿編集画面のタブ、フィールド見出し、説明文、グループ見出し、Loop行などの文字サイズを調整しました。
* 投稿編集画面の説明文の文字色を調整し、視認性を改善しました。
* at-shift CFSデータのリセット確認とLoop行削除確認のJavaScript確認メッセージを翻訳対応しました。
* フィールドグループ編集画面でフィールド名が重複している場合に警告を表示し、重複解消まで保存を止めるようにしました。

= 2.6.7.42.0.3 =
* Improved Field Group editor drag-and-drop when moving fields into or out of groups inside Tabs.
* Fixed issues related to Tab range handling.
* Optimized the Field Label and Field Type display.
* フィールドグループ編集画面で、タブ内グループへフィールドを入れる／外へ出す際のドラッグ＆ドロップを改善しました。
* タブ範囲に関連する不具合を修正しました。
* フィールドラベル、フィールド種の表示を最適化しました。

= 2.6.7.42 =
* Kept submitted front-end form values and displayed validation errors in the same form when server-side validation fails.
* Added server-side format validation for phone, email, URL, number, date, time, and color fields.
* Renamed the field group export/import tools to "at-shift CFS Tools".
* Fixed Field Group editor hierarchy indicators for Horizontal Group, Conditional Group, and Accordion Group fields inside Tabs.
* Allowed Loop (repeatable group) fields inside Conditional Group and Accordion Group fields.
* Fixed post edit screen tab switching when multiple tabs share the same field name.
* Treated stale outside-tab settings on fields between Tabs as part of the preceding Tab.
* Fixed Conditional Group fields inside Loop rows so they render correctly on the post edit screen.
* Fixed post edit screen validation for Conditional Group fields inside Loop rows.
* Preserved child field display conditions when editing Conditional Group choices.
* Improved admin asset cache busting so updates are reflected when replacing files within the same plugin version.
* サーバー側バリデーションに失敗した場合、フロントエンドフォームの入力値を保持し、同じフォーム内へエラーを表示するようにしました。
* 電話番号、メール、URL、数値、日付、時刻、カラーの形式をサーバー側でも検証するようにしました。
* フィールドグループの書き出し／読み込みのためのツールの名称を「at-shift CFSツール」に修正しました。
* フィールドグループ編集画面で、タブ内の横並び・条件分岐・アコーディオンの各グループの階層表現を修正しました。
* 条件分岐・アコーディオンのグループにLoop（複製グループ）を入れられるように修正しました。
* 投稿編集画面で、同じフィールド名のタブがある場合に別タブの内容が表示される問題を修正しました。
* タブ間のフィールドに古い外部タブ設定が残っている場合でも、前のタブ内として表示するように修正しました。
* Loop（複製グループ）内の条件分岐グループが投稿編集画面で正しく表示されるように修正しました。
* Loop（複製グループ）内の条件分岐グループでも、投稿画面のバリデーションが正しく動作するように修正しました。
* 条件分岐グループの選択肢を編集しても、子フィールドに設定した表示条件値が空に戻らないように修正しました。
* 同一バージョンの差し替え時にも、管理画面用のCFSアセット更新が反映されやすいように修正しました。

= 2.6.7.41.22.3 =
* Updated taxonomy placement rules immediately when categories are selected or cleared in the post editor.
* Resolved an issue where inactive taxonomy field groups hid the content editor or native WordPress fields.
* 投稿編集画面でカテゴリーを選択・解除した直後に、分類の配置ルールを反映するようにしました。
* 未選択の分類フィールドグループが、コンテンツエディターやWordPress標準フィールドを非表示にする問題を解決しました。

= 2.6.7.41.22.1 =
* Kept the Conditional Group selected-branch indicator line while using a white field background.
* 条件分岐グループの選択中ブランチで、左ラインは残しつつフィールド背景を白にしました。

= 2.6.7.41.22 =
* Localized the Date field calendar using the WordPress user language.
* Displayed calendar year and month headings in each locale's standard order.
* 日付フィールドのカレンダーをWordPressのユーザー言語に合わせて翻訳しました。
* カレンダーの年月見出しを各言語・地域の標準的な順序で表示するようにしました。

= 2.6.7.41.21 =
* Renamed the plugin to at-shift CFS.
* Added configurable placeholders for Text, Phone, Email Address, Hyperlink, and URL fields.
* Added tooltips explaining Default Value and Placeholder settings.
* プラグイン名を at-shift CFS に変更しました。
* 単一行テキスト、電話番号、メールアドレス、ハイパーリンク、URLにプレースホルダー設定を追加しました。
* デフォルト値とプレースホルダー設定に説明ツールチップを追加しました。

= 2.6.7.41.20 =
* Added a new Accordion Group field for organizing fields into collapsible sections.
* Improved the Field Group editor so the badges and ranges for Tabs, Loops, Horizontal Groups, and Accordion Groups are easier to identify.
* Other minor fixes.
* 新たにアコーディオン（開閉グループ）を追加しました。
* フィールドグループ設定画面で、タブ・ループ・横並びグループ・アコーディオンのバッジと対象範囲がわかりやすくなるように表示方法を改善しました。
* その他細かな修正

= 2.6.7.41.5 =
* Prevented public CFS forms from exposing private post titles or WordPress login names through Relationship and User fields.
* Added server-side validation for required fields and item limits, including fields inside Loops and Horizontal Groups.
* Hardened field saves against malformed nested input and strengthened CFS session ID generation while retaining the existing session format.
* Consolidated required badge handling and limited Code View assets to pages where they are needed.
* 公開CFSフォームの関連ポスト選択・ユーザーフィールドから、非公開投稿タイトルやWordPressログイン名が表示される問題を修正しました。
* ループ・横並びグループ内を含む必須フィールドと件数制限について、サーバー側でも入力チェックを行うようにしました。
* 不正な多重入力による保存エラーを防止し、既存形式を維持したままCFSセッションIDの生成を強化しました。
* 必須バッジの処理を共通化し、コードフィールドのCSS・JavaScriptを必要なページだけで読み込むようにしました。

= 2.6.7.41.4 =
* Improved drag-and-drop behavior when moving fields into Loop (repeatable field) and Horizontal Group fields in the Field Group editor.
* Improved the "Add new field below" button in the Field Group editor so fields can be added directly inside Loop (repeatable field) and Horizontal Group fields.
* フィールドグループ内の「ループ(複製フィールド)」と「横並びグループ」へフィールドをドラッグ移動しやすくしました。
* フィールドグループ内の「この下に新規フィールドを追加」ボタンを改善し、「ループ(複製フィールド)」と「横並びグループ」内へ直接フィールドを追加できるようにしました。

= 2.6.7.41.3 =
* Added a Code View field for showing examples such as HTML code on the front end.
* Fixed display issues after saving in some field inputs.
* フロントエンドでHTMLコードなどの例を記載できるコードフィールドを追加しました。
* フィールドの一部で保存後に表示が崩れる問題を修正しました。

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
