# atshift Fields Release Workflow Notes

## Release Notes Rule

Before pushing a release candidate, show the user the exact changelog text planned for `readme.txt` / `README.md`. When `README.md` includes both English and Japanese release notes, show both languages before pushing.

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

The user usually pushes through GitHub Desktop. Do not run CLI `git push` unless explicitly requested.
