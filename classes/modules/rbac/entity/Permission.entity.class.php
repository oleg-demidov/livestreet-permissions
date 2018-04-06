<?php
/*
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

/**
 * Сущность разрешения
 *
 * @package application.modules.rbac
 * @since 2.0
 */
class PluginPermissions_ModuleRbac_EntityPermission extends PluginPermissions_Inherit_ModuleRbac_EntityPermission
{
    
    /**
     * Связи ORM
     *
     * @var array
     */
    protected $aRelations = array(
        'roles' => array(
            self::RELATION_TYPE_MANY_TO_MANY,
            'ModuleRbac_EntityRole',
            'role_id',
            'ModuleRbac_EntityRolePermission',
            'permission_id'
        ),
        'role_permission' => array(
            EntityORM::RELATION_TYPE_HAS_ONE,
            'ModuleRbac_EntityRolePermission',
            'permission_id'
        ),
    );
    
    public function getRP($iRoleId) {
        if(!$iRoleId){
            return null;
        }
        return $this->getRolePermission(['role_id' => $iRoleId ]);
    }

    
}