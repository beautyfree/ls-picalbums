<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleNote extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	public function GetNoteById($iNoteId) {
		return $this->oMapper->GetNoteById ( $iNoteId );
	}
	
	public function GetNotesByPictureId($iPictureId) {
		$tag = "note_by_picture_id_{$iPictureId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetNotesByPictureId ( $iPictureId );
			$this->Cache_Set ( $data, $tag, array ("note_update_{$iPictureId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetConfirmedNotesByPictureId($iPictureId, $iUserId) {
		$notes = $this->GetNotesByPictureId($iPictureId);
		$aReturn = array ();
		foreach($notes as $note) {
			if(($note->getIsConfirm() == 1) || ($note->getIsConfirm() == '1'))
				$aReturn[] = $note;
			else if($note->getUserId() == $iUserId)
				$aReturn[] = $note;
			else if($note->getUserMarkId() == $iUserId)
				$aReturn[] = $note;
		}
		return $aReturn;
	}
	
	public function isHasMarkWithNonConfirm($iPictureId, $iUserId) {
		$notes = $this->GetNotesByPictureId($iPictureId);
		$aReturn = 0;
		if($notes)
			foreach($notes as $note) {
				if((($note->getIsConfirm() == 0) || ($note->getIsConfirm() == '0')) && ($note->getUserMarkId() == $iUserId))
					$aReturn++;
			}
		return $aReturn;
	}
	
	public function MarkCountByOneUser($iPictureId, $iUserId) {
		$notes = $this->GetNotesByPictureId($iPictureId);
		$aReturn = 0;
		if($notes)
			foreach($notes as $note) {
				if($note->getUserMarkId() == $iUserId)
					$aReturn++;
			}
		return $aReturn;
	}
	
	public function getUsersWhoMarkedAnotheUserByPicture($iPictureId, $iUserId) {
		$notes = $this->GetNotesByPictureId($iPictureId);
		$aReturn = array ();
		if($notes)
			foreach($notes as $note) {
				if(($note->getUserMarkId() == $iUserId) && ($note->getIsConfirm() == 0))
					$aReturn[] = $note->getUserId();
			}
			
		return array_unique($aReturn);
	}
	
	public function GetUsersMarkedByPictureId($iPictureId) {
		$tag = "note_user_marked_by_picture_id_{$iPictureId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetUsersMarkedByPictureId ( $iPictureId );
			$data = $this->User_GetUsersByArrayId($data);
			$this->Cache_Set ( $data, $tag, array ("note_update_{$iPictureId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetPicturesByUserMark($iUserMarkId, $iLimit) {
		$tag = "note_by_user_mark_id_{$iUserMarkId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPictureIdByUserMark ( $iUserMarkId, $iLimit );
			$data = $this->PluginPicalbums_Picture_GetPicturesByArrayId($data);
			$this->Cache_Set ( $data, $tag, array ("note_update_mark_user"), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetPictureIdByUserMarkInAlbum($iUserMarkId, $oAlbumId, $iLimit) {
		$tag = "note_by_user_mark_in_album_id_{$iUserMarkId}_{$oAlbumId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPictureIdByUserMarkInAlbum ( $iUserMarkId, $oAlbumId, $iLimit );
			$data = $this->PluginPicalbums_Picture_GetPicturesByArrayId($data);
			$this->Cache_Set ( $data, $tag, array ("note_update_mark_user"), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetPicturesByUserMarkAll($iUserMarkId) {
		$tag = "note_by_user_mark_all_id_{$iUserMarkId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPictureIdByUserMarkAll ( $iUserMarkId );
			$data = $this->PluginPicalbums_Picture_GetPicturesByArrayId($data);
			$this->Cache_Set ( $data, $tag, array ("note_update_mark_user"), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function AddNote($oNote) {		
		if ($this->oMapper->AddNote ( $oNote )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("note_update_mark_user", "note_update_{$oNote->getPictureId()}" ) );
			return true;
		}
		return false;
	}
	
	public function EditNote($oNote) {
		if ($this->oMapper->EditNote ( $oNote )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("note_update_mark_user", "note_update_{$oNote->getPictureId()}" ) );
			return true;
		}
		return false;
	}
	
	public function ConfirmMarks($iPictureId, $iUserId) {
		if ($this->oMapper->ConfirmMarks ( $iPictureId, $iUserId )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("note_update_mark_user", "note_update_{$iPictureId}" ) );
			return true;
		}
		return false;
	}
	
	public function NonConfirmMarks($iPictureId, $iUserId) {
		if ($this->oMapper->NonConfirmMarks ( $iPictureId, $iUserId )) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("note_update_mark_user", "note_update_{$iPictureId}" ) );
			return true;
		}
		return false;
	}
	
	public function DeleteNote($iNoteId) {
		$oNote = $this->GetNoteById($iNoteId);
		if ($this->oMapper->DeleteNote ( $iNoteId )) {
			if($oNote)
				$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("note_update_mark_user", "note_update_{$oNote->getPictureId()}" ) );
			return true;
		}
		return false;
	}
	
	public function DeleteNoteByPictureId($iPictureId) {
		if ($this->oMapper->DeleteNoteByPictureId($iPictureId)) {
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("note_update_mark_user", "note_update_{$iPictureId}" ) );
			return true;
		}
		return false;
	}
}
?>
