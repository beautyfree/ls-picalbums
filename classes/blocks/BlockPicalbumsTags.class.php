<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_BlockPicalbumsTags extends Block {
	public function Exec() {
		
		// Получаем список тегов
		$aTags=$this->PluginPicalbums_Tag_GetTags(70);

		// Расчитываем логарифмическое облако тегов
		if ($aTags) {
			$iMinSize=1; // минимальный размер шрифта
			$iMaxSize=10; // максимальный размер шрифта
			$iSizeRange=$iMaxSize-$iMinSize;

			$iMin=10000;
			$iMax=0;
			foreach ($aTags as $oTag) {
				if ($iMax<$oTag->getCount()) {
					$iMax=$oTag->getCount();
				}
				if ($iMin>$oTag->getCount()) {
					$iMin=$oTag->getCount();
				}
			}

			$iMinCount=log($iMin+1);
			$iMaxCount=log($iMax+1);
			$iCountRange=$iMaxCount-$iMinCount;
			if ($iCountRange==0) {
				$iCountRange=1;
			}
			foreach ($aTags as $oTag) {
				$iTagSize=$iMinSize+(log($oTag->getCount()+1)-$iMinCount)*($iSizeRange/$iCountRange);
				$oTag->setSize(round($iTagSize)); // результирующий размер шрифта для тега
			}
		 	// Устанавливаем шаблон вывода
			$this->Viewer_Assign("aTags",$aTags);
            $this->Viewer_Assign ( 'sMainAlbumsRouter', Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')) );
		}
	}
}
?>