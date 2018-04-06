<?php

class PluginPermissions_Update_CreateTable extends ModulePluginManager_EntityUpdate
{
    /**
     * Выполняется при обновлении версии
     */
    public function up()
    {
        $oRolePermissions = Engine::GetEntity('Rbac_RolePermission');
        $aFields = $oRolePermissions->_getFields();
        
        if(!in_array('count', $aFields)){
            /*
             * Добавить в таблицу RolePermission поля count и period
             */
            $this->exportSQL(Plugin::GetPath(__CLASS__) . '/update/1.0/dump.sql');
            $this->exportSQL(Plugin::GetPath(__CLASS__) . '/update/1.0/dump2.sql');
        }
        
        if (!$this->isTableExists('prefix_permissions_rbac_user_stat')) {
            /**
             * Добавить таблицу статистики
             */
            $this->exportSQL(Plugin::GetPath(__CLASS__) . '/update/1.0/table.sql');
        }
    }

    /**
     * Выполняется при откате версии
     */
    public function down()
    {
        $this->exportSQLQuery('ALTER TABLE `mk_rbac_role_permission` DROP `period`, DROP `count`');
        $this->exportSQLQuery('DROP TABLE prefix_permissions_rbac_user_stat;');
    }
}