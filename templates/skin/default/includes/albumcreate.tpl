<div class="album_form_add">
    <h1>{$aLang.picalbums_album_add}</h1>
	<form action="" method="POST" id="form_album_append" onsubmit="return false;" enctype="multipart/form-data">		
		<p>
			<label id="album_title_label">{$aLang.picalbums_album_add_title}:</label><br/>
			<input name="album_title_text" id="album_title_text" type="text" value="" class="input-wide">
			<br />
			<span class="note">{$aLang.picalbums_album_add_title_note}</span>
		</p>
		
		<p>
			<label id="album_description_label"><div class="picalbums_comment-counter"></div></label>
			<textarea name="album_description_text" id="album_description_text" class="input-wide"></textarea>
		</p>
		
		{if $isVisibility}
			<p>
				<select name="album_visibility">
					<option selected value="0">{$aLang.picalbums_album_visibility_all}</option>
					<option value="1">{$aLang.picalbums_album_visibility_auth}</option>
					<option value="2">{$aLang.picalbums_album_visibility_friends}</option>
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
					<option selected value="0">{$aLang.picalbums_not_need_moder}</option>
					<option value="1">{$aLang.picalbums_need_moder}</option>
				</select>
			</p>
            {/if}
		{/if}
		
		{if $aCategories}
            <p>
                <label>{$aLang.picalbums_category_name_title}:</label><br/>
                <select name="category_id">
                    {foreach from=$aCategories item=oCategory}
                    <option value="{$oCategory->getId()}">{$oCategory->getTitle()}</option>
                    {/foreach}
                </select>
            </p>
		{/if}

        <p>
			<label id="album_tag_label">{$aLang.picalbums_album_add_tag}:</label><br/>
			<input name="album_tag_text" id="album_tag_text" type="text" value="" class="input-wide">
		</p>
		
		<input 	type="submit" 
				name="submit_album" 
				id="submit_album" 
				value="{$aLang.picalbums_album_add_submit}" 
				onclick="picalbums.{if !$sAppendFunction}appendAlbum{else}{$sAppendFunction}{/if}('form_album_append', '{$sAlbumPath}'); return false;" />
				
		<input type="hidden" name="album_add_user_target_id" value="{$iUserId}">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}">
	</form>
</div>
