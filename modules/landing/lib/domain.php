<?php

namespace Bitrix\Landing;

use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Context;
use \Bitrix\Main\Web\Uri;

class Domain extends \Bitrix\Landing\Internals\BaseTable
{
    /**
     * Bitrix24 domains.
     */
    const B24_DOMAINS = [
        'bitrix24.site',
        'bitrix24.shop',
        'bitrix24site.by',
        'bitrix24shop.by',
        'bitrix24site.ua'
    ];

    /**
     * Internal class.
     * @var string
     */
    public static $internalClass = 'DomainTable';

    /**
     * Gets domain name.
     * @return string.
     */
    protected static function getDomainName()
    {
        static $domain = null;

        if (!$domain) {
            $context = \Bitrix\Main\Application::getInstance()->getContext();
            $server = $context->getServer();
            $domain = $server->getServerName();
            if (!$domain) {
                $domain = $server->getHttpHost();
            }
        }

        return $domain;
    }

    /**
     * Returns Bitrix24 sub domain name from full domain name.
     * @param string $domainName Full domain name.
     * @return string Null, if $domainName isn't sub domain of B24.
     */
    public static function getBitrix24Subdomain($domainName)
    {
        $re = '/^([^\.]+)\.(' . implode('|', self::B24_DOMAINS) . ')$/i';
        if (preg_match($re, $domainName, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Returns postfix for bitrix24.site.
     * @param string $type Site type.
     * @return string
     */
    public static function getBitrix24Postfix(string $type): string
    {
        $zone = Manager::getZone();
        $postfix = '.bitrix24.site';
        $type = mb_strtoupper($type);

        if ($type == 'STORE') {
            $postfix = ($zone == 'by')
                ? '.bitrix24shop.by'
                : '.bitrix24.shop';
        } else {
            if ($zone == 'by') {
                $postfix = '.bitrix24site.by';
            } else {
                if ($zone == 'ua') {
                    $postfix = '.bitrix24site.ua';
                }
            }
        }

        return $postfix;
    }

    /**
     * Create current domain and return new id..
     * @return int
     */
    public static function createDefault()
    {
        $res = self::add(
            array(
                'ACTIVE' => 'Y',
                'DOMAIN' => self::getDomainName()
            )
        );
        if ($res->isSuccess()) {
            return $res->getId();
        }

        return false;
    }

    /**
     * Get current domain id.
     * @return int
     */
    public static function getCurrentId()
    {
        $res = self::getList(
            array(
                'filter' => array(
                    '=ACTIVE' => 'Y',
                    'DOMAIN' => self::getDomainName()
                )
            )
        );
        if ($row = $res->fetch()) {
            return $row['ID'];
        } else {
            return self::createDefault();
        }
    }

    /**
     * Get available protocol list.
     * @return array
     */
    public static function getProtocolList()
    {
        return \Bitrix\Landing\Internals\DomainTable::getProtocolList();
    }

    /**
     * Gets current host url.
     * @return string
     */
    public static function getHostUrl()
    {
        static $hostUrl = null;

        if ($hostUrl !== null) {
            return $hostUrl;
        }

        $request = Context::getCurrent()->getRequest();
        $protocol = ($request->isHttps() ? 'https://' : 'http://');

        if (defined('SITE_SERVER_NAME') && SITE_SERVER_NAME) {
            $host = SITE_SERVER_NAME;
        } else {
            $host = Option::get('main', 'server_name', $request->getHttpHost());
        }

        $hostUrl = rtrim($protocol . $host, '/');

        return $hostUrl;
    }

    /**
     * Returns top level domain by domain name.
     * @param string $domainName Domain name.
     * @return string
     */
    public static function getTLD(string $domainName): string
    {
        $domainName = mb_strtolower(trim($domainName));
        $domainNameParts = explode('.', $domainName);
        $domainNameTld = $domainNameParts[count($domainNameParts) - 1];

        if ($domainNameParts[count($domainNameParts) - 2] == 'com') {
            $domainNameTld = 'com.' . $domainNameTld;
        }

        return $domainNameTld;
    }
}
