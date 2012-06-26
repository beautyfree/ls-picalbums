{assign var="iNextPage" value=$iPage+1}
{assign var="sPictureEnd" value=$iPosStart+$iPicCnt}

{if !$isPjax}
	{include file='header.tpl' menu='profile' showWhiteBack=false}
	<h1>
	{if $oConfig->GetValue('lang.current') == 'russian'}
		{if ($iPictureCount == 0) OR ($iPictureCount > 5) AND ($iPictureCount < 20)}
			{$iPictureCount} фотографий,
		{else}
			{assign var="piccntmod" value=$iPictureCount%10}
			{if $piccntmod == 1}
				{$iPictureCount} фотография,
			{elseif $piccntmod > 1 AND $piccntmod < 5}
				{$iPictureCount} фотографии,
			{else}
				{$iPictureCount} фотографий,
			{/if}
		{/if}
	{else}
		{$iPictureCount} {$aLang.picalbums_picture_title} ,
	{/if}
    {$aLang.picalbums_added_favourite} {if $iPage}/ {$aLang.picalbums_pagetitle} {$iNextPage}{/if}
	</h1>
{/if}

{assign var="iPicturePos" value=0}

{if $aPictures}
    {foreach from=$aPictures item=oPicture}
        {if $iPicturePos >= $iPosStart AND $iPicturePos < $sPictureEnd}
            {assign var="oAlbum" value=$oPicture->getAlbumOwner()}
            {if $oAlbum->getUserId() == $oConfig->GetValue('plugin.picalbums.virtual_main_user_id')}
                {assign var="sAlbumURL" value=$sMainAlbumsRouter}
            {else}
                {assign var="oUserOwner" value=$oAlbum->getUserOwner()}
                {assign var="sAlbumURL" value=$oUserOwner->getUserAlbumsWebPath()}
            {/if}

            {include file="$sIncludesTplPath/picture_preview.tpl" sAlbumPathStart=$sAlbumURL}
        {/if}
        {assign var="iPicturePos" value=$iPicturePos+1}
    {/foreach}
{/if}
	
{if $iPictureCount > $sPictureEnd}
    <div id="pjaxmore{$iPage}"><a class="pjaxmorepics{$iPage} friends-last-foto-get-more" href='{$oUserProfile->getUserAlbumsWebPath()}favourite/{$iNextPage}/' >{$aLang.picalbums_pagination_more}</a></div>
{/if}


<script type="text/javascript">
	$('.pjaxmorepics{$iPage}').pjax('#pjaxmore{$iPage}', {literal}{push: false,timeout : 5000}{/literal});
</script>

{if !$isPjax}
	{include file='footer.tpl'}
{/if}
