<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_BlockPicalbumsLastPictures extends Block {
	public function Exec() {
		$oUserCurrent = $this->User_GetUserCurrent();
		$aPictures = $this->PluginPicalbums_Picture_GetLastPictures($oUserCurrent ? true : false , Config::Get ( 'plugin.picalbums.block_last_count' ));

        $oViewer=$this->Viewer_GetLocalViewer();
        $oViewer->Assign('sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
        $oViewer->Assign('oUserCurrent', $oUserCurrent);
        $oViewer->Assign('sTitle',  $this->Lang_Get ( 'picalbums_block_last_pictures' ));
        $oViewer->Assign('aPictures', $aPictures);
        $aResult=$oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/block.PicalbumsContentPictures.tpl');

        $this->Viewer_Assign('aResult',$aResult);
	}
}
?>
