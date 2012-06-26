{if $oUserProfile->getId() == $oUserCurrent->getId()}
	<li {if $sAction==$sProfileAlbumsRouterName and $sEvent=='favourite'}class="active"{/if}>
	<a href="{$oUserCurrent->getUserAlbumsWebPath()}favourite/" >{$aLang.picalbums_favourite}</a>
	{if $iCountPictureFavourite} ({$iCountPictureFavourite}){/if}
	</li>
{/if}
