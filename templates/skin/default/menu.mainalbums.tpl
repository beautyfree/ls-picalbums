<ul class="menu">
	<li {if $sEvent != 'profileall'}class="active"{/if} >
		<a href="{router page="$sMainAlbumsRouterName"}">{$aLang.picalbums_menu_profile_albums}</a>
		{if $sEvent != 'profileall'}
		<ul class="sub-menu">			
			<li {if $sEvent != 'create'}class="active"{/if} ><a href="{router page="$sMainAlbumsRouterName"}">{$iAlbumCount}</a></li>
			
			<li {if $sEvent == 'create'}class="active"{/if}><a href="{router page="$sMainAlbumsRouterName"}create/">{$aLang.picalbums_menu_profile_add_album}</a></li>
		</ul>
		{/if}
	</li>
	{if $oUserCurrent}
	<li>
		<a href="{$oUserCurrent->getUserAlbumsWebPath()}">{$aLang.picalbums_yourprofilepictures}</a>
	</li>
	{/if}
	<li {if $sEvent == 'profileall'}class="active"{/if} >
		<a href="{router page="$sMainAlbumsRouterName"}profileall/">{$aLang.picalbums_allprofilepictures}</a>
	</li>
</ul>
