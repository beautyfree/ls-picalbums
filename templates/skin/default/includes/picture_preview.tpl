<div class="container-photo">
	<a href="{$sAlbumPathStart}{$oAlbum->getURL()}/{$oPicture->getURL()}/" title="{$oPicture->getDescription()}" id="navigation picture_link_{$oPicture->getId()}">
		<img src="{$oPicture->getMiniaturePath()}" alt="{$oPicture->getDescription()}" />
	</a>
</div>