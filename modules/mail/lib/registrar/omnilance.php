<?php

namespace Bitrix\Mail\Registrar;

use \Bitrix\Main\Text\Encoding;

class Omnilance extends Registrar
{
    /**
     * Base endpoint url.
     */
    const BASE_ENDPOINT = 'https://api.omnilance.com/v3/';

    /**
     * API key.
     * @var string
     */
    private $apiKey;

    /**
     * Secret key.
     * @var string
     */
    private $secretKey;

    /**
     * Http Client instance.
     * @var \Bitrix\Main\Web\HttpClient
     */
    private $http;

    /**
     * Omnilance constructor.
     * @param string $apiKey API key.
     * @param string $secretKey Secret key.
     */
    public function __construct(string $apiKey, string $secretKey)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->http = new \Bitrix\Main\Web\HttpClient;
        $this->http->setTimeout(5);
    }

    /**
     * Set necessary headers.
     * @param string $endPoint End point.
     * @param string $payLoad Payload.
     * @return void
     */
    private function setHeaders(string $endPoint, string $payLoad): void
    {
        $signature = hash_hmac('sha256', $this->apiKey . $endPoint . $payLoad, $this->secretKey);
        $this->http->setHeader('X-OMNI-APIKEY', $this->apiKey);
        $this->http->setHeader('X-OMNI-SIGNATURE', $signature);
        $this->http->setHeader('content-type', 'application/json');
    }

    /**
     * Sends command.
     * @param string $endPoint End point.
     * @param string $payLoad Payload.
     * @return string
     */
    private function sendPostCommand(string $endPoint, string $payLoad): string
    {
        $endPoint = $this::BASE_ENDPOINT . $endPoint;
        $this->setHeaders($endPoint, $payLoad);
        $this->http->post($endPoint, $payLoad);
        return $this->http->getResult();
    }

    /**
     * Checks domain available.
     * @param string $user User name.
     * @param string $password User password.
     * @param string $domain Domain name.
     * @param string|null &$error Error message if occurred.
     * @return bool|null Returns true if domain exists.
     */
    public static function checkDomain(string $user, string $password, string $domain, ?string &$error): ?bool
    {
        $domain = mb_strtolower($domain);
        $domain = Encoding::convertEncoding($domain, SITE_CHARSET, 'UTF-8');

        $omnilance = new self($user, $password);

        $payLoad = json_encode(
            [
                'domainNames' => [
                    $domain
                ]
            ]
        );
        $res = $omnilance->sendPostCommand('domains/checkAvailability', $payLoad);
        $res = json_decode($res, true);

        if (isset($res['error'])) {
            $error = $res['message'] ?? $res['error'];
            return null;
        }

        if (isset($res['results']) && is_array($res['results'])) {
            foreach ($res['results'] as $item) {
                if ($item['domainName'] == $domain) {
                    if ($item['status'] == 'registered') {
                        return true;
                    }
                    break;
                }
            }
        }

        return false;
    }

    /**
     * Suggests domains by query words.
     * @param string $user User name.
     * @param string $password User password.
     * @param string $word1 Query word 1.
     * @param string $word2 Query word 2.
     * @param array $tlds Query tlds.
     * @param string|null &$error Error message if occurred.
     * @return array|null
     */
    public static function suggestDomain(
        string $user,
        string $password,
        string $word1,
        string $word2,
        array $tlds,
        ?string &$error
    ): ?array {
        // method is not allowed by provider
        return null;
    }

    /**
     * Creates new domain.
     * @param string $user User name.
     * @param string $password User password.
     * @param string $domain Domain name.
     * @param array $params Additional params.
     * @param string|null &$error Error message if occurred.
     * @return bool|null Returns true on success.
     */
    public static function createDomain(
        string $user,
        string $password,
        string $domain,
        array $params,
        ?string &$error
    ): ?bool {
        $domain = mb_strtolower($domain);
        $domain = Encoding::convertEncoding($domain, SITE_CHARSET, 'UTF-8');

        $payLoad = json_encode(
            [
                'domain' => [
                    'domainName' => $domain,
                    'privacyEnabled' => true
                ],
                'years' => 1
            ]
        );

        $omnilance = new self($user, $password);
        $res = $omnilance->sendPostCommand('domains/createDomain', $payLoad);
        $res = json_decode($res, true);

        if (isset($res['error'])) {
            $error = $res['message'] ?? $res['error'];
            return null;
        }

        return true;
    }

    /**
     * Updates domain DNS.
     * @param string $user User name.
     * @param string $password User password.
     * @param string $domain Domain name.
     * @param array $params Additional params.
     * @param string|null &$error Error message if occurred.
     * @return bool|null Returns true on success.
     */
    public static function updateDns(
        string $user,
        string $password,
        string $domain,
        array $params,
        ?string &$error
    ): ?bool {
        $error = null;
        $domain = mb_strtolower($domain);
        $domain = Encoding::convertEncoding($domain, SITE_CHARSET, 'UTF-8');
        $params = Encoding::convertEncoding($params, SITE_CHARSET, 'UTF-8');

        $first = true;
        foreach ($params as $dns) {
            $payLoad = [
                'type' => (isset($dns['type']) && is_string($dns['type'])) ? strtoupper($dns['type']) : null,
                'name' => (isset($dns['name']) && is_string($dns['name'])) ? $dns['name'] : '',
                'value' => isset($dns['value']) ? [$dns['value']] : [],
                'ttl' => 3600
            ];
            $payLoad = json_encode($payLoad);

            $omnilance = new self($user, $password);

            if ($first) {
                $res = $omnilance->sendPostCommand('domains/createZoneRecord/' . $domain, $payLoad);
            } else {
                $res = $omnilance->sendPostCommand('domains/createDnsRecord/' . $domain, $payLoad);
            }

            $res = json_decode($res, true);

            if (isset($res['error'])) {
                $error = $res['message'] ?? $res['error'];
            }
            $first = false;
        }

        return $error === null;
    }
}