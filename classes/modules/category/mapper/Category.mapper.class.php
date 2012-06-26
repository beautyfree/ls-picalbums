<?php

class PluginPicalbums_ModuleCategory_MapperCategory extends Mapper {
	
	public function GetCategoryById($iCategoryId) {
		$sql = 	" SELECT a.* ".
				" FROM " . Config::Get ( 'plugin.picalbums.table.category' ) . " a " .
				" WHERE a.category_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iCategoryId )) {
			return Engine::GetEntity ( 'PluginPicalbums_Category', $aRow );
		}
		return false;
	}
	
	public function GetCategorysByUserId($iUserId) {
		$sql = 	" SELECT * " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.category' )  . 
				" WHERE user_id = ?d ORDER BY position ASC ";
				
		$aReturn = array ();
		if ($aRows = $this->oDb->select ( $sql, $iUserId )) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Category', $aRow );
			}
			return $aReturn;
		}
		return false;
	}
	
	public function GetCategorysCountByUserId($iUserId) {
		$sql = 	" SELECT COUNT(category_id) as count_category  " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.category' ) .
				" WHERE user_id = ?d ";
				
		if ($aRow = $this->oDb->selectRow ( $sql, $iUserId )) {
			return $aRow['count_category'];
		}
		return false;
	}
	
	public function GetMaxCategoryId() {
		$sql = 	" SHOW TABLE STATUS LIKE '" . Config::Get ( 'plugin.picalbums.table.category' ) ."'";
		if ($aRow = $this->oDb->selectRow ( $sql )) {
			return $aRow['Auto_increment'];
		}	
		
		$sql = 	" SELECT MAX(category_id) as max_category_id  " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.category' );
				
		if ($aRow = $this->oDb->selectRow ( $sql )) {
			return $aRow['max_category_id'];
		}
		return false;
	}
    
    public function GetCategorysLimitByUserId($iUserId, &$iCount,  $iCurrPage, $iPerPage) {
		$sql = 	" SELECT * " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.category' )  .
				" WHERE user_id = ?d ORDER BY position ASC LIMIT ?d, ?d";

		$aReturn = array ();
		if ($aRows = $this->oDb->selectPage ( $iCount, $sql, $iUserId, ($iCurrPage - 1) * $iPerPage, $iPerPage) ) {
			foreach ( $aRows as $aRow ) {
				$aReturn [] = Engine::GetEntity ( 'PluginPicalbums_Category', $aRow );
			}
			return $aReturn;
		}
		return false;
	}

    public function GetCategorysDateModify($iCategoryId) {
		$sql = 	" SELECT MAX(a.date_modify) as date_modify " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.category' )  . " c, " . Config::Get ( 'plugin.picalbums.table.album' ) . " a " .
				" WHERE c.category_id = ?d AND c.category_id = a.category_id ";

		if ($aRow = $this->oDb->selectRow ( $sql, $iCategoryId )) {
			return $aRow['date_modify'];
		}
		return false;
	}

	public function AddCategory($oCategory) {
		
		$sql = " INSERT INTO " . Config::Get ( 'plugin.picalbums.table.category' ) . "
												(	title,
													user_id
												)
												VALUES (?, ?d) ";
		
		if ($iId = $this->oDb->query ( $sql, $oCategory->getTitle (), $oCategory->getUserId ())) {			
			return $iId;
		}
		
		return false;
	}
	
	public function EditCategory($iCategoryId, $sTitle) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.category' ) . "
												SET 
											        title = ?
												WHERE category_id = ?d ";
		
		if (($iId = $this->oDb->query ( $sql, $sTitle, $iCategoryId ))) {
			return $iId;
		}
		
		return false;
	}
	
	public function UpdatePosition($iCategoryId, $newPos) {
		$sql = "UPDATE " . Config::Get ( 'plugin.picalbums.table.category' ) . "
												SET 
													position = ?
												WHERE category_id = ?d ";
		
		if (($iId = $this->oDb->query ( $sql, $newPos, $iCategoryId ))) {
			return $iId;
		}
		
		return false;
	}
	
	public function DeleteCategory($sCategoryId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.category' ) . " WHERE category_id = ?d ";
		if ($this->oDb->query ( $sql, $sCategoryId )) {
			return true;
		}
		return false;
	}
	
}
?>
