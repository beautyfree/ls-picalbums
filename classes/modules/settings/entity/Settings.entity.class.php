<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleSettings_EntitySettings extends Entity {
	
	public function getUserId() {
		return $this->_aData ['user_id'];
	}
	public function getCommentNotifyByEmail() {
		return $this->_aData ['comment_notify'];
	}
	public function getMarkNotifyByEmail() {
		return $this->_aData ['mark_notify'];
	}
	public function getIsUsedAjax() {
		return $this->_aData ['is_used_ajax'];
	}	
	
	public function setUserId($data) {
		$this->_aData ['user_id'] = $data;
	}	
	public function setCommentNotifyByEmail($data) {
		$this->_aData ['comment_notify'] = $data;
	}	
	public function setMarkNotifyByEmail($data) {
		$this->_aData ['mark_notify'] = $data;
	}
	public function setIsUsedAjax($data) {
		$this->_aData ['is_used_ajax'] = $data;
	}

}

?>
