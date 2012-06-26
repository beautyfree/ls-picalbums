<div class="showedpicture" id="{$oPicture->getId()}">
	<div style="text-align: center;">
		<div class="picturecontent">
			{if $sNextURL}
				{if $oConfig->GetValue('plugin.picalbums.pjax_for_picture_listing') == false}
					<a id="picture_link_navigation" href="{$sAlbumPath}{$oAlbum->getURL()}/{$sNextURL}/">
                {else}
                    <a id="picture_link_navigation" class="navigation_next_picture_link_pjax" href="{$sAlbumPath}{$oAlbum->getURL()}/{$sNextURL}/">
                {/if}
            {/if}
						<div class="samples-box clearfix">
                            <div id="jquery-notes_1" class="jquery-notes-container clearfix" style="height: auto;">
							    <div class="notes clearfix">
                                    <img id="loaded_image" alt="{$oPicture->getDescription()}" src="{$oPicture->getPicPath()}" class="jquery-note_picture" />
                                </div>
                            </div>
						</div>
            {if $sNextURL}
					</a>
            {/if}
		</div>
		
		<div id="nonmark_confirm">
			{if $iNonConfirmMark > 0}
				<a href="" onclick="picalbums.markConfirm({$oPicture->getId()}, 0); return false;">{$aLang.picalbums_albumshow_mark_confirm_yes}</a> - <a href="" onclick="picalbums.markConfirm({$oPicture->getId()}, 1); return false;">{$aLang.picalbums_albumshow_mark_confirm_no}</a>
			{/if}
		</div>
		{if $oPicture->getOriginalPath() != null AND $oPicture->getOriginalPath() != 'null'}
				<div id="divoriginalimagebiggallary">
				{if $oConfig->GetValue('plugin.picalbums.original_photo_navigation')}
					{foreach from=$aAllPictures item=oAllPicture}
						{if $oAllPicture->getId() == $oPicture->getId()}
							<a href="{$oPicture->getOriginalPath()}" rel="original-image[biggallary]" class="original-image original-imagegal" title="">
								{$aLang.picalbums_albumshow_original_zoom}
							</a>
						{else}
							<a 	style="display: none; width: 0px; heigth: 0px;" 
								href="{$oAllPicture->getOriginalPath()}" 
								rel="original-image[biggallary]" 
								class="original-imagegal" title="">
							</a>
						{/if}
					{/foreach}
				{else}
					<a href="{$oPicture->getOriginalPath()}" rel="original-image[biggallary]" class="original-image original-imagegal" title="">
						{$aLang.picalbums_albumshow_original_zoom}
					</a>
				{/if}
				</div>
		{/if}
		<div id="photo-nav">
			<span id="photo-nav-prev">
				{if $sPrevURL}
					{if $oConfig->GetValue('plugin.picalbums.pjax_for_picture_listing') == false}
						<a id="navigation" class="navigation_prev" href="{$sAlbumPath}{$oAlbum->getURL()}/{$sPrevURL}/">{$aLang.picalbums_albumshow_navigation_back}</a>
					{else}
						<a id="navigation" class="navigation_prev_pjax" href="{$sAlbumPath}{$oAlbum->getURL()}/{$sPrevURL}/">{$aLang.picalbums_albumshow_navigation_back}</a>
					{/if}
				{/if}
			</span>
		
			{$aLang.picalbums_albumshow_picture_current} {if $iCurrentPos}{$iCurrentPos} {if $iLastPos} {$aLang.picalbums_albumshow_picture_current_i} {$iLastPos}{/if}{/if}
			<span id="photo-nav-next">
				{if $sNextURL}
					{if $oConfig->GetValue('plugin.picalbums.pjax_for_picture_listing') == false}
						<a id="navigation" class="navigation_next" href="{$sAlbumPath}{$oAlbum->getURL()}/{$sNextURL}/">{$aLang.picalbums_albumshow_navigation_next}</a>
					{else}
						<a id="navigation" class="navigation_next_pjax" href="{$sAlbumPath}{$oAlbum->getURL()}/{$sNextURL}/">{$aLang.picalbums_albumshow_navigation_next}</a>
					{/if}
				{/if}
			</span>
		</div>
	</div>

	<div id="photo-data">
		<div class="left-col">
			<div class="markeduserlist">
				{if $aUserMarked}{$aLang.picalbums_albumshow_marked_userlist}: {foreach from=$aUserMarked item=userMarked name=usermarked}{if !$smarty.foreach.usermarked.first}, {/if}<a href="{$userMarked->getUserWebPath()}">{$userMarked->getLogin()}</a>{/foreach}{/if}
			</div>
			<div class="info">{$aLang.picalbums_albumshow_picture_date_add_note} {date_format date=$oPicture->getDateAdd()} | <a class="picture_comments_show" id="picture_comments_show_count" href="" onclick="picalbums.showCommentsWithClear({$oPicture->getId()}); return false;">{$aLang.picalbums_albumshow_picture_comments_note} ({$oPicture->getCommentCount()})</a> |
                <span>
                    <a class="like{if $bIsHeart}-active{/if}" id="heart_like" title="" href="#" onclick="picalbums.heartComment({$oPicture->getId()}); return false;">{$aLang.picalbums_albumshow_picture_heart_yes}</a><span id="heartcnt">{$iHeartCount}</span>
                    <div id="picture-heart-preview" style="display: none;">
                        {include file="$sIncludesTplPath/heart_avatar.tpl"}
                    </div>
                </span>
			</div>
			
			<div class="picture-desc">
			{$oPicture->getDescription()}
			
			{if $oConfig->GetValue('plugin.picalbums.exif_enable')}
				{if $oPicture->getExif()}
					<a class="exifinfo" href="" onclick="picalbums.showDialogWithExif(); return false;">&nbsp;</a>
				{/if}
			{/if}
			
			</div>
			{if $oUserCurrent}
				<a class="picture_comments_show" id="picture_comments_show_your_comments" href="" onclick="picalbums.toggleFromAppendComment(); return false;">{$aLang.picalbums_albumshow_picture_y_comment}</a>
				<div class="picalbums_comment_reply" {if $oConfig->GetValue('plugin.picalbums.show_comment_form_after_load_picture') == false}style="display: none;"{/if} >
					<form action="" method="POST" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
						<textarea name="comment_text" id="form_comment_text" class="input-wide"></textarea>
						<input 	type="submit" name="submit_comment" value="{$aLang.picalbums_albumshow_picture_comment_submit}" id="oComment-button-submit" 
								onclick="picalbums.AppendComment('form_comment',{$oPicture->getId()}); return false;" />
					</form>
				</div>
			{/if}

			<div class="piccomments">
				{if $oConfig->GetValue('plugin.picalbums.show_comments_after_load_picture') == true}
					{include file="$sIncludesTplPath/comments.tpl"}
				{/if}
			</div>
		</div>
	
		<div class="right-col">
			<div class="info">{$aLang.picalbums_albumshow_album_info_title}:
			</div>
			<div class="link">
				<a href="{$sAlbumPath}{$oAlbum->getURL()}/">{$oAlbum->getTitle()}</a>
			</div>
				
			{if $oUserAddOwner}
				<div class="info">{$aLang.picalbums_albumshow_album_info_author}:
				</div>
				<div class="link">
					<a href="{$oUserAddOwner->getUserWebPath()}">{$oUserAddOwner->getLogin()}</a>
				</div>
			{/if}

            {if $oUserCurrent}
                <div class="makenote">
                    <a class="add-note-link" title="add" href="javascript:void(0);">{$aLang.picalbums_make_note}</a>
                    <a class="reload-note-link" title="add" href="javascript:void(0);" style="display: none;" title="add" >!</a>
                </div>
            {/if}
			<div class="info">{$aLang.picalbums_albumshow_album_info_share}:</div>
			<div id="sharediv">
                <div id="share42">
                    {assign var="sLinkURL" value="`$sAlbumPath``$oAlbum->getURL()`/`$oPicture->getURL()`/"}
                    <a target="_blank" title="Поделиться в Facebook" href="http://www.facebook.com/sharer.php?u={$sLinkURL}&t={$oPicture->getDescription()|escape:'html'}" style="background:url({$sTemplateWebPathPicalbumsPlugin}/images/icons.png) -0px 0" rel="nofollow"></a>
                    <a target="_blank" title="Опубликовать в LiveJournal" href="http://www.livejournal.com/update.bml?event={$sLinkURL}&subject={$oPicture->getDescription()|escape:'html'}" style="background:url({$sTemplateWebPathPicalbumsPlugin}/images/icons.png) -16px 0" rel="nofollow"></a>
                    <a target="_blank" title="Поделиться в Моем Мире@Mail.Ru" href="http://connect.mail.ru/share?url={$sLinkURL}&title={$oPicture->getDescription()|escape:'html'}" style="background:url({$sTemplateWebPathPicalbumsPlugin}/images/icons.png) -32px 0" rel="nofollow"></a>
                    <a target="_blank" title="Добавить в Одноклассники" href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl={$sLinkURL}&title={$oPicture->getDescription()|escape:'html'}" style="background:url({$sTemplateWebPathPicalbumsPlugin}/images/icons.png) -48px 0" rel="nofollow"></a>
                    <a target="_blank" title="Добавить в Twitter" href="http://twitter.com/share?text={$oPicture->getDescription()|escape:'html'}&url={$sLinkURL}" style="background:url({$sTemplateWebPathPicalbumsPlugin}/images/icons.png) -64px 0" rel="nofollow"></a>
                    <a target="_blank" title="Поделиться В Контакте" onclick="window.open('http://vkontakte.ru/share.php?url={$sLinkURL}', '_blank', 'scrollbars=0, resizable=1, menubar=0, left=200, top=200, width=554, height=421, toolbar=0, status=0');return false" href="#" style="background:url({$sTemplateWebPathPicalbumsPlugin}/images/icons.png) -80px 0" rel="nofollow"></a>
                    <a target="_blank" title="Отправить на e-mail другу" href="http://www.feedburner.com/fb/a/emailFlare?loc=ru_RU&itemTitle={$oPicture->getDescription()|escape:'html'}&uri={$sLinkURL}" style="background:url({$sTemplateWebPathPicalbumsPlugin}/images/icons.png) -96px 0" rel="nofollow"></a>
                </div>
			</div>

            {if $oConfig->GetValue('plugin.picalbums.functional_copy_picture_enable')}
                {if !$oUserCurrent}
                    <a href="{router page='login'}" id="picalbums_login_form_show">{$aLang.picalbums_do_copy_small}</a>
                {else}
                    <div id="copy-picture-container">
                        {if $aCurrentUserAlbums}
                            {include file="$sIncludesTplPath/dialog_picture_copy.tpl"}
                        {else}
                            {include file="$sIncludesTplPath/dialog_create_album_copy.tpl"}
                        {/if}
                    </div>
                {/if}
            {/if}
		</div>
	</div>

    {if $oPicture->getIsModer() == 0}
        {if $oAlbum->GetUserIsModerator($oUserCurrent) == 1}
            <a class="button-del button-del-blue" href="" onclick="picalbums.moderatePicture({$oPicture->getId()}); return false;"><span>{$aLang.picalbums_moder_ok}</span></a>
        {/if}
    {/if}
</div>