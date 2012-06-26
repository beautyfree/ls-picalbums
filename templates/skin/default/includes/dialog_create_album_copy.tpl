<div class="link"><a href="" onclick="picalbums.showDialogForCreateAlbum(); return false;"">{$aLang.picalbums_do_copy_small}</a></div>
<div id="createalbum-dialog" title="{$aLang.picalbums_create_album_for_copy}: " style="display: none;">
    {include file="$sIncludesTplPath/albumcreate.tpl" aCategories=null isVisibility=true iUserId=$oUserCurrent->getId() sAppendFunction="appendAlbumForCopy"}
</div>