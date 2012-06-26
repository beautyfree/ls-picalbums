<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleBlacklist_MapperBlacklist extends Mapper {
	
	public function isUserBlocked($iUserId) {
		$sql = 	" SELECT COUNT(*) as cnt ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.blacklist' ) .
				" WHERE user_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId )) {
			return $aRow['cnt'];
		}
		return false;
	}
	
	public function getAllBlockedUsers() {
		$sql = 	" SELECT user_id ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.blacklist' );
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = $aRow['user_id'];
			}
			return $aReturn;
		}
		return false;
	}
	
	public function AddToBlackList($oBlackList) {
		$sql = "INSERT INTO " . Config::Get ( 'plugin.picalbums.table.blacklist' ) . "
											   (user_id)
											   VALUES (?d) ";
		if (($this->oDb->query ( $sql,$oBlackList->getUserId () ))) {			
			return true;
		}
		
		return false;
	}
	
	public function DeleteFromBlackList($iBlackListUserId) {
		$sql = "DELETE FROM " . Config::Get ( 'plugin.picalbums.table.blacklist' ) . " WHERE user_id = ?d ";
		if ($this->oDb->query ( $sql, $iBlackListUserId )) {
			return true;
		}
		return false;
	}

}
?>
