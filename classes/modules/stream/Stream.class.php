<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleStream extends PluginPicalbums_Inherit_ModuleStream {
	
	public function Init() {
		$this->AddEventType('add_picture', array('related' => 'picture'));
		$this->AddEventType('add_album', array('related' => 'album'));
		return parent::Init();
	}
	
	protected function loadRelatedAlbum($aIds) {
		return $this->PluginPicalbums_Album_GetAlbumsByArrayId($aIds);
	}
	
	protected function loadRelatedPicture($aIds) {
		return $this->PluginPicalbums_Picture_GetPicturesByArrayId($aIds);
	}
}

?>