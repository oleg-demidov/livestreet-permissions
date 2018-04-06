{**
 * Список разрешений роли
 *}

 
{$component = 'p-rbac-role-permissions'}
{component_define_params params=[ 'permissions', 'permissionGroups', 'groups', 'role' ]}
<ol>
    <li>Если указан только период - привилегия действует до его окончания, с момента присвоения роли/привилегии пользователю.</li>
    <li>Если указано только количество - привилегия действует до исчерпания количества обращений к ней пользователем.</li>
    <li>Если указаны период и количество - привилегия действует до исчерпания количества обращений к ней пользователем, после истечения периода количество восстанавливается.</li>
</ol><br>
<div>
    <select id="rbac-role-permissions-select" data-role-id="{$role->getId()}">
        {foreach $permissionGroups as $permissionGroup}
            {$group = $groups[$permissionGroup@key]}

            {if $group}
                {$groupName = $group->getTitle()}
            {else}
                {$groupName = 'Без группы'}
            {/if}

            <optgroup label="{$groupName}">
                {foreach $permissionGroup as $permission}
                    <option value="{$permission->getId()}">{$permission->getTitleLang()} ({$permission->getCode()})</option>
                {/foreach}
            </optgroup>
        {/foreach}
    </select>

    {component 'admin:button' type='button' text='Добавить разрешение' classes='js-rbac-role-permission-add'}
</div>
<table style="margin: 5px;">
    <tr><td> <label> Период(в секундах): <input type="text" name="period"></label></td>
        <td> <label> Количество: <input type="text" name="count"></label></td>
        
    </tr>
</table>
<script>
   $(document).ready(function(){
       $('.js-rbac-role-permission-add').unbind('click').click(function(){
            var sel=$('#rbac-role-permissions-select');
            var params = {   role: sel.data('roleId'), 
                    permission: sel.val(), 
                    period:$('input[name="period"]').val(),
                    count:$('input[name="count"]').val()
                    
                };
                console.log(params)
            ls.ajax.load(
                aRouter.admin + 'ajax/rbac/role-permission-add/', 
                params, 
                function(result) {
                    $('.js-rbac-role-permissions-area').prepend(result.sText);
            });
        });
       
    });
</script>

{if $permissions}
    
    <ul class="js-rbac-role-permissions-area">
        {foreach $permissions as $permission}
            
             {component 'admin:p-rbac' template='role-permissions-item' role=$role permission=$permission}            
        {/foreach}
    </ul>
{else}
    {component 'admin:blankslate' text='Нет ролей'}
{/if}