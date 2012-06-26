<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * @LiveStreet Version: 0.5.1
 * ----------------------------------------------------------------------------
 */
 
require_once(Config::Get('path.root.engine').'/lib/external/Jevix/jevix.class.php');

class PluginPicalbums_ActionSettings extends ActionPlugin {
	
	protected $sMenuItemSelect = 'picalbums';
	protected $oUserCurrent = null;
	
	public function Init() {
		if ($this->User_IsAuthorization ()) {
			$this->Viewer_Assign ( 'sTemplateWebPathPicalbumsPlugin', Plugin::GetTemplateWebPath ( __CLASS__ ) );
		} else {
			$this->Message_AddErrorSingle ( $this->Lang_Get ( 'not_access' ), $this->Lang_Get ( 'error' ) );
			return Router::Action ( 'error' );
		}

        $this->Lang_AddLangJs(array(
			'picalbums_delete_text',
		));
	}
	
	protected function RegisterEvent() {
		$this->AddEventPreg ( '/^settings$/i', '/^$/i', 'EventSettings' );
	}
	
	private function TextParser($sText) {	
		$oJevix = new Jevix();		

		$aConfig=Config::Get('plugin.picalbums.jevix.title');
		
		if (is_array($aConfig)) {
			foreach ($aConfig as $sMethod => $aExec) {
				foreach ($aExec as $aParams) {
					call_user_func_array(array($oJevix,$sMethod), $aParams);
				}
			}
			
			// ��������� ��������� ���������
			unset($oJevix->entities1['&']); // ��������� � ���������� ������ &
			if (Config::Get('view.noindex') and isset($oJevix->tagsRules['a'])) {
				$oJevix->cfgSetTagParamDefault('a','rel','nofollow',true);
			}
		}
		
		$errors = null;
		$sText = $oJevix->parse($sText, $errors);
		return $sText;		
	}
	
	protected function EventSettings() {		
		if (isPost ( 'submit_picalbums_settings' )) {
			if(!Config::Get ( 'plugin.picalbums.enable_ajax_navigation' )) {
				$usedAjax = 0;
			} else {
				$usedAjax = getRequest ( 'used_ajax' );
			}
		
			$this->PluginPicalbums_Settings_UpdateSettings ( $this->User_GetUserCurrent()->getId (),
															 $usedAjax);
			$this->Message_AddNoticeSingle($this->Lang_Get ( 'picalbums_settings_save' ));
		}
		
		if (isPost ( 'new-category-form-submit' )) {
			
			$textLength = mb_strlen(getRequest ( 'categoty_text_name' ), 'UTF-8');
			// ����������� ������������ ���������� ������
			$sTitle = $this->TextParser(getRequest ( 'categoty_text_name' )) ;	
			if(($textLength < 2) || ($textLength > Config::Get ( 'plugin.picalbums.text_title_max_characters' ))) {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_title_length_error_start' ).Config::Get ( 'plugin.picalbums.text_title_max_characters' ).$this->Lang_Get ( 'picalbums_length_error_end' ), $this->Lang_Get ( 'error' ) );
				return;
			}
		
			$oCategoryNew = Engine::GetEntity ( 'PluginPicalbums_Category' );
			$oCategoryNew->setUserId ( Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) );
			$oCategoryNew->setTitle ( $sTitle );
			
			// ��������� ����
			if (($this->PluginPicalbums_Category_AddCategory ( $oCategoryNew ))) {
				$this->Message_AddNoticeSingle ( $this->Lang_Get ( 'picalbums_new_category_add' ) );
			} else {
				$this->Message_AddErrorSingle ( $this->Lang_Get ( 'picalbums_new_category_add_error' ) );
			}
		}
		
		$settings = $this->PluginPicalbums_Settings_GetSettingsByUserID($this->User_GetUserCurrent()->getId ());
		
		$oUserCurrent = $this->User_GetUserCurrent();		
		if ($oUserCurrent->isAdministrator())	
			$aBlockUsers = $this->PluginPicalbums_Blacklist_getAllBlockedUsers();
		else
			$aBlockUsers = null;
		
		$this->Viewer_Assign ( 'bEnabledUsedAjax', Config::Get ( 'plugin.picalbums.enable_ajax_navigation' ));
		$this->Viewer_Assign ( 'bIsUsedAjax', $settings->getIsUsedAjax());
		$this->Viewer_Assign ( 'aBlockUsers', $aBlockUsers);
		$this->Viewer_Assign ( 'oUserCurrent', $oUserCurrent);
		$this->Viewer_Assign ( 'sMainAlbumsRouterName', Config::Get('plugin.picalbums.main_albums_router_name') );
		$this->Viewer_Assign ( 'aCategories', $this->PluginPicalbums_Category_GetCategorysByUserId ( Config::Get ( 'plugin.picalbums.virtual_main_user_id' ) ) );
		
		$this->SetTemplateAction ( 'settings' );
	}
	
	
}
?>
