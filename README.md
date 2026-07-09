# at-shift CFS

at-shift CFS is an unofficial maintenance build of the WordPress plugin Custom
Field Suite. It preserves the original data structure and API compatibility
while adding security hardening, current WordPress / PHP compatibility fixes,
and practical field improvements for existing CFS sites.

[Guides and reference](https://cfs.at-shift.net/en/)
[WordPress.org plugin page](https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/)

## Plugin Rename

The plugin previously released as **Custom Field Suite Maintenance** has been
renamed to **at-shift CFS**. This gives the unofficial maintenance build a
distinct identity while preserving its relationship with the original Custom
Field Suite project.

The original data structure and API compatibility are preserved, so existing
Custom Field Suite data can continue to be used.

### at-shift CFS supports the following WordPress custom fields:

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
- Phone
- Email Address
- Number
- URL
- Time
- Code View
- Checkbox
- Radio Button
- Post Categories
- Post Tags
- Featured Image
- Horizontal Group
- Accordion Group
- Conditional Group

For setup instructions, see the [field group setup guide](https://cfs.at-shift.net/en/guide/).
For return values and output examples, see the [field output reference](https://cfs.at-shift.net/en/output/).

Development of Custom Field Suite (CFS) by the original author has been
inactive since August 2024. at-shift CFS is
released under the GPLv2 license in order to address abandoned security
vulnerabilities.

The version numbering for this repository is based on the upstream Custom Field
Suite 2.6.7 release, with an additional maintenance suffix appended to the
version number.

# at-shift CFS

at-shift CFS は、WordPress の投稿編集画面にカスタムフィールドを視覚的に追加できるプラグイン Custom Field Suite のデータ構造と API 互換性を保ちながら、セキュリティ対応、現在の WordPress / PHP 互換性対応、実務向けのフィールド改善を追加した非公式メンテナンス版です。

[ガイド・リファレンス](https://cfs.at-shift.net/)
[WordPress.org プラグインページ](https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/)

## プラグイン名称の変更

これまで **Custom Field Suite Maintenance** として公開していたプラグインを、
**at-shift CFS** に名称変更しました。元版 Custom Field Suite との関係を保ちながら、
非公式のメンテナンス版として独立した名称にするための変更です。

元版のデータ構造と API 互換性を維持しているため、既存の Custom Field Suite の
データを引き続き利用できます。

### at-shift CFS はwordpressで以下のカスタムフィールドが利用できます:

- 単一行テキスト
- テキストエリア
- リッチエディタ
- 日付フォーマット
- カラーピッカー
- ハイパーリンク
- 真/偽・簡易チェックボックス
- セレクト・ドロップダウンメニュー
- ファイルのアップロード
- 関連ポスト選択
- ターム
- ユーザー
- ループ・複製フィールド
- タブ
- 電話番号
- メールアドレス
- 数字
- URL・ハイパーリンクではない
- 時間
- コード
- チェックボックス
- ラジオボタン
- 投稿カテゴリー・WordPress 標準
- 投稿タグ・WordPress 標準
- アイキャッチ画像・WordPress 標準
- 横並びグループ
- アコーディオン・開閉グループ
- 条件分岐グループ

設定方法は[フィールドグループ設定ガイド](https://cfs.at-shift.net/guide/)を、
返り値と出力例は[フィールド別の出力リファレンス](https://cfs.at-shift.net/output/)をご覧ください。

Custom Field Suite (CFS) は、作者による開発が 2024年8月以降停止しており、at-shift CFS は、未修正のセキュリティ上の問題へ対応するため、GPLv2 ライセンスに基づいて公開しています。

このリポジトリのバージョン番号は、上流版 Custom Field Suite 2.6.7 をベースにして、メンテナンス用の末尾番号を追加する形式です。

## Installation (インストール方法)

Current maintenance version: 2.6.7.42.1.6 (現在のメンテナンスバージョン: 2.6.7.42.1.6)

Install the public release from WordPress.org when possible:

可能であれば、公開版は WordPress.org からインストールしてください。

https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/

Before replacing an existing Custom Field Suite installation, back up the
plugin files and database, then deactivate the original plugin.

既存の Custom Field Suite と置き換える前に、プラグインファイルとデータベースをバックアップし、元版を停止してください。

When installing manually, place the plugin directory at (手動で設置する場合は、プラグインフォルダを以下に配置してください):

```text
wp-content/plugins/atshift-fields-maintenance-for-custom-field-suite
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

For field-specific output examples and context-appropriate escaping, see the
at-shift CFS output guide: https://cfs.at-shift.net/en/output/

各フィールドの出力例と、出力先に応じた適切なエスケープ方法については、at-shift CFS の出力ガイドをご覧ください: https://cfs.at-shift.net/output/

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

### 2.6.7.42.1.6

- Published the WordPress.org-compatible maintenance build.
- Removed the previous GitHub release notice / custom update-check behavior from
  the WordPress.org build.
- Matched the text domain to the assigned WordPress.org slug.
- Updated bundled PowerTip and Select2 assets.
- Renamed internal classes, hooks, options, AJAX actions, and the field group
  post type with atshift-prefixed identifiers while preserving the public CFS
  API and existing field data compatibility.
- WordPress.org 互換のメンテナンスビルドを公開しました。
- WordPress.org 版から、従来の GitHub リリース通知および独自更新確認の挙動を削除しました。
- Text Domain を WordPress.org の割り当てスラッグに合わせました。
- 同梱の PowerTip と Select2 を更新しました。
- 公開 CFS API と既存フィールドデータ互換性を維持しながら、内部クラス、フック、オプション、AJAX アクション、フィールドグループ投稿タイプを atshift 接頭辞付き識別子へ変更しました。

### 2.6.7.42.1.2

- Improved file upload previews and remove button alignment on post edit screens.
- Adjusted spacing inside Loop fields.
- 投稿編集画面のファイルアップロードプレビューと削除ボタン位置を調整しました。
- Loop内フィールドの余白を調整しました。

### 2.6.7.42.1.1

- Adjusted post edit screen spacing and typography.
- Improved the at-shift CFS Tools UI and multilingual text.
- 投稿編集画面の余白と文字サイズを調整しました。
- at-shift CFS ツールのUIと多言語テキストを改善しました。

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
- Improved nested structure handling for Tabs, Loops, Horizontal Groups,
  Accordion Groups, and Conditional Groups in the Field Group editor and post
  edit screen.
- Preserved Conditional Group child display settings while editing choices, so
  existing field group definitions are less likely to lose branch assignments.
- Strengthened session, query, import/export, and serialized data handling
  without changing existing data formats.
- Improved compatibility with current WordPress and PHP versions while
  retaining the behavior of existing CFS fields.

このメンテナンスビルド版では、既存のフィールドデータとAPIとの互換性を維持しながら、上流版2.6.7を強化しています。

このメンテナンスビルド版では、報告された既知の脆弱性への対策、様々な攻撃手法への対処、およびバグ修正を行っています。

- 管理画面と公開フォームの処理に、CSRF対策、権限確認、入力値の無害化、出力時のエスケープを追加しました。
- 権限のない利用者に、非公開投稿タイトルやWordPressログイン名が表示されないようにしました。
- 入れ子フィールドを含む必須項目と件数制限をサーバー側でも検証し、不正な入力データの処理を強化しました。
- フィールドグループ編集画面と投稿編集画面で、タブ、Loop（複製グループ）、横並びグループ、アコーディオン、条件分岐の入れ子構造の扱いを改善しました。
- 条件分岐グループの選択肢を編集しても、既存フィールドグループ定義の表示条件設定が失われにくいようにしました。
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
- Verify nested combinations of Tabs, Loops, Horizontal Groups, Accordion
  Groups, and Conditional Groups on field group and post edit screens.
- XSS and SQL hardening checks for the modified areas.
- Replacement from the original upstream 2.6.7 plugin to this maintenance build.
- WordPress 7.0 admin compatibility for Field Group editing, CFS meta boxes,
  WYSIWYG fields, and File media modal handling on PHP 8.3.

- プラグイン全体の PHP 構文チェック。
- 標準的な CFS の保存および読み込み動作。
- すべての組み込み CFS フィールドタイプの動作確認
- 追加した電話番号、メールアドレス、数字、URL、時間、WordPress 標準フィールド、横並びグループの動作確認。
- フィールドグループ編集画面と投稿編集画面で、タブ、Loop（複製グループ）、横並びグループ、アコーディオン、条件分岐の入れ子組み合わせを確認。
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
