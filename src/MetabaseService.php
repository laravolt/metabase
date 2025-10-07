<?php

namespace Laravolt\Metabase;

use stdClass;
use Firebase\JWT\JWT;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;

class MetabaseService
{
    /**
     * @var array<string, mixed>
     */
    private array $params = [];

    /**
     * @var array<string, mixed>
     */
    private array $additionalParams = [];

    private string $type = 'dashboard';

    /**
     * @param array<string, mixed> $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setAdditionalParams(array $params): void
    {
        $this->additionalParams = $params;
    }

    /**
     * Generate the embed URL for a Metabase dashboard or question.
     *
     * @param int|null $dashboard
     * @param int|null $question
     * @return string
     * @throws InvalidArgumentException
     */
    public function generateEmbedUrl(?int $dashboard, ?int $question): string
    {
        $secret = config('services.metabase.secret');
        $baseUrl = config('services.metabase.url');

        if (empty($secret)) {
            throw new InvalidArgumentException('Metabase secret is not configured');
        }

        if (empty($baseUrl)) {
            throw new InvalidArgumentException('Metabase URL is not configured');
        }

        // Build the payload
        $resource = [];

        if ($dashboard !== null) {
            $resource = ['dashboard' => $dashboard];
            $this->type = 'dashboard';
        } elseif ($question !== null) {
            $resource = ['question' => $question];
            $this->type = 'question';
        } else {
            throw new InvalidArgumentException('Either dashboard or question must be specified');
        }

        $params = new stdClass(); // Default to empty object

        // TODO: Still need to check this logic later
        // if (count($this->params) > 0) {
        //     $params = (object) $this->params;
        // }

        $payload = [
            "resource" => $resource,
            "params" => $params,
            "exp" => time() + (10 * 60) // 10 menit ke depan
        ];

        // Set expiration time (10 minutes from now)
        $payload['exp'] = time() + (10 * 60);

        // Generate JWT token using Firebase JWT
        $token = JWT::encode($payload, $secret, 'HS256');

        // Debug: Log the payload
        Log::info('Metabase JWT Token Payload:', [
            'payload' => $payload,
            'dashboard' => $dashboard,
            'question' => $question,
            'params' => $this->params,
            'type' => $this->type
        ]);

        $additionalQuery = !empty($this->additionalParams) ? '#' . http_build_query($this->additionalParams) : '';

        return sprintf(
            '%s/embed/%s/%s%s',
            rtrim($baseUrl, '/'),
            $this->type,
            $token,
            $additionalQuery
        );
    }
}
