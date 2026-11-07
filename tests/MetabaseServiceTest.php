<?php

namespace Laravolt\Metabase\Tests;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Laravolt\Metabase\MetabaseService;
use Laravolt\Metabase\MetabaseServiceProvider;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MetabaseServiceTest extends TestCase
{
    private const SECRET = 'test-only-placeholder-not-a-metabase-embedding-key-0000000000000';

    protected function getPackageProviders($app): array
    {
        return [MetabaseServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('services.metabase.secret', self::SECRET);
        $app['config']->set('services.metabase.url', 'https://metabase.example.com/');
    }

    protected function enableRoute($app): void
    {
        $app['config']->set('metabase.route.enabled', true);
    }

    #[Test]
    public function it_does_not_register_the_embed_route_by_default(): void
    {
        $this->assertSame('metabase', config('metabase.route.prefix'));
        $this->assertFalse(config('metabase.route.enabled'));
        $this->assertFalse(app('router')->has('metabase::embed.show'));
    }

    #[Test]
    #[DefineEnvironment('enableRoute')]
    public function it_registers_the_embed_route_when_enabled(): void
    {
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
    public function it_signs_the_given_params_into_the_token(): void
    {
        $service = new MetabaseService();
        $service->setParams(['category' => 'php', 'user_id' => 7]);

        $payload = $this->decode($service->generateEmbedUrl(12, null), 'dashboard');

        $this->assertSame('php', $payload->params->category);
        $this->assertSame(7, $payload->params->user_id);
    }

    #[Test]
    public function it_signs_an_empty_params_object_when_none_are_given(): void
    {
        $payload = $this->decode((new MetabaseService())->generateEmbedUrl(12, null), 'dashboard');

        // Metabase needs a JSON object here; an empty array would decode as [].
        $this->assertInstanceOf(\stdClass::class, $payload->params);
        $this->assertSame([], (array) $payload->params);
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

    private function decode(string $url, string $type): object
    {
        $token = substr($url, strlen("https://metabase.example.com/embed/{$type}/"));

        return JWT::decode($token, new Key(self::SECRET, 'HS256'));
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
