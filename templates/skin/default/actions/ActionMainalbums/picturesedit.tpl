{include file='header.tpl' menu="mainalbums" showWhiteBack=false}	

<h1>{$aLang.picalbums_pictures_edit}</h1>

{assign var="iPictureCnt" value=0}

{if $aPictures}		
	<ul id="pictures_listing">
		{foreach from=$aPictures item=oPicture}
			{assign var="isContinue" value=true}
			{assign var="isShowCover" value=true}
			
			{if $oAlbum->getAddUserId() != $oUserCurrent->getId() AND !$oUserCurrent->isAdministrator()}
				{assign var="isShowCover" value=false}
				{if $oPicture->getAddUserId() != $oUserCurrent->getId()}
					{assign var="isContinue" value=false}
				{/if}
			{/if}
			
			{if $isContinue}
				{assign var="iPictureCnt" value=$iPictureCnt+1}
				{assign var="oCoverPicture" value=$oAlbum->GetCoverPicture()}
				{if $oCoverPicture}
					{assign var="iCoverPictureId" value=$oCoverPicture->getId()}
				{else}
					{assign var="iCoverPictureId" value=null}
				{/if}
				<li id="picturesort_{$oPicture->getId()}">
					{include file="$sIncludesTplPath/picturesedit.tpl" sAlbumPath=$sMainAlbumsRouter}
				</li>
			{/if}
		{/foreach}
	</ul>

    <a href="" onclick='$(".picture_delete").attr("checked","checked"); return false;'>{$aLang.picalbums_select_all_for_delete}</a>
{/if}	

{if $iPictureCnt == 0}
	{$aLang.picalbums_picturesedit_nophoto}
{else}
	<input 	class="right" type="submit" 
			name="submit_pictures"
			value="{$aLang.picalbums_pictures_submit}" 
			onclick="picalbums.editPictures({$oAlbum->getId()}, '{router page="$sMainAlbumsRouterName"}{$oAlbum->getURL()}/'); return false;" />
{/if}

<script type="text/javascript" src="{$sTemplateWebPathPicalbumsPlugin}/js/picalbums-sort.js"></script>

{include file='footer.tpl'}