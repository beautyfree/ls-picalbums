{include file='header.tpl' menu='profile' showWhiteBack=false}	

{include file="$sIncludesTplPath/albumedit.tpl" isVisibility=true sAlbumPath=$oUserProfile->getUserAlbumsWebPath() iUserId=$oUserProfile->getId()}

{include file='footer.tpl'}
