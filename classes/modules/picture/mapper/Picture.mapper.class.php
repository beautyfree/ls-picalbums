<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * @LiveStreet Version: 0.5.1
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModulePicture_MapperPicture extends Mapper {
	
	// Получение картинки по её идентификатору
	public function GetPictureById($iPictureId) {
		$sql = 	"SELECT a.* ".
				"FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " a " .
				"WHERE a.picture_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iPictureId )) {
			return Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
		}
		return false;
	}
	
	public function GetLastPictureCountByUserId($iUserId, $oDate) {
		$sql = 	" SELECT COUNT(p.picture_id) as piccnt
				  FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a, " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p
				  WHERE a.album_id = p.album_id AND a.user_id = ?d AND p.date_add > ? AND p.ismoder = 1 ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId, $oDate )) {
			return $aRow['piccnt'];
		}
		return false;
	}

	public function GetPicturesCountByUserId($iUserType, $iUserId) {
        if($iUserType == 0)
            $sCondition = "AND a.visibility = 0";
        elseif ($iUserType == 1)
            $sCondition = "AND a.visibility in (0,1)";
        else
            $sCondition = "";

		$sql = 	" SELECT count(*) as piccnt ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . 
						  Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				" WHERE a.album_id = p.album_id and a.user_id = ?d AND p.ismoder = 1 $sCondition ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId )) {
			return $aRow['piccnt'];
		}
		return false;
	}
	
	public function GetPicturesCountByAlbumId($iAlbumId) {
		$sql = 	" SELECT count(*) as piccnt ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p " .
				" WHERE p.album_id = ?d AND p.ismoder = 1 ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iAlbumId )) {
			return $aRow['piccnt'];
		}
		return false;
	}
	
	public function GetPicturesByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}

		$sql = "SELECT
					a.*
				FROM
					".Config::Get('plugin.picalbums.table.picture')." as a
				WHERE
					a.picture_id IN(?a)
				ORDER BY a.date_add desc ";
		$aPictures=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId)) {
			foreach ($aRows as $aPicture) {
				$aPictures[]=Engine::GetEntity('PluginPicalbums_Picture',$aPicture);
			}
		}
		return $aPictures;
	}
	
	// Получение картинки по её адрессу
	public function GetPictureByURL($iAlbumId, $oPictureURL) {
		$sql = 	"SELECT a.* ".
				"FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " a " .
				"WHERE a.album_id = ?d and a.url = ? ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iAlbumId, $oPictureURL )) {
			return Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
		}
		return false;
	}
    
    // Получить количество неотмодерированны[ картинок альбома
	public function GetNonModeratedPicturesCountByAlbumId($iAlbumId) {
		$sql = 	" SELECT count(*) as piccnt ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p " .
				" WHERE p.album_id = ?d AND p.ismoder = 0 ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iAlbumId )) {
			return $aRow['piccnt'];
		}
		return false;
	}    
    // Получить неотмодерированные картинки альбома
	public function GetNonModeratedPictureByAlbumId($iAlbumId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " a " .
				"WHERE a.album_id = ?d AND a.ismoder = 0 ".
				"ORDER BY a.position, a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iAlbumId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
	// Получить неотмодерированные картинки альбома конкретного пользователя
	public function GetUserNonModeratedPictureByAlbumId($iAlbumId, $iUserId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " a " .
				"WHERE a.album_id = ?d AND a.ismoder = 0 AND a.adduser_id = ?d ".
				"ORDER BY a.position, a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iAlbumId, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
	// Получить картинки альбома
	public function GetPictureByAlbumId($iAlbumId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " a " .
				"WHERE a.album_id = ?d AND a.ismoder = 1 ".
				"ORDER BY a.position, a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iAlbumId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
    // Получить картинки альбома (даже те что находятся на модерации
    public function GetAllPictureByAlbumId($iAlbumId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " a " .
				"WHERE a.album_id = ?d ".
				"ORDER BY a.position, a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iAlbumId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
	// Получить картинки альбома отсортированные по дате добавления
	public function GetPictureByAlbumIdOrderByDate($iAlbumId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " a " .
				"WHERE a.album_id = ?d AND a.ismoder = 1 ".
				"ORDER BY a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iAlbumId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
	
	// Получить последние картинки для неавторизированного пользователя
	public function GetLastPictures($iLimit, $bIsAuth) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT p.* " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				" WHERE p.album_id = a.album_id AND a.visibility $sCondition AND p.ismoder = 1 " .
				" ORDER BY p.date_add desc limit 0, ?d ";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	// Получить последние лучшие картинки для неавторизированного пользователя
	public function GetLastBestPictures($iLimit, $bIsAuth) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT p.* " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				" WHERE p.album_id = a.album_id AND a.visibility $sCondition AND p.ismoder = 1 " .
				" ORDER BY (SELECT COUNT(*) FROM ". Config::Get ( 'plugin.picalbums.table.heart' ) . " h WHERE h.target_id = p.picture_id) DESC limit 0, ?d ";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	// Получить последние лучшие картинки за определенную дату для неавторизированного пользователя
	public function GetLastBestPicturesByDate($oDate, $iLimit, $bIsAuth) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT p.* " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				" WHERE p.date_add > ? AND p.album_id = a.album_id AND a.visibility $sCondition AND p.ismoder = 1 " .
				" ORDER BY (SELECT COUNT(*) FROM ". Config::Get ( 'plugin.picalbums.table.heart' ) . " h WHERE h.target_id = p.picture_id) DESC limit 0, ?d ";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $oDate, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	// Получить последние лучшие картинки конкретного пользователя для неавторизированного пользователя
	public function GetLastBestPicturesByUserProfile($iUserId, $iLimit, $bIsAuth) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT p.* " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a WHERE p.album_id = a.album_id AND a.visibility $sCondition AND a.user_id = ?d AND p.ismoder = 1 " .
				" ORDER BY (SELECT COUNT(*) FROM ". Config::Get ( 'plugin.picalbums.table.heart' ) . " h WHERE h.target_id = p.picture_id) DESC limit 0, ?d ";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	// Получить последние лучшие картинки конкретного пользователя для неавторизированного пользователя
	public function GetLastBestPicturesByUserProfileInAlbum($iUserId, $iAlbumId, $iLimit, $bIsAuth) {
		if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

        $sql = 	" SELECT p.* " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a WHERE p.album_id = a.album_id AND p.album_id = ?d AND a.visibility $sCondition AND a.user_id = ?d AND p.ismoder = 1 " .
				" ORDER BY (SELECT COUNT(*) FROM ". Config::Get ( 'plugin.picalbums.table.heart' ) . " h WHERE h.target_id = p.picture_id) DESC limit 0, ?d ";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iAlbumId, $iUserId, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	// Получить последние комментируемые картинки конкретного пользователя для неавторизированного пользователя
	public function GetLastCommentedPicturesByUserProfile($iUserId, $iLimit, $bIsAuth) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT p.* " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a WHERE p.album_id = a.album_id AND a.visibility $sCondition AND a.user_id = ?d AND p.ismoder = 1 " .
				" ORDER BY (SELECT COUNT(*) FROM ". Config::Get ( 'db.table.comment' ) . " c WHERE c.target_id = p.picture_id AND c.target_type = 'picalbums') DESC limit 0, ?d ";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	// Получить идентификаторы всех картинок для неавторизированного пользователя
	public function GetAllPicturesIds($bIsAuth) {
        if($bIsAuth)
            $sCond = "in (0, 1)";
        else
            $sCond = "= 0";

		$sql = 	" SELECT p.picture_id " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				" WHERE p.album_id = a.album_id AND a.visibility $sCond AND p.ismoder = 1 ";

		if($aRows = $this->oDb->selectCol($sql)) {
           return $aRows;
        }
		return false;
	}
		
	public function GetLastPictureId() {
		$sql = 	" SELECT MAX(p.picture_id) as picmax
				  FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p ";
				
		if ($aRow = $this->oDb->selectRow ( $sql )) {
			return $aRow['picmax'];
		}
		return false;
	}

    public function GetAllPicturesCount($bIsAuth) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT COUNT(p.picture_id) as picturecnt  " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .  
				" WHERE p.album_id = a.album_id AND a.visibility $sCondition ";

		if ($aRow = $this->oDb->selectRow ( $sql )) {
			return $aRow['picturecnt'];
		}
		return false;
	}

    public function GetAllPicturesLimit($bIsAuth, &$iCount,  $iCurrPage, $iPerPage) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT p.*, a.url as albumurl, (SELECT u.user_login FROM " .Config::Get('db.table.user') . " u WHERE u.user_id = a.user_id) as userlogin " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				" WHERE p.album_id = a.album_id AND a.visibility $sCondition ORDER BY date_add desc LIMIT ?d, ?d";

		$aReturn = array ();
		if ($aRows = $this->oDb->selectPage ( $iCount, $sql, ($iCurrPage - 1) * $iPerPage, $iPerPage) ) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Picture', $aRow );
			}
			return $aReturn;
		}
		return false;
	}

     public function GetPicturesCountByPicPath($sPath) {
		$sql = 	" SELECT COUNT(p.picture_id) as picturecnt  " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p " .
				" WHERE p.picpath = ? ";

		if ($aRow = $this->oDb->selectRow ( $sql, $sPath )) {
			return $aRow['picturecnt'];
		}
		return false;
	}

    public function GetPicturesCountByPicPathAndAlbumId($sPath, $iAlbumId) {
		$sql = 	" SELECT COUNT(p.picture_id) as picturecnt  " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p " .
				" WHERE p.picpath = ? AND p.album_id = ?d ";

		if ($aRow = $this->oDb->selectRow ( $sql, $sPath, $iAlbumId )) {
			return $aRow['picturecnt'];
		}
		return false;
	}
	
	// Добавление картинки
	public function AddPicture($oPicture) {
		$sql = "INSERT INTO " . Config::Get ( 'plugin.picalbums.table.picture' ) . "
												(	album_id,
											        description,
													url,
													picpath,
													picminiaturepath,
													picblockpath,
											        date_add,
													exif,
											        originalpath,
													adduser_id,
													ismoder
												)
												VALUES (?d, ?, ?, ?, ?, ?, ?, ?, ?, ?d, ?d) ";
		
		if (($iId = $this->oDb->query ( $sql,$oPicture->getAlbumId (), 
											$oPicture->getDescription(), 
											$oPicture->getURL(), 
											$oPicture->getPicPath(), 
											$oPicture->getMiniaturePath(), 
											$oPicture->getBlockPath(), 
											$oPicture->getDateAdd (),
											$oPicture->getExif (),
											$oPicture->getOriginalPath (),
											$oPicture->getAddUserId (),
                                            $oPicture->getIsModer ()))) {
			return $iId;
		}
		
		return false;
	}
	
	// Редактирование картинки
	public function EditPicture($iPictureId, $sTitle, $oUrl) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.picture' ) . "
												SET 
											        description = ?,
													url = ?
												WHERE picture_id = ?d ";
		
		if (($iId = $this->oDb->query ( $sql,$sTitle, $oUrl, $iPictureId ))) {
			return $iId;
		}
		
		return false;
	}
	
	public function UpdatePosition($iPictureId, $newPos) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.picture' ) . "
												SET 
													position = ?
												WHERE picture_id = ?d ";
		
		if (($iId = $this->oDb->query ( $sql, $newPos, $iPictureId ))) {
			return $iId;
		}
		
		return false;
	}
	
	// Удаление картинки по его идентификатору
	public function DeletePicture($iPictureId) {
		$sql = "DELETE FROM " . Config::Get ( 'plugin.picalbums.table.picture' ) . " WHERE picture_id = ?d ";
		if ($this->oDb->query ( $sql, $iPictureId )) {
			return true;
		}
		return false;
	}
	
	public function UpdateOrderPos($iPictureId, $newPos) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.picture' ) . "
												SET 
													position = ?
												WHERE picture_id = ?d ";
		
		if (($iId = $this->oDb->query ( $sql, $newPos, $iPictureId ))) {
			return $iId;
		}
		
		return false;
	}
    
    public function ModerPicture($iPictureId) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.picture' ) . " SET ismoder = 1 WHERE picture_id = ?d ";
		if ($this->oDb->query ( $sql, $iPictureId )) {
			return true;
		}
		return false;
	}
}
?>
