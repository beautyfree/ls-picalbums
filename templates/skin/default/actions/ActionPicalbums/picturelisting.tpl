{if !$isPjax}
	{include file='header.tpl' menu='profile' showWhiteBack=false}
	<link rel='stylesheet' type='text/css' href="{cfg name='path.root.engine_lib'}/external/prettyPhoto/css/prettyPhoto.css" />
{/if}

{include file="$sIncludesTplPath/picture_show_js.tpl" sAlbumPath=$oUserProfile->getUserAlbumsWebPath()}

<div id="picture_listing_pjax">
	{assign var="oUserAddOwner" value=$oUserProfile}
	{include file="$sIncludesTplPath/picture_show.tpl" sAlbumPath=$oUserProfile->getUserAlbumsWebPath()}
</div>


{if !$isPjax}
	<div id="heart-dialog" title="Эта фотография нравится: " style="display: none;">
		<div id="heart-dialog-content">
		</div>
	</div>
	
	{if $oConfig->GetValue('plugin.picalbums.exif_enable')}
		{if $oPicture->getExif()}
			<div id="exif-dialog" title="{$aLang.picalbums_albumshow_exif_dialog}: " style="display: none;">
				<div id="exif-dialog-content">
					{$oPicture->getExif()}
				</div>
			</div>
		{/if}
	{/if}

	{include file='footer.tpl'}
{/if}