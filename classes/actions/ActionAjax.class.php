<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
	class PluginPicalbums_ActionAjax extends PluginPicalbums_Inherit_ActionAjax {	
	
		protected function EventStreamComment() {
			if ($aComments=$this->Comment_GetCommentsOnlineByTargetTypeArray(array('topic','picalbums'),Config::Get('block.stream.row'))) {
				$oUserCurrent = $this->User_GetUserCurrent();	
				$oViewer=$this->Viewer_GetLocalViewer();					
				$oViewer->Assign('oUserCurrent',$oUserCurrent);
				$oViewer->Assign('aComments',$aComments);
				$oViewer->Assign('sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
				$sTextResult=$oViewer->Fetch(Plugin::GetTemplatePath ( __CLASS__ ). 'block.stream_comment.tpl');
				$this->Viewer_AssignAjax('sText',$sTextResult);
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('block_stream_comments_no'),$this->Lang_Get('attention'));
				return;
			}
		}
	
	}
?>