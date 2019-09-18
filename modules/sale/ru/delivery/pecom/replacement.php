<?
namespace Bitrix\Sale\Delivery\Pecom;

class Replacement
{
	public static function getRegionExceptions()
	{
		return array(
			'лняйбю' => 'лняйнбяйюъ накюярэ',
			'яюмйр-оерепаспц' => 'кемхмцпюдяйюъ накюярэ',
		);
	}

	public static function getDistrictMark()
	{
		return 'п\-м';
	}
}