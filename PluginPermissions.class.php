<?php
/**
 * @author Oleg Demidov
 *
 */

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attempt!');
}

class PluginPermissions extends Plugin
{


    protected $aInherits = array(
        'template' => array(
            'admin:component.p-rbac.role-permissions' => '_components/p-rbac/role/permissions.tpl',
            'admin:component.p-rbac.role-permissions-item' => '_components/p-rbac/role/permissions.item.tpl',
            'admin:component.p-rbac.role-form' => '_components/p-rbac/role/form.tpl',
        ),
        'entity'  =>array(
            'ModuleRbac_EntityRole' => '_ModuleRbac_EntityRole',
            'ModuleRbac_EntityRolePermission' => '_ModuleRbac_EntityRolePermission',
            'ModuleRbac_EntityUserStat' => '_ModuleRbac_EntityUserStat',
            'ModuleRbac_EntityPermission' => '_ModuleRbac_EntityPermission'
            ),
        'mapper' => array('ModuleRbac_MapperRbac' => '_ModuleRbac_MapperRbac'),
        'module' =>array('ModuleRbac' => 'PluginPermissions_ModuleRbac')
    );

    public function Init()
    {
    
    }

    public function Activate()
    {
        return true;
    }

    public function Deactivate()
    {
        return true;
    }
}