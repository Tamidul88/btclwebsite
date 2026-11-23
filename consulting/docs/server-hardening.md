# Server hardening guide

Ubuntu 24.04 LTS · Nginx · PHP 8.4-FPM · MySQL 8 · Let's Encrypt · Cloudflare

Run every step as written. Where a value must change (domain, username, IP),
it is written in `ANGLE_BRACKETS`.

---

## 1. First contact — before anything else

From your workstation, create the deploy user and lock down SSH **in one
session**, and keep that session open until you have proved the new one works.
Locking yourself out of a fresh VPS is the most common way to lose an hour.

```bash
# As root, on the new server
adduser --disabled-password --gecos "" deploy
usermod -aG sudo deploy
install -d -m 700 -o deploy -g deploy /home/deploy/.ssh
```

Copy your public key in:

```bash
# From your workstation
ssh-copy-id -i ~/.ssh/id_ed25519.pub deploy@<SERVER_IP>
```

Give `deploy` passwordless sudo only for the two service reloads the deploy
script needs — not blanket NOPASSWD, which turns any web-shell into root:

```bash
cat >/etc/sudoers.d/deploy <<'EOF'
deploy ALL=(root) NOPASSWD: /bin/systemctl reload php8.4-fpm, /bin/systemctl reload nginx
EOF
chmod 440 /etc/sudoers.d/deploy
visudo -c
```

### Harden SSH

```bash
cat >/etc/ssh/sshd_config.d/99-hardening.conf <<'EOF'
PermitRootLogin no
PasswordAuthentication no
KbdInteractiveAuthentication no
ChallengeResponseAuthentication no
PubkeyAuthentication yes
PermitEmptyPasswords no
MaxAuthTries 3
MaxSessions 4
LoginGraceTime 20
X11Forwarding no
AllowAgentForwarding no
AllowTcpForwarding no
ClientAliveInterval 300
ClientAliveCountMax 2
AllowUsers deploy

# Modern algorithms only.
KexAlgorithms curve25519-sha256,curve25519-sha256@libssh.org,diffie-hellman-group16-sha512
Ciphers chacha20-poly1305@openssh.com,aes256-gcm@openssh.com,aes128-gcm@openssh.com
MACs hmac-sha2-512-etm@openssh.com,hmac-sha2-256-etm@openssh.com
HostKeyAlgorithms ssh-ed25519,rsa-sha2-512,rsa-sha2-256
EOF

sshd -t && systemctl reload ssh
```

**Now open a second terminal and confirm `ssh deploy@<SERVER_IP>` works before
closing the first.**

---

## 2. Firewall

Default deny inbound, allow only what is served.

```bash
apt update && apt install -y ufw
ufw default deny incoming
ufw default allow outgoing
ufw limit 22/tcp comment 'SSH, rate limited'
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable
ufw status verbose
```

`ufw limit` throttles repeated SSH connections from one source — useful even
with passwords disabled, because it cuts the log noise from scanners.

### Restrict 80/443 to Cloudflare (do this after Cloudflare is live, §8)

If the site sits behind Cloudflare, accepting traffic from anywhere else lets
an attacker bypass the WAF by hitting the origin IP directly:

```bash
ufw delete allow 80/tcp
ufw delete allow 443/tcp
for ip in $(curl -s https://www.cloudflare.com/ips-v4); do ufw allow from $ip to any port 443 proto tcp; done
for ip in $(curl -s https://www.cloudflare.com/ips-v6); do ufw allow from $ip to any port 443 proto tcp; done
```

Keep port 80 open to Cloudflare ranges only if you need HTTP-01 certificate
renewal; DNS-01 avoids that entirely.

---

## 3. Fail2ban

```bash
apt install -y fail2ban

cat >/etc/fail2ban/jail.local <<'EOF'
[DEFAULT]
bantime  = 1h
findtime = 10m
maxretry = 5
backend  = systemd
# Never ban yourself out of the box.
ignoreip = 127.0.0.1/8 ::1

[sshd]
enabled = true
maxretry = 3
bantime = 24h

[nginx-http-auth]
enabled = true

[nginx-bad-request]
enabled = true

[nginx-botsearch]
enabled = true

# Bans repeated hits on the admin panel from one address.
[app-admin-probe]
enabled  = true
port     = http,https
filter   = app-admin-probe
logpath  = /var/www/<APP_DIR>/storage/logs/security-*.log
maxretry = 5
findtime = 10m
bantime  = 24h
EOF

cat >/etc/fail2ban/filter.d/app-admin-probe.conf <<'EOF'
[Definition]
failregex = .*"ip":"<HOST>".*Admin access blocked by IP allowlist
            .*"ip":"<HOST>".*Failed 2FA attempt
ignoreregex =
EOF

systemctl enable --now fail2ban
fail2ban-client status
```

> **Behind Cloudflare, Fail2ban sees Cloudflare's IP, not the visitor's.**
> Either install the `mod_cloudflare`-equivalent real-IP config in §5 (which
> makes Nginx log the true client), or do the banning at Cloudflare instead.
> Banning a Cloudflare edge address blocks a slice of your real traffic.

---

## 4. Packages, PHP and unattended upgrades

```bash
apt install -y nginx mysql-server redis-server certbot python3-certbot-nginx \
  php8.4-fpm php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl \
  php8.4-gd php8.4-zip php8.4-bcmath php8.4-intl php8.4-redis \
  unattended-upgrades git unzip
```

### PHP hardening

```bash
cat >/etc/php/8.4/fpm/conf.d/99-hardening.ini <<'EOF'
; Do not advertise PHP in the Server/X-Powered-By headers.
expose_php = Off

; Errors go to the log, never to the response body.
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php/error.log

; Functions with no legitimate use in this app, and high value to a web shell.
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,pcntl_exec,dl

; Sessions are handled by Laravel, but harden the defaults anyway.
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = "Strict"
session.use_strict_mode = 1

; Uploads: the app limits to 5 MB; give a little headroom and no more.
upload_max_filesize = 8M
post_max_size = 10M
max_file_uploads = 5
memory_limit = 256M
max_execution_time = 30

; Opcache, with validation off because deploys restart FPM.
opcache.enable = 1
opcache.validate_timestamps = 0
opcache.max_accelerated_files = 20000
opcache.memory_consumption = 192
EOF

install -d -m 750 -o www-data -g adm /var/log/php
systemctl restart php8.4-fpm
```

`disable_functions` is the single highest-value PHP setting here: it does not
prevent a file-upload vulnerability, but it removes the payoff from one.

### Unattended security updates

```bash
cat >/etc/apt/apt.conf.d/51-unattended-security <<'EOF'
Unattended-Upgrade::Allowed-Origins {
    "${distro_id}:${distro_codename}-security";
    "${distro_id}ESMApps:${distro_codename}-apps-security";
    "${distro_id}ESM:${distro_codename}-infra-security";
};
Unattended-Upgrade::AutoFixInterruptedDpkg "true";
Unattended-Upgrade::Remove-Unused-Kernel-Packages "true";
Unattended-Upgrade::Automatic-Reboot "false";
Unattended-Upgrade::Mail "ops@example.com";
EOF

systemctl enable --now unattended-upgrades
unattended-upgrade --dry-run --debug | tail -20
```

Automatic reboot is **off** deliberately: an unscheduled reboot mid-request is
its own incident. Watch for `/var/run/reboot-required` and reboot in a window.

---

## 5. Nginx

```bash
rm -f /etc/nginx/sites-enabled/default
```

```nginx
# /etc/nginx/sites-available/<DOMAIN>
server {
    listen 80;
    listen [::]:80;
    server_name <DOMAIN> www.<DOMAIN>;

    # ACME challenge must stay reachable over http.
    location /.well-known/acme-challenge/ { root /var/www/certbot; }
    location / { return 301 https://$host$request_uri; }
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name <DOMAIN> www.<DOMAIN>;

    # ---- Document root is the PUBLIC directory, never the project root ----
    root /var/www/<APP_DIR>/current/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/<DOMAIN>/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/<DOMAIN>/privkey.pem;
    ssl_trusted_certificate /etc/letsencrypt/live/<DOMAIN>/chain.pem;

    # TLS 1.2 and 1.3 only. 1.0/1.1 are broken and 1.3-only breaks old clients.
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers off;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305;
    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:10m;
    ssl_session_tickets off;
    ssl_stapling on;
    ssl_stapling_verify on;
    resolver 1.1.1.1 8.8.8.8 valid=300s;

    server_tokens off;
    client_max_body_size 10M;
    client_body_timeout 15s;
    client_header_timeout 15s;

    # Security headers come from the application middleware so there is a
    # single source of truth (and so the CSP nonce works). Only add here what
    # must survive a PHP failure.
    add_header X-Content-Type-Options "nosniff" always;

    # ---- Deny sensitive paths ----
    # .env, .git, composer files, backups, editor droppings.
    location ~ /\.(?!well-known) { deny all; access_log off; log_not_found off; }
    location ~* /(\.env|\.git|composer\.(json|lock)|package(-lock)?\.json|phpunit\.xml|phpstan\.neon)$ {
        deny all;
    }
    location ~* \.(sql|sqlite|bak|old|orig|save|swp|log|ini|sh)$ { deny all; }

    # Nothing under storage/ or vendor/ is ever served directly.
    location ~ ^/(storage/framework|storage/logs|vendor)/ { deny all; }

    # ---- No PHP execution outside the front controller ----
    # Even if a .php file were written into an asset directory, it cannot run.
    location ~* ^/(build|images|css|js|fonts)/.*\.php$ { deny all; }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ ^/index\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm-<APP_DIR>.sock;
        fastcgi_hide_header X-Powered-By;
        fastcgi_param HTTPS on;
        fastcgi_read_timeout 30s;
    }

    # Any other .php request is a probe. 404, do not execute.
    location ~ \.php$ { return 404; }

    location ~* \.(jpg|jpeg|png|gif|webp|svg|ico|css|js|woff2?)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    access_log /var/log/nginx/<DOMAIN>.access.log;
    error_log  /var/log/nginx/<DOMAIN>.error.log warn;
}
```

### Real client IP behind Cloudflare

Without this, every rate limit and the admin IP allowlist sees Cloudflare's
address instead of the visitor's, and both become useless.

```bash
cat >/etc/nginx/conf.d/cloudflare-realip.conf <<'EOF'
# Regenerate when Cloudflare's ranges change (they publish them).
EOF

{
  for ip in $(curl -s https://www.cloudflare.com/ips-v4); do echo "set_real_ip_from $ip;"; done
  for ip in $(curl -s https://www.cloudflare.com/ips-v6); do echo "set_real_ip_from $ip;"; done
  echo "real_ip_header CF-Connecting-IP;"
  echo "real_ip_recursive on;"
} >>/etc/nginx/conf.d/cloudflare-realip.conf

nginx -t && systemctl reload nginx
```

Also set `TRUSTED_PROXIES` in `.env` to the Cloudflare ranges. **Never `*`** —
a wildcard lets any client forge `X-Forwarded-For` and defeat both controls.

### Dedicated PHP-FPM pool

A pool per site means one compromised site cannot read another's files or
sessions.

```bash
cat >/etc/php/8.4/fpm/pool.d/<APP_DIR>.conf <<'EOF'
[<APP_DIR>]
user = deploy
group = www-data
listen = /run/php/php8.4-fpm-<APP_DIR>.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 500

; open_basedir confines PHP to the app and its temp dir. A path-traversal bug
; then cannot read /etc/passwd or another site's .env.
php_admin_value[open_basedir] = /var/www/<APP_DIR>:/tmp:/usr/share/php
php_admin_value[error_log] = /var/log/php/<APP_DIR>-error.log
php_admin_flag[log_errors] = on
php_admin_value[upload_tmp_dir] = /var/www/<APP_DIR>/tmp
EOF

install -d -m 750 -o deploy -g www-data /var/www/<APP_DIR>/tmp
systemctl restart php8.4-fpm
```

---

## 6. TLS certificate

```bash
install -d -m 755 /var/www/certbot
certbot certonly --webroot -w /var/www/certbot \
  -d <DOMAIN> -d www.<DOMAIN> \
  --email ops@example.com --agree-tos --no-eff-email

systemctl list-timers | grep certbot     # renewal timer is installed by the package
certbot renew --dry-run
```

Add a reload hook so a renewed certificate is actually picked up:

```bash
cat >/etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh <<'EOF'
#!/bin/sh
systemctl reload nginx
EOF
chmod +x /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh
```

---

## 7. MySQL 8 — least privilege, localhost only

```bash
mysql_secure_installation
```

Confirm the server is not listening on the network:

```bash
grep -E '^bind-address' /etc/mysql/mysql.conf.d/mysqld.cnf
# bind-address = 127.0.0.1
ss -lntp | grep 3306        # must show 127.0.0.1:3306, never 0.0.0.0:3306
```

Create the database and an application user with **only** the privileges the
app needs. Note there is no `DROP`, no `GRANT`, no `FILE`:

```sql
CREATE DATABASE consulting CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

CREATE USER 'consulting_app'@'127.0.0.1' IDENTIFIED BY '<STRONG_RANDOM_PASSWORD>';
GRANT SELECT, INSERT, UPDATE, DELETE ON consulting.* TO 'consulting_app'@'127.0.0.1';

-- Migrations need schema rights. Use a separate user, invoked only at deploy,
-- so a runtime SQL-injection cannot reach DDL.
CREATE USER 'consulting_migrate'@'127.0.0.1' IDENTIFIED BY '<ANOTHER_PASSWORD>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES
  ON consulting.* TO 'consulting_migrate'@'127.0.0.1';

FLUSH PRIVILEGES;
```

Verify the split actually holds:

```bash
mysql -u consulting_app -p -e "DROP TABLE consulting.users;"   # must be denied
```

---

## 8. Cloudflare

DNS: proxied (orange cloud) A/AAAA records for `@` and `www`.

**SSL/TLS**
- Mode: **Full (strict)** — "Flexible" leaves the origin leg unencrypted and
  silently makes your HTTPS a lie.
- Edge certificate: on. Always Use HTTPS: on. Minimum TLS: 1.2.
- HSTS: enable only once §5 is serving HTTPS correctly, and leave preload off
  until you are certain (see the note in `.env.example`).

**WAF**
- Managed Ruleset: on. OWASP Core Ruleset: on, paranoia level 2 to start.
- Custom rule — restrict the admin panel at the edge:
  `(http.request.uri.path contains "/<ADMIN_PATH>" and ip.src ne <OFFICE_IP>)` → **Block**
- Rate limiting rule: `/enquiries/*` → 10 requests per minute per IP → Block 1h.
- Bot Fight Mode: on.

**Scrape shield**: Email Obfuscation on, Hotlink Protection on.

After Cloudflare is proxying, close the origin to everyone else (§2).

---

## 9. Deploy layout and file permissions

Releases directory so a deploy is an atomic symlink swap:

```
/var/www/<APP_DIR>/
├── current -> releases/20260928120000
├── releases/
├── shared/
│   ├── .env
│   └── storage/
└── tmp/
```

```bash
install -d -o deploy -g www-data /var/www/<APP_DIR>/{releases,shared}
```

Permissions — the web server must **not** be able to write application code:

```bash
cd /var/www/<APP_DIR>/current

# Code: owned by deploy, readable by www-data, writable by neither at runtime.
sudo chown -R deploy:www-data .
sudo find . -type d -exec chmod 750 {} \;
sudo find . -type f -exec chmod 640 {} \;

# Only these two trees are writable, and they contain no executable code.
sudo chmod -R 770 storage bootstrap/cache
sudo chown -R deploy:www-data storage bootstrap/cache

# .env holds the APP_KEY and database password. Nothing but the owner reads it.
sudo chmod 600 ../shared/.env
sudo chown deploy:deploy ../shared/.env
```

Confirm the attachment directory is outside the document root:

```bash
ls -ld storage/app/private/enquiry-attachments
# Served only through AttachmentController; there is no URL that reaches it.
curl -I https://<DOMAIN>/storage/app/private/   # expect 404
```

### Deploy script

```bash
#!/usr/bin/env bash
# /home/deploy/deploy.sh
set -euo pipefail

APP=/var/www/<APP_DIR>
RELEASE="$APP/releases/$(date +%Y%m%d%H%M%S)"

git clone --depth 1 --branch main <REPO_URL> "$RELEASE"
cd "$RELEASE"

ln -sfn "$APP/shared/.env" .env
rm -rf storage && ln -sfn "$APP/shared/storage" storage

composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build && rm -rf node_modules

# Fail the deploy on a known advisory rather than shipping it.
composer audit --locked --no-interaction

# Migrations run as the privileged user, then it is not used again.
DB_USERNAME=consulting_migrate DB_PASSWORD='<MIGRATE_PASSWORD>' \
  php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# The page cache keys on the Vite manifest mtime, so a new build invalidates
# it on its own. This is belt-and-braces for a config or content change that
# does not touch the assets.
php artisan cache:clear

ln -sfn "$RELEASE" "$APP/current"

sudo systemctl reload php8.4-fpm
sudo systemctl reload nginx

php artisan queue:restart

# Keep the last five releases for a fast rollback.
ls -1dt "$APP"/releases/* | tail -n +6 | xargs -r rm -rf
```

Rollback is `ln -sfn <previous_release> current && sudo systemctl reload php8.4-fpm`.

### Queue and scheduler

```bash
cat >/etc/systemd/system/<APP_DIR>-queue.service <<'EOF'
[Unit]
Description=Queue worker
After=network.target mysql.service

[Service]
User=deploy
Group=www-data
Restart=always
RestartSec=5
ExecStart=/usr/bin/php /var/www/<APP_DIR>/current/artisan queue:work --sleep=3 --tries=3 --max-time=3600
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=full
ProtectHome=true

[Install]
WantedBy=multi-user.target
EOF

systemctl enable --now <APP_DIR>-queue

# Scheduler
crontab -u deploy -e
# * * * * * cd /var/www/<APP_DIR>/current && php artisan schedule:run >> /dev/null 2>&1
```

---

## 10. Encrypted off-server backups

Backups you have never restored are a hope, not a plan. This uses `restic`,
which encrypts client-side — the storage provider never sees plaintext.

```bash
apt install -y restic
install -d -m 700 /etc/restic

# Generate and store the repository password. Write it down somewhere the
# server cannot reach: if you lose it, the backups are unrecoverable by design.
openssl rand -base64 48 >/etc/restic/password
chmod 600 /etc/restic/password

cat >/etc/restic/env <<'EOF'
export RESTIC_REPOSITORY="s3:https://<S3_ENDPOINT>/<BUCKET>"
export RESTIC_PASSWORD_FILE="/etc/restic/password"
export AWS_ACCESS_KEY_ID="<KEY>"
export AWS_SECRET_ACCESS_KEY="<SECRET>"
EOF
chmod 600 /etc/restic/env

set -a; . /etc/restic/env; set +a
restic init
```

Backup script:

```bash
cat >/usr/local/bin/backup.sh <<'EOF'
#!/usr/bin/env bash
set -euo pipefail
set -a; . /etc/restic/env; set +a

STAMP=$(date +%Y%m%d%H%M%S)
DUMP="/var/backups/db-$STAMP.sql.gz"
install -d -m 700 /var/backups

# --single-transaction keeps InnoDB consistent without locking writes.
mysqldump --single-transaction --quick --routines --triggers \
  --default-character-set=utf8mb4 consulting | gzip -9 >"$DUMP"

restic backup \
  "$DUMP" \
  /var/www/<APP_DIR>/shared/.env \
  /var/www/<APP_DIR>/shared/storage/app/private \
  --tag daily --host <DOMAIN>

restic forget --tag daily --keep-daily 14 --keep-weekly 8 --keep-monthly 12 --prune
restic check --read-data-subset=5%

shred -u "$DUMP"
EOF

chmod 700 /usr/local/bin/backup.sh
```

Schedule with a systemd timer (survives reboots; cron does not report failure):

```bash
cat >/etc/systemd/system/backup.service <<'EOF'
[Unit]
Description=Encrypted off-server backup
[Service]
Type=oneshot
ExecStart=/usr/local/bin/backup.sh
EOF

cat >/etc/systemd/system/backup.timer <<'EOF'
[Unit]
Description=Daily backup
[Timer]
OnCalendar=*-*-* 02:30:00
RandomizedDelaySec=900
Persistent=true
[Install]
WantedBy=timers.target
EOF

systemctl enable --now backup.timer
systemctl list-timers backup.timer
```

### Restore procedure — test this quarterly, on a scratch host

```bash
set -a; . /etc/restic/env; set +a

# 1. What do we actually have?
restic snapshots

# 2. Restore the most recent snapshot to a scratch directory.
restic restore latest --target /tmp/restore

# 3. Verify the dump is intact before trusting it.
gunzip -t /tmp/restore/var/backups/db-*.sql.gz && echo "dump OK"

# 4. Load into a throwaway database and check real content.
mysql -e "CREATE DATABASE restore_test;"
gunzip -c /tmp/restore/var/backups/db-*.sql.gz | mysql restore_test
mysql restore_test -e "SELECT COUNT(*) FROM enquiries; SELECT MAX(created_at) FROM enquiries;"

# 5. Confirm attachments came back and are readable.
ls -la /tmp/restore/var/www/<APP_DIR>/shared/storage/app/private/enquiry-attachments | head

# 6. Tear down.
mysql -e "DROP DATABASE restore_test;"
rm -rf /tmp/restore
```

Record the date of each successful restore test. An untested backup is the
thing that fails on the day you need it.

> **Note on `.env` in backups:** it contains `APP_KEY`, which decrypts the 2FA
> secrets in the database. Anyone holding both the dump and the `.env` can
> bypass 2FA. Keep the restic password in a separate system from the object
> storage credentials.

---

## 11. Verification

```bash
# Headers
curl -sI https://<DOMAIN> | grep -iE 'strict-transport|content-security|x-frame|x-content|referrer|permissions'

# X-Powered-By must be absent
curl -sI https://<DOMAIN> | grep -i x-powered-by && echo "LEAK" || echo "clean"

# Sensitive paths must not be served
for p in /.env /.git/config /storage/logs/laravel.log /composer.json /vendor/autoload.php; do
  printf '%s %s\n' "$(curl -s -o /dev/null -w '%{http_code}' https://<DOMAIN>$p)" "$p"
done   # all 403/404

# PHP must not execute outside the front controller
curl -s -o /dev/null -w '%{http_code}\n' https://<DOMAIN>/build/test.php   # 404

# TLS grade
docker run --rm -ti drwetter/testssl.sh --quiet --severity HIGH https://<DOMAIN>
```

External checks: <https://securityheaders.com>, <https://ssllabs.com/ssltest>,
<https://observatory.mozilla.org>.
