<?php
namespace Bitrix\Sale\Exchange;

interface ISettings
{
	public static function getCurrent();

    /**
     * @param $typeId
     * @return string
     */
	public function prefixFor($typeId);
}

interface ISettingsExport extends ISettings
{
	/**
	 * @return string
	 */
	public function getSiteId();

	/**
	 * @return string
	 */
	public function getReplaceCurrency();

	/**
	 * @param $entityTypeId
	 * @return string
	 */
	public function groupPermissionFor($entityTypeId);

	/**
	 * @param $entityTypeId
	 * @return string
	 */
	public function finalStatusFor($entityTypeId);

	/**
	 * @param $entityTypeId
	 * @return string
	 */
	public function payedFor($entityTypeId);

	/**
	 * @param $entityTypeId
	 * @return string
	 */
	public function allowDeliveryFor($entityTypeId);
}

interface ISettingsImport extends ISettings
{
    /**
     * @param $entityTypeId
     * @return bool
     */
    public function isImportableFor($entityTypeId);

    /**
     * @param $entityTypeId
     * @return int
     */
    public function paySystemIdFor($entityTypeId);

    /**
     * @param $entityTypeId
     * @return int
     */
    public function shipmentServiceFor($entityTypeId);

    /**
     * @return string
     */
    public function getSiteId();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param $typeId
     * @return string
     */
	public function canCreateOrder($typeId);

    /**
     * @param $typeId
     * @return string
     */
	public function finalStatusIdFor($typeId);

    /**
     * @param $typeId
     * @return string
     */
	public function finalStatusOnDeliveryFor($typeId);

    /**
     * @param $typeId
     * @return string
     */
	public function changeStatusFor($typeId);
}