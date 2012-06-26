{include file='header.tpl' menu='settings' showWhiteBack=true}

<link rel='stylesheet' type='text/css' href='{$sTemplateWebPathPicalbumsPlugin}/css/picalbums.css' />	

<h2>{$aLang.picalbums_settings_albums}</h2>

<div class="picalbums_setting" >		

	<form action="" method="POST" id="form_picalbums_setting" enctype="multipart/form-data">

		{if $bEnabledUsedAjax}
			<div>
				<p>
				<div class="picalbums_used_ajax">
					{$aLang.picalbums_used_ajax}
				</div>
				<div> 
					<select name="used_ajax">
						<option {if $bIsUsedAjax == 0}selected{/if} value="0">{$aLang.picalbums_used_ajax_no}</option>
						<option {if $bIsUsedAjax == 1}selected{/if} value="1">{$aLang.picalbums_used_ajax_yes}</option>
					</select>
				</div>
				</p>
			</div>
		{/if}
		
		<input 	type="submit" 
				name="submit_picalbums_settings" 
				value="{$aLang.picalbums_settings_albums_submit}" />
			
		<br/><br/>
	</form>
	<br/>
	{if $oUserCurrent->isAdministrator()}

		<p>{$aLang.picalbums_settings_blackuser_note}:<br />
		<input id="blackuserinput" type="text" value="" name="author" class="input-200">
		</p>
		
		<div id="blacklict_useslist">
		{if $aBlockUsers}
			{$aLang.picalbums_settings_blackuser_list}:<br/>		
			{foreach from=$aBlockUsers item=blockUser}
				<div id="blacklict_user_{$blockUser->getId()}">
					<a class="user" href="{$blockUser->getUserWebPath()}">{$blockUser->getLogin()}</a> - <a href="" onclick="picalbums.deleteFromBlacklist({$blockUser->getId()}); return false">{$aLang.picalbums_settings_blackuser_delete}</a>
				</div>
			{/foreach}		
		{/if}
		</div>
		
		{literal}
		<script type="text/javascript">
		$( "#blackuserinput" ).autocomplete({
				open: function () {
						$(".text-box").parent().css('display', 'block');
					},
				source: function(request, response){
					$.ajax({
					  url: aRouter[picalbumsConfig["picalbums_router_name"]]+'ajaxuserautocompleteblacklist/',
					  dataType: "json",
					  type: 'POST',
					  data:{
						security_ls_key: LIVESTREET_SECURITY_KEY,
						name_startsWith: request.term
					  },
					  success: function(data){
						response(data.aResult);
					  }
					});
				  },
			  minLength: 2
			});
			
		$('#blackuserinput').bind('keypress', function(e) {
			 var code = (e.keyCode ? e.keyCode : e.which);
			 if(code == 13) { 
				$('#blacllistuseradd').click();
			 }
		});
		</script>
		{/literal}
		<input 	type="submit" 
				name="blacllistuseradd" 
				id="blacllistuseradd" 
				value="{$aLang.picalbums_settings_blackuser_add}" 
				onclick="picalbums.addToBlackList('blackuserinput'); return false;" />
				
		<br/><br/><br/>

		{if $aCategories}
			<div class="category_listing">
            <ul id="category_listing_ul">
			{foreach from=$aCategories item=oCategory}
                <li id="categorysetting_{$oCategory->getId()}">
                <div class="category_setting" id="category_setting{$oCategory->getId()}">
                    <span class="categorytitle">{$oCategory->getTitle()}</span>
                    <span class="categorycontrol">
                        <a class="categoryedit" href="{router page="$sMainAlbumsRouterName"}categoryedit/{$oCategory->getId()}/">{$aLang.picalbums_albumslisting_album_edit}</a>
                        <a class="categoryremove" href="" onclick="picalbums.removeCategory({$oCategory->getId()}); return false;">{$aLang.picalbums_delete_text}</a>
                        <a class="move" id="change_placed_category_link_{$oCategory->getId()}" href="" onclick="return false;"></a>
                    </span>
                </div>
                </li>
			{/foreach}
			</div>
            </ul>
		{/if}

        <script type="text/javascript" src="{$sTemplateWebPathPicalbumsPlugin}/js/picalbums-sort.js"></script>

		<form id="new_category_save" method="POST" enctype="multipart/form-data">
			<p>
				<div>
					<label for="">{$aLang.picalbums_category_name_title}</label><br />
					<input type="text" id="categoty_text_name" name="categoty_text_name" /><br><br>
				</div>
				
				<input type="submit" name="new-category-form-submit" value="{$aLang.picalbums_settings_albums_submit}" />
			</p>
		</form>	
	{/if}
	
</div>

	
{include file='footer.tpl'}
