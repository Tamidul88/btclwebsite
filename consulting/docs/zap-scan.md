# Running an OWASP ZAP scan yourself

ZAP is a dynamic scanner: it drives the running site like an attacker would
and reports what it finds. It complements the Pest suite rather than replacing
it — the tests prove a control works, ZAP looks for the ones nobody thought to
write a test for.

**Only scan a host you own.** Scanning someone else's site without written
permission is unlawful in most jurisdictions. Scan staging, never production:
the active scan submits thousands of hostile payloads and will fill your
database with junk enquiries and trip every rate limit you have.

---

## 1. Prepare a target

Use staging, or run locally against a dedicated database:

```bash
php artisan serve --port=8199
```

Two settings must change for the scan to be meaningful:

```dotenv
# Otherwise ZAP trips the limiter in the first 30 seconds and every
# subsequent request returns 429 — the scan then "finds nothing" because
# it never reached the application.
LOGIN_MAX_ATTEMPTS=100000

# Honeypot must be off, or every form POST is silently dropped and ZAP
# reports the forms as unreachable.
HONEYPOT_ENABLED=false
```

> Turn **both** back on afterwards. A checklist item exists for this in
> `pre-launch-checklist.md` for a reason: forgetting is easy and the
> consequence is a site with no brute-force protection.

---

## 2. Baseline scan — passive, ~1 minute

Passive only: it crawls and inspects responses, and changes nothing. Safe to
run against any environment, including production.

```bash
docker run --rm -t \
  -v "$(pwd)/storage/zap:/zap/wrk:rw" \
  --network host \
  ghcr.io/zaproxy/zaproxy:stable \
  zap-baseline.py \
    -t http://localhost:8199 \
    -c zap-rules.tsv \
    -r baseline-report.html \
    -J baseline-report.json
```

`storage/zap/` needs to exist first:

```bash
mkdir -p storage/zap && cp docs/zap-rules.tsv storage/zap/
```

Exit codes: `0` clean · `1` failures present · `2` warnings only · `3` error.

### What it should find on this app

Nothing at FAIL level. The rules file suppresses two expected findings:

| Rule | Why it is suppressed |
| --- | --- |
| `10063` Permissions-Policy | ZAP's signature is outdated; the header is present and verified by `SecurityHeadersTest` |
| `10035` HSTS missing | Absent over plain http **by design** — see the `secure()` guard in `SecurityHeaders` |

Anything else is a real finding. Do not add it to the suppression file without
understanding it first — a suppression file is where security debt goes to
hide.

---

## 3. Full scan — active, 20–60 minutes

This one attacks. Injection, traversal, XSS, command injection, the lot.
**Staging only.**

```bash
docker run --rm -t \
  -v "$(pwd)/storage/zap:/zap/wrk:rw" \
  --network host \
  ghcr.io/zaproxy/zaproxy:stable \
  zap-full-scan.py \
    -t http://localhost:8199 \
    -c zap-rules.tsv \
    -r full-report.html \
    -J full-report.json
```

Afterwards, reset the database — it will be full of scan payloads:

```bash
php artisan migrate:fresh --seed
```

---

## 4. Scanning behind authentication

The panel is the highest-value target, and an unauthenticated scan never
reaches it. ZAP needs to log in, and this app requires TOTP — which ZAP cannot
produce.

The practical approach is to scan with 2FA temporarily disabled on a
**staging-only** account:

```bash
# Staging only. Never on production data.
php artisan tinker --execute="
  \$u = App\Models\User::where('email','zap@staging.test')->first();
  \$u->forceFill(['two_factor_secret'=>null,'two_factor_confirmed_at'=>null])->save();
"
```

Then set `TWO_FACTOR_ENFORCE=false` in the staging `.env`, and give ZAP a
context file with form-based authentication against
`/<ADMIN_PATH>/login`, with a logged-in indicator of `Sign out` and a
logged-out indicator of `name="password"`.

Re-enable both immediately after. This is the single easiest way to leave a
staging box wide open, so make it the same commit that turns it back on.

---

## 5. Reading the report

Triage in this order:

1. **High** — fix before release, no exceptions.
2. **Medium** — fix or write down why not, with a date.
3. **Low / Informational** — usually noise on a Laravel app (cookie
   attributes ZAP cannot see, framework headers). Read them once, then
   suppress the persistently-wrong ones with a comment saying why.

For every genuine finding, **write a Pest test that fails, then fix it.**
A scanner finding that gets patched without a regression test tends to come
back at the next refactor. That is how the tests in
`tests/Feature/Security/` should grow.

---

## 6. Automating it

The CI workflow runs the baseline scan on a weekly schedule and on pull
requests touching routes, middleware or views — not on every push, because a
scan takes long enough to be an annoyance and short enough to be ignored.

See the `zap-baseline` job in `.github/workflows/ci.yml`.

---

## Scan log

Record every scan so you can tell what changed between them.

| Date | Type | Target | High | Med | Notes |
| --- | --- | --- | --- | --- | --- |
| | baseline | staging | | | |
