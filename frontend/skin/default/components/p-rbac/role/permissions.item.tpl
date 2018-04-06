{**
 * Список разрешений роли
 *}

{$component = 'p-rbac-role-permissions-item'}
{component_define_params params=[ 'permission', 'role' ]}

<li class="js-rbac-role-permission-item" data-id="{$permission->getId()}">
	{$permission->getTitleLang()} ({$permission->getCode()})
        {if $rolePermission = $permission->_getManyToManyRelationEntity()}
            {if $rolePermission->_getDataOne('period')}| Период: <b>{$rolePermission->_getDataOne('period')}</b>  {/if}
            {if $rolePermission->_getDataOne('count')}| Колличество: <b>{$rolePermission->_getDataOne('count')}</b>  {/if}
            {if $rolePermission->_getDataOne('count')}| Цена: <b>{$rolePermission->_getDataOne('price')}</b>  {/if}
        {/if} &mdash;
	<a href="#" class="fa fa-trash-o js-confirm-remove js-rbac-role-permission-remove" data-permission="{$permission->getId()}" data-role="{$role->getId()}" title="{$aLang.plugin.admin.delete}"></a>
</li>