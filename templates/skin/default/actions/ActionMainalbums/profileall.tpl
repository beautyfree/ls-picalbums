{assign var="iNextPage" value=$iPage+1}

{if $isPjax == false}
	{include file='header.tpl' menu='mainalbums' showWhiteBack=false}	
	<h1 class="albums-page-title">{$aLang.picalbums_allprofilepictures} {if $iPosStart}/ {$aLang.picalbums_pagetitle} {$iNextPage}{/if}</h1>
{/if}

{assign var="iAlbumsEnd" value=$iPosStart+$iAllAlbumsCnt}	
{assign var="iCurrShowCnt" value=0}

{if $aAlbums}
	{foreach from=$aAlbums item=oAlbum}	
		
			{assign var="oUserOwner" value=$oAlbum->getUserOwner()}
			
			{if $oUserOwner}
				{assign var="aPictures" value=$oAlbum->GetLimitPictures(0, $oConfig->GetValue('plugin.picalbums.friend_page_start_count'))}
				{assign var="iPicturesCount" value=$oAlbum->GetPicturesCount()}
				{assign var=isContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}
			
				{if $isContinue}
					{if $aPictures}						
						{if $iCurrShowCnt >= $iPosStart AND $iCurrShowCnt < $iAlbumsEnd}		
							{include file="$sIncludesTplPath/album_user_preview.tpl" bClearCloseButton=true}
						{/if}
						{assign var="iCurrShowCnt" value=$iCurrShowCnt+1}
					{/if}
				{/if}
			{/if}

	{/foreach}
{/if}
{if $iCurrShowCnt == 0}
    <div class="friends-last-foto">
        <div>
            {$aLang.picalbums_block_profile_albums_none}
        </div>
    </div>
    <div style="clear: both"></div>
{/if}

	
{if $iCurrShowCnt > $iAlbumsEnd}
	<div id="pjaxmore{$iPosStart}"><a class="pjaxmorepics{$iPosStart} friends-last-foto-get-more" href='{router page="$sMainAlbumsRouterName"}profileall/{$iNextPage}/' >{$aLang.picalbums_pagination_more_allprofile}</a></div>
{/if}

<script type="text/javascript">
	$('.pjaxmorepics{$iPosStart}').pjax('#pjaxmore{$iPosStart}', {literal}{push: false,timeout : 5000}{/literal});
</script>

{if $isPjax == false}
	{include file='footer.tpl'}
{/if}
