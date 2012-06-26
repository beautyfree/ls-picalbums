<div class="photo-albums albums-preview-panel" id="album_{$oAlbum->getId()}">
	{if $oCoverPicture}
		<a id="album_last_picture_link_{$oAlbum->getId()}" href="{$aAlbumStart}{$oAlbum->getURL()}/">
			<img src="{$oCoverPicture->getMiniaturePath()}" class="album" alt="{$oAlbum->getTitle()}" />
		</a>
	{else}
		<a href="{$aAlbumStart}{$oAlbum->getURL()}/">
			<img src="{$sTemplateWebPathPicalbumsPlugin}/images/no_foto.gif" class="album" alt="{$oAlbum->getTitle()}" />
		</a>
	{/if}					
	<div class="album-title">
		<div id="album_title_{$oAlbum->getId()}">
			<a id="album_link_{$oAlbum->getId()}" href="{$aAlbumStart}{$oAlbum->getURL()}/">{$oAlbum->getTitle()}</a>
			{if $bIsCanModify == true}
				<a href="{$aAlbumStart}{$oAlbum->getURL()}/edit/" class="edit">{$aLang.picalbums_albumslisting_album_edit}</a>
				<a href="" onclick="picalbums.removeAlbum({$oAlbum->getId()}); return false;" class="delete">{$aLang.picalbums_albumslisting_album_delete}</a>
			{/if}
		</div>	
	</div>
	
	<div class="album-info">
		{if $oConfig->GetValue('lang.current') == 'russian'}
			{assign var="iPictureCount" value=$oAlbum->GetPicturesCount()}
			{if ($iPictureCount == 0) OR ($iPictureCount > 5) AND ($iPictureCount < 20)}
				{$iPictureCount} фотографий
			{else}
				{assign var="piccntmod" value=$iPictureCount%10}
				{if $piccntmod == 1}
					{$iPictureCount} фотография
				{elseif $piccntmod > 1 AND $piccntmod < 5}
					{$iPictureCount} фотографии
				{else}
					{$iPictureCount} фотографий
				{/if}
			{/if}
		{else}
			{$oAlbum->GetPicturesCount()} {$aLang.picalbums_picture_title} 
		{/if}
		
		<div class="album-loader" id="album-loader_{$oAlbum->GetId()}"></div>
	</div>
	<div class="album-info">
		{$aLang.picalbums_albumslisting_album_date} {date_format date=$oAlbum->GetDateAdd()}
	</div>
	<div class="album-info">
		{$aLang.picalbums_albumslisting_album_date_update} {date_format date=$oAlbum->GetDateModify()}
	</div>

     {if $oConfig->GetValue('plugin.picalbums.virtual_main_user_id') == $oAlbum->getUserId()}
        {assign var=oAppendedUser value=$oAlbum->GetAppendedAlbumUser()}
        {if $oAppendedUser}
            <div class="album-info">
                {$aLang.picalbums_main_albums_author}: <a href="{$oAppendedUser->getUserWebPath()}">{$oAppendedUser->getLogin()}</a>
            </div>
        {/if}
        {if $oAlbum->getNeedModer() == 1}
            <div class="album-info">
                {$aLang.picalbums_album_om_moder}
            </div>
        {/if}
     {else}
        <div class="album-info">
            {assign var="bAlbumVisibility" value=$oAlbum->GetVisibility()}
            {if $bAlbumVisibility == '0'}
                {$aLang.picalbums_albumslisting_visibility_all}
            {elseif $bAlbumVisibility == '1'}
                {$aLang.picalbums_albumslisting_visibility_auth}
            {else}
                {$aLang.picalbums_albumslisting_visibility_friends}
            {/if}
        </div>
    {/if}
    <div class="album-info">
        {assign var=aTags value=$oAlbum->GetTags()}
        {if $aTags}
            {$aLang.picalbums_tags}:
            {foreach from=$aTags item=oTag name=tags}
                <a href="{$sMainAlbumsRouter}tag/{$oTag}/">{$oTag}</a> {if not $smarty.foreach.tags.last},{/if}
            {/foreach}
        {/if}
	</div>
	<div class="album-about">
		<div id="album_description_{$oAlbum->getId()}">
			{$oAlbum->getDescription()}
		</div>
		<div id="edit_album_form_{$oAlbum->getId()}"></div>
	</div>
</div>			