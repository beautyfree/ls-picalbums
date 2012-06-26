<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
class PluginPicalbums_ModuleUser_EntityUser extends PluginPicalbums_Inherit_ModuleUser_EntityUser {
	
	public function getUserAlbumsWebPath() {   
    	return Router::GetPath(Config::Get ( 'plugin.picalbums.albums_router_name' )).$this->getLogin().'/';
    }
	
	public function getPicalbums() {   
    	return $this->PluginPicalbums_Album_GetAlbumsByUserId($this->getId());
    }
	public function getAppendedPicalbums() {
    	return $this->PluginPicalbums_Album_GetAlbumsAppendedByUserId($this->getId());
    }
    
	public function getPicalbumsModifySort() {   
    	return $this->PluginPicalbums_Album_GetAlbumsModifySortByUserId($this->getId());
    }
    
	public function getPicalbumsCount($iUserType) {
    	return $this->PluginPicalbums_Album_GetAlbumCountByUserId($this->getId(), $iUserType);
    }
    
	public function isUsersFriend($oUser) {   
    	if(!$oUser)
			return false;
		if($this->getId() == $oUser->getId())
			return true;
    	$oUserFriend = $this->User_GetFriend($this->getId(), $oUser->getId());
		$oUserFriend = $oUserFriend && 
						(($oUserFriend->getFriendStatus()==ModuleUser::USER_FRIEND_ACCEPT+ModuleUser::USER_FRIEND_OFFER) || 
						($oUserFriend->getFriendStatus()==ModuleUser::USER_FRIEND_ACCEPT+ModuleUser::USER_FRIEND_ACCEPT));
						
		return $oUserFriend;
    }
	
	public function isAlbumRelated($iAlbumId) {
    	return $this->PluginPicalbums_Related_isUserRelatedWithTarget ($this->getId(), $iAlbumId);
    }
}

?>
