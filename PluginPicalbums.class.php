<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * @LiveStreet Version: 0.5.1
 * ----------------------------------------------------------------------------
 */

if (! class_exists ( 'Plugin' )) {
	die ( 'Hacking attemp!' );
}

class PluginPicalbums extends Plugin {
	public function Activate() {
		if (! $this->isTableExists ( 'prefix_picalbums_album' )) {
			$this->ExportSQL ( dirname ( __FILE__ ) . '/install.sql' );
		}
		
		@func_mkdir(Config::Get('path.root.server').Config::Get('path.uploads.root'), "picalbums");
		
		$this->Database_addEnumType('prefix_comment', 'target_type', 'picalbums');
		$this->Database_addEnumType('prefix_comment_online', 'target_type', 'picalbums');
		
		return true;
	}
	
	public function Deactivate() {
		if (Config::Get ( 'plugin.picalbums.dropuninstall' ) == true) {
			if ($this->isTableExists ( 'prefix_picalbums_album' )) {
				$this->ExportSQL ( dirname ( __FILE__ ) . '/uninstall.sql' );
			}
		}
		return true;
	}
	
	public function Init() {
		$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/picalbums.css');
		$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/notes/lteIE8.css');
		$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/notes/style.css');
		$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/fileuploader.css');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/fileuploader.css');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/tip/black/black.css');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/tip/yellow/yellow.css');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/slides.css');
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/carousel.css');
		$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('picalbums').'css/smoothness/jquery-ui-1.8.17.custom.css');

        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/jquery-ui-1.8.17.custom.min.js');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/jquery.carouFredSel-5.5.0-packed.js');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/jquery.textchange.js');
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/jquery.poshytip.min.js');
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/jquery.imagesloaded.min.js');
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/slides.js');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/pjax.js');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/share42.js');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/fileuploader.js');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/picalbums.js');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath('picalbums').'js/picalbums-notes.js');
		
		$this->mainalbumsMenu = Plugin::GetTemplateWebPath('picalbums').'menu.mainalbums.tpl';
	}
	
	protected $aInherits=array(
	   'module'  => array(	'ModuleComment' => 'PluginPicalbums_ModuleComment',
                            'PluginSitemap_ModuleSitemap' => 'PluginPicalbums_ModuleSitemap',
	   						'ModuleStream' => 'PluginPicalbums_ModuleStream', 
	   						'ModuleImage' => 'PluginPicalbums_ModuleImage'),
	   'entity' => array(	'ModuleUser_EntityUser' => 'PluginPicalbums_ModuleUser_EntityUser', 
							'ModuleComment_EntityComment' => 'PluginPicalbums_ModuleComment_EntityComment'
						),
	   'mapper' => array(   'ModuleComment_MapperComment' => 'PluginPicalbums_ModuleComment_MapperComment'),
	   
	   'action' => array( 	'ActionSettings' => 'PluginPicalbums_ActionSettingsmain',
							'ActionAjax' => 'PluginPicalbums_ActionAjax',
							'ActionComments' => 'PluginPicalbums_ActionComments',
							'ActionRss' => 'PluginPicalbums_ActionRss',
							),
	   
	   'block' => array( 'BlockStream' => 'PluginPicalbums_BlockStream' ),
	);
	
	protected $aDelegates = array(
		'template' => array('menu.mainalbums.tpl'=> '_menu.mainalbums.tpl'),
	);
}

?>
