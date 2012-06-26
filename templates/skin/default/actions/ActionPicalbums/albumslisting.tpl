{assign var="sAlbumEnd" value=$iPosStart+$iAlbCnt}
{assign var="iNextPage" value=$iPage+1}

{if !$isPjax}
	{include file='header.tpl' menu='profile' showWhiteBack=false}	
	<h1>{$aLang.picalbums_albumshow_albums} {$oUserProfile->getLogin()} {if $iPage}/ {$aLang.picalbums_pagetitle} {$iNextPage}{/if}</h1>
{/if}

{if $aAlbums}
	{assign var=iShowAlbumCnt value=0}
	{foreach from=$aAlbums item=oAlbum}
		{assign var="oCoverPicture" value=$oAlbum->GetCoverPicture()}
		{assign var=bIsContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}

		{if $bIsContinue}
			{if $iShowAlbumCnt >= $iPosStart AND $iShowAlbumCnt < $sAlbumEnd}
				{include file="$sIncludesTplPath/albums_listing_album.tpl" aAlbumStart=$oUserProfile->getUserAlbumsWebPath()}
			{/if}
		{/if}
		{assign var=iShowAlbumCnt value=$iShowAlbumCnt + 1}
	{/foreach}

	{if $iShowAlbumCnt == 0}
		{$aLang.picalbums_albumslisting_visibility_none}
	{/if}

	{if $iShowAlbumCnt > $sAlbumEnd}
		<div id="pjaxmore{$iPage}"><a class="pjaxmorepics{$iPage} friends-last-foto-get-more" href='{$oUserProfile->getUserAlbumsWebPath()}p/{$iNextPage}/' >{$aLang.picalbums_pagination_more}</a></div>
	{/if}
{else}
	{$aLang.picalbums_albumslisting_user_none_albums}
{/if}

<script type="text/javascript">
	$('.pjaxmorepics{$iPage}').pjax('#pjaxmore{$iPage}', {literal}{push: false,timeout : 5000}{/literal});
</script>

{if !$isPjax}
	{include file='footer.tpl'}
{/if}
