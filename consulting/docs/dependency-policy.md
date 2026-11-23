# Dependency and vulnerability policy

## Patch windows

The clock starts when the advisory becomes **known to us** — a Dependabot PR,
a failing `composer audit`, or a report to security@ — not when it was
published upstream.

| Severity | Patch within | Who decides | Out of hours? |
| --- | --- | --- | --- |
| **Critical** (CVSS 9.0+), or any RCE / auth bypass / unauthenticated data access reachable in our code | **24 hours** | Anyone may ship it; tell the team after | Yes — page the on-call |
| **High** (7.0–8.9) | **72 hours** | Engineer + one reviewer | Next working day |
| **Medium** (4.0–6.9) | **14 days** | Normal PR review | No |
| **Low** (<4.0) | Next scheduled update | Normal PR review | No |

"Reachable in our code" is doing real work in that table. A critical
advisory in a package we never call is a *high* in practice — but you must
demonstrate unreachability, not assume it.

## When an advisory lands

1. **Confirm it affects us.**
   ```bash
   composer audit --locked
   composer why <vendor/package>     # direct dependency or transitive?
   ```
   A transitive dependency we never invoke is lower risk, but only after you
   have checked.

2. **Check for exploitation before patching.** If it is critical and the site
   is internet-facing, assume it may already have been used:
   ```bash
   grep -h 'Failed login\|lockout\|blocked by IP allowlist' storage/logs/security-*.log | tail -100
   php artisan tinker --execute="
     App\Models\AuditLog::where('created_at','>',now()->subDays(7))
       ->orderByDesc('created_at')->limit(50)
       ->get(['created_at','user_email','event','ip'])->each(fn(\$l)=>print(\$l->created_at.' '.\$l->user_email.' '.\$l->event.' '.\$l->ip.PHP_EOL));
   "
   ```
   Anything suspicious → `docs/incident-response.md` first, patch second.

3. **Patch.**
   ```bash
   composer update vendor/package --with-dependencies
   composer audit --locked
   ./vendor/bin/pest && ./vendor/bin/phpstan analyse
   ```

4. **If no patched release exists**, in order of preference:
   - Apply the upstream fix as a patch (`cweagans/composer-patches`)
   - Disable the affected feature
   - Replace the package
   - Block the vector at Cloudflare as a stopgap — **with a dated ticket**,
     never as the permanent answer

5. **Record it** in the log at the bottom of this file.

## A version being unpatchable is a blocker, not a warning

This project runs Laravel 12 rather than the originally specified Laravel 11
for exactly this reason: every 11.x release carries three published
advisories with no patched release in that line, including `CVE-2026-48019`
(CRLF injection in the default `email` validation rule — which every form
here uses). Laravel 11 is outside its security-support window, so the fix was
never backported.

The rule this sets: **if a dependency has a known advisory and no fixed
version in the line you are on, you move lines.** You do not add it to an
ignore list to make CI green. `composer audit` failing is the control
working.

## Dependabot

Configured in `.github/dependabot.yml`: Composer, npm and GitHub Actions,
weekly on Monday.

- Patch and minor bumps are **grouped** into one PR to keep review load sane.
- Majors come individually, because they need real attention.
- Laravel major versions are ignored by Dependabot and upgraded deliberately.

**Grouped PRs still need reading.** Auto-merge is deliberately off: a
compromised package publishing a malicious patch release is a real supply-chain
attack, and the group PR is exactly where it would hide.

Review checklist for any dependency PR:

- [ ] CI green, including `composer audit` and the security suite
- [ ] Changelog read for anything touching auth, validation, serialisation or file handling
- [ ] Release published by the usual maintainer, not a new account
- [ ] No unexplained new transitive dependencies

## Weekly audit

CI runs `composer audit` and `npm audit` on a Monday schedule as well as on
every push. This matters because **an advisory can be published against code
you have not changed** — a repository with no commits for a month is not a
repository with no new vulnerabilities.

## Supply-chain hygiene

- `composer.lock` and `package-lock.json` are committed; deploys use
  `composer install --no-dev` and `npm ci`, never `update`
- Gitleaks runs in CI on full history
- Production installs exclude dev dependencies
- Before adding any new package: when was it last released, how many
  maintainers, does it already do something we could write in fifty lines?

## Advisory log

| Date known | Package | Advisory | Severity | Action | Closed |
| --- | --- | --- | --- | --- | --- |
| 2026-09-28 | laravel/framework 11.x | CVE-2026-48019 + 2 others | High | Moved to Laravel 12.69.2 — no fixed 11.x release exists | 2026-09-28 |
