<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleBlacklist extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	public function isUserBlocked($iUserId) {
		return $this->oMapper->isUserBlocked ( $iUserId );
		
		$tag = "isblocked_userid_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->isUserBlocked ( $iUserId );
			$this->Cache_Set ( $data, $tag, array ("blacklist_picalbums" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function getAllBlockedUsers() {
		$data = $this->oMapper->getAllBlockedUsers ();
		$data = $this->User_GetUsersByArrayId($data);
		
		return $data;
	}	
	
	public function AddToBlackList($oBlackList) {
		if($this->isUserBlocked($oBlackList->getUserId()) == 1)
			return false;
		
		$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("blacklist_picalbums" ) );		
		$this->oMapper->AddToBlackList ( $oBlackList );
		return true;
	}
	
	public function DeleteFromBlackList($iBlackListUserId) {
		$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("blacklist_picalbums" ) );
		return $this->oMapper->DeleteFromBlackList ( $iBlackListUserId );
	}
}
?>
