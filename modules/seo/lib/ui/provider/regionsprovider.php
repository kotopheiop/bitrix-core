<?php

namespace Bitrix\Seo\UI\Provider;

use Bitrix\Seo\Marketing\Configurator;
use Bitrix\Seo\Marketing\Services\AdCampaignFacebook;
use Bitrix\UI\EntitySelector\Dialog;
use Bitrix\UI\EntitySelector\Item;
use Bitrix\UI\EntitySelector\SearchQuery;

class RegionsProvider extends InterestsProvider
{
    const SEARCH_TYPE = 'adgeolocation';
    const ENTITY_TYPE = 'regions';

    public function doSearch(SearchQuery $searchQuery, Dialog $dialog): void
    {
        $service = Configurator::getService();
        $service->setClientId($this->getOption('clientId'));
        $searchQuery->setCacheable(false);

        $response = Configurator::searchTargetingData(
            AdCampaignFacebook::TYPE_CODE,
            [
                'q' => $searchQuery->getQuery(),
                'type' => static::SEARCH_TYPE
            ]
        );

        $items = [];

        foreach ($response as $value) {
            if (!isset($value['country_code'])) {
                continue;
            }


            $items[] =
                new Item(
                    [
                        'id' => $value['country_code'],
                        'entityId' => static::ENTITY_TYPE,
                        'title' => $value['name'],
                        'tagOptions' => [
                            'bgColor' => "#{$this->stringToColor($value['country_name'])}",
                            'textColor' => "#fff"
                        ]
                    ]
                );
        }

        $dialog->addItems(
            $items
        );
    }
}