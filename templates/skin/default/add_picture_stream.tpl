{$aLang.picalbums_add_picture_stream}

{if $oAlbum}
	{assign var=isContinue value=true}

	{if $oAlbum->getUserId() != $oConfig->GetValue('plugin.picalbums.virtual_main_user_id')}
        {assign var=isContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}
	{/if}
	
	{if $isContinue}
		{if $oUser}
			<br />
			<a href="{$oUser->getUserAlbumsWebPath()}{$oAlbum->getURL()}/{$oTarget->getURL()}/">
			<img src="{$oTarget->getMiniaturePath()}" alt="" class="photo" />
			</a>
		{else}
			{if $oAddUser}
				<br />
				<a href="{$AlbumsRouter}{$oAlbum->getURL()}/{$oTarget->getURL()}/">
				<img src="{$oTarget->getMiniaturePath()}" alt="" class="photo" />
				</a>
			{/if}
		{/if}
	{else}
		{$aLang.picalbums_picture_is_private_stream}
	{/if}
{/if}		
