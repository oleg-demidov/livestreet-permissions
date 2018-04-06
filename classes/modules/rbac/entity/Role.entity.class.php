<?php

class PluginPermissions_ModuleRbac_EntityRole extends PluginPermissions_Inherit_ModuleRbac_EntityRole
{

    /**
     * Определяем правила валидации
     *
     * @var array
     */
    protected $aValidateRules = array(
        array('title', 'string', 'max' => 200, 'min' => 1, 'allowEmpty' => false),
        array('code', 'regexp', 'pattern' => '/^[\w\-_]+$/i', 'allowEmpty' => false),
        array('code', 'check_code'),
        array('pid', 'parent_role'),
        array('price', 'number'),
    );
    /**
     * Связи ORM
     *
     * @var array
     */
    protected $aRelations = array(
        'permissions' => array(
            self::RELATION_TYPE_MANY_TO_MANY,
            'ModuleRbac_EntityPermission',
            'permission_id',
            'ModuleRbac_EntityRolePermission',
            'role_id'
        ),
        'role_permission' => array(
            EntityORM::RELATION_TYPE_HAS_ONE,
            'ModuleRbac_EntityRolePermission',
            'role_id'
        ),
        self::RELATION_TYPE_TREE,
    );

    public function getRolesPay() {
        $oUser = $this->User_GetUserCurrent();
        $aPermissions = $this->getPermissions();
        $aCodes = [];
        $aCodesStop = [$oUser->getStrRole().'_pro',$oUser->getStrRole().'_profi'];
        foreach($aPermissions as $oPermission){
            if(!in_array($oUser->getStrRole().'_'.$oPermission->getCode(),$aCodesStop)){
                $aCodes[] = $oUser->getStrRole().'_'.$oPermission->getCode();
            }
        }
        $aRoles = $this->Rbac_GetRoleItemsByFilter([
            '#where'=>['t.price IS NOT NULL'=>[]],
            '#order' => ['title' => 'asc'],
            'code in' => $aCodes]);
        return $aRoles;
    }
}