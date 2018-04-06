<?php
/*

 * @author Oleg
 *
 */

class PluginPermissions_ModuleRbac_EntityRolePermission extends PluginPermissions_Inherit_ModuleRbac_EntityRolePermission
{
    /**
     * Выполняется перед сохранением
     *
     * @return bool
     */
    protected function beforeSave()
    {
        if ($bResult = parent::beforeSave()) {
            if ($this->_isNew()) {
                $this->setDateCreate(date("Y-m-d H:i:s"));
            }
            $this->setPeriod(getRequest('period'));
            $this->setCount(getRequest('count'));
            $this->setPrice(getRequest('price'));
        }
        return $bResult;
    }
    
    public function getStrPeriod() {
        
        $sDays = round($this->getPeriod()/(60*60*24));
        return $sDays.' '.num2word($sDays, ['день', 'дня', 'дней']);
    }
    
    public function getStrCount() {
        
        $sCount = $this->getCount();
        return $sCount.' '.num2word($sCount, ['штука', 'штуки', 'штук']);
    }
}