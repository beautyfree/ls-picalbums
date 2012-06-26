<?

function AnaliseAlbums($oEngine, $aAlbums, $oUser, $oCallBack) {
    $sMessageText = "";
    foreach ($aAlbums as $oAlbum) {
        if($oAlbum->getNeedModer() == 1) {
            $aNonModeratedPictures = $oEngine->PluginPicalbums_Picture_GetNonModeratedPictureByAlbumId($oAlbum->getId());
            if($aNonModeratedPictures) {
                if($oAlbum->getUserId() == Config::Get('plugin.picalbums.virtual_main_user_id'))
                    $sUrl = Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name')) . $oAlbum->getURL() . '/';
                else {
                    $sUrl = $oAlbum->GetAppendedAlbumUser()->getUserAlbumsWebPath() . $oAlbum->getURL() . '/';
                }

                $sMessageText .= $oEngine->Lang_Get ( 'picalbums_cron_album_not_moderate_start' ) .
                                 " <a href='" . $sUrl . "'>" . $oAlbum->getTitle() . "</a> " .
                                 $oEngine->Lang_Get ( 'picalbums_cron_album_not_moderate_end' ) . " <br/>";
                
                if(Config::Get('plugin.picalbums.cron_need_images')) {
                    foreach ($aNonModeratedPictures as $oPicture) {
                        $sMessageText .= "<img src='" . $oPicture->getMiniaturePath() . "'></img><br/><br/>";
                    }
                }
            }
        }
    }
    if($sMessageText != "") {
        if($oCallBack) {
            call_user_func($oCallBack, $oAlbum, $oUser);
        }
        $oEngine->Notify_Send(
            $oUser,
            'notify.formoderators.tpl',
            $oEngine->Lang_Get ( 'picalbums_cron_mail_title' ),
            array(
                'sUserName' => $oUser->getLogin(),
                'sMessageText' => $sMessageText,
            ),
            'picalbums'
        );
    }
}

define('SYS_HACKER_CONSOLE',false);
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config/loader.php');
require_once(Config::Get('path.root.engine')."/classes/Engine.class.php");
$oEngine=Engine::getInstance();
$oEngine->Init();

$aNotModeratedAlbums = array();
$aSendMailUsers = array();

function AnaliseAlbumsCallBack($oAlbum, $oUser) {
    global $aNotModeratedAlbums;
    global $aSendMailUsers;

    if(!in_array($oAlbum->getId(), $aNotModeratedAlbums))
        array_push($aNotModeratedAlbums, $oAlbum->getId());

    if(!in_array($oUser->getLogin(), $aSendMailUsers))
        array_push($aSendMailUsers, $oUser->getLogin());
}

$aUsers = $oEngine->PluginPicalbums_Picalbums_GetUserCollectiveAlbumOwner(Config::Get('plugin.picalbums.virtual_main_user_id'));
foreach ($aUsers as $oUser) {
    $aAlbums = $oEngine->PluginPicalbums_Album_GetAlbumsAppendedByUserId($oUser->getId());
    if($aAlbums) {
        AnaliseAlbums($oEngine, $aAlbums, $oUser, "AnaliseAlbumsCallBack");
    }
}

if(count($aNotModeratedAlbums) > 0) {
    foreach(Config::Get('plugin.picalbums.moderators') as $sModeratorLogin) {
        $oModerator = $oEngine->User_GetUserByLogin($sModeratorLogin);
        if($oModerator) {
            if(!in_array($oModerator->getLogin(), $aSendMailUsers)) {  
                AnaliseAlbums($oEngine,
                              $oEngine->PluginPicalbums_Album_GetAlbumsByArrayId($aNotModeratedAlbums),
                              $oModerator,
                              null);
            }
        }
    }
}

?>
