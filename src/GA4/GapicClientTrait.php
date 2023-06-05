<?php

namespace Botble\Analytics\GA4;

use Google\ApiCore\AgentHeader;
use Google\ApiCore\Call;
use Google\ApiCore\GapicClientTrait as BaseGapicClientTrait;
use Google\ApiCore\Middleware\CredentialsWrapperMiddleware;
use Google\ApiCore\Middleware\FixedHeaderMiddleware;
use Google\ApiCore\Middleware\OptionsFilterMiddleware;
use Google\ApiCore\Middleware\RetryMiddleware;
use Google\ApiCore\RetrySettings;
use Google\ApiCore\Transport\GrpcFallbackTransport;
use Google\ApiCore\Transport\GrpcTransport;
use Google\ApiCore\Transport\RestTransport;
use Google\ApiCore\Transport\TransportInterface;
use Google\ApiCore\ValidationException;
use Google\ApiCore\Version;
use Google\Auth\CredentialsLoader;
use Google\Auth\FetchAuthTokenInterface;
use Google\Protobuf\Internal\Message;
use Grpc\Gcp\ApiConfig;
use Grpc\Gcp\Config;

trait GapicClientTrait
{
    use BaseGapicClientTrait;

    private $transport;
    private $credentialsWrapper;

    private static $gapicVersionFromFile;
    private $retrySettings;
    private $serviceName;
    private $agentHeader;
    private $descriptors;
    private $transportCallMethods = [
        Call::UNARY_CALL => 'startUnaryCall',
        Call::BIDI_STREAMING_CALL => 'startBidiStreamingCall',
        Call::CLIENT_STREAMING_CALL => 'startClientStreamingCall',
        Call::SERVER_STREAMING_CALL => 'startServerStreamingCall',
    ];

    private static function getGapicVersion(array $options)
    {
        if (isset($options['libVersion'])) {
            return $options['libVersion'];
        } else {
            if (! isset(self::$gapicVersionFromFile)) {
                self::$gapicVersionFromFile = AgentHeader::readGapicVersionFromFile(__CLASS__);
            }

            return self::$gapicVersionFromFile;
        }
    }

    private static function initGrpcGcpConfig(string $hostName, string $confPath): Config
    {
        $apiConfig = new ApiConfig();
        $apiConfig->mergeFromJsonString(file_get_contents($confPath));

        return new Config($hostName, $apiConfig);
    }

    private static function getClientDefaults(): array
    {
        return [];
    }

    private function buildClientOptions(array $options): array
    {
        // Build $defaultOptions starting from top level
        // variables, then going into deeper nesting, so that
        // we will not encounter missing keys
        $defaultOptions = self::getClientDefaults();
        $defaultOptions += [
            'disableRetries' => false,
            'credentials' => null,
            'credentialsConfig' => [],
            'transport' => null,
            'transportConfig' => [],
            'gapicVersion' => self::getGapicVersion($options),
            'libName' => null,
            'libVersion' => null,
            'apiEndpoint' => null,
            'clientCertSource' => null,
        ];

        $supportedTransports = $this->supportedTransports();
        foreach ($supportedTransports as $transportName) {
            if (! array_key_exists($transportName, $defaultOptions['transportConfig'])) {
                $defaultOptions['transportConfig'][$transportName] = [];
            }
        }
        if (in_array('grpc', $supportedTransports)) {
            $defaultOptions['transportConfig']['grpc'] = [
                'stubOpts' => ['grpc.service_config_disable_resolution' => 1],
            ];
        }

        // Merge defaults into $options starting from top level
        // variables, then going into deeper nesting, so that
        // we will not encounter missing keys
        $options += $defaultOptions;
        $options['credentialsConfig'] += $defaultOptions['credentialsConfig'];
        $options['transportConfig'] += $defaultOptions['transportConfig'];  // @phpstan-ignore-line
        if (isset($options['transportConfig']['grpc'])) {
            $options['transportConfig']['grpc'] += $defaultOptions['transportConfig']['grpc'];
            $options['transportConfig']['grpc']['stubOpts'] += $defaultOptions['transportConfig']['grpc']['stubOpts'];
        }
        if (isset($options['transportConfig']['rest'])) {
            $options['transportConfig']['rest'] += $defaultOptions['transportConfig']['rest'];
        }

        $this->modifyClientOptions($options);

        // serviceAddress is now deprecated and acts as an alias for apiEndpoint
        if (isset($options['serviceAddress'])) {
            $options['apiEndpoint'] = $this->pluck('serviceAddress', $options, false);
        }

        // If an API endpoint is set, ensure the "audience" does not conflict
        // with the custom endpoint by setting "user defined" scopes.
        if ($options['apiEndpoint'] != $defaultOptions['apiEndpoint']
            && empty($options['credentialsConfig']['scopes'])
            && ! empty($options['credentialsConfig']['defaultScopes'])
        ) {
            $options['credentialsConfig']['scopes'] = $options['credentialsConfig']['defaultScopes'];
        }

        if (extension_loaded('sysvshm')
            && isset($options['gcpApiConfigPath'])
            && file_exists($options['gcpApiConfigPath'])
            && isset($options['apiEndpoint'])) {
            $grpcGcpConfig = self::initGrpcGcpConfig(
                $options['apiEndpoint'],
                $options['gcpApiConfigPath']
            );

            if (array_key_exists('stubOpts', $options['transportConfig']['grpc'])) {
                $options['transportConfig']['grpc']['stubOpts'] += [
                    'grpc_call_invoker' => $grpcGcpConfig->callInvoker(),
                ];
            } else {
                $options['transportConfig']['grpc'] += [
                    'stubOpts' => [
                        'grpc_call_invoker' => $grpcGcpConfig->callInvoker(),
                    ],
                ];
            }
        }

        // mTLS: detect and load the default clientCertSource if the environment variable
        // "GOOGLE_API_USE_CLIENT_CERTIFICATE" is true, and the cert source is available
        if (empty($options['clientCertSource']) && CredentialsLoader::shouldLoadClientCertSource()) {
            if ($defaultCertSource = CredentialsLoader::getDefaultClientCertSource()) {
                $options['clientCertSource'] = function () use ($defaultCertSource) {
                    $cert = call_user_func($defaultCertSource);

                    // the key and the cert are returned in one string
                    return [$cert, $cert];
                };
            }
        }

        // mTLS: If no apiEndpoint has been supplied by the user, and either
        // GOOGLE_API_USE_MTLS_ENDPOINT tells us to, or mTLS is available, use the mTLS endpoint.
        if ($options['apiEndpoint'] === $defaultOptions['apiEndpoint']
            && $this->shouldUseMtlsEndpoint($options)
        ) {
            $options['apiEndpoint'] = self::determineMtlsEndpoint($options['apiEndpoint']);
        }

        return $options;
    }

    private function shouldUseMtlsEndpoint(array $options): bool
    {
        $mtlsEndpointEnvVar = getenv('GOOGLE_API_USE_MTLS_ENDPOINT');
        if ('always' === $mtlsEndpointEnvVar) {
            return true;
        }
        if ('never' === $mtlsEndpointEnvVar) {
            return false;
        }
        // For all other cases, assume "auto" and return true if clientCertSource exists
        return ! empty($options['clientCertSource']);
    }

    private static function determineMtlsEndpoint(string $apiEndpoint): string
    {
        $parts = explode('.', $apiEndpoint);
        if (count($parts) < 3) {
            return $apiEndpoint; // invalid endpoint!
        }

        return sprintf('%s.mtls.%s', array_shift($parts), implode('.', $parts));
    }

    private function setClientOptions(array $options): void
    {
        // serviceAddress is now deprecated and acts as an alias for apiEndpoint
        if (isset($options['serviceAddress'])) {
            $options['apiEndpoint'] = $this->pluck('serviceAddress', $options, false);
        }
        $this->validateNotNull($options, [
            'apiEndpoint',
            'serviceName',
            'descriptorsConfigPath',
            'clientConfig',
            'disableRetries',
            'credentialsConfig',
            'transportConfig',
        ]);
        $this->traitValidate($options, [
            'credentials',
            'transport',
            'gapicVersion',
            'libName',
            'libVersion',
        ]);

        $clientConfig = $options['clientConfig'];
        if (is_string($clientConfig)) {
            $clientConfig = json_decode(file_get_contents($clientConfig), true);
        }
        $this->serviceName = $options['serviceName'];
        $this->retrySettings = RetrySettings::load(
            $this->serviceName,
            $clientConfig,
            $options['disableRetries']
        );

        // Edge case: If the client has the gRPC extension installed, but is
        // a REST-only library, then the grpcVersion header should not be set.
        if ($this->transport instanceof GrpcTransport) {
            $options['grpcVersion'] = phpversion('grpc');
            unset($options['restVersion']);
        } elseif ($this->transport instanceof RestTransport
            || $this->transport instanceof GrpcFallbackTransport) {
            unset($options['grpcVersion']);
            $options['restVersion'] = Version::getApiCoreVersion();
        }

        $this->agentHeader = AgentHeader::buildAgentHeader(
            $this->pluckArray([
                'libName',
                'libVersion',
                'gapicVersion',
            ], $options)
        );
        self::validateFileExists($options['descriptorsConfigPath']);
        $descriptors = require($options['descriptorsConfigPath']);
        $this->descriptors = $descriptors['interfaces'][$this->serviceName];

        $this->credentialsWrapper = $this->createCredentialsWrapper(
            $options['credentials'],
            $options['credentialsConfig']
        );

        $transport = $options['transport'] ?: self::defaultTransport();
        $this->transport = $transport instanceof TransportInterface
            ? $transport
            : $this->createTransport(
                $options['apiEndpoint'],
                $transport,
                $options['transportConfig'],
                $options['clientCertSource']
            );
    }

    private function createCredentialsWrapper($credentials, array $credentialsConfig): CredentialsWrapper
    {
        if (is_null($credentials)) {
            return CredentialsWrapper::build($credentialsConfig);
        } elseif (is_string($credentials) || is_array($credentials)) {
            return CredentialsWrapper::build(['keyFile' => $credentials] + $credentialsConfig);
        } elseif ($credentials instanceof FetchAuthTokenInterface) {
            $authHttpHandler = $credentialsConfig['authHttpHandler'] ?? null;

            return new CredentialsWrapper($credentials, $authHttpHandler);
        } elseif ($credentials instanceof CredentialsWrapper) {
            return $credentials;
        } else {
            throw new ValidationException(
                'Unexpected value in $auth option, got: ' .
                print_r($credentials, true)
            );
        }
    }

    private function createTransport(
        string $apiEndpoint,
        $transport,
        array $transportConfig,
        callable $clientCertSource = null
    ): GrpcTransport|GrpcFallbackTransport|RestTransport {
        if (! is_string($transport)) {
            throw new ValidationException(
                "'transport' must be a string, instead got:" .
                print_r($transport, true)
            );
        }
        $supportedTransports = self::supportedTransports();
        if (! in_array($transport, $supportedTransports)) {
            throw new ValidationException(
                sprintf(
                    'Unexpected transport option "%s". Supported transports: %s',
                    $transport,
                    implode(', ', $supportedTransports)
                )
            );
        }
        $configForSpecifiedTransport = $transportConfig[$transport] ?? [];
        $configForSpecifiedTransport['clientCertSource'] = $clientCertSource;
        switch ($transport) {
            case 'grpc':
                return GrpcTransport::build($apiEndpoint, $configForSpecifiedTransport);
            case 'grpc-fallback':
                return GrpcFallbackTransport::build($apiEndpoint, $configForSpecifiedTransport);
            case 'rest':
                if (! isset($configForSpecifiedTransport['restClientConfigPath'])) {
                    throw new ValidationException(
                        "The 'restClientConfigPath' config is required for 'rest' transport."
                    );
                }
                $restConfigPath = $configForSpecifiedTransport['restClientConfigPath'];

                return RestTransport::build($apiEndpoint, $restConfigPath, $configForSpecifiedTransport);
            default:
                throw new ValidationException(
                    "Unexpected 'transport' option: " . $transport . ". Supported values: ['grpc', 'rest', 'grpc-fallback']"
                );
        }
    }

    private static function defaultTransport()
    {
        return self::getGrpcDependencyStatus()
            ? 'grpc'
            : 'rest';
    }

    private function startCall(
        string $methodName,
        string $decodeType,
        array $optionalArgs = [],
        Message $request = null,
        int $callType = Call::UNARY_CALL,
        string $interfaceName = null
    ) {
        $callStack = $this->createCallStack(
            $this->configureCallConstructionOptions($methodName, $optionalArgs)
        );

        $descriptor = $this->descriptors[$methodName]['grpcStreaming'] ?? null;

        $call = new Call(
            $this->buildMethod($interfaceName, $methodName),
            $decodeType,
            $request,
            $descriptor,
            $callType
        );
        switch ($callType) {
            case Call::UNARY_CALL:
                $this->modifyUnaryCallable($callStack);

                break;
            case Call::BIDI_STREAMING_CALL:
            case Call::CLIENT_STREAMING_CALL:
            case Call::SERVER_STREAMING_CALL:
                $this->modifyStreamingCallable($callStack);

                break;
        }

        return $callStack(
            $call,
            $optionalArgs + array_filter([
                'audience' => self::getDefaultAudience(),
            ])
        );
    }

    private function createCallStack(array $callConstructionOptions): OptionsFilterMiddleware
    {
        $quotaProject = $this->credentialsWrapper->getQuotaProject();
        $fixedHeaders = $this->agentHeader;
        if ($quotaProject) {
            $fixedHeaders += [
                'X-Goog-User-Project' => [$quotaProject],
            ];
        }
        $callStack = function (Call $call, array $options) {
            $startCallMethod = $this->transportCallMethods[$call->getCallType()];

            return $this->transport->$startCallMethod($call, $options);
        };
        $callStack = new CredentialsWrapperMiddleware($callStack, $this->credentialsWrapper);
        $callStack = new FixedHeaderMiddleware($callStack, $fixedHeaders, true);
        $callStack = new RetryMiddleware($callStack, $callConstructionOptions['retrySettings']);

        return new OptionsFilterMiddleware($callStack, [
            'headers',
            'timeoutMillis',
            'transportOptions',
            'metadataCallback',
            'audience',
        ]);
    }

    private function configureCallConstructionOptions(string $methodName, array $optionalArgs): array
    {
        $retrySettings = $this->retrySettings[$methodName];
        // Allow for retry settings to be changed at call time
        if (isset($optionalArgs['retrySettings'])) {
            if ($optionalArgs['retrySettings'] instanceof RetrySettings) {
                $retrySettings = $optionalArgs['retrySettings'];
            } else {
                $retrySettings = $retrySettings->with(
                    $optionalArgs['retrySettings']
                );
            }
        }

        return [
            'retrySettings' => $retrySettings,
        ];
    }

    private function buildMethod(string $interfaceName = null, string $methodName = null): string
    {
        return sprintf(
            '%s/%s',
            $interfaceName ?: $this->serviceName,
            $methodName
        );
    }

    private static function getDefaultAudience(): string|null
    {
        if (! defined('self::SERVICE_ADDRESS')) {
            return null;
        }

        return 'https://' . self::SERVICE_ADDRESS . '/';
    }

    private static function supportedTransports(): array
    {
        return ['grpc', 'grpc-fallback', 'rest'];
    }
}
