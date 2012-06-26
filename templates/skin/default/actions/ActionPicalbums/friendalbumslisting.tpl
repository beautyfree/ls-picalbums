{if !$isPjax}
	{include file='header.tpl' menu='profile' showWhiteBack=false}

	{assign var="iNextPage" value=$iPage+1}
	<h1>{$aLang.picalbums_friendlisting} {$oUserProfile->getLogin()} {if $iPage}/ {$aLang.picalbums_pagetitle} {$iNextPage}{/if}</h1>
{/if}

{assign var="iFriendEnd" value=$iPosStart+$iFrCnt}
{if $aUsersFriend}
	{assign var="iFriendPos" value=0}	
	{foreach from=$aUsersFriend item=oUserFriend}	
		{if $iFriendPos >= $iPosStart AND $iFriendPos < $iFriendEnd}
			{assign var="aAlbums" value=$oUserFriend->getPicalbumsModifySort()}
		
			{if $aAlbums}
				{foreach from=$aAlbums item=oAlbum}
					{assign var="oUserOwner" value=$oAlbum->getUserOwner()}
					{assign var="aPictures" value=$oAlbum->GetLimitPictures(0, $oConfig->GetValue('plugin.picalbums.friend_page_start_count'))}
					{assign var="iPicturesCount" value=$oAlbum->GetPicturesCount()}
					{assign var="bIsAlbumHistorySave" value=$oUserProfile->isAlbumRelated($oAlbum->getId())}
					
					{if $bIsAlbumHistorySave == false}
						{include file="$sIncludesTplPath/album_user_preview.tpl"}
					{/if}
				{/foreach}
			{/if}		
		{/if}
		{assign var="iFriendPos" value=$iFriendPos+1}
	{/foreach}
	
	{if $iFriendPos > $iFriendEnd}
		<div id="pjaxmore{$iPage}"><a class="pjaxmorepics{$iPage} friends-last-foto-get-more" href='{$oUserProfile->getUserAlbumsWebPath()}friend/{$iNextPage}/' >{$aLang.picalbums_pagination_more_friendpage}</a></div>
	{/if}
{/if}

<script type="text/javascript">
	$('.pjaxmorepics{$iPage}').pjax('#pjaxmore{$iPage}', {literal}{push: false,timeout : 5000}{/literal});
</script>

{if !$isPjax}
	{include file='footer.tpl'}
{/if}
