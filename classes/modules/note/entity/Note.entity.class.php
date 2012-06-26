<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleNote_EntityNote extends Entity {
	
	public function getId() {
		return $this->_aData ['note_id'];
	}
	public function getLeft() {
		return $this->_aData ['left'];
	}
	public function getTop() {
		return $this->_aData ['top'];
	}
	public function getWidth() {
		return $this->_aData ['width'];
	}
	public function getHeight() {
		return $this->_aData ['height'];
	}
	public function getDateAdd() {
		return $this->_aData ['dateadd'];
	}
	public function getNote() {
		return $this->_aData ['note'];
	}
	public function getLink() {
		return $this->_aData ['link'];
	}
	public function getUserId() {
		return $this->_aData ['user_id'];
	}
	public function getUserMarkId() {
		return $this->_aData ['user_mark_id'];
	}	
	public function getPictureId() {
		return $this->_aData ['picture_id'];
	}	
	public function getIsConfirm() {
		return $this->_aData ['is_confirm'];
	}
	
	
	
	public function setId($data) {
		$this->_aData ['note_id'] = $data;
	}
	public function setLeft($data) {
		$this->_aData ['left'] = $data;
	}
	public function setTop($data) {
		$this->_aData ['top'] = $data;
	}
	public function setWidth($data) {
		$this->_aData ['width'] = $data;
	}
	public function setHeight($data) {
		$this->_aData ['height'] = $data;
	}
	public function setDateAdd($data) {
		$this->_aData ['dateadd'] = $data;
	}
	public function setNote($data) {
		$this->_aData ['note'] = $data;
	}
	public function setLink($data) {
		$this->_aData ['link'] = $data;
	}
	public function setUserId($data) {
		$this->_aData ['user_id'] = $data;
	}
	public function setUserMarkId($data) {
		$this->_aData ['user_mark_id'] = $data;
	}
	public function setPictureId($data) {
		$this->_aData ['picture_id'] = $data;
	}	
	public function setIsConfirm($data) {
		$this->_aData ['is_confirm'] = $data;
	}

}

?>
