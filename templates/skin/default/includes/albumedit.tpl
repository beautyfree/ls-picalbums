<script type="text/javascript" src="{$sTemplateWebPathPicalbumsPlugin}/js/picalbums-textareas.js"></script>

<h1>{$aLang.picalbums_album_edit}</h1>

<div class="album_form_add">
	<form action="" method="POST" id="form_album_edit" onsubmit="return false;" enctype="multipart/form-data">		
		<p>
			<label id="album_title_label">{$aLang.picalbums_album_add_title}:</label><br/>
			<input name="album_title_text" id="album_title_text" type="text" value="{$oAlbum->getTitle()}" name="title" class="input-wide">
			<br />
			<span class="note">{$aLang.picalbums_album_add_title_note}</span>
		</p>
		
		<p>
			<label id="album_description_label"><div class="picalbums_comment-counter"></div></label>
			<textarea name="album_description_text" id="album_description_text" class="input-wide">{$oAlbum->getDescription()}</textarea>
		</p>
		
		{if $isVisibility}
			<p>
				<select name="album_visibility">
					<option {if $bAlbumVisibility == 0}selected{/if} value="0">{$aLang.picalbums_album_visibility_all}</option>
					<option {if $bAlbumVisibility == 1}selected{/if} value="1">{$aLang.picalbums_album_visibility_auth}</option>
					<option {if $bAlbumVisibility == 2}selected{/if} value="2">{$aLang.picalbums_album_visibility_friends}</option>
				</select>
			</p>
            <input name="album_need_moder" id="album_need_moder" type="hidden" value="0" >
		{else}
			<input name="album_visibility" id="album_visibility" type="hidden" value="0" >
            {if $oConfig->GetValue('plugin.picalbums.create_moderated_only_for_moderators') and (!in_array($oUserCurrent->getLogin(), $oConfig->GetValue('plugin.picalbums.moderators')))}
                <input name="album_need_moder" id="album_need_moder" type="hidden" value="0" >
            {else}
                <p>
                    <label>{$aLang.picalbums_moderation_info}</label><br/>
                    <select name="album_need_moder">
                        <option {if $bNeedModer == 0}selected{/if} value="0">{$aLang.picalbums_not_need_moder}</option>
                        <option {if $bNeedModer == 1}selected{/if} value="1">{$aLang.picalbums_need_moder}</option>
                    </select>
                </p>
            {/if}
		{/if}
		
		{if $aCategories}
            <p>
                <label>{$aLang.picalbums_category_name_title}:</label><br/>
                <select name="category_id">
                    {foreach from=$aCategories item=oCategory}
                    <option {if $oAlbum->getCategoryId() == $oCategory->getId()}selected{/if} value="{$oCategory->getId()}">{$oCategory->getTitle()}</option>
                    {/foreach}
                </select>
            </p>
		{/if}

        <p>
			<label id="album_tag_label">{$aLang.picalbums_album_add_tag}:</label><br/>
			<input name="album_tag_text" id="album_tag_text" type="text" value="{$sTag}" class="input-wide">
		</p>
		
		<input 	type="submit" 
				name="submit_album" 
				id="submit_album" 
				value="{$aLang.picalbums_album_edit_submit}" 
				onclick="picalbums.editAlbum('form_album_edit', {$oAlbum->getId()}, '{$sAlbumPath}'); return false;" />	
				
		<input type="hidden" name="album_add_user_target_id" value="{$iUserId}">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}">
	</form>
</div>
