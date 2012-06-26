<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleSettings_MapperSettings extends Mapper {
	
	// Получение настроек 
	public function GetSettingsByUserID($iUserId) {
		$sql = "SELECT * FROM " . Config::Get ( 'plugin.picalbums.table.settings' ) . " WHERE user_id = ?d";
		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId )) {
			return Engine::GetEntity ( 'PluginPicalbums_Settings', $aRow );
		} else {
			$this->SetDefaultSettings($iUserId);
			
			if ($aRow = $this->oDb->selectRow ( $sql, $iUserId ))
				return Engine::GetEntity ( 'PluginPicalbums_Settings', $aRow );
			else 
				return false;
		}
		return false;
	}
	
	// Установка настроек по-умолчанию
	private function SetDefaultSettings($iUserId) {
		$sql = "INSERT INTO " . Config::Get ( 'plugin.picalbums.table.settings' ) . "(user_id) VALUES (?d) ";
		if ($this->oDb->query ( $sql, $iUserId )) {
			return true;
		}
		return false;
	}
	
	// Обновление настроек пользователя
	public function UpdateSettings($iUserId, $bIsUsedAjax) {
		$this->GetSettingsByUserID($iUserId);
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.settings' ) . " SET is_used_ajax = ?d WHERE user_id = ?d ";
		if ($this->oDb->query ( $sql, $bIsUsedAjax, $iUserId )) {
			return true;
		}
		return false;
	}
	
	public function UpdateSettingsNotice($iUserId, $bCommentNotify, $bMarkNotify) {
		$this->GetSettingsByUserID($iUserId);
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.settings' ) . " SET comment_notify = ?d, mark_notify = ?d WHERE user_id = ?d ";
		if ($this->oDb->query ( $sql, $bCommentNotify, $bMarkNotify, $iUserId )) {
			return true;
		}
		return false;
	}
}
?>
