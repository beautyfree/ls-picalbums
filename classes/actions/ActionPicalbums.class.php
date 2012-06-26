<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * @LiveStreet Version: 0.5.1
 * ----------------------------------------------------------------------------
 */


class PluginPicalbums_ActionPicalbums extends ActionPlugin {
	
	protected $oUserCurrent;
	
	public function Init() {
        require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/function.php');
		
		if(Config::Get ( 'plugin.picalbums.picalbums_only_for_auth' ) == true) {
			if (!$this->User_IsAuthorization()) {
				$this->Message_AddErrorSingle($this->Lang_Get('not_access'));
				return Router::Action('404'); 
			}
		}

		$this->Viewer_Assign('sIncludesTplPath', rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes');
        $this->Viewer_Assign('sTemplateWebPathPicalbumsPlugin', rtrim(Plugin::GetTemplateWebPath(__CLASS__),'/'));
        $this->Viewer_Assign ( 'sMainAlbumsRouter', Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')) );
        $this->oUserCurrent = $this->User_GetUserCurrent();

        $this->Lang_AddLangJs(array(
			'picalbums_text_characters_start','picalbums_text_characters_end','picalbums_confirm_delete_comment',
            'picalbums_confirm_delete_album','picalbums_show_friendpage_yet','picalbums_show_friendpage_yet_middle','picalbums_show_friendpage_yet_end','picalbums_show_friendpage_all','picalbums_show_friendpage_all_end',
            'picalbums_hide_status_upload','picalbums_make_note','picalbums_do_make_note','picalbums_click_into_picture_for_make_note',
            'picalbums_ready_delete_category','picalbums_confirm_moderate_album','picalbums_confirm_moderate_image',
            'picalbums_saving_note','picalbums_editing_note','picalbums_deleting_note'
		));
		
		// Получаем текущего юзера
		$this->SetDefaultEvent('albums');
	}
	
	protected function RegisterEvent() {
		$this->AddEvent('ajaxappendalbum', 'AjaxAppendAlbum');
		$this->AddEvent('ajaxremovealbum', 'AjaxRemoveAlbum');
		$this->AddEvent('ajaxeditalbum', 'AjaxEditAlbum');
		$this->AddEvent('ajaxeditpictures', 'AjaxEditPictures');
		$this->AddEvent('ajaxaddpicture', 'AjaxAppendPicture');		
		$this->AddEvent('ajaxuploadserviceflash', 'AlaxEventUploadImageFlash');
		$this->AddEvent('ajaxuploadserviceajax', 'AlaxEventUploadImageAjax');		
		$this->AddEvent('ajaxgetcommentforpicture', 'AjaxGetComments');		
		$this->AddEvent('ajaxremovecomment', 'AjaxRemoveComment');
		$this->AddEvent('ajaxappendcomment', 'AjaxAppendComment');	
		$this->AddEvent('ajaxheartpicture', 'AjaxHeartPicture');
		$this->AddEvent('ajaxallheartusers', 'AjaxAllHeartPicture');		
		$this->AddEvent('ajaxnote', 'AjaxNote');
		$this->AddEvent('ajaxuserautocomplete', 'AjaxUserAutoComplete');
		$this->AddEvent('ajaxuserautocompleteblacklist', 'AjaxUserAutoCompleteBlackList');
		$this->AddEvent('ajaxremovefromblacklist', 'AjaxRemoveFromBlackList');
		$this->AddEvent('ajaxappendtoblacklist', 'AjaxAppendToBlackList');
		$this->AddEvent('ajaxmarkconfirm', 'AjaxMarkConfirm');
		$this->AddEvent('ajaxalbumshownextpictures', 'AjaxShowNextPictures');
		$this->AddEvent('ajaxsavefriendpagehistory', 'AjaxSaveFriendPageHistory');
		$this->AddEvent('ajaxremovecategory', 'AjaxRemoveCategory');
		$this->AddEvent('ajaxeditcategory', 'AjaxEditCategory');
		$this->AddEvent('ajaxsortpictures', 'AjaxSortPictures');
        $this->AddEvent('ajaxsortcatset', 'AjaxSortCatSet');
        $this->AddEvent('ajaxmoderatealbum', 'ModerateAllPicturesInAlbum');
        $this->AddEvent('ajaxmoderatepicture', 'ModeratePicturesInAlbum');
        $this->AddEvent('ajaxtagautocompleter', 'AjaxAutocompleterTag');
        $this->AddEvent('ajaxcopypicture', 'AjaxCopyPicture');
		
		$this->AddEventPreg('/^.+$/i', '/^p$/i', '/^.+$/i', 'AlbumsListingEvent');
		$this->AddEventPreg('/^.+$/i', '/^friend$/i', '/^.+$/i', 'AlbumsFriendListingEvent');
		$this->AddEventPreg('/^.+$/i', '/^friend$/i', 'AlbumsFriendListingEvent');
		$this->AddEventPreg('/^.+$/i', '/^create$/i', 'AlbumCreateEvent');

        $this->AddEventPreg('/^.+$/i', '/^allpictures$/i', '/^.+$/i', 'AllPicturesShowEvent');
		$this->AddEventPreg('/^.+$/i', '/^allpictures$/i', 'AllPicturesShowEvent');
        $this->AddEventPreg('/^.+$/i', '/^favourite/i', '/^.+$/i', 'FavouriteShowEvent');
        $this->AddEventPreg('/^.+$/i', '/^favourite/i', 'FavouriteShowEvent');

		$this->AddEventPreg('/^.+$/i', '/^note$/i', 'AllPicturesNoteShowEvent');
		if(Config::Get('plugin.picalbums.paraloid_enable'))
			$this->AddEventPreg('/^.+$/i', '/^polaroid$/i', 'ParanoidShowEvent');
		if(Config::Get('plugin.picalbums.slidergallary_enable'))
			$this->AddEventPreg('/^.+$/i', '/^slidergallery$/i', 'SliderGalleryShowEvent');	
			
		$this->AddEventPreg('/^.+$/i', '/^.+$/i', '/^p$/i', '/^.+$/i', 'AlbumShowEvent');
			
		$this->AddEventPreg('/^.+$/i', '/^.+$/i', '/^edit$/i', 'AlbumEditEvent');
		$this->AddEventPreg('/^.+$/i', '/^.+$/i', '/^picturesedit$/i', 'PicturesEditEvent');		
		$this->AddEventPreg('/^.+$/i', '/^.+$/i', '/^.+$/i', 'PictureShowEvent');
		$this->AddEventPreg('/^.+$/i', '/^.+$/i', 'AlbumShowEvent');
		
		$this->AddEventPreg('/^.+$/i', 'AlbumsListingEvent');
	}
	
	private function TextParser($sText, $isComment) {
        require_once(Config::Get('path.root.engine').'/lib/external/Jevix/jevix.class.php');
		$oJevix = new Jevix();		
		if($isComment == true)
			$sType = 'comment';
		else
			$sType = 'title';
			
		$aConfig=Config::Get('plugin.picalbums.jevix.'.$sType);
		
		if (is_array($aConfig)) {
			foreach ($aConfig as $sMethod => $aExec) {
				foreach ($aExec as $aParams) {
					call_user_func_array(array($oJevix,$sMethod), $aParams);
				}
			}
			
			// Хардкодим некоторые параметры
			unset($oJevix->entities1['&']); // разрешаем в параметрах символ &
			if (Config::Get('view.noindex') and isset($oJevix->tagsRules['a'])) {
				$oJevix->cfgSetTagParamDefault('a','rel','nofollow',true);
			}
		}
		
		$errors = null;
		$sText = $oJevix->parse($sText, $errors);
		return $sText;		
	}
	
	// Вспомогательная функция - замена имени пользователя на ссылку
	private function UserNameLinkDetect($text) {
		$iTextLen = strlen($text);
		$i = 0;
		$sResultText = "";
		
		while($i < $iTextLen) {
			if($text[$i] != '@') {
				$sResultText .= $text[$i]; $i++;
			} else {
				$sCurrentUserName = ""; $i++;
				while( ($i < $iTextLen) && (($text[$i] >= 'a' && $text[$i] <= 'z') || 
										  ($text[$i] >= 'A' && $text[$i] <= 'Z') ||
										  ($text[$i] >= '0' && $text[$i] <= '9') || 
										   $text[$i] == '_' || $text[$i] == '-')) {
					$sCurrentUserName .= $text[$i]; $i++;					   	
				}
				
				if($this->User_GetUserByLogin($sCurrentUserName) == false)
					$sResultText .= "@".$sCurrentUserName;
				else				
					$sResultText .= "<a href='" . Router::GetPath('profile') . $sCurrentUserName . "/'>" . $sCurrentUserName . "</a>";
			}			
		}
		
		return $sResultText;
	}
	
	private function BuildParams() {
		$aDefault = (array)Config::Get('module.image.default');
		$aNamed   = (array)Config::Get('plugin.picalbums.image');
		
		return func_array_merge_assoc($aDefault, $aNamed);
	}
	
	private function GetIdDir($iUserId) {
		return Config::Get('path.uploads.root').'/picalbums/'.preg_replace('~(.{2})~U', "\\1/", str_pad($iUserId, 6, "0", STR_PAD_LEFT)).date('Y/m/d');
	}
	
	// Загрузка изображения на сервер, испольузется flash-ом и обычным загрузчиком
	private function UploadAlbumImageFile($aFile, $iUserId) {
		
		if(!is_array($aFile) || !isset($aFile['tmp_name'])) {
			return false;
		}
		// Копируем файл в временное хранилище
		$sFileTmp=Config::Get('sys.cache.dir').func_generator();		
		if (!move_uploaded_file($aFile['tmp_name'],$sFileTmp)) {			
			return false;
		}
		// Получаем каталог для сохранения
		$sDirUpload=$this->GetIdDir($iUserId);
		$aParams=$this->BuildParams();
		
		// Копируем оригинал если нужно
		if(!Config::Get ( 'plugin.picalbums.is_save_original' ))
			$sFileOriginalPath = null;
		else {
			if ($sFileImage=$this->Image_ResizeAdditional($sFileTmp,
														$sDirUpload,
														func_generator(Config::Get('plugin.picalbums.func_generator_length')),
														0, 0, null, null, true, null)) 
				$sFileOriginalPath = $this->Image_GetWebPath($sFileImage);
			else
				$sFileOriginalPath =null;
		}
		
		$sExif = '';
		if(Config::Get('plugin.picalbums.exif_enable')) {
			$sExif = $this->GetExif($sFileTmp);
		}
		
		// Делаем ресайзы, для показа и для 2-ух превьюх
		if ($sFileImage=$this->Image_ResizeAdditional(	$sFileTmp,
														$sDirUpload,
														func_generator(Config::Get('plugin.picalbums.func_generator_length')),
														Config::Get('plugin.picalbums.picture_max_width_size'),
														Config::Get('plugin.picalbums.picture_max_height_size'),
														Config::Get('plugin.picalbums.picture_resize_width_value'),
														Config::Get('plugin.picalbums.picture_resize_height_value'),
														true,
														$aParams)) 
		{
			$sFilePath = $this->Image_GetWebPath($sFileImage);
			
			if ($sFileImage=$this->Image_ResizeAdditional(	$sFileTmp,
															$sDirUpload,
															func_generator(Config::Get('plugin.picalbums.func_generator_length')),
															Config::Get('plugin.picalbums.picture_max_width_size'),
															Config::Get('plugin.picalbums.picture_max_height_size'),
															Config::Get('plugin.picalbums.miniature_resize_width_value'),
															Config::Get('plugin.picalbums.miniature_resize_height_value'),
															true,
															$aParams,
															null, 
															Config::Get('plugin.picalbums.miniature_crop'),
															Config::Get('plugin.picalbums.miniature_crop_middle'))) 
			{
				$sFileMiniaturePath = $this->Image_GetWebPath($sFileImage);
				
				if ($sFileImage=$this->Image_ResizeAdditional(	$sFileTmp,
																$sDirUpload,
																func_generator(Config::Get('plugin.picalbums.func_generator_length')),
																Config::Get('picture_max_width_size'),
																Config::Get('plugin.picalbums.picture_max_height_size'),
																Config::Get('plugin.picalbums.miniature_block_resize_width_value'),
																Config::Get('plugin.picalbums.miniature_block_resize_height_value'),
																true,
																$aParams,
																null,
																Config::Get('plugin.picalbums.miniature_crop'),
																Config::Get('plugin.picalbums.miniature_crop_middle'))) 
				{
					$sFileBlockPath = $this->Image_GetWebPath($sFileImage);
					$arr = array();				
					$arr['filePath'] = $sFilePath;
					$arr['fileMiniaturePath'] = $sFileMiniaturePath;
					$arr['fileBlockPath'] = $sFileBlockPath;
					$arr['fileOriginalPath'] = $sFileOriginalPath;
					$arr['exif'] = $sExif;
					@unlink($sFileTmp);
					return $arr;
				}
			}
		}

		@unlink($sFileTmp);
		return false;
	}

    private function GetExif($sFile) {
		$exif = '';
		$exifData = @exif_read_data ($sFile,0,true);
		if($exifData && is_array($exifData)) {
			foreach ($exifData as $name => $val) {

				if(is_array($val)) {
					foreach ($val as $namenew => $valnew) {
						$param = trim("$name.$namenew");
						if($param == 'COMPUTED.Height')
							$exif .= "Высота: " . $valnew . "<br/>";
						else if($param == 'COMPUTED.Width')
							$exif .= "Ширина: " . $valnew . "<br/>";
						else if($param == 'FILE.FileSize')
							$exif .= "Размер: " . $valnew . "<br/>";
						else if($param == 'FILE.MimeType')
							$exif .= "Тип файла: " . $valnew . "<br/>";
						else if($param == 'IFD0.DateTime')
							$exif .= "Дата создания: " . $valnew . "<br/>";
						else if($param == 'IFD0.Make')
							$exif .= "Производитель фотоаппарата: " . $valnew . "<br/>";
						else if($param == 'IFD0.Model')
							$exif .= "Модель фотоаппарата: " . $valnew . "<br/>";
						else if($param == 'IFD0.XResolution')
							$exif .= "Горизонтальное разрешение: " . $valnew . "<br/>";
						else if($param == 'IFD0.YResolution')
							$exif .= "Вертикальное разрешение: " . $valnew . "<br/>";
						else if($param == 'IFD0.ResolutionUnit')
							$exif .= "Единица измерения разрешения: " . $valnew . "<br/>";
						else if($param == 'IFD0.YCbCrPositioning')
							$exif .= "Порядок размещения компонент Y и C: " . $valnew . "<br/>";
						else if($param == 'COMPUTED.ApertureFNumber') {
							$valnew = str_replace("f", "F", $valnew);
							$valnew = str_replace("/", "", $valnew);
							$exif .= "Диафрагма: " . $valnew . "<br/>";
						}
						else if($param == 'EXIF.ExposureTime')
							$exif .= "Выдержка: " . $valnew . "<br/>";
						else if($param == 'EXIF.FNumber')
							$exif .= "Число диафрагмы: " . $valnew . "<br/>";
						else if($param == 'EXIF.CompressedBitsPerPixel')
							$exif .= "Глубина цвета после сжатия: " . $valnew . "<br/>";
						else if($param == 'EXIF.ExposureBiasValue')
							$exif .= "Компенсация экспозиции: " . $valnew . "<br/>";
						else if($param == 'EXIF.MaxApertureValue')
							$exif .= "Минимальное число диафрагмы: " . $valnew . "<br/>";
						else if($param == 'EXIF.FocalLength')
							$exif .= "Фокусное расстояние: " . $valnew . "<br/>";
						//else $this->Logger_Error("$name.$namenew = $valnew");
					}
				}
			}
		}
		return $exif;
	}

	private function SaveToStorage($sFileImage, $iUserId) {
		if(Config::Get('plugin.picalbums.amasons3_enable')) {

			$pos = strpos($sFileImage, Config::Get('path.uploads.root'));
			if(gettype($pos) == 'integer') {
				$sFileImage = Config::Get('path.root.server') . substr($sFileImage, $pos);

				if (!(!extension_loaded('curl')))
				{
					$s3 = new S3(Config::Get ( 'plugin.picalbums.awsAccessKey' ), Config::Get ( 'plugin.picalbums.awsSecretKey' ));

					$ext = strtolower ( array_pop ( explode ( ".", $sFileImage ) ) );
					$filename = func_generator(Config::Get ( 'plugin.picalbums.func_generator_length' )).'.'.$ext;

					$dir = 'picalbums/'.preg_replace('~(.{2})~U', "\\1/", str_pad($iUserId, 6, "0", STR_PAD_LEFT)).date('Y/m/d');
					$filename = $dir .'/'. $filename;

					if ($s3->putObjectFile($sFileImage, Config::Get ( 'plugin.picalbums.bucketName' ), $filename, S3::ACL_PUBLIC_READ)) {
						@unlink($sFileImage);
						$path = "http://" . Config::Get ( 'plugin.picalbums.bucketName' ) .  Config::Get ( 'plugin.picalbums.amason_suffix_url' ) . '/' . $filename;
						return $path;
					}
				}
			}

			return $sFileImage;
		} else if(Config::Get('plugin.picalbums.imageshack_enable')) {
			$pos = strpos($sFileImage, Config::Get('path.uploads.root'));
			if(gettype($pos) == 'integer') {
				$sFileImage = Config::Get('path.root.server') . substr($sFileImage, $pos);

				$uploader = new ImageShackUploader(Config::Get('plugin.imageshack.devkey'));

			    $response = $uploader->upload($sFileImage);
			    $response = object2array($response);

			    if($response) {
				    if($response['links']) {
				    	if($response['links']['image_link']) {
				    		@unlink($sFileImage);
							return $response['links']['image_link'];
				    	}
				    }
		    	}
			}
			return $sFileImage;
		}
	}
	
	// Эвент список альбомов пользователя
	protected function AlbumsListingEvent() {
		// Получение имени пользователя в чей профайл зашли
		$sUserLogin=$this->sCurrentEvent;		
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if(($oUser == null)) {
			return Router::Action('404'); 
		}
		
		$iPage = 0;
		if($this->GetParam(1))
			$iPage = $this->GetParam(1);
		
		// Получение альбомов пользователя 
		$aAlbums = $this->PluginPicalbums_Album_GetAlbumsByUserId($oUser->getId());
		
		// Ваш ли это профиль
		$bIsCanModify = false;		
		if($this->oUserCurrent) {
			if($this->oUserCurrent->getId() == $oUser->getId() || $this->oUserCurrent->isAdministrator()) {
				$bIsCanModify = true;
			}		
		}
		
		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.albums_listing_page_cnt' ) );
		$this->Viewer_Assign ( 'iAlbCnt', Config::Get ( 'plugin.picalbums.albums_listing_page_cnt' ) );
		
		$this->Viewer_Assign ( 'aAlbums', $aAlbums );
		$this->Viewer_Assign ( 'bIsCanModify', $bIsCanModify );
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_albumshow_albums').' '.$oUser->getLogin()));
		
		$this->SetTemplateAction ( 'albumslisting' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );
		}
		else {		
			$this->Viewer_Assign ( 'isPjax', true );
		}	
	}
	
	// Список альбовом друзей с историей
	protected function AlbumsFriendListingEvent() {
		// Получение имени пользователя в чей профайл зашли
		$sUserLogin=$this->sCurrentEvent;		
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if(($oUser == null)) {
			return Router::Action('404'); 
		}
		// Недоступно для неавторизированных
		if (! $this->User_IsAuthorization ()) {
			return Router::Action('404'); 
		}
		// Смотреть можно только свою фредленту		
		if(!($this->oUserCurrent->isAdministrator()))
			if($this->oUserCurrent->getId() != $oUser->getId() ) {
				return Router::Action('404'); 
			}
			
		$iPage = 0;
		if($this->GetParam(1))
			$iPage = $this->GetParam(1); 
			
		// Получаем списко друзей
		$aUsersFriend=$this->User_GetUsersFriend($oUser->getId());
		
		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.friendpage_page_cnt' ) );
		$this->Viewer_Assign ( 'iFrCnt', Config::Get ( 'plugin.picalbums.friendpage_page_cnt' ) );
		
		$this->Viewer_Assign ( 'aUsersFriend', $aUsersFriend );	
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_menu_profile_friends').' '.$oUser->getLogin()));
		
		$this->SetTemplateAction ( 'friendalbumslisting' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );			
		} else {
			$this->Viewer_Assign ( 'isPjax', true );
		}
	}
	
	// Создание альбома
	protected function AlbumCreateEvent() {
		// Только для авторизированных пользователей
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		
		// Получение имени пользователя в чей профайл зашли
		$sUserLogin=$this->sCurrentEvent;				
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if($oUser == null) {
			return Router::Action('404'); 
		}
		// Создание разрешено только в своем профиле
		if($this->oUserCurrent->getId () != $oUser->getId()) {
			return Router::Action('404'); 
		}
		
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_album_add')));		
		$this->SetTemplateAction ( 'albumcreate' );
	}
	
	// Демонтсрация альбома
	protected function AlbumShowEvent() {
		// Получение имени пользователя в чей профайл зашли
		$sUserLogin=$this->sCurrentEvent;				
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if($oUser == null) {
			return Router::Action('404'); 
		}
		
		// Получаем обьект альбома, в который зашли
		$sAlbumURL=$this->GetParam(0);
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumByURL($oUser->getId(), $sAlbumURL);
		
		// Если альбом не найден
		if(!$oAlbum) {
			return Router::Action('404'); 
		}
		
		//Фотки добавлять можно только автору
		$bIsCanAppend = false;
		
		if($this->oUserCurrent) {
			if((($this->oUserCurrent->getId () == $oUser->getId())&&($this->oUserCurrent->getRating() >= Config::Get ( 'plugin.picalbums.minimal_rating_for_append_picture' ))) || ( $this->oUserCurrent->isAdministrator())) {
				$bIsCanAppend = true;
			}
		}

        if(!$oAlbum->GetVisibilityForUser($this->oUserCurrent))
            return Router::Action('404');
		
		// Получаем массив картинок в данном альбоме
		$aPictures = $this->PluginPicalbums_Picture_GetPictureByAlbumId($oAlbum->getId());
		
		$iPage = 0;
		if($this->GetParam(2))
			$iPage = $this->GetParam(2); 
		
		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.albumshow_page_cnt' ) );
		$this->Viewer_Assign ( 'iPicCnt', Config::Get ( 'plugin.picalbums.albumshow_page_cnt' ) );
		
		$this->Viewer_Assign ( 'oAlbum', $oAlbum );	
		$this->Viewer_Assign ( 'aPictures', $aPictures );	
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		$this->Viewer_Assign ( 'bIsCanAppend', $bIsCanAppend );
		
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_albumshow_albums').' '.$oUser->getLogin()));
		$this->Viewer_AddHtmlTitle(htmlspecialchars($oAlbum->getTitle()));

        $this->Lang_AddLangJs(array(
			'picalbums_swf_upload_done','picalbums_swf_do_uploading','picalbums_swf_pending','picalbums_swf_file',
            'picalbums_ajaxuploader_from', 'picalbums_ajaxuploader_cancel', 'picalbums_ajaxuploader_failed',
		));

		$this->SetTemplateAction ( 'albumshow' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->SetTemplateAction ( 'albumshow' );
		} else {
			$this->Viewer_Assign ( 'sAlbumPathStart', $oUser->getUserAlbumsWebPath() );
			$this->SetTemplateAction ( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes/albumshow_pjax' );
		}
	}
	
	// Редактирование альбома
	protected function AlbumEditEvent() {
		// Только для авториизированных
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		
		$sUserLogin=$this->sCurrentEvent;		
		$sAlbumURL=$this->GetParam(0);
		
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if(($oUser == null) || ($this->GetParam(1) != 'edit')) {
			return Router::Action('404'); 
		}
		
		// Редактировать можно только свои альбомы		
		if( ($this->oUserCurrent->getId () != $oUser->getId()) &&(! $this->oUserCurrent->isAdministrator()) ) {
			return Router::Action('404'); 
		}
		
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumByURL($oUser->getId(), $sAlbumURL);
		
		if(!$oAlbum) {
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
		$this->Viewer_Assign ( 'oUserProfile', $oUser );	
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_album_edit')));				
		$this->SetTemplateAction ( 'albumedit' );
	}
	
	// Массовое редактирование изображений
	protected function PicturesEditEvent() {
		// Только для авторизированных пользователей
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		
		$sUserLogin=$this->sCurrentEvent;		
		$sAlbumURL=$this->GetParam(0);
		
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if(($oUser == null) || ($this->GetParam(1) != 'picturesedit')) {
			return Router::Action('404'); 
		}
		
		// Редактировать можно только свои альбомы		
		if( ($this->oUserCurrent->getId () != $oUser->getId()) &&(! $this->oUserCurrent->isAdministrator()) ) {
			return Router::Action('404'); 
		}
		
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumByURL($oUser->getId(), $sAlbumURL);
		if(!$oAlbum) {
			return Router::Action('404'); 
		}
		
		$this->Viewer_Assign ( 'oAlbum', $oAlbum );	
		$this->Viewer_Assign ( 'aPictures', $oAlbum->GetPictures() );
		$this->Viewer_Assign ( 'oUserProfile', $oUser );	
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_pictures_edit')));	
		$this->SetTemplateAction ( 'picturesedit' );
	}	
		
	// Показ картинки
	protected function PictureShowEvent() {
		$sUserLogin=$this->sCurrentEvent;		
		$sAlbumURL=$this->GetParam(0);
		$sPictureURL=$this->GetParam(1);
		
		// Получание пользователя
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if($oUser == null) {
			return Router::Action('404'); 
		}
		// Получение альбома
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumByURL($oUser->getId(), $sAlbumURL);
		
		if(!$oAlbum) {
			return Router::Action('404'); 
		}
		
		// Получение картинки
		$oPicture = $this->PluginPicalbums_Picture_GetPictureByURL($oAlbum->getId(), $sPictureURL);
		if(!$oPicture) {
			return Router::Action('404'); 
		}
		
		// Получание следующей и предыдущей картинки
		$oNextPrev = $this->PluginPicalbums_Picture_GetNextPrev($oAlbum->getId(), $sPictureURL);
		if($oNextPrev == null) {
			$sNextURL = null;
			$sPrevURL = null;
		} else {
			$sNextURL = $oNextPrev['next'];
			$sPrevURL = $oNextPrev['prev'];
		}
		
		// Получение номера текущей фотографии и послдеей 
		$aCurrLastPos = $this->PluginPicalbums_Picture_GetCurrentAndLastPosition($oAlbum->getId(), $sPictureURL);
		if($aCurrLastPos == null) {
			$iCurrentPos = null;
			$iLastPos = null;
		} else {
			$iCurrentPos = $aCurrLastPos['current'];
			$iLastPos = $aCurrLastPos['last'];
		}
		
		if($this->oUserCurrent) {
			$bIsHeart = $this->PluginPicalbums_Heart_isUserVotedByTarget($this->oUserCurrent->getId(), $oPicture->getId());
			$bNoteActivate = true;
		} else { 
			$bIsHeart = false;
			$bNoteActivate = false;
		}

        if(!$oAlbum->GetVisibilityForUser($this->oUserCurrent))
            return Router::Action('404');

		// Получение информации о сердечках
		$aUsersHearted = $this->PluginPicalbums_Heart_GetUsersHeartedLimitByTargetId($oPicture->getId(), 6);
		$iHeartCount = $this->PluginPicalbums_Heart_GetUsersHeartedCountByTargetId($oPicture->getId());

		// Получение списка помеченных пользователей
		$aUsersMarked = $this->PluginPicalbums_Note_GetUsersMarkedByPictureId($oPicture->getId());
		
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
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		$this->Viewer_Assign ( 'sNextURL', $sNextURL );
		$this->Viewer_Assign ( 'sPrevURL', $sPrevURL );
		$this->Viewer_Assign ( 'iCurrentPos', $iCurrentPos );
		$this->Viewer_Assign ( 'iLastPos', $iLastPos );
		$this->Viewer_Assign ( 'bNoteActivate', $bNoteActivate );
		$this->Viewer_Assign ( 'bIsHeart', $bIsHeart );

		$this->Viewer_Assign ( 'aUsersHearted', $aUsersHearted );
		$this->Viewer_Assign ( 'iHeartCount', $iHeartCount );

		$this->Viewer_Assign ( 'aUserMarked', $aUsersMarked );
		$this->Viewer_Assign ( 'iNonConfirmMark', $iNonConfirmMark );

        $this->Viewer_Assign ( 'aAllPictures', $aAllPictures );
		
        $aComments = $this->Comment_GetCommentsByTargetId($oPicture->getId(), 'picalbums');
        $aComments = $aComments['comments'];
        $this->Viewer_Assign ( 'aComments', $aComments );

        if($this->oUserCurrent && Config::Get ('plugin.picalbums.functional_copy_picture_ebable') && $oAlbum->getUserId() != $this->oUserCurrent->getId()) {
            $aCurrentUserAlbums = $this->PluginPicalbums_Album_GetAlbumsByUserId($this->oUserCurrent->getId());
            $this->Viewer_Assign ( 'aCurrentUserAlbums', $aCurrentUserAlbums );
        }
		
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_albumshow_albums').' '.$oUser->getLogin()));
		$this->Viewer_AddHtmlTitle(htmlspecialchars($oAlbum->getTitle()));
		$this->Viewer_AddHtmlTitle(htmlspecialchars($oPicture->getDescription()));

        $this->Lang_AddLangJs(array('panel_b', 'panel_i', 'panel_u', 'panel_s', 'panel_url', 'panel_url_promt', 'panel_code',
                                   'panel_video', 'panel_image', 'panel_cut', 'panel_quote', 'panel_list', 'panel_list_ul',
                                   'panel_list_ol', 'panel_title', 'panel_clear_tags', 'panel_video_promt', 'panel_list_li',
                                   'panel_image_promt', 'panel_user', 'panel_user_promt'));

		if (!isset($_SERVER['HTTP_X_PJAX'])) 
			$this->Viewer_Assign ( 'isPjax', false );
		else 	
			$this->Viewer_Assign ( 'isPjax', true );
				
		$this->SetTemplateAction ( 'picturelisting' );
	}
	
	// Загрузка картинок аджаксом
	protected function AlaxEventUploadImageAjax() {
        require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/ajaxupload.php');

		$this->Viewer_SetResponseAjax('json');
		// Только авторизированным
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		
		// Загрузка во временную диреуторию
		$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
		$sizeLimit = Config::Get('plugin.picalbums.ajax_max_size_upload_file');
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload(Config::Get('path.root.server') .'/uploads/');
		
		$error = null;
		$filename = null;
		foreach($result as $key => $value)
		{
		     if($key == 'error')
		     	$error = $result['error'];
		     else if($key == 'filename') {
		     	$filename = $result['filename'];
		     }
		} 
		
		if($error) {
			$this->Message_AddErrorSingle($result['error'], $this->Lang_Get('error'));
			return;
		}
		
		if(!$filename) {
			$this->Message_AddErrorSingle($this->Lang_Get('uploadimg_file_error'),$this->Lang_Get('error'));
			return;
		}

		$iUserId = $this->oUserCurrent->getId ();		
		
		$sFileTmp=$filename;		
		$sDirUpload=$this->GetIdDir($iUserId);
		$aParams=$this->BuildParams();
		
		$exif = '';
		if(Config::Get('plugin.picalbums.exif_enable')) {
			$exif = $this->GetExif($sFileTmp);
		}

		// Сохранять ли оригинал
		if(!Config::Get ( 'plugin.picalbums.is_save_original' ))
			$sFileOriginalPath = null;
		else {
			if ($sFileImage=$this->Image_ResizeAdditional(	$sFileTmp,
														$sDirUpload,
														func_generator(Config::Get('plugin.picalbums.func_generator_length')),
														0, 0, null, null, true, null)) 
				$sFileOriginalPath = $this->Image_GetWebPath($sFileImage);
			else
				$sFileOriginalPath =null;
		}
		
		// Сохраняем все расайзы
		if ($sFileImage=$this->Image_ResizeAdditional($sFileTmp,
													  $sDirUpload,
													  func_generator(Config::Get('plugin.picalbums.func_generator_length')),
													  Config::Get('plugin.picalbums.picture_max_width_size'),
													  Config::Get('plugin.picalbums.picture_max_height_size'),
													  Config::Get('plugin.picalbums.picture_resize_width_value'),
													  Config::Get('plugin.picalbums.picture_resize_height_value'),
													  true,
													  $aParams)) 
		{
			$sFilePath = $this->Image_GetWebPath($sFileImage);
			
			if ($sFileImage=$this->Image_ResizeAdditional(
												$sFileTmp,
												$sDirUpload,
												func_generator(Config::Get('plugin.picalbums.func_generator_length')),
												Config::Get('plugin.picalbums.picture_max_width_size'),
												Config::Get('plugin.picalbums.picture_max_height_size'),
												Config::Get('plugin.picalbums.miniature_resize_width_value'),
												Config::Get('plugin.picalbums.miniature_resize_height_value'),
												true,
												$aParams,
												null, 
												Config::Get('plugin.picalbums.miniature_crop'),
												Config::Get('plugin.picalbums.miniature_crop_middle'))) 
			{
				$fileMiniaturePath = $this->Image_GetWebPath($sFileImage);
				
				if ($sFileImage=$this->Image_ResizeAdditional(
													$sFileTmp,
													$sDirUpload,
													func_generator(Config::Get('plugin.picalbums.func_generator_length')),
													Config::Get('picture_max_width_size'),
													Config::Get('plugin.picalbums.picture_max_height_size'),
													Config::Get('plugin.picalbums.miniature_block_resize_width_value'),
													Config::Get('plugin.picalbums.miniature_block_resize_height_value'),
													true,
													$aParams,
													null,
													Config::Get('plugin.picalbums.miniature_crop'),
													Config::Get('plugin.picalbums.miniature_crop_middle'))) 
				{
					$fileBlockPath = $this->Image_GetWebPath($sFileImage);
					$arr['filePath'] = $sFilePath;
					$arr['fileMiniaturePath'] = $fileMiniaturePath;
					$arr['fileBlockPath'] = $fileBlockPath;
					$arr['fileOriginalPath'] = $sFileOriginalPath;
				}
				@unlink($sFileTmp);
			}
			@unlink($sFileTmp);
		}
		@unlink($sFileTmp);
		
		if(count($arr) == 0) {
			$this->Message_AddErrorSingle($this->Lang_Get ( 'picalbums_error_upload' ), $this->Lang_Get('error'));
			return;
		}
		
		if((Config::Get('plugin.picalbums.amasons3_enable')) || (Config::Get('plugin.picalbums.imageshack_enable'))) {		
			if((Config::Get('plugin.picalbums.imageshack_enable'))) {
                require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/imageshack.class.php');
                require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/multipost.class.php');
            } else {
                require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/s3.php');
            }
            $this->Viewer_AssignAjax('sFilePictureUpload',$this->SaveToStorage($arr['filePath'], $iUserId));
			$this->Viewer_AssignAjax('sFileMiniatureUpload',$this->SaveToStorage($arr['fileMiniaturePath'], $iUserId));
			$this->Viewer_AssignAjax('sFileBlockUpload',$this->SaveToStorage($arr['fileBlockPath'], $iUserId));
			$this->Viewer_AssignAjax('sFileOriginalUpload',$this->SaveToStorage($arr['fileOriginalPath'], $iUserId));
		} else {
			$this->Viewer_AssignAjax('sFilePictureUpload',$arr['filePath']);		
			$this->Viewer_AssignAjax('sFileMiniatureUpload',$arr['fileMiniaturePath']);
			$this->Viewer_AssignAjax('sFileBlockUpload',$arr['fileBlockPath']);
			$this->Viewer_AssignAjax('sFileOriginalUpload',$arr['fileOriginalPath']);
		}
		$this->Viewer_AssignAjax('exif', $exif);
		$this->Viewer_AssignAjax('success',true);
	}
	
	// Загрузка через flash и обычный загрузчик
	protected function AlaxEventUploadImageFlash() {
		
		// В зависимости от типа загрузчика устанавливается тип ответа
		if (getRequest('is_iframe')) {
			$this->Viewer_SetResponseAjax('jsonIframe', false);
		} else {
			$this->Viewer_SetResponseAjax('json');
		}		
		
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		
		if (!isset($_FILES['Filedata']['tmp_name'])) {
			$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
			return false;
		}
		
		$aForm=getRequest('value',null,'post');
		$sFile=null;
		// Загрузка картинки, деланье ресайзов
		if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			if(!$sFile=$this->UploadAlbumImageFile($_FILES['Filedata'],$this->oUserCurrent->getId ())) {
				$this->Message_AddErrorSingle($this->Lang_Get('uploadimg_file_error'),$this->Lang_Get('error'));
				return;
			}
		}

		if($sFile == null) {
			$this->Message_AddErrorSingle($this->Lang_Get ( 'picalbums_file_not_select' ), $this->Lang_Get('error'));
			return;
		}
		
		$iUserId = $this->oUserCurrent->getId ();
		
		if((Config::Get('plugin.picalbums.amasons3_enable')) || (Config::Get('plugin.picalbums.imageshack_enable'))) {
			if((Config::Get('plugin.picalbums.imageshack_enable'))) {
                require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/imageshack.class.php');
                require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/multipost.class.php');
            } else {
                require_once(Config::Get('path.root.server').'/plugins/picalbums/include/lib/s3.php');
            }
            $this->Viewer_AssignAjax('sFilePictureUpload',$this->SaveToStorage($sFile['filePath'], $iUserId));
			$this->Viewer_AssignAjax('sFileMiniatureUpload',$this->SaveToStorage($sFile['fileMiniaturePath'], $iUserId));
			$this->Viewer_AssignAjax('sFileBlockUpload',$this->SaveToStorage($sFile['fileBlockPath'], $iUserId));
			$this->Viewer_AssignAjax('sFileOriginalUpload',$this->SaveToStorage($sFile['fileOriginalPath'], $iUserId));
		} else {
			$this->Viewer_AssignAjax('sFilePictureUpload',$sFile['filePath']);		
			$this->Viewer_AssignAjax('sFileMiniatureUpload',$sFile['fileMiniaturePath']);
			$this->Viewer_AssignAjax('sFileBlockUpload',$sFile['fileBlockPath']);
			$this->Viewer_AssignAjax('sFileOriginalUpload',$sFile['fileOriginalPath']);
		}
		
		$this->Viewer_AssignAjax('exif',$sFile['exif']);
	}
	
	protected function AjaxAppendAlbum() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		if(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) == getRequest ( 'album_add_user_target_id' )) {
			$iUserId = getRequest ( 'album_add_user_target_id' );
		} else {
			if (! ($oUser = $this->User_GetUserById ( getRequest ( 'album_add_user_target_id' ) ))) {		
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
				return;
			}
			$iUserId = $oUser->getId();
		}
		
		// Получаем идентификатор текущего авторизированного пользователя, того, который размещает запись
		if (! $this->oUserCurrent->isAdministrator())	{			
			if ((Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) != getRequest ( 'album_add_user_target_id' )) && 
					($iUserId != $this->oUserCurrent->getId ())) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_create_album_inprofile' ), $this->Lang_Get ( 'error' ) );
				return;
			}
			
			if($this->PluginPicalbums_Blacklist_isUserBlocked($iUserId) >= 1) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_user_is_blocked' ), $this->Lang_Get ( 'error' ) );
				return;
			}
			
			if(Config::Get ( 'plugin.picalbums.rating_create_album_minimal_activate' )) {
				$iAlbumCount = $this->oUserCurrent->getPicalbumsCount(2);
				$iUserRating = $this->oUserCurrent->getRating();
				$bCanAppendAlbum = true;
				if($iUserRating < Config::Get ( 'plugin.picalbums.rating_create_album_minimal_1' )) {
					if($iAlbumCount >= Config::Get ( 'plugin.picalbums.create_album_count_less_1' ))
						$bCanAppendAlbum = false;
				} 
				else if(($iUserRating >= Config::Get ( 'plugin.picalbums.rating_create_album_minimal_1' )) && ($iUserRating < Config::Get ( 'plugin.picalbums.rating_create_album_minimal_2' ))) {
					if($iAlbumCount >= Config::Get ( 'plugin.picalbums.create_album_count_1_2' ))
						$bCanAppendAlbum = false;
				}
				else if(($iUserRating >= Config::Get ( 'plugin.picalbums.rating_create_album_minimal_2' )) && ($iUserRating < Config::Get ( 'plugin.picalbums.rating_create_album_minimal_3' ))) {
					if($iAlbumCount >= Config::Get ( 'plugin.picalbums.create_album_count_2_3' ))
						$bCanAppendAlbum = false;
				}
				else if(($iUserRating >= Config::Get ( 'plugin.picalbums.rating_create_album_minimal_3' )) && ($iUserRating < Config::Get ( 'plugin.picalbums.rating_create_album_minimal_4' ))) {
					if($iAlbumCount >= Config::Get ( 'plugin.picalbums.create_album_count_3_4' ))
						$bCanAppendAlbum = false;
				}
				else if(($iUserRating >= Config::Get ( 'plugin.picalbums.rating_create_album_minimal_4' ))) {
					if($iAlbumCount >= Config::Get ( 'plugin.picalbums.create_album_count_4_more' ))
						$bCanAppendAlbum = false;
				}
				
				if(!$bCanAppendAlbum) {
					$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_small_rating_to_create_album' ), $this->Lang_Get ( 'error' ) );
					return;
				}
			}
		}

		$iTextLength = mb_strlen(getRequest ( 'album_title_text' ), 'UTF-8');
		// Проверяется корректность заполнения текста
		$sTitle = $this->TextParser(getRequest ( 'album_title_text' ), false) ;	
		if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_title_max_characters' ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_title_length_error_start' ).Config::Get ( 'plugin.picalbums.text_title_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$iTextLength = mb_strlen(getRequest ( 'album_description_text' ), 'UTF-8');
		// Проверяется корректность заполнения текста
		$sDescription = $this->TextParser(getRequest ( 'album_description_text' ), false) ;	
		if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_form_max_characters' ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_desc_length_error_start' ).Config::Get ( 'plugin.picalbums.text_form_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на дубликаты
		$sUrl = func_translit($sTitle);
		if($this->PluginPicalbums_Album_GetAlbumByURL($iUserId, $sUrl) != false) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_album_name_exist' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		// Выделение ссылки на других пользователей
		$sDescription = $this->UserNameLinkDetect($sDescription);

        $sTags=getRequest('album_tags');
		$aTags=explode(',',$sTags);
		$aTagsNew=array();
		$aTagsNewLow=array();
		foreach ($aTags as $sTag) {
			$sTag=trim($sTag);

            $iLength = mb_strlen($sTag, 'UTF-8');
            $sTag = $this->TextParser($sTag, false);
            if(($iLength >= 2) && ($iLength < Config::Get ( 'plugin.picalbums.text_tag_max_characters' ))) {
                $aTagsNew[]=$sTag;
				$aTagsNewLow[]=mb_strtolower($sTag,'UTF-8');
            }
		}
		
		// Формируем альбом
		$oAlbumNew = Engine::GetEntity ( 'PluginPicalbums_Album' );
		$oAlbumNew->setUserId ( $iUserId );
		$oAlbumNew->setTitle ( $sTitle );
		$oAlbumNew->setURL ( $sUrl );
		$oAlbumNew->setDescription ( $sDescription );
		$oAlbumNew->setVisibility ( getRequest ( 'album_visibility' ) );
		$oAlbumNew->setDateAdd ( date ( "Y-m-d H:i:s" ) );
		if(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) == getRequest ( 'album_add_user_target_id' )) {
			$oAlbumNew->setAddUserId ( $this->oUserCurrent->getId () );
			$oAlbumNew->setCategoryId ( getRequest ( 'album_category_id' ) );
		} else {
			$oAlbumNew->setAddUserId ( NULL );
			$oAlbumNew->setCategoryId ( NULL );
		}
        $oAlbumNew->setNeedModer ( getRequest ( 'album_need_moder' ) );
		
		// Добавляем альбом в профиль
		if (($iAlbumId = $this->PluginPicalbums_Album_AddAlbum ( $oAlbumNew ))) {
            if (count($aTagsNew)) {
                foreach ($aTagsNewLow as $sTag) {
                    $oTagNew = Engine::GetEntity ( 'PluginPicalbums_Tag' );
                    $oTagNew->setTargetId ( $iAlbumId );
                    $oTagNew->setText ( $sTag );
                    $this->PluginPicalbums_Tag_AddTag ( $oTagNew );
                }
            }

			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_album_append' ), $this->Lang_Get ( 'attention' ) );
			
			if(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) == $iUserId) {
				$this->Stream_Write($this->oUserCurrent->getId (), 'add_album', $iAlbumId);
			} else {				
				$this->Stream_Write($iUserId, 'add_album', $iAlbumId);
			}
			
			$this->Viewer_AssignAjax('albumulr',$sUrl);
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
		}	
	}	
	
	// Удаление альбома из профайл пользователя
	protected function AjaxRemoveAlbum() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		// Проверка на существование альбома
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( getRequest ( 'album_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Получаем идентификатор текущего авторизированного пользователя, того, который размещает запись		
		$iCurrentUserId = $this->oUserCurrent->getId ();
		if (! $this->oUserCurrent->isAdministrator())	{
		
			if(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) != $oAlbum->getUserId()) {
				if($oAlbum->getUserId() != $iCurrentUserId) {
					$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_delete_album_inprofile' ), $this->Lang_Get ( 'error' ) );
					return;
				}
			} else {
				if($oAlbum->getAddUserId() != $iCurrentUserId) {
					$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_main_albums_delete_norights' ), $this->Lang_Get ( 'error' ) );
					return;
				}
			}
			
			if($this->PluginPicalbums_Blacklist_isUserBlocked($iCurrentUserId) >= 1) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_user_is_blocked' ), $this->Lang_Get ( 'error' ) );
				return;
			}
		}

		// Удаление альбома
		if ($this->PluginPicalbums_Album_DeleteAlbum ( getRequest ( 'album_target_id' ) ) == true) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_album_delete' ), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}

	// Удаление картинки из альбома
	protected function AjaxRemovePicture() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById ( getRequest ( 'picture_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование альбома
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Получаем идентификатор текущего авторизированного пользователя, того, который размещает запись
		$iCurrentUserId = $this->oUserCurrent->getId ();
		if($oAlbum->getUserId() != $iCurrentUserId) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_delete_picture_inprofile' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Удаление картинки
		if ($this->PluginPicalbums_Picture_DeletePicture ( getRequest ( 'picture_target_id' ) ) == true) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_picture_delete' ), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}
	

	// Редактирование картинки из альбома
	protected function AjaxEditPicture() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById ( getRequest ( 'picture_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование альбома
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Получаем идентификатор текущего авторизированного пользователя, того, который размещает запись
		$iCurrentUserId = $this->oUserCurrent->getId ();
		if($oAlbum->getUserId() != $iCurrentUserId) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_edit_picture_inprofile' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$sTitle = getRequest ( 'picture_description' );		
		$iTextLength = mb_strlen($sTitle, 'UTF-8');
		
		// Проверяется корректность заполнения текста
		$sTitle = $this->TextParser($sTitle, false) ;	
		if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_title_max_characters' ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_desc_length_error_start' ).Config::Get ( 'plugin.picalbums.text_title_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Редактирваоть не нужно
		if($oPicture->getDescription() == $sTitle) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_no_edit' ), $this->Lang_Get ( 'attention' ) );
			return;
		}
		
		// Проверка на дубликаты
		$sUrl = func_translit($sTitle);
		if($this->PluginPicalbums_Picture_GetPictureByURL($oAlbum->getId(), $sUrl) != false) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_picture_name_exist' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		// редактирование картинки
		$iResId = $this->PluginPicalbums_Picture_EditPicture ( getRequest ( 'picture_target_id' ), $sTitle, $sUrl );
		$this->PluginPicalbums_Picture_ClearCache ( $oAlbum->getId() );
		
		if (($iResId == true) || ($iResId == null)) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_picture_edit' ), $this->Lang_Get ( 'attention' ) );
			
			$aResult=array();
			$oUser = $this->User_GetUserById($iCurrentUserId );
			if($oUser == null)
				$profileloginurl = '';
			else
				$profileloginurl = $oUser->getUserWebPath();
			$aResult[]=array(
					'profileloginurl' => $profileloginurl,
					'albumulr' => $oAlbum->getURL(),
					'title' => $sTitle,
					'url' => $sUrl,
				);
			
			$this->Viewer_AssignAjax('aResult',$aResult);
			
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}
	
	// Редактирование альбома
	protected function AjaxEditAlbum() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		// Проверка на существование альбома
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( getRequest ( 'album_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Получаем идентификатор текущего авторизированного пользователя, того, который размещает запись
		$iCurrentUserId = $this->oUserCurrent->getId ();
		if (! $this->oUserCurrent->isAdministrator())	{
		
			if(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) != $oAlbum->getUserId()) {		
				if($oAlbum->getUserId() != $iCurrentUserId) {
					$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_edit_album_inprofile' ), $this->Lang_Get ( 'error' ) );
					return;
				}
			} else {
				if($oAlbum->getAddUserId() != $iCurrentUserId) {
					$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_edit_album_inprofile' ), $this->Lang_Get ( 'error' ) );
					return;
				}
			}
			
			if($this->PluginPicalbums_Blacklist_isUserBlocked($iCurrentUserId) >= 1) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_user_is_blocked' ), $this->Lang_Get ( 'error' ) );
				return;
			}			
		}
		$iTextLength = mb_strlen(getRequest ( 'album_title' ), 'UTF-8');
		// Проверяется корректность заполнения текста
		$sTitle = $this->TextParser(getRequest ( 'album_title' ), false) ;	
		if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_title_max_characters' ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_title_length_error_start' ).Config::Get ( 'plugin.picalbums.text_title_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$iTextLength = mb_strlen(getRequest ( 'album_description' ), 'UTF-8');
		// Проверяется корректность заполнения текста
		$sDescription = $this->TextParser(getRequest ( 'album_description' ), false) ;	
		if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_form_max_characters' ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_desc_length_error_start' ).Config::Get ( 'plugin.picalbums.text_form_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
			return;
		}		
		// Выделение ссылки на других пользователей
		$sDescription = $this->UserNameLinkDetect($sDescription);
		
		$sUrl = func_translit($sTitle);
		if($oAlbum->getUrl() != $sUrl) {
			if($this->PluginPicalbums_Album_GetAlbumByURL($oAlbum->getUserId(), $sUrl) != false) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_album_name_exist' ), $this->Lang_Get ( 'error' ) );
				return;
			}
		}
		// Удаляем настройки истории френд ленты
		$this->PluginPicalbums_Related_DeleteRelatedByTargetId ( getRequest ( 'album_target_id' ) );

		if(Config::Get ( 'plugin.picalbums.url_rename_after_edit' ) == false)
			$sUrl = $oAlbum->getUrl();
		
		// редактирование альбома
		$iResId = $this->PluginPicalbums_Album_EditAlbum ( getRequest ( 'album_target_id' ),
                                                           $sTitle,
                                                           $sDescription,
                                                           $sUrl,
                                                           getRequest ( 'album_visivility' ),
                                                           getRequest ( 'album_category_id' ),
                                                           getRequest ( 'album_need_moder' ) );
        $sTags=getRequest('album_tags');
		$aTags=explode(',',$sTags);
		$aTagsNew=array();
		$aTagsNewLow=array();
		foreach ($aTags as $sTag) {
			$sTag=trim($sTag);

            $iLength = mb_strlen($sTag, 'UTF-8');
            $sTag = $this->TextParser($sTag, false);
            if(($iLength >= 2) && ($iLength < Config::Get ( 'plugin.picalbums.text_tag_max_characters' ))) {
                $aTagsNew[]=$sTag;
				$aTagsNewLow[]=mb_strtolower($sTag,'UTF-8');
            }
		}
        $this->PluginPicalbums_Tag_DeleteTagsByTargetId ( getRequest ( 'album_target_id' ) );
        if (count($aTagsNew)) {
            foreach ($aTagsNewLow as $sTag) {
                $oTagNew = Engine::GetEntity ( 'PluginPicalbums_Tag' );
                $oTagNew->setTargetId ( getRequest ( 'album_target_id' ) );
                $oTagNew->setText ( $sTag );
                $this->PluginPicalbums_Tag_AddTag ( $oTagNew );
            }
        }

		if (($iResId == true) || ($iResId == null)) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_album_edit' ), $this->Lang_Get ( 'attention' ) );
			$this->Viewer_AssignAjax('albumulr',$sUrl);
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}
	
	// Редактирование всех фотографий альбома
	protected function AjaxEditPictures() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		$array = getRequest ( 'pictures_array' );
		
		// Проверка на существование альбома
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( getRequest ( 'album_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		// Получение картинок альбома
		$aPictures = $oAlbum->getAllPictures();
		if(!$aPictures){
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_album_has_no_pictures' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$iCurrentUserId = $this->oUserCurrent->getId ();
		
		$ok = true;
		foreach($aPictures as $aPicture) {
			$iPictureId = $aPicture->getId();
			// Получаем идентификатор текущего авторизированного пользователя, того, который размещает запись
			if (! $this->oUserCurrent->isAdministrator())	{
			
				if(Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) != $oAlbum->getUserId()) {
					if($oAlbum->getUserId() != $iCurrentUserId) {
						$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_edit_pictures_inprofile' ), $this->Lang_Get ( 'error' ) );
						return;
					}
				}
				
				if($this->PluginPicalbums_Blacklist_isUserBlocked($iCurrentUserId) >= 1) {
					$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_user_is_blocked' ), $this->Lang_Get ( 'error' ) );
					return;
				}
			}
			
			// Установка новой обложки
			if(array_key_exists('picture_cover_' . $iPictureId, $array) == true) {
				$this->PluginPicalbums_Album_UpdateCoverPicture($oAlbum->getId(), $iPictureId);
			}
			
			// Удаляем картинку
			if(array_key_exists('picture_delete_' . $iPictureId, $array) == true) {
				$this->PluginPicalbums_Picture_DeletePicture ( $iPictureId );
				continue;
			}
			
			// Получаем текст для редактировани
			if(!array_key_exists('picture_description_text_' . $iPictureId, $array)) {
				continue;
			}
			$sTitle = $array['picture_description_text_' . $iPictureId];			
			$iTextLength = mb_strlen($sTitle, 'UTF-8');
			
			// Проверяется корректность заполнения текста
			$sTitle = $this->TextParser($sTitle,false) ;	
			if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_title_max_characters' ))) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_desc_length_error_start' ).Config::Get ( 'plugin.picalbums.text_title_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
				return;
			}
			$sTitle = $this->UserNameLinkDetect($sTitle);
			
			// Редактирваоть не нужно
			if($aPicture->getDescription() == $sTitle) {
				continue;
			}
			
			// Проверка на дубликаты
			$sUrl = func_translit($sTitle);
			if($this->PluginPicalbums_Picture_GetPictureByURL($oAlbum->getId(), $sUrl) != false) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_picture_must_have_different_names' ), $this->Lang_Get ( 'error' ) );
				return;
			}
			
			if(Config::Get ( 'plugin.picalbums.url_rename_after_edit' ) == false) {
				if(Config::Get ( 'plugin.picalbums.url_rename_picture_default_name' ) == false) {
					$sUrl = $aPicture->getUrl();
				}
				else {				
					if(mb_substr ( $aPicture->getDescription(), 0, mb_strlen($this->Lang_Get ( 'picalbums_picture_default_name' ), 'UTF-8'), 'UTF-8')
							!= $this->Lang_Get ( 'picalbums_picture_default_name' ))
						$sUrl = $aPicture->getUrl();
				}
			}
			
			
			// редактирование картинки
			$iResId = $this->PluginPicalbums_Picture_EditPicture ( $iPictureId, $sTitle, $sUrl );
			if (!(($iResId == true) || ($iResId == null))) {
				$ok = false;
			}
		}
		// Очистак кеша
		$this->PluginPicalbums_Picture_ClearCache ( $oAlbum->getId() );
		// Удаление зависимостей  истории френд ленты
		$this->PluginPicalbums_Related_DeleteRelatedByTargetId ( getRequest ( 'album_id' ) );
		
		if ($ok) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_pictures_edit' ), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}
	
	// Добавление кратинки, сюда уже приходят адресса файлов
	protected function AjaxAppendPicture() {
		if(!$this->AjaxAppendPictureMain()) {
			@unlink(getRequest ( 'picture_path_html' ));
			@unlink(getRequest ( 'picture_path_minimal_html' ));
			@unlink(getRequest ( 'picture_path_block_html' ));
			@unlink(getRequest ( 'picture_path_original_html' ));
		}
	}
	
	// Добавление картинки в альбом
	private function AjaxAppendPictureMain() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return false;
		}

		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( getRequest ( 'album_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		if (! (getRequest ( 'picture_path_html' ) )) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		if (! (getRequest ( 'picture_path_minimal_html' ) )) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		if (! (getRequest ( 'picture_path_block_html' ) )) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
				
		$iCurrentUserId = $this->oUserCurrent->getId ();
		
		if (! $this->oUserCurrent->isAdministrator())	{
			if( Config::Get ( 'plugin.picalbums.virtual_main_user_id' != $oAlbum->getUserId())) {
				if($oAlbum->getUserId() != $iCurrentUserId) {
					$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_append_pictures_inprofile' ), $this->Lang_Get ( 'error' ) );
					return false;
				}
			}
			
			if($this->PluginPicalbums_Blacklist_isUserBlocked($iCurrentUserId) >= 1) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_user_is_blocked' ), $this->Lang_Get ( 'error' ) );
				return false;
			}
			
			if($this->oUserCurrent->getRating() < Config::Get ( 'plugin.picalbums.minimal_rating_for_append_picture' )) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_small_rating_to_append_new_picture' ), $this->Lang_Get ( 'error' ) );
				return false;
			}
		
			$iPictureCnt = $oAlbum->GetPicturesCount();
			if($iPictureCnt >= Config::Get ( 'plugin.picalbums.max_picture_in_album' )) {
				$this->Viewer_AssignAjax('docanccel','do');
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_max_picture_count_exceed' ), $this->Lang_Get ( 'error' ) );
				return false;
			}
		
			$sDate=date("Y-m-d H:i:s",time() - Config::Get ( 'plugin.picalbums.picture_limit_time' ));
			$iLastPictureCnt = $this->PluginPicalbums_Picture_GetLastPictureCountByUserId($iCurrentUserId, $sDate);
						
			if($iLastPictureCnt > Config::Get ( 'plugin.picalbums.picture_count_limit_by_time' )){
				$this->Viewer_AssignAjax('docanccel','do');
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_max_speed_append_picture_exceed_start' ) . 
												Config::Get ( 'plugin.picalbums.picture_count_limit_by_time' ) .
												$this->Lang_Get ( 'picalbums_max_speed_append_picture_exceed_middle' ) . Config::Get ( 'plugin.picalbums.picture_limit_time' ) . $this->Lang_Get ( 'picalbums_max_speed_append_picture_exceed_end' ), $this->Lang_Get ( 'error' ) );
				return false;
			}
		}
		
		$sTitle = getRequest ( 'picture_description' );		
		$iTextLength = mb_strlen($sTitle, 'UTF-8');
		
		if($iTextLength == 0) {
			$aPicturesCount= $this->PluginPicalbums_Picture_GetLastPictureId() + 1;
			if(!$aPicturesCount) 
				$aPicturesCount = 1;
			
			$sTitle = $this->Lang_Get ( 'picalbums_picture_default_name' ) . $aPicturesCount;
			$iTextLength = 2;
		}
		
		// Проверяется корректность заполнения текста
		$sTitle = $this->TextParser($sTitle, false) ;	
		if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_title_max_characters' ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_desc_length_error_start' ).Config::Get ( 'plugin.picalbums.text_title_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
				
		// Проверка на дубликаты
		$sUrl = func_translit($sTitle);
		if($this->PluginPicalbums_Picture_GetPictureByURL($oAlbum->getId(), $sUrl) != false) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_picture_name_exist' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		$this->PluginPicalbums_Related_DeleteRelatedByTargetId ( getRequest ( 'album_target_id' ) );
		
		// Формируем картинку
		$oPictureNew = Engine::GetEntity ( 'PluginPicalbums_Picture' );
		$oPictureNew->setAlbumId ( $oAlbum->getId() );
		$oPictureNew->setDescription ( $sTitle );
		$oPictureNew->setURL ( $sUrl );
		$oPictureNew->setPicPath ( getRequest ( 'picture_path_html' ) );
		$oPictureNew->setMiniaturePath ( getRequest ( 'picture_path_minimal_html' ) );
		$oPictureNew->setBlockPath ( getRequest ( 'picture_path_block_html' ) );
		$oPictureNew->setOriginalPath ( getRequest ( 'picture_path_original_html' ) );
		$oPictureNew->setExif ( getRequest ( 'exif' ) );
		$oPictureNew->setDateAdd ( date ( "Y-m-d H:i:s" ) );
		$oPictureNew->setAddUserId ( $this->oUserCurrent->getId () );
        $bIsModer = 1;
        if($oAlbum->GetUserNeedBeModerated($this->oUserCurrent) == 1)
            $bIsModer = 0;
        $oPictureNew->setIsModer($bIsModer);
		
		// Добавляем картинку в альбом
		if (($iPictureId = $this->PluginPicalbums_Picture_AddPicture ( $oPictureNew ))) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_picture_append' ), $this->Lang_Get ( 'attention' ) );
			$oPictureNew->setId($iPictureId);
			
			// Формируем даныне для jquery templates
			$aResult=array();
			$oUser = $this->User_GetUserById($iCurrentUserId );
			
			if( Config::Get ( 'plugin.picalbums.virtual_main_user_id') == $oAlbum->getUserId()) {
				$sAlbumPathStart = Router::GetPath(Config::Get ( 'plugin.picalbums.main_albums_router_name'));
			} else {
				if($oUser == null)
					$sAlbumPathStart = '';
				else
					$sAlbumPathStart = $oUser->getUserAlbumsWebPath();
			}

            $this->Stream_Write($oUser->getId(), 'add_picture', $iPictureId);
			
			$oViewer=$this->Viewer_GetLocalViewer();
			$oViewer->Assign('sAlbumPathStart',$sAlbumPathStart); 
			$oViewer->Assign('oUserCurrent', $this->oUserCurrent); 
			$oViewer->Assign('oAlbum', $oAlbum);
			$oViewer->Assign('oPicture', $oPictureNew); 
			$aResNewPhoto=$oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes/picture_preview.tpl');
			
			$this->Viewer_AssignAjax('aResult',$aResNewPhoto);
			return true;
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}	
	}

	// Пагинация на странице альбомы друзей
	public function AjaxShowNextPictures() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return false;
		}

		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( getRequest ( 'album_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		$oUserOwner = $oAlbum->getUserOwner();
		if (!$oUserOwner) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		if (! getRequest ( 'start' ) ) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		if (! getRequest ( 'limit' ) ) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		$aPictures=$oAlbum->GetLimitPictures(getRequest ( 'start' ), getRequest ( 'limit' ));
		
		$sResPhotos="";
		foreach($aPictures as $oPicture) {
			$oViewer=$this->Viewer_GetLocalViewer();
			$oViewer->Assign('sAlbumPathStart',$oUserOwner->getUserAlbumsWebPath()); 
			$oViewer->Assign('oUserCurrent', $this->oUserCurrent); 
			$oViewer->Assign('oAlbum', $oAlbum);
			$oViewer->Assign('oPicture', $oPicture); 
			$sResPhotos .= $oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes/picture_preview.tpl');
		}
		
		$this->Viewer_AssignAjax('aResult',$sResPhotos);
	}
	
	public function AjaxSaveFriendPageHistory() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return false;
		}

		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( getRequest ( 'album_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		
		$oRelatedNew = Engine::GetEntity ( 'PluginPicalbums_Comment' );
		$oRelatedNew->setTargetId ( getRequest ( 'album_target_id' ) );
		$oRelatedNew->setUserId ( $this->oUserCurrent->getId() );
		
		if (!($this->PluginPicalbums_Related_AddRelated ( $oRelatedNew ) == true)) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		} 
	}
	// Получение комментариев
	protected function AjaxGetComments() {
		$this->Viewer_SetResponseAjax('json');

		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById ( getRequest ( 'picture_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование альбома
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		$aComments = $this->Comment_GetCommentsByTargetId(getRequest ( 'picture_target_id' ), 'picalbums');
		$aComments = $aComments['comments'];		
				
		$oViewer=$this->Viewer_GetLocalViewer();
		$oViewer->Assign('oUserCurrent', $this->oUserCurrent); 
		$oViewer->Assign('aComments', $aComments);
		$aResult = $oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes/comments.tpl');
		
		$this->Viewer_AssignAjax('aResult',$aResult);
	}
	
	// Удаление комментария
	protected function AjaxRemoveComment() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование комментария
		if (! ($oComment = $this->Comment_GetCommentById ( getRequest ( 'comment_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById ( $oComment->getTargetId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование альбома
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}		
		
		$iCurrentUserId = $this->oUserCurrent->getId ();
		if (! $this->oUserCurrent->isAdministrator())	{
			if($oComment->getUserId() != $iCurrentUserId) {
				if($oAlbum->getUserId() != $iCurrentUserId) {
					$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_no_right_to_delete_comment' ), $this->Lang_Get ( 'error' ) );
					return;
				}
			}
			if($this->PluginPicalbums_Blacklist_isUserBlocked($iCurrentUserId) >= 1) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_user_is_blocked' ), $this->Lang_Get ( 'error' ) );
				return;
			}
		}
		
		// Удаление комментария
		if ($this->PluginPicalbums_Comment_DeleteComment ( getRequest ( 'comment_target_id' ) ) == true) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_comment_delete' ), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}
	
	// Добавление комментария к картинке
	protected function AjaxAppendComment() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById (getRequest ( 'picture_target_id' )))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}		
		
		$iCurrentUserId = $this->oUserCurrent->getId ();		
		if (! $this->oUserCurrent->isAdministrator())	{
			if($this->PluginPicalbums_Blacklist_isUserBlocked($iCurrentUserId) >= 1) {
				$this->Message_AddErrorSingle ($this->Lang_Get ( 'picalbums_user_is_blocked' ), $this->Lang_Get ( 'error' ) );
				return;
			}
			
			if($this->oUserCurrent->getRating() < Config::Get('plugin.picalbums.comment_limit_time_off_rating')) {
				$LastDate = $this->Comment_GetLastCommentDate ( $iCurrentUserId, 'picalbums' );		
				if (Config::Get ( 'plugin.picalbums.comment_limit_time' ) > 0 and $LastDate) {
					$sDateLast = strtotime ( $LastDate );			
					if (((time () - $sDateLast) < Config::Get('plugin.picalbums.comment_limit_time'))) {
						$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_comment_append_speed_error' ), $this->Lang_Get ( 'error' ) );
						return;
					}
				}
			}
		}

		$sText = getRequest ( 'comment_text' );		
		$iTextLength = mb_strlen($sText, 'UTF-8');
		
		// Проверяется корректность заполнения текста
		$sText = $this->TextParser($sText, true) ;	
		if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_form_max_characters' ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_comment_length_error_start' ).Config::Get ( 'plugin.picalbums.text_form_max_characters' ). $this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Выделение ссылки на других пользователей
		$sText = $this->UserNameLinkDetect($sText);
		
		$dCdate = date ( "Y-m-d H:i:s" );
		// Формируем комментарий		
		$oCommentNew=Engine::GetEntity('Comment');
		$oCommentNew->setTargetId(getRequest ( 'picture_target_id' ));
		$oCommentNew->setTargetType('picalbums');
		$oCommentNew->setTargetParentId($oAlbum->getId());
		$oCommentNew->setUserId($iCurrentUserId);		
		$oCommentNew->setText($sText);
		$oCommentNew->setDate($dCdate);
		$oCommentNew->setUserIp(func_getIp());
		$oCommentNew->setPid(null);
		$oCommentNew->setPublish(1);
		$oCommentNew->setTextHash(md5($sText));
		
		// Добавляем комментарий
		if (($oComment = $this->Comment_AddComment ( $oCommentNew ))) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_comment_append' ), $this->Lang_Get ( 'attention' ) );
			
			// Добавляем коммент в прямой эфир
			$oCommentOnline=Engine::GetEntity('Comment_CommentOnline');
			$oCommentOnline->setTargetId(getRequest ( 'picture_target_id' ));
			$oCommentOnline->setTargetType('picalbums');
			$oCommentOnline->setTargetParentId($oAlbum->getId());
			$oCommentOnline->setCommentId($oComment->GetId());

			$this->Comment_AddCommentOnline($oCommentOnline);
			
			// Если мы отправили коммент не на свою фотографию, то возможно отправить имейл
			if($oAlbum->getUserId() != $iCurrentUserId) {
				$oSettings = $this->PluginPicalbums_Settings_GetSettingsByUserID($oAlbum->getUserId());
				if($oSettings->getCommentNotifyByEmail() == 1) {
					$oSendUser = $this->User_GetUserById($oAlbum->getUserId());				
					$this->Notify_Send(
						$oSendUser,
						'notify.picture_comment.tpl',
						$this->Lang_Get ( 'picalbums_notify_email_text' ),
						array(
							'userPost' => $this->oUserCurrent->getLogin(),
							'userPostWebPath' => $this->oUserCurrent->getUserWebPath(),
							'userLogin' => $oSendUser->getLogin(),							
							'userLoginWebPath' => $oSendUser->getUserAlbumsWebPath(),
							'commentText' => $sText,
							'sAlbumURL' => $oAlbum->getURL(),
							'pictureURL' => $oPicture->getURL(),
							'dateAdd' => date ( "Y-m-d H:i:s" ),
						),
						'picalbums'
					);
				}
			}
			
			$aComments = array();
			$oComment->setUser($this->oUserCurrent);
			array_push($aComments, $oComment);
			
			$oViewer=$this->Viewer_GetLocalViewer();
			$oViewer->Assign('oUserCurrent', $this->oUserCurrent); 
			$oViewer->Assign('aComments', $aComments);
			$aResult = $oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes/comments.tpl');
			$this->Viewer_AssignAjax('aResult',$aResult);
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
		}	
	}

    protected function AjaxAutocompleterTag() {
        $this->Viewer_SetResponseAjax('json');
        
		if (!($sValue=getRequest('value',null,'post'))) {
			return ;
		}

		$aItems=array();
		$aTags=$this->PluginPicalbums_Tag_GetTagsByLike($sValue,10);
		foreach ($aTags as $oTag) {
			$aItems[]=$oTag->getText();
		}
		$this->Viewer_AssignAjax('aItems',$aItems);
	}

	// Получить список пользователей проголосовавших за картинку
	protected function AjaxAllHeartPicture() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById (getRequest ( 'picture_target_id' )))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$aUsersHearted = $this->PluginPicalbums_Heart_GetUsersHeartedByTargetId(getRequest ( 'picture_target_id' ));
		if(count($aUsersHearted) == 0) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_no_heart_by_picture' ), $this->Lang_Get ( 'attention' ) );
			return;
		}
		$iHeartCount = $this->PluginPicalbums_Heart_GetUsersHeartedCountByTargetId(getRequest ( 'picture_target_id' ));

        $oViewer=$this->Viewer_GetLocalViewer();
        $oViewer->Assign('oPicture',$oPicture);
        $oViewer->Assign('oUserCurrent', $this->oUserCurrent);
        $oViewer->Assign('aUsersHearted', $aUsersHearted);
        $oViewer->Assign('iHeartCount', $iHeartCount);
        $oViewer->Assign('bDntShowTitle', true);
        $sHeartText = $oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes/heart_avatar.tpl');

		$this->Viewer_AssignAjax ( 'textHearted', $sHeartText );
		$this->Viewer_AssignAjax ( 'iHeartCount', $iHeartCount );
	}
	
	// Подтверждение себя на метке
	protected function AjaxMarkConfirm() {
		$this->Viewer_SetResponseAjax('json');	
		
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById (getRequest ( 'picture_target_id' )))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$sCurrentUser = $this->oUserCurrent;
		$iCurrentUserId = $sCurrentUser->getId ();
		
		if((getRequest ( 'confirm_status' ) == 0) || (getRequest ( 'confirm_status' ) == '0')) {
			$this->talkNotifyWhenMarkConfirm($iCurrentUserId, $oAlbum, $oPicture);
			if($this->PluginPicalbums_Note_ConfirmMarks(getRequest ( 'picture_target_id' ), $iCurrentUserId )) 
				$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_you_confirm_ok' ), $this->Lang_Get ( 'attention' ) );
			else 
				$this->Message_AddErrorSingle($this->Lang_Get ( 'picalbums_you_confirm_error' ), $this->Lang_Get ( 'error' ) );
		} else {
			if($this->PluginPicalbums_Note_NonConfirmMarks(getRequest ( 'picture_target_id' ), $iCurrentUserId ))
				$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_you_unconfirm_ok' ), $this->Lang_Get ( 'attention' ) );
			else 
				$this->Message_AddErrorSingle($this->Lang_Get ( 'picalbums_you_confirm_error' ), $this->Lang_Get ( 'error' ) );
		}
	}

    private function SetHeartTextAjax($oPicture) {
        $aUsersHearted = $this->PluginPicalbums_Heart_GetUsersHeartedLimitByTargetId($oPicture->getId(), 6);
        $iHeartCount = $this->PluginPicalbums_Heart_GetUsersHeartedCountByTargetId($oPicture->getId());

        $oViewer=$this->Viewer_GetLocalViewer();
        $oViewer->Assign('oPicture',$oPicture);
        $oViewer->Assign('oUserCurrent', $this->oUserCurrent);
        $oViewer->Assign('aUsersHearted', $aUsersHearted);
        $oViewer->Assign('iHeartCount', $iHeartCount);
        $sHeartText = $oViewer->Fetch( rtrim(Plugin::GetTemplatePath(__CLASS__),'/').'/includes/heart_avatar.tpl');

        $this->Viewer_AssignAjax('sHeartText',$sHeartText);
        $this->Viewer_AssignAjax('iHeartCount',$iHeartCount);
    }
	
	// Добавление сердечка к картинке
	protected function AjaxHeartPicture() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById (getRequest ( 'picture_target_id' )))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}		
		
		$iCurrentUserId = $this->oUserCurrent->getId ();
		
		if (! $this->oUserCurrent->isAdministrator())	{
			if($this->PluginPicalbums_Blacklist_isUserBlocked($iCurrentUserId) >= 1) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_user_is_blocked' ), $this->Lang_Get ( 'error' ) );
				return;
			}
		}

		// Если уже голосвали значит вы отменяете свой голос
		if($this->PluginPicalbums_Heart_isUserVotedByTarget($iCurrentUserId, $oPicture->getId())) {
			if($this->PluginPicalbums_Heart_DeleteHeart($iCurrentUserId, getRequest ( 'picture_target_id' ))) {
				$this->Viewer_AssignAjax ( 'heartStatus', false );
				
				$iHeartCount = $this->PluginPicalbums_Heart_GetUsersHeartedCountByTargetId($oPicture->getId());
				$this->Viewer_AssignAjax ( 'iHeartCount', $iHeartCount );
				
				$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_heart_on' ), $this->Lang_Get ( 'attention' ) );
			}
			else 
				$this->Message_AddErrorSingle($this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
            $this->SetHeartTextAjax($oPicture);
			return;
		}
		
		// Пользователь не голосовал, добавляем голос
		$oHeartNew = Engine::GetEntity ( 'PluginPicalbums_Heart' );
		$oHeartNew->setTargetId ( getRequest ( 'picture_target_id' ) );
		$oHeartNew->setUserId ( $iCurrentUserId );
		
		if (($iCommentId = $this->PluginPicalbums_Heart_AddHeart ( $oHeartNew ))) {
			$this->Viewer_AssignAjax ( 'heartStatus', true );
            $this->SetHeartTextAjax($oPicture);
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_heart_create' ), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
		}	
	}
	
	// Красивая галерея на jquery
	protected function ParanoidShowEvent() {
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		
		$sUserLogin=$this->sCurrentEvent;				
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if(($oUser == null) || ($this->GetParam(0) != 'polaroid')) {
			return Router::Action('404'); 
		}
		
		$aAlbums = $this->PluginPicalbums_Album_GetAlbumsByUserId($oUser->getId());
		
		$this->Viewer_Assign ( 'aAlbums', $aAlbums );
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		$this->SetTemplateAction ( 'paraloid' );
	}
	
	// Показ всех картинок где отмечен пользователь
	protected function AllPicturesNoteShowEvent() {
		$sUserLogin=$this->sCurrentEvent;				
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if(($oUser == null) || ($this->GetParam(0) != 'note')) {
			return Router::Action('404'); 
		}
		
		$aPictures = $this->PluginPicalbums_Note_GetPicturesByUserMarkAll($oUser->getId());
		
		$this->Viewer_Assign ( 'aPictures', $aPictures );
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		$this->Viewer_Assign ( 'sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
		
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_mark_user_photo').' '.$oUser->getLogin()));
		
		$this->SetTemplateAction ( 'allnotepictures' );
	}
	
	// Показ всех фотографий страница
	protected function AllPicturesShowEvent() {
		$sUserLogin=$this->sCurrentEvent;				
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if(($oUser == null) || ($this->GetParam(0) != 'allpictures')) {
			return Router::Action('404'); 
		}
		
		$iPage = 0;
		if($this->GetParam(1))
			$iPage = $this->GetParam(1); 
		
		$aAlbums = $this->PluginPicalbums_Album_GetAlbumsByUserId($oUser->getId());
		
		$this->Viewer_Assign ( 'iPage', $iPage );
		$this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.all_picture_page_cnt' ) );
		$this->Viewer_Assign ( 'iPicCnt', Config::Get ( 'plugin.picalbums.all_picture_page_cnt' ) );
		
		$this->Viewer_Assign ( 'aAlbums', $aAlbums );
		$this->Viewer_Assign ( 'oUserProfile', $oUser );

		$iUserType = 0;		
		if($this->oUserCurrent) {
			$iUserType = 1;
			if($this->oUserCurrent->getId() == $oUser->getId())
				$iUserType = 2;
			else {
				$oUserFriend = $oUser->isUsersFriend($this->oUserCurrent);
				if($oUserFriend)
					$iUserType = 2;
			}
		}	
		$this->Viewer_Assign ( 'oPictureCount', $this->PluginPicalbums_Picture_GetPicturesCountByUserId($oUser->getId(), $iUserType) );
		$this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_menu_profile_all_photo').' '.$oUser->getLogin()));
		
		$this->SetTemplateAction ( 'allpictures' );
		if (!isset($_SERVER['HTTP_X_PJAX'])) {
			$this->Viewer_Assign ( 'isPjax', false );	
		} else {			
			$this->Viewer_Assign ( 'isPjax', true );	
		}
	}

    protected function FavouriteShowEvent() {
        $sUserLogin=$this->sCurrentEvent;
        $oUser = $this->User_GetUserByLogin($sUserLogin);
        if(($oUser == null) || ($this->GetParam(0) != 'favourite') || ($oUser->getId() != $this->oUserCurrent->getId())) {
            return Router::Action('404');
        }



        $iPage = 0;
        if($this->GetParam(1))
            $iPage = $this->GetParam(1);

        $aPictures = $this->PluginPicalbums_Heart_GetPicturesHeartedByUserId($oUser->getId());

        $this->Viewer_Assign ( 'iPage', $iPage );
        $this->Viewer_Assign ( 'iPosStart', $iPage * Config::Get ( 'plugin.picalbums.all_picture_page_cnt' ) );
        $this->Viewer_Assign ( 'iPicCnt', Config::Get ( 'plugin.picalbums.all_picture_page_cnt' ) );

        $this->Viewer_Assign ( 'aPictures', $aPictures );

        $this->Viewer_Assign ( 'oUserProfile', $oUser );
        $this->Viewer_Assign ( 'iPictureCount', $this->PluginPicalbums_Heart_GetUsersHeartedCountByUserId($this->User_GetUserCurrent()->getId()) );
        $this->Viewer_AddHtmlTitle(htmlspecialchars($this->Lang_Get('picalbums_favourite')));

        $this->SetTemplateAction ( 'favourite' );
        if (!isset($_SERVER['HTTP_X_PJAX'])) {
            $this->Viewer_Assign ( 'isPjax', false );
        } else {
            $this->Viewer_Assign ( 'isPjax', true );
        }
    }

	
	// Автоподстановка пользователей
	protected function AjaxUserAutoComplete() {
		$this->Viewer_SetResponseAjax('json');
		
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$aResult=array();
		
		$sNameStartsWith = $this->TextParser(getRequest ( 'name_startsWith' ), false);	
		
		if(Config::Get ( 'plugin.picalbums.notes_mark_only_friend' ) == true)
			$aUsers = $this->PluginPicalbums_Picalbums_GetFriendsByUserIdAndLoginLike($this->oUserCurrent->getId(), $sNameStartsWith, 10);
		else
			$aUsers = $this->PluginPicalbums_Picalbums_GetAllUsersLoginLike($sNameStartsWith, 10);
	
		foreach ($aUsers as $oUser) {
			$aResult[] = $oUser->getLogin();
		}		
		$this->Viewer_AssignAjax('aResult', $aResult);
	}
	
	// Автоподстановка для черного списка
	protected function AjaxUserAutoCompleteBlackList() {
		$this->Viewer_SetResponseAjax('json');
		
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		
		if (! $this->oUserCurrent->isAdministrator())	{
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$sNameStartsWith = $this->TextParser(getRequest ( 'name_startsWith' ), false);	
		
		$aResult=array();
		$aUsers = $this->PluginPicalbums_Picalbums_GetAllUsersLoginLike($sNameStartsWith, 10);	
		foreach ($aUsers as $oUser) {
			$aResult[] = $oUser->getLogin();
		}		
		$this->Viewer_AssignAjax('aResult', $aResult);
	}
	
	// Удаление с черного списка
	protected function AjaxRemoveFromBlackList() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		
		if (! $this->oUserCurrent->isAdministrator())	{
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		if (! ($oUser = $this->User_GetUserById ( getRequest ( 'user_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		// Удаление 
		if ($this->PluginPicalbums_Blacklist_DeleteFromBlackList ( getRequest ( 'user_id' ) ) == true) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_user_add_to_black_list' ), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}
	
	// добавление в черный список
	protected function AjaxAppendToBlackList() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		
		if (! $this->oUserCurrent->isAdministrator())	{
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$sLogin = $this->TextParser(getRequest ( 'user_login' ), false);	
		
		if (! ($oUser = $this->User_GetUserByLogin ( $sLogin ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_user_by_name_not_found' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$oBlackList = Engine::GetEntity ( 'PluginPicalbums_Blacklist' );
		$oBlackList->setUserId ( $oUser->getId() );
		
		if(!$this->PluginPicalbums_Blacklist_AddToBlackList ( $oBlackList )){
			$this->Message_AddErrorSingle ($this->Lang_Get ( 'picalbums_user_already_in_black_list' ), $this->Lang_Get ( 'error' ) );
			return;
		}
			
		$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_user_append_to_black_list' ), $this->Lang_Get ( 'attention' ) );
		$this->Viewer_AssignAjax('userId', $oUser->getId());
		$this->Viewer_AssignAjax('userProfilePath', $oUser->getUserWebPath());
	}

	protected function AjaxRemoveCategory() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		if(!$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return; 
		}

		if (! ($this->PluginPicalbums_Category_GetCategoryById ( getRequest ( 'category_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		if ($this->PluginPicalbums_Category_DeleteCategory ( getRequest ( 'category_target_id' ) )) {
			$this->Message_AddNoticeSingle ($this->Lang_Get('picalbums_new_category_delete'), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get('picalbums_new_category_delete_error'), $this->Lang_Get ( 'error' ) );
		}
	}
	
	protected function AjaxEditCategory() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}		
		
		$iCurrentUserId = $this->oUserCurrent->getId ();
		
		if(!$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}

		// Проверка на существование альбома
		if (! ($oCategory = $this->PluginPicalbums_Category_GetCategoryById ( getRequest ( 'category_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}
				
		$iTextLength = mb_strlen(getRequest ( 'category_title' ), 'UTF-8');
		// Проверяется корректность заполнения текста
		$sTitle = $this->TextParser(getRequest ( 'category_title' ), false) ;	
		if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_title_max_characters' ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_title_length_error_start' ).Config::Get ( 'plugin.picalbums.text_title_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		$iResId = $this->PluginPicalbums_Category_EditCategory ( getRequest ( 'category_target_id' ), $sTitle );
		if (($iResId == true) || ($iResId == null)) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_category_edit_ok' ), $this->Lang_Get ( 'attention' ) );			
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}
	
	protected function SliderGalleryShowEvent() {
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		
		$sUserLogin=$this->sCurrentEvent;				
		$oUser = $this->User_GetUserByLogin($sUserLogin);
		if(($oUser == null) || ($this->GetParam(0) != 'slidergallery')) {
			return Router::Action('404'); 
		}
		
		$aAlbums = $this->PluginPicalbums_Album_GetAlbumsByUserId($oUser->getId());
		
		$this->Viewer_Assign ( 'aAlbums', $aAlbums );
		$this->Viewer_Assign ( 'oUserProfile', $oUser );
		$this->SetTemplateAction ( 'slidergallery' );
	}
	
	protected function AjaxSortPictures() {
		$this->Viewer_SetResponseAjax('json');		
	
		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}
		
		if (! getRequest ( 'sortdata' ) ) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		if ($this->PluginPicalbums_Picture_SortPictures ( getRequest ( 'sortdata' ) )) {
			$this->Message_AddNoticeSingle ($this->Lang_Get('picalbums_picture_change_placed_ok'), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get('picalbums_picture_change_placed_error'), $this->Lang_Get ( 'error' ) );
		}
	}

    protected function AjaxSortCatSet() {
		$this->Viewer_SetResponseAjax('json');

		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		if (! getRequest ( 'sortdata' ) ) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		if ($this->PluginPicalbums_Category_SortCategories ( getRequest ( 'sortdata' ) )) {
			$this->Message_AddNoticeSingle ($this->Lang_Get('picalbums_picture_change_placed_ok'), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get('picalbums_picture_change_placed_error'), $this->Lang_Get ( 'error' ) );
		}
	}

    // Сохранение метки в базу
    private function setNote($iPosition, $sNoteText, $sLink, $iPictureId, $iUserId, $iUserMarkId, $bIsConfirm) {

        $iPosition = explode(',', $iPosition);

        if (count($iPosition) != 4)
            return $this->Lang_Get ( 'picalbums_param_incorrect' );

        if (empty($sNoteText))
            $sNoteText = '';

        $sNoteText = str_replace("\n", ' ', $sNoteText);

        while(strstr($sNoteText, '  '))
            $sNoteText = str_replace('  ', ' ', $sNoteText);

        $sNoteText = trim($sNoteText, ' ');

        if (empty($sLink) || !isValidLink($sLink))
            $sLink = '';

        if (empty($sNoteText))
            return $this->Lang_Get ( 'picalbums_note_is_empty' );

        if(empty($sLink) || ($iUserMarkId == null))
            $bIsConfirm = false;

        if($iUserId == $iUserMarkId)
            $bIsConfirm = false;

        // Формируем метку
        $sNoteTextNew = Engine::GetEntity ( 'PluginPicalbums_Note' );

        $sNoteTextNew->setLeft ( $iPosition[0] );
        $sNoteTextNew->setTop ( $iPosition[1] );
        $sNoteTextNew->setWidth ( $iPosition[2] );
        $sNoteTextNew->setHeight ( $iPosition[3] );
        $sNoteTextNew->setDateAdd ( date ( "Y-m-d H:i:s" ) );
        $sNoteTextNew->setNote ( $sNoteText );
        $sNoteTextNew->setLink ( $sLink );
        $sNoteTextNew->setUserId($iUserId);
        $sNoteTextNew->setUserMarkId($iUserMarkId);
        $sNoteTextNew->setPictureId($iPictureId);
        $sNoteTextNew->setIsConfirm($bIsConfirm ? 0 : 1);

        if (($iNoteId = $this->PluginPicalbums_Note_AddNote ( $sNoteTextNew ))) {
            return 'true';
        } else {
            return $this->Lang_Get ( 'picalbums_note_DB_Error' );
        }
    }
    // Редактирование метки
    private function editNote($iPosition, $sNoteText, $sLink, $id, $iPictureId, $iUserMarkId, $iUserId) {

        $iPosition = explode(',', $iPosition);

        if (count($iPosition) != 4)
            return $this->Lang_Get ( 'picalbums_param_incorrect' );

        if (empty($sNoteText))
            $sNoteText = '';

        $sNoteText = str_replace("\n", ' ', $sNoteText);

        while(strstr($sNoteText, '  '))
            $sNoteText = str_replace('  ', ' ', $sNoteText);

        $sNoteText = trim($sNoteText, ' ');

        if (empty($sLink) || !isValidLink($sLink))
            $sLink = '';

        if (empty($sNoteText) || empty($sLink))
            return $this->Lang_Get ( 'picalbums_note_is_empty' );

        if(empty($sLink) || ($iUserMarkId == null))
            $bIsConfirm = false;

        if($iUserId == $iUserMarkId)
            $bIsConfirm = false;

        $sNoteTextNew = Engine::GetEntity ( 'PluginPicalbums_Note' );
        $sNoteTextNew->setId ( $id );
        $sNoteTextNew->setLeft ( $iPosition[0] );
        $sNoteTextNew->setTop ( $iPosition[1] );
        $sNoteTextNew->setWidth ( $iPosition[2] );
        $sNoteTextNew->setHeight ( $iPosition[3] );
        $sNoteTextNew->setDateAdd ( date ( "Y-m-d H:i:s" ) );
        $sNoteTextNew->setNote ( $sNoteText );
        $sNoteTextNew->setLink ( $sLink );
        $sNoteTextNew->setUserMarkId ( $iUserMarkId );
        $sNoteTextNew->setPictureId ( $iPictureId );

        if (($this->PluginPicalbums_Note_EditNote ( $sNoteTextNew ))) {
            return 'true';
        } else {
            return $this->Lang_Get ( 'picalbums_note_DB_Error' );
        }
    }

    // Отправка имейла при отметке пользователя
    private function emailNotifyWhenMark($oUserCurrent, $oMarkUser, $oAlbum, $oPicture, $sNoteText) {
        if($oMarkUser!= $this->oUserCurrent->getId()) {
            $oSettings = $this->PluginPicalbums_Settings_GetSettingsByUserID($oMarkUser);
            if($oSettings->getMarkNotifyByEmail() == 1) {
                $oSendUser = $this->User_GetUserById($oMarkUser);

                $oUserOwner = $oAlbum->getUserOwner();
                if($oUserOwner) {
                    $sAlbumWebPath = $oUserOwner->getUserAlbumsWebPath();
                } else {
                    $sAlbumWebPath = Router::GetPath(Config::Get ( 'plugin.picalbums.main_albums_router_name'));
                }

                $this->Notify_Send(
                    $oSendUser,
                    'notify.picture_mark.tpl',
                    $this->Lang_Get ( 'picalbums_picture_confirm_mail_title' ),
                    array(
                        'userPost' => $this->oUserCurrent->getLogin(),
                        'userPostWebPath' => $this->oUserCurrent->getUserWebPath(),
                        'userLogin' => $oSendUser->getLogin(),

                        'albumWebPath' => $sAlbumWebPath,
                        'sAlbumURL' => $oAlbum->getURL(),
                        'pictureURL' => $oPicture->getURL(),
                        'pictureDesc' => $oPicture->getDescription(),
                        'noteText' => $sNoteText,
                    ),
                    'picalbums'
                );
            }
        }
    }

    // Отпрака личного сообщения при отметке
    private function talkNotifyWhenMark($oUserCurrent, $oMarkUser, $oAlbum, $oPicture, $sNoteText) {
        if( Config::Get ( 'plugin.picalbums.talk_notify_when_user_mark' ) == true)
        {
            $oSettings = $this->PluginPicalbums_Settings_GetSettingsByUserID($oMarkUser);
            if($oSettings->getMarkNotifyByEmail() == 0)
            {
                $oSendUser = $this->User_GetUserById($oMarkUser);
                if($oMarkUser !=  $this->oUserCurrent->getId()) {

                    $oUserOwner = $oAlbum->getUserOwner();
                    if($oUserOwner) {
                        $sAlbumWebPath = $oUserOwner->getUserAlbumsWebPath();
                    } else {
                        $sAlbumWebPath = Router::GetPath(Config::Get ( 'plugin.picalbums.main_albums_router_name'));
                    }

                    $sText=$this->Lang_Get(
                        'picalbums_user_mark',
                        array(
                            'userPost' => $this->oUserCurrent->getLogin(),
                            'userPostWebPath' => $this->oUserCurrent->getUserWebPath(),
                            'userLogin' => $oSendUser->getLogin(),
                            'albumWebPath' => $sAlbumWebPath,
                            'sAlbumURL' => $oAlbum->getURL(),
                            'pictureURL' => $oPicture->getURL(),
                            'pictureID' => $oPicture->getId(),
                            'pictureDesc' => $oPicture->getDescription(),
                            'noteText' => $sNoteText,
                        )
                    );

                    $this->Talk_SendTalk($this->Lang_Get ( 'picalbums_mark_mail_title' ), $sText, $this->oUserCurrent, $oSendUser, Config::Get ( 'plugin.picalbums.talk_notify_send_mail' ));
                }
            }
        }
    }

    // Отправка ЛС когда метка удалена
    private function talkNotifyWhenMarkDelete($oUserDoMarkId, $oMarkUserId, $oAlbum, $oPicture) {
        if( Config::Get ( 'plugin.picalbums.talk_notify_when_user_mark_delete' ) == true)
        {
            $oMarkUser = $this->User_GetUserById($oMarkUserId);
            $oUserDoMark = $this->User_GetUserById($oUserDoMarkId);

            $oUserOwner = $oAlbum->getUserOwner();
            if($oUserOwner) {
                $sAlbumWebPath = $oUserOwner->getUserAlbumsWebPath();
            } else {
                $sAlbumWebPath = Router::GetPath(Config::Get ( 'plugin.picalbums.main_albums_router_name'));
            }

            $sText=$this->Lang_Get(
                'picalbums_user_mark_delete',
                array(
                    'userPost' => $oMarkUser->getLogin(),
                    'userPostWebPath' => $oMarkUser->getUserWebPath(),
                    'userLogin' => $oUserDoMark->getLogin(),

                    'albumWebPath' => $sAlbumWebPath,
                    'sAlbumURL' => $oAlbum->getURL(),
                    'pictureURL' => $oPicture->getURL(),
                    'pictureID' => $oPicture->getId(),
                    'pictureDesc' => $oPicture->getDescription(),
                )
            );

            $this->Talk_SendTalk($this->Lang_Get ( 'picalbums_mark_delete_mail_title' ), $sText, $oMarkUser, $oUserDoMark, Config::Get ( 'plugin.picalbums.talk_notify_send_mail' ));
        }
    }

    // Отправка ЛС при подтверждении метки
    private function talkNotifyWhenMarkConfirm($oMarkUserId, $oAlbum, $oPicture) {
        if( Config::Get ( 'plugin.picalbums.talk_notify_when_user_mark' ) == true)
        {
            $oMarkUser = $this->User_GetUserById($oMarkUserId);
            $aUsersIds = $this->PluginPicalbums_Note_getUsersWhoMarkedAnotheUserByPicture($oPicture->getId(), $oMarkUserId);

            if($aUsersIds) {
                foreach($aUsersIds as $aUserId) {
                    if($oMarkUserId  != $aUserId) {
                        $oUserPost = $this->User_GetUserById($aUserId);

                        $oUserOwner = $oAlbum->getUserOwner();
                        if($oUserOwner) {
                            $sAlbumWebPath = $oUserOwner->getUserAlbumsWebPath();
                        } else {
                            $sAlbumWebPath = Router::GetPath(Config::Get ( 'plugin.picalbums.main_albums_router_name'));
                        }

                        $sText=$this->Lang_Get(
                            'picalbums_user_mark_confirm',
                            array(
                                'userPost' => $oMarkUser->getLogin(),
                                'userPostWebPath' => $oMarkUser->getUserWebPath(),
                                'userLogin' => $oUserPost->getLogin(),
                                'albumWebPath' => $sAlbumWebPath,
                                'sAlbumURL' => $oAlbum->getURL(),
                                'pictureURL' => $oPicture->getURL(),
                                'pictureID' => $oPicture->getId(),
                                'pictureDesc' => $oPicture->getDescription(),
                            )
                        );

                        $this->Talk_SendTalk($this->Lang_Get ( 'picalbums_mark_confirm_mail_title' ), $sText, $oMarkUser, $oUserPost, Config::Get ( 'plugin.picalbums.talk_notify_send_mail' ));
                    }
                }
            }
        }
    }

    // Функция добавления в друзья отправка запроса и т.д.
    private function SubmitAddFriend($oUser, $oUserCurrent, $oFriend=null) {

        $oFriendNew=Engine::GetEntity('User_Friend');
        $oFriendNew->setUserTo($oUser->getId());
        $oFriendNew->setUserFrom($this->oUserCurrent->getId());
        // Добавляем заявку в друзья
        $oFriendNew->setStatusFrom(ModuleUser::USER_FRIEND_OFFER);
        $oFriendNew->setStatusTo(ModuleUser::USER_FRIEND_NULL);

        $bStateError=($oFriend)
            ? !$this->User_UpdateFriend($oFriendNew)
            : !$this->User_AddFriend($oFriendNew);

        if ( !$bStateError ) {
            $sTitle=$this->Lang_Get(
                'user_friend_offer_title',
                array(
                    'login'=>$this->oUserCurrent->getLogin(),
                    'friend'=>$oUser->getLogin()
                )
            );

            require_once Config::Get('path.root.engine').'/lib/external/XXTEA/encrypt.php';
            $sCode=$this->oUserCurrent->getId().'_'.$oUser->getId();
            $sCode=rawurlencode(base64_encode(xxtea_encrypt($sCode, Config::Get('module.talk.encrypt'))));

            $aPath=array(
                'accept'=>Router::GetPath('profile').'friendoffer/accept/?code='.$sCode,
                'reject'=>Router::GetPath('profile').'friendoffer/reject/?code='.$sCode
            );

            $sText=$this->Lang_Get(
                'user_friend_offer_text',
                array(
                    'login'=>$this->oUserCurrent->getLogin(),
                    'accept_path'=>$aPath['accept'],
                    'reject_path'=>$aPath['reject'],
                    'user_text'=>''
                )
            );
            $oTalk=$this->Talk_SendTalk($sTitle,$sText,$this->oUserCurrent,array($oUser),false,false);
            // Отправляем пользователю заявку
            $this->Notify_SendUserFriendNew(
                $oUser,$this->oUserCurrent,'',
                Router::GetPath('talk').'read/'.$oTalk->getId().'/'
            );
            // Удаляем отправляющего юзера из переписки
            $this->Talk_DeleteTalkUserByArray($oTalk->getId(),$this->oUserCurrent->getId());
        }
    }

    // Обработка добавления в друзья
    private function NoteMarkFriendAdd($oUser, $oUserCurrent) {
        // При попытке добавить в друзья себя, возвращаем ошибку
        if ($this->oUserCurrent->getId()==$oUser->getId()) {
            return;
        }
        // Получаем статус дружбы между пользователями
        $oFriend=$this->User_GetFriend($oUser->getId(),$this->oUserCurrent->getId());
        // Если связи ранее не было в базе данных, добавляем новую
        if( !$oFriend ) {
            $this->SubmitAddFriend($oUser, $this->oUserCurrent, $oFriend);
            return;
        }
        // Если статус связи соответствует статусам отправленной и акцептованной заявки,
        if($oFriend->getFriendStatus()==ModuleUser::USER_FRIEND_OFFER + ModuleUser::USER_FRIEND_ACCEPT) {
            return;
        }
        // Если пользователь ранее отклонил нашу заявку,
        if($oFriend->getUserFrom()==$this->oUserCurrent->getId()
                && $oFriend->getStatusTo()==ModuleUser::USER_FRIEND_REJECT ) {
            return;
        }
        // Если дружба была удалена, то проверяем кто ее удалил
        // и разрешаем восстановить только удалившему
        if($oFriend->getFriendStatus()>ModuleUser::USER_FRIEND_DELETE
                && $oFriend->getFriendStatus()<ModuleUser::USER_FRIEND_REJECT) {
            // Определяем статус связи текущего пользователя
            $iStatusCurrent	= $oFriend->getStatusByUserId($this->oUserCurrent->getId());

            if($iStatusCurrent==ModuleUser::USER_FRIEND_DELETE) {
                // Меняем статус с удаленного, на акцептованное
                $oFriend->setStatusByUserId(ModuleUser::USER_FRIEND_ACCEPT,$this->oUserCurrent->getId());
                $this->User_UpdateFriend($oFriend);
                return;
            }
        }
    }

	// Эвент обработки меток
	protected function AjaxNote() {
		$this->Viewer_SetResponseAjax('json');

		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		if (! $this->oUserCurrent->isAdministrator())	{
			if($this->PluginPicalbums_Blacklist_isUserBlocked($this->oUserCurrent->getId()) >= 1) {
				$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_user_is_blocked' ));
				return;
			}
		}

		$iPictureId = strip_tags(getRequest('pictureid'));
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById ( $iPictureId ))) {
			$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'system_error' ));
			return;
		}

		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'system_error' ));
			return;
		}

		if($oAlbum->getUserId() == Config::Get ( 'plugin.picalbums.virtual_main_user_id' )) {
			$oPictureOwnerUserId = Config::Get ( 'plugin.picalbums.virtual_main_user_id' );
		} else {
			$oPictureOwnerUser = $oAlbum->getUserOwner();
			if (! $oPictureOwnerUser) {
				$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'system_error' ));
				return;
			}
			$oPictureOwnerUserId = $oPictureOwnerUser->getId();
		}

		if(getRequest('add')) {
			$id = (int) strip_tags(getRequest('id'));
			$iPosition = getRequest('position');
			$sNoteText = (string) strip_tags((getRequest('note')));
			$sLink = (string) strip_tags(getRequest('link'));
			$sAuthor = (string) strip_tags(getRequest('author'));
			$sImage = (string) strip_tags(getRequest('image'));

			$sNoteText = $this->TextParser($sNoteText, false);
			$sLink = $this->TextParser($sLink, false);
			$sAuthor = $this->TextParser($sAuthor, false);

			$iTextLength = mb_strlen($sNoteText, 'UTF-8');
			$sNoteText = $this->TextParser($sNoteText, false) ;
			if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_form_max_characters' ))) {
				$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_note_length_error_start' ).Config::Get ( 'plugin.picalbums.text_form_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ));
				return;
			}

			$oUser = null;
			if($sAuthor) {
				$oUser = $this->User_GetUserByLogin($sAuthor);
				if(!$oUser) {
					$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_user_mark_not_exist' ));
					return;
				}
			}

			$iUserMarkId = null;
			if($oUser) {
				if(Config::Get ( 'plugin.picalbums.notes_mark_only_friend' ) == true) {
					$oUserFriend = $oUser->isUsersFriend($this->oUserCurrent);
					if(!$oUserFriend) {
						$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_mark_only_friend' ));
						return;
					}
				}
				$sLink = $oUser->getUserWebPath();
				$iUserMarkId = $oUser->getId();

				if(Config::Get ( 'plugin.picalbums.max_mark_count_by_one_picture' ) > 0) {
					if(Config::Get ( 'plugin.picalbums.max_mark_count_by_one_picture' ) == $this->PluginPicalbums_Note_MarkCountByOneUser($oPicture->getId(), $iUserMarkId)) {
						$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_mark_limit_by_one_user' ));
						return;
					}
				}

				if(Config::Get ( 'plugin.picalbums.notes_send_become_friend' ) == true)
					$this->NoteMarkFriendAdd($oUser, $this->oUserCurrent);
			}

			$sResultSet = $this->setNote($iPosition, $sNoteText, $sLink, $iPictureId, $this->oUserCurrent->getId (), $iUserMarkId, Config::Get ( 'plugin.picalbums.notes_mark_confirm' ));

			if($sResultSet == 'true') {
				if($oUser) {
					$this->emailNotifyWhenMark($this->oUserCurrent, $iUserMarkId, $oAlbum, $oPicture, $sNoteText);
					$this->talkNotifyWhenMark($this->oUserCurrent, $iUserMarkId, $oAlbum, $oPicture, $sNoteText);
				}
			}

			$this->Viewer_AssignAjax('result', $sResultSet);
		}else if(getRequest('edit')) {
			$id = (int) strip_tags(getRequest('id'));
			$iPosition = getRequest('position');
			$sNoteText = (string) strip_tags((getRequest('note')));
			$sLink = (string) strip_tags(getRequest('link'));
			$sImage = (string) strip_tags(getRequest('image'));
			$sAuthor = (string) strip_tags(getRequest('author'));

			$sNoteText = $this->TextParser($sNoteText, false);
			$sLink = $this->TextParser($sLink, false);
			$sAuthor = $this->TextParser($sAuthor, false);

			$iTextLength = mb_strlen($sNoteText, 'UTF-8');
			$sNoteText = $this->TextParser($sNoteText, false) ;
			if(($iTextLength < 2) || ($iTextLength > Config::Get ( 'plugin.picalbums.text_form_max_characters' ))) {
				$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_note_length_error_start' ).Config::Get ( 'plugin.picalbums.text_form_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ));
				return;
			}
			$oUser = null;
			if($sAuthor) {
				$oUser = $this->User_GetUserByLogin($sAuthor);
				if(!$oUser) {
					$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_user_mark_not_exist' ));
					return;
				}
			}
			$iUserMarkId = null;
			if($oUser) {
				if(Config::Get ( 'plugin.picalbums.notes_mark_only_friend' ) == true) {
					$oUserFriend = $oUser->isUsersFriend($this->oUserCurrent);
					if(!$oUserFriend) {
						$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_mark_only_friend' ));
						return;
					}
				}
				$sLink = $oUser->getUserWebPath();
				$iUserMarkId = $oUser->getId();

				if(Config::Get ( 'plugin.picalbums.notes_send_become_friend' ) == true)
					$this->NoteMarkFriendAdd($oUser, $this->oUserCurrent);
			}

			$aNotes = $this->PluginPicalbums_Note_GetNoteById ( $id );
			if($aNotes) {
				if($aNotes->getPictureId() == $iPictureId) {
					if(($this->oUserCurrent->getId () != $aNotes->getUserId()) && ($this->oUserCurrent->getId () != $aNotes->getUserMarkId()) &&
					   ($this->oUserCurrent->getId () != $oPictureOwnerUserId))
					{
						$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_no_right_to_edit_note' ));
						return;
					}

					if((Config::Get ( 'plugin.picalbums.notes_mark_confirm' ) == true) &&
							(($aNotes->getIsConfirm() == 1) || ($aNotes->getIsConfirm() == '1'))) {
						$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_no_right_to_edit_marknote' ));
						return;
					}

					$sResultSet = $this->editNote($iPosition, $sNoteText, $sLink, $id, $iPictureId, $iUserMarkId, $this->oUserCurrent->getId ());
					$this->Viewer_AssignAjax('result', $sResultSet);

					if($sResultSet == 'true') {
						if($oUser) {
							$this->emailNotifyWhenMark($this->oUserCurrent, $iUserMarkId, $oAlbum, $oPicture, $sNoteText);
							$this->talkNotifyWhenMark($this->oUserCurrent, $iUserMarkId, $oAlbum, $oPicture, $sNoteText);
						}
					}

					return;
				}
			}

			$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_note_not_found' ));
		} else if(getRequest('get')) {
			// Администратор и автор картинки видит все пометки
			if(($this->oUserCurrent->isAdministrator()) ||($oPictureOwnerUserId == $this->oUserCurrent->getId()))
				$aNotes = $this->PluginPicalbums_Note_GetNotesByPictureId ( $iPictureId );
			else {
				$aNotes = $this->PluginPicalbums_Note_GetConfirmedNotesByPictureId ( $iPictureId, $this->oUserCurrent->getId() );
			}

			$aResult=array();
			if($aNotes)
				foreach($aNotes as $sNoteText) {
					$sNoteTextUser = $this->User_GetUserById($sNoteText->getUserMarkId());
					$sLink = $sNoteText->getLink();
					$sAuthorMark = '';

					if($sNoteTextUser) {
						$sLink = $sNoteTextUser->getUserWebPath();
						$sAuthorMark = $sNoteTextUser->getLogin();
					}

					$sNoteTextAuthor = $this->User_GetUserById($sNoteText->getUserId());
					if($sNoteTextAuthor)
						$sNoteTextAuthor = $sNoteTextAuthor->getLogin();
					else
						$sNoteTextAuthor = '';

					$sAvatar = "";
					if($sNoteText->getUserMarkId()) {
						$oUser = $this->User_GetUserById($sNoteText->getUserMarkId());
						if($oUser)
							$sAvatar = "<img src='" . $oUser->getProfileAvatarPath(24) . "' /><br/>";
					}
					$bCanEdit = 0;
					$bCanDelete = 0;
					if(($this->oUserCurrent->getId () == $sNoteText->getUserId()) ||  ($this->oUserCurrent->getId () == $sNoteText->getUserMarkId()) ||
					   ($this->oUserCurrent->getId () == $oPictureOwnerUserId)) {
					   $bCanDelete = 1;
						if(!Config::Get ( 'plugin.picalbums.notes_mark_confirm' ))
							$bCanEdit = 1;
						else {
						   if(($sNoteText->getIsConfirm() == 0) || ($sNoteText->getIsConfirm() == '0'))
								$bCanEdit = 1;
						}
					}

					$aResult[]=array(
							'ID' => $sNoteText->getId(),
							'LEFT' => $sNoteText->getLeft(),
							'WIDTH' => $sNoteText->getWidth(),
							'TOP' => $sNoteText->getTop(),
							'HEIGHT' => $sNoteText->getHeight(),
							'DATE' => $sNoteText->getDateAdd(),
							'NOTE' => $sAvatar . $sNoteText->getNote(),
							'LINK' => $sLink,
							'AUTHOR' => $sNoteTextAuthor,
							'AUTHORMARK' => $sAuthorMark,
							'CANEDIT' => $bCanEdit,
							'CANDELETE' => $bCanDelete,
							'ISCONFIRM' => $sNoteText->getIsConfirm(),
					);
				}
			$this->Viewer_AssignAjax('result', $aResult);
		}
		else if(getRequest('delete')) {
			$iPictureId = strip_tags(getRequest('pictureid'));
			$iNoteId = strip_tags(getRequest('id'));

			$aNotes = $this->PluginPicalbums_Note_GetNoteById ( $iNoteId );
			if($aNotes) {
				if($aNotes->getPictureId() == $iPictureId) {
					if($this->oUserCurrent->getId () != $aNotes->getUserId()) {
						if(($this->oUserCurrent->getId () != $aNotes->getUserId()) && ($this->oUserCurrent->getId () != $aNotes->getUserMarkId()) &&
						   ($this->oUserCurrent->getId () != $oPictureOwnerUserId))
						{
							$this->Viewer_AssignAjax('result', 'Вы не можете удалить данную метку');
							return;
						}
					}
					if(($this->oUserCurrent->getId () == $aNotes->getUserMarkId()) &&
						($aNotes->getUserMarkId() != $aNotes->getUserId()))
							$this->talkNotifyWhenMarkDelete($aNotes->getUserId(), $aNotes->getUserMarkId(), $oAlbum, $oPicture);

					$this->PluginPicalbums_Note_DeleteNote ( $iNoteId );
					$this->Viewer_AssignAjax('result', 'true');
					return;
				}
			}
			$this->Viewer_AssignAjax('result', $this->Lang_Get ( 'picalbums_note_not_found' ));
		}
	}


    // Отмодерировать все картинки в альбоме
	protected function ModerateAllPicturesInAlbum() {
		$this->Viewer_SetResponseAjax('json');

		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		// Проверка на существование альбома
		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( getRequest ( 'album_target_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}

        if($oAlbum->GetUserIsModerator($this->oUserCurrent) == 0) {
            $this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
        }

		// модерировать альбом
		if ($this->PluginPicalbums_Album_ModerateAll ( getRequest ( 'album_target_id' ) ) == true) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_moder_allinalbum_ok' ), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}


    // Отмодерировать картинку
	protected function ModeratePicturesInAlbum() {
		$this->Viewer_SetResponseAjax('json');

		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		// Проверка на существование картинки
		if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById (getRequest ( 'picture_target_id' )))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}

		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( $oPicture->getAlbumId() ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
		}

        if($oAlbum->GetUserIsModerator($this->oUserCurrent) == 0) {
            $this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return;
        }

		// модерировать картинку
		if ($this->PluginPicalbums_Picture_ModerPicture ( getRequest ( 'picture_target_id' ) ) == true) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_moder_image_ok' ), $this->Lang_Get ( 'attention' ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_critical_error' ), $this->Lang_Get ( 'error' ) );
		}
	}

    public function AjaxCopyPicture() {
		$this->Viewer_SetResponseAjax('json');

		// Если пользователь не авторизован
		if (! $this->User_IsAuthorization ()) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'need_authorization' ), $this->Lang_Get ( 'error' ) );
			return false;
		}

		if (! ($oAlbum = $this->PluginPicalbums_Album_GetAlbumById ( getRequest ( 'copy_to_album_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}

		$iCurrentUserId = $this->oUserCurrent->getId ();
		if($iCurrentUserId != $oAlbum->getUserId()) {
            $this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
        }

        if (! ($oPicture = $this->PluginPicalbums_Picture_GetPictureById ( getRequest ( 'copy_picture_id' ) ))) {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
		if($oPicture->getAlbumId() == $oAlbum->getId()) {
            $this->Message_AddErrorSingle ( $this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
        }

		// Формируем картинку
		$oPictureNew = Engine::GetEntity ( 'PluginPicalbums_Picture' );
		$oPictureNew->setAlbumId ( $oAlbum->getId() );
		$oPictureNew->setDescription ( $oPicture->getDescription() );
		$oPictureNew->setURL ( $oPicture->getURL() );
		$oPictureNew->setPicPath ( $oPicture->getPicPath() );
		$oPictureNew->setMiniaturePath ( $oPicture->getMiniaturePath() );
		$oPictureNew->setBlockPath ( $oPicture->getBlockPath() );
		$oPictureNew->setOriginalPath ( $oPicture->getOriginalPath() );
		$oPictureNew->setExif ( $oPicture->getExif() );
		$oPictureNew->setDateAdd ( date ( "Y-m-d H:i:s" ) );
		$oPictureNew->setAddUserId ( $this->oUserCurrent->getId () );
        $oPictureNew->setIsModer(1);

		// Добавляем картинку в альбом
		if (($this->PluginPicalbums_Picture_AddPicture ( $oPictureNew ))) {
			$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_do_copy_ok' ), $this->Lang_Get ( 'attention' ) );
			return true;
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get ( 'system_error' ), $this->Lang_Get ( 'error' ) );
			return false;
		}
	}
	
}
?>
