<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_BlockPicalbumsProfileAlbums extends Block {
	public function Exec() {
        if((Router::GetAction() == 'profile') || ((Router::GetAction() == Config::Get ( 'plugin.picalbums.albums_router_name' ))))
		{
            $sUserLogin=Router::GetActionEvent();
            $oUserCurrent = $this->User_GetUserCurrent();
            $oUserProfile = $this->User_GetUserByLogin($sUserLogin);

            if($oUserProfile) {
				$aAlbums = $this->PluginPicalbums_Album_GetAlbumsByUserId($oUserProfile->getId());
                $oViewer=$this->Viewer_GetLocalViewer();
                $oViewer->Assign('sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
                $oViewer->Assign('oUserCurrent', $oUserCurrent);
                $oViewer->Assign('oUserProfile', $oUserProfile);
                $oViewer->Assign('sTitle',  $this->Lang_Get ( 'picalbums_block_profile_albums' ));
                $oViewer->Assign('aAlbums', $aAlbums);
                $aResult=$oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/block.PicalbumsContentProfileAlbums.tpl');

                $this->Viewer_Assign('aResult',$aResult);
            }
        }
	}
}
?>
