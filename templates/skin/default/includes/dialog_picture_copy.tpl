<div class="link"><a href="" onclick="picalbums.showDialogForCopyPicture(); return false;"">{$aLang.picalbums_do_copy_small}</a></div>
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