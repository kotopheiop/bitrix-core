<?

namespace Bitrix\Seo\Retargeting;

use Bitrix\Main\InvalidOperationException;
use Bitrix\Seo\Engine\Bitrix as EngineBitrix;

class ProxyRequest extends Request
{
    const REST_METHOD_PREFIX = '';

    /**
     * Request through cloud-adv service
     *
     * @param array $params Request params.
     * @return array|bool
     * @throws \Bitrix\Main\SystemException
     */
    public function query(array $params = array())
    {
        if ($this->useDirectQuery) {
            return $this->directQuery($params);
        }

        $methodName = static::REST_METHOD_PREFIX . '.' . $params['methodName'];
        $parameters = $params['parameters'];
        $engine = new EngineBitrix();
        if (!$engine->isRegistered()) {
            return false;
        }
        $parameters['proxy_client_id'] = $this->getAuthAdapter()->getClientId();
        $parameters['lang'] = LANGUAGE_ID;

        $transport = $engine->getInterface()->getTransport();
        if ($params['timeout']) {
            $transport->setTimeout($params['timeout']);
        }
        $response = $transport->call($methodName, $parameters);
        if ($response['result']['RESULT']) {
            return $response['result']['RESULT'];
        }
        if ($response['error']) {
            throw new InvalidOperationException($response['error_description'] ? $response['error_description'] : $response['error']);
        }
        return [];
    }
}