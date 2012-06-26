{if !$isPjax}
	{include file='header.tpl' menu='profile' showWhiteBack=false}
	<link rel='stylesheet' type='text/css' href="{cfg name='path.root.engine_lib'}/external/prettyPhoto/css/prettyPhoto.css" />
{/if}

{include file="$sIncludesTplPath/picture_show_js.tpl" sAlbumPath=$oUserProfile->getUserAlbumsWebPath()}

<div id="picture_listing_pjax">
	{assign var="oUserAddOwner" value=$oUserProfile}
	{assign var="bCopyFunctionalEnable" value=($oUserCurrent and $oConfig->GetValue('plugin.picalbums.functional_copy_picture_ebable') and $aCurrentUserAlbums)}

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

    {if $bCopyFunctionalEnable}
        <div id="currentuserpictures-dialog" title="{$aLang.picalbums_select_album_for_copy}: " style="display: none;">
			<p>
				<select name="current_user_albums" id="current_user_albums">
                    {foreach from=$aCurrentUserAlbums item=oAlbum}
					    <option value="{$oAlbum->getId()}">{$oAlbum->getTitle()}</option>
                    {/foreach}
				</select>
			</p>
            <p>
                <a class="button-del button-del-blue" href="" onclick="picalbums.copyPicture(); return false;"><span>{$aLang.picalbums_do_copy}</span></a>
            </p>
        </div>
	{/if}
	{include file='footer.tpl'}
{/if}