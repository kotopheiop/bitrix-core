<?

namespace Sale\Handlers\Delivery\Additional\Location;

class Replacement
{
    public static function getLocalityTypes()
    {
        return array(
            '��Ѩ��� ���������� ����' => array('���'),
            '��Ѩ���' => array('�', '���', '�������'),
            '���' => array('���'),
            '����' => array('����', 'C'),
            '�����' => array('�����', '�'),
            '�������' => array('�������', '�', '���'),
            '�������' => array('�������', '��-��', '����'),
            '���' => array(),
            '������ ��Ѩ���' => array(),
            '������� ��Ѩ���' => array(),
            '����˨���� �����' => array(),
            '����������' => array(),
            '�������' => array(),
            '��������' => array(),
            '��������������� �������' => array(),
            '�������� ���������' => array(),
            '�������� ���������' => array(),
            '��������' => array(),
            '���������' => array()
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

    public static function getRegionExceptions()
    {
        return array(
            '�������' => '���������',
            '������' => '���������� �������',
            '�����-���������' => '������������� �������',
            '��������' => '����������'
        );
    }

    public static function getDistrictTypes()
    {
        return array(
            '�����' => array('�-�', '�-��')
        );
    }

    public static function getNameRussia()
    {
        return '������';
    }

    public static function getCountryName()
    {
        return self::getNameRussia();
    }
}