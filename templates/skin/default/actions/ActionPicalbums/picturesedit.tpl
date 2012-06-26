{include file='header.tpl' menu='profile' showWhiteBack=false}	

<h1>{$aLang.picalbums_pictures_edit}</h1>

{if $aPictures}
	<ul id="pictures_listing">
		{foreach from=$aPictures item=oPicture}
			{assign var="oCoverPicture" value=$oAlbum->GetCoverPicture()}
			{if $oCoverPicture}
				{assign var="oCoverPictureId" value=$oCoverPicture->getId()}
			{else}
				{assign var="oCoverPictureId" value=null}
			{/if}

			<li id="picturesort_{$oPicture->getId()}">
				{include file="$sIncludesTplPath/picturesedit.tpl" sAlbumPath=$oUserProfile->getUserAlbumsWebPath()}
			</li>		
		{/foreach}
	</ul>

    <a href="" onclick='$(".picture_delete").attr("checked","checked"); return false;'>{$aLang.picalbums_select_all_for_delete}</a>
	<input 	class="right" type="submit" 
			name="submit_pictures"
			value="{$aLang.picalbums_pictures_submit}" 
			onclick="picalbums.editPictures({$oAlbum->getId()}, '{$oUserProfile->getUserAlbumsWebPath()}{$oAlbum->getURL()}/'); return false;" />
{/if}

<script type="text/javascript" src="{$sTemplateWebPathPicalbumsPlugin}/js/picalbums-sort.js"></script>

{include file='footer.tpl'}