<?php

namespace Bitrix\Mail\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\Mail\Internals\MailContactTable;

/**
 * Class AddressBook
 * @package Bitrix\Mail\Controller
 */
class AddressBook extends Controller
{
    private function editContact($contactData)
    {
        $id = $contactData['ID'];
        $userID = MailContactTable::getRow(
            [
                'filter' => ['ID' => $id],
                'select' => ['USER_ID'],
            ]
        )['USER_ID'];

        if (!($this->getCurrentUser()->getId() === $userID &&
            $contactData['NAME'] <> "" &&
            check_email($contactData['EMAIL']))) {
            return false;
        }

        MailContactTable::update(
            $id,
            [
                'NAME' => $contactData['NAME'],
                'EMAIL' => $contactData['EMAIL'],
            ]
        );
    }

    private function isUserAdmin()
    {
        global $USER;
        if (!(is_object($USER) && $USER->IsAuthorized())) {
            return false;
        }

        return (bool)($USER->isAdmin() || $USER->canDoOperation('bitrix24_config'));
    }

    private function checkAccess()
    {
        return (check_bitrix_sessid() && $this->isUserAdmin() && Loader::includeModule('mail'));
    }

    /**
     * @param $idSet
     *
     * @return bool
     * @throws \Exception
     */
    public function removeContactsAction($idSet)
    {
        if (!$this->checkAccess()) {
            return false;
        }

        foreach ($idSet as $id) {
            MailContactTable::delete($id);
        }

        return true;
    }

    /**
     * @param $contactData
     *
     * @return bool
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function saveContactAction($contactData)
    {
        if (!$this->checkAccess()) {
            return false;
        }

        $contactData['EMAIL'] = mb_strtolower($contactData['EMAIL']);

        if (!check_email($contactData['EMAIL'])) {
            return false;
        }

        if ($contactData['ID'] !== null) {
            return $this->editContact($contactData);
        } else {
            $contactsData[] = [
                'USER_ID' => $this->getCurrentUser()->getId(),
                'NAME' => $contactData['NAME'],
                'ICON' => '',
                'EMAIL' => $contactData['EMAIL'],
                'ADDED_FROM' => 'MANUAL',
            ];
            MailContactTable::addContactsBatch($contactsData);
        }

        return true;
    }
}