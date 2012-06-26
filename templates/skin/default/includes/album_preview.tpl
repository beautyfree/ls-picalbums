<div class="container-photo album-preview" id="album-preview-cover-{$oAlbum->getId()}">
	<a href="{$sAlbumPathStart}{$oAlbum->getURL()}/" title="{$oAlbum->getDescription()}" id="album_link_{$oAlbum->getId()}">
        {if $oCoverPicture}
            <img src="{$oCoverPicture->getMiniaturePath()}" alt="{$oCoverPicture->getDescription()}" />
        {else}
            <img src="{$sTemplateWebPathPicalbumsPlugin}/images/no_foto.gif" class="album" alt="{$oAlbum->getTitle()}" />
        {/if}
	</a>
</div>

{if  $oConfig->GetValue('plugin.picalbums.album_preview_tooltip_enable')}
    {assign var=aPictures value=$oAlbum->GetLimitPictures(0, $oConfig->GetValue('plugin.picalbums.image_count_in_tooltip'))}
    {if $aPictures}
        <div id="album-preview-{$oAlbum->getId()}" style="display: none;">
            <div class="pictip-thumbs-title">
                {$oAlbum->getTitle()}
            </div>
            <div class="pictip-thumbs">
                <p>
                {foreach from=$aPictures item=oPicture name=preview}
                    <a href="{$sMainAlbumsRouter}{$oAlbum->getURL()}/{$oPicture->getURL()}/">
                        <img src="{$oPicture->getBlockPath()}" />
                    </a>
                    {if $aPictures|@count == $oConfig->GetValue('plugin.picalbums.image_count_in_tooltip') AND $smarty.foreach.preview.index == ($oConfig->GetValue('plugin.picalbums.image_count_in_tooltip')/2-1)}
                        </p><div style="clear: both;"></div><p>
                    {/if}
                {/foreach}
                </p>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#album-preview-cover-{$oAlbum->getId()} > a').poshytip({
                    className: 'tip-yellow',
                    alignTo: 'cursor',
                    alignX: 'inner-left',
                    alignY: 'top',
                    offsetY: 5,
                    content: function(updateCallback) {
                        return $('#album-preview-{$oAlbum->getId()}').html();
                    }
                });
            });
        </script>
    {/if}
{/if}