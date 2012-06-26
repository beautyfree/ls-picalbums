{if $aPictures}
    <div class="block white">
        <div class="tl">
            <div class="tr"></div>
        </div>

        <div class="cl">
            <div class="cr">
            <h1>{$sTitle}</h1>
                <div class="blockpictures">
                    {foreach from=$aPictures item=oPicture}
                        {assign var=oAlbum value=$oPicture->getAlbumOwner()}
                        {assign var=oUser value=$oAlbum->GetUserOwner()}

                        {if $oUser}
                            {assign var=sAlbumURL value=$oUser->getUserAlbumsWebPath()}
                            {if $oAlbum->GetVisibilityForUser($oUserCurrent) == false}
                                {assign var=oUser value=null}
                            {/if}
                        {else}
                            {assign var=oUser value=$oAlbum->GetAppendedAlbumUser()}
                            {assign var=sAlbumURL value=$sMainAlbumsRouter}
                        {/if}

                        {if $oUser}
                            <div class="blockpicture" id="blockpicture_{$oPicture->getId()}">
                                <a href="{$sAlbumURL}{$oAlbum->getURL()}/{$oPicture->getURL()}/" id="blockpicture_link_{$oPicture->getId()}">
                                    <img src="{$oPicture->getBlockPath()}" alt="{$oPicture->getDescription()}" />
                                </a>
                            </div>
                        {/if}
                    {/foreach}
                    {if $sAdditionalLink}
                        <div class="bottom">
                            <a href="{$sAdditionalLink}">{$sAdditionalLinkText}</a>
                        </div>
                    {/if}
                </div>
            </div>
        </div>

        <div class="bl">
            <div class="br"></div>
        </div>
    </div>
{/if}