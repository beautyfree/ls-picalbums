{if $aComments}
	{foreach from=$aComments item=oComment}						
		{assign var="oUser" value=$oComment->getUser()}
		{assign var="bIsCanDelete" value=false}
		{if $oUserCurrent}
			{if ($oUser->getId() == $oUserCurrent->getId()) OR ($oUserCurrent->isAdministrator())}
				{assign var="bIsCanDelete" value=true}
			{/if}
		{/if}

        <div id="comment_record_{$oComment->getId()}" class="comment picalbumscomment" >
            <div class="folding"></div>
            <div id="comment_content_id_{$oComment->getId()}" class="content">
                {$oComment->getText()}
            </div>

            <ul class="info">
                <li class="avatar"><a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" /></a></li>
                <li class="username"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
                <li class="date">{date_format date=$oComment->getDate()}</li>

                {if $bIsCanDelete}
                    <li><a href="#" class="delete" onclick="picalbums.removeComment({$oComment->getId()}); return false;">{$aLang.comment_delete}</a></li>
                {/if}
            </ul>
        </div>
	{/foreach}
{/if}