<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleHeart_MapperHeart extends Mapper {
	
	public function GetHeartById($iHeartId) {
		$sql = 	" SELECT a.* ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.heart' ) . " a " .
				" WHERE a.heart_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iHeartId )) {
			return Engine::GetEntity ( 'PluginPicalbums_Heart', $aRow );
		}
		return false;
	}
	
	public function GetHeartsByTargetId($iTargetId) {
		$sql = 	" SELECT * " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.heart' )  . 
				" WHERE target_id = ?d ";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iTargetId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Heart', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetUsersHeartedByTargetId($iTargetId) {
		$sql = 	" SELECT user_id " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.heart' )  . 
				" WHERE target_id = ?d ORDER BY heart_id DESC ";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iTargetId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = $aRow['user_id'];
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetUsersHeartedLimitByTargetId($iTargetId, $iLimit) {
		$sql = 	" SELECT user_id " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.heart' )  . 
				" WHERE target_id = ?d ORDER BY heart_id DESC LIMIT 0, ?d ";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iTargetId, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = $aRow['user_id'];
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetUsersHeartedCountByTargetId($iTargetId) {
		$sql = 	" SELECT count(*) as cntheart " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.heart' )  . 
				" WHERE target_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iTargetId )) {
			return $aRow['cntheart'];
		}
		return false;
	}

    public function GetUsersHeartedCountByUserId($iUserId) {
		$sql = 	" SELECT count(*) as cntheart " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.heart' )  .
				" WHERE user_id = ?d ";

		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId )) {
			return $aRow['cntheart'];
		}
		return false;
	}
	
	public function GetHeartsByUserId($iUserId) {
		$sql = 	" SELECT * " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.heart' )  . 
				" WHERE user_id = ?d ";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Heart', $aRow );
			}
			return $aReturn;
		}
		return false;
	}

    public function GetPicturesHeartedByUserId($iUserId) {
		$sql = 	"SELECT p.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.heart' )  . " h " .
				"WHERE h.user_id = ?d AND h.target_id = p.picture_id ".
				"ORDER BY p.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return null;
	}

	public function AddHeart($oHeart) {
		$sql = " INSERT INTO " . Config::Get ( 'plugin.picalbums.table.heart' ) . "
												(	target_id,
											        user_id
												)
												VALUES (?d, ?d) ";
		
		if ($iId = $this->oDb->query ( $sql, $oHeart->GetTargetId (), $oHeart->getUserId ())) {			
			return $iId;
		}
		
		return false;
	}
	
	public function DeleteHeart($iUserId, $iTargetId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.heart' ) . " WHERE user_id = ?d AND target_id = ?d ";
		if ($this->oDb->query ( $sql, $iUserId, $iTargetId )) {
			return true;
		}
		return false;
	}
	
	public function DeleteHeartByTargetId($iTargetId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.heart' ) . " WHERE target_id = ?d ";
		if ($this->oDb->query ( $sql, $iTargetId )) {
			return true;
		}
		return false;
	}
}
?>
