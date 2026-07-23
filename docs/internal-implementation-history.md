# atshift Fields Internal Implementation History

This file records implementation details that are intentionally more detailed than the public WordPress.org and GitHub release notes.

## 3.0.2.7

- Removed premature `format_for_editor()` calls from WYSIWYG and native Post Content visual editor paths. WordPress applies editor formatting internally when `wp_editor()` is used.
- Added editor input normalization for WYSIWYG-style content so previously stored escaped HTML tags such as `&lt;p&gt;` can be shown as normal visual editor content.
- Kept the normalization limited to common rich text HTML tags before visual editor display, avoiding a broad decode for ordinary text fields.
- Applied the TinyMCE `content_style` font reset in admin screens as well as frontend screens so non-admin editor users see the same readable WordPress admin font stack.
- Verified on the live edit screen that literal `<p>` text no longer appears in the WYSIWYG iframe and that the editor font uses the intended sans-serif stack.
