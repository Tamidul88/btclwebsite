# Logging, alerting and uptime monitoring

The aim is that **every alert means someone must act**. An alert nobody acts
on trains the team to ignore the channel, which is worse than having no alert
at all.

---

## What is logged, and where

| Signal | Channel | Level | Retention |
| --- | --- | --- | --- |
| Failed login | `security` | warning | 90 days |
| Login lockout (throttle tripped) | `security` | error | 90 days |
| Successful login | `security` + audit | info | 90 days / 2 years |
| Password reset completed | `security` + audit | warning | 90 days / 2 years |
| Failed 2FA attempt | `security` + audit | warning | 90 days / 2 years |
| Admin IP allowlist block | `security` | warning | 90 days |
| Any admin create/update/delete | `audit_logs` table | — | 2 years |
| Enquiry viewed / attachment downloaded | `audit_logs` table | — | 2 years |
| Unhandled exception (500) | `daily` + Sentry | error | 14 days / Sentry |

Two separate stores on purpose: `storage/logs/security-*.log` is line-oriented
and easy to ship to a SIEM or grep during an incident; `audit_logs` is
queryable, append-only at the model layer, and surfaced in the panel.

### What is never logged

Passwords (not even length), 2FA secrets, recovery codes, session tokens,
API keys, full request bodies. `AuditLogger` redacts these by key name, and
`bootstrap/app.php` strips exception context so payloads never reach the log
or Sentry.

**Failed logins do record the submitted email address verbatim.** That is
deliberate: without it you cannot tell one person mistyping their password
from someone spraying a list, and that distinction is the entire reason to
watch failures.

---

## Sentry

Installed as `sentry/sentry-laravel`. Configure:

```dotenv
SENTRY_LARAVEL_DSN=https://...@...ingest.sentry.io/...
SENTRY_TRACES_SAMPLE_RATE=0.1
# Must stay false. True attaches user identifiers, IPs and request bodies to
# every event — enquiry messages are personal data and must not leave the
# server inside a stack trace.
SENTRY_SEND_DEFAULT_PII=false
```

Verify it end to end before relying on it:

```bash
php artisan sentry:test
```

Then confirm in the Sentry UI that the event body contains **no** request
payload and no email address. If it does, `send_default_pii` is on.

### Alert rules worth having

| Rule | Condition | Route to |
| --- | --- | --- |
| New issue | any unseen exception type | Slack |
| Regression | resolved issue reappears | Slack + email |
| Spike | 500s > 10 in 5 minutes | **Page on-call** |
| Auth anomaly | `auth.lockout` > 5 in 10 minutes | **Page on-call** |

Set `environment` so staging noise never pages anyone.

---

## Uptime monitoring

The app exposes `/up` (Laravel's health endpoint) — it returns 200 only if the
framework boots, so it catches a broken deploy, not just a dead server.

Use any of Better Stack, UptimeRobot, Healthchecks.io or Pingdom. Minimum
configuration:

- **Endpoint:** `https://<DOMAIN>/up`
- **Interval:** 60 seconds
- **Locations:** at least two regions — a single probe reports its own
  network problems as your outage
- **Failure threshold:** 2 consecutive failures, so one dropped packet does
  not page anyone at 03:00
- **Alert to:** a channel a human actually watches out of hours

Also monitor, because these fail silently:

| Check | Why |
| --- | --- |
| TLS expiry, alert at 21 days | Certbot renewal fails quietly more often than you would think |
| Disk space > 85% | A full disk takes the site down *and* stops the logs that would explain it |
| Scheduler heartbeat | If `schedule:run` dies, retention stops and you keep personal data past the period your privacy notice promises |
| Backup completion | A silent backup failure is the default behaviour of every backup system |
| Queue depth | A stalled worker means enquiry notifications stop arriving |

Scheduler heartbeat — ping a dead-man's-switch after the weekly prune:

```php
// routes/console.php
Schedule::command(PruneEnquiries::class, ['--months=24'])
    ->weeklyOn(1, '03:00')
    ->onOneServer()
    ->thenPing('https://hc-ping.com/<UUID>');
```

---

## Shipping the security log

`storage/logs/security-*.log` is JSON-ish line output, 90-day retention, mode
0640. Ship it off the box — logs that only exist on the compromised host are
not evidence.

Minimal approach with `vector` or `filebeat`:

```
source: /var/www/<APP_DIR>/shared/storage/logs/security-*.log
sink:   your SIEM / Better Stack / CloudWatch
```

Alert on these patterns:

| Pattern | Threshold | Meaning |
| --- | --- | --- |
| `Login lockout triggered` | > 5 in 10 min | Credential stuffing |
| `Failed login attempt` with many distinct emails from one IP | > 20 in 10 min | Account enumeration |
| `Failed 2FA attempt` | > 3 for one user | Password already compromised |
| `Admin access blocked by IP allowlist` | any, if the allowlist is set | Someone knows the admin path |

That last one deserves attention even as a single event: the path is not
published, so a hit means it was found or leaked.

---

## Weekly review

Ten minutes, someone named:

- [ ] Sentry: new issues triaged, regressions reopened
- [ ] Failed-login volume vs last week
- [ ] Any `auth.lockout` events — real users or attacks?
- [ ] Audit log: any admin action nobody recognises
- [ ] Backup ran every night; last restore test still within 90 days
- [ ] Dependabot PRs open longer than the policy window

---

## Verifying the pipeline works

Monitoring that has never fired is monitoring you cannot trust. Quarterly:

```bash
# 1. Sentry receives events
php artisan sentry:test

# 2. Failed logins reach the security log
curl -s -X POST https://staging/<ADMIN_PATH>/login \
  -d 'email=nobody@example.com&password=wrong' >/dev/null
tail -5 storage/logs/security-$(date +%Y-%m-%d).log

# 3. Lockout fires and alerts
for i in $(seq 1 6); do
  curl -s -X POST https://staging/<ADMIN_PATH>/login \
    -d 'email=target@example.com&password=wrong' >/dev/null
done
grep 'Login lockout triggered' storage/logs/security-*.log

# 4. Uptime monitor actually pages
#    Stop the app on staging and confirm the alert arrives, to the right
#    person, within the interval you configured.
```

Record the date of each verification. An untested alert is a hope.
