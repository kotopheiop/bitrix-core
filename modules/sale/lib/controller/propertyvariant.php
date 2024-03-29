<?php


namespace Bitrix\Sale\Controller;


use Bitrix\Main\Engine\Response\DataType\Page;
use Bitrix\Main\Error;
use Bitrix\Sale\Result;

class PropertyVariant extends Controller
{
    //region Actions
    public function getFieldsAction()
    {
        $entity = new \Bitrix\Sale\Rest\Entity\PropertyVariant();
        return [
            'PROPERTY_VARIANT' => $entity->prepareFieldInfos(
                $entity->getFields()
            )
        ];
    }

    public function addAction(array $fields)
    {
        $variant = new \CSaleOrderPropsVariant();
        $variantId = 0;

        $r = $this->existsProperty($fields['ORDER_PROPS_ID']);

        if ($r->isSuccess()) {
            if (!isset($fields['XML_ID']) && $fields['XML_ID'] == '') {
                $fields['XML_ID'] = \Bitrix\Sale\Internals\OrderPropsTable::generateXmlId();
            }

            $variantId = $variant->Add($fields);
            if ((int)$variantId <= 0) {
                if ($ex = self::getApplication()->GetException()) {
                    $r->addError(new Error($ex->GetString(), $ex->GetID()));
                } else {
                    $r->addError(new Error('variant add error', 201550000002));
                }
            }
        }

        if (!$r->isSuccess()) {
            foreach ($r->getErrors() as $error) {
                $this->addError(new Error($error->getMessage(), 201550000003));
            }
            return null;
        } else {
            return ['PROPERTY_VARIANT' => $this->get($variantId)];
        }
    }

    public function updateAction($id, array $fields)
    {
        $variant = new \CSaleOrderPropsVariant();

        $r = $this->exists($id);
        if ($r->isSuccess()) {
            if (empty($fields) == false) {
                if (!$variant->Update($id, $fields)) {
                    if ($ex = self::getApplication()->GetException()) {
                        $r->addError(new Error($ex->GetString(), $ex->GetID()));
                    } else {
                        $r->addError(new Error('variant update error', 201550000004));
                    }
                }
            }
        }

        if ($r->isSuccess()) {
            return ['PROPERTY_VARIANT' => $this->get($id)];
        } else {
            $this->addErrors($r->getErrors());
            return null;
        }
    }

    public function deleteAction($id)
    {
        $variant = new \CSaleOrderPropsVariant();
        $r = $this->exists($id);
        if ($r->isSuccess()) {
            if (!$variant->Delete($id)) {
                if ($ex = self::getApplication()->GetException()) {
                    $r->addError(new Error($ex->GetString(), $ex->GetID()));
                } else {
                    $r->addError(new Error('variant delete error ', 201550000001));
                }
            }
        }

        if ($r->isSuccess()) {
            return true;
        } else {
            $this->addErrors($r->getErrors());
            return null;
        }
    }

    public function getAction($id)
    {
        $r = $this->exists($id);
        if ($r->isSuccess()) {
            return ['PROPERTY_VARIANT' => $this->get($id)];
        } else {
            $this->addErrors($r->getErrors());
            return null;
        }
    }

    public function listAction($select = [], $filter = [], $order = [], $start = 0)
    {
        $result = [];

        $orderPropsVariant = new \CSaleOrderPropsVariant();

        $select = empty($select) ? ['*'] : $select;
        $order = empty($order) ? ['ID' => 'ASC'] : $order;

        $r = $orderPropsVariant->GetList($order, $filter, false, self::getNavData($start), $select);
        while ($l = $r->fetch()) {
            $result[] = $l;
        }

        return new Page(
            'PROPERTY_VARIANTS', $result, function () use ($filter) {
            $orderPropsVariant = new \CSaleOrderPropsValue();

            $list = [];
            $r = $orderPropsVariant->GetList([], $filter);
            while ($l = $r->fetch()) {
                $list[] = $l;
            }

            return count($list);
        }
        );
    }

    //end region

    protected function get($id)
    {
        $orderPropsGroup = new \CSaleOrderPropsVariant();

        return $orderPropsGroup->GetByID($id);
    }

    protected function exists($id)
    {
        $r = new Result();
        if (isset($this->get($id)['ID']) == false) {
            $r->addError(new Error('property variant is not exists', 201540400001));
        }

        return $r;
    }

    protected function existsProperty($id)
    {
        $r = new Result();

        $property = \Bitrix\Sale\Internals\OrderPropsTable::getRow(
            [
                'filter' => [
                    '=ID' => $id
                ]
            ]
        );

        if (is_null($property)) {
            $r->addError(new Error('property id is not exists', 201550000005));
        }

        return $r;
    }
}