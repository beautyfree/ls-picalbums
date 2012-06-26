<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleHeart extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	private function GetHeartsByUserId($iUserId) {
		$tag = "heart_by_user_id_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetHeartsByUserId ( $iUserId );
			if($data)
				$this->Cache_Set ( $data, $tag, array ("heart_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}

    public function GetPicturesHeartedByUserId($iUserId) {
        $tag = "pictures_hearted_by_user_id_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPicturesHeartedByUserId ( $iUserId );
			if($data)
				$this->Cache_Set ( $data, $tag, array ("heart_update_{$iUserId}", "album_user_picture_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function isUserVotedByTarget($iUserId, $iTargetId) {
		$hearts = $this->GetHeartsByUserId($iUserId);
		if($hearts != false) {
			foreach($hearts as $heart) {
				if($heart->getTargetId() == $iTargetId)
					return true;
			}
		}
		return false;
	}
	
	public function GetUsersHeartedByTargetId($iTargetId) {
		$tag = "heart_by_target_id_{$iTargetId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetUsersHeartedByTargetId ( $iTargetId );
			$data = $this->User_GetUsersByArrayId($data);
			if($data)
				$this->Cache_Set ( $data, $tag, array ("heart_update_by_target_{$iTargetId}" ), 60 * 60 * 24 );
		}
		return $data;
	}

	function GetUsersHeartedLimitByTargetId($iTargetId, $iLimit) {
		$tag = "heart_by_target_id_{$iTargetId}_{$iLimit}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetUsersHeartedLimitByTargetId ( $iTargetId, $iLimit );
			$data = $this->User_GetUsersByArrayId($data);
			if($data)
				$this->Cache_Set ( $data, $tag, array ("heart_update_by_target_{$iTargetId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetUsersHeartedCountByTargetId($iTargetId) {
		$tag = "heartcount_by_target_id_{$iTargetId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetUsersHeartedCountByTargetId ( $iTargetId );
			$this->Cache_Set ( $data, $tag, array ("heart_update_by_target_{$iTargetId}" ), 60 * 60 * 24 );
		}
		return $data;
	}

    public function GetUsersHeartedCountByUserId($iUserId) {
		$tag = "heartcount_by_user_id_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetUsersHeartedCountByUserId ( $iUserId );
			$this->Cache_Set ( $data, $tag, array ("heart_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function AddHeart($oHeart) {
		if ($this->oMapper->AddHeart ( $oHeart )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_best_pictures", "heart_update_by_target_{$oHeart->getTargetId()}", "heart_update_{$oHeart->getUserId()}" ) );
			return true;
		}
		return false;
	}
	
	public function DeleteHeart($iUserId, $iTargetId) {
		if ($this->oMapper->DeleteHeart ( $iUserId, $iTargetId )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_best_pictures", "heart_update_by_target_{$iTargetId}", "heart_update_{$iUserId}" ) );
			return true;
		}
		return false;
	}
	
	public function DeleteHeartByTargetId($iTargetId) {
		if ($this->oMapper->DeleteHeartByTargetId ( $iTargetId )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("heart_update_by_target_{$iTargetId}" ) );
			return true;
		}
		return false;
	}

}
?>
