<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleSettings extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	// Получение настроек 
	public function GetSettingsByUserID($iUserId) {
		$tag = "picalbums_settings_by_user_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetSettingsByUserID ( $iUserId );
			$this->Cache_Set ( $data, $tag, array ("picalbums_settings_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	// Обновление настроек пользователя
	public function UpdateSettings($iUserId, $bIsUsedAjax) {
		if ($this->oMapper->UpdateSettings ( $iUserId, $bIsUsedAjax )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("picalbums_settings_update_{$iUserId}" ) );
			return true;
		}
		return false;
	}
	
	// Обновление настроек пользователя
	public function UpdateSettingsNotice($iUserId, $bCommentNotify, $bMarkNotify) {
		if ($this->oMapper->UpdateSettingsNotice ( $iUserId, $bCommentNotify, $bMarkNotify )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("picalbums_settings_update_{$iUserId}" ) );
			return true;
		}
		return false;
	}
}
?>
