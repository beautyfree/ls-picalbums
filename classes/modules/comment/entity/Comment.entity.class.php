<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleComment_EntityComment extends PluginPicalbums_Inherit_ModuleComment_EntityComment {
	
	public function getPicture() {
    	return $this->PluginPicalbums_Picture_GetPictureById ($this->getTargetId());
    }
}

?>
