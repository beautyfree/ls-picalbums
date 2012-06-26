{if !$isPjax}
	{include file='header.tpl' menu='mainalbums' showWhiteBack=false}
	{assign var="nextPage" value=$iPage+1}
    <h1 class="albums-page-title">{$aLang.picalbums_main_albums_listing}</h1>
{/if}

{assign var=iShowCategoryCnt value=0}
{assign var=iCategoryEnd value=$iPosStart+$iCategoryCnt}

{if $aCategories}
	{foreach from=$aCategories item=oCategory}
		{if $iShowCategoryCnt >= $iPosStart AND $iShowCategoryCnt < $iCategoryEnd}
			{assign var=aAlbums value=$oCategory->getAlbums($oConfig->GetValue('plugin.picalbums.virtual_main_user_id'))}
			
			{if $aAlbums}	
				<h2 class="category_title" id="category_title"><a href="{$sMainAlbumsRouter}category/{$oCategory->getId()}/">{$oCategory->getTitle()}</a></h2>
				
				{assign var=iAlbumsPreviewCnt value=0}
				<div class="albums-preview-panel">
					{foreach from=$aAlbums item=oAlbum}
						{if $iAlbumsPreviewCnt < $oConfig->GetValue('plugin.picalbums.categories_listing_max_albums_preview_cnt')}						
							{assign var=oCoverPicture value=$oAlbum->GetCoverPicture()}
								
							{include file="$sIncludesTplPath/album_preview.tpl" sAlbumPathStart=$sMainAlbumsRouter}
						{else}
							{break}
						{/if}
						{assign var=iAlbumsPreviewCnt value=$iAlbumsPreviewCnt+1}
					{/foreach}
				</div>
			{/if}
		{/if}
		{assign var=iShowCategoryCnt value=$iShowCategoryCnt+1}
	{/foreach}
{/if}

{if $aNonCatAlbums}
	{if $iShowCategoryCnt >= $iPosStart AND $iShowCategoryCnt < $iCategoryEnd}
		<h2 class="category_title" id="category_title">
            <a href="{$sMainAlbumsRouter}category/noname/">{$aLang.picalbums_main_albums_noncategory}</a>
        </h2>
		
		{assign var=iAlbumsPreviewCnt value=0}
		{foreach from=$aNonCatAlbums item=oAlbum}
			{if $iAlbumsPreviewCnt < $oConfig->GetValue('plugin.picalbums.categories_listing_max_albums_preview_cnt')}
				{assign var=iAlbumsPreviewCnt value=$iAlbumsPreviewCnt+1}
				{assign var=oCoverPicture value=$oAlbum->GetCoverPicture()}
					
				{include file="$sIncludesTplPath/album_preview.tpl" sAlbumPathStart=$sMainAlbumsRouter}
			{else}
				{break}
			{/if}
		{/foreach}
	{/if}
    {assign var=iShowCategoryCnt value=$iShowCategoryCnt+1}
{/if}

{if $iShowCategoryCnt == 0}
	{$aLang.picalbums_mainalbumslisting_user_none_albums}
{/if}

{if $iShowCategoryCnt > $iCategoryEnd}
	<div id="pjaxmore{$iPage}" class="pjaxmore">
		<a class="pjaxmorepics{$iPage} friends-last-foto-get-more" href='{router page="$sMainAlbumsRouterName"}p/{$nextPage}/' >{$aLang.picalbums_pagination_more}
		</a>
	</div>
{/if}

<script type="text/javascript">
	$('.pjaxmorepics{$iPage}').pjax('#pjaxmore{$iPage}', {literal}{push: false,timeout : 5000}{/literal});
</script>

{if !$isPjax}
    {if $oConfig->GetValue('plugin.picalbums.best_pictures_slider_enable')}
        <div class="main-slides-block ">
            <h1>{$aLang.picalbums_best_pictures_slider}</h1>
            {include file="$sIncludesTplPath/pictures_slide.tpl" aPictures=$aBestPictures}
        </div>
        <div style="clear: both;"></div>
    {/if}

	{include file='footer.tpl'}
{/if}
