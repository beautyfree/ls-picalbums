<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * @LiveStreet Version: 0.5.1
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModulePicture_EntityPicture extends Entity {
	
	public function getId() {
		return $this->_aData ['picture_id'];
	}
	public function getAlbumId() {
		return $this->_aData ['album_id'];
	}
	public function getDescription() {
		return $this->_aData ['description'];
	}
	public function getURL() {
		return $this->_aData ['url'];
	}
	public function getExif() {
		return $this->_aData ['exif'];
	}
	public function getPosition() {
		return $this->_aData ['position'];
	}	
	public function getAddUserId() {
		return $this->_aData ['adduser_id'];
	}
	public function getPicPath() {
		return $this->_aData ['picpath'];
	}
	public function getOriginalPath() {
		return $this->_aData ['originalpath'];
	}
	public function getMiniaturePath() {
		return $this->_aData ['picminiaturepath'];
	}
	public function getBlockPath() {
		return $this->_aData ['picblockpath'];
	}
	public function getDateAdd() {
		return $this->_aData ['date_add'];
	}
	public function getIsModer() {
		return $this->_aData ['ismoder'];
	}	

	public function getCommentCount() {
		return $this->Comment_GetCountCommentsByTargetId($this->getId(), 'picalbums');
	}		
	public function getAlbumOwner() {
		return $this->PluginPicalbums_Album_GetAlbumById($this->getAlbumId());
	}	
	public function GetAppendedAlbumUser() {
		return $this->User_GetUserById($this->getAddUserId());
	}
	
	public function setId($data) {
		$this->_aData ['picture_id'] = $data;
	}
	public function setAlbumId($data) {
		$this->_aData ['album_id'] = $data;
	}
	public function setDescription($data) {
		$this->_aData ['description'] = $data;
	}
	public function setURL($data) {
		$this->_aData ['url'] = $data;
	}
	public function setExif($data) {
		$this->_aData ['exif'] = $data;
	}
	public function setPosition($data) {
		$this->_aData ['position'] = $data;
	}
	public function setPicPath($data) {
		$this->_aData ['picpath'] = $data;
	}
	public function setMiniaturePath($data) {
		$this->_aData ['picminiaturepath'] = $data;
	}
	public function setBlockPath($data) {
		$this->_aData ['picblockpath'] = $data;
	}
	public function setDateAdd($data) {
		$this->_aData ['date_add'] = $data;
	}
	public function setOriginalPath($data) {
		$this->_aData ['originalpath'] = $data;
	}
	public function setAddUserId($data) {
		$this->_aData ['adduser_id'] = $data;
	}
	public function setIsModer($data) {
		$this->_aData ['ismoder'] = $data;
	}
}

?>
