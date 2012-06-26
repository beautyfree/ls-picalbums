<div class="block white">
	<div class="tl">
		<div class="tr"></div>
	</div>
	
	<div class="cl">
		<div class="cr">

		    <h1>{$sTitle} {if !$bDontShowLogin}{$oUserProfile->getLogin()}{/if} {$sEndTitle}</h1>

			<div class="blockpictures">			
                {if $aPictures}
                    {foreach from=$aPictures item=oPicture}
                        {assign var=oAlbum value=$oPicture->getAlbumOwner()}
                        {assign var=isContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}
                        {if $isContinue}
                            <div class="blockpicture" id="blockpicture_{$oPicture->getId()}">
                                <a href="{$oUserProfile->getUserAlbumsWebPath()}{$oAlbum->getURL()}/{$oPicture->getURL()}/" id="blockpicture_link_{$oPicture->getId()}">
                                    <img src="{$oPicture->getBlockPath()}" alt="{$oPicture->getDescription()}" />
                                </a>
                            </div>
                        {/if}
                    {/foreach}
                {else}
                    {$aLang.picalbums_block_profile_best_pictures_none}
                {/if}
            </div>
		</div>
	</div>
	
	<div class="bl">
		<div class="br"></div>
	</div>
</div>