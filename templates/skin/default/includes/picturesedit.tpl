<div class="change_placed_pictures">
	<div class="photo-albums" id="photo-albums_{$oPicture->getId()}">
		<a href="{$sAlbumPath}{$oAlbum->getURL()}/{$oPicture->getURL()}/" id="navigation picture_link_{$oPicture->getId()}">
			<img src="{$oPicture->getMiniaturePath()}" class="album" alt="{$oPicture->getDescription()}" />
		</a>
		<div class="album-title">
			<p>
                {if !$bIsDisableSort}
				    <a class="move" title="{$aLang.picalbums_move_picture_in_anothe_place}" id="change_placed_picture_link_{$oPicture->getId()}" href="" onclick="picalbums.ChangePlaced({$oPicture->getId()}); return false;"></a>
                {/if}
				<input type="radio" name="album_cover" class="album_cover" id="picture_cover_{$oPicture->getId()}" value="picture_cover" {if $iCoverPictureId == $oPicture->getId()}checked{/if}>{$aLang.picalbums_pictures_cover}
				<input type="checkbox" name="picture_delete" class="picture_delete" id="picture_delete_{$oPicture->getId()}" value="picture_cover">{$aLang.picalbums_pictures_delete}
                {if $oPicture->getIsModer() == 0}
                    <span class="notmoderedtext">({$aLang.picalbums_need_moder})</span>
                {/if}
			</p>
		</div>
		<div class="album-about">
			<textarea class="picture_description_text" id="picture_description_text_{$oPicture->getId()}" style="width: 100%; height: 54px;">{$oPicture->getDescription()}</textarea>		
		</div>
	</div>
</div>
