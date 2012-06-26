<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleBlacklist_EntityBlacklist extends Entity {
	
	public function getUserId() {
		return $this->_aData ['user_id'];
	}
	public function setUserId($data) {
		$this->_aData ['user_id'] = $data;
	}
}

?>
