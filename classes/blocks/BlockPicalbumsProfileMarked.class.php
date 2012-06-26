<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_BlockPicalbumsProfileMarked extends Block {
	public function Exec() {
        $aResult=null;
		if((Router::GetAction() == 'profile') || ((Router::GetAction() == Config::Get ( 'plugin.picalbums.albums_router_name' )))) {
            $sUserLogin=Router::GetActionEvent();
            $oUserCurrent = $this->User_GetUserCurrent();
            $oUserProfile = $this->User_GetUserByLogin($sUserLogin);
            if($oUserProfile) {
                $sAlbumURL=Router::GetParam(0);
				$isBestAlbum = false;
				if($sAlbumURL) {
					$sAlbum = $this->PluginPicalbums_Album_GetAlbumByURL($oUserProfile->getId(), $sAlbumURL);
					if($sAlbum) {
						$aPictures = $this->PluginPicalbums_Note_GetPictureIdByUserMarkInAlbum($oUserProfile->getId(),
																									$sAlbum->getId(),
																									Config::Get ( 'plugin.picalbums.block_profile_mark' ));
						$isBestAlbum = true;
					}
				}

				if($isBestAlbum == false)
					$aPictures = $this->PluginPicalbums_Note_GetPicturesByUserMark($oUserProfile->getId(),
																								Config::Get ( 'plugin.picalbums.block_profile_mark' ));
                $oViewer=$this->Viewer_GetLocalViewer();
                $oViewer->Assign('sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
                $oViewer->Assign('oUserCurrent', $oUserCurrent);
                $oViewer->Assign('oUserProfile', $oUserProfile);
                $sTitle = $this->Lang_Get ( 'picalbums_block_profile_marked' ) . ' ' .$oUserProfile->getLogin();
                if($isBestAlbum)
                    $sTitle .= ' ' . $this->Lang_Get ( 'picalbums_block_profile_best_pictures_in_album' );
                $oViewer->Assign('sTitle', $sTitle);

                if($aPictures) {
                    $oViewer->Assign('sAdditionalLink',  $oUserProfile->getUserAlbumsWebPath() . 'note/');
                    $oViewer->Assign('sAdditionalLinkText',  $this->Lang_Get ( 'picalbums_marks_all' ));
                }

                $oViewer->Assign('aPictures', $aPictures);
                $aResult=$oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/block.PicalbumsContentPictures.tpl');
            }
	    }
        $this->Viewer_Assign('aResult',$aResult);
	}
}
?>
