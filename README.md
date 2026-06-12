# at-shift CFS

at-shift CFS is an unofficial maintenance and extension build of the WordPress
plugin Custom Field Suite. It preserves the original data structure and API
compatibility while adding security hardening, PHP 8 support, and new field
types.

Project documentation: https://cfs.at-shift.net/

## Plugin Rename

The plugin previously released as **Custom Field Suite Maintenance** has been
renamed to **at-shift CFS**. This gives the unofficial maintenance and extension
build a distinct identity while preserving its relationship with the original
Custom Field Suite project.

The original data structure and API compatibility are preserved, so existing
Custom Field Suite data can continue to be used.

### Custom Field Suite (CFS) supports the following WordPress custom fields:

- Single-line text
- Textarea
- WYSIWYG
- Date
- Color
- Hyperlink
- True / False (Checkbox)
- Select
- File Upload
- Relationship
- Term
- User
- Loop
- Tab
- Additional fields introduced by at-shift CFS

For setup instructions and field output examples, see the at-shift CFS
documentation: https://cfs.at-shift.net/en/

Development of Custom Field Suite (CFS) by the original author has been
inactive since August 2024. at-shift CFS is
released under the GPLv2 license in order to address abandoned security
vulnerabilities.

The version numbering for this repository is based on the upstream Custom Field
Suite 2.6.7 release, with an additional maintenance suffix appended to the
version number.

# at-shift CFS

at-shift CFS は、WordPress の投稿編集画面にカスタムフィールドを視覚的に追加できるプラグイン Custom Field Suite のデータ構造と API 互換性を保ちながら、セキュリティ対応、PHP 8 対応、新しいフィールドを追加した非公式メンテナンス・拡張版です。

プロジェクトドキュメント: https://cfs.at-shift.net/

## プラグイン名称の変更

これまで **Custom Field Suite Maintenance** として公開していたプラグインを、
**at-shift CFS** に名称変更しました。元版 Custom Field Suite との関係を保ちながら、
非公式のメンテナンス・拡張版として独立した名称にするための変更です。

元版のデータ構造と API 互換性を維持しているため、既存の Custom Field Suite の
データを引き続き利用できます。

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

設定方法と各フィールドの出力例は、at-shift CFS のドキュメントをご覧ください: https://cfs.at-shift.net/

Custom Field Suite (CFS) は、作者による開発が 2024年8月以降停止しており、at-shift CFS は、未修正のセキュリティ上の問題へ対応するため、GPLv2 ライセンスに基づいて公開しています。

このリポジトリのバージョン番号は、上流版 Custom Field Suite 2.6.7 をベースにして、メンテナンス用の末尾番号を追加する形式です。

## Added Field Types (新たに追加した機能)

This maintenance build adds the following field types and editor features:

このメンテナンスビルド版では、以下のフィールドタイプおよび編集機能を追加しています。上流版に戻した際にはこれらのフィールドや設定は使用することができません。

- Phone (電話番号)
- Email Address (メールアドレス)
- Number (数字)
- URL (Not Hyperlink / ハイパーリンクではない)
- Time (時間)
- Code View (コード)
- Checkbox (チェックボックス)
- Radio Button (ラジオボタン)
- Post Categories (投稿カテゴリー - WordPress 標準)
- Post Tags (投稿タグ - WordPress 標準)
- Featured Image (アイキャッチ画像 - WordPress 標準)
- Horizontal Group (横並びグループ)
- Accordion Group (アコーディオン（開閉グループ）)

These fields are intended to make it easier to migrate existing Custom Field
Suite sites while keeping front-end output flexible for theme developers. The
plugin stores and returns data only; it does not generate fixed front-end HTML
for most fields. Code View returns escaped display markup for showing code
examples on the front end.

これらのフィールドは、既存の Custom Field Suite サイトを移行しやすくしつつ、フロントエンド出力はテーマ開発者が自由にデザインできるようにすることを目的としています。多くのフィールドでは値の保存と取得のみを行います。Code View では、フロントエンドでコード例を表示するためのエスケープ済み表示用マークアップを返します。

Behavior (動作):

- Checkbox fields can define choices one per line and save multiple selected
  values.
- Radio Button fields can define choices one per line and save one selected
  value.
- Phone, Email Address, Number, URL, and Time fields provide format validation
  in the post edit screen.
- Time fields use hour and minute select menus. The configured minute interval
  is reflected in the minute options.
- Code View fields let editors choose a language per entry and render saved
  code as escaped `<pre><code>` output with a language label and optional copy
  button, so the code is displayed for copying and is not executed in place.
- Post Categories, Post Tags, and Featured Image fields edit the native
  WordPress taxonomy / featured image data, not CFS-only post meta.
- Horizontal Group fields arrange multiple child fields side by side in the
  post edit screen, with mobile stacking on narrow screens.
- Accordion Group fields organize child fields into collapsible sections in
  the post edit screen.
- チェックボックスフィールドでは、選択肢を1行ずつ定義し、複数の選択値を保存できます。
- ラジオボタンフィールドでは、選択肢を1行ずつ定義し、1つの選択値を保存できます。
- 電話番号、メールアドレス、数字、URL、時間フィールドは、投稿編集画面で形式チェックを行います。
- 時間フィールドは時・分のセレクトメニュー形式で、設定した分の刻み幅が分の選択肢に反映されます。
- コードフィールドは、投稿編集画面で入力ごとに言語を選択でき、保存したコードを言語ラベル付きのエスケープ済み `<pre><code>` として表示します。任意でコピーボタンを付けられ、コードはその場では実行されず、コピー用として表示されます。
- 投稿カテゴリー、投稿タグ、アイキャッチ画像フィールドは、CFS 独自メタではなく WordPress 標準のタクソノミー / アイキャッチ画像データを編集します。
- 横並びグループは、複数の子フィールドを投稿編集画面で横に並べ、狭い画面では縦並びに切り替わります。
- アコーディオン（開閉グループ）は、複数の子フィールドを投稿編集画面の開閉可能なセクションにまとめます。

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

### Accordion Group (アコーディオン（開閉グループ）)

Accordion Group is a layout field for organizing child fields into a
collapsible section in the post edit screen. The section can be configured to
open by default.

アコーディオン（開閉グループ）は、複数の子フィールドを投稿編集画面の開閉可能なセクションにまとめるためのレイアウト用フィールドです。初期状態で開くように設定できます。

## Installation (インストール方法)

Current maintenance version: 2.6.7.41.22 (現在のメンテナンスバージョン: 2.6.7.41.22)

Plugin download (プラグインのダウンロード): https://github.com/at-shift/at-shift-cfs/archive/refs/heads/main.zip

GitHub ZIP downloads are extracted as `at-shift-cfs-main`. Before replacing an
existing Custom Field Suite installation, back up the plugin files and database,
deactivate the original plugin, then rename the extracted folder to
`at-shift-cfs` and place it at:

GitHub の ZIP ダウンロードは `at-shift-cfs-main` というフォルダ名で展開されます。既存の Custom Field Suite と置き換える前に、プラグインファイルとデータベースをバックアップし、元版を停止してから、展開されたフォルダ名を `at-shift-cfs` に変更して以下の場所に配置してください。

Copy the renamed `at-shift-cfs` directory to (リネーム済みの `at-shift-cfs` フォルダを以下にコピーしてください):

```text
wp-content/plugins/at-shift-cfs
```

After installation, please activate at-shift CFS from the Plugins screen
in the WordPress admin dashboard.

Keep the original upstream v2.6.7 in a separate backup folder so that you can
restore it if this maintenance build does not function correctly in your
environment.

インストール後、WordPress 管理画面のプラグイン画面から at-shift CFS を有効化してください。

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

// Code View output. The field returns escaped display markup with a language
// label and optional copy button. (コード)
$code_view = CFS()->get( 'code_view_field' );
echo wp_kses( $code_view, [
    'div'    => [ 'class' => true ],
    'span'   => [ 'class' => true ],
    'pre'    => [],
    'code'   => [ 'class' => true ],
    'button' => [
        'type'        => true,
        'class'       => true,
        'data-label'  => true,
        'data-copied' => true,
    ],
] );

// Native WordPress category IDs. (投稿カテゴリー - WordPress 標準)
$category_ids = array_map( 'intval', (array) CFS()->get( 'wp_category_field', false, [ 'format' => 'raw' ] ) );

// Native WordPress tag IDs. (投稿タグ - WordPress 標準)
$tag_ids = array_map( 'intval', (array) CFS()->get( 'wp_tag_field', false, [ 'format' => 'raw' ] ) );

// Native featured image ID. (アイキャッチ画像 - WordPress 標準)
$thumbnail_id = (int) CFS()->get( 'featured_image_field', false, [ 'format' => 'raw' ] );

// Horizontal Group and Accordion Group fields are layout-only fields in the
// admin screen and do not need front-end output escaping.
// (横並びグループとアコーディオン（開閉グループ）は管理画面用の表示整理フィールドのため、フロントエンド出力は不要です)
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

### 2.6.7.41.22

- Documented the rename from Custom Field Suite Maintenance to at-shift CFS.
- Clarified that existing Custom Field Suite data and API compatibility are preserved.
- Localized the Date field calendar using the WordPress user language.
- Displayed calendar year and month headings in each locale's standard order.
- Custom Field Suite Maintenance から at-shift CFS への名称変更を明記。
- 既存の Custom Field Suite データと API 互換性を維持することを明記。
- 日付フィールドのカレンダーをWordPressのユーザー言語に合わせて翻訳。
- カレンダーの年月見出しを各言語・地域の標準的な順序で表示。

### 2.6.7.41.21

- Renamed the plugin to at-shift CFS.
- Added configurable placeholders for Text, Phone, Email Address, Hyperlink, and URL fields.
- Added explanatory tooltips to the Default Value and Placeholder settings.
- プラグイン名を at-shift CFS に変更。
- 単一行テキスト、電話番号、メールアドレス、ハイパーリンク、URLにプレースホルダー設定を追加。
- デフォルト値とプレースホルダー設定に説明ツールチップを追加。

### 2.6.7.41.20

- Added a new Accordion Group field for organizing fields into collapsible
  sections.
- Improved the Field Group editor so the badges and ranges for Tabs, Loops,
  Horizontal Groups, and Accordion Groups are easier to identify.
- Other minor fixes.
- 新たにアコーディオン（開閉グループ）を追加しました。
- フィールドグループ設定画面で、タブ・ループ・横並びグループ・アコーディオンのバッジと対象範囲がわかりやすくなるように表示方法を改善しました。
- その他細かな修正

### [2.6.7.41.5](https://github.com/at-shift/at-shift-cfs/releases/tag/v2.6.7.41.5)

- Prevented public CFS forms from exposing private post titles or WordPress
  login names through Relationship and User fields.
- Added server-side validation for required fields and item limits, including
  fields inside Loops and Horizontal Groups.
- Hardened field saves against malformed nested input and strengthened CFS
  session ID generation while retaining the existing session format.
- Consolidated required badge handling and limited Code View assets to pages
  where they are needed.
- 公開CFSフォームの関連ポスト選択・ユーザーフィールドから、非公開投稿タイトルやWordPressログイン名が表示される問題を修正しました。
- ループ・横並びグループ内を含む必須フィールドと件数制限について、サーバー側でも入力チェックを行うようにしました。
- 不正な多重入力による保存エラーを防止し、既存形式を維持したままCFSセッションIDの生成を強化しました。
- 必須バッジの処理を共通化し、コードフィールドのCSS・JavaScriptを必要なページだけで読み込むようにしました。

Past release notes are available on the [Releases page](https://github.com/at-shift/at-shift-cfs/releases).

過去の履歴は[リリースページ](https://github.com/at-shift/at-shift-cfs/releases)をご覧ください。


## Security and Compatibility Changes (脆弱性対策と互換性)

This maintenance build strengthens upstream 2.6.7 while preserving existing
field data and APIs.

This maintenance build addresses reported known vulnerabilities, strengthens
protection against various attack methods, and fixes bugs.

- Added CSRF protection, capability checks, input sanitization, and output
  escaping to administrative and public form processing.
- Prevented public fields from exposing private post titles or WordPress login
  names to unauthorized users.
- Added server-side validation for required fields and item limits, including
  nested fields, and hardened malformed input handling.
- Strengthened session, query, import/export, and serialized data handling
  without changing existing data formats.
- Improved compatibility with current WordPress and PHP versions while
  retaining the behavior of existing CFS fields.

このメンテナンスビルド版では、既存のフィールドデータとAPIとの互換性を維持しながら、上流版2.6.7を強化しています。

このメンテナンスビルド版では、報告された既知の脆弱性への対策、様々な攻撃手法への対処、およびバグ修正を行っています。

- 管理画面と公開フォームの処理に、CSRF対策、権限確認、入力値の無害化、出力時のエスケープを追加しました。
- 権限のない利用者に、非公開投稿タイトルやWordPressログイン名が表示されないようにしました。
- 入れ子フィールドを含む必須項目と件数制限をサーバー側でも検証し、不正な入力データの処理を強化しました。
- 既存のデータ形式を変更せず、セッション、クエリ、インポート／エクスポート、シリアライズデータの処理を強化しました。
- 既存CFSフィールドの動作を維持しながら、現行のWordPressおよびPHPとの互換性を改善しました。

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
