<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleTag extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	public function GetTags($iLimit) {
		$tag = "tags_all_{$iLimit}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetTags ( $iLimit );
			$this->Cache_Set ( $data, $tag, array ("tag_update", "album_main",  ), 60 * 60 * 24 );
		}
		return $data;
	}
	
    public function GetTagsByTargetId($iTargetId) {
		$tag = "tags_by_target_id_{$iTargetId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetTagsByTargetId ( $iTargetId );
			$this->Cache_Set ( $data, $tag, array ("tag_update", "album_picture_update_{$iTargetId}",  ), 60 * 60 * 24 );
		}
		return $data;
	}

    public function GetTagsByLike($sTag,$iLimit) {
		if (false === ($data = $this->Cache_Get("tag_picalbums_{$sTag}_{$iLimit}"))) {
			$data = $this->oMapper->GetTagsByLike($sTag,$iLimit);
			$this->Cache_Set($data, "tag_picalbums_{$sTag}_{$iLimit}", array("tag_update"), 60*60*24);
		}
		return $data;
	}
	
	public function AddTag($oTag) {
		if ($this->oMapper->AddTag ( $oTag )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("tag_update") );
			return true;
		}
		return false;
	}
	
	public function DeleteTagsByTargetId($iTargetId) {
		if ($this->oMapper->DeleteTagsByTargetId ($iTargetId )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("tag_update") );
			return true;
		}
		return false;
	}

}
?>
