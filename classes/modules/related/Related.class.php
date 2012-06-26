<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleRelated extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	private function GetRelatedsByUserId($iUserId) {
		$tag = "related_by_user_id_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetRelatedsByUserId ( $iUserId );
			$this->Cache_Set ( $data, $tag, array ("related_global_all", "related_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function isUserRelatedWithTarget($iUserId, $iTargetId) {
		$relateds = $this->GetRelatedsByUserId($iUserId);
		if($relateds != false) {
			foreach($relateds as $related) {
				if($related->getTargetId() == $iTargetId)
					return true;
			}
		}
		return false;
	}
	
	public function AddRelated($oRelated) {
		if ($this->oMapper->AddRelated ( $oRelated )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("related_update_{$oRelated->getUserId()}" ) );
			return true;
		}
		return false;
	}
	
	public function DeleteRelated($iUserId, $iTargetId) {
		if ($this->oMapper->DeleteRelated ( $iUserId, $iTargetId )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("related_update_{$iUserId}" ) );
			return true;
		}
		return false;
	}
	
	public function DeleteRelatedByTargetId($iTargetId) {
		if ($this->oMapper->DeleteRelatedByTargetId ( $iTargetId )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("related_global_all") );
			return true;
		}
		return false;
	}

}
?>
