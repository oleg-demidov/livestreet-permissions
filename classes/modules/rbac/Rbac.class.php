<?php
/*
 * @author Oleg
 *
 */

class PluginPermissions_ModuleRbac extends PluginPermissions_Inherit_ModuleRbac
{
    protected $aParams;
    
    protected $sMessageLastAdd;
    /**
     * Загружает в кеш разрешения
     */
    protected function LoadPermissions()
    {
        if ($this->aRulePermissionCache) {
            return;
        }
        $aResult = $this->oMapper->GetRoleWithPermissions();
        foreach ($aResult as $aRow) {
            $this->aRulePermissionCache[$aRow['role_id']][$aRow['plugin']][$aRow['code']] = $aRow;
            $this->aPermissionCache[$aRow['plugin']][$aRow['code']] = $aRow;
        }
        //print_r($this->aRulePermissionCache);
    }
    
    /**
     * Проверяет наличие разрешения у конкретной роли, учитывается наследование ролей
     *  И проверяет соответствие данным разрешения
     * 
     */
    protected function CheckPermissionByRole($oRole, $sPermissionCode, $sPlugin = '')
    {
        /**
         * Проверяем наличие пермишена в текущей роли
         */
        if (isset($this->aRulePermissionCache[$oRole->getId()][$sPlugin])) {            
            if (in_array($sPermissionCode, array_keys( $this->aRulePermissionCache[$oRole->getId()][$sPlugin] ))) {
                return $this->CheckPermissionByData( $this->aRulePermissionCache[$oRole->getId()][$sPlugin][$sPermissionCode] );
            }
        }
        /**
         * Смотрим родительскую роль
         */
        if ($oRole->getPid() and isset($this->aRoleCache[$oRole->getPid()])) {
            return $this->CheckPermissionByRole($this->aRoleCache[$oRole->getPid()], $sPermissionCode, $sPlugin);
        }
        return false;
    }
    
        
    public function CheckPermissionByData($aRpData) {
        //print_r($aRpData);
        
        if(!$aRpData['count'] && !$aRpData['period']){
            return true;
        }
        if(!$this->aParams['user']){
            $this->Logger_Error('Rbac no user take');
            return true;
        } 
        
        if(!$oUserStat = $this->GetUserStatByUserIdAndRpId($this->aParams['user']->getId(), $aRpData['id'])){
            $oUserStat = Engine::GetEntity('Rbac_UserStat');
            $oUserStat->setUserId($this->aParams['user']->getId());
            $oUserStat->setRpId($aRpData['id']);
        }           
        
        $bResult = true;
        if($aRpData['count'] && $aRpData['period']){
            $bResult = $this->CheckCountPeriod($oUserStat, $aRpData);
        }elseif($aRpData['count']){
            $bResult = $this->CheckCount($oUserStat, $aRpData);
        }elseif($aRpData['period']){
            $bResult = $this->CheckPeriod( $aRpData );
        }

        if(isset($this->aParams['stat_off'])){
            
            $oUserStat->setCount($oUserStat->getCount() + ($this->aParams['stat_off']-1));
            $oUserStat->Save();
        }
            
        $this->aParams['count_allow'] = $aRpData['count'];
        
        if(isset($this->aParams['stat']) and $this->aParams['stat']){
            //echo $oUserStat->getCount();
            $oUserStat->Save();
        }
        return $bResult;
    }
    
    protected function CheckPeriod( $aRpData ) {
        $iTimeCreate = $this->getRoleUserTime($aRpData);
        if((time() - $iTimeCreate) > $aRpData['period']){
            $this->sMessageLastAdd = $this->Lang_Get(
                    'plugin.permissions.error.max_period',[ 'period'=>$this->getTimeStr($aRpData['period'])]);
            return false;
        }        
        return true;
    }
    
    protected function CheckCount(&$oUserStat, $aRpData) {
        if(isset($this->aParams['count'])){
            if($this->aParams['count'] > $aRpData['count']){                
                $this->sMessageLastAdd = $this->Lang_Get(
                    'plugin.permissions.error.max_count',[ 'count'=>$aRpData['count']]);
                return false;
            }else{
                return true;
            }
        }
        $oUserStat->setCount($oUserStat->getCount()+1);
        if($oUserStat->getCount() > $aRpData['count']){
            $this->sMessageLastAdd = $this->Lang_Get(
                    'plugin.permissions.error.max_count',[ 'count'=>$aRpData['count']]);
            return false;
        }      
        return true;
    }
    
    protected function CheckCountPeriod(&$oUserStat, $aRpData) {
        
        $iTimeCreate = $this->getRoleUserTime($aRpData['role_id'], $this->aParams['user']->getId());
       
        $iTimeAllPeriod = time() - $iTimeCreate; 

        $iCountPeriod = floor($iTimeAllPeriod/$aRpData['period']);
        
        $iRestPeriod = $aRpData['period']-($iTimeAllPeriod%$aRpData['period']);

        if($oUserStat->getCountPeriod() < $iCountPeriod){
            $oUserStat->setCountPeriod($iCountPeriod);
            $oUserStat->setCount(0);
        }
        
        $oUserStat->setCount($oUserStat->getCount()+1);

        if($oUserStat->getCount() > $aRpData['count']){
            $this->sMessageLastAdd = $this->Lang_Get(
                    'plugin.permissions.error.max_per_period',
                    [ 
                        'count'=>$aRpData['count'], 
                        'period'=> $this->getTimeStr($aRpData['period']),
                        'rest'=> $this->getTimeStr($iRestPeriod) 
                    ]);
            return false;
        }
        
        return true;
    }
    
    protected function getRoleUserTime($iRoleId, $iUserId) {
        
        if($oUserRole = $this->GetRoleUserByUserIdAndRoleId($iUserId, $iRoleId)){
            return strtotime( $oUserRole->getDateCreate() );
        }
        
        $oRole = $this->GetRoleById($iRoleId);
        
        $aRoles = $oRole->getChildren();
        foreach($aRoles as $oRole){
            if($iTimeCreate = $this->getRoleUserTime($oRole->getId(), $iUserId)){
                return $iTimeCreate;
            }            
        }
        return 0;
    }
    
    protected function getRoleUserTimeSingle($iUserId, $iRoleId) {
        if($oUserRole = $this->GetRoleUserByUserIdAndRoleId($iUserId, $iRoleId)){
            return strtotime( $oUserRole->getDateCreate() );
        }
        return false;
    }
    
    private function getTimeStr($seconds) {
        
        $oInterval = new DateInterval('PT'.$seconds.'S');
        $datetime1 = new DateTime();    
        $datetime2 = new DateTime();
        $datetime2->add($oInterval);
        $interval = $datetime1->diff($datetime2);        
        
        $iDays = $interval->format('%d');
        $iHours = $interval->format('%h');
        $iMins = $interval->format('%m');
        
        $iDays .= ' '.num2word($iDays, ['день', 'дня', 'дней']);
        $iHours .= ' '.num2word($iHours, ['час', 'часа', 'часов']);
        $iMins .= ' '.num2word($iMins, ['минута', 'минут', 'минут']);
        
        return $iDays.' '.$iHours.' '.$iMins;//$sTime.$seconds.' Секунд'; // Получаем время 1:40
    }

    protected function GetChildRoleUsers($oRole, $iUserId, &$aRoleUsers) {
        if($oRoleUser = $this->GetRoleUserByRoleIdAndUserId($oRole->getId(), $iUserId)){
            //print_r($oRoleUser);
            $aRoleUsers[$oRole->getId()] = $oRoleUser;
        }
        if($oRoles = $this->GetRoleItemsByPid($oRole->getId())){
            //print_r($oRoles);
            foreach($oRoles as $oRole){
                $this->GetChildRoleUsers($oRole, $iUserId, $aRoleUsers);
            }
        }
    }
    protected function getRoleUser($iRoleId, $iIdUser) {
        if($oRoleUser = $this->GetRoleUserByRoleIdAndUserId($iRoleId, $iIdUser) ){
            return $oRoleUser;
        }
        if($oRole = $this->GetRoleById($iRoleId)){
            //print_r($oRole);
            return $this->getRoleUser($oRole->getPid(), $iIdUser);
        }
        return null;
    }
    
    public function IsAllowUser($oUser, $sPermissionCode, $aParamsOrPlugin = array(), $sPluginOrParams = null)
    {
        
        $aParams = array();
        $sPlugin = null;
        if (!is_array($sPluginOrParams)) {
            $sPlugin = $sPluginOrParams;
        } else {
            $aParams = $sPluginOrParams;
        }
        if (is_array($aParamsOrPlugin)) {
            $aParams = $aParamsOrPlugin;
        } else {
            $sPlugin = $aParamsOrPlugin;
        }
        $this->aParams = array_merge($aParams, ['user' => $oUser]);
        
        return $this->IsAllowUserFull($oUser, $sPermissionCode, $aParams, $sPlugin);
    }

    public function GetMsgLast()
    {
        return $this->sMessageLast.' '.$this->sMessageLastAdd;
    }
    
    public function GetUsersByPermissionCode($sCode) {
        return $this->oMapper->GetUsersIdsByPermission($sCode);
    }
    
    public function RemoveRoleUser($iUserId, $iRoleId) {
        if($oRoleUser = $this->GetRoleUserByFilter(['user_id' => $iUserId, 'role_id' => $iRoleId])){
            $oRoleUser->Delete();
        }        
    }
    public function GetRolesByUser($oUser, $bActiveOnly = true, $aFilterDef = [])
    {
        if (!$oUser) {
            return array();
        }
        if (is_object($oUser)) {
            $iUserId = $oUser->getId();
        } else {
            $iUserId = $oUser;
        }
        /**
         * Сначала получаем все связи
         */
        $aRoleUserItems = $this->GetRoleUserItemsByFilter(array('user_id' => $iUserId, '#index-from' => 'role_id'));
        $aRoleIds = array_keys($aRoleUserItems);
        /**
         * Теперь получаем список ролей
         */
        if ($aRoleIds) {
            $aFilter = array('id in' => $aRoleIds);
            if ($bActiveOnly) {
                $aFilter['state'] = self::ROLE_STATE_ACTIVE;
            }
            return $this->GetRoleItemsByFilter(array_merge($aFilter, $aFilterDef));
        }
        return array();
    }
    
}