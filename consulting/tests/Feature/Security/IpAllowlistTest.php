<?php

use App\Support\Cidr;

it('allows every address when the allowlist is empty', function () {
    config()->set('security.admin_ip_allowlist', []);

    $this->get('/'.config('security.admin_path'))->assertRedirect();  // auth, not 404
});

it('returns 404 rather than 403 for a blocked address', function () {
    // 404 so a probe cannot confirm the panel exists at this path.
    config()->set('security.admin_ip_allowlist', ['203.0.113.7']);

    $this->get('/'.config('security.admin_path'))->assertNotFound();
});

it('admits an address inside an allowed range', function () {
    config()->set('security.admin_ip_allowlist', ['127.0.0.0/8']);

    $this->get('/'.config('security.admin_path'))->assertRedirect();
});

it('matches exact addresses', function () {
    expect(Cidr::matches('203.0.113.7', '203.0.113.7'))->toBeTrue()
        ->and(Cidr::matches('203.0.113.8', '203.0.113.7'))->toBeFalse();
});

it('matches ipv4 cidr ranges correctly', function (string $ip, string $range, bool $expected) {
    expect(Cidr::matches($ip, $range))->toBe($expected);
})->with([
    ['192.168.1.5', '192.168.1.0/24', true],
    ['192.168.2.5', '192.168.1.0/24', false],
    ['10.0.0.1', '10.0.0.0/8', true],
    ['11.0.0.1', '10.0.0.0/8', false],
    ['203.0.113.130', '203.0.113.128/25', true],
    ['203.0.113.127', '203.0.113.128/25', false],
    ['1.2.3.4', '0.0.0.0/0', true],
]);

it('matches ipv6 ranges and never crosses address families', function () {
    expect(Cidr::matches('2001:db8::1', '2001:db8::/32'))->toBeTrue()
        ->and(Cidr::matches('2001:db9::1', '2001:db8::/32'))->toBeFalse()
        // An IPv4 address must never match an IPv6 range, or vice versa.
        ->and(Cidr::matches('192.168.1.1', '2001:db8::/32'))->toBeFalse()
        ->and(Cidr::matches('2001:db8::1', '192.168.1.0/24'))->toBeFalse();
});

it('rejects malformed ranges instead of failing open', function (string $range) {
    expect(Cidr::matches('192.168.1.5', $range))->toBeFalse();
})->with(['', 'not-an-ip', '192.168.1.0/abc', '192.168.1.0/33', '192.168.1.0/-1']);

it('treats a null client ip as not allowed', function () {
    expect(Cidr::matches(null, '0.0.0.0/0'))->toBeFalse();
});
