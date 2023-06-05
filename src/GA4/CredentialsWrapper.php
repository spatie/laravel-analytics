<?php

namespace Botble\Analytics\GA4;

use DomainException;
use Exception;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\Cache\MemoryCacheItemPool;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\CredentialsLoader;
use Google\Auth\FetchAuthTokenCache;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Validation\ValidationException;
use Psr\Cache\CacheItemPoolInterface;

class CredentialsWrapper extends \Google\ApiCore\CredentialsWrapper
{
    public static function build(array $args = []): CredentialsWrapper
    {
        $args += [
            'keyFile' => null,
            'scopes' => null,
            'authHttpHandler' => null,
            'enableCaching' => true,
            'authCache' => null,
            'authCacheOptions' => [],
            'quotaProject' => null,
            'defaultScopes' => null,
            'useJwtAccessWithScope' => true,
        ];
        $keyFile = $args['keyFile'];
        $authHttpHandler = $args['authHttpHandler'] ?: self::buildHttpHandlerFactory();

        if (empty($keyFile)) {
            $loader = self::buildApplicationDefaultCredentials(
                $args['scopes'],
                $authHttpHandler,
                null,
                null,
                $args['quotaProject'],
                $args['defaultScopes']
            );
        } else {
            if (is_string($keyFile)) {
                $keyFile = json_decode($keyFile, true);
            }

            if (isset($args['quotaProject'])) {
                $keyFile['quota_project_id'] = $args['quotaProject'];
            }

            if (! isset($keyFile['type'])) {
                $keyFile['type'] = 'service_account';
            }

            $loader = CredentialsLoader::makeCredentials(
                $args['scopes'],
                (array)$keyFile,
                $args['defaultScopes']
            );
        }

        if ($loader instanceof ServiceAccountCredentials && $args['useJwtAccessWithScope']) {
            // Ensures the ServiceAccountCredentials uses JWT Access, also known
            // as self-signed JWTs, even when user-defined scopes are supplied.
            $loader->useJwtAccessWithScope();
        }

        if ($args['enableCaching']) {
            $authCache = $args['authCache'] ?: new MemoryCacheItemPool();
            $loader = new FetchAuthTokenCache(
                $loader,
                $args['authCacheOptions'],
                $authCache
            );
        }

        return new CredentialsWrapper($loader, $authHttpHandler);
    }

    private static function buildApplicationDefaultCredentials(
        array $scopes = null,
        callable $authHttpHandler = null,
        array $authCacheOptions = null,
        CacheItemPoolInterface $authCache = null,
        $quotaProject = null,
        array $defaultScopes = null
    ) {
        try {
            return ApplicationDefaultCredentials::getCredentials(
                $scopes,
                $authHttpHandler,
                $authCacheOptions,
                $authCache,
                $quotaProject,
                $defaultScopes
            );
        } catch (DomainException $ex) {
            throw new \Google\ApiCore\ValidationException('Could not construct ApplicationDefaultCredentials', $ex->getCode(), $ex);
        }
    }

    private static function buildHttpHandlerFactory()
    {
        try {
            return HttpHandlerFactory::build();
        } catch (Exception $ex) {
            throw new ValidationException('Failed to build HttpHandler', $ex->getCode(), $ex);
        }
    }
}
