{assign var="iNextPage" value=$iPage+1}

<div class="picture_album_class">
	<div class="picture_album_class_inner">
		<h1>
            {if $bWithLogin}
                <a href="{$sAlbumPathStart}">{$aLang.picalbums_albumshow_albums} {$bWithLogin}</a> &rarr;
            {/if}
            {if $bShowCategory}
                {if $oCategory}
                    <a href="{$sMainAlbumsRouter}category/{$oCategory->getId()}/">{$aLang.picalbums_category} "{$oCategory->getTitle()}"</a> &rarr;
                {else}
                    <a href="{$sMainAlbumsRouter}category/noname/">{$aLang.picalbums_main_albums_noncategory}</a> &rarr;
                {/if}
            {/if}
            {$oAlbum->getTitle()}
            {if $iPage}/ {$aLang.picalbums_pagetitle} {$iPage+1}{/if}
            {if $oUserCurrent}
                {if $oAlbum->GetUserNeedBeModerated($oUserCurrent) == 1}
                    ({$aLang.picalbums_album_om_moder})
                {/if}
            {/if}
        </h1>
		
		{if $bIsCanAppend == true}
            <script type="text/javascript" src="{$sTemplateWebPathPicalbumsPlugin}/js/picalbums-albumshow.js" ></script>
			{include file="$sIncludesTplPath/upload_image_form.tpl"}
			<a href="{$sAlbumPathStart}{$oAlbum->getURL()}/picturesedit/" class="photo-edit">{$aLang.picalbums_albumshow_albums_edit_photo}</a>
		{/if}
		
		<div class="{if $oAlbum->GetUserNeedBeModerated($oUserCurrent) == 0}pictures_for_ajax_upload{/if} albums-preview-panel">
			{assign var="sPictureEnd" value=$iPosStart+$iPicCnt}
			{if $aPictures}		
				{assign var="iPicturePos" value=0}
				{foreach from=$aPictures item=oPicture}
					{if $iPicturePos >= $iPosStart AND $iPicturePos < $sPictureEnd}
						{include file="$sIncludesTplPath/picture_preview.tpl" sAlbumPathStart=$sAlbumPathStart}
					{/if}
					{assign var="iPicturePos" value=$iPicturePos+1}
				{/foreach}
            {else}
                <div id="empty_album">{$aLang.picalbums_albumshow_album_hasnot_pictures}</div>
			{/if}
		</div>


        {if $oAlbum->getNeedModer() == 1}
            {if $oAlbum->GetUserIsModerator($oUserCurrent) == 1}
                {assign var="aNonModeratedPictures" value=$oAlbum->GetNonModeratedPictures()}
                {if $aNonModeratedPictures}
                    <br/><h1>{$aLang.picalbums_pictures_om_moder}:</h1>

                    <div class="albums-preview-panel">
                        {foreach from=$aNonModeratedPictures item=oPicture}
                            {include file="$sIncludesTplPath/picture_preview.tpl" sAlbumPathStart=$sAlbumPathStart}
                        {/foreach}
                    </div>
                    <a class="button-del button-del-blue" href="" onclick="picalbums.moderateAlbum({$oAlbum->getId()}); return false;"><span>{$aLang.picalbums_moder_ok}</span></a>
                {/if}
            {else}
                {if $oUserCurrent}
                    <br/><h1>{$aLang.picalbums_your_pictures_om_moder}:</h1>
                    <div class="pictures_for_ajax_upload albums-preview-panel">
                        {assign var="aNonModeratedPictures" value=$oAlbum->GetUserNonModeratedPictures($oUserCurrent)}
                        {if $aNonModeratedPictures}
                            {foreach from=$aNonModeratedPictures item=oPicture}
                                {include file="$sIncludesTplPath/picture_preview.tpl" sAlbumPathStart=$sAlbumPathStart}
                            {/foreach}
                        {else}
                            <div id="empty_album">{$aLang.picalbums_albumshow_album_hasnot_user_moder_pictures}</div>
                        {/if}
                    </div>
                {/if}
            {/if}
        {/if}

		{if $aPictures}
			{assign var="iNextPage" value=$iPage+1}
			{if $iPicturePos > $sPictureEnd}
				<div id="pjaxmore{$iPage}" class="pjaxmore">
					<a class="pjaxmorepics{$iPage} friends-last-foto-get-more" href='{$sAlbumPathStart}{$oAlbum->getURL()}/p/{$iNextPage}/' >{$aLang.picalbums_pagination_more}</a>
				</div>
			{/if}
		{/if}
	</div>
</div>

<div id="heart-dialog" title="{$aLang.picalbums_albumshow_heart_dialog}: " style="display: none;">
	<div id="heart-dialog-content">
	</div>
</div>

<div id="exif-dialog" title="{$aLang.picalbums_albumshow_exif_dialog}: " style="display: none;">
	<div id="exif-dialog-content">		
	</div>
</div>

<script type="text/javascript">
	$('.pjaxmorepics{$iPage}').pjax('#pjaxmore{$iPage}', {literal}{push: false,timeout : 5000}{/literal});
    var picalbums_album_id = {$oAlbum->getId()};
</script>
