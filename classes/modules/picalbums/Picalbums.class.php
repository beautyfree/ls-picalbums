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

    public function GetUserCollectiveAlbumOwner($iVirtualUserId) {
		return $this->oMapper->GetUserCollectiveAlbumOwner($iVirtualUserId);
	}
	
}
?>
