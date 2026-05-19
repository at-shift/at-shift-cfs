# Custom Field Suite (CFS) Maintenance Build

Custom Field Suite (CFS) is a WordPress plugin for adding custom fields to
posts, pages, and custom post types.

Development of Custom Field Suite (CFS) by the original author has been
discontinued. This "Custom Field Suite Maintenance Build" is released under the
GPLv2 license in order to address previously unaddressed security
vulnerabilities.

This repository contains a security and compatibility maintenance build of
Custom Field Suite, based on the upstream Custom Field Suite 2.6.7 codebase.

This maintenance build fixes known vulnerabilities up to Custom Field Suite
2.6.7, whose upstream updates have been left inactive since 2024. It is intended
to reduce the effort required to replace websites built with Custom Field Suite
with alternative plugins.

Custom Field Suite is a WordPress plugin for adding custom fields to posts,
pages, and custom post types. In other words, simply replacing the original
Custom Field Suite (CFS), which is no longer maintained and contains known
vulnerabilities, with Custom Field Suite Maintenance Build allows you to
mitigate security risks such as XSS attacks.

Custom Field Suite (CFS) は、投稿、固定ページ、カスタム投稿タイプにカスタムフィールドを追加するための WordPress プラグインです。

Custom Field Suite (CFS) は作者による開発が終了しており、この「Custom Field Suite Maintenance Build」は、放置されたセキュリティ脆弱性へ対応するため、GPLv2 ライセンスに基づいて公開されています。

このリポジトリには、上流版 Custom Field Suite 2.6.7 をベースにした、セキュリティおよび互換性維持のためのメンテナンスビルドが含まれています。

このメンテナンスビルドでは、2024年以降アップデートが停止している Custom Field Suite 2.6.7 までに確認されている既知の脆弱性へ対応しています。また、Custom Field Suite を利用して構築された Web サイトを、他の代替プラグインへ移行する際の負担を軽減することを目的としています。つまり、Custom Field Suite Maintenance Build は、すでに開発が終了している脆弱性を含むオリジナルの Custom Field Suite (CFS) を置き換えるだけで、XSS などのセキュリティリスクを軽減できます。

## Attribution (帰属表示)

- Original plugin (元プラグイン): Custom Field Suite (CFS)
- Original author (元作者): Matt Gibbs
- Original project (元プロジェクト): https://wordpress.org/plugins/custom-field-suite/
- Original source (元ソースコード): https://github.com/mgibbs189/custom-field-suite
- Maintenance build (メンテナンスビルド): @shift Yoshiya Tsuchisaka
- GitHub account (GitHub アカウント): https://github.com/at-shift

The original author attribution and GPLv2 license are preserved. This repository
is a maintenance build, not an official upstream release by the original author.

This repository is not a GitHub fork of the upstream repository. It is an
independent GPLv2 maintenance redistribution based on the upstream 2.6.7 source
code.

元作者の表記および GPLv2 ライセンス表記は保持しています。このリポジトリはメンテナンスビルドであり、元作者による公式の上流リリースではありません。

このリポジトリは GitHub 上の fork ではありません。上流版 2.6.7 のソースコードをベースに、GPLv2 に基づいて独立して再配布しているメンテナンス版です。

## License (公開ライセンス)

This maintenance build is distributed under the GNU General Public License
version 2 (GPLv2), the same license as the upstream plugin.

You may use, copy, modify, and redistribute this package, including modified
versions, under the terms of GPLv2. When redistributing, keep the GPLv2 license
notice, preserve the original author attribution, include the source code, and
make clear that this is a maintenance build.

このメンテナンスビルドは、上流プラグインと同じ GNU General Public License version 2 (GPLv2) のもとで配布されます。

GPLv2 の条件に従い、このパッケージおよび改変版を使用、複製、改変、再配布できます。再配布する場合は、GPLv2 のライセンス表記、元作者の表記、ソースコードを保持し、これがメンテナンスビルドであることを明示してください。

See [LICENSE](LICENSE) for the full GPLv2 license text. GPLv2 の全文は [LICENSE](LICENSE) を参照してください。

## Maintenance Version (このバージョンについて)

Current maintenance version: 2.6.7.20 (現在のメンテナンスバージョン: 2.6.7.20)

Plugin download (プラグインのダウンロード):
https://github.com/at-shift/custom-field-suite-maintenance/archive/refs/heads/main.zip

GitHub ZIP downloads are extracted as `custom-field-suite-maintenance-main`.
Before installing or replacing an existing WordPress plugin, rename the
extracted folder to `custom-field-suite` and place it at:

Do not overwrite your original plugin copy without first saving it locally. Keep
the original upstream version in a separate backup folder so that you can
restore it if this maintenance build does not work correctly in your
environment.

GitHub の ZIP ダウンロードは `custom-field-suite-maintenance-main` というフォルダ名で展開されます。WordPress プラグインとしてインストールまたは既存版と置き換える前に、展開されたフォルダ名を `custom-field-suite` に変更し、以下の場所に配置してください。

オリジナル版のプラグインを、事前に保存せず上書きしないでください。メンテナンス版が利用環境で正しく機能しない場合に備えて、オリジナルの上流版を別のバックアップフォルダにローカル保存し、いつでも復元できる状態にしてください。

```text
wp-content/plugins/custom-field-suite
```

Versioning follows the upstream 2.6.7 base version with an additional
maintenance suffix. Future stabilization updates should increment the final
number, for example 2.6.7.21, 2.6.7.22, and so on.

バージョン番号は、上流版 2.6.7 をベースにメンテナンス用の末尾番号を追加する形式です。今後の安定化更新では、2.6.7.21、2.6.7.22 のように最後の番号を増やします。

## Security and Compatibility Changes (脆弱性対策と互換性)

This build includes security and compatibility hardening on top of upstream
2.6.7.

このビルドには、上流版 2.6.7 に対するセキュリティおよび互換性の強化が含まれています。

Main changes (脆弱性に対する対策):

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

- Loop フィールドの `eval()` による描画を削除し、構造化された参照ロジックに置き換えました。
- Relationship、Term、User フィールドの ID を、保存およびクエリ前に正規化するようにしました。
- CFS フォームの `post_title` と `post_content` 送信値をサニタイズし、保存型 XSS リスクを低減しました。
- CFS フォーム送信で既存投稿を更新する前に、権限チェックを行うようにしました。
- セッション SQL、インポート/エクスポート処理、逆引き Relationship のフィルタリング、シリアライズ済みフィールドデータの読み込みを強化しました。
- フィールド設定、タブ、ファイルリンク、選択済み Relationship / Term / User ラベル、生成 JSON など、管理画面の出力エスケープを強化しました。
- PHP 8.2 以降の管理画面投稿編集ページとの互換性を安定化しました。
- CFS WYSIWYG フィールドで TinyMCE code プラグインが正しく読み込まれるように修正しました。

## Safe Front-End Output (フロントエンドでの安全な出力方法)

Do not output CFS values directly in theme templates without escaping them.
Although this maintenance build hardens plugin-side handling, front-end output
in theme files such as `single.php` should still be escaped according to where
the value is rendered for better protection against code injection.

テーマテンプレート内では、CFS の値をエスケープせず直接出力することは避けてください。このメンテナンスビルドではプラグイン側の処理を強化していますが、`single.php` などのテーマファイルでのフロントエンド出力は、コードインジェクション対策として、表示する場所に応じてエスケープする方がより安全です。

Existing template code that directly echoes CFS values will continue to display
values in the same way as before:

既存のテンプレートで CFS の値を直接 `echo` しているコードは、これまでと同様に表示できます。

```php
echo CFS()->get( 'text_field' );
```

This is kept for compatibility with existing Custom Field Suite sites. However,
direct output also means that any HTML saved in that field may be rendered by
the browser. If an attacker or low-privileged editor can save malicious content
into a field, direct output can lead to stored XSS, unexpected HTML injection,
layout breakage, malicious links, or JavaScript execution in a visitor's
browser.

これは既存の Custom Field Suite サイトとの互換性を維持するためです。ただし、直接出力するということは、そのフィールドに保存された HTML がブラウザでそのまま解釈される可能性があるということでもあります。攻撃者や低権限の編集者が悪意ある内容をフィールドに保存できる場合、直接出力は保存型 XSS、意図しない HTML 挿入、レイアウト崩れ、不正リンク、訪問者のブラウザ上での JavaScript 実行につながる可能性があります。

It is safer to avoid direct output like this when possible (以下のような直接出力はできれば避ける方が安全です):

```php
echo CFS()->get( 'text_field' );
```

For new templates, and when maintaining existing templates, use
context-appropriate escaping instead:

新規テンプレートを作成する場合や既存テンプレートを保守する場合は、代わりに、出力先に応じたエスケープを行ってください。

```php
// Plain text.
echo esc_html( CFS()->get( 'text_field' ) );

// Multi-line plain text.
echo nl2br( esc_html( CFS()->get( 'textarea_field' ) ) );

// WYSIWYG or other content where limited post HTML should be allowed.
echo wp_kses_post( CFS()->get( 'wysiwyg_field' ) );

// URL output.
echo esc_url( CFS()->get( 'url_field' ) );

// HTML attribute output.
echo esc_attr( CFS()->get( 'attribute_field' ) );

// Numeric IDs, including File, Relationship, Term, and User raw values.
$ids = array_map( 'intval', (array) CFS()->get( 'relationship_field', false, [ 'format' => 'raw' ] ) );
```

The correct escaping function depends on the output context: use `esc_html()`
for visible text, `wp_kses_post()` for trusted rich text, `esc_url()` for URLs,
`esc_attr()` for HTML attributes, and integer casting for IDs.

適切なエスケープ関数は出力先によって異なります。画面上の文字には `esc_html()`、信頼できるリッチテキストには `wp_kses_post()`、URL には `esc_url()`、HTML 属性には `esc_attr()`、ID には整数化を使用してください。

## Added Field Types (新たに追加した機能)

This maintenance build adds two list-based field types:

このメンテナンスビルドでは、以下のリスト系フィールドタイプを追加しています。

- Checkbox (チェックボックス)
- Radio Button (ラジオボタン)

These fields are intended to make it easier to migrate existing Custom Field
Suite sites while keeping front-end output flexible for theme developers. The
plugin stores and returns data only; it does not generate fixed front-end HTML
for these fields.

これらのフィールドは、既存の Custom Field Suite サイトを移行しやすくしつつ、フロントエンド出力はテーマ開発者が自由にデザインできるようにすることを目的としています。プラグイン側では値の保存と取得を行い、固定のフロントエンド HTML は出力しません。

Behavior (動作):

- Checkbox fields can define choices one per line and save multiple selected
  values.
- Radio Button fields can define choices one per line and save one selected
  value.
- Both fields support the existing required-field validation setting.
- Both fields use the existing Notes field for editor-facing descriptions.
- Both fields work inside Loop fields.
- Post edit screen choices are displayed with flexible horizontal wrapping so
  short and long labels can be mixed.

- チェックボックスフィールドでは、選択肢を1行ずつ定義し、複数の選択値を保存できます。
- ラジオボタンフィールドでは、選択肢を1行ずつ定義し、1つの選択値を保存できます。
- どちらのフィールドも既存の入力必須設定に対応します。
- どちらのフィールドも、編集者向けの説明には既存の概要欄（Notes）を使用します。
- どちらのフィールドも Loop フィールド内で利用できます。
- 投稿編集画面では、短い選択肢と長い選択肢が混在しても扱いやすいよう、横並びと折り返し表示に対応しています。

Checkbox fields return an array of selected values. Escape each value before
output:

チェックボックスフィールドは、選択された値の配列を返します。出力する前に、それぞれの値をエスケープしてください。

```php
$values = (array) CFS()->get( 'checkbox_field' );

foreach ( $values as $value ) {
    echo '<span class="cfs-checkbox-value">' . esc_html( $value ) . '</span>';
}
```

Radio Button fields return a single selected value:

ラジオボタンフィールドは、選択された単一の値を返します。

```php
$value = CFS()->get( 'radio_field' );

if ( '' !== $value ) {
    echo esc_html( $value );
}
```

## Verification (検証)

The maintenance build was locally verified against:

このメンテナンスビルドでは、ローカル環境で以下を検証しました。

- PHP syntax checks across the plugin.
- Standard CFS save/read behavior.
- All built-in CFS field types:
  Text, Textarea, WYSIWYG, Hyperlink, Date, Color, True / False, Select,
  Checkbox, Radio Button, Relationship, Term, User, File, Loop, and Tab.
- XSS and SQL hardening checks for the modified areas.
- Replacement from the original upstream 2.6.7 plugin to this maintenance build.
- WordPress admin post edit screen compatibility on PHP 8.3.

- プラグイン全体の PHP 構文チェック。
- 標準的な CFS の保存および読み込み動作。
- すべての組み込み CFS フィールドタイプ:
  Text、Textarea、WYSIWYG、Hyperlink、Date、Color、True / False、Select、Checkbox、Radio Button、Relationship、Term、User、File、Loop、Tab。
- 修正箇所に対する XSS および SQL 強化の確認。
- 元の上流版 2.6.7 プラグインからこのメンテナンスビルドへの置き換え確認。
- PHP 8.3 上での WordPress 管理画面投稿編集ページの互換性確認。

These checks are local verification only and are not a third-party security
audit.

これらはローカル検証であり、第三者によるセキュリティ監査ではありません。

## Installation (インストール方法)

Copy the renamed `custom-field-suite` directory to:

リネーム済みの `custom-field-suite` フォルダを以下にコピーしてください。

```text
wp-content/plugins/custom-field-suite
```

Then activate Custom Field Suite from the WordPress admin Plugins screen.

When replacing an existing Custom Field Suite 2.6.7 installation, back up the
site files and database first.

Also keep a local copy of the original upstream version before installing this
maintenance build, so that you can restore the original version at any time if
this maintenance build does not work correctly in your environment.

その後、WordPress 管理画面のプラグイン画面から Custom Field Suite を有効化してください。
既存の Custom Field Suite 2.6.7 を置き換える場合は、事前にサイトファイルとデータベースをバックアップしてください。

また、このメンテナンス版が利用環境で正しく機能しない場合に備えて、インストール前にオリジナルの上流版をローカルに保存しておいてください。必要に応じて、いつでも元のバージョンへ戻せる状態にしておくことを推奨します。

## Gutenberg / Block Editor Compatibility (Gutenberg（ブロックエディタ）対応について)

Custom Field Suite (CFS) supports the WordPress block editor through classic
meta box compatibility.

The "Hide the content editor" display setting only hides the classic editor
(`postdivrich`) and does not remove the Gutenberg block editor content area.

This is a limitation of the WordPress block editor integration and not a bug in
Custom Field Suite (CFS).

If you are not using the Classic Editor plugin, you may need to disable editor
support manually with `remove_post_type_support()` in your theme's
`functions.php`.

Custom Field Suite (CFS) は、WordPress のクラシックメタボックス互換機能を通じて Gutenberg をサポートしています。

フィールドグループ内の設定「Hide the content editor」は Classic Editor (`postdivrich`) を対象としており、Gutenberg の本文ブロックエリアは非表示になりません。

これは Custom Field Suite (CFS) の不具合ではなく、WordPress ブロックエディタとの互換仕様によるものです。

そのため、「Classic Editor」プラグインを利用していない場合は、必要に応じて `functions.php` で直接 `remove_post_type_support()` によりコンテンツエディターサポートを無効化してください。

Add the following code to `functions.php` as needed (`functions.php` に必要に応じて以下のコードを追加してください):

Posts (投稿):

```php
add_action( 'init', function() {
    remove_post_type_support( 'post', 'editor' );
} );
```

Pages (固定ページ):

```php
add_action( 'init', function() {
    remove_post_type_support( 'page', 'editor' );
} );
```

Custom Post Types (カスタム投稿タイプ):

If you register the custom post type yourself, the usual approach is to remove
`editor` from the post type's `supports` setting.

カスタム投稿タイプを自分で登録している場合は、通常はその投稿タイプの `supports` 設定から `editor` を外します。

```php
register_post_type( 'your_post_type', [
    'label'    => 'Your Post Type',
    'public'   => true,
    'supports' => [ 'title', 'thumbnail' ],
] );
```

If the post type is already registered, or is registered by another theme or
plugin, you can remove editor support later with `remove_post_type_support()`.

投稿タイプがすでに登録済みの場合や、他のテーマ・プラグインによって登録されている場合は、後から `remove_post_type_support()` で editor サポートを外すこともできます。

```php
add_action( 'init', function() {
    remove_post_type_support( 'your_post_type', 'editor' );
} );
```

Example (例):

```php
add_action( 'init', function() {
    remove_post_type_support( 'information', 'editor' );
} );
```
