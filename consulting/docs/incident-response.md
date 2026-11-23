# Incident response checklist

Keep this short enough to follow at 03:00. Print it; an incident that takes the
server down may take the wiki with it.

**Roles:** one *lead* (decides), one *scribe* (timestamps everything).
Everything else waits.

---

## 0. Before you touch anything

Start a log. Every entry gets a UTC timestamp and a name.

```
2026-09-28T02:14Z  alex  Paged: 500s on /contact, ~40% of requests
2026-09-28T02:16Z  alex  Confirmed in Sentry, first event 02:09Z
```

**Do not** restart services, clear caches or redeploy before capturing state.
Those actions destroy exactly the evidence you will need in step 3.

---

## 1. Triage (first 5 minutes)

Decide which this is:

| Signal | Likely | Go to |
| --- | --- | --- |
| Site down, no auth anomalies | Availability | §2 |
| Unexpected admin login / audit entries | **Compromise** | §3 |
| Data visible that should not be | **Breach** | §3 + §6 |
| Sustained traffic spike | Abuse / DoS | §5 |

When unsure, treat it as compromise. Over-reacting costs an afternoon;
under-reacting costs the incident.

---

## 2. Availability

```bash
systemctl status nginx php8.4-fpm mysql
df -h                      # full disk is the most common cause
free -m
tail -100 /var/log/nginx/<DOMAIN>.error.log
tail -100 /var/www/<APP_DIR>/current/storage/logs/laravel-$(date +%Y-%m-%d).log
```

If the last deploy is implicated, roll back rather than debug forward:

```bash
ls -1dt /var/www/<APP_DIR>/releases/* | head -3
ln -sfn /var/www/<APP_DIR>/releases/<PREVIOUS> /var/www/<APP_DIR>/current
sudo systemctl reload php8.4-fpm nginx
```

Put up maintenance mode if you need room to work:

```bash
php artisan down --render=errors::503 --retry=120
```

---

## 3. Suspected compromise

### Contain — but preserve first

```bash
# 1. Snapshot the disk / take a VM image if your provider supports it.

# 2. Capture volatile state BEFORE killing anything.
ps auxf                     > /tmp/ir-ps.txt
ss -tupan                   > /tmp/ir-net.txt
last -20                    > /tmp/ir-logins.txt
w                           >> /tmp/ir-logins.txt
find /var/www/<APP_DIR> -newermt '-48 hours' -type f -ls > /tmp/ir-changed.txt
```

### Cut access

```bash
# Close the site at the edge (Cloudflare → "Under Attack" or a Block rule),
# which is faster and safer than stopping nginx.

# Invalidate every session. Anyone logged in is logged out.
php artisan tinker --execute="DB::table('sessions')->delete();"

# Deactivate all panel users pending review.
php artisan tinker --execute="App\Models\User::query()->update(['is_active' => false]);"
```

### Rotate every credential — assume all are known

Order matters: rotate the database password **before** `APP_KEY`, or the app
cannot reach the database to re-encrypt.

- [ ] MySQL `consulting_app` and `consulting_migrate` passwords
- [ ] Mail/SMTP credentials
- [ ] Object-storage keys (`AWS_*`)
- [ ] Sentry DSN
- [ ] Cloudflare API tokens
- [ ] SSH: remove unknown keys from `~/.ssh/authorized_keys`, rotate your own
- [ ] **`APP_KEY` last.** Rotating it invalidates every encrypted value,
      including stored 2FA secrets. Every admin must re-enrol. Plan that.

### Investigate

```bash
# Who did what in the panel
php artisan tinker --execute="
  App\Models\AuditLog::where('created_at','>',now()->subDays(7))
    ->orderByDesc('created_at')->get(['created_at','user_email','event','ip'])
    ->each(fn(\$l) => print(\$l->created_at.' '.\$l->user_email.' '.\$l->event.' '.\$l->ip.PHP_EOL));
"

# Failed auth and allowlist blocks
grep -h 'Failed 2FA\|blocked by IP allowlist' storage/logs/security-*.log | tail -50

# Files changed since the last deploy — a web shell shows up here
cd /var/www/<APP_DIR>/current && git status --porcelain
git diff --stat HEAD

# Anything executable written into a writable directory
find storage bootstrap/cache -name '*.php' -newermt '-30 days' -ls
```

The audit log is append-only at the model layer, but a scheduled prune uses a
mass delete. If you suspect tampering, compare against the off-server backup
rather than trusting the live table.

### Recover

1. Rebuild from a known-good commit onto a **fresh host**. Do not clean a
   compromised host in place — you cannot prove you got everything.
2. Restore data from the most recent backup taken *before* the first sign of
   compromise (find it in the timeline, not by picking "yesterday").
3. Re-enrol 2FA for every admin.
4. Reactivate users one at a time, confirming each with its owner.

---

## 4. Personal data breach

This app stores names, email addresses, phone numbers, free-text messages and
uploaded documents. If any of that may have been accessed:

- [ ] Record what categories, how many records, and the time window
- [ ] Notify the data protection lead **immediately**
- [ ] Under UK/EU GDPR, a reportable breach must reach the supervisory
      authority within **72 hours of becoming aware** — the clock started when
      you were paged, not when you finish investigating
- [ ] If individuals are at high risk, they must be told directly
- [ ] Do not delete anything: logs, dumps and images are the evidence

---

## 5. Abuse and volumetric traffic

```bash
# Top talkers
awk '{print $1}' /var/log/nginx/<DOMAIN>.access.log | sort | uniq -c | sort -rn | head -20

# Most-hit paths
awk '{print $7}' /var/log/nginx/<DOMAIN>.access.log | sort | uniq -c | sort -rn | head -20
```

Handle it at Cloudflare, not on the origin — filtering after the packet has
already cost you bandwidth is not filtering.

- Enable "Under Attack" mode
- Add a rate-limiting rule for the targeted path
- Block or challenge the offending ASN/country if the pattern is clear

For form spam specifically: the honeypot and the `forms` limiter already
handle ordinary bots. A sustained campaign means adding a Turnstile challenge.

---

## 6. After it is over

Within five working days, write up:

- **Timeline** — detection, containment, recovery, with timestamps
- **Root cause** — the actual mechanism, not "human error"
- **Impact** — records, users, duration
- **What made it worse** — missing alert, unclear runbook, absent backup
- **Actions** — each with a named owner and a date

Then do the boring part: schedule the actions and check them off.

A post-mortem without owners is a diary entry.

---

## Contacts

| Role | Name | Contact |
| --- | --- | --- |
| Incident lead | | |
| Data protection lead | | |
| Hosting provider | | |
| Cloudflare account owner | | |
| Legal / DPO | | |
