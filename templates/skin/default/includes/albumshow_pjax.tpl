{assign var="sPictureEnd" value=$iPosStart+$iPicCnt}
{if $aPictures}		
	{assign var="iPicturePos" value=0}
	{foreach from=$aPictures item=oPicture}
		{if $iPicturePos >= $iPosStart AND $iPicturePos < $sPictureEnd}
			{include file="$sIncludesTplPath/picture_preview.tpl" sAlbumPathStart=$sAlbumPathStart}
		{/if}
		{assign var="iPicturePos" value=$iPicturePos+1}
	{/foreach}
	
	{assign var="iNextPage" value=$iPage+1}
	{if $iPicturePos > $sPictureEnd}
		<div id="pjaxmore{$iPage}">
			<a class="pjaxmorepics{$iPage} friends-last-foto-get-more" href='{$sAlbumPathStart}{$oAlbum->getURL()}/p/{$iNextPage}/' >{$aLang.picalbums_pagination_more}</a></div>
	{/if}
{/if}

<script type="text/javascript">
	$('.pjaxmorepics{$iPage}').pjax('#pjaxmore{$iPage}', {literal}{push: false,timeout : 5000}{/literal});
	$('.pjaxmore .container-photo').appendTo('.pictures_for_ajax_upload');
</script>
