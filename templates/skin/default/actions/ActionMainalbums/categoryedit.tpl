{include file='header.tpl' menu="mainalbums" showWhiteBack=false}	

<h1>{$aLang.picalbums_category_edit}</h1>

<div class="category_form_edit">
	<form action="" method="POST" id="form_category_edit" onsubmit="return false;" enctype="multipart/form-data">
		
		<p>
			<label id="album_title_label">{$aLang.picalbums_category_name_title}:</label><br/>
			<input name="category_title_text" id="category_title_text" type="text" value="{$oCategory->getTitle()}" name="title" class="input-wide">
		</p>
		
		<input 	type="submit" 
				name="submit_category" 
				id="submit_category" 
				value="{$aLang.picalbums_category_edit_submit}" 
				onclick="picalbums.editCategory('form_category_edit', {$oCategory->getId()}, '{router page='settings'}picalbums/'); return false;" />	
				
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}">
	</form>
</div>


{include file='footer.tpl'}
