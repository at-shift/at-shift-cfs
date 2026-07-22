# atshift Fields

atshift Fields is also an excellent choice for building WordPress websites that manage structured content, such as corporate and organizational sites. It helps organize complex editing screens into a clear, intuitive interface, making it easier for content managers to edit information safely and consistently.

atshift Fields is an extended version based on Custom Field Suite. It keeps compatibility with the original Custom Field Suite API and saved data while adding native WordPress fields, improved group editing, multilingual labels, and a refined administration UI.

atshift Fields is an unofficial maintenance build of the WordPress plugin Custom
Field Suite. It preserves the original data structure and API compatibility
while adding security hardening, current WordPress / PHP compatibility fixes,
and practical field improvements for existing CFS sites.

Formal WordPress.org name: **atshift Fields (Maintenance for Custom Field Suite)**.
Short name: **atshift Fields**.

[Guides and reference](https://cfs.at-shift.net/en/) |
[WordPress.org plugin page](https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/)

## Plugin Rename

The plugin previously released as **Custom Field Suite Maintenance / at-shift CFS** has been
renamed to **atshift Fields**. This gives the unofficial maintenance build a
distinct identity while preserving its relationship with the original Custom
Field Suite project.

The original data structure and API compatibility are preserved, so existing
Custom Field Suite data can continue to be used.

### atshift Fields supports the following WordPress custom fields:

- Single-line text
- Textarea
- WYSIWYG
- Tab Group
- Loop (repeatable fields)
- Horizontal Group
- Accordion Group (collapsible group)
- Conditional Group
- Phone Number
- Email Address
- Hyperlink
- URL
- Number
- Select
- Checkbox
- True / False
- Radio Button
- Date
- Time
- File Upload
- Photo Gallery
- Color
- Code View
- Shortcode
- Classic Meta Box Placement
- Post Title (native WordPress title)
- Post Content (native WordPress content)
- Save / Publish (native WordPress save and publish controls)
- Post Categories (Standard / Global)
- Post Tags (native WordPress tags)
- Featured Image (native WordPress featured image)
- Term
- Relationship
- User

For setup instructions, see the [field group setup guide](https://cfs.at-shift.net/en/guide/).
For return values and output examples, see the [field output reference](https://cfs.at-shift.net/en/output/).

Development of Custom Field Suite (CFS) by the original author has been
inactive since August 2024. atshift Fields is
released under the GPLv2 license in order to address abandoned security
vulnerabilities.

The version numbering for this repository is based on the upstream Custom Field
Suite 2.6.7 release, with an additional maintenance suffix appended to the
version number.

# atshift Fields

atshift Fields は、企業サイト・団体サイトサイトなど、構造化された情報を扱う WordPress サイト構築にも最適プラグインです。複雑な入力画面を分かりやすく整理し、運用担当者が安全に編集できる管理画面を作りたいケースに適しています。

atshift Fields は Custom Field Suite をベースにした拡張版です。上流版の API と保存データの互換性を保ちながら、WordPress 標準フィールドの取り込み、フィールドグループ編集画面、多言語翻訳、管理画面 UI を強化しています。

atshift Fields は、WordPress の投稿編集画面にカスタムフィールドを視覚的に追加できるプラグイン Custom Field Suite のデータ構造と API 互換性を保ちながら、セキュリティ対応、現在の WordPress / PHP 互換性対応、実務向けのフィールド改善を追加した非公式メンテナンス版です。

WordPress.org 上の正式名: **atshift Fields (Maintenance for Custom Field Suite)**。
短縮名: **atshift Fields**。

[ガイド・リファレンス](https://cfs.at-shift.net/) |
[WordPress.org プラグインページ](https://wordpress.org/plugins/atshift-fields-maintenance-for-custom-field-suite/)

## プラグイン名称の変更

これまで **Custom Field Suite Maintenance / at-shift CFS** として公開していたプラグインを、
**atshift Fields** に名称変更しました。元版 Custom Field Suite との関係を保ちながら、
非公式のメンテナンス版として独立した名称にするための変更です。

元版のデータ構造と API 互換性を維持しているため、既存の Custom Field Suite の
データを引き続き利用できます。

### atshift Fields はwordpressで以下のカスタムフィールドが利用できます:

- 単一行テキスト
- テキストエリア
- リッチエディタ
- タブグループ
- ループ・複製フィールド
- 横並びグループ
- アコーディオン・開閉グループ
- 条件分岐グループ
- 電話番号
- メールアドレス
- ハイパーリンク
- URL(ハイパーリンクではない)
- 数字
- セレクト・ドロップダウンメニュー
- チェックボックス
- 真/偽・簡易チェックボックス
- ラジオボタン
- 日付フォーマット
- 時間
- ファイルのアップロード
- 写真ギャラリー
- カラーピッカー
- コード
- ショートコード
- メタボックス配置（Classic）
- 投稿タイトル（WordPress 標準）
- 本文（WordPress 標準）
- 保存・公開（WordPress 標準）
- 投稿カテゴリー (標準 / グローバル)
- 投稿タグ（WordPress 標準）
- アイキャッチ画像（WordPress 標準）
- ターム
- 関連ポスト選択
- ユーザー

設定方法は[フィールドグループ設定ガイド](https://cfs.at-shift.net/guide/)を、
返り値と出力例は[フィールド別の出力リファレンス](https://cfs.at-shift.net/output/)をご覧ください。

Custom Field Suite (CFS) は、作者による開発が 2024年8月以降停止しており、atshift Fields は、未修正のセキュリティ上の問題へ対応するため、GPLv2 ライセンスに基づいて公開しています。

このリポジトリのバージョン番号は、上流版 Custom Field Suite 2.6.7 をベースにして、メンテナンス用の末尾番号を追加する形式です。

## Installation (インストール方法)

Current maintenance version: 3.0.2.5 (現在のメンテナンスバージョン: 3.0.2.5)

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

After installation, please activate atshift Fields from the Plugins screen
in the WordPress admin dashboard.

Keep the original upstream v2.6.7 in a separate backup folder so that you can
restore it if this maintenance build does not function correctly in your
environment.

インストール後、WordPress 管理画面のプラグイン画面から atshift Fields を有効化してください。

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
atshift Fields output guide: https://cfs.at-shift.net/en/output/

各フィールドの出力例と、出力先に応じた適切なエスケープ方法については、atshift Fields の出力ガイドをご覧ください: https://cfs.at-shift.net/output/

## Gutenberg / Block Editor Compatibility (Gutenberg（ブロックエディタ）対応について)

Custom Field Suite (CFS) supports the WordPress block editor through classic
meta box compatibility.

The "Hide the content editor" display setting hides the classic editor content
area. For matching posts that would use Gutenberg / the block editor, atshift
Fields disables the block editor and shows the compatible classic editing
screen with the content editor hidden.

Custom Field Suite (CFS) は、WordPress のクラシックメタボックス互換機能を通じて Gutenberg をサポートしています。

フィールドグループ内の設定「コンテンツエディターを隠す」は Classic Editor (`postdivrich`) の本文入力エリアを隠します。Gutenberg / ブロックエディタを使用する投稿にフィールドグループが一致する場合は、atshift Fields がブロックエディタを無効化し、互換性のあるクラシック編集画面で本文入力エリアを隠します。

To disable the content editor for an entire post type, you can still use
`functions.php` as needed.

投稿タイプ全体で本文エディタを無効化したい場合は、必要に応じて `functions.php` で以下のように設定できます:

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

### 3.0.2.5

#### English

- Improved field placement when dragging fields into lower Tab Groups.
- Improved where the bottom "Add New Field" button places fields when Tab Groups are present.
- Fixed field addition issues when multiple Loop fields exist on the same field group.
- Improved mobile spacing for nested category items.
- Added a warning for empty Tab Group labels.
- Adjusted several edit-screen colors to follow the user's admin color scheme.
- Improved Textarea height adjustment for saved long content.
- Added missing translations.

#### 日本語

- 下部のタブフィールドへフィールドをドラッグする際の配置を改善しました。
- タブフィールドがある場合、最下部の「新規フィールドを追加」ボタンで追加される場所を改善しました。
- 複数のループ（複製フィールド）が存在するとき、フィールドを追加できないことがある問題を改善しました。
- モバイル表示で、カテゴリーに縦方向の余白ができることがある問題を改善しました。
- タブグループのラベルが未入力の場合に警告を表示するようにしました。
- ユーザーの管理画面カラースキームに合わせて、編集画面内の一部のカラーが追随するようにしました。
- テキストエリアの入力内容に応じて、保存後のテキストエリアの高さが追随するようにしました。
- 一部の翻訳を追加しました。

For full release history, see [GitHub Releases](https://github.com/at-shift/at-shift-cfs/releases).

すべての更新履歴は [GitHub Releases](https://github.com/at-shift/at-shift-cfs/releases) を参照してください。

## Attribution (帰属表示)

- Original (元): Custom Field Suite (CFS) / Matt Gibbs
- Original project (元プロジェクト): https://wordpress.org/plugins/custom-field-suite/
- Original source (元ソースコード): https://github.com/mgibbs189/custom-field-suite
- Extension and maintenance (拡張・更新): @shift
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
