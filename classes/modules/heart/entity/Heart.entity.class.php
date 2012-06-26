<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleHeart_EntityHeart extends Entity {
	
	public function getId() {
		return $this->_aData ['heart_id'];
	}
	public function getTargetId() {
		return $this->_aData ['target_id'];
	}		
	public function getUserId() {
		return $this->_aData ['user_id'];
	}
	
	public function setId($data) {
		$this->_aData ['heart_id'] = $data;
	}
	public function setTargetId($data) {
		$this->_aData ['target_id'] = $data;
	}
	public function setUserId($data) {
		$this->_aData ['user_id'] = $data;
	}

}

?>
