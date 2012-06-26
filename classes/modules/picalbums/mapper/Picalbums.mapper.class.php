<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModulePicalbums_MapperPicalbums extends Mapper {
	
	public function FriendFilter($iUserId) {
		$sql = "SELECT
					uf.user_from,
					uf.user_to
				FROM
					".Config::Get('db.table.friend')." as uf
				WHERE
					( uf.user_from = ?d
					OR
					uf.user_to = ?d )
					AND
					( 	uf.status_from + uf.status_to = ?d
					OR
						uf.status_from + uf.status_from = ?d
					)
					;";
		$aUsers=array();
		if ($aRows=$this->oDb->select($sql,$iUserId,$iUserId,ModuleUser::USER_FRIEND_ACCEPT+ModuleUser::USER_FRIEND_OFFER,ModuleUser::USER_FRIEND_ACCEPT+ModuleUser::USER_FRIEND_ACCEPT )) {
			foreach ($aRows as $aUser) {
				$aUsers[]=($aUser['user_from']==$iUserId)
							? $aUser['user_to']
							: $aUser['user_from'];
			}
                        $aUsers[]=$iUserId;
		}
		return array_unique($aUsers);
	}
	
	public function AllUser() {
		$sql = "SELECT
					user_id
				FROM
					".Config::Get('db.table.user');
		$aUsers=array();
		if ($aRows=$this->oDb->select($sql)) {
			foreach ($aRows as $aUser) {
				$aUsers[]=$aUser['user_id'];
			}
		}
		return array_unique($aUsers);
	}
	
	public function GetAllUsersLoginLike($sUserLogin,$iLimit) {		

		$aAllUsers=$this->AllUser();

		if (!$aAllUsers) {
            return array();
		}

        $sql = "SELECT
				u.*					 
			FROM 
				".Config::Get('db.table.user')." as u
			WHERE
				u.user_login LIKE ?	and
				u.user_activate = 1     and
                                u.user_id IN(?a)
			ORDER BY
				u.user_login									
			LIMIT 0, ?d		
				";	
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,$sUserLogin.'%',$aAllUsers,$iLimit)) {
			foreach ($aRows as $aRow) {
				$aReturn[] = new ModuleUser_EntityUser($aRow);
			}
		}
		return $aReturn;
	}
	
	public function GetFriendsByUserIdAndLoginLike($iUserId,$sUserLogin,$iLimit) {

		$aFriends=$this->FriendFilter($iUserId);

		if (!$aFriends) {
            return array();
		}

        $sql = "SELECT
				u.*					 
			FROM 
				".Config::Get('db.table.user')." as u
			WHERE
				u.user_login LIKE ?	and
				u.user_activate = 1     and
                                u.user_id IN(?a)
			ORDER BY
				u.user_login									
			LIMIT 0, ?d		
				";	
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,$sUserLogin.'%',$aFriends,$iLimit)) {
			foreach ($aRows as $aRow) {
				$aReturn[] = new ModuleUser_EntityUser($aRow);
			}
		}
		return $aReturn;
	}
	
	public function GetUserCollectiveAlbumOwner($iVirtualUserId) {
		$sql = "SELECT DISTINCT 
				u.*			 
			  FROM 
				".Config::Get ( 'plugin.picalbums.table.album' ) . " a, " . Config::Get('db.table.user')." as u
			  WHERE
				a.user_id = ?d AND a.adduser_id = u.user_id
				";
				
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,$iVirtualUserId)) {
			foreach ($aRows as $aRow) {
				$aReturn[] = new ModuleUser_EntityUser($aRow);
			}
		}
		return $aReturn;
	}

}
?>
