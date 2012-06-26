<h1 class="title user-profile-header"><a class="albumsprofilelink" href="{$oUserProfile->getUserAlbumsWebPath()}">{$aLang.picalbums_profile_albums}</a></h1>

{if $aAlbums}
    <div class="image_carousel">
        <div id="picalbums-carousel">
            {foreach from=$aAlbums item=oAlbum}
                {assign var=isContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}

                {if $isContinue}
                    {assign var=oCoverPicture value=$oAlbum->GetCoverPicture()}
                    {if $oCoverPicture}
                        <a class="carimage" href="{$oUserProfile->getUserAlbumsWebPath()}{$oAlbum->getURL()}/">
                            <img src="{$oCoverPicture->getMiniaturePath()}" alt="{$oAlbum->getTitle()}" />
                        </a>
                    {else}
                        <a class="carimage" href="{$oUserProfile->getUserAlbumsWebPath()}{$oAlbum->getURL()}/">
                            <img src="{$sTemplateWebPathPicalbumsPlugin}/images/no_foto.gif" alt="{$oAlbum->getTitle()}" />
                        </a>
                    {/if}
                {/if}
            {/foreach}
        </div>
        <div class="clearfix"></div>
        <a class="prev" id="picalbums-carousel-prev" href="#"><span>prev</span></a>
        <a class="next" id="picalbums-carousel-next" href="#"><span>next</span></a>
        <div class="pagination" id="picalbums-carousel-pag"></div>
    </div>
    <script type="text/javascript">
            $('#picalbums-carousel').carouFredSel({
                circular: false,
                infinite: false,
                auto 	: false,
                responsive : false,
                scroll	: {
                    items	: "page"
                },
                prev	: {
                    button	: "#picalbums-carousel-prev",
                    key		: "left"
                },
                next	: {
                    button	: "#picalbums-carousel-next",
                    key		: "right"
                },
                pagination	: "#picalbums-carousel-pag"
            });
    </script>
{/if}

