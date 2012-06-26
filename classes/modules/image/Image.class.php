<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */
require_once Config::Get ( 'path.root.engine' ) . '/lib/external/LiveImage/Image.php';

class PluginPicalbums_ModuleImage extends PluginPicalbums_Inherit_ModuleImage {
	
	private function CropMaximumRectangle(LiveImage $oImage, $iWidthProp, $iHeightProp, $isCropInMiddle) {
		if (! $oImage || $oImage->get_last_error ()) {
			return false;
		}
		$iWidth = $iNewWidth = $oImage->get_image_params ( 'width' );
		$iHeight = $iNewHeight = $oImage->get_image_params ( 'height' );
		
		if ($iWidth >= $iHeight) {
			$iNewWidth = $iWidthProp * $iHeight / $iHeightProp;
		} else {
			$iNewHeight = $iHeightProp * $iWidth / $iWidthProp;
		}
		
		$iNewSize = min ( $iWidth, $iHeight );
		
		if($isCropInMiddle == true)
			$oImage->crop ( $iNewWidth, $iNewHeight, ($iWidth - $iNewWidth) / 2, ($iHeight - $iNewHeight) / 2 );
		else
			$oImage->crop ( $iNewWidth, $iNewHeight, ($iWidth - $iNewWidth) / 2, 0 );
		
		return $oImage;
	}
	
	public function ResizeAdditional($sFileSrc,$sDirDest,$sFileDest,
									$iWidthMax,$iHeightMax,
									$iWidthDest=null,$iHeightDest=null,
									$bForcedMinSize=true,$aParams=null,$oImage=null, $Crop=false, $isCropInMiddle=false) {
		$this->ClearLastError();
		/**
		 * Если параметры не переданы, устанавливаем действия по умолчанию
		 */
		if(!is_array($aParams)) {
			$aParams=$this->aParamsDefault;
		}
		/**
		 * Если объект не передан как параметр, 
		 * создаем новый
		 */
		if(!$oImage) $oImage=new LiveImage($sFileSrc);
		
		if($oImage->get_last_error()){
			$this->SetLastError($oImage->get_last_error());
			return false;
		}

		$sFileDest.='.'.$oImage->get_image_params('format');
		if (($oImage->get_image_params('width')>$iWidthMax) || ($oImage->get_image_params('height')>$iHeightMax)) {
			if(($iWidthMax != 0) && ($iHeightMax != 0))
				return false;
		}
		$sFileFullPath=rtrim(Config::Get('path.root.server'),"/").'/'.trim($sDirDest,"/").'/'.$sFileDest;
		$this->CreateDirectory($sDirDest);
			
		if ($iWidthDest) {
			if ($bForcedMinSize and ($iWidthDest>$oImage->get_image_params('width'))) {
				$iWidthDest=$oImage->get_image_params('width');
			}
			
			if($Crop) {
				$oImage = $this->CropMaximumRectangle($oImage, $iWidthDest, $iHeightDest, $isCropInMiddle);
			}
			/**
			 * Ресайзим и выводим результат в файл.
			 * Если не задана новая высота, то применяем масштабирование.
			 * Если нужно добавить Watermark, то запрещаем ручное управление alfa-каналом
			 */
			$oImage->resize($iWidthDest,$iHeightDest,(!$iHeightDest),(!$aParams['watermark_use']));
			
			/**
			 * Добавляем watermark согласно в конфигурации заданым параметрам
			 */
			if($aParams['watermark_use']) {
				if ($oImage->get_image_params('width')>$aParams['watermark_min_width'] and $oImage->get_image_params('height')>$aParams['watermark_min_height']) {
					switch($aParams['watermark_type']) {
						default:
						case 'text':
							$oImage->set_font(
								$aParams['watermark_font_size'],  0,
								$aParams['path']['fonts'].$aParams['watermark_font'].'.ttf'
							);

							$oImage->watermark(
								$aParams['watermark_text'],
								explode(',',$aParams['watermark_position'],2),
								explode(',',$aParams['watermark_font_color']),
								explode(',',$aParams['watermark_back_color']),
								$aParams['watermark_font_alfa'],
								$aParams['watermark_back_alfa']
							);
							break;
						case 'image':
							$oImage->paste_image(
								$aParams['path']['watermarks'].$aParams['watermark_image'],
								false, explode(',',$aParams['watermark_position'],2)
							);
							break;
					}
				}
			}
			/**
			 * Скругляем углы
			 */
			if($aParams['round_corner']) {
				$oImage->round_corners($aParams['round_corner_radius'], $aParams['round_corner_rate']);
			}
			/**
			 * Для JPG формата устанавливаем output quality, если это предусмотрено в конфигурации
			 */
			if(isset($aParams['jpg_quality']) and $oImage->get_image_params('format')=='jpg') {
				$oImage->set_jpg_quality($aParams['jpg_quality']);
			}
			
			$oImage->output(null,$sFileFullPath);
			
			chmod($sFileFullPath,0666);
			return $sFileFullPath;
		} elseif (copy($sFileSrc,$sFileFullPath)) {
			chmod($sFileFullPath,0666);
			return $sFileFullPath;
		}
		
		return false;
	}

}

?>