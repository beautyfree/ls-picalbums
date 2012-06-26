<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Livestreet Picture Albums
 * @Plugin URI: http://lsmafia.com/blog/picalbums/3.html
 * @Authors: Sebastian Prelesniy (sebastian.prelesniy@gmail.com) and Lora_GT (gttgirl@gmail.com)
 * @Contacts: http://lsmafia.com
 * ----------------------------------------------------------------------------
 */

class PluginPicalbums_ModuleTag_MapperTag extends Mapper {
	
	public function GetTags($iLimit) {
		$sql = "SELECT
			tt.tag_text,
			count(tt.tag_text) as count
			FROM
				".Config::Get('plugin.picalbums.table.tag')." as tt
			GROUP BY
				tt.tag_text
			ORDER BY
				count desc
			LIMIT 0, ?d
				";
		$aReturn=array();
		$aReturnSort=array();
		if ($aRows=$this->oDb->select(
				$sql,
				$iLimit
			)
		) {
			foreach ($aRows as $aRow) {
				$aReturn[mb_strtolower($aRow['tag_text'],'UTF-8')]=$aRow;
			}
			ksort($aReturn);
			foreach ($aReturn as $aRow) {
				$aReturnSort[]=Engine::GetEntity('PluginPicalbums_Tag',$aRow);
			}
		}
		return $aReturnSort;
	}

    public function GetTagsByTargetId($iTargetId) {
		$sql = 	" SELECT tag_text " .
				" FROM " . Config::Get ( 'plugin.picalbums.table.tag' )  .
				" WHERE target_id = ?d ORDER BY tag_text ASC ";

		if($aRows = $this->oDb->selectCol($sql, $iTargetId)) {
           return $aRows;
        }
		return false;
	}

    public function GetTagsByLike($sTag,$iLimit) {
		$sTag=mb_strtolower($sTag,"UTF-8");
		$sql = "SELECT
				tag_text
			FROM
				".Config::Get('plugin.picalbums.table.tag')."
			WHERE
				tag_text LIKE ?
			GROUP BY
				tag_text
			LIMIT 0, ?d
				";
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,$sTag.'%',$iLimit)) {
			foreach ($aRows as $aRow) {
				$aReturn[]=Engine::GetEntity('PluginPicalbums_Tag',$aRow);
			}
		}
		return $aReturn;
	}


	public function AddTag($oTag) {
		$sql = "INSERT INTO ".Config::Get('plugin.picalbums.table.tag')."
			(target_id,
			tag_text
			)
			VALUES(?d,  ?)
		";
		if ($iId=$this->oDb->query($sql,$oTag->getTargetId(),$oTag->getText()))
		{
			return $iId;
		}
		return false;
	}
	
	public function DeleteTagsByTargetId($iTargetId) {
		$sql = " DELETE FROM " . Config::Get ( 'plugin.picalbums.table.tag' ) . " WHERE target_id = ?d ";
		if ($this->oDb->query ( $sql, $iTargetId )) {
			return true;
		}
		return false;
	}
}
?>
