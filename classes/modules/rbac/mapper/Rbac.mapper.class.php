<?php
/* 
 * @author Oleg Demidov
 *
 */
 
class PluginPermissions_ModuleRbac_MapperRbac extends PluginPermissions_Inherit_ModuleRbac_MapperRbac
{

    /**
     * Получает список всех задействованых в ролях разрешений
     *
     * @return array|null
     */
    public function GetRoleWithPermissions()
    {
        $sql = "SELECT
					r.role_id,
                                        r.period,
                                        r.count,
                                        r.price,
                                        r.id,
					p.code,
					p.plugin,
					p.title,
					p.msg_error
				FROM
					" . Config::Get('db.table.rbac_role_permission') . " as r
					LEFT JOIN " . Config::Get('db.table.rbac_permission') . " as p ON r.permission_id=p.id
				WHERE
					p.state = ?d ; ";
        if ($aRows = $this->oDb->select($sql, ModuleRbac::PERMISSION_STATE_ACTIVE)) {
            return $aRows;
        }
        return array();
    }
    
    public function GetUsersIdsByPermission($sPermissionCode)
    {
        $sql = "SELECT
                    ru.user_id
                FROM " . Config::Get('db.table.rbac_permission') . " as p
                JOIN " . Config::Get('db.table.rbac_role_permission') . " as rp ON rp.permission_id = p.id 
                JOIN " . Config::Get('db.table.rbac_role') . " as r ON rp.role_id = r.id
                JOIN " . Config::Get('db.table.rbac_role_user') . " as ru ON ru.role_id = rp.role_id 
                WHERE p.state = 1 AND  r.state = 1 AND p.code = ?;";
        if ($aRows = $this->oDb->selectCol($sql, $sPermissionCode)) {
            return $aRows;
        }
        return array();
    } 
}