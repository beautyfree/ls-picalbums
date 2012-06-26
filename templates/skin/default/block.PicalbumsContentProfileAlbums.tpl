<div class="block white">
	<div class="tl">
		<div class="tr"></div>
	</div>
	
	<div class="cl">
		<div class="cr">
		<h1>{$sTitle} {$oUserProfile->getLogin()}</h1>
			<div class="blockpictures">
			{assign var="iCountPicture" value=0}
			{if $aAlbums}
				{foreach from=$aAlbums item=oAlbum}
				
				{if $oConfig->GetValue('plugin.picalbums.block_profile_albums') > $iCountPicture}
					{assign var="oCoverPicture" value=$oAlbum->GetCoverPicture()}
					{assign var=isContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}

					{if $isContinue}
						{if $oCoverPicture}
							{assign var="iCountPicture" value=$iCountPicture+1}
							<div class="blockpicture" id="blockpicture_{$oCoverPicture->getId()}">
								<a href="{$oUserProfile->getUserAlbumsWebPath()}{$oAlbum->getURL()}/" id="blockpicture_link_{$oCoverPicture->getId()}">
									<img src="{$oCoverPicture->getBlockPath()}" alt="{$oCoverPicture->getDescription()}" />
								</a>
							</div>
						{/if}
					{/if}
					
				{/if}
				
				{/foreach}
				
			{/if}
			{if $iCountPicture == 0}
				{$aLang.picalbums_block_profile_albums_none}
			{/if}
			</div>
		</div>
	</div>
	
	<div class="bl">
		<div class="br"></div>
	</div>
</div>