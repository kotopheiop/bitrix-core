<?

namespace Bitrix\Sale\Location\Comparator;

class Replacement
{
    public static function getLocalityTypes()
    {
        return array(
            '������� ��Ѩ���' => array(),
            '��Ѩ��� ���������� ����' => array('���'),
            '��Ѩ���' => array('�', '���', '�������'),
            '���' => array(),
            '����' => array('C'),
            '�����' => array('�'),
            '�������' => array('�', '���'),
            '�������' => array('��-��', '����')
        );
    }

    public static function getRegionTypes()
    {
        return array(
            '�������' => array('���'),
            '���������� �����' => array('��', '��� �����'),
            '����������' => array('����')
        );
    }

    public static function getRegionVariants()
    {
        return array(
            '�������' => '���������',
            '������' => '���������� �������',
            '�����-���������' => '������������� �������',
            '��������' => '����������',
            '���� /������/ ����' => '���������� ���� (������)',
            '�����-���������� ���������� ����� - ���� ��' => '�����-���������� ���������� �����',
            '��������� ����' => '��������� ���������� �������'
        );
    }

    public static function getCountryVariants()
    {
        return array(
            '��' => '������',
            '���������� ���������' => '������'
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
                '��',
                '���������� ���������',
                '������'
            )
        );
    }

    public static function getDistrictTypes()
    {
        return array(
            '�����' => array('�-�', '�-��')
        );
    }

    public static function changeYoE($string)
    {
        return str_replace('�', '�', $string);
    }
}