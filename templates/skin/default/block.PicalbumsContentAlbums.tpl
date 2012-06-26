{if $aAlbums}
    <div class="block white">
        <div class="tl">
            <div class="tr"></div>
        </div>

        <div class="cl">
            <div class="cr">
            <h1>{$aLang.picalbums_block_last_albums}</h1>
                <div class="blockpictures">
                    {foreach from=$aAlbums item=oAlbum}
                        {assign var=oUser value=$oAlbum->GetUserOwner()}

                        {if $oUser}
                            {assign var=sAlbumURL value=$oUser->getUserAlbumsWebPath()}
                        {else}
                            {assign var=oUser value=$oAlbum->GetAppendedAlbumUser()}
                            {assign var=sAlbumURL value=$sMainAlbumsRouter}
                        {/if}

                        {if $oUser}
                            {assign var=oPictureCover value=$oAlbum->GetCoverPicture()}
                            {if $oPictureCover}
                                <div class="blockpicture">
                                    <a href="{$sAlbumURL}{$oAlbum->getURL()}/">
                                        <img src="{$oPictureCover->getBlockPath()}" alt="{$oPictureCover->getDescription()}" />
                                    </a>
                                </div>
                            {/if}
                        {/if}
                    {/foreach}

                </div>
            </div>
        </div>

        <div class="bl">
            <div class="br"></div>
        </div>
    </div>
{/if}