<?php

namespace Botble\Analytics;

use Google\Client;
use LogicException;

class GoogleClient extends Client
{
    /**
     * Set the auth config from new or deprecated JSON config.
     * This structure should match the file downloaded from
     * the "Download JSON" button on in the Google Developer
     * Console.
     * @param string|array $config the configuration json
     */
    public function setAuthConfig($config): void
    {
        if (is_string($config)) {
            if (! $config = json_decode($config, true)) {
                throw new LogicException('Invalid data for auth config');
            }
        }

        $key = isset($config['installed']) ? 'installed' : 'web';
        if (isset($config['type']) && $config['type'] == 'service_account') {
            // application default credentials
            $this->useApplicationDefaultCredentials();

            // set the information from the config
            $this->setClientId($config['client_id']);
            $this->setConfig('client_email', $config['client_email']);
            $this->setConfig('signing_key', $config['private_key']);
            $this->setConfig('signing_algorithm', 'HS256');
        } elseif (isset($config[$key])) {
            // old-style
            $this->setClientId($config[$key]['client_id']);
            $this->setClientSecret($config[$key]['client_secret']);
            if (isset($config[$key]['redirect_uris'])) {
                $this->setRedirectUri($config[$key]['redirect_uris'][0]);
            }
        } elseif (is_array($config) && isset($config['client_id']) && isset($config['client_secret'])) {
            // new-style
            $this->setClientId($config['client_id']);
            $this->setClientSecret($config['client_secret']);
            if (isset($config['redirect_uris'])) {
                $this->setRedirectUri($config['redirect_uris'][0]);
            }
        }
    }
}
