<?php

namespace Bitrix\Bizproc\Script;

use Bitrix\Bizproc\Script\Entity\EO_ScriptQueue;
use Bitrix\Bizproc\Script\Entity\ScriptQueueDocumentTable;
use Bitrix\Bizproc\Script\Entity\ScriptQueueTable;
use Bitrix\Bizproc\Workflow\Template\Packer\RoboPackage;
use Bitrix\Bizproc\Workflow\Template\SourceType;
use Bitrix\Main;
use Bitrix\Bizproc\Workflow\Template\Entity\WorkflowTemplateTable;
use Bitrix\Main\Localization\Loc;

class Manager
{
    public static function getListByDocument(array $documentType, $showInactive = false)
    {
        $filter = [
            '=MODULE_ID' => $documentType[0],
            '=ENTITY' => $documentType[1],
            '=DOCUMENT_TYPE' => $documentType[2],
        ];

        if (!$showInactive) {
            $filter['=ACTIVE'] = 'Y';
        }

        $list = Entity\ScriptTable::getList(
            [
                'select' => [
                    'ID',
                    'NAME',
                    'ORIGINATOR_ID',
                    'ORIGIN_ID',
                    'MODULE_ID',
                    'ENTITY',
                    'DOCUMENT_TYPE',
                    'NAME',
                    'DESCRIPTION',
                ],
                'filter' => $filter,
                'order' => [
                    'SORT' => 'ASC',
                    'NAME' => 'ASC'
                ],
            ]
        )->fetchAll();

        return $list;
    }

    public static function getById($id)
    {
        $script = Entity\ScriptTable::getList(
            [
                'select' => ['*'],
                'filter' => [
                    '=ID' => $id,
                ],
            ]
        )->fetchObject();

        return $script;
    }

    public static function getQueueById(int $queueId): ?EO_ScriptQueue
    {
        return Entity\ScriptQueueTable::getById($queueId)->fetchObject();
    }

    public static function saveScript(int $id, array $documentType, array $fields, $userId = null)
    {
        if ($id > 0) {
            $result = self::updateScript($id, $fields['script'], $fields['robotsTemplate'], $userId);
        } else {
            $result = self::addScript($documentType, $fields['script'], $fields['robotsTemplate'], $userId);
        }

        if ($result->isSuccess()) {
            self::clearMenuCache();
        }

        return $result;
    }

    public static function createScript(array $documentType)
    {
        $fields = [
            'ID' => 0,
            'MODULE_ID' => $documentType[0],
            'ENTITY' => $documentType[1],
            'DOCUMENT_TYPE' => $documentType[2],
            'NAME' => Loc::getMessage("BIZPROC_SCRIPT_MANAGER_NEW_SCRIPT"),
            'DESCRIPTION' => Loc::getMessage("BIZPROC_SCRIPT_MANAGER_NEW_DESCRIPTION"),
        ];

        return $fields;
    }

    private static function updateScript($id, array $scriptFields, array $templateFields, $userId): Main\Result
    {
        $script = static::getById($id);

        if (!$script) {
            $result = new Main\Result();
            $result->addError(new Main\Error('Script not found'));

            return $result;
        }

        $wl = array_fill_keys(['NAME', 'DESCRIPTION'], true);

        $scriptFields = array_intersect_key($scriptFields, $wl);
        $scriptFields['MODIFIED_BY'] = $userId;
        $scriptFields['MODIFIED_DATE'] = new Main\Type\DateTime();

        $scriptUpdateResult = Entity\ScriptTable::update($script['ID'], $scriptFields);

        if (!$scriptUpdateResult->isSuccess()) {
            return $scriptUpdateResult;
        }

        $documentType = [$script['MODULE_ID'], $script['ENTITY'], $script['DOCUMENT_TYPE']];

        $template = null;
        $tpl = WorkflowTemplateTable::getById($script['WORKFLOW_TEMPLATE_ID'])->fetchObject();
        if ($tpl) {
            $template = \Bitrix\Bizproc\Automation\Engine\Template::createByTpl($tpl);
        }

        if (!$template) {
            $template = new \Bitrix\Bizproc\Automation\Engine\Template($documentType);
        }

        $template->setName($scriptFields['NAME'] ?? $script->getName());
        $robots = isset($templateFields['ROBOTS']) && is_array(
            $templateFields['ROBOTS']
        ) ? $templateFields['ROBOTS'] : [];

        $result = $template->save($robots, $userId);

        if ($result->isSuccess()) {
            self::saveTemplateConfigs($template->getId(), $templateFields);
        }

        return $result;
    }

    private static function addScript(
        array $documentType,
        array $scriptFields,
        array $templateFields,
        $userId
    ): Main\Result {
        $templateFields['NAME'] = $scriptFields['NAME'];
        $result = self::addWorkflowTemplate($documentType, $templateFields, $userId);

        if (!$result->isSuccess()) {
            return $result;
        }

        return self::addScriptRecord(
            $documentType,
            [
                'WORKFLOW_TEMPLATE_ID' => $result->getData()['ID'],
                'NAME' => $scriptFields['NAME'],
                'DESCRIPTION' => $scriptFields['DESCRIPTION'],
            ],
            $userId
        );
    }

    private static function addWorkflowTemplate(
        array $documentType,
        array $templateFields,
        int $userId,
        $extractParameters = true
    ) {
        $template = new \Bitrix\Bizproc\Automation\Engine\Template($documentType);
        $robots = isset($templateFields['ROBOTS']) && is_array(
            $templateFields['ROBOTS']
        ) ? $templateFields['ROBOTS'] : [];
        $template->setDocumentStatus('SCRIPT');
        $template->setName($templateFields['NAME']);
        $template->setExecuteType(\CBPDocumentEventType::Script);

        $result = $template->save($robots, $userId);

        if (!$result->isSuccess()) {
            return $result;
        }

        self::saveTemplateConfigs($template->getId(), $templateFields, $extractParameters);

        $result->setData(['ID' => $template->getId()]);

        return $result;
    }

    private static function addScriptRecord(array $documentType, array $scriptFields, int $userId)
    {
        $addFields = [
            'MODULE_ID' => $documentType[0],
            'ENTITY' => $documentType[1],
            'DOCUMENT_TYPE' => $documentType[2],
            'WORKFLOW_TEMPLATE_ID' => $scriptFields['WORKFLOW_TEMPLATE_ID'],
            'NAME' => $scriptFields['NAME'],
            'DESCRIPTION' => $scriptFields['DESCRIPTION'],
            'CREATED_BY' => $userId,
            'CREATED_DATE' => new Main\Type\DateTime(),
            'MODIFIED_BY' => $userId,
            'MODIFIED_DATE' => new Main\Type\DateTime(),
        ];

        if (isset($scriptFields['ORIGINATOR_ID']) && isset($scriptFields['ORIGIN_ID'])) {
            $addFields['ORIGINATOR_ID'] = $scriptFields['ORIGINATOR_ID'];
            $addFields['ORIGIN_ID'] = $scriptFields['ORIGIN_ID'];
        }

        return Entity\ScriptTable::add($addFields);
    }

    private static function saveTemplateConfigs(int $tplId, array $templateFields, $extractParameters = true)
    {
        $tpl = WorkflowTemplateTable::getById($tplId)->fetchObject();
        if (!$tpl) {
            return false;
        }

        $constants = $templateFields['CONSTANTS'] ?? [];
        $parameters = $templateFields['PARAMETERS'] ?? [];

        $usages = $tpl->collectUsages();

        $usedConstants = $usages->getValuesBySourceType(SourceType::Constant);
        $usedParameters = $usages->getValuesBySourceType(SourceType::Parameter);

        $constants = array_intersect_key($constants, array_fill_keys($usedConstants, true));
        $parameters = array_intersect_key($parameters, array_fill_keys($usedParameters, true));

        if ($extractParameters) {
            $constants = self::extractProperties($constants, $tpl->getDocumentComplexType());
            $parameters = self::extractProperties($parameters, $tpl->getDocumentComplexType());
        }

        \CBPWorkflowTemplateLoader::update(
            $tplId,
            ['CONSTANTS' => $constants, 'PARAMETERS' => $parameters],
            true
        );
    }

    private static function extractProperties(array $properties, array $documentType): array
    {
        $documentService = \CBPRuntime::GetRuntime(true)->getDocumentService();

        foreach ($properties as $i => $property) {
            $properties[$i]['Default'] = $documentService->GetFieldInputValue(
                $documentType,
                $property,
                'field',
                ['field' => $property['Default']],
                $errors
            );
        }

        return $properties;
    }

    public static function deleteScript($id): Main\Result
    {
        $script = static::getById($id);
        $result = new Main\Result();

        if (!$script) {
            $result->addError(new Main\Error('Script not found'));

            return $result;
        }

        try {
            \CBPWorkflowTemplateLoader::getLoader()->deleteTemplate($script['WORKFLOW_TEMPLATE_ID']);
            Entity\ScriptTable::delete($id);
            Entity\ScriptQueueTable::deleteByScript($id);

            self::clearMenuCache();
        } catch (\Exception $e) {
            $result->addError(new Main\Error($e->getMessage()));
        }

        return $result;
    }

    public static function activateScript($id): Main\Result
    {
        $script = static::getById($id);
        $result = new Main\Result();

        if (!$script) {
            $result->addError(new Main\Error('Script not found'));

            return $result;
        }

        $script->setActive('Y');
        $script->save();
        self::clearMenuCache();

        return $result;
    }

    public static function deactivateScript($id): Main\Result
    {
        $script = static::getById($id);
        $result = new Main\Result();

        if (!$script) {
            $result->addError(new Main\Error('Script not found'));

            return $result;
        }

        $script->setActive('N');
        $script->save();
        self::clearMenuCache();

        return $result;
    }

    public static function terminateQueue(int $queueId, int $userId)
    {
        ScriptQueueTable::markTerminated($queueId, $userId);
    }

    public static function deleteQueue(int $queueId, int $userId)
    {
        ScriptQueueTable::delete($queueId);
        ScriptQueueDocumentTable::deleteByQueue($queueId);
    }

    public static function canUserStartScript(int $scriptId, int $userId): bool
    {
        $user = new \CBPWorkflowTemplateUser($userId);

        return $user->isAdmin();
    }

    public static function canUserEditScript(int $scriptId, int $userId): bool
    {
        $user = new \CBPWorkflowTemplateUser($userId);

        return $user->isAdmin();
    }

    public static function canUserCreateScript(array $documentType, int $userId): bool
    {
        $user = new \CBPWorkflowTemplateUser($userId);

        return $user->isAdmin();
    }

    public static function exportScript(int $scriptId): ?array
    {
        $script = static::getById($scriptId);
        if (!$script) {
            return null;
        }

        $exportData = [
            'MODULE_ID' => $script->getModuleId(),
            'ENTITY' => $script->getEntity(),
            'DOCUMENT_TYPE' => $script->getDocumentType(),
            'NAME' => $script->getName(),
            'DESCRIPTION' => $script->getDescription(),
            'ORIGINATOR_ID' => $script->getOriginatorId(),
            'ORIGIN_ID' => $script->getOriginId(),
        ];

        $script->fillWorkflowTemplate();
        $roboPackage = new RoboPackage();
        $packageData = $roboPackage->makePackageData($script->getWorkflowTemplate());
        $exportData['WORKFLOW_TEMPLATE'] = [
            'PARAMETERS' => $packageData['PARAMETERS'],
            'CONSTANTS' => $packageData['CONSTANTS'],
            'ROBOTS' => $packageData['ROBOTS'],
            'DOCUMENT_FIELDS' => $packageData['DOCUMENT_FIELDS'],
            'REQUIRED_APPLICATIONS' => $packageData['REQUIRED_APPLICATIONS'],
        ];

        return $exportData;
    }

    public static function importScript(array $data, int $userId)
    {
        $documentType = [$data['MODULE_ID'], $data['ENTITY'], $data['DOCUMENT_TYPE']];
        $templateFields = $data['WORKFLOW_TEMPLATE'];
        $templateFields['DOCUMENT_TYPE'] = $documentType;
        $templateFields['NAME'] = $data['NAME'];
        $templateFields['DESCRIPTION'] = $data['DESCRIPTION'];

        $result = self::importWorkflowTemplate($templateFields, $userId);

        if (!$result->isSuccess()) {
            return $result;
        }

        $result = self::addScriptRecord(
            $documentType,
            [
                'WORKFLOW_TEMPLATE_ID' => $result->getData()['ID'],
                'NAME' => $data['NAME'],
                'DESCRIPTION' => $data['DESCRIPTION'],
                'ORIGINATOR_ID' => $data['ORIGINATOR_ID'],
                'ORIGIN_ID' => $data['ORIGIN_ID'],
            ],
            $userId
        );

        if ($result->isSuccess()) {
            self::clearMenuCache();
        }

        return $result;
    }

    private static function importWorkflowTemplate(array $data, int $userId)
    {
        $roboPackage = new RoboPackage();
        $result = $roboPackage->unpack($data);

        if ($result->isSuccess()) {
            $tpl = $result->getTpl();
            $tpl->setUserId($userId);
            $tpl->setDocumentStatus('SCRIPT');
            $tpl->setAutoExecute(\CBPDocumentEventType::Script);

            $saveResult = $tpl->save();

            if ($saveResult->isSuccess()) {
                $result->setData(['ID' => $saveResult->getId()]);
            } else {
                $result->addErrors($saveResult->getErrors());
            }
        }

        return $result;
    }

    private static function clearMenuCache(): void
    {
        if (defined('BX_COMP_MANAGED_CACHE')) {
            $cache = Main\Application::getInstance()->getTaggedCache();
            $cache->clearByTag('intranet_menu_binding');
        }
    }
}
