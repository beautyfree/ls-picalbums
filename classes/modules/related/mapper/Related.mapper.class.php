<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleRelated_MapperRelated extends Mapper {
	
	public function GetRelatedById($iRelatedId) {
		$sql = 	" SELECT a.* ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.related' ) . " a " .
				" WHERE a.related_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iRelatedId )) {
			return Engine::GetEntity ( 'PluginPicalbums_Related', $aRow );
		}
		return false;
	}
	
	public function GetRelatedsByTargetId($iTargetId) {
		$sql = 	" SELECT * " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.related' )  . 
				" WHERE target_id = ?d ";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iTargetId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Related', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetRelatedsByUserId($iUserId) {
		$sql = 	" SELECT * " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.related' )  . 
				" WHERE user_id = ?d ";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Related', $aRow );
			}
			return $aReturn;
		}
		return null;
	}

	public function AddRelated($oRelated) {
		$sql = " INSERT INTO " . Config::Get ( 'plugin.picalbums.table.related' ) . "
												(	target_id,
											        user_id
												)
												VALUES (?d, ?d) ";
		
		if ($iId = $this->oDb->query ( $sql, $oRelated->GetTargetId (), $oRelated->getUserId ())) {			
			return $iId;
		}
		
		return false;
	}
	
	public function DeleteRelated($iUserId, $iTargetId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.related' ) . " WHERE user_id = ?d AND target_id = ?d ";
		if ($this->oDb->query ( $sql, $iUserId, $iTargetId )) {
			return true;
		}
		return false;
	}
	
	public function DeleteRelatedByTargetId($iTargetId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.related' ) . " WHERE target_id = ?d ";
		if ($this->oDb->query ( $sql, $iTargetId )) {
			return true;
		}
		return false;
	}
}
?>
