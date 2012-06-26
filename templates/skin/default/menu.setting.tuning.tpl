<p>
	<label><input {if $CommentNotifyEmail == 1}checked{/if} type="checkbox" id="settings_picalbums_comment_notice" name="settings_picalbums_comment_notice" value="1" class="checkbox" />{$aLang.picalbums_settings_comment_notice} </label><br />
	<label><input {if $MarkNotifyEmail == 1}checked{/if} type="checkbox" id="settings_picalbums_mark_notice" name="settings_picalbums_mark_notice" value="1" class="checkbox" />{$aLang.picalbums_settings_mark_notice} </label>
</p>