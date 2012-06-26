<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
	class PluginPicalbums_ActionComments extends PluginPicalbums_Inherit_ActionComments {	
	
		protected function EventComments() {	
			$iPage=$this->GetEventMatch(2) ? $this->GetEventMatch(2) : 1;
			
			$aCloseBlogs = ($this->oUserCurrent)
				? $this->Blog_GetInaccessibleBlogsByUser($this->oUserCurrent)
				: $this->Blog_GetInaccessibleBlogsByUser();
				
			$aResult=$this->Comment_GetCommentsAllByTargetTypeArray(Array('topic','picalbums'),$iPage,Config::Get('module.comment.per_page'),array(),$aCloseBlogs);		
			$aComments=$aResult['collection'];	
			
			$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.comment.per_page'),4,Router::GetPath('comments'));	
			
			$this->Viewer_Assign ( 'sTemplateWebPathPicalbumsPlugin', Plugin::GetTemplateWebPath ( __CLASS__ ) );
			$this->Viewer_Assign ( 'sTemplatePathPicalbumsPlugin', Plugin::GetTemplatePath ( __CLASS__ ) );
			
			$this->Viewer_Assign('aPaging',$aPaging);					
			$this->Viewer_Assign("aComments",$aComments);
			$this->Viewer_Assign('sMainAlbumsRouter',Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')));
			$this->Viewer_AddHtmlTitle($this->Lang_Get('comments_all'));
			$this->Viewer_SetHtmlRssAlternate(Router::GetPath('rss').'allcomments/',$this->Lang_Get('comments_all'));
			
			$this->SetTemplateAction('index');				
		}
	
	}
?>