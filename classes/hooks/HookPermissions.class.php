<?php
/**
 * 
 * 
 * @author Oleg Demidov
 *
 */


class PluginPermissions_HookPermissions extends Hook
{

    public function RegisterHook()
    {
        $this->AddHook('template_permissions_add', 'AddPermissions');
    }


    public function AddPermissions($aParams)
    {   
        $this->Viewer_Assign('aPermissions', $aParams['params']);
        return $aParams['params'];
        
    }

}

