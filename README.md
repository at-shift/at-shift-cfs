# Custom Field Suite (CFS) Maintenance Build

Custom Field Suite (CFS) is a WordPress plugin for adding custom fields to
posts, pages, and custom post types.

This maintenance build addresses known vulnerabilities identified up to Custom
Field Suite 2.6.7. It is also intended to reduce the effort required to migrate
websites built with Custom Field Suite to alternative plugins. In other words,
simply replacing the original Custom Field Suite (CFS) with Custom Field Suite
Maintenance Build can help mitigate security risks such as XSS attacks.

### Custom Field Suite (CFS) supports the following WordPress custom fields:

- Single-line text
- Textarea
- WYSIWYG
- Date
- Color
- Hyprerlink
- True / False (Chackbox)
- Select
- File Upload
- Relationship
- Term
- User
- Loop
- Tab
- And... the various fields that were newly added in the maintenance build.

For more information about Custom Field Suite (CFS), see the documentation
created by the original author: https://mgibbs189.github.io/custom-field-suite/

Development of Custom Field Suite (CFS) by the original author has been
inactive since August 2024. This "Custom Field Suite Maintenance Build" is
released under the GPLv2 license in order to address abandoned security
vulnerabilities.

The version numbering for this repository is based on the upstream Custom Field
Suite 2.6.7 release, with an additional maintenance suffix appended to the
version number.

# Custom Field Suite (CFS) メンテナンスビルド版

Custom Field Suite (CFS) は、投稿、固定ページ、カスタム投稿タイプにカスタムフィールドを追加するための WordPress プラグインです。
このメンテナンスビルド版では、Custom Field Suite 2.6.7 までに確認されている既知の脆弱性へ対応しています。また、Custom Field Suite を利用して構築された Web サイトを、他の代替プラグインへ移行する際の負担を軽減することを目的としています。つまり、Custom Field Suite Maintenance Build は、オリジナルの Custom Field Suite (CFS) を置き換えるだけで、XSS などのセキュリティリスクを軽減できます。

### Custom Field Suite (CFS) はwordpressで以下のカスタムフィールドが利用できます:

- 単一行テキスト
- テキストエリア
- リッチエディタ
- 日付フォーマット
- カラーピッカー
- ハイパーリンク
- 真/偽 (簡易チェックボックス)
- セレクト (ドロップダウンメニュー)
- ファイルのアップロード
- 関連ポスト選択
- ターム
- ユーザー
- ループ (複製フィールド)
- タブ
- そして、メンテナンスビルド版で新たに追加した各種フィールド

Custom Field Suite (CFS) の機能については、元作者作成のドキュメントをご覧ください: https://mgibbs189.github.io/custom-field-suite/

Custom Field Suite (CFS) は、作者による開発が 2024年8月以降停止しており、この「Custom Field Suite Maintenance Build」は、放置されたセキュリティ脆弱性へ対応するため、GPLv2 ライセンスに基づいて公開しています。

このリポジトリのバージョン番号は、上流版 Custom Field Suite 2.6.7 をベースにして、メンテナンス用の末尾番号を追加する形式です。

## Added Field Types (新たに追加した機能)

This maintenance build adds the following field types and editor features:

このメンテナンスビルド版では、以下のフィールドタイプおよび編集機能を追加しています。上流版に戻した際にはこれらのフィールドや設定は使用することができません。

- Phone (電話番号)
- Email Address (メールアドレス)
- Number (数字)
- URL (Not Hyperlink / ハイパーリンクではない)
- Time (時間)
- Checkbox (チェックボックス)
- Radio Button (ラジオボタン)
- Post Categories (投稿カテゴリー - WordPress 標準)
- Post Tags (投稿タグ - WordPress 標準)
- Featured Image (アイキャッチ画像 - WordPress 標準)
- Horizontal Group (横並びグループ)

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
- Phone, Email Address, Number, URL, and Time fields provide format validation
  in the post edit screen.
- Time fields use hour and minute select menus. The configured minute interval
  is reflected in the minute options.
- Post Categories, Post Tags, and Featured Image fields edit the native
  WordPress taxonomy / featured image data, not CFS-only post meta.
- Horizontal Group fields arrange multiple child fields side by side in the
  post edit screen, with mobile stacking on narrow screens.
- チェックボックスフィールドでは、選択肢を1行ずつ定義し、複数の選択値を保存できます。
- ラジオボタンフィールドでは、選択肢を1行ずつ定義し、1つの選択値を保存できます。
- 電話番号、メールアドレス、数字、URL、時間フィールドは、投稿編集画面で形式チェックを行います。
- 時間フィールドは時・分のセレクトメニュー形式で、設定した分の刻み幅が分の選択肢に反映されます。
- 投稿カテゴリー、投稿タグ、アイキャッチ画像フィールドは、CFS 独自メタではなく WordPress 標準のタクソノミー / アイキャッチ画像データを編集します。
- 横並びグループは、複数の子フィールドを投稿編集画面で横に並べ、狭い画面では縦並びに切り替わります。

### Native WordPress fields (WordPress 標準フィールド)

Post Categories, Post Tags, and Featured Image fields are intended for moving
standard WordPress sidebar controls into a CFS field group. These fields update
the same WordPress data as the standard editor UI.

投稿カテゴリー、投稿タグ、アイキャッチ画像フィールドは、WordPress 標準のサイドバー入力欄を CFS フィールドグループ内へ移動するためのフィールドです。これらは標準の編集画面と同じ WordPress データを更新します。

Category behavior (カテゴリーの動作):

- Selecting a child or grandchild category automatically selects its parent
  categories.
- Unchecking a parent category also unchecks its child and grandchild
  categories.
- When another category is selected, the default category is automatically
  unchecked.

- 子カテゴリーや孫カテゴリーを選択すると、親カテゴリーも自動で選択します。
- 親カテゴリーのチェックを外すと、配下の子カテゴリー・孫カテゴリーのチェックも外します。
- 他のカテゴリーを選択すると、デフォルトカテゴリーのチェックは自動で外れます。

### Horizontal Group (横並びグループ)

Horizontal Group is a layout field for arranging multiple child fields side by
side in the post edit screen. It is useful for related fields such as first /
last name, phone / email, or date / time combinations.

横並びグループは、複数の子フィールドを投稿編集画面で横に並べるためのレイアウト用フィールドです。姓 / 名、電話番号 / メールアドレス、日付 / 時間など、関連する入力欄をまとめたい場合に利用できます。

Behavior (動作):

- Fields are displayed side by side on desktop and stacked on narrow screens.
- Alignment can be set to evenly distributed or left aligned.
- A warning is shown when a Horizontal Group has fewer than two child fields.
- Tabs, Loops, and other Horizontal Groups cannot be placed inside a Horizontal
  Group.

- デスクトップでは横並び、狭い画面では縦並びで表示します。
- 配置は均等配置または左寄せを選択できます。
- 横並びグループ内の子フィールドが2つ未満の場合は警告を表示します。
- 横並びグループの中に、タブ、ループ、別の横並びグループは入れられません。

## Installation (インストール方法)

Current maintenance version: 2.6.7.41.2 (現在のメンテナンスバージョン: 2.6.7.41.2)

Plugin download (プラグインのダウンロード): https://github.com/at-shift/custom-field-suite-maintenance/archive/refs/heads/main.zip

GitHub ZIP downloads are extracted as `custom-field-suite-maintenance-main`.
Before installing or replacing an existing WordPress plugin, save your original
plugin copy locally, then rename the extracted folder to `custom-field-suite`
and place it at:

GitHub の ZIP ダウンロードは `custom-field-suite-maintenance-main` というフォルダ名で展開されます。WordPress プラグインとしてインストールまたは既存版と置き換える前に、オリジナル版のプラグインをローカルに保存してから、展開されたフォルダ名を `custom-field-suite` に変更し、以下の場所に配置してください。

Copy the renamed `custom-field-suite` directory to (リネーム済みの `custom-field-suite` フォルダを以下にコピーしてください):

```text
wp-content/plugins/custom-field-suite
```

After installation, please activate Custom Field Suite from the Plugins screen
in the WordPress admin dashboard.

Keep the original upstream v2.6.7 in a separate backup folder so that you can
restore it if this maintenance build does not function correctly in your
environment.

インストール後、WordPress 管理画面のプラグイン画面から Custom Field Suite を有効化してください。

また、このメンテナンスビルド版が利用環境で正しく機能しない場合に備えて、オリジナルの上流版 v2.6.7 を別のバックアップフォルダに保存し、必要に応じていつでも元のバージョンへ戻せる状態にしておくことを推奨します。

```text
Verified environment (確認環境):

- WordPress: 7.0
- PHP: 8.3.31
- MySQL: 8.4.9

The versions above describe the local verification environment and are not
strict minimum requirements. In addition to local checks, replacement and
compatibility have been verified on several live sites, but operation in other
environments is not guaranteed unless separately verified.

上記はローカルで動作確認を行った環境であり、厳密な最低動作要件を示すものではありません。
ローカル検証に加えて、数サイトの実運用環境でも置き換えと互換性を確認していますが、その他の環境での動作は、個別に検証されていない限り保証されません。
```

## Safe Front-End Output (フロントエンドでの安全な出力方法)

Do not output CFS values directly in theme templates without escaping them.
Although this maintenance build hardens plugin-side handling, front-end output
in theme files such as `single.php` should still be escaped according to where
the value is rendered for better protection against code injection.

テーマテンプレート内では、CFS の値をエスケープせず直接出力することは避けてください。このメンテナンスビルド版ではプラグイン側の処理を強化していますが、`single.php` などのテーマファイルでのフロントエンド出力は、コードインジェクション対策として、表示する場所に応じてエスケープする方がより安全です。

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
// Plain text. (単一行テキスト)
echo esc_html( CFS()->get( 'text_field' ) );

// Multi-line plain text. (テキストエリア)
echo nl2br( esc_html( CFS()->get( 'textarea_field' ) ) );

// WYSIWYG or other content where limited post HTML should be allowed. (リッチエディタ)
echo wp_kses_post( CFS()->get( 'wysiwyg_field' ) );

// Hyperlink output when the field is configured to return a PHP array. (ハイパーリンク)
$link = (array) CFS()->get( 'hyperlink_field' );
if ( ! empty( $link['url'] ) ) {
    printf(
        '<a href="%s" target="%s">%s</a>',
        esc_url( $link['url'] ),
        esc_attr( $link['target'] ?? '_self' ),
        esc_html( $link['text'] ?? $link['url'] )
    );
}

// Select output. CFS returns an array even for single-select fields. (セレクト - ドロップダウン選択メニュー)
$select_values = (array) CFS()->get( 'select_field' );
foreach ( $select_values as $select_value ) {
    echo esc_html( $select_value );
}

// True / False output. (真/偽 - 簡易チェックボックス)
if ( CFS()->get( 'true_false_field' ) ) {
    echo esc_html__( 'Yes', 'your-theme-textdomain' );
}

// Date output. (日付フォーマット)
echo esc_html( CFS()->get( 'date_field' ) );

// File URL output. (ファイルのアップロード)
$file_id  = (int) CFS()->get( 'file_field', false, [ 'format' => 'raw' ] );
$file_url = wp_get_attachment_url( $file_id );
if ( $file_url ) {
    echo esc_url( $file_url );
}

// Color value used in an HTML attribute. (カラーピッカー)
$color = sanitize_hex_color( CFS()->get( 'color_field' ) );
if ( $color ) {
    echo '<div style="background-color: ' . esc_attr( $color ) . ';"></div>';
}

// Term IDs. (ターム)
$term_ids = array_map( 'intval', (array) CFS()->get( 'term_field', false, [ 'format' => 'raw' ] ) );

// Relationship IDs. (関連ポスト選択)
$relationship_ids = array_map( 'intval', (array) CFS()->get( 'relationship_field', false, [ 'format' => 'raw' ] ) );

// User IDs. (ユーザ)
$user_ids = array_map( 'intval', (array) CFS()->get( 'user_field', false, [ 'format' => 'raw' ] ) );

// Loop output. Escape each sub-field according to its own output context. (ループ - 複製フィールド)
$rows = (array) CFS()->get( 'loop_field' );
foreach ( $rows as $row ) {
    echo esc_html( $row['text_sub_field'] ?? '' );
}

// Tab fields are layout-only fields in the admin screen and do not need
// front-end output escaping.
// (タブは管理画面用の表示整理フィールドのため、フロントエンド出力は不要です)
```

### メンテナンスビルド版で追加したフィールドの出力方法

Maintenance-build added fields output examples:

```php
// Phone output. (電話番号)
echo esc_html( CFS()->get( 'phone_field' ) );

// Email output. (メールアドレス)
$email = sanitize_email( CFS()->get( 'email_field' ) );
if ( '' !== $email ) {
    echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
}

// URL output. (URL)
$url = CFS()->get( 'url_field' );
if ( '' !== $url ) {
    echo '<a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a>';
}

// Number output. (数字)
echo esc_html( CFS()->get( 'number_field' ) );

// Checkbox output. (チェックボックス)
$checkbox_values = (array) CFS()->get( 'checkbox_field' );
foreach ( $checkbox_values as $checkbox_value ) {
    echo esc_html( $checkbox_value );
}

// Radio Button output. (ラジオボタン)
echo esc_html( CFS()->get( 'radio_field' ) );

// Time output. (時間)
echo esc_html( CFS()->get( 'time_field' ) );

// Native WordPress category IDs. (投稿カテゴリー - WordPress 標準)
$category_ids = array_map( 'intval', (array) CFS()->get( 'wp_category_field', false, [ 'format' => 'raw' ] ) );

// Native WordPress tag IDs. (投稿タグ - WordPress 標準)
$tag_ids = array_map( 'intval', (array) CFS()->get( 'wp_tag_field', false, [ 'format' => 'raw' ] ) );

// Native featured image ID. (アイキャッチ画像 - WordPress 標準)
$thumbnail_id = (int) CFS()->get( 'featured_image_field', false, [ 'format' => 'raw' ] );

// Horizontal Group fields are layout-only fields in the admin screen and do not
// need front-end output escaping.
// (横並びグループは管理画面用の表示整理フィールドのため、フロントエンド出力は不要です)
```

The correct escaping function depends on the output context: use `esc_html()`
for visible text, `wp_kses_post()` for trusted rich text, `esc_url()` for URLs,
`esc_attr()` for HTML attributes, and integer casting for IDs.

適切なエスケープ関数は出力先によって異なります。画面上の文字には `esc_html()`、信頼できるリッチテキストには `wp_kses_post()`、URL には `esc_url()`、HTML 属性には `esc_attr()`、ID には整数化を使用してください。

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

フィールドグループ内の設定「コンテンツエディターを隠す」は Classic Editor (`postdivrich`) を対象としており、Gutenberg の本文ブロックエリアは非表示になりません。

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

## Maintenance Release Notes (メンテナンスリリース履歴)

### 2.6.7.41.2

- Fixed an issue where required fields rendered inside Loop rows did not show
  the `Required` badge.
- Added and completed bundled translations for recently added admin strings
  across supported non-Japanese language files.
- Loop 内の必須フィールドに「必須」バッジが表示されない問題を修正しました。
- 追加済みの管理画面文字列について、日本語以外の同梱翻訳ファイルの不足分を補完しました。

### 2.6.7.41.1

- Hardened CFS field group saves with explicit post type and capability checks.
- Prevented duplicate field IDs across field groups from causing post edit values to be overwritten.
- CFSフィールドグループ保存時に投稿タイプと権限チェックを明示し、CSRF/認可防御を強化
- フィールドグループ間のフィールドID重複により投稿編集画面の値が上書きされる問題を防止

### 2.6.7.41

- Fixed issues related to Tabs, Loops (Repeatable Fields), and Horizontal Groups.
- タブ・ループ(複製フィールド)・横並びグループで発生した不具合を修正

### 2.6.7.40

- Added Phone, Email Address, Number, URL, and Time fields with post edit
  screen format validation.
- Added hour and minute select menus for the Time field, including configurable
  minute intervals.
- Added an empty `Please select...` option to the top of existing single Select
  field dropdown lists.
- Added native WordPress Post Categories, Post Tags, and Featured Image fields
  that can be placed inside CFS field groups.
- Improved native category behavior: default category fallback, Japanese
  `未分類` display, parent auto-selection, descendant unchecking, and automatic
  default category removal / restoration.
- Added Horizontal Group for arranging multiple child fields side by side in
  the post edit screen.
- Added Horizontal Group alignment options: evenly distributed and left aligned.
- Added warnings when a Horizontal Group has fewer than two child fields.
- Prevented Tabs, Loops, and other Horizontal Groups from being placed inside a
  Horizontal Group.
- Added an "Add new field below" button to the Field Group editor.
- Reordered the Field Group field type selector into grouped,
  workflow-oriented sections.
- Hardened Field Group editor parent / child synchronization so hidden
  `parent_id` values follow the visible nested structure.
- Fixed cases where newly nested fields could trigger undefined `id` warnings
  or disappear from the post edit screen after saving.
- Added a visible `Required` badge beside required field labels in the post edit
  screen.
- Added placement rule warnings for Field Groups with no placement rules.
- Added a GitHub release notice on the WordPress Plugins screen when a newer
  release is available.
- Updated Japanese translation files for added and changed admin strings.

- 電話番号、メールアドレス、数字、URL、時間フィールドを追加し、投稿編集画面で形式チェックを行うようにしました。
- 時間フィールドに時・分のセレクトメニューを追加し、設定した分の刻み幅が選択肢へ反映されるようにしました。
- 既存のセレクト（ドロップダウン）リストの先頭に「選択してください...」を追加しました。
- WordPress 標準の投稿カテゴリー、投稿タグ、アイキャッチ画像を CFS フィールドグループ内に配置できるフィールドを追加しました。
- 投稿カテゴリーの動作を改善し、デフォルトカテゴリー復元、`未分類` 表示、親カテゴリーの自動選択、親を外した際の子孫カテゴリー解除、デフォルトカテゴリーの自動解除 / 復元に対応しました。
- 複数の子フィールドを投稿編集画面で横に並べる横並びグループを追加しました。
- 横並びグループに均等配置と左寄せの配置設定を追加しました。
- 横並びグループ内の子フィールドが2つ未満の場合に警告を表示するようにしました。
- 横並びグループの中に、タブ、ループ、別の横並びグループを入れられないようにしました。
- Field Group 編集画面に「このフィールドの下に新規フィールドを追加」ボタンを追加しました。
- Field Group のフィールドタイプ選択メニューを、用途ごとに探しやすい順番へ整理しました。
- Field Group 編集画面の親子関係同期を強化し、hidden の `parent_id` が画面上の入れ子構造に追従するようにしました。
- 新規フィールドをグループへ追加して保存した際に、未定義の `id` 警告が出たり、投稿編集画面から子フィールドが消える可能性があるケースを修正しました。
- 入力必須フィールドに「必須」バッジを表示するようにしました。
- 配置ルールが空の Field Group に警告を表示するようにしました。
- WordPress プラグイン一覧画面で、GitHub に新しいリリースがある場合のみ通知を表示するようにしました。
- 追加・変更された管理画面文字列に対応する日本語翻訳ファイルを更新しました。

### 2.6.7.23

- Hardened Field Group type switching JavaScript so field type labels are
  written as text and generated option controls are updated through DOM
  attributes instead of string-rewritten HTML.
- Replaced an unnecessary jQuery object wrapper in the bundled datepicker
  parser with array filtering to reduce CodeQL unsafe jQuery plugin findings.
- Verified the updated Field Group field-type switching behavior directly in
  Safari, including option row insertion, field name replacement, and new field
  indexing.

- フィールドタイプ切り替え時の管理画面 JavaScript を強化し、フィールドタイプ表示はテキストとして書き込み、生成されるオプション入力の更新は文字列 HTML の再解釈ではなく DOM 属性の更新で行うようにしました。
- 同梱 datepicker のパーサー内で不要な jQuery オブジェクト化を配列フィルタリングに置き換え、CodeQL の unsafe jQuery plugin 検出を低減しました。
- Safari で、更新後の Field Group フィールドタイプ切り替え動作を直接検証しました。オプション行の挿入、フィールド名の置換、新規フィールド index の更新を確認しています。

### 2.6.7.22

- Verified WordPress 7.0 admin compatibility for Field Group editing, CFS meta
  boxes, WYSIWYG fields, and File media modal handling.
- Moved Field Group admin asset loading to WordPress enqueue APIs.

- WordPress 7.0 管理画面での Field Group 編集、CFS メタボックス、WYSIWYG フィールド、File メディアモーダル処理の互換性を検証しました。
- Field Group 管理画面のアセット読み込みを WordPress の enqueue API に移行しました。

## Security and Compatibility Changes (脆弱性対策と互換性)

This maintenance build includes security and compatibility hardening on top of upstream
2.6.7.

このメンテナンスビルド版には、上流版 2.6.7 に対するセキュリティおよび互換性の強化が含まれています。

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
- Fixed PHP 8.2+ deprecated dynamic property notices in Checkbox, Radio Button,
  and Select field settings.
- Fixed Field Group Placement Rules layout overflow in the WordPress admin
  screen.
- Moved Field Group admin asset loading to WordPress enqueue APIs for better
  WordPress 7.0 admin compatibility.
- Updated bundled translation files for the added and changed admin strings.
- Fixed TinyMCE code plugin loading for CFS WYSIWYG fields.

- Loop フィールドの `eval()` による描画を削除し、構造化された参照ロジックに置き換えました。
- Relationship、Term、User フィールドの ID を、保存およびクエリ前に正規化するようにしました。
- CFS フォームの `post_title` と `post_content` 送信値をサニタイズし、保存型 XSS リスクを低減しました。
- CFS フォーム送信で既存投稿を更新する前に、権限チェックを行うようにしました。
- セッション SQL、インポート/エクスポート処理、逆引き Relationship のフィルタリング、シリアライズ済みフィールドデータの読み込みを強化しました。
- フィールド設定、タブ、ファイルリンク、選択済み Relationship / Term / User ラベル、生成 JSON など、管理画面の出力エスケープを強化しました。
- PHP 8.2 以降の管理画面投稿編集ページとの互換性を安定化しました。
- Checkbox、Radio Button、Select フィールド設定で発生していた PHP 8.2 以降の動的プロパティ Deprecated 通知を修正しました。
- WordPress 管理画面内の Field Group Placement Rules レイアウトが横にはみ出す問題を修正しました。
- WordPress 7.0 の管理画面互換性を高めるため、Field Group 管理画面のアセット読み込みを WordPress の enqueue API に移行しました。
- 追加・変更された管理画面文字列に対応する同梱翻訳ファイルを更新しました。
- CFS WYSIWYG フィールドで TinyMCE code プラグインが正しく読み込まれるように修正しました。

## Verification (検証)

The maintenance build was locally verified against:

このメンテナンスビルド版では、ローカル環境で以下を検証しました。

- PHP syntax checks across the plugin.
- Standard CFS save/read behavior.
- Verify the functionality of all built-in CFS field types.
- Verify the functionality of the added Phone, Email Address, Number, URL,
  Time, native WordPress fields, and Horizontal Group fields.
- XSS and SQL hardening checks for the modified areas.
- Replacement from the original upstream 2.6.7 plugin to this maintenance build.
- WordPress 7.0 admin compatibility for Field Group editing, CFS meta boxes,
  WYSIWYG fields, and File media modal handling on PHP 8.3.

- プラグイン全体の PHP 構文チェック。
- 標準的な CFS の保存および読み込み動作。
- すべての組み込み CFS フィールドタイプの動作確認
- 追加した電話番号、メールアドレス、数字、URL、時間、WordPress 標準フィールド、横並びグループの動作確認。
- 修正箇所に対する XSS および SQL 強化の確認。
- 元の上流版 2.6.7 プラグインからこのメンテナンスビルド版への置き換え確認。
- PHP 8.3 上での WordPress 7.0 管理画面、Field Group 編集、CFS メタボックス、WYSIWYG フィールド、File メディアモーダルの互換性確認。

After A.I.-assisted or manual local verification, replacement from Custom Field
Suite 2.6.7, compatibility, and feature preservation have been confirmed on
several sites. However, this is not a third-party security audit.

A.I.または手動によるローカル検証後に、数サイトでの Custom Field Suite 2.6.7 からの置き換え、互換性の確認、機能維持確認を実証済み。ただし、第三者によるセキュリティ監査はおこなっていません。

## Attribution (帰属表示)

- Original plugin (元プラグイン): Custom Field Suite (CFS)
- Original author (元作者): Matt Gibbs
- Original project (元プロジェクト): https://wordpress.org/plugins/custom-field-suite/
- Original source (元ソースコード): https://github.com/mgibbs189/custom-field-suite
- Maintenance build (メンテナンスビルド版): @shift Yoshiya Tsuchisaka
- GitHub account (GitHub アカウント): https://github.com/at-shift

The original author attribution and GPLv2 license are preserved. This repository
is a maintenance build, not an official upstream release by the original author.

This repository is not a GitHub fork of the upstream repository. It is an
independent GPLv2 maintenance redistribution based on the upstream 2.6.7 source
code.

元作者の表記および GPLv2 ライセンス表記は保持しています。このリポジトリはメンテナンスビルド版であり、元作者による公式の上流リリースではありません。

このリポジトリは GitHub 上の fork ではありません。上流版 2.6.7 のソースコードをベースに、GPLv2 に基づいて独立して再配布しているメンテナンス版です。

## License (公開ライセンス)

This maintenance build is distributed under the GNU General Public License
version 2 (GPLv2), the same license as the upstream plugin.

You may use, copy, modify, and redistribute this package, including modified
versions, under the terms of GPLv2. When redistributing, keep the GPLv2 license
notice, preserve the original author attribution, include the source code, and
make clear that this is a maintenance build.

このメンテナンスビルド版は、上流プラグインと同じ GNU General Public License version 2 (GPLv2) のもとで配布されます。

GPLv2 の条件に従い、このパッケージおよび改変版を使用、複製、改変、再配布できます。再配布する場合は、GPLv2 のライセンス表記、元作者の表記、ソースコードを保持し、これがメンテナンスビルド版であることを明示してください。

See [LICENSE](LICENSE) for the full GPLv2 license text. GPLv2 の全文は [LICENSE](LICENSE) を参照してください。
