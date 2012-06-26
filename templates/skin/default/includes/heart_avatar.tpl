{if !$bDntShowTitle}
    <div class="pictip-thumbs-title">
        <a href="" onclick="picalbums.showDialogWithHeartedUsers({$oPicture->getId()}); return false;">
            {if $oConfig->GetValue('lang.current') == 'russian'}
                {if $iHeartCount == 0}
                    Пока никому не понравилось
                {elseif $iHeartCount == 1}
                    Понравилось 1 человеку
                {else}
                    Понравилось {$iHeartCount} человекам
                {/if}
            {else}
                {$aLang.picalbums_like_text_start} {$iHeartCount} {$aLang.picalbums_like_text_end}
            {/if}
        </a>
    </div>
{/if}

{if $aUsersHearted}
    <div class="pictip-thumbs heart-pictip-thumbs">
        {foreach from=$aUsersHearted item=oUserHearted}
            <a class='heart_avatar_link' href='{$oUserHearted->getUserAlbumsWebPath()}'>
                <img src='{$oUserHearted->getProfileAvatarPath({$oConfig->GetValue('plugin.picalbums.heart_users_avatar_size')})}' class='heart_avatar' />
            </a>
        {/foreach}
    </div>
{/if}