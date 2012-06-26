<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
	class PluginPicalbums_ActionRss extends PluginPicalbums_Inherit_ActionRss {	
	
		protected function RssComments() {
			$aCloseTopics = $this->Topic_GetTopicsCloseByUser();		
			
			$aResult=$this->Comment_GetCommentsAllByTargetTypeArray(Array('topic','picalbums'),1,Config::Get('module.comment.per_page')*2,$aCloseTopics);
			$aComments=$aResult['collection'];
			
			$aChannel['title']=Config::Get('view.name');
			$aChannel['link']=Config::Get('path.root.web');
			$aChannel['description']=Config::Get('path.root.web').' / RSS channel';
			$aChannel['language']='ru';
			$aChannel['managingEditor']=Config::Get('general.rss_editor_mail');
			$aChannel['generator']=Config::Get('path.root.web');
			
			$comments=array();
			foreach ($aComments as $oComment){
				if($oComment->getTargetType() == 'topic') {
					$item['title']='Comments: '.$oComment->getTarget()->getTitle();
					$item['guid']=$oComment->getTarget()->getUrl().'#comment'.$oComment->getId();
					$item['link']=$oComment->getTarget()->getUrl().'#comment'.$oComment->getId();
				} else {
					$oPicture = $oComment->getPicture();
					$oAlbum = $oPicture->getAlbumOwner();
					$oUserOwner = $oAlbum->getUserOwner();
										
					if($oUserOwner) {
						$albumWebPath = $oUserOwner->getUserAlbumsWebPath();
					} else {
						$albumWebPath = Router::GetPath(Config::Get ( 'plugin.picalbums.main_albums_router_name'));
					}
					
					$item['title']='Comments: '.$oComment->getPicture()->getDescription();
					$item['guid']= $albumWebPath . $oAlbum->getURL() . '/'. $oPicture->getURL() . '/';
					$item['link']= $albumWebPath . $oAlbum->getURL() . '/'. $oPicture->getURL() . '/';
				}
				
				$item['description']=$oComment->getText();
				$item['pubDate']=$oComment->getDate();
				$item['author']=$oComment->getUser()->getLogin(); 
				$item['category']='comments';
				$comments[]=$item;
			}
			
			$this->InitRss();
			$this->Viewer_Assign('aChannel',$aChannel);
			$this->Viewer_Assign('aItems',$comments);
			$this->SetTemplateAction('index');
		}
	
	
	}
?>
