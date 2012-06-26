<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
class PluginPicalbums_BlockPicalbumsBestPicturesByDays extends Block {
	public function Exec() {
		$sDate=date("Y-m-d H:00:00",time() - (60*60*24*Config::Get ( 'plugin.picalbums.top_images_by_days_day_count' )));
        $oUserCurrent = $this->User_GetUserCurrent();
		$aPictures = $this->PluginPicalbums_Picture_GetLastBestPicturesByDate($oUserCurrent ? true : false, $sDate, Config::Get ( 'plugin.picalbums.block_best_by_date_count' ));

        $oViewer=$this->Viewer_GetLocalViewer();
        $oViewer->Assign('sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
        $oViewer->Assign('oUserCurrent', $oUserCurrent);
        $oViewer->Assign('sTitle',  $this->Lang_Get ( 'picalbums_block_best_pictures_day' ) . ' ' . Config::Get ( 'plugin.picalbums.top_images_by_days_day_count' ) . ' ' .  $this->Lang_Get ( 'picalbums_block_best_pictures_day_date' ));
        $oViewer->Assign('aPictures', $aPictures);
        $aResult=$oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/block.PicalbumsContentPictures.tpl');

        $this->Viewer_Assign('aResult',$aResult);
	}
}
?>
