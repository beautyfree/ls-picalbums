<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleComment_MapperComment extends PluginPicalbums_Inherit_ModuleComment_MapperComment {	

	// Получение времени последнего комментария
	public function GetLastCommentDate($iUserId, $sTargetType) {
		$sql = 	" SELECT MAX(a.comment_date) as maxdate ".
				" FROM " . Config::Get('db.table.comment') . " a " .
				" WHERE user_id = ?d AND target_type= ?  ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId, $sTargetType )) {
			return $aRow['maxdate'];
		}
		return false;
	}
	
	// Удаление комментария по его идентификатору
	public function DeleteComment($iCommentId) {
		$sql = " DELETE FROM " . Config::Get('db.table.comment') . " WHERE comment_id = ?d ";
		if ($this->oDb->query ( $sql, $iCommentId )) {
			return true;
		}
		return false;
	}
	
	public function GetCommentsAllByTargetTypeArray($sTargetTypeArray,&$iCount,$iCurrPage,$iPerPage,$aExcludeTarget=array(),$aExcludeParentTarget=array()) {
		$sql = "SELECT 					
					comment_id 				
				FROM 
					".Config::Get('db.table.comment')." 
				WHERE								
					comment_delete = 0
					AND
					comment_publish = 1 AND
					
					(
						(
							target_type = 'topic'
							{ AND target_id NOT IN(?a) }
							{ AND target_parent_id NOT IN(?a) }
						)
						OR
						(
							target_type != 'topic'
							AND target_type in (?a)
						)
					)
				ORDER by comment_id desc
				LIMIT ?d, ?d ";			
		$aComments=array();
		if ($aRows=$this->oDb->selectPage(
				$iCount,$sql,
				(count($aExcludeTarget)?$aExcludeTarget:DBSIMPLE_SKIP),
				(count($aExcludeParentTarget)?$aExcludeParentTarget:DBSIMPLE_SKIP),
				$sTargetTypeArray,
				($iCurrPage-1)*$iPerPage, 
				$iPerPage
			)
		) {
			foreach ($aRows as $aRow) {
				$aComments[]=$aRow['comment_id'];
			}		
		}
		return $aComments;
	}
	
	public function GetCommentsOnlineByTargetTypeArray($sTargetTypeArray,$aExcludeTargets,$iLimit) {		
		$sql = "SELECT 					
					comment_id	
				FROM 
					".Config::Get('db.table.comment_online')." 
				WHERE 												
					( 
							(target_type = 'topic' 
								{ AND target_parent_id NOT IN(?a) }
							) 
						OR 
							(target_type != 'topic' AND target_type IN (?a) ) 
					)
				ORDER by comment_online_id desc limit 0, ?d ; ";
		
		$aComments=array();
		if ($aRows=$this->oDb->select(
				$sql,
				(count($aExcludeTargets)?$aExcludeTargets:DBSIMPLE_SKIP),
				$sTargetTypeArray,
				$iLimit
			)
		) {
			foreach ($aRows as $aRow) {
				$aComments[]=$aRow['comment_id'];
			}
		}
		return $aComments;
	}
	
	public function GetCountCommentsByTargetId($iId, $sTargetType)  {
		$sql = "SELECT 
					count(comment_id) as c
				FROM 
					".Config::Get('db.table.comment')."
				WHERE 
					target_id = ?d 
					AND			
					target_type = ? 					
					AND
					comment_pid IS NULL	;";
		
		if ($aRow=$this->oDb->selectRow($sql,$iId,$sTargetType)) {
			return $aRow['c'];
		}
	}
	
}
?>
