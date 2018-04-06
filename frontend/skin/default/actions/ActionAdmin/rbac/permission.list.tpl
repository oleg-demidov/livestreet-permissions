{extends "{$aTemplatePathPlugin.admin}layouts/layout.base.tpl"}
gdfg
{block 'layout_content_actionbar'}
	{component 'admin:button' text=$aLang.plugin.admin.add url={router page="admin/users/rbac/permission-create"} mods='primary'}

	{include './menu.tpl'}
{/block}

{block 'layout_page_title'}
	Список разрешений
{/block}

{block 'layout_content'}
	{component 'permissions:p-rbac' template='permission-list' permissionGroups=$aPermissionGroupItems groups=$aGroupItems}
{/block}