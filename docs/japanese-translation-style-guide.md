# Japanese Translation Style Guide for atshift Fields

Checked: 2026-07-22

Primary source:

- WordPress Japanese Team: https://ja.wordpress.org/team/handbook/translation/translation-style-guide/

Supplemental background:

- Japanese Style Guide archive PDF linked from the WordPress Japanese Team page:
  https://web.archive.org/web/20160715234554/https://blogs.oracle.com/reiko/resource/ja-style-open.pdf
- Japanese Style Guide quick reference archive PDF linked from the WordPress Japanese Team page:
  https://web.archive.org/web/20160715234551/http://blogs.oracle.com/reiko/resource/ja-style-quick.pdf

Use this note when adding or updating Japanese strings in PHP, JavaScript, `readme.txt`, `README.md`, WordPress.org translations, and documentation examples.

## Priority

1. Follow the WordPress Japanese Team style guide first.
2. Keep approved WordPress glossary terms and existing atshift Fields translations consistent.
3. Use the archived PDF guidance only as supplemental writing guidance when it does not conflict with WordPress rules.

## Writing Style

- Prefer clear, friendly, concise Japanese.
- Avoid literal translation when it produces stiff or unclear Japanese.
- Prefer active phrasing where natural.
- Avoid unnecessary `あなた` and `あなたの`.
- Do not translate `Sorry, ...` as `すみません`; omit it in Japanese error messages.
- Use `ください`, `すべて`, and `すでに`.
- Use `です・ます` for explanatory user-facing sentences.
- Use noun phrases or plain forms for headings and list items.
- Use short command-style labels for buttons. Avoid `します` or `する` when the shorter label is natural.

## Characters And Spacing

- Use Japanese punctuation: `。` and `、`.
- Use half-width letters, numbers, symbols, `?`, `!`, `%`, and parentheses.
- Use half-width Arabic numerals unless a conventional Japanese expression is clearer.
- Insert one half-width space between half-width characters and Japanese text.
- Do not add spaces around `「」`, `『』`, `。`, or `、`.
- Do not add a leading space at the beginning of a string.
- Do not add a space between a number or placeholder and a counter: `%d件`, `1件`.
- Use half-width parentheses `( )`; add spaces outside the parentheses, but not inside them.
- Avoid `～` for numeric or placeholder ranges in runtime messages. Prefer `A から B` when practical.

## Quotation And Labels

- Use `「」` for menu names, button labels, field labels, tab labels, and other UI labels referenced inside a sentence.
- Keep English quotation marks around code, function names, IDs, filenames, domains, and placeholders when the source uses them.
- Do not translate plugin names, theme names, product names, function names, class names, IDs, file names, or placeholders.

## Placeholders And Code-Like Text

- Preserve every placeholder exactly: `%s`, `%d`, `%1$s`, `%2$d`, `{field_name}`, `{フィールド値}`, etc.
- Preserve placeholder type and count.
- If Japanese word order requires reordering printf placeholders, use ordered placeholders such as `%2$s`.
- Preserve HTML tags and translator comments unless the string explicitly allows removal.
- Keep escape sequences such as `\n` and escaped quotes intact.

## Term Selection

When choosing a new Japanese term:

1. Prefer a widely used WordPress or web-industry translation.
2. Prefer understandable Japanese when it is more natural than katakana.
3. Use katakana when it is already established in WordPress or web UI.
4. Keep English when it is a product name, code term, or clearer as-is.

## atshift Fields Terms

- `Field`: `フィールド`
- `Field Group`: `フィールドグループ`
- `Tab Group`: `タブグループ`
- `Loop`: `ループ`
- `Horizontal Group`: `横並びグループ`
- `Accordion Group`: `アコーディオングループ`
- `Conditional Group`: `条件分岐グループ`
- `Row Label`: `行ラベル`
- `Required`: `必須`
- `Permission`: `権限`
- `Role`: `ユーザー権限グループ`
- `Show`: `表示`
- `Hide`: `非表示`
- `Save / Publish`: `保存・公開`
- `Code View`: `コードビュー`
- `Shortcode`: `ショートコード`
- `Classic Meta Box Placement`: `メタボックス配置 (Classic)`
- `Native WordPress`: `WordPress 標準`

## Lowercase Source Strings And Fuzzy Prevention

GlotPress can flag source strings that begin with a lowercase word, such as `atshift`, as fuzzy or style-warning candidates. For new English source strings:

- Do not start full-sentence msgids with lowercase `atshift` when there is a natural alternative.
- Prefer `The atshift Fields plugin ...`, `This plugin ...`, or another sentence that starts with an uppercase word.
- Keep the product name `atshift Fields` unchanged inside the sentence.
- Labels, slugs, IDs, examples, and code-like strings can remain lowercase when that is the correct value.
- Before updating `.pot` or importing translations, scan new English strings for lowercase sentence starts.

## WordPress.org Feedback Workflow

WordPress.org translations can be rejected, adjusted, or marked as waiting/fuzzy/warning. When that happens, do not treat the WordPress.org edit as a one-off correction. Mirror useful corrections back into the local translation files so future `.pot` updates or imports do not roll them back.

Recommended workflow:

1. Review the rejected or fuzzy strings on translate.wordpress.org.
2. Identify whether the issue is Japanese style, glossary consistency, placeholder handling, spacing, or an English source-string problem.
3. Fix the local `.po` file when the Japanese translation is the issue.
4. Fix the source msgid when the English source string itself is causing repeated fuzzy or warning states.
5. Regenerate `.pot`, `.po`, and `.mo` only after preserving accepted WordPress.org wording.
6. Re-import or resubmit the corrected strings.

Current priority policy:

1. Japanese (`ja`): keep this as the primary quality target and mirror WordPress.org fixes locally.
2. English UK (`en_GB`): resolve small remaining waiting/fuzzy counts quickly.
3. Korean (`ko_KR`): improve plugin strings first; readme is already comparatively advanced.
4. Chinese Taiwan (`zh_TW`) and Chinese China (`zh_CN`): review carefully before re-importing because many strings may remain waiting/fuzzy.
5. Russian (`ru_RU`), Dutch (`nl_NL`), and Spanish Spain (`es_ES`): readme translations can be improved after plugin strings stabilize.

## Review Checklist

- Existing atshift Fields wording is reused where possible.
- WordPress glossary and approved translations have been checked for important terms.
- Japanese reads naturally, not like a word-for-word translation.
- UI labels and button labels are consistent across the plugin.
- Placeholders, tags, IDs, and escape sequences are preserved.
- Spaces around ASCII and Japanese text follow the WordPress Japanese style guide.
- Full-sentence English source strings do not unnecessarily begin with lowercase words.
