{if $oConfig->GetValue('plugin.picalbums.ajax_upload_progress_disable')}
<style type="text/css">
	.qq-upload-list { 
		display: none;
	}
</style>
{/if}

{if $oConfig->GetValue('plugin.picalbums.flash_upload_progress_disable')}
<style type="text/css">
	#queuestatus { 
		display:none;
	}
	#log {
		display:none;
	}
</style>
{/if}

<div class="picalbums_apload_form">
	<ul class="multiupload_tabs multiupload_tabs1">
		<li class="multiupload_tab1 tab-current"><a>{$aLang.picalbums_albumshow_multiload_ajax}<div class="album-loader" id="album-loader_{$oAlbum->GetId()}"></div></a></li>
		<li class="multiupload_tab2"><a>{$aLang.picalbums_albumshow_multiload_flash}<div class="flash-album-loader" id="flash-album-loader_{$oAlbum->GetId()}"></div></a></li>
		<li class="multiupload_tab3"><a>{$aLang.picalbums_albumshow_multiload}</a></li>
	</ul>
	<div class="multiupload_tab1">
		<div id="file-uploader">       
			<noscript>          
				<p>{$aLang.picalbums_albumshow_error_javascript}</p>
			</noscript>         
		</div>
	</div>
	<div class="multiupload_tab2">
		<div id="swfupload-control">  
			<div id="swfuploadbutton"></div>
			<p id="queuestatus" ></p>  
			<ol id="log"></ol>  
		</div>
	</div>
	<div class="multiupload_tab3">
		<form id="picalbums-upload-form" method="POST" enctype="multipart/form-data" onsubmit="return false;">
			<p id="topic-photo-upload-input" class="topic-photo-upload-input">
				<div>
					<label>{$aLang.picalbums_albumshow_albums_title}:</label><br/>
					<input id="picture_img_title" type="text" class="input-wide" value="" name="title">
				</div>
				
				<div>
					<label>{$aLang.picalbums_albumshow_albums_upload_file}:</label><br />
					<input type="file" id="picalbums-upload-file" name="Filedata" /><br><br>
				</div>
				
				<button onclick="picmulti.picalbums.upload();">{$aLang.picalbums_albumshow_albums_upload_button}</button>
				<input type="hidden" name="is_iframe" value="true" />
			</p>
		</form>	
	</div>
</div>