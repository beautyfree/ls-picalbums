<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleNote_MapperNote extends Mapper {
	
	public function GetNoteById($iNoteId) {
		$sql = 	" SELECT a.* ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " a " .
				" WHERE a.note_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iNoteId )) {
			return Engine::GetEntity ( 'PluginPicalbums_Note', $aRow );
		}
		return false;
	}
	
	public function GetNotesByPictureId($iPictureId) {
		$sql = 	" SELECT a.* ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " a " .
				" WHERE a.picture_id = ?d";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iPictureId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Note', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetUsersMarkedByPictureId($iPictureId) {
		$sql = 	" SELECT a.user_mark_id ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " a " .
				" WHERE a.picture_id = ?d AND is_confirm = 1 ";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iPictureId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = $aRow['user_mark_id'];
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetPictureIdByUserMarkInAlbum($iUserMarkId, $oAlbumId, $iLimit) {
		$sql = 	" SELECT n.picture_id ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " n, " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p " .
				" WHERE n.user_mark_id = ?d AND n.is_confirm = 1 AND n.picture_id = p.picture_id AND p.album_id = ?d LIMIT 0, ?d ";
		
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserMarkId, $oAlbumId, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = $aRow['picture_id'];
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetPictureIdByUserMark($iUserMarkId, $iLimit) {
		$sql = 	" SELECT a.picture_id ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " a " .
				" WHERE a.user_mark_id = ?d AND is_confirm = 1 LIMIT 0, ?d ";
		
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserMarkId, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = $aRow['picture_id'];
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetPictureIdByUserMarkAll($iUserMarkId) {
		$sql = 	" SELECT a.picture_id ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " a " .
				" WHERE a.user_mark_id = ?d AND is_confirm = 1 ";
		
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserMarkId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = $aRow['picture_id'];
			}
			return $aReturn;
		}
		return false;
	}
	
	public function ConfirmMarks($iPictureId, $iUserId) {
		$sql = " UPDATE " . Config::Get ( 'plugin.picalbums.table.note' ) . "
				SET is_confirm = 1 WHERE picture_id = ?d AND user_mark_id = ?d ";
		
		if ($this->oDb->query ( $sql, $iPictureId, $iUserId)) {
			return true;
		}
		
		return false;
	}
	
	public function AddNote($oNote) {
		$sql = " INSERT INTO " . Config::Get ( 'plugin.picalbums.table.note' ) . "
												(  `left`,
													top,
													width,
													height,
													dateadd,
													note,
													link,
											        user_id,
													user_mark_id,
											        picture_id,
													is_confirm
												)
												VALUES (?, ?, ?, ?, ?, ?, ?, ?d, ?d, ?d, ?d) ";
		
		if ($iId = $this->oDb->query ( $sql, $oNote->GetLeft (),
											 $oNote->GetTop (),
											 $oNote->GetWidth (),
											 $oNote->GetHeight (),
											 $oNote->GetDateAdd (),
											 $oNote->GetNote (),
											 $oNote->GetLink (),
											 $oNote->GetUserId (),
											 $oNote->GetUserMarkId (),
											 $oNote->GetPictureId (),
											 $oNote->GetIsConfirm ()
											 )) {			
			return $iId;
		}
		
		return false;
	}
	
	public function EditNote($oNote) {
		$sql = " UPDATE " . Config::Get ( 'plugin.picalbums.table.note' ) . "
											SET		`left` = ?,
													top = ?,
													width = ?,
													height = ?,
													dateadd = ?,
													note = ?,
													user_mark_id = ?,
													link = ?
												
											WHERE note_id = ?d ";
		
		if ($iId = $this->oDb->query ( $sql, $oNote->GetLeft (),
											 $oNote->GetTop (),
											 $oNote->GetWidth (),
											 $oNote->GetHeight (),
											 $oNote->GetDateAdd (),
											 $oNote->GetNote (),
											 $oNote->GetUserMarkId (),
											 $oNote->GetLink (),
											 $oNote->GetId ()
											 )) {			
			return $iId;
		}
		
		return false;
	}
	
	public function DeleteNote($iNoteId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " WHERE note_id = ?d ";
		if ($this->oDb->query ( $sql, $iNoteId )) {
			return true;
		}
		return false;
	}
	
	public function DeleteNoteByPictureId($iPictureId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " WHERE picture_id = ?d ";
		if ($this->oDb->query ( $sql, $iPictureId )) {
			return true;
		}
		return false;
	}
	
	public function NonConfirmMarks($iPictureId, $iUserId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.note' ) . " WHERE picture_id = ?d AND user_mark_id = ?d AND is_confirm = 0 ";
		if ($this->oDb->query ( $sql, $iPictureId, $iUserId)) {
			return true;
		}
		return false;
	}
}
?>
