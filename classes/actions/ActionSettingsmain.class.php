<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
	class PluginPicalbums_ActionSettingsmain extends PluginPicalbums_Inherit_ActionSettings {	
	
		protected function EventTuning() {
			parent::EventTuning();
			if (isPost('submit_settings_tuning')) {
				$this->PluginPicalbums_Settings_UpdateSettingsNotice ( $this->User_GetUserCurrent()->getId (), 
																		getRequest('settings_picalbums_comment_notice') ? 1 : 0,
																		getRequest('settings_picalbums_mark_notice') ? 1 : 0
																		);
			}
		}
	
	}
?>