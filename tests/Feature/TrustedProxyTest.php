<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/_proxy-probe', fn () => [
            'ip'     => request()->ip(),
            'secure' => request()->isSecure(),
        ]);
    }

    private function probe(string $remoteAddr)
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $remoteAddr])
            ->withHeaders([
                'X-Forwarded-For'   => '203.0.113.7',
                'X-Forwarded-Proto' => 'https',
            ])
            ->getJson('/_proxy-probe');
    }

    public function test_forwarded_headers_from_trusted_tunnel_are_used(): void
    {
        config(['trustedproxy.proxies' => '127.0.0.1,::1']);

        $this->probe('127.0.0.1')->assertExactJson(['ip' => '203.0.113.7', 'secure' => true]);
    }

    public function test_forwarded_headers_from_untrusted_source_are_ignored(): void
    {
        config(['trustedproxy.proxies' => '127.0.0.1,::1']);

        // Akses langsung dari LAN tidak boleh bisa memalsukan IP (bypass rate limit login)
        $this->probe('192.168.1.50')->assertExactJson(['ip' => '192.168.1.50', 'secure' => false]);
    }
}
