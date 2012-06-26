{assign var="iNextPage" value=$iPage+1}
{assign var="sPictureEnd" value=$iPosStart+$iPicCnt}

{if !$isPjax}
	{include file='header.tpl' menu='profile' showWhiteBack=false}
	<h1>
	{if $oConfig->GetValue('lang.current') == 'russian'}
		{if ($oPictureCount == 0) OR ($oPictureCount > 5) AND ($oPictureCount < 20)}
			{$oPictureCount} фотографий
		{else}
			{assign var="piccntmod" value=$oPictureCount%10}
			{if $piccntmod == 1}
				{$oPictureCount} фотография
			{elseif $piccntmod > 1 AND $piccntmod < 5}
				{$oPictureCount} фотографии
			{else}
				{$oPictureCount} фотографий
			{/if}
		{/if}
	{else}
		{$oPictureCount} {$aLang.picalbums_picture_title} 
	{/if}
	{$oUserProfile->getLogin()} {if $iPage}/ {$aLang.picalbums_pagetitle} {$iNextPage}{/if}
	</h1>
{/if}

{if $aAlbums}
	{assign var="iPicturePos" value=0}	
	{foreach from=$aAlbums item=oAlbum}
		{assign var=bIsContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}

		{if $bIsContinue}
			{assign var="aPictures" value=$oAlbum->GetPictures()}
			{if $aPictures}
				{foreach from=$aPictures item=oPicture}
					{if $iPicturePos >= $iPosStart AND $iPicturePos < $sPictureEnd}
						{include file="$sIncludesTplPath/picture_preview.tpl" sAlbumPathStart=$oUserProfile->getUserAlbumsWebPath()}
					{/if}
					{assign var="iPicturePos" value=$iPicturePos+1}
				{/foreach}
			{/if}
		{/if}
	{/foreach}
	
	{if $oPictureCount > $sPictureEnd}
		<div id="pjaxmore{$iPage}"><a class="pjaxmorepics{$iPage} friends-last-foto-get-more" href='{$oUserProfile->getUserAlbumsWebPath()}allpictures/{$iNextPage}/' >{$aLang.picalbums_pagination_more}</a></div>
	{/if}
{/if}

<script type="text/javascript">
	$('.pjaxmorepics{$iPage}').pjax('#pjaxmore{$iPage}', {literal}{push: false,timeout : 5000}{/literal});
</script>

{if !$isPjax}
	{include file='footer.tpl'}
{/if}
