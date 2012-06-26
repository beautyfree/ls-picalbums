<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
class PluginPicalbums_HookPicalbums extends Hook {
	
	// Хук вывода записей на стене
	public function RegisterHook() {		
		$this->AddHook('init_action', 'InitAction');
		$this->AddHook (Config::Get ( 'plugin.picalbums.usehookname' ), 'AlbumsPrint' );
		if ( $this->User_IsAuthorization ()) {
			$this->AddHook('template_stream_list_event_add_album', 'AddAlbumStream', __CLASS__, -5);
			$this->AddHook('template_stream_list_event_add_picture', 'AddPictureStream', __CLASS__, -5);
			$this->AddHook('template_menu_settings', 'MenuSettingsTpl');
            $this->AddHook ('template_menu_profile_profile_item', 'MenuProfileItemTpl');
		}
		$this->AddHook('template_menu_profile', 'tplMenuProfile', __CLASS__, -5);	
		$this->AddHook('template_html_head_end', 'tplHtmlHeadEnd', __CLASS__, -5);
		$this->AddHook('template_form_settings_tuning_end', 'tplTuningSettings', __CLASS__, -5);
		
		$this->AddHook('template_main_menu','MainMenu');

        $this->Viewer_Assign ( 'sMainAlbumsRouterName', Config::Get('plugin.picalbums.main_albums_router_name') );
        $this->Viewer_Assign ( 'sProfileAlbumsRouterName', Config::Get('plugin.picalbums.albums_router_name') );
	}
	
	public function InitAction() {
		if (Router::GetAction()=='settings' and Router::GetActionEvent()=='picalbums') {
    		Router::Action('picalbums_settings','settings');
    	}
    }
	
	public function MainMenu() {

		return $this->Viewer_Fetch(Plugin::GetTemplatePath('picalbums').'main_menu.tpl');
    }
	
	// Вывод на страицу профиля пользователя
	public function AlbumsPrint($aVars) {
		$oUser = $aVars ["oUserProfile"];
		if(!$oUser) {
			return Router::Action('404'); 
		}
		
		// Получение альбомов пользователя 
		$aAlbums = $this->PluginPicalbums_Album_GetAlbumsByUserId($oUser->getId());
		$this->Viewer_Assign ( 'aAlbums', $aAlbums );
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		
		return $this->Viewer_Fetch ( Plugin::GetTemplatePath ( 'Picalbums' ) . 'template_profile_albums.tpl' );
	}
			
	public function tplHtmlHeadEnd($aVars) {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		$this->Viewer_Assign('AlbumsRouter', Config::Get ( 'plugin.picalbums.albums_router_name' ) );
		return $this->Viewer_Fetch ( Plugin::GetTemplatePath ( 'Picalbums' ) . 'inject.html_head_end.tpl' );
	}
	
	public function tplMenuProfile($aVars) {
		$oUserCurrent = $this->User_GetUserCurrent();
		if($oUserCurrent)
			$this->Viewer_Assign ( 'oLoginUser', $oUserCurrent);
		else
			$this->Viewer_Assign ( 'oLoginUser', null );
		
		if (Router::GetAction() == Config::Get ( 'plugin.picalbums.albums_router_name' )) {
			$oUser = $this->User_GetUserByLogin(Router::GetActionEvent());
			if($oUser == null) {
				return Router::Action('404'); 
			}
			$oUserType = 0;
			if($oUserCurrent) {
				$oUserType = 1;
				if($oUserCurrent->getId() == $oUser->getId())
					$oUserType = 2;
				else {
					$oUserFriend = $oUser->isUsersFriend($oUserCurrent);
					if($oUserFriend)
						$oUserType = 2;
				}
			}
			$iCnt = $this->PluginPicalbums_Album_GetAlbumCountByUserId($oUser->getId(), $oUserType);
			
			if(Config::Get ( 'lang.current' ) != 'russian') {
				$sAlbumCountTitle = $iCnt . ' ' . $this->Lang_Get ( 'picalbums_album_title' );
			} else {
				if(($iCnt == 0)||($iCnt > 5 && $iCnt < 20))
					$sAlbumCountTitle = $iCnt . ' альбомов';
				else {
					$iCntMod = $iCnt % 10;
					if($iCntMod == 0)
						$sAlbumCountTitle = $iCnt . ' альбомов';
					else if($iCntMod == 1)
						$sAlbumCountTitle = $iCnt . ' альбом';
					else if(($iCntMod > 1) &&($iCntMod < 5))
						$sAlbumCountTitle = $iCnt . ' альбома';
					else
						$sAlbumCountTitle = $iCnt . ' альбомов';
				}
			}
			
			if(Config::Get ( 'plugin.picalbums.show_count_info_in_menu' )) {
				$iTotalAlbums = null;
				if($oUserCurrent) {
					if($oUserCurrent->getId() == $oUser->getId()) {
						$aUsersFriend=$this->User_GetUsersFriend($oUser->getId());
						$iTotalAlbums = 0;
						if($aUsersFriend)
							foreach($aUsersFriend as $aUserFriend) {
								$aAlbums = $aUserFriend->getPicalbums();
								
								if($aAlbums)
									foreach($aAlbums as $oAlbum) {
										if( !$oUserCurrent->isAlbumRelated($oAlbum->getId()) )
											$iTotalAlbums++;
									}
							}
					}
				}
				$this->Viewer_Assign ( 'iTotalAlbums', $iTotalAlbums);
			}			
			
			$this->Viewer_Assign ( 'iAlbumCount', $sAlbumCountTitle);
			$this->Viewer_Assign ( 'oPictureCount', $this->PluginPicalbums_Picture_GetPicturesCountByUserId($oUser->getId(), $oUserType) );
		}
		
		return $this->Viewer_Fetch(Plugin::GetTemplatePath('Picalbums').'/inject.menu_profile.tpl');
	} 
	
	public function AddAlbumStream($aVars) {
		$oStreamEvent = $aVars["oStreamEvent"];
		if($oStreamEvent) {
			$oTarget = $oStreamEvent->getTarget();
		
			$oAlbum = $this->PluginPicalbums_Album_GetAlbumById($oTarget->getAlbumId());		
			$oUser = $this->User_GetUserById($oAlbum->getUserId());
			$oAddUser = $this->User_GetUserById($oAlbum->getAddUserId());				
			$oUserCurrent = $this->User_GetUserCurrent();
			
			$this->Viewer_Assign ( 'oStreamEvent', $oStreamEvent );		
			$this->Viewer_Assign ( 'oUser', $oUser );
			$this->Viewer_Assign ( 'oAddUser', $oAddUser );
			$this->Viewer_Assign ( 'oAlbum', $oAlbum );
			$this->Viewer_Assign ( 'oUserCurrent', $oUserCurrent );
			
			$this->Viewer_Assign('AlbumsRouter', Router::GetPath(Config::Get ( 'plugin.picalbums.main_albums_router_name' )) );		
			return $this->Viewer_Fetch(Plugin::GetTemplatePath('Picalbums').'/add_album_stream.tpl');
		}
	} 
	
	public function AddPictureStream($aVars) {
		$oStreamEvent = $aVars["oStreamEvent"];
		if($oStreamEvent) {
			$oTarget = $oStreamEvent->getTarget();
		
			$oAlbum = $this->PluginPicalbums_Album_GetAlbumById($oTarget->getAlbumId());		
			$oUser = $this->User_GetUserById($oAlbum->getUserId());
			$oAddUser = $this->User_GetUserById($oAlbum->getAddUserId());			
            $oUserCurrent = $this->User_GetUserCurrent();
			
			$this->Viewer_Assign('AlbumsRouter', Router::GetPath(Config::Get ( 'plugin.picalbums.main_albums_router_name' )) );
		
			$this->Viewer_Assign ( 'oStreamEvent', $oStreamEvent );		
			$this->Viewer_Assign ( 'oUser', $oUser );
			$this->Viewer_Assign ( 'oAddUser', $oAddUser );
			$this->Viewer_Assign ( 'oAlbum', $oAlbum );
			$this->Viewer_Assign ( 'oTarget', $oTarget );
			
			$this->Viewer_Assign ( 'oUserCurrent', $oUserCurrent );
			
			return $this->Viewer_Fetch(Plugin::GetTemplatePath('Picalbums').'/add_picture_stream.tpl');
		}
	}

	public function MenuSettingsTpl() {
		$this->Viewer_Assign ( 'sMenuItemSelect', Router::GetAction() );
    	return $this->Viewer_Fetch(Plugin::GetTemplatePath('picalbums').'menu.setting.item.tpl');
    }
	
	public function tplTuningSettings() {
		if ($this->User_IsAuthorization ()) {
			$settings = $this->PluginPicalbums_Settings_GetSettingsByUserID($this->User_GetUserCurrent()->getId ());
			
			$this->Viewer_Assign ( 'CommentNotifyEmail', $settings->getCommentNotifyByEmail());
			$this->Viewer_Assign ( 'MarkNotifyEmail', $settings->getMarkNotifyByEmail());
			
			return $this->Viewer_Fetch(Plugin::GetTemplatePath('picalbums').'menu.setting.tuning.tpl');
		}
    }

    public function MenuProfileItemTpl() {
		$this->Viewer_Assign ( 'iCountPictureFavourite', $this->PluginPicalbums_Heart_GetUsersHeartedCountByUserId($this->User_GetUserCurrent()->getId()));
		return $this->Viewer_Fetch(Plugin::GetTemplatePath('picalbums').'menu_profile_profile_item.tpl');
    }
}
?>
