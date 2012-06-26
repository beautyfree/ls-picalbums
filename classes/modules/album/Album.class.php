<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleAlbum extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	// Получение альбома по её идентификатору
	public function GetAlbumById($iAlbumId) {
		$tag = "album_by_id_{$iAlbumId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAlbumById ( $iAlbumId );
			if($data != false)
				$this->Cache_Set ( $data, $tag, array ("album_update_{$data->getUserId()}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetAlbumsByArrayId($aArrayId) {
		if (!$aArrayId) {
			return array();
		}
		if (!is_array($aArrayId)) {
			$aArrayId=array($aArrayId);
		}
		$aArrayId=array_unique($aArrayId);
		
		$aAlbums=array();
		$aAlbumIdNotNeedQuery=array();
		
		// Делаем мульти-запрос к кешу
		$aCacheKeys=func_build_cache_keys($aArrayId,'album_by_id_');
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {
			// проверяем что досталось из кеша
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {
					if ($data[$sKey]) {
						$aAlbums[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aAlbumIdNotNeedQuery[]=$sValue;
					}
				}
			}
		}

		// Смотрим каких альбомов не было в кеше и делаем запрос в БД
		$aAlbumIdNeedQuery=array_diff($aArrayId,array_keys($aAlbums));
		$aAlbumIdNeedQuery=array_diff($aAlbumIdNeedQuery,$aAlbumIdNotNeedQuery);
		$aAlbumIdNeedStore=$aAlbumIdNeedQuery;
		if ($data = $this->oMapper->GetAlbumsByArrayId($aAlbumIdNeedQuery)) {
			foreach ($data as $oAlbum) {
				
				// Добавляем к результату и сохраняем в кеш				 
				$aAlbums[$oAlbum->getId()]=$oAlbum;
				$this->Cache_Set($oAlbum, "album_by_id_{$oAlbum->getId()}", array("album_update_{$oAlbum->getUserId()}"), 60*60*24);
				$aAlbumIdNeedStore=array_diff($aAlbumIdNeedStore,array($oAlbum->getId()));
			}
		}

		// Сохраняем в кеш запросы не вернувшие результата
		foreach ($aAlbumIdNeedStore as $iId) {
			$this->Cache_Set(null, "album_by_id_{$iId}", array(), 60*60*24);
		}

		// Сортируем результат согласно входящему массиву
		$aAlbums=func_array_sort_by_keys($aAlbums,$aArrayId);
		return $aAlbums;
	}
	
	public function GetAlbumCountByUserId($iUserId, $iUserType) {
        $tag = "album_count_by_userid_{$iUserId}_{$iUserType}";
        if (false === ($data = $this->Cache_Get ( $tag ))) {
            $data = $this->oMapper->GetAlbumCountByUserId ( $iUserType, $iUserId );
            $this->Cache_Set ( $data, $tag, array ("album_update_{$iUserId}"), 60 * 60 * 24 );
        }
		return $data;
	}
	
	// Получение альбома по ЧПУ
	public function GetAlbumByURL($iAlbumId, $sUrl) {
		$tag = "album_by_url_{$iAlbumId}_{$sUrl}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAlbumByURL ( $iAlbumId, $sUrl );
			if($data != false)
				$this->Cache_Set ( $data, $tag, array ("album_update_{$data->getUserId()}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	// Получить альбомы пользователя
	public function GetAlbumsByUserId($iUserId) {
		$tag = "album_by_user_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAlbumsByUserId ( $iUserId );
			$this->Cache_Set ( $data, $tag, array ("album_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}

    // Получить альбомы по тегу
    public function GetAlbumsByTag($sTag) {
		$tag = "album_by_tag_{$sTag}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAlbumsByTag($sTag) ;
			$this->Cache_Set ( $data, $tag, array ("album_main","tag_update", ), 60 * 60 * 24 );
		}
		return $data;
	}

    public function GetAlbumsAppendedByUserId($iUserId) {
		return $this->oMapper->GetAlbumsAppendedByUserId ( $iUserId );
	}
	
	public function GetPrivateAlbumsByAllUsers($iVirtualUserId) {
		$tag = "album_private_all_user_{$iVirtualUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetPrivateAlbumsByAllUsers ( $iVirtualUserId );
			$this->Cache_Set ( $data, $tag, array ("album_update_{$iVirtualUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}	
	
	public function GetNonCategoryAlbumsByUserId($iUserId) {
		$tag = "album_by_user_noncategory_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetNonCategoryAlbumsByUserId ( $iUserId );
			$this->Cache_Set ( $data, $tag, array ("album_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	// Получить альбомы пользователя по категории
	public function GetAlbumsByUserIdAndCategoryId($iUserId, $iCategoryId) {
		$tag = "album_by_user_category_{$iUserId}_{$iCategoryId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAlbumsByUserIdAndCategoryId($iUserId, $iCategoryId);
			$this->Cache_Set ( $data, $tag, array ("album_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	// Получить последние комментируемые альбомы конкретного пользователя
	public function GetLastCommentedAlbumsByUserProfile($bIsAuth, $iUserId, $iLimit) {
        $iIsAuth = $bIsAuth ? "1" : "0";
        $tag = "album_commented_by_user_albums_{$iUserId}_{$iLimit}_{$iIsAuth}";
        if (false === ($data = $this->Cache_Get ( $tag ))) {
            $data = $this->oMapper->GetLastCommentedAlbumsByUserProfile ( $iUserId, $iLimit, $bIsAuth );
            $this->Cache_Set ( $data, $tag, array ( "comment_new_picalbums", "comment_update_status_picalbums", "album_update_{$iUserId}"), 60 * 60 * 24 );
        }

		return $data;
	}
	
	public function GetAlbumsModifySortByUserId($iUserId) {
		$tag = "album_by_user_modify_sort_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetAlbumsModifySortByUserId ( $iUserId );
			$this->Cache_Set ( $data, $tag, array ("album_update_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	// Получить последние альбомы
	public function GetLastAlbums($bIsAuth, $iLimit) {
        $iIsAuth = $bIsAuth ? "1" : "0";
        $tag = "album_last_album_{$iLimit}_{$iIsAuth}";
        if (false === ($data = $this->Cache_Get ( $tag ))) {
            $data = $this->oMapper->GetLastAlbums ( $iLimit, $bIsAuth );
            $this->Cache_Set ( $data, $tag, array ("album_main"), 60 * 60 * 24 );
        }

		return $data;
	}

    public function GetAllAlbumsCount($bIsAuth) {
        $iIsAuth = $bIsAuth ? "1" : "0";
        $tag = "album_all_album_cnt_{$iIsAuth}";
        if (false === ($data = $this->Cache_Get ( $tag ))) {
            $data = $this->oMapper->GetAllAlbumsCount ( $bIsAuth );
            $this->Cache_Set ( $data, $tag, array ("album_main"), 60 * 60 * 24 );
        }

		return $data;
	}

    public function GetAllAlbumsLimit($bIsAuth, &$iCount,  $iCurrPage, $iPerPage) {
        $iIsAuth = $bIsAuth ? "1" : "0";
        $tag = "album_all_albums_{$iIsAuth}_{$iCurrPage}_{$iPerPage}";
        if (false === ($data = $this->Cache_Get ( $tag ))) {
            $data = $this->oMapper->GetAllAlbumsLimit($bIsAuth, $iCount,  $iCurrPage, $iPerPage);
            $this->Cache_Set ( $data, $tag, array ("album_main"), 60 * 60 * 24 );
        }

		return $data;
	}
	
	// Добавление альбома
	public function AddAlbum($oAlbum) {
		if ($oId=$this->oMapper->AddAlbum ( $oAlbum )) {
			$this->UpdateDateModify($oId, $oAlbum->getDateAdd());
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_main", 
																				"album_update_{$oAlbum->getUserId()}" ) );
			$oAlbum->setId($oId);
			return $oId;
		}
		return false;
	}
	
	public function UpdateDateModify($iAlbumId, $oDate) {
		return $this->oMapper->UpdateDateModify($iAlbumId, $oDate);
	}
	
	public function UpdateCoverPicture($iAlbumId, $iPictureId) {		
		$this->UpdateDateModify($iAlbumId, date ( "Y-m-d H:i:s" ));
		return $this->oMapper->UpdateCoverPicture($iAlbumId, $iPictureId);
	}
	
	// Редактирвоание альбома
	public function EditAlbum($iAlbumId, $sTitle, $sDesc, $sUrl, $bVisibility, $iCategoryId, $bNeedModer) {
		$oAlbum = $this->GetAlbumById($iAlbumId);
		if ($oId=$this->oMapper->EditAlbum($iAlbumId, $sTitle, $sDesc, $sUrl, $bVisibility, $iCategoryId, $bNeedModer)) {
			$this->UpdateDateModify($iAlbumId, date ( "Y-m-d H:i:s" ));
			if($oAlbum)
				$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_main", 
																					"album_update_{$oAlbum->getUserId()}" ) );
			return $oId;
		}
		return false;
	}
	
	// Удаление альбома по его идентификатору
	public function DeleteAlbum($iAlbumId) {

		$oAlbum = $this->GetAlbumById($iAlbumId);
		if ($this->oMapper->DeleteAlbum ( $iAlbumId )) {
			if($oAlbum != false)
				$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_main", 
																					"album_picture_update_{$iAlbumId}", 
																					"album_user_picture_update_{$oAlbum->getUserId()}",
																					"album_update_{$oAlbum->getUserId()}" ) );
				
			$aPictures = $this->PluginPicalbums_Picture_GetPictureByAlbumId($iAlbumId);
			if($aPictures) {
				foreach($aPictures as $picture) {
					$this->PluginPicalbums_Picture_DeletePicture($picture->getId());
				}
			}

			$this->PluginPicalbums_Tag_DeleteTagsByTargetId ( $iAlbumId );
			return true;
		}
		return false;
	}
    
    public function ModerateAll($iAlbumId) {
		$oAlbum = $this->GetAlbumById($iAlbumId);
		if ($this->oMapper->ModerateAll ( $iAlbumId )) {
			if($oAlbum != false)
				$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_main", 
																					"album_picture_update_{$iAlbumId}", 
																					"album_user_picture_update_{$oAlbum->getUserId()}",
																					"album_update_{$oAlbum->getUserId()}" ) );
			return true;
		}
		return false;
	}
}
?>
