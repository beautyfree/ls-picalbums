<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picalbums 0.2
 * @Plugin URI: http://lsmafia.com/blog/Picalbums/
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com)
 * @Contacts: http://lsmafia.com
 * @LiveStreet Version: 0.5.1
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleSitemap extends PluginPicalbums_Inherit_PluginSitemap_ModuleSitemap {
	
	public function getExternalCounters() {
		$aCounters = parent::getExternalCounters ();
        $aCounters ['picalbumsalbums'] = ceil ($this->PluginPicalbums_Album_GetAllAlbumsCount (0) / Config::Get ( 'plugin.sitemap.objects_per_page' ));
        $aCounters ['picalbumspictures'] = ceil ($this->PluginPicalbums_Picture_GetAllPicturesCount (0) / Config::Get ( 'plugin.sitemap.objects_per_page' ));
        $aCounters ['picalbumscategory'] = ceil ( $this->PluginPicalbums_Category_GetCategorysCountByUserId (Config::Get ( 'plugin.picalbums.virtual_main_user_id' )) / Config::Get ( 'plugin.sitemap.objects_per_page' ) );
		return $aCounters;
	}

	public function getDataForPicalbumscategory($iCurrPage) {
		$iPerPage = Config::Get ( 'plugin.sitemap.objects_per_page' );
        $iCount = 0;
        $aCategories = $this->PluginPicalbums_Category_GetCategorysLimitByUserId ( Config::Get ( 'plugin.picalbums.virtual_main_user_id' ),
                                                                                   $iCount, $iCurrPage, $iPerPage );

        $sMainAlbumsRouter = Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name'));
        $aData = array ();
        foreach ( $aCategories as $oCategory ) {
            $aData [] = $this->PluginSitemap_Sitemap_GetDataForSitemapRow ( $sMainAlbumsRouter . 'category/'. $oCategory->getId (). '/',
                                                                            $oCategory->getDateModify (Config::Get ( 'plugin.picalbums.virtual_main_user_id' )),
                                                                            Config::Get ( 'plugin.picalbums.sitemap.sitemap_priority' ),
                                                                            Config::Get ( 'plugin.picalbums.sitemap.sitemap_changefreq' ) );
        }
		
		return $aData;
	}

    public function getDataForPicalbumsalbums($iCurrPage) {
		$iPerPage = Config::Get ( 'plugin.sitemap.objects_per_page' );
        $iCount = 0;
        $aAlbums = $this->PluginPicalbums_Album_GetAllAlbumsLimit (0, $iCount, $iCurrPage, $iPerPage );
        $sMainAlbumsRouter = Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name'));
        $aData = array ();
        foreach ( $aAlbums as $oAlbum ) {
            if($oAlbum->getUserId() == Config::Get ( 'plugin.picalbums.virtual_main_user_id' ))
                $sAlbumUrl = $sMainAlbumsRouter;
            else {
                $oUser = $oAlbum->GetUserOwner();
                $sAlbumUrl = $oUser->getUserAlbumsWebPath();
            }

            $aData [] = $this->PluginSitemap_Sitemap_GetDataForSitemapRow ( $sAlbumUrl . $oAlbum->getURL() . '/',
                                                                            $oAlbum->getDateModify(),
                                                                            Config::Get ( 'plugin.picalbums.sitemap.sitemap_priority' ),
                                                                            Config::Get ( 'plugin.picalbums.sitemap.sitemap_changefreq' ) );
        }

		return $aData;
	}

    public function getDataForPicalbumspictures($iCurrPage) {
		$iPerPage = Config::Get ( 'plugin.sitemap.objects_per_page' );
        $iCount = 0;
        $aPictures = $this->PluginPicalbums_Picture_GetAllPicturesLimit (0, $iCount, $iCurrPage, $iPerPage );
        $sMainAlbumsRouter = Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name'));
        $aData = array ();
        foreach ($aPictures as $oPicture ) {
            if(!$oPicture->getUserlogin())
                $sUrlStart = $sMainAlbumsRouter;
            else {
                $sUrlStart = Router::GetPath(Config::Get('plugin.picalbums.albums_router_name')) . $oPicture->getUserlogin() . '/';
            }

            $aData [] = $this->PluginSitemap_Sitemap_GetDataForSitemapRow ( $sUrlStart . $oPicture->getAlbumurl() . '/' . $oPicture->getURL() . '/',
                                                                            $oPicture->getDateAdd(),
                                                                            Config::Get ( 'plugin.picalbums.sitemap.sitemap_priority' ),
                                                                            Config::Get ( 'plugin.picalbums.sitemap.sitemap_changefreq' ) );
        }

		return $aData;
	}

}