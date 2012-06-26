<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
class PluginPicalbums_BlockPicalbumsAlbumGuestMarked extends Block {
	public function Exec() {
        $aResult=null;
		if((Router::GetAction() == Config::Get ( 'plugin.picalbums.albums_router_name' ))) {
            $sUserLogin=Router::GetActionEvent();
            $oUserCurrent = $this->User_GetUserCurrent();
            if($oUserCurrent) {
                $oUserProfile = $this->User_GetUserByLogin($sUserLogin);
                if($oUserProfile) {
                    $sAlbumURL=Router::GetParam(0);
                    $sAlbum = $this->PluginPicalbums_Album_GetAlbumByURL($oUserProfile->getId(), $sAlbumURL);
                    if($sAlbum) {
                        $aPictures = $this->PluginPicalbums_Note_GetPictureIdByUserMarkInAlbum($oUserCurrent->getId(),
                                                                                                       $sAlbum->getId(),
                                                                                                       Config::Get ( 'plugin.picalbums.block_profile_mark' ));
                        $oViewer=$this->Viewer_GetLocalViewer();
                        $oViewer->Assign('sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
                        $oViewer->Assign('oUserCurrent', $oUserCurrent);
                        $oViewer->Assign('oUserProfile', $oUserProfile);
                        $oViewer->Assign('sTitle',  $this->Lang_Get ( 'picalbums_block_profile_marked_in_album' ));
                        $oViewer->Assign('aPictures', $aPictures);
                        $oViewer->Assign('bDontShowLogin', true);
                        $aResult=$oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/block.PicalbumsContentProfilePictures.tpl');
                    }
                }
            }
	    }
        $this->Viewer_Assign('aResult',$aResult);
	}
}
?>
