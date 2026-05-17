# Custom Field Suite Maintenance Build

This repository contains a security and compatibility maintenance build of
Custom Field Suite, based on the upstream Custom Field Suite 2.6.7 codebase.

このリポジトリは、上流版 Custom Field Suite 2.6.7 をベースにした、セキュリティおよび互換性維持のためのメンテナンスビルドです。

Custom Field Suite is a WordPress plugin for adding custom fields to posts,
pages, and custom post types.

Custom Field Suite は、投稿、固定ページ、カスタム投稿タイプにカスタムフィールドを追加するための WordPress プラグインです。

## Attribution

- Original plugin: Custom Field Suite
- Original author: Matt Gibbs
- Original project: https://wordpress.org/plugins/custom-field-suite/
- Original source: https://github.com/mgibbs189/custom-field-suite
- Maintenance build: @shift Yoshiya Tsuchisaka
- GitHub account: https://github.com/at-shift

- 元プラグイン: Custom Field Suite
- 元作者: Matt Gibbs
- 元プロジェクト: https://wordpress.org/plugins/custom-field-suite/
- 元ソースコード: https://github.com/mgibbs189/custom-field-suite
- メンテナンスビルド: @shift Yoshiya Tsuchisaka
- GitHub アカウント: https://github.com/at-shift

The original author attribution and GPLv2 license are preserved. This repository
is a maintenance build, not an official upstream release by the original author.

元作者の表記および GPLv2 ライセンス表記は保持しています。このリポジトリはメンテナンスビルドであり、元作者による公式の上流リリースではありません。

## License

This maintenance build is distributed under the GNU General Public License
version 2 (GPLv2), the same license as the upstream plugin.

このメンテナンスビルドは、上流プラグインと同じ GNU General Public License version 2 (GPLv2) のもとで配布されます。

You may use, copy, modify, and redistribute this package, including modified
versions, under the terms of GPLv2. When redistributing, keep the GPLv2 license
notice, preserve the original author attribution, include the source code, and
make clear that this is a maintenance build.

GPLv2 の条件に従い、このパッケージおよび改変版を使用、複製、改変、再配布できます。再配布する場合は、GPLv2 のライセンス表記、元作者の表記、ソースコードを保持し、これがメンテナンスビルドであることを明示してください。

See [LICENSE](LICENSE) for the full GPLv2 license text.

GPLv2 の全文は [LICENSE](LICENSE) を参照してください。

## Maintenance Version

Current maintenance version: 2.6.7.11

現在のメンテナンスバージョン: 2.6.7.11

Plugin download:
https://github.com/at-shift/custom-field-suite-maintenance/archive/refs/heads/main.zip

プラグインのダウンロード:
https://github.com/at-shift/custom-field-suite-maintenance/archive/refs/heads/main.zip

GitHub ZIP downloads are extracted as `custom-field-suite-maintenance-main`.
Before installing or replacing an existing WordPress plugin, rename the extracted
folder to `custom-field-suite` and place it at:

GitHub の ZIP ダウンロードは `custom-field-suite-maintenance-main` というフォルダ名で展開されます。WordPress プラグインとしてインストールまたは既存版と置き換える前に、展開されたフォルダ名を `custom-field-suite` に変更し、以下の場所に配置してください。

Do not overwrite your original plugin copy without first saving it locally. Keep
the original upstream version in a separate backup folder so that you can restore
it if this maintenance build does not work correctly in your environment.

オリジナル版のプラグインを、事前に保存せず上書きしないでください。メンテナンス版が利用環境で正しく機能しない場合に備えて、オリジナルの上流版を別のバックアップフォルダにローカル保存し、いつでも復元できる状態にしてください。

```text
wp-content/plugins/custom-field-suite
```

Versioning follows the upstream 2.6.7 base version with an additional maintenance
suffix. Future stabilization updates should increment the final number, for
example 2.6.7.12, 2.6.7.13, and so on.

バージョン番号は、上流版 2.6.7 をベースにメンテナンス用の末尾番号を追加する形式です。今後の安定化更新では、2.6.7.12、2.6.7.13 のように最後の番号を増やします。

## Security and Compatibility Changes

This build includes security and compatibility hardening on top of upstream
2.6.7.

このビルドには、上流版 2.6.7 に対するセキュリティおよび互換性の強化が含まれています。

Main changes:

主な変更点:

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

## Verification

The maintenance build was locally verified against:

このメンテナンスビルドでは、ローカル環境で以下を検証しました。

- PHP syntax checks across the plugin.
- Standard CFS save/read behavior.
- All built-in CFS field types:
  Text, Textarea, WYSIWYG, Hyperlink, Date, Color, True / False, Select,
  Relationship, Term, User, File, Loop, and Tab.
- XSS and SQL hardening checks for the modified areas.
- Replacement from the original upstream 2.6.7 plugin to this maintenance build.
- WordPress admin post edit screen compatibility on PHP 8.3.

- プラグイン全体の PHP 構文チェック。
- 標準的な CFS の保存および読み込み動作。
- すべての組み込み CFS フィールドタイプ:
  Text、Textarea、WYSIWYG、Hyperlink、Date、Color、True / False、Select、Relationship、Term、User、File、Loop、Tab。
- 修正箇所に対する XSS および SQL 強化の確認。
- 元の上流版 2.6.7 プラグインからこのメンテナンスビルドへの置き換え確認。
- PHP 8.3 上での WordPress 管理画面投稿編集ページの互換性確認。

These checks are local verification only and are not a third-party security
audit.

これらはローカル検証であり、第三者によるセキュリティ監査ではありません。

## Installation

Copy the renamed `custom-field-suite` directory to:

リネーム済みの `custom-field-suite` フォルダを以下にコピーしてください。

```text
wp-content/plugins/custom-field-suite
```

Then activate Custom Field Suite from the WordPress admin Plugins screen.

その後、WordPress 管理画面のプラグイン画面から Custom Field Suite を有効化してください。

When replacing an existing Custom Field Suite 2.6.7 installation, back up the
site files and database first.

既存の Custom Field Suite 2.6.7 を置き換える場合は、事前にサイトファイルとデータベースをバックアップしてください。

Also keep a local copy of the original upstream version before installing this
maintenance build, so that you can restore the original version at any time if
this maintenance build does not work correctly in your environment.

また、このメンテナンス版が利用環境で正しく機能しない場合に備えて、インストール前にオリジナルの上流版をローカルに保存しておいてください。必要に応じて、いつでも元のバージョンへ戻せる状態にしておくことを推奨します。
