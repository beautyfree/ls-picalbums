{include file='header.tpl' menu="mainalbums" showWhiteBack=false}	

{include file="$sIncludesTplPath/albumcreate.tpl" isVisibility=false sAlbumPath=$sMainAlbumsRouter iUserId={$oConfig->GetValue('plugin.picalbums.virtual_main_user_id')}}

{include file='footer.tpl'}
