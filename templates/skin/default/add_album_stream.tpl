{if $oAlbum}
	{assign var=isContinue value=true}
	
	{if $oAlbum->getUserId() != $oConfig->GetValue('plugin.picalbums.virtual_main_user_id')}
        {assign var=isContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}
	{/if}
	
	{if $isContinue}
		{if $oUser}
			{$aLang.picalbums_add_album_stream} <a href="{$oUser->getUserAlbumsWebPath()}{$oAlbum->getURL()}/">{$oAlbum->getTitle()}</a>
		{else}
			{if $oAddUser}
				{$aLang.picalbums_add_album_stream} <a href="{$AlbumsRouter}{$oAlbum->getURL()}/">{$oAlbum->getTitle()}</a>
			{/if}
		{/if}
	{else}
		{$aLang.picalbums_albums_is_private_stream}
	{/if}
{/if}
