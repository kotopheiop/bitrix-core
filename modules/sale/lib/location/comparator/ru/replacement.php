<?
namespace Bitrix\Sale\Location\Comparator;

class Replacement
{
	public static function getLocalityTypes()
	{
		return array(
			'ĞÀÁÎ×ÈÉ ÏÎÑ¨ËÎÊ' => array(),
			'ÏÎÑ¨ËÎÊ ÃÎĞÎÄÑÊÎÃÎ ÒÈÏÀ' => array('ÏÃÒ'),
			'ÏÎÑ¨ËÎÊ' => array('Ï', 'ÏÎÑ', 'ÏÎÑÅËÎÊ'),
			'ÀÓË' => array(),
			'ÑÅËÎ' => array('C'),
			'ÕÓÒÎĞ' => array('Õ'),
			'ÄÅĞÅÂÍß' => array('Ä', 'ÄÅĞ'),
			'ÑÒÀÍÈÖÀ' => array('ÑÒ-ÖÀ', 'ÑÒÀÍ')
		);
	}

	public static function getRegionTypes()
	{
		return array(
			'ÎÁËÀÑÒÜ' => array('ÎÁË'),
			'ÀÂÒÎÍÎÌÍÛÉ ÎÊĞÓÃ' => array('ÀÎ', 'ÀÂÒ ÎÊĞÓÃ'),
			'ĞÅÑÏÓÁËÈÊÀ' => array('ĞÅÑÏ')
		);
	}

	public static function getRegionVariants()
	{
		return array(
			'×ÓÂÀØÈß' => '×ÓÂÀØÑÊÀß',
			'ÌÎÑÊÂÀ' => 'ÌÎÑÊÎÂÑÊÀß ÎÁËÀÑÒÜ',
			'ÑÀÍÊÒ-ÏÅÒÅĞÁÓĞÃ' => 'ËÅÍÈÍÃĞÀÄÑÊÀß ÎÁËÀÑÒÜ',
			'ÓÄÌÓĞÒÈß' => 'ÓÄÌÓĞÒÑÊÀß',
			'ÑÀÕÀ /ßÊÓÒÈß/ ĞÅÑÏ' => 'ĞÅÑÏÓÁËÈÊÀ ÑÀÕÀ (ßÊÓÒÈß)',
			'ÕÀÍÒÛ-ÌÀÍÑÈÉÑÊÈÉ ÀÂÒÎÍÎÌÍÛÉ ÎÊĞÓÃ - ŞÃĞÀ ÀÎ' => 'ÕÀÍÒÛ-ÌÀÍÑÈÉÑÊÈÉ ÀÂÒÎÍÎÌÍÛÉ ÎÊĞÓÃ',
			'ÅÂĞÅÉÑÊÀß ÀÎÁË' => 'ÅÂĞÅÉÑÊÀß ÀÂÒÎÍÎÌÍÀß ÎÁËÀÑÒÜ'
		);
	}

	public static function getCountryVariants()
	{
		return array(
			'ĞÔ' => 'ĞÎÑÑÈß',
			'ĞÎÑÑÈÉÑÊÀß ÔÅÄÅĞÀÖÈß' => 'ĞÎÑÑÈß'
		);
	}

	public static function isCountryRussia($countryName)
	{
		return in_array(
			ToUpper(
				trim(
					$countryName
				)
			),
			array(
				'ĞÔ',
				'ĞÎÑÑÈÉÑÊÀß ÔÅÄÅĞÀÖÈß',
				'ĞÎÑÑÈß'
			)
		);
	}

	public static function getDistrictTypes()
	{
		return array(
			'ĞÀÉÎÍ' => array('Ğ-Í', 'Ğ-ÎÍ')
		);
	}

	public static function changeYoE($string)
	{
		return str_replace('¨', 'Å', $string);
	}
}