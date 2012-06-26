<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModulePicalbums extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	public function GetFriendsByUserIdAndLoginLike($iUserId, $sUserLogin,$iLimit) {
		if (false === ($data = $this->Cache_Get("friend_user_like_{$iUserId}_{$sUserLogin}_{$iLimit}"))) {
			$data = $this->oMapper->GetFriendsByUserIdAndLoginLike($iUserId,$sUserLogin,$iLimit);
			$this->Cache_Set($data, "friend_user_like_{$iUserId}_{$sUserLogin}_{$iLimit}", array("user_update","user_new"), 60*15);
		}
		return $data;		
	}
	
	public function GetAllUsersLoginLike($sUserLogin,$iLimit) {	
		if (false === ($data = $this->Cache_Get("all_user_like_{$sUserLogin}_{$iLimit}"))) {			
			$data = $this->oMapper->GetAllUsersLoginLike($sUserLogin,$iLimit);
			$this->Cache_Set($data, "all_user_like_{$sUserLogin}_{$iLimit}", array("user_update","user_new"), 60*15);
		}
		return $data;		
	}
	
	public function GetAllUsers() {
		if (false === ($data = $this->Cache_Get("all_user_for_picalbums"))) {			
			$data = $this->oMapper->AllUser();
			$this->Cache_Set($data, "all_user_like_for_picalbums", array("user_update","user_new"), 60*15);
		}
		$data=$this->User_GetUsersAdditionalData($data);
		return $data;		
	}

    public function GetUserCollectiveAlbumOwner($iVirtualUserId, $iPictureId) {
		return $this->oMapper->GetUserCollectiveAlbumOwner($iVirtualUserId);
	}
    
    public function GetNoteArrayByPictureId($iPictureOwnerUserId, $iPictureId, $oUserCurrent) {
        $iUserCurrentId = null;
        $bIsAdmin = false;
        if($oUserCurrent) {
            $iUserCurrentId = $oUserCurrent->getId();
            $bIsAdmin = $oUserCurrent->isAdministrator();
        }

        // Администратор и автор картинки видит все пометки
        if(($bIsAdmin) ||($iPictureOwnerUserId == $iUserCurrentId))
            $aNotes = $this->PluginPicalbums_Note_GetNotesByPictureId ( $iPictureId );
        else {
            $aNotes = $this->PluginPicalbums_Note_GetConfirmedNotesByPictureId ( $iPictureId, $iUserCurrentId );
        }

        $aResult=array();
        if($aNotes)
            foreach($aNotes as $sNoteText) {
                $sNoteTextUser = $this->User_GetUserById($sNoteText->getUserMarkId());
                $sLink = $sNoteText->getLink();
                $sAuthorMark = '';

                if($sNoteTextUser) {
                    $sLink = $sNoteTextUser->getUserWebPath();
                    $sAuthorMark = $sNoteTextUser->getLogin();
                }

                $sNoteTextAuthor = $this->User_GetUserById($sNoteText->getUserId());
                if($sNoteTextAuthor)
                    $sNoteTextAuthor = $sNoteTextAuthor->getLogin();
                else
                    $sNoteTextAuthor = '';

                $sAvatar = "";
                if($sNoteText->getUserMarkId()) {
                    $oUser = $this->User_GetUserById($sNoteText->getUserMarkId());
                    if($oUser)
                        $sAvatar = "<img src='" . $oUser->getProfileAvatarPath(24) . "' /><br/>";
                }
                $bCanEdit = 0;
                $bCanDelete = 0;
                if(($iUserCurrentId == $sNoteText->getUserId()) ||  ($iUserCurrentId == $sNoteText->getUserMarkId()) ||
                   ($iUserCurrentId == $iPictureOwnerUserId)) {
                   $bCanDelete = 1;
                    if(!Config::Get ( 'plugin.picalbums.notes_mark_confirm' ))
                        $bCanEdit = 1;
                    else {
                       if(($sNoteText->getIsConfirm() == 0) || ($sNoteText->getIsConfirm() == '0'))
                            $bCanEdit = 1;
                    }
                }

                $aResult[]=array(
                        'ID' => $sNoteText->getId(),
                        'LEFT' => $sNoteText->getLeft(),
                        'WIDTH' => $sNoteText->getWidth(),
                        'TOP' => $sNoteText->getTop(),
                        'HEIGHT' => $sNoteText->getHeight(),
                        'DATE' => $sNoteText->getDateAdd(),
                        'NOTE' => $sAvatar . $sNoteText->getNote(),
                        'LINK' => $sLink,
                        'AUTHOR' => $sNoteTextAuthor,
                        'AUTHORMARK' => $sAuthorMark,
                        'CANEDIT' => $bCanEdit,
                        'CANDELETE' => $bCanDelete,
                        'ISCONFIRM' => $sNoteText->getIsConfirm(),
                );
            }
        return $aResult;
    }
	
}
?>
