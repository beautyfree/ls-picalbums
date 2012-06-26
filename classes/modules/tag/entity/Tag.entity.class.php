<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleTag_EntityTag extends Entity {
	
	public function getId() {
		return $this->_aData ['tag_id'];
	}
	public function getTargetId() {
		return $this->_aData ['target_id'];
	}
	public function getText() {
		return $this->_aData ['tag_text'];
	}
	
	public function setId($data) {
		$this->_aData ['tag_id'] = $data;
	}
	public function setTargetId($data) {
		$this->_aData ['target_id'] = $data;
	}
	public function setText($data) {
		$this->_aData ['tag_text'] = $data;
	}

}

?>
