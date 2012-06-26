	<li {if $sAction==$oConfig->GetValue('plugin.picalbums.albums_router_name')}class="active"{/if}>
		<a href="{$oUserProfile->getUserAlbumsWebPath()}">{$aLang.picalbums_menu_profile_albums}</a>
			{if $sAction==$oConfig->GetValue('plugin.picalbums.albums_router_name')}
				<ul class="sub-menu">
					<li {if $sAction==$oConfig->GetValue('plugin.picalbums.albums_router_name') AND $aParams[0]==null}class="active"{/if}>
						<a href="{$oUserProfile->getUserAlbumsWebPath()}">{$iAlbumCount}</a>
					</li>
					<li {if $sAction==$oConfig->GetValue('plugin.picalbums.albums_router_name') AND $aParams[0]=='allpictures'}class="active"{/if}>
						<a href="{$oUserProfile->getUserAlbumsWebPath()}allpictures/">{$aLang.picalbums_menu_profile_all_photo} ({$oPictureCount})</a>
					</li>
					{if $oLoginUser}
						{if $oLoginUser->getId() == $oUserProfile->getId()}
							{if $oConfig->GetValue('plugin.picalbums.show_count_info_in_menu') AND $iTotalAlbums != null}
								<li {if $sAction==$oConfig->GetValue('plugin.picalbums.albums_router_name') AND $aParams[0]=='friend'}class="active"{/if}>
									<a href="{$oUserProfile->getUserAlbumsWebPath()}friend/">{$aLang.picalbums_menu_profile_friends} (<span id="span-friend-page-album-cnt">{$iTotalAlbums}</span>)</a>
								</li>
							{else}
								<li {if $sAction==$oConfig->GetValue('plugin.picalbums.albums_router_name') AND $aParams[0]=='friend'}class="active"{/if}>
									<a href="{$oUserProfile->getUserAlbumsWebPath()}friend/">{$aLang.picalbums_menu_profile_friends}</a>
								</li>
							{/if}
						{/if}
					{/if}
					{if $oLoginUser}
						{if $oLoginUser->GetLogin() == $sEvent}				
							<li {if $sAction==$oConfig->GetValue('plugin.picalbums.albums_router_name') AND $aParams[0]=='create'}class="active"{/if}><a href="{$oLoginUser->getUserAlbumsWebPath()}create/">{$aLang.picalbums_menu_profile_add_album}</a></li>
						{/if}
					{/if}
				</ul>
			{/if}
	</li>