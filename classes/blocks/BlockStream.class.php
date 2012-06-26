<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_BlockStream extends PluginPicalbums_Inherit_BlockStream  {
	public function Exec() {
		
		if ($aComments=$this->Comment_GetCommentsOnlineByTargetTypeArray(array('topic','picalbums'),Config::Get('block.stream.row'))) {
			$oViewer=$this->Viewer_GetLocalViewer();
			$oUserCurrent = $this->User_GetUserCurrent();
			$oViewer->Assign('aComments',$aComments);			
			$oViewer->Assign('oUserCurrent',$oUserCurrent);
			$oViewer->Assign('sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
			$sTextResult=$oViewer->Fetch(Plugin::GetTemplatePath ( __CLASS__ ). 'block.stream_comment.tpl');
			$this->Viewer_Assign('sStreamComments',$sTextResult);
		}
	}
}
?>