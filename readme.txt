=== atshift Fields (Maintenance for Custom Field Suite) ===
Contributors: mgibbs189, atshift
Tags: custom fields, postmeta, relationship, repeater, fields
Requires at least: 5.0
Tested up to: 7.0
Stable tag: 3.0.1
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Add custom fields to posts, pages, and custom post types.

== Description ==


atshift Fields 3.0.1 is a maintenance update for corporate, organizational, non-profit, and other content-heavy WordPress sites that need structured field management, safer editing workflows, and clearer admin screens.

atshift Fields は、企業サイト・団体サイト・NPO サイトなど、構造化された情報を扱う WordPress サイトに向けたメジャーアップデートです。複雑な入力画面を分かりやすく整理し、運用担当者が安全に編集できる管理画面を作りたいケースに適しています。

atshift Fields is an extended version based on Custom Field Suite. It keeps compatibility with the original Custom Field Suite API and saved data while adding native WordPress fields, improved group editing, multilingual labels, and a refined administration UI.

atshift Fields は Custom Field Suite をベースにした拡張版です。上流版の API と保存データの互換性を保ちながら、WordPress 標準フィールドの取り込み、フィールドグループ編集画面、多言語翻訳、管理画面 UI を強化しています。
Custom Field Suite (CFS) is a lightweight WordPress plugin for adding custom fields to posts, pages, and custom post types.

This package is a maintenance build based on the upstream Custom Field Suite 2.6.7 release. It keeps the basic CFS data structure and API compatibility while adding security hardening, admin compatibility fixes, and practical field types for existing CFS sites.

For setup instructions, field value output examples, migration notes, and implementation examples, see the [atshift Fields documentation site](https://cfs.at-shift.net/en/).

= Things to know =

* This is a maintenance build, not an official upstream release.
* Always back up your files and database before replacing an existing CFS installation.
* Deactivate the original Custom Field Suite plugin before activating this maintenance build.
* Test on a local or staging site before using it on a production site.
* CFS returns stored values; theme templates should still escape output with WordPress escaping functions.
* Added field types are not available in the original upstream CFS 2.6.7 release.

= Field types =

* Text
* Textarea
* WYSIWYG
* Tab Group
* Loop (repeatable fields)
* Horizontal Group
* Accordion Group (collapsible group)
* Conditional Group
* Phone Number
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
* Photo Gallery
* Color
* Code View
* Shortcode
* Post Title (native WordPress title)
* Post Content (native WordPress content)
* Save / Publish (native WordPress save and publish controls)
* Post Categories (Standard / Global)
* Post Tags (Native)
* Featured Image (Native)
* Term
* Relationship
* User

= Added features in this maintenance build =

* Security hardening for known CFS 2.6.7 vulnerability classes.
* PHP 8.2+ and WordPress admin compatibility fixes.
* Checkbox and Radio Button fields.
* Phone Number, Email Address, Number, URL, and Time fields with format validation.
* Time field with hour and minute select menus.
* Photo Gallery field with sortable media selection and gallery-friendly output data.
* Native WordPress Post Title, Post Content, Save / Publish, Standard and Shared Taxonomy Post Categories, Post Tags, and Featured Image fields inside CFS field groups.
* Horizontal Group field for arranging multiple fields side by side, with evenly distributed and left-aligned layout options.
* Accordion Group field for organizing child fields into collapsible sections on post edit screens.
* Field Group editor buttons to add a new field directly below an existing field or inside a Loop, Horizontal Group, or Accordion Group.
* Color-coded structure badges and matching range backgrounds for Tabs, Loops, Horizontal Groups, and Accordion Groups.
* Improved drag-and-drop and Tab boundary handling in the Field Group editor.
* Inline validation messages and an error summary that opens the containing Tab, Loop, or Accordion Group and scrolls to the selected invalid field.
* Field type list ordering grouped by common editing workflows.
* Field Group parent / child synchronization to reduce cases where nested fields disappear from the post edit screen.
* Placement rule warnings for field groups that have no placement rules.
* Configurable placeholders for Text, Phone Number, Email Address, Hyperlink, and URL fields.

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

Post Title, Post Content, Save / Publish, Post Categories, Post Tags, and Featured Image edit the native WordPress objects directly. They are not CFS-only post meta fields.

For Post Categories, child category selection can also select parent categories, and removing a parent selection removes its child selections. If all categories are removed, WordPress' default category is restored.

= Security maintenance notes =

This package includes local security and compatibility hardening on top of the upstream 2.6.7 codebase.

The maintenance work addresses known vulnerability classes around Loop field code execution, Term field SQL injection, CFS form title / content stored XSS, and existing post updates through CFS forms without normal edit capability checks.

The changes were verified locally against the built-in CFS field types, added field types, and an upgrade path from the original 2.6.7 codebase. This package has also been adjusted for the WordPress.org plugin directory and published through the official WordPress.org SVN release flow. WordPress.org publication is not a substitute for an independent third-party security audit.

= Redistribution and license =

This maintenance build is distributed under the GNU General Public License version 2 (GPLv2), the same license as the upstream plugin. You may use, copy, modify, and redistribute this package, including modified versions, under GPLv2.

When redistributing this package, keep the GPLv2 license notice, preserve the original author attribution, include the source code, and make clear that this is a maintenance build inherited by @shift Yoshiya Tsuchisaka.

= Documentation and support =

* [Documentation site](https://cfs.at-shift.net/en/)
* [Field group setup guide](https://cfs.at-shift.net/en/guide/)
* [Field value output reference](https://cfs.at-shift.net/en/output/)
* [Implementation examples](https://cfs.at-shift.net/en/examples/)
* [Development repository](https://github.com/at-shift/at-shift-cfs)
* [Original Custom Field Suite source](https://github.com/mgibbs189/custom-field-suite)

== Changelog ==

= 3.0.1 =

**English**
* Added a Shortcode field that returns rendered shortcode output through `CFS()->get()`.
* Added role-based shortcode editing controls. Users without permission do not see the field, and existing shortcode values are preserved when they save other fields.
* Added an Extra Display Setting to force the classic post edit Screen Options layout to 1 column.
* Added Side / Main Extra Display Settings for hiding unnecessary native editor sections with section-level role targeting.

**日本語**
* `CFS()->get()` でショートコード実行済みの出力を返す Shortcode フィールドを追加しました。
* ショートコード入力のロール制御を追加しました。権限のないユーザーにはフィールドを表示せず、他のフィールド保存時も既存のショートコード値を維持します。
* 投稿編集画面のスクリーンオプションを1列レイアウトに固定できる Extra の表示設定を追加しました。
* 不要になりやすい標準セクションを Side / Main に分けて隠せる Extra の表示設定を追加し、セクション単位でロール適用を指定できるようにしました。

= 3.0.0 =

**English**
* Redesigned the Field Group editor UI, including group hierarchy, spacing, drag-and-drop behavior, and responsive layouts.
* Clarified nested structures for Tab, Accordion, Loop, Horizontal, and Conditional groups.
* Added condition-specific drop areas for Conditional Groups so dragged fields can be assigned to each condition more easily.
* Added automatic field-name handling for group fields and native WordPress fields to reduce unnecessary manual input.
* Improved native WordPress field support for Post Title, Post Content, Save / Publish, Post Categories, Post Tags, and Featured Image.
* Refined Save / Publish status, visibility, publish date, draft, publish, and update behavior.
* Improved display and selection behavior for Post Categories (Standard / Global) and Post Tags.
* Improved admin previews and handling for Photo Gallery, File Upload, and image fields.
* Updated the atshift Fields Tool UI with clearer Export / Import / Reset guidance and translations.
* Improved placement rules, extras, tooltips, Select2/Selectize displays, and admin UI details.
* Refined required-field and group-structure warnings so issues are easier to understand.
* Improved metabox compatibility and display stability in both Block Editor and Classic Editor environments.
* Cleaned up internal PHP/CSS/JavaScript while preserving compatibility with the original Custom Field Suite API and saved data.
* Strengthened admin save and AJAX handling, including nonce and capability checks.
* Updated multilingual translations for new fields, settings, warnings, and tool screens.

**日本語**
* フィールドグループ編集画面のUIを全面的に見直し、グループ階層・余白・ドラッグ操作・レスポンシブ表示を改善しました。
* タブ、アコーディオン、Loop、横並び、条件分岐グループの表示を整理し、入れ子構造を分かりやすくしました。
* 条件分岐グループで、条件ごとの追加エリアを表示し、ドラッグしたフィールドを条件に紐付けやすくしました。
* グループ系フィールドやWordPress標準フィールドのフィールド名を自動命名するようにし、不要な手入力を減らしました。
* WordPress標準の投稿タイトル、本文、保存・公開、カテゴリー、タグ、アイキャッチ画像をFields内で扱いやすくしました。
* 保存・公開フィールドのステータス、公開状態、公開日時、下書き保存、公開/更新の表示と挙動を改善しました。
* カテゴリー（標準 / グローバル）とタグフィールドの表示・選択操作を改善しました。
* 写真ギャラリーフィールド、ファイル/画像フィールドの管理画面表示を改善しました。
* atshift Fields Tool のUIを整理し、エクスポート/インポート/リセットの説明と翻訳を追加しました。
* 配置ルール、エクストラ設定、ツールチップ、Select2/Selectize表示など管理画面UIを改善しました。
* 入力必須やグループ構成に関する警告表示を見直し、問題点を把握しやすくしました。
* ブロックエディタ/クラシックエディタ環境でのメタボックス互換性と表示安定性を改善しました。
* 既存のCustom Field Suite APIおよび保存データとの互換性を維持しながら、内部処理とCSS/JavaScriptを整理しました。
* 管理画面の保存・AJAX処理を見直し、nonce/権限チェックなど安全性を強化しました。
* 多言語翻訳を更新し、新しいフィールド名、設定項目、警告文、ツール画面の翻訳漏れを補完しました。
