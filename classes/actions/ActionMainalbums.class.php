<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ActionMainalbums extends ActionPlugin {
	protected $oUserCurrent;
	
	public function Init() {
        require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/function.php');
		
		if(Config::Get ( 'plugin.picalbums.picalbums_only_for_auth' ) == true) {
			if (!$this->User_IsAuthorization()) {
				$this->Message_AddErrorSingle($this->Lang_Get('not_access'));
				return Router::Action('404'); 
			}
		}

		$this->Viewer_Assign ( 'sMainAlbumsRouterName', Config::Get('plugin.picalbums.main_albums_router_name') );
		$this->Viewer_Assign ( 'sMainAlbumsRouter', Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')) );
		$iAlbumsCount = $this->PluginPicalbums_Album_GetAlbumCountByUserId(Config::Get('plugin.picalbums.virtual_main_user_id'), 0);
			
		if(Config::Get ( 'lang.current' ) != 'russian') {
			$sAlbumCountTitle = $iAlbumsCount . ' ' . $this->Lang_Get ( 'picalbums_album_title' );
		} else {
			if(($iAlbumsCount == 0)||($iAlbumsCount > 5 && $iAlbumsCount < 20))
				$sAlbumCountTitle = $iAlbumsCount . ' альбомов';
			else {
				$iAlbumsCountmod = $iAlbumsCount % 10;
				if($iAlbumsCountmod == 0)
					$sAlbumCountTitle = $iAlbumsCount . ' альбомов';
				else if($iAlbumsCountmod == 1)
					$sAlbumCountTitle = $iAlbumsCount . ' альбом';
				else if(($iAlbumsCountmod > 1) &&($iAlbumsCountmod < 5))
					$sAlbumCountTitle = $iAlbumsCount . ' альбома';
				else
					$sAlbumCountTitle = $iAlbumsCount . ' альбомов';
			}
		}
		$this->oUserCurrent = $this->User_GetUserCurrent();
		$this->Viewer_Assign ( 'sIncludesTplPath', rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes');
		$this->Viewer_Assign ( 'iAlbumCount', $sAlbumCountTitle);

        $this->Lang_AddLangJs(array(
			'picalbums_text_characters_start','picalbums_text_characters_end','picalbums_confirm_delete_comment',
            'picalbums_confirm_delete_album','picalbums_show_friendpage_yet','picalbums_show_friendpage_yet_middle','picalbums_show_friendpage_yet_end','picalbums_show_friendpage_all','picalbums_show_friendpage_all_end',
            'picalbums_hide_status_upload','picalbums_make_note','picalbums_do_make_note','picalbums_click_into_picture_for_make_note',
            'picalbums_ready_delete_category','picalbums_confirm_moderate_album','picalbums_confirm_moderate_image',
            'picalbums_saving_note','picalbums_editing_note','picalbums_deleting_note', 'picalbums_ajaxuploader_button_title'
		));

        $this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		$this->SetDefaultEvent('albums');
	}
	
	protected function RegisterEvent() {
		$this->AddEventPreg('/^p$/i', '/^.+$/i', 'AlbumsListingEvent');
		$this->AddEvent('categoryedit', 'CategoryEditEvent');
		$this->AddEvent('create', 'AlbumCreateEvent');
		$this->AddEvent('albums', 'AlbumsListingEvent');

        $this->AddEventPreg('/^tag/i', '/^.+$/i', '/^.+$/i', 'TagEvent');
        $this->AddEventPreg('/^tag/i', '/^.+$/i', 'TagEvent');

		$this->AddEventPreg('/^category$/i', '/^noname/i', 'CategoryNoNameShowEvent');
		$this->AddEventPreg('/^category$/i', '/^noname/i', '/^.+$/i', 'CategoryNoNameShowEvent');

		$this->AddEventPreg('/^category$/i', '/^.+$/i', 'CategoryShowEvent');
		$this->AddEventPreg('/^category$/i', '/^.+$/i', '/^.+$/i', 'CategoryShowEvent');
		
		$this->AddEventPreg('/^profileall$/i', '/^.+$/i', 'ProfileAllEvent');
		$this->AddEventPreg('/^profileall$/i', 'ProfileAllEvent');
		
		$this->AddEventPreg('/^.+$/i', '/^p$/i', '/^.+$/i', 'AlbumShowEvent');
		$this->AddEventPreg('/^.+$/i', '/^picturesedit$/i', 'PicturesEditEvent');
		$this->AddEventPreg('/^.+$/i', '/^edit$/i', 'AlbumEditEvent');
		$this->AddEventPreg('/^.+$/i', '/^.+$/i', 'PictureShowEvent');
		$this->AddEventPreg('/^.+$/i', 'AlbumShowEvent');
	}

	// Эвент список альбомов разбитого по категориям
	protected function AlbumsListingEvent() {
		$iPage = 0;
		if($this->GetParam(0))
			$iPage = $this->GetParam(0); 

        if(Config::Get ( 'plugin.picalbums.best_pictures_slider_enable' ))
            $this->Viewer_Assign ( 'aBestPictures', $this->PluginPicalbums_Picture_GetLastBestPictures($this->oUserCurrent ? true : false , Config::Get ( 'plugin.picalbums.best_pictures_slider_piccnt' )));
		$this->Viewer_Assign ( 'aCategories', $this->PluginPicalbums_Category_GetCategorysByUserId(Config::Get ( 'plugin.picalbums.virtual_main_user_id' )) );
		$this->Viewer_Assign ( 'aNonCatAlbums', $this->PluginPicalbums_Album_GetNonCategoryAlbumsByUserId(Config::Get ( 'plugin.picalbums.virtual_main_user_id' )) );
		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.categories_listing_page_cnt' ) );
		$this->Viewer_Assign ( 'iCategoryCnt', Config::Get ( 'plugin.picalbums.categories_listing_page_cnt' ) );

		$this->SetTemplateAction ( 'albumslisting' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );
		} else {
			$this->Viewer_Assign ( 'isPjax', true );
		}
	}

    protected function TagEvent() {
		// Получение имени пользователя в чей профайл зашли
		$sTag=$this->GetParam(0);

		$iPage = 0;
		if($this->GetParam(1))
			$iPage = $this->GetParam(1);

		// Получение альбомов пользователя
		$aAlbums = $this->PluginPicalbums_Album_GetAlbumsByTag($sTag);

		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.albums_listing_page_cnt' ) );
		$this->Viewer_Assign ( 'iAlbCnt', Config::Get ( 'plugin.picalbums.albums_listing_page_cnt' ) );

		$this->Viewer_Assign ( 'aAlbums', $aAlbums );
        $this->Viewer_Assign ( 'sTag', $sTag );
		$this->Viewer_Assign ( 'bIsCanModify', false );
		$this->Viewer_AddHtmlTitle($this->Lang_Get('picalbums_albums_tags') . ' "' . $sTag . '"');

		$this->SetTemplateAction ( 'tagshow' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );
		}
		else {
			$this->Viewer_Assign ( 'isPjax', true );
		}
	}

    protected function CategoryNoNameShowEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));

		$iPage = 0;
		if($this->GetParam(1))
			$iPage = $this->GetParam(1);

        $aAlbums = $this->PluginPicalbums_Album_GetNonCategoryAlbumsByUserId(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ));

		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.albums_listing_page_cnt' ) );
		$this->Viewer_Assign ( 'iAlbCnt', Config::Get ( 'plugin.picalbums.albums_listing_page_cnt' ) );

		$this->Viewer_Assign ( 'aAlbums', $aAlbums );
		$this->Viewer_Assign ( 'sCategoryTitle', $this->Lang_Get('picalbums_main_albums_noncategory') );
        $this->Viewer_Assign ( 'sCategoryUrl', 'noname' );
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_main_albums_noncategory')));

		$this->SetTemplateAction ( 'categoryshow' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );
		}
		else {
			$this->Viewer_Assign ( 'isPjax', true );
		}
	}

	// Эвент список альбомов категории
	protected function CategoryShowEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		
		// Получение имени пользователя в чей профайл зашли
		$sCategoryId = $this->GetParam(0);	
		
		$oCategory = $this->PluginPicalbums_Category_GetCategoryById($sCategoryId);
		if(($oCategory == null)) {
			return Router::Action('404'); 
		}
		
		$iPage = 0;
		if($this->GetParam(1))
			$iPage = $this->GetParam(1);
		
		// Получение альбомов пользователя 
		$aAlbums = $oCategory->getAlbums(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ));		
		
		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.albums_listing_page_cnt' ) );
		$this->Viewer_Assign ( 'iAlbCnt', Config::Get ( 'plugin.picalbums.albums_listing_page_cnt' ) );
		
		$this->Viewer_Assign ( 'aAlbums', $aAlbums );
		$this->Viewer_Assign ( 'sCategoryTitle', $oCategory->getTitle() );
        $this->Viewer_Assign ( 'sCategoryUrl', $oCategory->getId() );
		$this->Viewer_AddHtmlTitle(htmlspecialchars($oCategory->getTitle()));
		
		$this->SetTemplateAction ( 'categoryshow' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );
		}
		else {		
			$this->Viewer_Assign ( 'isPjax', true );
		}	
	}

	protected function CategoryEditEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		
		// Только для авториизированных
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}

		if(!$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		
		$iCategoryId = $this->GetParam(0);
		$oCategory = $this->PluginPicalbums_Category_GetCategoryById($iCategoryId);
		if(!$oCategory) {
			return Router::Action('404'); 
		}
		
		$this->Viewer_Assign ( 'oCategory', $oCategory );		
		$this->SetTemplateAction ( 'categoryedit' );
	}

	// Создание альбома
	protected function AlbumCreateEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		
		// Только для авторизированных пользователей
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		
		$this->Viewer_Assign ( 'aCategories', $this->PluginPicalbums_Category_GetCategorysByUserId(Config::Get ( 'plugin.picalbums.virtual_main_user_id' )) );
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_album_add')));		
		$this->SetTemplateAction ( 'albumcreate' );
	}

	// Демонтсрация альбома
	protected function AlbumShowEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		
		// Получаем обьект альбома, в который зашли
		$sAlbumURL=$this->sCurrentEvent;	
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumByURL(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ), $sAlbumURL);
		
		// Если альбом не найден
		if(!$oAlbum) {
			return Router::Action('404'); 
		}
		
		//Фотки добавлять можно только автору
		$bIsCanAppend = false;
		if($this->oUserCurrent) {
			if((($this->oUserCurrent->getRating() >= Config::Get ( 'plugin.picalbums.minimal_rating_for_append_picture' ))) || ( $this->oUserCurrent->isAdministrator())) {
				$bIsCanAppend = true;
			}
		}
		
		// Получаем массив картинок в данном альбоме
		$aPictures = $this->PluginPicalbums_Picture_GetPictureByAlbumId($oAlbum->getId());
		
		$iPage = 0;
		if($this->GetParam(1))
			$iPage = $this->GetParam(1); 
		
		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.albumshow_page_cnt' ) );
		$this->Viewer_Assign ( 'iPicCnt', Config::Get ( 'plugin.picalbums.albumshow_page_cnt' ) );
		
		$this->Viewer_Assign ( 'oAlbum', $oAlbum );	
		$this->Viewer_Assign ( 'aPictures', $aPictures );	
		$this->Viewer_Assign ( 'bIsCanAppend', $bIsCanAppend );

        if($oAlbum->getCategoryId())
            $oCategory = $this->PluginPicalbums_Category_GetCategoryById($oAlbum->getCategoryId());
        else
            $oCategory = null;
        $this->Viewer_Assign ( 'oCategory', $oCategory );

		$this->Viewer_AddHtmlTitle(htmlspecialchars($oAlbum->getTitle()));

        $this->Lang_AddLangJs(array(
			'picalbums_swf_upload_done','picalbums_swf_do_uploading','picalbums_swf_pending','picalbums_swf_file',
            'picalbums_ajaxuploader_from', 'picalbums_ajaxuploader_cancel', 'picalbums_ajaxuploader_failed',
		));

		$this->SetTemplateAction ( 'albumshow' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->SetTemplateAction ( 'albumshow' );
		} else {
			$this->Viewer_Assign ( 'sAlbumPathStart', Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')) );
			$this->SetTemplateAction ( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes/albumshow_pjax' );
		}
	}

	// Массовое редактирование изображений
	protected function PicturesEditEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		// Только для авторизированных пользователей
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		
		$sAlbumURL=$this->sCurrentEvent;	
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumByURL(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ), $sAlbumURL);
		
		if(!$oAlbum) {
			return Router::Action('404'); 
		}

        if($oAlbum->getAddUserId() != $this->oUserCurrent->getId())
		    $this->Viewer_Assign ( 'bIsDisableSort', true );

		$this->Viewer_Assign ( 'oAlbum', $oAlbum );	
		$this->Viewer_Assign ( 'aPictures', $oAlbum->GetAllPictures() );;
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_pictures_edit')));	
		$this->SetTemplateAction ( 'picturesedit' );
	}

	// Редактирование альбома
	protected function AlbumEditEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		
		// Только для авториизированных
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
			
		$sAlbumURL=$this->sCurrentEvent;	
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumByURL(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ), $sAlbumURL);
		
		if(!$oAlbum) {
			return Router::Action('404'); 
		}
		if($oAlbum->getAddUserId() != $this->oUserCurrent->getId() && !$this->oUserCurrent->isAdministrator()) {
			return Router::Action('404'); 
		}

        $aTags = $this->PluginPicalbums_Tag_GetTagsByTargetId($oAlbum->getId());
        if($aTags)
            $sTag = join(',',$aTags);
        else
            $sTag = "";
		
		$this->Viewer_Assign ( 'bAlbumVisibility', $oAlbum->GetVisibility() );
        $this->Viewer_Assign ( 'bNeedModer', $oAlbum->getNeedModer() );
        $this->Viewer_Assign ( 'sTag', $sTag );
		$this->Viewer_Assign ( 'oAlbum', $oAlbum );	
		$this->Viewer_Assign ( 'aCategories', $this->PluginPicalbums_Category_GetCategorysByUserId(Config::Get ( 'plugin.picalbums.virtual_main_user_id' )) );

		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_album_edit')));				
		$this->SetTemplateAction ( 'albumedit' );
	}

	// Показ картинки
	protected function PictureShowEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		
		$sAlbumURL=$this->sCurrentEvent;
		$oPictureURL=$this->GetParam(0);

		// Получение альбома
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumByURL(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ), $sAlbumURL);
		
		if(!$oAlbum) {
			return Router::Action('404'); 
		}
		// Получение картинки
		$oPicture = $this->PluginPicalbums_Picture_GetPictureByURL($oAlbum->getId(), $oPictureURL);
		if(!$oPicture) {
			return Router::Action('404'); 
		}

        if (($oAlbum->getNeedModer() == 1) && (!$this->oUserCurrent || $oPicture->getAddUserId() != $this->oUserCurrent->getId())) {
            if(($oAlbum->GetUserIsModerator($this->oUserCurrent) == 0) && ($oPicture->getIsModer() == 0)) {
                return Router::Action('404');
            }
        }
		
		// Получание следующей и предыдущей картинки
		$oNextPrev = $this->PluginPicalbums_Picture_GetNextPrev($oAlbum->getId(), $oPictureURL);
		if($oNextPrev == null) {
			$sNextURL = null;
			$sPrevURL = null;
		} else {
			$sNextURL = $oNextPrev['next'];
			$sPrevURL = $oNextPrev['prev'];
		}
		
		// Получение номера текущей фотографии и послдеей 
		$oCurrLastPos = $this->PluginPicalbums_Picture_GetCurrentAndLastPosition($oAlbum->getId(), $oPictureURL);
		if($oCurrLastPos == null) {
			$iCurrentPos = null;
			$iLastPos = null;
		} else {
			$iCurrentPos = $oCurrLastPos['current'];
			$iLastPos = $oCurrLastPos['last'];
		}
		
		if($this->oUserCurrent)
			$bIsHeart = $this->PluginPicalbums_Heart_isUserVotedByTarget($this->oUserCurrent->getId(), $oPicture->getId());
		else
			$bIsHeart = false;

		// Получение информации о сердечках
		$aUsersHearted = $this->PluginPicalbums_Heart_GetUsersHeartedLimitByTargetId($oPicture->getId(), 6);
		$iHeartCount = $this->PluginPicalbums_Heart_GetUsersHeartedCountByTargetId($oPicture->getId());

		// Получение списка помеченных пользователей
		$aUserMarked = $this->PluginPicalbums_Note_GetUsersMarkedByPictureId($oPicture->getId());
		
		$iNonConfirmMark = null;
		if($this->oUserCurrent) {
			// Есть не подтвержденные вами метки
			$iNonConfirmMark = $this->PluginPicalbums_Note_isHasMarkWithNonConfirm($oPicture->getId(), $this->oUserCurrent->getId());
		}

        $aAllPictures = $oAlbum->GetPictures();
        if (Config::Get ( 'plugin.picalbums.preload_images_emable' )) {
            $aArrayPreload = array_for_preload($aAllPictures, $oPicture->getId());
            $this->Viewer_Assign ( 'aArrayPreload', $aArrayPreload );
        }
		
		$this->Viewer_Assign ( 'oAlbum', $oAlbum );	
		$this->Viewer_Assign ( 'oPicture', $oPicture );	
		$this->Viewer_Assign ( 'sNextURL', $sNextURL );
		$this->Viewer_Assign ( 'sPrevURL', $sPrevURL );
		$this->Viewer_Assign ( 'iCurrentPos', $iCurrentPos );
		$this->Viewer_Assign ( 'iLastPos', $iLastPos );
		$this->Viewer_Assign ( 'bIsHeart', $bIsHeart );

        $this->Viewer_Assign ( 'aUsersHearted', $aUsersHearted );
		$this->Viewer_Assign ( 'iHeartCount', $iHeartCount );

		$this->Viewer_Assign ( 'aUserMarked', $aUserMarked );
		$this->Viewer_Assign ( 'iNonConfirmMark', $iNonConfirmMark );

        $this->Viewer_Assign ( 'aAllPictures', $aAllPictures );
        $this->Viewer_Assign ( 'sNoteArrayJson', str_replace("'", "\\'", json_encode($this->PluginPicalbums_Picalbums_GetNoteArrayByPictureId($oAlbum->getUserId(),
                                                                                                         $oPicture->getId(),
                                                                                                         $this->oUserCurrent)))  );

        $aComments = $this->Comment_GetCommentsByTargetId($oPicture->getId(), 'picalbums');
        $aComments = $aComments['comments'];
        $this->Viewer_Assign ( 'aComments', $aComments );

        if($this->oUserCurrent) {
            if(Config::Get ('plugin.picalbums.functional_copy_picture_enable')) {
                $aCurrentUserAlbums = $this->PluginPicalbums_Album_GetAlbumsByUserId($this->oUserCurrent->getId());
                $this->Viewer_Assign ( 'aCurrentUserAlbums', $aCurrentUserAlbums );
            }
        }
		
		$this->Viewer_AddHtmlTitle(htmlspecialchars($oAlbum->getTitle()));
		$this->Viewer_AddHtmlTitle(htmlspecialchars($oPicture->getDescription()));

        $this->Lang_AddLangJs(array('panel_b', 'panel_i', 'panel_u', 'panel_s', 'panel_url', 'panel_url_promt', 'panel_code',
                                   'panel_video', 'panel_image', 'panel_cut', 'panel_quote', 'panel_list', 'panel_list_ul',
                                   'panel_list_ol', 'panel_title', 'panel_clear_tags', 'panel_video_promt', 'panel_list_li',
                                   'panel_image_promt', 'panel_user', 'panel_user_promt'));

		$this->SetTemplateAction ( 'picturelisting' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );
		} else {
			$this->Viewer_Assign ( 'isPjax', true );
		}
	}

	protected function ProfileAllEvent() {
		$this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
		$aAlbums = $this->PluginPicalbums_Album_GetPrivateAlbumsByAllUsers(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ));
		
		$iPage = 0;
		if($this->GetParam(0))
			$iPage = $this->GetParam(0); 
		
		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.allprofilepictures_page_cnt' ) );
		$this->Viewer_Assign ( 'iAllAlbumsCnt', Config::Get ( 'plugin.picalbums.allprofilepictures_page_cnt' ) );
		$this->Viewer_Assign ( 'aAlbums', $aAlbums );	
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_allprofilepictures')));

		$this->SetTemplateAction ( 'profileall' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );
		} else {
			$this->Viewer_Assign ( 'isPjax', true );
		}
	}
}
?>
