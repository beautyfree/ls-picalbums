{assign var="sAlbumEnd" value=$iPosStart+$iAlbCnt}
{assign var="iNextPage" value=$iPage+1}

{if !$isPjax}
	{include file='header.tpl' menu='mainalbums' showWhiteBack=false}	
	<h1 class="albums-page-title">{$aLang.picalbums_albums_tags} "{$sTag}" {if $iPage}/ {$aLang.picalbums_pagetitle} {$iNextPage}{/if}</h1>
{/if}

{if $aAlbums}
	{assign var=iShowAlbumCnt value=0}
    {assign var=iShowEnablesAlbumCnt value=0}
	{foreach from=$aAlbums item=oAlbum}	
		{assign var="oCoverPicture" value=$oAlbum->GetCoverPicture()}		
		{assign var="bIsCanModify" value=false}
		{if ($oUserCurrent) AND ($oUserCurrent->isAdministrator() OR $oAlbum->getAddUserId() == $oUserCurrent->getId())}
			{assign var="bIsCanModify" value=true}
		{/if}
	    {assign var=bIsContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}
        {if $bIsContinue}
            {if $iShowAlbumCnt >= $iPosStart AND $iShowAlbumCnt < $sAlbumEnd}
                {if $oAlbum->getUserId() == $oConfig->GetValue('plugin.picalbums.virtual_main_user_id')}
                    {include file="$sIncludesTplPath/albums_listing_album.tpl" aAlbumStart=$sMainAlbumsRouter}
                {else}
                    {assign var="oUserOwner" value=$oAlbum->getUserOwner()}
                    {include file="$sIncludesTplPath/albums_listing_album.tpl" aAlbumStart=$oUserOwner->getUserAlbumsWebPath()}
                {/if}
                {assign var=iShowEnablesAlbumCnt value=$iShowEnablesAlbumCnt + 1}
            {/if}
		{/if}
    
		{assign var=iShowAlbumCnt value=$iShowAlbumCnt + 1}
	{/foreach}
	
	{if $iShowEnablesAlbumCnt == 0}
		{$aLang.picalbums_tag_not_has_publoc_albums}
	{/if}

	{if $iShowAlbumCnt > $sAlbumEnd}
		<div id="pjaxmore{$iPage}"><a class="pjaxmorepics{$iPage} friends-last-foto-get-more" href='{$sMainAlbumsRouter}tag/{$sTag}/{$iNextPage}/' >{$aLang.picalbums_pagination_more}</a></div>
	{/if}
{else}
	{$aLang.picalbums_tag_not_has_albums}
{/if}

<script type="text/javascript">
	$('.pjaxmorepics{$iPage}').pjax('#pjaxmore{$iPage}', {literal}{push: false,timeout : 5000}{/literal});
</script>

{if !$isPjax}
	{include file='footer.tpl'}
{/if}
