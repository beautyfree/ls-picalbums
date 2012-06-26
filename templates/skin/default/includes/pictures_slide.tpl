{if $aPictures}

    <div id="container-slides">
        <div id="slides">
            <div class="slides_container">
                {foreach from=$aPictures item=oPicture}
                    <div class="slides-outer">
                        <div class="slides-inner">
                            {if $oConfig->GetValue('plugin.picalbums.album_preview_tooltip_link_enable')}
                                {assign var="oAlbum" value=$oPicture->getAlbumOwner()}
                                {if $oAlbum->getUserId() == $oConfig->GetValue('plugin.picalbums.virtual_main_user_id')}
                                    {assign var="sAlbumURL" value=$sMainAlbumsRouter}
                                {else}
                                    {assign var="oUserOwner" value=$oAlbum->getUserOwner()}
                                    {assign var="sAlbumURL" value=$oUserOwner->getUserAlbumsWebPath()}
                                {/if}
                                <a href="{$sAlbumURL}{$oAlbum->getURL()}/{$oPicture->getURL()}/"><img src="{$oPicture->getPicPath()}" alt="{$oPicture->getDescription()}"></a>
                            {else}
                                <img src="{$oPicture->getPicPath()}" alt="{$oPicture->getDescription()}">
                            {/if}
                        </div>
                    </div>
                {/foreach}
            </div>
            <a href="#" class="prev"><img src="{$sTemplateWebPathPicalbumsPlugin}/images/slides/arrow-prev.png" width="24" height="43" alt="Arrow Prev"></a>
			<a href="#" class="next"><img src="{$sTemplateWebPathPicalbumsPlugin}/images/slides/arrow-next.png" width="24" height="43" alt="Arrow Next"></a>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){
            $(function(){
                $('#slides').slides({
                    preload: true,
                    preloadImage: DIR_PICALBUM_PLUGIN + '/images/slides/loading.gif',
                    play: 5000,
                    pause: 2500,
                    hoverPause: true
                });
            });
        });
    </script>
{/if}