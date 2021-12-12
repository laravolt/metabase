<?php

namespace Laravolt\Metabase;

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
     * @param array<string> $params
     *
     * @return void
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     * @param int|null $dashboard
     * @param int|null $question
     *
     * @return string
     */
    public function generateDashboardUrl(?int $dashboard, ?int $question): string
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(config('services.metabase.secret'))
        );
        $metabaseUrl = config('services.metabase.url');

        $builder = $config->builder();
        if ($dashboard) {
            $builder->withClaim('resource', ['dashboard' => $dashboard]);
        }
        if ($question) {
            $builder->withClaim('resource', ['question' => $question]);
        }
        $token = $builder
            ->getToken($config->signer(), $config->signingKey())
            ->toString();

        return sprintf('%s/embed/question/%s#bordered=true&titled=false', $metabaseUrl, $token);
    }
}
