{**
 * Отображение страницы
 *}

{extends file='layouts/layout.base.tpl'}

{block name='layout_options' prepend}
	{$layoutNoSidebar = !Config::Get('plugin.page.show_block_structure')}
{/block}

{block name='layout_page_title'}
	{$oPage->getTitle()|escape}
{/block}

{block name='layout_content'}
	{if $oUserCurrent and $oUserCurrent->isAdministrator()}
		{$items = [
			[ 'icon' => 'edit', 'url' => $oPage->getAdminEditWebUrl(), 'text' => $aLang.common.edit ]
		]}
		{component 'actionbar' items=[[ 'buttons' => $items ]]}
	{/if}

	{capture page_text}
		{if !$oPage->getAutoBr() or Config::Get('view.wysiwyg')}
			{$oPage->getText()}
		{else}
			{$oPage->getText()|nl2br}
		{/if}
	{/capture}

	{component 'text' text=$smarty.capture.page_text}
{/block}