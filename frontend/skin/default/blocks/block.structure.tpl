{capture 'block_content'}
	{foreach $aPagesTree as $aPage}
		{$oPage=$aPage.entity}
		{$aItems[] = [
			'text'       => ($oPage->getTitle()|escape),
			'url'        => $oPage->getWebUrl(),
			'attributes' => [ 'style' => "margin-left: {$aPage.level * 20}px;" ],
			'name'		 => $oPage->getUrlFull()
		]}
	{/foreach}

	{component 'nav'
		name       = 'pages_tree'
		classes    = 'actionbar-item-link'
		activeItem = $oCurrentPage->getUrlFull()
		mods       = 'stacked pills'
		items      = $aItems}
{/capture}

{*component 'block'
	title   = {lang 'plugin.page.structure_title'}
	content = $smarty.capture.block_content*}