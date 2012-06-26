<div class="friends-last-foto" id="friends-last-foto_{$oAlbum->getId()}">
	<ul class="albums-friends">
		<li class="title-album"><a id="album_link_{$oAlbum->getId()}" href="{$oUserOwner->getUserAlbumsWebPath()}{$oAlbum->getURL()}/">{$oAlbum->getTitle()}</a></li>
		<li>, {$aLang.picalbums_friendlisting_album} <a href="{$oUserOwner->getUserAlbumsWebPath()}">{$oUserOwner->getLogin()}</a></li>
		{if !$bClearCloseButton}<li class="hide"><a href="" class="hide" title="{$aLang.picalbums_friendlisting_album_hide}" onclick="picalbums.friendAlbumSlideUp({$oAlbum->getId()}); return false;"></a></li>{/if}
		<li class="date">{$aLang.picalbums_friendlisting_album_date_update} {date_format date=$oAlbum->GetDateModify()}</li>
	</ul>
	<div style="clear:both"></div>
		
	<div id="pictures_for_ajax_upload_{$oAlbum->getId()}">
		{if $aPictures}
			{foreach from=$aPictures item=oPicture}
				{include file="$sIncludesTplPath/picture_preview.tpl" sAlbumPathStart=$oUserOwner->getUserAlbumsWebPath()}
			{/foreach}
		{/if}
	</div>
	{if $iPicturesCount > $oConfig->GetValue('plugin.picalbums.friend_page_start_count')}
		<div id="show_next_link_{$oAlbum->getId()}">
			{if ($iPicturesCount - $oConfig->GetValue('plugin.picalbums.friend_page_start_count')) <= $oConfig->GetValue('plugin.picalbums.friend_page_step')}		
				<a class="friends-last-foto-get-more" id="" href="" onclick="picalbums.albumShowNext({$oAlbum->getId()}, {$oConfig->GetValue('plugin.picalbums.friend_page_start_count')}, {$oConfig->GetValue('plugin.picalbums.friend_page_step')}, {$iPicturesCount}); return false;">{$aLang.picalbums_friendlisting_album_show_photo_all} {$iPicturesCount} {$aLang.picalbums_friendlisting_album_show_photo}</a>
			{else}
				<a class="friends-last-foto-get-more" id="" href="" onclick="picalbums.albumShowNext({$oAlbum->getId()}, {$oConfig->GetValue('plugin.picalbums.friend_page_start_count')}, {$oConfig->GetValue('plugin.picalbums.friend_page_step')}, {$iPicturesCount}); return false;">{$aLang.picalbums_friendlisting_album_show_photo_more} {$oConfig->GetValue('plugin.picalbums.friend_page_step')} {$aLang.picalbums_friendlisting_album_show_photo_i} {$iPicturesCount} {$aLang.picalbums_friendlisting_album_show_photo}</a>
			{/if}	
		</div>		
	{/if}	
</div>
<div style="clear: both"></div>