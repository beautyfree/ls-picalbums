<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleAlbum_EntityAlbum extends Entity {
	
	public function getId() {
		return $this->_aData ['album_id'];
	}
	public function getUserId() {
		return $this->_aData ['user_id'];
	}
	public function getTitle() {
		return $this->_aData ['title'];
	}
	public function getURL() {
		return $this->_aData ['url'];
	}
	public function getDescription() {
		return $this->_aData ['description'];
	}
	public function getDateAdd() {
		return $this->_aData ['date_add'];
	}
	public function getDateModify() {
		return $this->_aData ['date_modify'];
	}	
	public function getCoverPictureId() {
		return $this->_aData ['cover_picture_id'];
	}		
	public function getVisibility() {
		return $this->_aData ['visibility'];
	}
	public function getAddUserId() {
		return $this->_aData ['adduser_id'];
	}
	public function getCategoryId() {
		return $this->_aData ['category_id'];
	}
	public function getNeedModer() {
		return $this->_aData ['needmoder'];
	}
	public function GetLastPicture() {	
		return $this->PluginPicalbums_Picture_GetLastPictureByAlbumId($this->getId());
	}
	public function GetCoverPicture() {	
		if($this->getCoverPictureId() == null)
			return $this->GetLastPicture();
	
		$oPicture = $this->PluginPicalbums_Picture_GetPictureById($this->getCoverPictureId());
		if(!$oPicture) 
			return $this->GetLastPicture();
		else
			return $oPicture;
	}
	public function GetPictures() {	
		return $this->PluginPicalbums_Picture_GetPictureByAlbumId($this->getId());
	}
	public function GetAllPictures() {
		return $this->PluginPicalbums_Picture_GetAllPictureByAlbumId($this->getId());
	}
	public function GetLimitPictures( $iStartPos, $iLimit) {	
		return $this->PluginPicalbums_Picture_GetLimitPictureByAlbumId($this->getId(), $iStartPos, $iLimit);
	}
	public function GetPicturesCount() {	
		return $this->PluginPicalbums_Picture_GetPicturesCountByAlbumId($this->getId());
	}	
	public function GetUserOwner() {	
		return $this->User_GetUserById($this->getUserId());
	}
	public function GetAppendedAlbumUser() {
		return $this->User_GetUserById($this->getAddUserId());
	}

    public function GetVisibilityForUser($oUser) {
        if($oUser and $oUser->isAdministrator())
            return true;

        $iVisibility = $this->getVisibility();
        $oUserOwner = $this->GetUserOwner();
        $bIsContinue = true;
        if(!$oUserOwner)
            return true;
        
        if(!$oUser or $oUser->getId() != $oUserOwner->getId()) {
            if($iVisibility == 1) {
                if(!$oUser)
                    $bIsContinue = false;
            }
            elseif($iVisibility == 2) {
                $bIsUsersFriend = $oUserOwner->isUsersFriend($oUser);
                if(!$oUser or !$bIsUsersFriend) {
                    $bIsContinue = false;
                }
            }
        }
        return $bIsContinue;
    }

	public function GetUserNeedBeModerated($oUser) {
        if(($this->getNeedModer() == 1) && !$oUser)
            return 1;

		if ( ($this->getNeedModer() == 0) or ($oUser->isAdministrator()) or ($this->getUserId() == $oUser->getId()) or
             ($this->getAddUserId() == $oUser->getId()) or (in_array($oUser->getLogin(), Config::Get ( 'plugin.picalbums.moderators'))) ) {
            return 0;
        }
        return 1;
	}
	public function GetUserIsModerator($oUser) {
        if(!$oUser)
            return 0;

		if ( $oUser->isAdministrator() or ($this->getUserId() == $oUser->getId()) or ($this->getAddUserId() == $oUser->getId()) or
                in_array($oUser->getLogin(), Config::Get ( 'plugin.picalbums.moderators'))) {
            return 1;
        }
        return 0;
	}
    public function GetNonModeratedPictures() {
		return $this->PluginPicalbums_Picture_GetNonModeratedPictureByAlbumId($this->getId());
	}
    public function GetUserNonModeratedPictures($oUser) {
		return $this->PluginPicalbums_Picture_GetUserNonModeratedPictureByAlbumId($this->getId(), $oUser->getId());
	}
    public function GetTags() {
		return $this->PluginPicalbums_Tag_GetTagsByTargetId($this->getId());
	}
	
	public function setId($data) {
		$this->_aData ['album_id'] = $data;
	}
	public function setUserId($data) {
		$this->_aData ['user_id'] = $data;
	}
	public function setTitle($data) {
		$this->_aData ['title'] = $data;
	}
	public function setURL($data) {		
		$this->_aData ['url'] = $data;
	}
	public function setDescription($data) {
		$this->_aData ['description'] = $data;
	}
	public function setDateAdd($data) {
		$this->_aData ['date_add'] = $data;
	}
	public function setDateModify($data) {
		$this->_aData ['date_add'] = $data;
	}
	public function setCoverPictureId($data) {
		$this->_aData ['cover_picture_id'] = $data;
	}
	public function setVisibility($data) {
		$this->_aData ['visibility'] = $data;
	}	
	public function SetAddUserId($data) {
		$this->_aData ['adduser_id'] = $data;
	}	
	public function setCategoryId($data) {
		$this->_aData ['category_id'] = $data;
	}
	public function setNeedModer($data) {
        $this->_aData ['needmoder'] = $data;
	}
	
}

?>
