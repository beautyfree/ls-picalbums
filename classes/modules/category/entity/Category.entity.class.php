<?php

class PluginPicalbums_ModuleCategory_EntityCategory extends Entity {
	
	public function getId() {
		return $this->_aData ['category_id'];
	}
	public function getUserId() {
		return $this->_aData ['user_id'];
	}
	public function getTitle() {
		return $this->_aData ['title'];
	}
	public function getAlbums($iUserId) {
		return $this->PluginPicalbums_Album_GetAlbumsByUserIdAndCategoryId($iUserId, $this->getId());
	}
	public function getDateModify($iUserId) {
		return $this->PluginPicalbums_Category_GetCategorysDateModify($iUserId, $this->getId());
	}

	public function setId($data) {
		$this->_aData ['heart_id'] = $data;
	}
	public function setUserId($data) {
		$this->_aData ['user_id'] = $data;
	}
	public function setTitle($data) {
		$this->_aData ['title'] = $data;
	}
}

?>
