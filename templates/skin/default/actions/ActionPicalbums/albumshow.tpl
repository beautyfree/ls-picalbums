{include file='header.tpl' menu='profile' showWhiteBack=false}

{include file="$sIncludesTplPath/albumshow_main.tpl" sAlbumPathStart=$oUserProfile->getUserAlbumsWebPath() bWithLogin=$oUserProfile->getLogin()}

{include file='footer.tpl'}
