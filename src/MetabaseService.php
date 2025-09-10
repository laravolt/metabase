<?php

namespace Laravolt\Metabase;

use InvalidArgumentException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

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
        /** @var string|null $secret */
        $secret = config('services.metabase.secret');
        /** @var string|null $baseUrl */
        $baseUrl = config('services.metabase.url');
        
        if (empty($secret) || !is_string($secret)) {
            throw new InvalidArgumentException('Metabase secret is not configured or invalid');
        }
        
        if (empty($baseUrl) || !is_string($baseUrl)) {
            throw new InvalidArgumentException('Metabase URL is not configured or invalid');
        }

        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($secret)
        );

        $builder = $config->builder();

        if ($dashboard !== null) {
            $builder->withClaim('resource', ['dashboard' => $dashboard]);
            $this->type = 'dashboard';
        } elseif ($question !== null) {
            $builder->withClaim('resource', ['question' => $question]);
            $this->type = 'question';
        } else {
            throw new InvalidArgumentException('Either dashboard or question must be specified');
        }

        $params = empty($this->params) ? (object) [] : $this->params;
        $builder->withClaim('params', $params);

        $token = $builder
            ->getToken($config->signer(), $config->signingKey())
            ->toString();

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
