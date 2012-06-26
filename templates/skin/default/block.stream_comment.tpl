<ul class="list">
	{foreach from=$aComments item=oComment name="cmt"}
			{assign var="oUser" value=$oComment->getUser()}
		{if $oComment->getTargetType() == 'topic'}
			{assign var="oTopic" value=$oComment->getTarget()}
			{assign var="oBlog" value=$oTopic->getBlog()}
			
			<li {if $smarty.foreach.cmt.iteration % 2 == 1}class="even"{/if}>
				<a href="{$oUser->getUserWebPath()}" class="user">{$oUser->getLogin()}</a> &rarr;
				<span class="stream-comment-icon"></span>
				<a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}{$oTopic->getUrl()}#comment{/if}{$oComment->getId()}" class="topic-title">{$oTopic->getTitle()|escape:'html'}</a>
				<span>{$oTopic->getCountComment()}</span> &rarr;
				<a href="{$oBlog->getUrlFull()}" class="blog-title">{$oBlog->getTitle()|escape:'html'}</a>
			</li>
		{else if $oComment->getTargetType() == 'picalbums'}
			{assign var="oPicture" value=$oComment->getPicture()}
			{if $oPicture}
				{assign var="oAlbum" value=$oPicture->getAlbumOwner()}
				{if $oAlbum}
					{assign var="oUserOwner" value=$oAlbum->getUserOwner()}
					
					{assign var="URLType" value=1}
					{if !$oUserOwner}
						{assign var="oUserOwner" value=$oPicture->GetAppendedAlbumUser()}
						{assign var="URLType" value=2}
					{/if}
					
					{if $oUserOwner}
                        {assign var=isContinue value=$oAlbum->GetVisibilityForUser($oUserCurrent)}
						{if $isContinue}
							{if $URLType == 1}
								{assign var="URLAlbum" value=$oUserOwner->getUserAlbumsWebPath()}
							{else}
								{assign var="URLAlbum" value=$sMainAlbumsRouter}
							{/if}
						
							<li {if $smarty.foreach.cmt.iteration % 2 == 1}class="even"{/if}>
								<a href="{$oUser->getUserWebPath()}" class="user">{$oUser->getLogin()}</a> &rarr;
								<span class="stream-comment-icon"></span>
								<a href="{$URLAlbum}{$oAlbum->getURL()}/{$oPicture->getURL()}/" class="topic-title">{$oPicture->getDescription()|escape:'html'}</a>
								<span>{$oPicture->getCommentCount()}</span> &rarr;
								<a href="{$URLAlbum}{$oAlbum->getURL()}/" class="blog-title">{$aLang.picalbums_block_stream_albums} {$oAlbum->getTitle()|escape:'html'}</a>
							</li>
						{/if}
					{/if}
				{/if}
			{/if}
		{/if}
	{/foreach}
</ul>


<div class="bottom">
	<a href="{router page='comments'}">{$aLang.block_stream_comments_all}</a> | <a href="{router page='rss'}allcomments/">RSS</a>
</div>