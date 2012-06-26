<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleAlbum_MapperAlbum extends Mapper {
	
	// Получение альбома по её идентификатору
	public function GetAlbumById($iAlbumId) {
		$sql = 	"SELECT a.* ".
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.album_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iAlbumId )) {
			return Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
		}
		return false;
	}
	
	public function GetAlbumsByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}

		$sql = "SELECT
					a.*
				FROM
					".Config::Get('plugin.picalbums.table.album')." as a
				WHERE
					a.album_id IN(?a)
				ORDER BY a.date_add desc ";
		$aAlbums=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId)) {
			foreach ($aRows as $aAlbum) {
				$aAlbums[]=Engine::GetEntity('PluginPicalbums_Album',$aAlbum);
			}
		}
		return $aAlbums;
	}
	
	// Получение альбома по ЧПУ
	public function GetAlbumByURL($iUserId, $sUrl) {		
		$sql = 	"SELECT a.* ".
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.user_id = ?d AND a.url = ? ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId, $sUrl )) {
			return Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
		}
		return false;
	}
	
	// Получить альбомы всех пользователей
	public function GetPrivateAlbumsByAllUsers($iVirtualUserId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.user_id != ?d ".
				"ORDER BY a.date_modify desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iVirtualUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
	
	
	// Получить альбомы пользователя
	public function GetAlbumsByUserId($iUserId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.user_id = ?d ".
				"ORDER BY a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
    // Получить альбомы по тегу
    public function GetAlbumsByTag($sTag) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a, " .
                          Config::Get ( 'plugin.picalbums.table.tag' ) . " t " .
				"WHERE a.album_id = t.target_id AND t.tag_text = ? ".
				"ORDER BY a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $sTag )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return null;
	}

    public function GetAlbumsAppendedByUserId($iUserId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.adduser_id = ?d ".
				"ORDER BY a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
	
	public function GetAlbumsByUserIdAndCategoryId($iUserId, $iCategoryId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.user_id = ?d AND a.category_id = ?d ".
				"ORDER BY a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId, $iCategoryId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
	
	public function GetNonCategoryAlbumsByUserId($iUserId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.user_id = ?d AND (a.category_id is NULL OR a.category_id NOT IN (SELECT category_id FROM " . Config::Get ( 'plugin.picalbums.table.category' ) . " ) )".
				"ORDER BY a.date_add desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return null;
	}
	
	public function GetAlbumsModifySortByUserId($iUserId) {
		$sql = 	"SELECT a.* " .
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.user_id = ?d ".
				"ORDER BY a.date_modify desc";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return null;
	}

	public function GetAlbumCountByUserId($iUserType, $iUserId) {
        if($iUserType == 0)
            $sCondition = "AND a.visibility = 0";
        elseif ($iUserType == 1)
            $sCondition = "AND a.visibility in (0,1)";
        else
            $sCondition = "";

		$sql = 	"SELECT count(*) as albumcount ".
				"FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				"WHERE a.user_id = ?d $sCondition ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId )) {
			return $aRow["albumcount"];
		}
		return false;
	}
	
	// Получить последние комментируемые альбомы конкретного пользователя для авторизированного пользователя
	public function GetLastCommentedAlbumsByUserProfile($iUserId, $iLimit, $bIsAuth) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT a.* " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a WHERE a.visibility $sCondition AND a.user_id = ?d " .
				" ORDER BY (SELECT COUNT(c.comment_id) FROM ". Config::Get ( 'db.table.comment' ) . " c, " . Config::Get ( 'plugin.picalbums.table.picture' ) . " p WHERE a.album_id = p.album_id AND c.target_id = p.picture_id AND c.target_type = 'picalbums') DESC limit 0, ?d ";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	// Получить последние альбомы для неавторизированного пользователя
	public function GetLastAlbums($iLimit, $bIsAuth) {
		if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

        $sql = 	" SELECT a.* " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				" WHERE a.visibility $sCondition " .
				" ORDER BY a.date_add desc limit 0, ?d ";

		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iLimit )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return false;
	}

    public function GetAllAlbumsCount($bIsAuth) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT COUNT(album_id) as albumcnt  " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.album' ) .
				" WHERE visibility $sCondition ";

		if ($aRow = $this->oDb->selectRow ( $sql )) {
			return $aRow['albumcnt'];
		}
		return false;
	}

    public function GetAllAlbumsLimit($bIsAuth, &$iCount,  $iCurrPage, $iPerPage) {
        if($bIsAuth)
            $sCondition = "in (0,1)";
        else
            $sCondition = "= 0";

		$sql = 	" SELECT * " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.album' )  .
				" WHERE visibility $sCondition ORDER BY date_add desc LIMIT ?d, ?d";

		$aReturn = array ();
		if ($aRows = $this->oDb->selectPage ( $iCount, $sql, ($iCurrPage - 1) * $iPerPage, $iPerPage) ) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Album', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	// Добавление альбома
	public function AddAlbum($oAlbum) {
		$sql = "INSERT INTO " . Config::Get ( 'plugin.picalbums.table.album' ) . "
												(	user_id,
													adduser_id,
											        description,
													title,
													url,
											        date_add,
													visibility,
													category_id,
													needmoder
												)
												VALUES (?d, ?d, ?, ?, ?, ?, ?d, ?d, ?d) ";
		
		if (($iId = $this->oDb->query ( $sql,$oAlbum->getUserId (),
											$oAlbum->getAddUserId (),  
											$oAlbum->getDescription(), 
											$oAlbum->getTitle(),
											$oAlbum->getURL(),  
											$oAlbum->getDateAdd(),
											$oAlbum->getVisibility(),
											$oAlbum->getCategoryId(),
                                            $oAlbum->getNeedModer()))) {
			return $iId;
		}
		
		return false;
	}
	
	public function UpdateDateModify($iAlbumId, $oDate) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.album' ) . "
												SET 
													date_modify = ?
												WHERE album_id = ?d ";
		
		if (($iId = $this->oDb->query ( $sql,$oDate, $iAlbumId ))) {			
			return $iId;
		}
		
		return false;
	}
	
	public function UpdateCoverPicture($iAlbumId, $iPictureId) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.album' ) . "
												SET 
													cover_picture_id = ?d
												WHERE album_id = ?d ";
		
		if (($iId = $this->oDb->query ( $sql, $iPictureId, $iAlbumId ))) {			
			return $iId;
		}
		
		return false;
	}
	
	// Редактирование альбома
	public function EditAlbum($iAlbumId, $sTitle, $sDesc, $sUrl, $bVisibility, $iCategoryId, $bNeedModer) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.album' ) . "
												SET 
													title = ?,
											        description = ?,
													url = ?,
													visibility = ?d,
													category_id = ?d,
													needmoder = ?d
												WHERE album_id = ?d ";
		
		if (($iId = $this->oDb->query ( $sql,$sTitle, $sDesc, $sUrl, $bVisibility, $iCategoryId, $bNeedModer, $iAlbumId ))) {
			return $iId;
		}
		
		return false;
	}
	
	// Удаление альбома по его идентификатору
	public function DeleteAlbum($iAlbumId) {
		$sql = "DELETE FROM " . Config::Get ( 'plugin.picalbums.table.album' ) . " WHERE album_id = ?d ";
		if ($this->oDb->query ( $sql, $iAlbumId )) {
			return true;
		}
		return false;
	}
    
    // Отмодерировать все картинки в альбоме
	public function ModerateAll($iAlbumId) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.picture' ) . " SET ismoder = 1 WHERE album_id = ?d ";
		if ($this->oDb->query ( $sql, $iAlbumId )) {
			return true;
		}
		return false;
	}

}
?>
