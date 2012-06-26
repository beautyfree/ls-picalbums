<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleComment extends PluginPicalbums_Inherit_ModuleComment {
	
	// Получение времени последнего комментария
	public function GetLastCommentDate($iUserId, $sTargetType) {
		return $this->oMapper->GetLastCommentDate ( $iUserId, $sTargetType );
	}
	
	public function DeleteComment($iCommentId) {
		$oComment = $this->GetCommentById($iCommentId);
		
		if ($this->oMapper->DeleteComment ( $iCommentId )) {
			
			if($oComment) {
				$this->DeleteCommentOnlineByArrayId($iCommentId, $oComment->getTargetType());
				$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array (
					"comment_update",
					"comment_update_status_{$oComment->getTargetType()}",
					"comment_update_{$oComment->getTargetType()}_{$oComment->getTargetId()}" ));
			}
			return true;
		}
		return false;
	}
	
	public function GetCommentsAllByTargetTypeArray($sTargetTypeArray,$iPage,$iPerPage,$aExcludeTarget=array(),$aExcludeParentTarget=array()) {		
		$s=serialize($aExcludeTarget).serialize($aExcludeParentTarget).serialize($sTargetTypeArray);
		if (false === ($data = $this->Cache_Get("comment_all_{$iPage}_{$iPerPage}_{$s}"))) {			
			$data = array('collection'=>$this->oMapper->GetCommentsAllByTargetTypeArray($sTargetTypeArray,$iCount,$iPage,$iPerPage,$aExcludeTarget,$aExcludeParentTarget),'count'=>$iCount);
			$arr = Array();
			foreach($sTargetTypeArray as $sTargetType) {
				$arr[] = "comment_new_{$sTargetType}";
				$arr[] = "comment_update_status_{$sTargetType}";
			}
			$this->Cache_Set($data, "comment_all_{$iPage}_{$iPerPage}_{$s}", $arr, 60*60*24*1);
		}
		$data['collection']=$this->GetCommentsAdditionalData($data['collection'],array('target','favourite','user'=>array()));
		return $data;		 	
	}	
	
	public function GetCommentsOnlineByTargetTypeArray($sTargetTypeArray, $iLimit) {
		$aCloseBlogs = ($this->oUserCurrent)
			? $this->Blog_GetInaccessibleBlogsByUser($this->oUserCurrent)
			: $this->Blog_GetInaccessibleBlogsByUser();
			
		$s=serialize($aCloseBlogs);
		$sTargetTypeSerialize = serialize($sTargetTypeArray);
		
		if (false === ($data = $this->Cache_Get("comment_online_{$sTargetTypeSerialize}_{$s}_{$iLimit}"))) {			
			$data = $this->oMapper->GetCommentsOnlineByTargetTypeArray($sTargetTypeArray,$aCloseBlogs,$iLimit);
			$arr = Array();
			foreach($sTargetTypeArray as $sTargetType) {
				$arr[] = "comment_online_update_{$sTargetType}";
			}
			
			$this->Cache_Set($data, "comment_online_{$sTargetTypeSerialize}_{$s}_{$iLimit}", $arr, 60*60*24*1);
		}
		$data=$this->GetCommentsAdditionalData($data);
		return $data;		
	}
	
	public function GetCountCommentsByTargetId($iId,$sTargetType) {
		if (false === ($data = $this->Cache_Get("comment_count_pic_{$sTargetType}_{$iId}"))) {
			$data = $this->oMapper->GetCountCommentsByTargetId($iId,$sTargetType);
			$this->Cache_Set($data, "comment_count_pic_{$sTargetType}_{$iId}", array("comment_update", "comment_new_{$sTargetType}_{$iId}","comment_target_{$iId}_{$sTargetType}"), 60*60*24*1);
		}
		return $data;		 	
	}
	
}
?>
