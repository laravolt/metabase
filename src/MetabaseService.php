<?php

namespace Laravolt\Metabase;

use InvalidArgumentException;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class MetabaseService
{
    /**
     * @var array<string> @params
     */
    private $params;

    /**
     * @var array<string> @params
     */
    private $additionalParams;

    /**
     * @param array<string> $params
     *
     * @return void
     */
    public function setParams(array $params): void
    {
        if (empty($params)) {
            $params = ['foo' => null];
        }

        $this->params = $params;
    }

    /**
     * @param array $params
     *
     * @return void
     */
    public function setAdditionalParams(array $params): void
    {
        $this->additionalParams = $params;
    }

    private string $type = 'dashboard';

    /**
     * @param int|null $dashboard
     * @param int|null $question
     *
     * @return string
     */
    public function generateEmbedUrl(?int $dashboard, ?int $question): string
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(config('services.metabase.secret'))
        );

        $builder = $config
            ->builder();

        if ($dashboard) {
            $builder->withClaim('resource', ['dashboard' => $dashboard]);
        } elseif ($question) {
            $builder->withClaim('resource', ['question' => $question]);
            $this->type = 'question';
        } else {
            throw new InvalidArgumentException('Dashboard or question must be specified');
        }

        $builder->withClaim('params', $this->params);

        $token = $builder
            ->getToken($config->signer(), $config->signingKey())
            ->toString();

        return sprintf(
            '%s/embed/%s/%s#' . http_build_query($this->additionalParams),
            config('services.metabase.url'),
            $this->type,
            $token
        );
    }
}
