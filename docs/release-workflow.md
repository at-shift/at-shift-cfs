# atshift Fields Release Workflow Notes

## Release Notes Rule

Before pushing a release candidate, show the user the exact changelog text planned for `readme.txt` / `README.md`. When `README.md` includes both English and Japanese release notes, show both languages before pushing.

This is a correction step, not only a confirmation step. Leave room for wording changes before commit, push, GitHub release, or WordPress.org SVN commit.

Public changelogs should stay concise and user-facing. Combine small related fixes when possible, and avoid exposing overly internal implementation details unless they affect users, compatibility, or troubleshooting.

## Internal Implementation History

Keep a fuller internal implementation history separately from the public changelog. Record all meaningful implementation details, including:

- UI and CSS adjustments
- JavaScript behavior changes
- PHP/API behavior changes
- translation updates
- validation and security-related changes
- local fixture or verification script updates
- bugs found during verification, even if they were fixed before release

The internal log can be more detailed than the public README changelog so later debugging can trace what changed and why.

## Push / Release Reminder

The user usually pushes through GitHub Desktop. Do not rely on CLI `git push` unless explicitly requested; local HTTPS credentials may not be available to Codex.

Successful GitHub Desktop push pattern:

```bash
osascript -e 'tell application "GitHub Desktop" to activate'
osascript -e 'tell application "System Events" to tell process "GitHub Desktop" to keystroke "p" using command down'
git fetch origin
git status --short --branch
git log --oneline --decorate --max-count=5
```

Confirm `HEAD`, `origin/main`, and `origin/HEAD` match before continuing to WordPress.org.

## WordPress.org Completion Reminder

After SVN commit, run `svn status` and check the generated ZIP URL. A `404` immediately after commit can be normal while WordPress.org generates or caches the ZIP.

When GitHub release and WordPress.org approval are complete, turn the local WordPress environment OFF as the final cleanup step:

- stop the PHP server process for `127.0.0.1:8080`
- stop MySQL through `launchctl bootout`
- verify `launchctl list homebrew.mxcl.mysql@8.4` reports the service missing
- verify the scoped `ps aux | rg ...` check only shows the current `rg` command
- verify `curl -I http://127.0.0.1:8080/wp-admin/` fails to connect
