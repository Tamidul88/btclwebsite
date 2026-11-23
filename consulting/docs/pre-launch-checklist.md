# Pre-launch security checklist

Tick every line. Anything marked **BLOCKER** stops the launch.

---

## Application configuration

- [ ] **BLOCKER** `APP_DEBUG=false` in the production `.env`
      `php artisan tinker --execute="var_dump(config('app.debug'));"` → `false`
- [ ] **BLOCKER** `APP_ENV=production`
- [ ] **BLOCKER** `APP_KEY` is set, and is **different** from every other
      environment. It decrypts the 2FA secrets — a shared key means staging can
      decrypt production.
- [ ] `APP_URL` is the real https URL (signed URLs and the sitemap depend on it)
- [ ] `ADMIN_PATH` changed from the shipped default
- [ ] `ADMIN_IP_ALLOWLIST` set, or a conscious decision recorded not to
- [ ] **BLOCKER** `TRUSTED_PROXIES` lists the proxy ranges and is **not** `*`
- [ ] `config:cache`, `route:cache`, `view:cache`, `event:cache` all run
- [ ] `.env` is `chmod 600`, owned by the deploy user, not in git

## Transport

- [ ] **BLOCKER** HTTPS enforced; http redirects to https
- [ ] TLS 1.2 + 1.3 only; 1.0/1.1 disabled
- [ ] HSTS present on https responses
- [ ] `HSTS_PRELOAD=false` unless every subdomain is https-only and you accept
      that preload is very hard to undo
- [ ] Certificate auto-renewal tested: `certbot renew --dry-run`
- [ ] Cloudflare SSL mode is **Full (strict)**, not Flexible

## Headers

Verify with `curl -sI https://<DOMAIN>`:

- [ ] `Content-Security-Policy` present, and the public site has **no**
      `unsafe-inline` / `unsafe-eval`
- [ ] `X-Frame-Options: DENY`
- [ ] `X-Content-Type-Options: nosniff`
- [ ] `Referrer-Policy: strict-origin-when-cross-origin`
- [ ] `Permissions-Policy` present
- [ ] `X-Powered-By` **absent** (needs `expose_php = Off`; the middleware alone
      cannot remove a header PHP adds at the SAPI level)
- [ ] `Server` version suppressed (`server_tokens off`)

## Authentication and the admin panel

- [ ] **BLOCKER** Default/seeded accounts removed. There must be **no**
      `admin@example.test`.
      `php artisan tinker --execute="App\Models\User::pluck('email');"`
- [ ] **BLOCKER** Every panel user has 2FA confirmed
      `App\Models\User::whereNull('two_factor_confirmed_at')->pluck('email')` → empty
- [ ] Password policy: 12+ chars, mixed case, number, symbol, HIBP breach check
- [ ] Login lockout after 5 attempts verified by hand
- [ ] Session lifetime is 30 minutes or less
- [ ] Session cookie: `Secure`, `HttpOnly`, `SameSite=Strict`, encrypted
- [ ] Roles assigned least-privilege; nobody is admin "just in case"
- [ ] Recovery codes issued and stored somewhere other than the server

## Input, output and uploads

- [ ] All input validated in FormRequests — no `$request->all()` into a model
- [ ] `$fillable` set on every model; server-controlled columns excluded
- [ ] Output escaped with `{{ }}`; `grep -rn '{!!' resources/views` reviewed
      line by line (the only permitted uses are the self-generated QR SVG and
      the JSON-LD block)
- [ ] Uploads: extension **and** content-type checked, images re-encoded,
      filenames randomised, stored outside the web root
- [ ] PHP execution denied in every writable directory
- [ ] Rate limits active on both public forms

## Database

- [ ] **BLOCKER** MySQL bound to `127.0.0.1`; `ss -lntp | grep 3306` confirms
- [ ] Application user has no `DROP`/`GRANT`/`FILE`; migrations use a separate user
- [ ] Root password set and not reused anywhere
- [ ] Charset `utf8mb4`
- [ ] No test/demo data left in the tables

## Server

- [ ] **BLOCKER** SSH: keys only, root login disabled, `deploy` user only
- [ ] UFW default-deny inbound; only 22/80/443 open (and 80/443 limited to
      Cloudflare if proxied)
- [ ] Fail2ban running with the sshd and app-admin-probe jails
- [ ] Unattended security upgrades enabled and dry-run verified
- [ ] Document root is `.../public`, not the project root
- [ ] `.env`, `.git`, `storage/`, `vendor/` all return 403/404 over http
- [ ] `open_basedir` and `disable_functions` set in the FPM pool
- [ ] Code directories are **not** writable by `www-data`

## Backups

- [ ] Encrypted off-server backups running on a timer
- [ ] **BLOCKER** A restore has been performed end-to-end on a scratch host and
      the row counts checked. Date of last successful restore: `____________`
- [ ] Restic password stored somewhere the server cannot reach
- [ ] Retention: 14 daily / 8 weekly / 12 monthly
- [ ] Backup failure raises an alert (a silent backup failure is the default)

## Automated baseline

- [ ] **BLOCKER** `php artisan security:audit --production` exits 0 on the
      production host, with the real `.env` in place
- [ ] OWASP ZAP baseline scan run against staging, report filed, no FAIL-level
      findings — see `docs/zap-scan.md`
- [ ] Rate limiting and honeypot **re-enabled** after any ZAP run
      (`LOGIN_MAX_ATTEMPTS`, `HONEYPOT_ENABLED`)
- [ ] Any ZAP finding that was fixed has a Pest regression test

## Monitoring

- [ ] Sentry DSN set, `SENTRY_SEND_DEFAULT_PII=false`
- [ ] `php artisan sentry:test` produced an event, and the payload contains
      no request body and no email address
- [ ] Failed-login and lockout alerts verified by triggering them for real
      (see the verification section of `docs/monitoring.md`)
- [ ] Scheduler heartbeat wired, so a dead scheduler is noticed before it
      silently stops the retention prune
- [ ] A test exception reached Sentry and the payload contains no request body
- [ ] Uptime monitor on `/up` from at least two regions, alerting to a human
- [ ] Certificate expiry alert at 21 days
- [ ] `storage/logs/security-*.log` shipped somewhere durable
- [ ] Disk-space alert (a full disk takes the site down and stops the logs)

## Legal and privacy

- [ ] Privacy notice matches what the code actually does
- [ ] Retention period in the notice matches `enquiries:prune`, and the
      schedule is actually running
- [ ] Cookie notice accurate — this app sets only strictly-necessary cookies
- [ ] `{{COMPANY}}` placeholder replaced everywhere:
      `grep -rn '{{COMPANY}}' --include='*.php' --include='*.blade.php' .`

## Final verification

```bash
composer audit --locked          # no advisories
npm audit --audit-level=moderate
./vendor/bin/pest                # 142 passing
./vendor/bin/phpstan analyse     # no errors
./vendor/bin/pint --test
```

- [ ] CI green on the commit being deployed
- [ ] securityheaders.com grade A or better
- [ ] SSL Labs grade A or better
- [ ] Rollback rehearsed: previous release symlinked back, site still up

---

## Sign-off

| Role | Name | Date |
| --- | --- | --- |
| Engineer | | |
| Reviewer | | |
| Owner | | |
