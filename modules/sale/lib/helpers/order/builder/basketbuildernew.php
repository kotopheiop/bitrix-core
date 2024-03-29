<?

namespace Bitrix\Sale\Helpers\Order\Builder;

use Bitrix\Sale\BasketItem;
use Bitrix\Sale\Fuser;
use Bitrix\Sale;

class BasketBuilderNew implements IBasketBuilderDelegate
{
    protected $builder = null;

    public function __construct(BasketBuilder $builder)
    {
        $this->builder = $builder;

        $registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);

        /** @var Sale\Basket $basketClass */
        $basketClass = $registry->getBasketClassName();

        $basket = $basketClass::create($this->builder->getOrder()->getSiteId());
        $res = $this->builder->getOrder()->setBasket($basket);
        if (!$res->isSuccess()) {
            $this->builder->getErrorsContainer()->addErrors($res->getErrors());
            throw  new BuildingException();
        }
        $fUserId = null;

        if ($this->builder->getOrder()->getUserId() > 0) {
            $fUserId = Fuser::getIdByUserId($this->builder->getOrder()->getUserId());
        }

        $basket->setFUserId($fUserId);
    }

    public function getItemFromBasket($basketCode, $productData)
    {
        if (empty($productData['MANUALLY_EDITED'])) {
            $item = $this->builder->getBasket()->getExistsItem(
                $productData["MODULE"],
                $productData["OFFER_ID"],
                $productData["PROPS"]
            );
        } else {
            $item = $this->builder->getBasket()->getItemByBasketCode($basketCode);
        }

        if ($item == null && $basketCode != BasketBuilder::BASKET_CODE_NEW) {
            $item = $this->builder->getBasket()->getItemByBasketCode($basketCode);
        }

        return $item;
    }

    /**
     * @param string $basketCode
     * @param array $productData
     * @param BasketItem $item
     */
    public function setItemData($basketCode, &$productData, &$item)
    {
        //Let's extract cached provider product data from field
        if (!empty($productData["PROVIDER_DATA"]) && CheckSerializedData($productData["PROVIDER_DATA"])) {
            if ($providerData = unserialize($productData["PROVIDER_DATA"], ['allowed_classes' => false])) {
                $this->builder->sendProductCachedDataToProvider($item, $this->builder->getOrder(), $providerData);
            }
        }

        if (!empty($productData["SET_ITEMS_DATA"]) && CheckSerializedData($productData["SET_ITEMS_DATA"])) {
            $productData["SET_ITEMS"] = unserialize($productData["SET_ITEMS_DATA"], ['allowed_classes' => false]);
        }

        $res = $item->setField("QUANTITY", $item->getField("QUANTITY") + $productData["QUANTITY"]);

        if (!$res->isSuccess()) {
            $this->builder->getErrorsContainer()->addErrors($res->getErrors());
            throw  new BuildingException();
        }
    }

    public function finalActions()
    {
        //not needed yet for new orders
    }
}