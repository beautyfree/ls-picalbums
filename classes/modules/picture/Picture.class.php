<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModulePicture extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	// Получение изображения по её идентификатору
	public function GetPictureById($iPictureId) {
		$tag = "album_picture_by_id_{$iPictureId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPictureById ( $iPictureId );
			if($data)
				$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$data->getAlbumId()}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetLastPictureCountByUserId($iUserId, $oDate) {
		return $this->oMapper->GetLastPictureCountByUserId ( $iUserId, $oDate );
	}
	
	public function GetLastPictureId() {
		return $this->oMapper->GetLastPictureId ();
	}
	
	public function GetPicturesCountByUserId($iUserId, $iUserType) {
        $tag = "album_picture_count_by_user_id_{$iUserId}_{$iUserType}";
        if (false === ($data = $this->Cache_Get ( $tag ))) {
            $data = $this->oMapper->GetPicturesCountByUserId ( $iUserType, $iUserId );
            $this->Cache_Set ( $data, $tag, array ("album_user_picture_update_{$iUserId}" ), 60 * 60 * 24 );
        }
		
		return $data;
	}
	
	public function GetPicturesCountByAlbumId($iAlbumId) {
		$tag = "album_picture_count_by_album_id_{$iAlbumId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPicturesCountByAlbumId ( $iAlbumId );
			$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$iAlbumId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetPicturesByArrayId($aArrayId) {
		if (!$aArrayId) {
			return array();
		}
		if (!is_array($aArrayId)) {
			$aArrayId=array($aArrayId);
		}
		$aArrayId=array_unique($aArrayId);
		
		$aPictures=array();
		$aPictureIdNotNeedQuery=array();
		
		// Делаем мульти-запрос к кешу
		$aCacheKeys=func_build_cache_keys($aArrayId,'album_picture_by_id_');
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {
			// проверяем что досталось из кеша
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {
					if ($data[$sKey]) {
						$aPictures[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aPictureIdNotNeedQuery[]=$sValue;
					}
				}
			}
		}

		// Смотрим каких картинок не было в кеше и делаем запрос в БД
		$aPictureIdNeedQuery=array_diff($aArrayId,array_keys($aPictures));
		$aPictureIdNeedQuery=array_diff($aPictureIdNeedQuery,$aPictureIdNotNeedQuery);
		$aPictureIdNeedStore=$aPictureIdNeedQuery;
		if ($data = $this->oMapper->GetPicturesByArrayId($aPictureIdNeedQuery)) {
			foreach ($data as $oPicture) {
				
				// Добавляем к результату и сохраняем в кеш				 
				$aPictures[$oPicture->getId()]=$oPicture;
				$this->Cache_Set($oPicture, "album_picture_by_id_{$oPicture->getId()}", array("album_picture_update_{$oPicture->getAlbumId()}"), 60*60*24*4);
				$aPictureIdNeedStore=array_diff($aPictureIdNeedStore,array($oPicture->getId()));
			}
		}

		// Сохраняем в кеш запросы не вернувшие результата
		foreach ($aPictureIdNeedStore as $iId) {
			$this->Cache_Set(null, "album_picture_by_id_{$iId}", array(), 60*60*24*4);
		}

		// Сортируем результат согласно входящему массиву
		$aPictures=func_array_sort_by_keys($aPictures,$aArrayId);
		return $aPictures;
	}
    
    // Получить неотмодерированные картинки альбома
	public function GetNonModeratedPictureByAlbumId($iAlbumId) {
		$tag = "album_non_moder_picture_by_album_{$iAlbumId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetNonModeratedPictureByAlbumId ( $iAlbumId );
			$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$iAlbumId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	// Получить неотмодерированные картинки альбома конкретного пользователя
	public function GetUserNonModeratedPictureByAlbumId($iAlbumId, $iUserId) {
		$tag = "album_non_moder_user_picture_by_album_{$iAlbumId}_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetUserNonModeratedPictureByAlbumId ( $iAlbumId, $iUserId );
			$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$iAlbumId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
    
    // Получить количество неотмодерированных картинок альбома
    public function GetNonModeratedPicturesCountByAlbumId($iAlbumId) {
		$tag = "album_non_moder_picture_cnt_by_album_{$iAlbumId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetNonModeratedPicturesCountByAlbumId ( $iAlbumId );
			$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$iAlbumId}" ), 60 * 60 * 24 );
		}
		return $data;
	}        
	
	// Получить картинки альбома
	public function GetPictureByAlbumId($iAlbumId) {
		$tag = "album_picture_by_album_{$iAlbumId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPictureByAlbumId ( $iAlbumId );
			$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$iAlbumId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
    // Получить картинки альбома (даже те что находятся на модерации)
	public function GetAllPictureByAlbumId($iAlbumId) {
		$tag = "album_all_picture_by_album_{$iAlbumId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAllPictureByAlbumId ( $iAlbumId );
			$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$iAlbumId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
    // Получить картинки альбома отсортированные по дате добавления
	public function GetPictureByAlbumIdOrderByDate($iAlbumId) {
		$tag = "album_picture_by_album_order_date_{$iAlbumId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPictureByAlbumIdOrderByDate ( $iAlbumId );
			$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$iAlbumId}" ), 60 * 60 * 24 );
		}
		return $data;
	}	
	
	public function GetLimitPictureByAlbumId($iAlbumId, $startPos, $iLimit) {
		$data = $this->GetPictureByAlbumIdOrderByDate($iAlbumId);
		if(!$data)
			return null;
		$aPictures = array();
		$i = 0;
		foreach ($data as $oPicture) {
			if($startPos > $i) {
				$i++;
				continue;
			}
			$i++;
			$aPictures[] = $oPicture;
			
			if($i == ($iLimit + $startPos)) {
				return $aPictures;
			}
		}
		
		return $aPictures;
	}
	
	// Получить последние картинки
	public function GetLastPictures($bIsAuth, $iLimit) {
        $iIsAuth = $bIsAuth ? "1" : "0";
        $tag = "album_last_pictures_{$iLimit}_{$iIsAuth}";

        if (false === ($data = $this->Cache_Get ( $tag ))) {
            $data = $this->oMapper->GetLastPictures ( $iLimit, $bIsAuth );
            $this->Cache_Set ( $data, $tag, array ("album_all_pictures"), 60 * 60 * 24 );
        }
        return $data;
	}
	
	// Получить последние лучшие картинки конкретного пользователя
	public function GetLastBestPicturesByUserProfile($bIsAuth, $iUserId, $iLimit) {
        $iIsAuth = $bIsAuth ? "1" : "0";
        $tag = "album_best_by_user_pictures_auth_{$iUserId}_{$iLimit}_{$iIsAuth}";
        if (false === ($data = $this->Cache_Get ( $tag ))) {
            $data = $this->oMapper->GetLastBestPicturesByUserProfile ( $iUserId, $iLimit, $bIsAuth );
            $this->Cache_Set ( $data, $tag, array ("album_all_pictures","album_best_pictures","album_user_picture_update_{$iUserId}"), 60 * 60 * 24 );
        }
        return $data;

	}
	
	// Получить последние лучшие картинки конкретного пользователя в альбоме
	public function GetLastBestPicturesByUserProfileInAlbum($bIsAuth, $iUserId, $iAlbumId, $iLimit) {
        $iIsAuth = $bIsAuth ? "1" : "0";
		$tag = "album_best_by_user_pictures_in_album_block_{$iUserId}_{$iLimit}_{$iAlbumId}_{$iIsAuth}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetLastBestPicturesByUserProfileInAlbum ($iUserId, $iAlbumId, $iLimit, $bIsAuth);
			$this->Cache_Set ( $data, $tag, array ("album_all_pictures","album_best_pictures","album_user_picture_update_{$iUserId}"), 60 * 60 * 24 );
		}
		return $data;
	}
	
	// Получить последние комментируемые картинки конкретного пользователя
	public function GetLastCommentedPicturesByUserProfile($bIsAuth, $iUserId, $iLimit) {
        $iIsAuth = $bIsAuth ? "1" : "0";
		$tag = "album_commented_by_user_pictures_{$iUserId}_{$iLimit}_{$iIsAuth}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetLastCommentedPicturesByUserProfile ($iUserId, $iLimit, $bIsAuth);
			$this->Cache_Set ( $data, $tag, array ( "comment_new_picalbums", 
													"comment_update_status_picalbums", 
													"album_user_picture_update_{$iUserId}"), 60 * 60 * 24 );
		}
		return $data;
	}
	
	// Получить последние лучшие картинки 
	public function GetLastBestPictures($bIsAuth, $iLimit) {
        $iIsAuth = $bIsAuth ? "1" : "0";
		$tag = "album_best_pictures_{$iLimit}_{$iIsAuth}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetLastBestPictures ( $iLimit, $bIsAuth );
			$this->Cache_Set ( $data, $tag, array ("album_all_pictures","album_best_pictures"), 60 * 60 * 24 );
		}
		return $data;
	}
	
	// Получить последние лучшие картинки за определенную дату
	public function GetLastBestPicturesByDate($bIsAuth, $oDate, $iLimit) {
        $iIsAuth = $bIsAuth ? "1" : "0";
		$tag = "album_best_pictures_{$oDate}_{$iLimit}_{$iIsAuth}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetLastBestPicturesByDate ( $oDate, $iLimit, $bIsAuth );
			$this->Cache_Set ( $data, $tag, array ("album_all_pictures","album_best_pictures"), 60 * 60 * 24 );
		}
		return $data;
	}
	
	private function GetAllPicturesIds($bIsAuth) {
        $iIsAuth = $bIsAuth ? "1" : "0";
		$tag = "album_all_pictures_ids_{$iIsAuth}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAllPicturesIds ( $bIsAuth );
			$this->Cache_Set ( $data, $tag, array ("album_all_pictures"), 60 * 60 * 24 );
		}
		return $data;
	}
	// Получить случайные картинки 
	public function GetRandomPictures($bIsAuth, $iLimit) {
		$aPictureIds = $this->GetAllPicturesIds($bIsAuth);
			
		if(!$aPictureIds)
			return false;

        shuffle($aPictureIds);
		return $this->GetPicturesByArrayId(array_slice($aPictureIds, 0, $iLimit));
	}
	
	// Получить последнюю картинку альбома
	public function GetLastPictureByAlbumId($iAlbumId) {
		$aPictures = $this->GetPictureByAlbumId($iAlbumId);
		
		if(!$aPictures)
			return null;
		if(count($aPictures) == 0)
			return null;
		return $aPictures[0];
	}
	
	// Получение картинки по её адрессу
	public function GetPictureByURL($iAlbumId, $sPictureURL) {
		$tag = "album_picture_by_url_{$iAlbumId}_{$sPictureURL}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPictureByURL ( $iAlbumId, $sPictureURL );
			$this->Cache_Set ( $data, $tag, array ("album_picture_update_{$iAlbumId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetNextPrev($iAlbumId, $sPictureURL) {
		$aPictures = $this->GetPictureByAlbumId($iAlbumId);
		
		if($aPictures)
			foreach ($aPictures as $index => $picture) {
				if($picture->getURL() == $sPictureURL) {
					if($index == 0)
						$prev = null;
					else
						$prev = $aPictures[$index - 1]->getURL();

					if($index == (count($aPictures) - 1))
						$next = null;
					else
						$next = $aPictures[(int)$index + 1]->getURL();
						
					$arr = array();
					$arr['prev'] = $prev;
					$arr['next'] = $next;
					return $arr;
				}
			}
		return null;
	}
	
	public function GetCurrentAndLastPosition($iAlbumId, $sPictureURL) {
		$aPictures = $this->GetPictureByAlbumId($iAlbumId);
		if($aPictures)
			foreach ($aPictures as $index => $picture) {
				if($picture->getURL() == $sPictureURL) {
					$arr = array();
					
					$arr['current'] = ((int)$index) + 1;
					$arr['last'] = count($aPictures);
					return $arr;
				}
			}
		return null;
	}

    public function GetAllPicturesCount($bIsAuth) {
        $iIsAuth = $bIsAuth ? "1" : "0";
        $tag = "pictures_all_count_{$iIsAuth}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAllPicturesCount ( $iIsAuth );
			$this->Cache_Set ( $data, $tag, array ("album_all_pictures"), 60 * 60 * 24 );
		}
		return $data;
	}

    public function GetAllPicturesLimit($bIsAuth, &$iCount,  $iCurrPage, $iPerPage) {
        $iIsAuth = $bIsAuth ? "1" : "0";
        $tag = "pictures_all_limit_{$iIsAuth}_{$iCurrPage}_{$iPerPage}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAllPicturesLimit ( $bIsAuth, $iCount,  $iCurrPage, $iPerPage );
			$this->Cache_Set ( $data, $tag, array ("album_all_pictures"), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function SortPictures($aPictures) {
		$iAlbumId = null; 
		foreach ($aPictures as $key => $value) {
			$this->oMapper->UpdateOrderPos($key, $value);
			
			if(!$iAlbumId && ($oPicture1 = $this->GetPictureById($key))) {
				$iAlbumId = $oPicture1->getAlbumId();
			}
		}
		
		if($iAlbumId)
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_picture_update_{$iAlbumId}" ) );
			
		return true;
	}
	
	// Добавление картинки
	public function AddPicture($oPicture) {
		if ($oId=$this->oMapper->AddPicture ( $oPicture )) {
			$this->oMapper->UpdatePosition($oId, $oId);
			$this->PluginPicalbums_Album_UpdateDateModify($oPicture->getAlbumId(), date ( "Y-m-d H:i:s" ));

            $this->ClearCache($oPicture->getAlbumId());
			$oPicture->setId($oId);
			return $oId;
		}
		return false;
	}


	
	// Редактирвоание картинки
	public function EditPicture($iPictureId, $sTitle, $oUrl) {
		return $this->oMapper->EditPicture ( $iPictureId, $sTitle, $oUrl );
	}
	
	public function ClearCache($iAlbumId) {
		$arr = Array();
		$arr[] = "album_main";
		$arr[] = "album_all_pictures";
		$arr[] = "album_best_pictures";
		$arr[] = "album_picture_update_{$iAlbumId}";
		$oAlbum = $this->PluginPicalbums_Album_GetAlbumById($iAlbumId);
		if($oAlbum) {
			$arr[] = "album_user_picture_update_{$oAlbum->getUserId()}";
			$arr[] = "album_update_{$oAlbum->getUserId()}";
		}
		
		$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, $arr );
	}
	
	// Удаление альбома по его идентификатору
	public function DeletePicture($iPictureId) {
		
		$oPicture = $this->GetPictureById($iPictureId);
		if ($this->oMapper->DeletePicture ( $iPictureId )) {

            $this->ClearCache($oPicture->getAlbumId());
			if($oPicture) {
				$this->DeletePictureFromFS($oPicture->getPicPath());
				$this->DeletePictureFromFS($oPicture->getMiniaturePath());
				$this->DeletePictureFromFS($oPicture->getBlockPath());
				$this->DeletePictureFromFS($oPicture->getOriginalPath());
			}
			
			$this->PluginPicalbums_Note_DeleteNoteByPictureId($iPictureId);
			$this->PluginPicalbums_Heart_DeleteHeartByTargetId($iPictureId);
			$this->Comment_DeleteCommentByTargetId($iPictureId, 'picalbums');
			
			return true;
		}
		return false;
	}
	
	
	private function DeletePictureFromFS($WebPath) {
		$pos = strpos($WebPath, Config::Get('path.uploads.root'));
		if(gettype($pos) == 'integer') {
			$forremove = Config::Get('path.root.server') . substr($WebPath, $pos);
			@unlink($forremove);
		}
	}

    public function ModerPicture($iPictureId) {
		$oPicture = $this->GetPictureById($iPictureId);
		if ($this->oMapper->ModerPicture ( $iPictureId )) {
            $this->ClearCache($oPicture->getAlbumId());
			return true;
		}
		return false;
	}
}
?>
