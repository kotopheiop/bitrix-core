<?

namespace Bitrix\Sale\Delivery\Pecom;

class Replacement
{
    public static function getRegionExceptions()
    {
        return array(
            '������' => '���������� �������',
            '�����-���������' => '������������� �������',
        );
    }

    public static function getDistrictMark()
    {
        return '�\-�';
    }
}