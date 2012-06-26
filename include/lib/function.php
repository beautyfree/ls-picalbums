<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

if (!function_exists('isValidLink')) {
    function isValidLink($link) {
        return preg_match('/^(https?:\/\/){1}+[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,6})(.*?)$/i', $link);
    }
}

if (!function_exists('object2array')) {
    function object2array($object) {
        return @json_decode ( @json_encode ( $object ), 1 );
    }
}

if (!function_exists('russian_date')) {
    function russian_date($pdate){
        $date=explode(" ", $pdate);
        $m='';
        switch ($date[1]) {
            case 1: $m='января'; break;
            case 2: $m='февраля'; break;
            case 3: $m='марта'; break;
            case 4: $m='апреля'; break;
            case 5: $m='мая'; break;
            case 6: $m='июня'; break;
            case 7: $m='июля'; break;
            case 8: $m='августа'; break;
            case 9: $m='сентября'; break;
            case 10: $m='октября'; break;
            case 11: $m='ноября'; break;
            case 12: $m='декабря'; break;
        }
        return $date[0].' '.$m.' '.$date[2].' '.$date[3];
    }
}
if (!function_exists('func_translit')) {
    function func_translit($sText,$bLower=true) {
        $aConverter=array(  
            'а' => 'a',   'б' => 'b',   'в' => 'v',  
            'г' => 'g',   'д' => 'd',   'е' => 'e',  
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',  
            'и' => 'i',   'й' => 'y',   'к' => 'k',  
            'л' => 'l',   'м' => 'm',   'н' => 'n',  
            'о' => 'o',   'п' => 'p',   'р' => 'r',  
            'с' => 's',   'т' => 't',   'у' => 'u',  
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',  
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',  
            'ь' => "'",  'ы' => 'y',   'ъ' => "'",  
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',  
      
            'А' => 'A',   'Б' => 'B',   'В' => 'V',  
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',  
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',  
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',  
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',  
            'О' => 'O',   'П' => 'P',   'Р' => 'R',  
            'С' => 'S',   'Т' => 'T',   'У' => 'U',  
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',  
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',  
            'Ь' => "'",  'Ы' => 'Y',   'Ъ' => "'",  
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya', 
            
            " "=> "-", "."=> "", "/"=> "-" 
        );  
        $sRes=strtr($sText,$aConverter);
        if ($sResIconv=@iconv("UTF-8", "ISO-8859-1//IGNORE//TRANSLIT", $sRes)) {
            $sRes=$sResIconv;
        }
        if (preg_match('/[^A-Za-z0-9_\-]/', $sRes)) {    	
            $sRes = preg_replace('/[^A-Za-z0-9_\-]/', '', $sRes);
            $sRes = preg_replace('/\-+/', '-', $sRes);
        }
        if ($bLower) {
            $sRes=strtolower($sRes);
        }
        return $sRes;
    }
}

if (!function_exists('array_for_preload')) {
    function array_for_preload($aMainArray, $iCenterId) {
        if(!is_array($aMainArray)) {
            $aArrayPreload = array();
            array_push($aArrayPreload, $aMainArray);
            return $aArrayPreload;
        }
        $aMainArrayReverse = array_reverse($aMainArray);
        $aArray1 = array();
        $bNeedAdd = false;
        foreach($aMainArray as $oAllPicture) {
            if($oAllPicture->getId() == $iCenterId)
                $bNeedAdd = true;
            if($bNeedAdd)
                array_push($aArray1, $oAllPicture);
        }
        $aArray2 = array();
        $bNeedAdd = false;
        foreach($aMainArrayReverse as $oAllPicture) {
            if($bNeedAdd)
                array_push($aArray2, $oAllPicture);
            if($oAllPicture->getId() == $iCenterId)
                $bNeedAdd = true;
        }
        $iArray1Len = count($aArray1); $iArray2Len = count($aArray2);
        $iMaxLen = max($iArray1Len, $iArray2Len);
        $aArrayPreload = array();
        for ($i = 0; $i <= $iMaxLen; $i++) {
            if($iArray1Len > $i)
                array_push($aArrayPreload, $aArray1[$i]);
            if($iArray2Len > $i)
                array_push($aArrayPreload, $aArray2[$i]);
        }

        return $aArrayPreload;
    }
}
?>