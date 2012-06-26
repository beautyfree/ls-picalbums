{include file='header.tpl' menu='profile' showWhiteBack=false}

<h1 class="albums-page-title">{$aLang.picalbums_mark_user_photo} {$oUserProfile->getLogin()}</h1>

{if $aPictures}
	{foreach from=$aPictures item=oPicture}
	{assign var=oAlbum value=$oPicture->getAlbumOwner()}
	{assign var=oUser value=$oAlbum->GetUserOwner()}
	{assign var=isContinue value=true}
	
	{if $oUser}
		{assign var=sAlbumURL value=$oUser->getUserAlbumsWebPath()}
		{assign var=isContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}
	{else}
		{assign var=oUser value=$oAlbum->GetAppendedAlbumUser()}
		{assign var=sAlbumURL value=$sMainAlbumsRouter}
	{/if}
	
	
	{if $isContinue}		
		{include file="$sIncludesTplPath/picture_preview.tpl" sAlbumPathStart=$sAlbumURL}
	{/if}
	{/foreach}
{/if}
	
{include file='footer.tpl'}
