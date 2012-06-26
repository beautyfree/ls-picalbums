<?php


class PluginPicalbums_ModuleCategory extends Module {
	
	protected $oMapper;
	
	public function Init() {
		$this->oMapper = Engine::GetMapper ( __CLASS__ );
	}
	
	public function GetCategoryById($iCategoryId) {
		$tag = "categoryby_id_{$iCategoryId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetCategoryById ( $iCategoryId );
			if($data)
				$this->Cache_Set ( $data, $tag, array ("picalbums_category_update_by_target_{$data->getUserId()}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetMaxCategoryId() {
		$res = $this->oMapper->GetMaxCategoryId ( );
		if(!$res) {
			if(($res == 0) || ($res == '0'))
				return 1;
			else 
				return 0;
		}
		return $res + 1;
	}
	
	public function GetCategorysByUserId($iUserId) {
		$tag = "categoryby_user_id_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetCategorysByUserId ( $iUserId );
			$this->Cache_Set ( $data, $tag, array ("picalbums_category_update_by_target_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}
	
	public function GetCategorysCountByUserId($iUserId) {
		$tag = "categorycount_by_user_id_{$iUserId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetCategorysCountByUserId ( $iUserId );
			
			$this->Cache_Set ( $data, $tag, array ("picalbums_category_update_by_target_{$iUserId}" ), 60 * 60 * 24 );
		}
		return $data;
	}

    public function GetCategorysLimitByUserId($iUserId, &$iCount,  $iCurrPage, $iPerPage) {
		$tag = "wall_all_records_limit_{$iUserId}_{$iCurrPage}_{$iPerPage}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetCategorysLimitByUserId($iUserId, $iCount,  $iCurrPage, $iPerPage);
			$this->Cache_Set ( $data, $tag, array ("picalbums_category_update_by_target_{$iUserId}"  ), 60 * 60 * 24 );
		}
		return $data;
	}

    public function GetCategorysDateModify($iUserId, $iCategoryId) {
        $tag = "wall_get_date_modify_{$iCategoryId}";
		if (false === ($data = $this->Cache_Get ( $tag ))) {
			$data = $this->oMapper->GetCategorysDateModify($iCategoryId);
			$this->Cache_Set ( $data, $tag, array ("picalbums_category_update_by_target_{$iUserId}"  ), 60 * 60 * 24 );
		}
		return $data;
	}

	
	public function AddCategory($oCategory) {
		if (($oId=$this->oMapper->AddCategory ( $oCategory ))) {
			$this->oMapper->UpdatePosition($oId, $oId);
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_update_{$oCategory->getUserId()}","picalbums_category_update_by_target_{$oCategory->getUserId()}" ) );
			return true;
		}
		return false;
	}
	
	public function EditCategory($iCategoryId, $sTitle) {
		$oCategory = $this->GetCategoryById($iCategoryId);
		if ($oId=$this->oMapper->EditCategory($iCategoryId, $sTitle)) {
			if($oCategory)
				$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_update_{$oCategory->getUserId()}","picalbums_category_update_by_target_{$oCategory->getUserId()}" ) );
			return $oId;
		}
		return false;
	}

    public function SortCategories($aCategories) {
		$iUserId = null;
		foreach ($aCategories as $key => $value) {
			$this->oMapper->UpdatePosition($key, $value);

			if(!$iUserId && ($oCategory1 = $this->GetCategoryById($key))) {
				$iUserId = $oCategory1->getUserId();
			}
		}

		if($iUserId)
			$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("picalbums_category_update_by_target_{$iUserId}" ) );

		return true;
	}
	
	public function DeleteCategory($iCategoryId) {
		$oCategory = $this->GetCategoryById($iCategoryId);
		
		if ($this->oMapper->DeleteCategory ( $iCategoryId )) {
			if($oCategory) {
				$this->Cache_Clean ( Zend_Cache::CLEANING_MODE_MATCHING_TAG, array ("album_update_{$oCategory->getUserId()}", "picalbums_category_update_by_target_{$oCategory->getUserId()}" ) );
			}
			return true;
		}
		return false;
	}
}
?>
