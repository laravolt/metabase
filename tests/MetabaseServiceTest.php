<?php

namespace Laravolt\Metabase\Tests;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Laravolt\Metabase\MetabaseService;
use Laravolt\Metabase\MetabaseServiceProvider;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MetabaseServiceTest extends TestCase
{
    private const SECRET = 'a3f1c2e4b5d6a7f8091a2b3c4d5e6f708192a3b4c5d6e7f8091a2b3c4d5e6f70';

    protected function getPackageProviders($app): array
    {
        return [MetabaseServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('services.metabase.secret', self::SECRET);
        $app['config']->set('services.metabase.url', 'https://metabase.example.com/');
    }

    #[Test]
    public function it_registers_package_config_and_routes(): void
    {
        $this->assertSame('metabase', config('metabase.route.prefix'));
        $this->assertTrue(app('router')->has('metabase::embed.show'));
    }

    #[Test]
    public function it_generates_a_signed_dashboard_embed_url(): void
    {
        $url = (new MetabaseService())->generateEmbedUrl(12, null);

        $this->assertStringStartsWith('https://metabase.example.com/embed/dashboard/', $url);

        $token = substr($url, strlen('https://metabase.example.com/embed/dashboard/'));
        $payload = JWT::decode($token, new Key(self::SECRET, 'HS256'));

        $this->assertSame(12, $payload->resource->dashboard);
        $this->assertGreaterThan(time(), $payload->exp);
    }

    #[Test]
    public function it_generates_a_question_embed_url_with_additional_params(): void
    {
        $service = new MetabaseService();
        $service->setAdditionalParams(['bordered' => 'false', 'titled' => 'true']);

        $url = $service->generateEmbedUrl(null, 7);

        $this->assertStringStartsWith('https://metabase.example.com/embed/question/', $url);
        $this->assertStringEndsWith('#bordered=false&titled=true', $url);
    }

    #[Test]
    public function it_does_not_log_the_embed_payload(): void
    {
        Log::spy();

        (new MetabaseService())->generateEmbedUrl(12, null);

        Log::shouldNotHaveReceived('info');
        Log::shouldNotHaveReceived('debug');
    }

    #[Test]
    public function it_requires_a_dashboard_or_question(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new MetabaseService())->generateEmbedUrl(null, null);
    }

    #[Test]
    public function it_requires_a_configured_secret(): void
    {
        config(['services.metabase.secret' => null]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Metabase secret is not configured');

        (new MetabaseService())->generateEmbedUrl(1, null);
    }
}
