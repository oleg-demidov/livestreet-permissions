<?php

/**
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 *
 * ------------------------------------------------------
 *
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 *
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * ------------------------------------------------------
 *
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */
class PluginPage_ActionAdmin extends PluginAdmin_ActionPlugin
{

    /**
     * Объект УРЛа админки, позволяет удобно получать УРЛы на страницы управления плагином
     */
    public $oAdminUrl;
    public $oUserCurrent;

    public function Init()
    {
        $this->oAdminUrl = Engine::GetEntity('PluginAdmin_ModuleUi_EntityAdminUrl');
        $this->oAdminUrl->setPluginCode(Plugin::GetPluginCode($this));
        $this->oUserCurrent = $this->User_GetUserCurrent();
        $this->Viewer_AppendScript(Plugin::GetWebPath(__CLASS__) . 'frontend/js/admin.js');

        $this->SetDefaultEvent('index');
    }

    /**
     * Регистрируем евенты
     *
     */
    protected function RegisterEvent()
    {
        /**
         * Для ajax регистрируем внешний обработчик
         */
        $this->RegisterEventExternal('Ajax', 'PluginPage_ActionAdmin_EventAjax');
        /**
         * Список страниц, создание и обновление
         */
        $this->AddEvent('index', 'EventIndex');
        $this->AddEvent('create', 'EventCreate');
        $this->AddEventPreg('/^update$/i', '/^\d{1,6}$/i', '/^$/i', 'EventUpdate');
        $this->AddEventPreg('/^sort$/i', '/^up|down$/i', '/^\d{1,6}$/i', '/^$/i', 'EventSort');
        /**
         * Ajax обработка
         */
        $this->AddEventPreg('/^ajax$/i', '/^page-create$/i', '/^$/i', 'Ajax::EventPageCreate');
        $this->AddEventPreg('/^ajax$/i', '/^page-update$/i', '/^$/i', 'Ajax::EventPageUpdate');
        $this->AddEventPreg('/^ajax$/i', '/^page-remove$/i', '/^$/i', 'Ajax::EventPageRemove');
    }

    /**
     *    Вывод списка страниц
     */
    protected function EventIndex()
    {
        /**
         * Получаем список страниц
         */
        $aPages = $this->PluginPage_Main_LoadTreeOfPage(array('#order' => array('sort' => 'desc')));
        $aPages = ModuleORM::buildTree($aPages);
        /**
         * Прогружаем переменные в шаблон
         */
        $this->Viewer_Assign('aPageItems', $aPages);
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');
    }

    /**
     * Создание страницы. По факту только отображение шаблона, т.к. обработка идет на ajax
     */
    protected function EventCreate()
    {
        /**
         * Получаем список страниц
         */
        $aPages = $this->PluginPage_Main_LoadTreeOfPage(array('#order' => array('sort' => 'desc')));
        $aPages = ModuleORM::buildTree($aPages);

        $this->Viewer_Assign('aPageItems', $aPages);
        $this->SetTemplateAction('create');
    }

    /**
     * Редактирование страницы
     */
    protected function EventUpdate()
    {
        /**
         * Проверяем страницу на существование
         */
        if (!($oPage = $this->PluginPage_Main_GetPageById($this->GetParam(0)))) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.page.notices.error_not_found'), $this->Lang_Get('common.error.error'));
            return $this->EventError();
        }

        /**
         * Получаем список страниц
         */
        $aPages = $this->PluginPage_Main_LoadTreeOfPage(array('#order' => array('sort' => 'desc')));
        $aPages = ModuleORM::buildTree($aPages);

        $this->Viewer_Assign('aPageItems', $aPages);
        $this->Viewer_Assign("oPage", $oPage);
        $this->SetTemplateAction('create');
    }

    protected function EventSort()
    {
        $this->Security_ValidateSendForm();
        /**
         * Проверяем страницу на существование
         */
        if (!($oPage = $this->PluginPage_Main_GetPageById($this->GetParam(1)))) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.page.notices.error_not_found'), $this->Lang_Get('common.error.error'));
            return $this->EventError();
        }

        $sWay = $this->GetParam(0);
        $iSortOld = $oPage->getSort();
        if ($oPagePrev = $this->PluginPage_Main_GetNextPageBySort($iSortOld, $oPage->getPid(), $sWay)) {
            $iSortNew = $oPagePrev->getSort();
            $oPagePrev->setSort($iSortOld);
            $oPagePrev->Update();
        } else {
            if ($sWay == 'down') {
                $iSortNew = $iSortOld - 1;
            } else {
                $iSortNew = $iSortOld + 1;
            }
        }
        /**
         * Меняем значения сортировки местами
         */
        $oPage->setSort($iSortNew);
        $oPage->Update();

        Router::Location($this->oAdminUrl->get());
    }
}