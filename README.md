# `ci` — fork automation branch

This is the **default branch of the `breckenedge` fork only**. It is *not* part of
the Dallas Makerspace project and intentionally contains no application code.

Its sole purpose is to host scheduled automation that the project's `master`
branch must stay free of:

- `.github/workflows/sync-fork.yml` — fast-forwards this fork's `master` to
  `Dallas-Makerspace/calendar@master` (upstream) daily, keeping `master` a
  pristine mirror so PR diffs stay clean.

## Why a separate default branch?

GitHub only runs `schedule` (cron) workflows from a repository's **default
branch**. If the sync workflow lived on `master`, `master` would carry a
fork-only commit and could no longer be a clean fast-forward mirror of upstream.
Hosting the cron here on `ci` lets `master` stay byte-identical to upstream.

## Day-to-day

Real development still happens against `master` and feature branches. Open PRs
with an explicit base, e.g. `gh pr create --repo breckenedge/dallas-makerspace-calendar --base master ...`.

To sync manually, run the workflow from the Actions tab (**Sync master with
upstream → Run workflow**) or locally:

```bash
gh repo sync breckenedge/dallas-makerspace-calendar \
  --source Dallas-Makerspace/calendar --branch master
```
