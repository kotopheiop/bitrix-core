<?
namespace Sale\Handlers\Delivery\Additional\Location;

class Replacement
{
	public static function getLocalityTypes()
	{
		return array(
			'ÏÎÑ¨ËÎÊ ÃÎÐÎÄÑÊÎÃÎ ÒÈÏÀ' => array('ÏÃÒ'),
			'ÏÎÑ¨ËÎÊ' => array('Ï', 'ÏÎÑ', 'ÏÎÑÅËÎÊ'),
			'ÀÓË' => array('ÀÓË'),
			'ÑÅËÎ' => array('ÑÅËÎ', 'C'),
			'ÕÓÒÎÐ' => array('ÕÓÒÎÐ', 'Õ'),
			'ÄÅÐÅÂÍß' => array('ÄÅÐÅÂÍß', 'Ä', 'ÄÅÐ'),
			'ÑÒÀÍÈÖÀ' => array('ÑÒÀÍÈÖÀ', 'ÑÒ-ÖÀ', 'ÑÒÀÍ'),
			'ÑÍÒ' => array(),
			'ÄÀ×ÍÛÉ ÏÎÑ¨ËÎÊ' => array(),
			'ÐÀÁÎ×ÈÉ ÏÎÑ¨ËÎÊ' => array(),
			'ÍÀÑÅË¨ÍÍÛÉ ÏÓÍÊÒ' => array(),
			'ÌÈÊÐÎÐÀÉÎÍ' => array(),
			'ÑËÎÁÎÄÀ' => array(),
			'ÆÈËÐÀÉÎÍ' => array(),
			'ÆÅËÅÇÍÎÄÎÐÎÆÍÀß ÑÒÀÍÖÈß' => array(),
			'ÏÎ×ÒÎÂÎÅ ÎÒÄÅËÅÍÈÅ' => array(),
			'ÑÅËÜÑÊÎÅ ÏÎÑÅËÅÍÈÅ' => array(),
			'ÌÅÑÒÅ×ÊÎ' => array(),
			'ÑÅËÜÑÎÂÅÒ' => array()
		);
	}

	public static function getRegionTypes()
	{
		return array(
			'ÎÁËÀÑÒÜ' => array('ÎÁË'),
			'ÀÂÒÎÍÎÌÍÛÉ ÎÊÐÓÃ' => array('ÀÎ', 'ÀÂÒ ÎÊÐÓÃ'),
			'ÐÅÑÏÓÁËÈÊÀ' => array('ÐÅÑÏ')
		);
	}

	public static function getRegionExceptions()
	{
		return array(
			'×ÓÂÀØÈß' => '×ÓÂÀØÑÊÀß',
			'ÌÎÑÊÂÀ' => 'ÌÎÑÊÎÂÑÊÀß ÎÁËÀÑÒÜ',
			'ÑÀÍÊÒ-ÏÅÒÅÐÁÓÐÃ' => 'ËÅÍÈÍÃÐÀÄÑÊÀß ÎÁËÀÑÒÜ',
			'ÓÄÌÓÐÒÈß' => 'ÓÄÌÓÐÒÑÊÀß'
		);
	}

	public static function getDistrictTypes()
	{
		return array(
			'ÐÀÉÎÍ' => array('Ð-Í', 'Ð-ÎÍ')
		);
	}

	public static function getNameRussia()
	{
		return 'ÐÎÑÑÈß';
	}

	public static function getCountryName()
	{
		return self::getNameRussia();
	}
}