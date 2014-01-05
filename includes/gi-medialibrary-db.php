<?php

class gi_medialibrary_db {

    function __construct() {
        global $wpdb;
        //$wpdb->show_errors();
    }

    public function get_numrows() {
        global $wpdb;
        return $wpdb->num_rows;
    }

    public function get_group_subgroups($groupid) {
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}giml_subgroup WHERE groupid={$groupid} ORDER BY subgroupsortorder ASC");
        return $result;
    }

    public function get_groups($sortbyname = false) {
        global $wpdb;
        $sortby = ($sortbyname == false) ? "createddate DESC" : "grouplabel ASC";
        $result = $wpdb->get_results("SELECT id,grouplabel FROM {$wpdb->prefix}giml_group WHERE id>0 ORDER BY " . $sortby);
        return $result;
    }

    public function get_group($ids) {
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}giml_group WHERE id IN ({$ids}) ORDER BY grouplabel ASC");
        return $result;
    }

    public function group_delete($ids) {
        global $wpdb;
        $result = $wpdb->query("DELETE FROM {$wpdb->prefix}giml_group WHERE id IN ({$ids})");
        return $result;
    }

    public function group_add($data) {
        global $wpdb;
        $result = $wpdb->insert($wpdb->prefix . 'giml_group', $data);
        return $wpdb->insert_id;
    }

    public function group_update($data, $id) {
        global $wpdb;
        $result = $wpdb->update($wpdb->prefix . 'giml_group', $data, array('id' => $id), null, array('%d'));
        return $result;
    }

    // SUBGROUPS
    public function get_subgroup($ids) {
        global $wpdb;
        $result = $wpdb->get_results("SELECT `subgroup`.*, `groupid` AS 'subgroupgroup', `group`.`grouplabel`, `group`.`grouprightlabel`, `group`.`groupleftlabel`, `group`.`groupcss`, `group`.`groupdirection`
							FROM {$wpdb->prefix}giml_subgroup `subgroup`, {$wpdb->prefix}giml_group `group`
							WHERE `subgroup`.id IN ({$ids}) AND `group`.id = `subgroup`.groupid
							ORDER BY `subgroup`.subgroupsortorder ASC");
        return $result;
    }

    public function get_independentsubgroups($sortbysortorder = false) {
        global $wpdb;
        $sortby = ($sortbysortorder == false) ? "subgroup.subgrouplabel ASC" : "subgroup.subgroupsortorder ASC";
        $sql = "
			SELECT subgroup.*, subgroup.id AS 'subgroupid'
			FROM {$wpdb->prefix}giml_subgroup AS subgroup
			WHERE groupid = 0
			ORDER BY {$sortby}
		";
        $result = $wpdb->get_results($sql);
        return $result;
    }

    public function get_groupsubgroups($groupid, $sortbysortorder = false) {
        global $wpdb;
        $sortby = ($sortbysortorder == false) ? "subgroup.subgrouplabel ASC" : "subgroup.subgroupsortorder ASC";
        $sql = "
			SELECT subgroup.*, subgroup.id AS 'subgroupid', `group`.grouplabel
			FROM {$wpdb->prefix}giml_group AS `group`, {$wpdb->prefix}giml_subgroup AS `subgroup`
			WHERE subgroup.groupid = {$groupid} AND `group`.id = subgroup.groupid
			ORDER BY {$sortby}
		";
        $result = $wpdb->get_results($sql);
        return $result;
    }

    public function get_subgroups() {
        global $wpdb;
        $sql = "
			SELECT {$wpdb->prefix}giml_group.grouplabel, {$wpdb->prefix}giml_subgroup.subgrouplabel, {$wpdb->prefix}giml_subgroup.id AS 'subgroupid', {$wpdb->prefix}giml_group.id AS 'groupid'
			FROM {$wpdb->prefix}giml_group
			RIGHT OUTER JOIN {$wpdb->prefix}giml_subgroup
			ON {$wpdb->prefix}giml_group.id = {$wpdb->prefix}giml_subgroup.groupid
			ORDER BY {$wpdb->prefix}giml_subgroup.createddate DESC
		";
        /* $sql = "
          SELECT {$wpdb->prefix}giml_group.grouplabel, {$wpdb->prefix}giml_subgroup.subgrouplabel, {$wpdb->prefix}giml_subgroup.id AS 'subgroupid', {$wpdb->prefix}giml_subgroup.groupid AS 'subgroupgroup'
          FROM {$wpdb->prefix}giml_group, {$wpdb->prefix}giml_subgroup
          WHERE {$wpdb->prefix}giml_subgroup.groupid = {$wpdb->prefix}giml_group.id
          ORDER BY {$wpdb->prefix}giml_subgroup.createddate DESC
          "; */
        $result = $wpdb->get_results($sql);
        return $result;
    }

    public function subgroup_delete($ids) {
        global $wpdb;
        $result = $wpdb->query("DELETE FROM {$wpdb->prefix}giml_subgroup WHERE id IN ({$ids})");
        return $result;
    }

    public function subgroup_add($data) {
        global $wpdb;
        $result = $wpdb->insert($wpdb->prefix . 'giml_subgroup', $data);
        $id = $wpdb->insert_id;
        $result = $this->insert('playlisttable', array('subgroupid' => $id));
        $result = $this->insert('playlistcombo', array('subgroupid' => $id));
        return $id;
    }

    public function subgroup_update($data, $id) {
        global $wpdb;
        $result = $wpdb->update($wpdb->prefix . 'giml_subgroup', $data, array('id' => $id), null, array('%d'));
        return $result;
    }

    public function get_playlistcolumnssubgroup($id, $sectionid = null) {
        global $wpdb;
        $sectionid = (is_null($sectionid)) ? "" : " AND section.id={$sectionid}";
        $result = $wpdb->get_results("
							select `section`.playlistsectionlabel, section.id as sectionid, `column`.*, comboitem.playlistcomboitemlabel, comboitem.id as comboitemid
							FROM
								`{$wpdb->prefix}giml_playlistcolumn` `column`, `{$wpdb->prefix}giml_playlistsection` `section`, {$wpdb->prefix}giml_playlistcomboitem AS comboitem
							where `section`.id IN
							(select distinct `column`.playlistcolumnsectionid
							FROM
							`{$wpdb->prefix}giml_playlistcolumn` `column`
								INNER join `{$wpdb->prefix}giml_playlistsection` `section`
								on `column`.`playlistcolumnsectionid` = `section`.`id`
									inner join `{$wpdb->prefix}giml_playlisttable` `table`
									on `table`.`id` = `section`.`playlisttableid`
									WHERE
							`table`.subgroupid = {$id})
							AND `section`.playlistsectioncomboitemid = comboitem.id
							AND `column`.playlistcolumnsectionid = `section`.id {$sectionid}
							ORDER BY `column`.playlistsortorder asc");
        //ORDER BY `column`.createddate desc");
        /* SELECT `section`.playlistsectionlabel, `column`.*, comboitem.playlistcomboitemlabel
          FROM
          {$wpdb->prefix}giml_playlistcolumn `column`, `{$wpdb->prefix}giml_playlistsection` `section`,
          {$wpdb->prefix}giml_playlistcomboitem AS comboitem, {$wpdb->prefix}giml_playlistcombo combo
          WHERE
          combo.subgroupid = {$id}
          AND comboitem.playlistcomboid = combo.id
          AND `section`.playlistsectioncomboitemid = comboitem.id
          AND `column`.playlistcolumnsectionid = `section`.id ORDER BY `column`.playlistcolumnsectionid ASC"); */
        return $result;
    }

    public function get_playlistcolumnsbysection($sectionid, $cols = "") {
        global $wpdb;
        $colsql = (!empty($cols)) ? "AND tablecolumn.playlisttablecolumnlabel IN ({$cols})" : "";
        $result = $wpdb->get_results("
							SELECT section.playlistsectionlabel, playlistcolumn.*, tablecolumn.playlisttablecolumnlabel, tablecolumn.playlisttablecolumntype
							FROM
								{$wpdb->prefix}giml_playlistsection section, {$wpdb->prefix}giml_playlistcolumn playlistcolumn,
								{$wpdb->prefix}giml_playlisttablecolumn tablecolumn
							WHERE
								playlistcolumn.playlistcolumnsectionid = section.id AND tablecolumn.id = playlistcolumn.playlisttablecolumnid
								{$colsql}
								AND section.id= {$sectionid}
							ORDER BY playlistcolumn.rowid ASC");
        return $result;
    }

    public function get_distinctplaylistcolumnsbysection($sectionid, $cols = "") {
        global $wpdb;
        $colsql = (!empty($cols)) ? "AND tablecolumn.playlisttablecolumnlabel IN ({$cols})" : "";
        $result = $wpdb->get_results("
							SELECT distinct playlistcolumn.rowid, playlistcolumn.playlistsortorder
							FROM
								{$wpdb->prefix}giml_playlistsection section, {$wpdb->prefix}giml_playlistcolumn playlistcolumn,
								{$wpdb->prefix}giml_playlisttablecolumn tablecolumn
							WHERE
								playlistcolumn.playlistcolumnsectionid = section.id AND tablecolumn.id = playlistcolumn.playlisttablecolumnid
								{$colsql}
								AND section.id= {$sectionid}
							ORDER BY playlistcolumn.rowid ASC");
        return $result;
    }

    public function get_playlistsectioncolumnsbysection($sectionid, $cols) {
        global $wpdb;
        $result = $wpdb->get_results("
							SELECT section.playlistsectionlabel, sectioncolumn.*, tablecolumn.playlisttablecolumnlabel, tablecolumn.playlisttablecolumntype
							FROM
								{$wpdb->prefix}giml_playlistsection section, {$wpdb->prefix}giml_playlistsectioncolumn sectioncolumn,
								{$wpdb->prefix}giml_playlisttablecolumn tablecolumn
							WHERE
								sectioncolumn.playlistsectionid = section.id AND tablecolumn.id = sectioncolumn.playlisttablecolumnid
								AND tablecolumn.playlisttablecolumnlabel IN ({$cols})
								AND section.id= {$sectionid}
							ORDER BY tablecolumn.playlisttablecolumnsortorder ASC");
        return $result;
    }

    public function get_playlistsectioncolumnssubgroup($id, $comboitemid = 0) {
        global $wpdb;
        $result = $wpdb->get_results("
							select `section`.playlistsectionlabel, `column`.*, comboitem.playlistcomboitemlabel, comboitem.id as comboitemid
							FROM
								`{$wpdb->prefix}giml_playlistsectioncolumn` `column`, `{$wpdb->prefix}giml_playlistsection` `section`, {$wpdb->prefix}giml_playlistcomboitem AS comboitem
							where `section`.id IN
							(select distinct `column`.playlistsectionid
							FROM
							`{$wpdb->prefix}giml_playlistsectioncolumn` `column`
								INNER join `{$wpdb->prefix}giml_playlistsection` `section`
								on `column`.`playlistsectionid` = `section`.`id`
									inner join `{$wpdb->prefix}giml_playlisttable` `table`
									on `table`.`id` = `section`.`playlisttableid`
									WHERE
							`table`.subgroupid = {$id})
							AND `section`.playlistsectioncomboitemid = comboitem.id
							AND comboitem.id = {$comboitemid}
							AND `column`.playlistsectionid = `section`.id ORDER BY `column`.playlistsectionid ASC");
        /*
          $sections = $this->get_playlistsectionssubgroup($id);
          $result = array();
          foreach ($sections as $section) {
          $sectioncolumns = $wpdb->get_results("
          SELECT * FROM {$wpdb->prefix}giml_playlistsectioncolumn
          WHERE playlistsectionid = {$section->id} ORDER BY createddate DESC");
          $tmp = array();
          $tmp['sectionname'] = $section->playlistsectionlabel;
          foreach ($sectioncolumns as $row) {
          $tmp[] = $row->playlistsectiontablecolumntext;
          }
          $result[$section->id] = $tmp;
          }
         */
        return $result;
    }

    public function get_playlistplaylistsectionssubgroup($id) {
        global $wpdb;
        $result = $wpdb->get_results("
						SELECT
							`section`.*, comboitem.playlistcomboitemlabel
						FROM `{$wpdb->prefix}giml_playlistsection` `section`, `{$wpdb->prefix}giml_playlisttable` `table`, {$wpdb->prefix}giml_playlistcomboitem AS comboitem
						WHERE
							`section`.playlistsectioncomboitemid = comboitem.id
						AND `table`.subgroupid = {$id} AND section.playlisttableid = table.id
						 GROUP BY section.playlistsectionlabel");
        /* AFTER WHERE TO AVOID CREATION OF SAME SECTION COLUMNS AGAIN
          `section`.id NOT IN (select distinct `column`.playlistcolumnsectionid
          FROM
          `{$wpdb->prefix}giml_playlistcolumn` `column`
          INNER join `{$wpdb->prefix}giml_playlistsection` `section`
          on `column`.`playlistcolumnsectionid` = `section`.`id`
          inner join `{$wpdb->prefix}giml_playlisttable` `table`
          on `table`.`id` = `section`.`playlisttableid`
          WHERE
          `table`.subgroupid = {$id}
          )
          AND
         */
        return $result;
    }

    public function get_playlistcolumnsectionssubgroup($id) {
        global $wpdb;
        $result = $wpdb->get_results("
						SELECT
							`section`.*, comboitem.playlistcomboitemlabel
						FROM `{$wpdb->prefix}giml_playlistsection` `section`, `{$wpdb->prefix}giml_playlisttable` `table`, {$wpdb->prefix}giml_playlistcomboitem AS comboitem
						WHERE
							`section`.id NOT IN (select distinct `column`.playlistsectionid
							FROM
							`{$wpdb->prefix}giml_playlistsectioncolumn` `column`
								INNER join `{$wpdb->prefix}giml_playlistsection` `section`
								on `column`.`playlistsectionid` = `section`.`id`
									inner join `{$wpdb->prefix}giml_playlisttable` `table`
									on `table`.`id` = `section`.`playlisttableid`
									WHERE
							`table`.subgroupid = {$id}
								)
						AND `section`.playlistsectioncomboitemid = comboitem.id
						AND `table`.subgroupid = {$id} AND section.playlisttableid = table.id
						 GROUP BY section.playlistsectionlabel");
        /* 		$result = $wpdb->get_results("
          SELECT
          `section`.*
          FROM `{$wpdb->prefix}giml_playlistsection` `section`, `{$wpdb->prefix}giml_playlisttable` `table`
          WHERE
          `section`.id NOT IN (select distinct `column`.playlistsectionid
          FROM
          `{$wpdb->prefix}giml_playlistsectioncolumn` `column`
          INNER join `{$wpdb->prefix}giml_playlistsection` `section`
          on `column`.`playlistsectionid` = `section`.`id`
          inner join `{$wpdb->prefix}giml_playlisttable` `table`
          on `table`.`id` = `section`.`playlisttableid`
          WHERE
          `table`.subgroupid = {$id}
          )
          AND `table`.subgroupid = {$id} AND section.playlisttableid = table.id
          GROUP BY section.playlistsectionlabel");
         */
        return $result;
    }

    public function get_playlistcombosectionssubgroup($id, $sortbysortorder = false, $section = 0) {
        global $wpdb;
        $sortby = ($sortbysortorder == true) ? "playlistsectionsortorder ASC" : "createddate DESC";
        $sectionid = (empty($sectionid)) ? "" : " AND playlistsection.id=" . $sectionid;
        $result = $wpdb->get_results("
						SELECT playlistsection.*
						FROM {$wpdb->prefix}giml_playlistsection AS playlistsection, {$wpdb->prefix}giml_playlistcomboitem AS comboitem,
							 {$wpdb->prefix}giml_playlistcombo AS combo
						WHERE comboitem.id = playlistsection.playlistsectioncomboitemid AND combo.id = comboitem.playlistcomboid AND combo.subgroupid = {$id} {$sectionid}
						ORDER BY " . $sortby);
        return $result;
    }

    public function get_playlistcombosections($id, $sortbysortorder = false, $sectionid = 0, $subgroupid = null) {
        global $wpdb;
        $sortby = ($sortbysortorder == true) ? "playlistsectionsortorder ASC" : "playlistsection.playlistsectionlabel ASC";
        $sectionid = (empty($sectionid)) ? "" : " AND playlistsection.id=" . $sectionid;
        $subgroupid = (is_null($subgroupid)) ? "" : " AND playlisttable.subgroupid = {$subgroupid}";
        $result = $wpdb->get_results("
						SELECT playlistsection.*
						FROM {$wpdb->prefix}giml_playlistsection AS playlistsection, {$wpdb->prefix}giml_playlisttable AS playlisttable
						WHERE playlistsection.playlistsectioncomboitemid = {$id} AND playlisttable.id = playlistsection.playlisttableid
						{$sectionid} {$subgroupid}
						ORDER BY " . $sortby);
        return $result;
    }

    public function get_independentplaylistsectionssubgroup($id, $sortbysortorder = false, $sectionid = 0) {
        global $wpdb;
        $sortby = ($sortbysortorder == true) ? "playlistsectionsortorder ASC" : "createddate DESC";
        $sectionid = (empty($sectionid)) ? "" : " AND playlistsection.id=" . $sectionid;
        $result = $wpdb->get_results("
						SELECT playlistsection.*
						FROM {$wpdb->prefix}giml_playlistsection AS playlistsection, {$wpdb->prefix}giml_playlisttable AS playlisttable
						WHERE playlistsection.playlisttableid = playlisttable.id AND playlistsection.playlistsectioncomboitemid = 0
						AND playlisttable.subgroupid = {$id} {$sectionid}
						ORDER BY " . $sortby);
        return $result;
    }

    public function get_playlistsectionssubgroup($id, $sortbysortorder = false, $sectionid = 0) {
        global $wpdb;
        $sortby = ($sortbysortorder == true) ? "playlistsectionsortorder ASC" : "createddate DESC";
        $sectionid = (empty($sectionid)) ? "" : " AND playlistsection.id=" . $sectionid;
        $result = $wpdb->get_results("
						SELECT playlistsection.*, comboitem.playlistcomboitemlabel
						FROM {$wpdb->prefix}giml_playlistsection AS playlistsection, {$wpdb->prefix}giml_playlisttable AS playlisttable, {$wpdb->prefix}giml_playlistcomboitem AS comboitem
						WHERE playlistsection.playlisttableid = playlisttable.id AND playlistsection.playlistsectioncomboitemid = comboitem.id
						AND playlisttable.subgroupid = {$id} {$sectionid}
						ORDER BY " . $sortby);
        /*
          SELECT playlistsection.*
          FROM {$wpdb->prefix}giml_playlistsection AS playlistsection, {$wpdb->prefix}giml_playlisttable AS playlisttable
          WHERE playlistsection.playlisttableid = playlisttable.id
          AND playlisttable.subgroupid = {$id}
          ORDER BY " . $sortby);
          /*if ($wpdb->num_rows == 0) {
          $result = $wpdb->get_results("
          SELECT playlistsection.*
          FROM {$wpdb->prefix}giml_playlistsection as playlistsection, {$wpdb->prefix}giml_playlistcomboitem AS comboitem, {$wpdb->prefix}giml_playlistcombo AS combo
          WHERE playlistsection.playlistsectioncomboitemid = comboitem.id
          AND comboitem.playlistcomboid = combo.id
          AND combo.subgroupid = {$id} ORDER BY createddate DESC");
          } */
        return $result;
    }

    public function get_playlistcombosubgroup($id) {
        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}giml_playlistcombo WHERE subgroupid={$id}");
        return $result;
    }

    public function get_playlistcomboitemssubgroupfirstid($subgroupid) {
        global $wpdb;
        $id = $wpdb->get_col("
						SELECT comboitem.id
						FROM {$wpdb->prefix}giml_playlistcomboitem AS comboitem, {$wpdb->prefix}giml_playlistcombo AS combo
						WHERE comboitem.playlistcomboid = combo.id AND combo.subgroupid = {$subgroupid} AND comboitem.playlistcomboitemdefault = 1
						ORDER BY comboitem.playlistcomboitemsortorder ASC");
        if ($wpdb->num_rows == 0) {
            $id = $wpdb->get_col("
						SELECT comboitem.id
						FROM {$wpdb->prefix}giml_playlistcomboitem AS comboitem, {$wpdb->prefix}giml_playlistcombo AS combo
						WHERE comboitem.playlistcomboid = combo.id AND combo.subgroupid = {$subgroupid}
						ORDER BY comboitem.playlistcomboitemsortorder ASC");
        }
        if ($wpdb->num_rows == 0)
            return "";
        else
            return $id[0];
    }

    public function get_playlistcomboitemssubgroup($id, $sortbysortorder = false) {
        global $wpdb;
        $sortby = ($sortbysortorder == true) ? "playlistcomboitemsortorder ASC" : "playlistcomboitem.playlistcomboitemlabel ASC";
        $result = $wpdb->get_results("
						SELECT playlistcombo.*, playlistcomboitem.*
						FROM {$wpdb->prefix}giml_playlistcombo AS playlistcombo
						LEFT OUTER JOIN {$wpdb->prefix}giml_playlistcomboitem AS playlistcomboitem
						ON playlistcombo.id = playlistcomboitem.playlistcomboid
						WHERE playlistcombo.subgroupid = {$id}
						ORDER BY " . $sortby);
        return $result;
    }

    public function get_playlisttablecolumnssubgroup($id, $sortbysortorder = false) {
        global $wpdb;
        $sortby = ($sortbysortorder == true) ? "playlisttablecolumn.playlisttablecolumnsortorder ASC" : "createddate DESC";
        $result = $wpdb->get_results("
						SELECT playlisttablecolumn.*, playlisttable.playlisttablecss
						FROM {$wpdb->prefix}giml_playlisttable AS playlisttable
						LEFT OUTER JOIN {$wpdb->prefix}giml_playlisttablecolumn AS playlisttablecolumn
						ON playlisttable.id = playlisttablecolumn.playlisttableid
						WHERE playlisttable.subgroupid = {$id} ORDER BY {$sortby}");
        return $result;
    }

    public function get_playlisttablesubgroup($id) {
        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}giml_playlisttable WHERE subgroupid={$id}");
        return $result;
    }

    public function get_playlistnextrowid() {
        global $wpdb;
        $rowid = $wpdb->get_var("select max(rowid) from `{$wpdb->prefix}giml_playlistcolumn`");
        $rowid++;
        return $rowid;
    }

    public function insert($table, $data, $subgroupid = null) {
        global $wpdb;
        switch ($table) {
            case 'playlisttablecolumn':
                $tmp = $this->get_playlisttablesubgroup($subgroupid);
                $data['playlisttableid'] = $tmp->id;
                $result = $wpdb->insert($wpdb->prefix . 'giml_' . $table, $data);
                if (!is_null($subgroupid)) {
                    $id = $wpdb->insert_id;
                    $sections = $this->get_playlistsectionssubgroup($subgroupid);
                    foreach ($sections as $section) {
                        $sectionids = $wpdb->get_results("SELECT playlistsectionid FROM {$wpdb->prefix}giml_playlistsectioncolumn WHERE playlistsectionid = {$section->id}");
                        if (!empty($sectionids)) {
                            $result1 = $wpdb->insert($wpdb->prefix . 'giml_playlistsectioncolumn', array('playlistsectionid' => $section->id,
                                'playlisttablecolumnid' => $id,
                                'playlistsectiontablecolumntext' => '',
                                'createddate' => date('Y-m-d H:i:s')));
                        }
                    }
                    foreach ($sections as $section) {
                        $tmp = $wpdb->get_results("SELECT distinct rowid, playlistcolumnsectionid FROM {$wpdb->prefix}giml_playlistcolumn WHERE playlistcolumnsectionid = {$section->id}");
                        if (!empty($tmp)) {
                            foreach ($tmp as $playlist) {
                                $result1 = $wpdb->insert($wpdb->prefix . 'giml_playlistcolumn', array('playlistcolumnsectionid' => $section->id,
                                    'playlisttablecolumnid' => $id,
                                    'rowid' => $playlist->rowid,
                                    'playlistcolumntext' => '',
                                    'createddate' => date('Y-m-d H:i:s')));
                            }
                        }
                    }
                }
                break;
            case 'playlistcomboitem':
                $tmp = $this->get_playlistcombosubgroup($subgroupid);
                $data['playlistcomboid'] = $tmp->id;
                $result = $wpdb->insert($wpdb->prefix . 'giml_' . $table, $data);
                break;
            case 'playlistsection':
                //if ($data['playlistsectioncomboitemid'] == 0) {
                $tableid = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}giml_playlisttable WHERE subgroupid=%d", $subgroupid));
                $data['playlisttableid'] = $tableid;
                //}
                $result = $wpdb->insert($wpdb->prefix . 'giml_' . $table, $data);
                break;
            default:
                $result = $wpdb->insert($wpdb->prefix . 'giml_' . $table, $data);
        }
        return $result;
    }

    public function update($table, $data, $where) {
        global $wpdb;
        switch ($table) {
            default:
                $result = $wpdb->update($wpdb->prefix . 'giml_' . $table, $data, $where);
        }
        return $result;
    }

    public function delete($table, $ids) {
        global $wpdb;
        switch ($table) {
            case 'playlistsectioncolumn':
                $result = $wpdb->query("DELETE FROM {$wpdb->prefix}giml_{$table} WHERE playlistsectionid IN ({$ids})");
                break;
            case 'playlistcolumn':
                $result = $wpdb->query("DELETE FROM {$wpdb->prefix}giml_{$table} WHERE id IN ({$ids})");
                break;
            default:
                $result = $wpdb->query("DELETE FROM {$wpdb->prefix}giml_{$table} WHERE id IN ({$ids})");
        }
        return $result;
    }

    public function get_playlisttablecolumnsbycolumn($subgroupid, $cols = "") {
        global $wpdb;
        $colsql = (!empty($cols)) ? "AND `column`.playlisttablecolumnlabel IN ({$cols})" : "";
        $result = $wpdb->get_results("SELECT `column`.*, `table`.playlisttablecss
									FROM {$wpdb->prefix}giml_playlisttablecolumn `column`, {$wpdb->prefix}giml_playlisttable `table`
									WHERE `table`.id = `column`.playlisttableid
									{$colsql}
									AND `table`.subgroupid = {$subgroupid}
									ORDER BY `column`.playlisttablecolumnsortorder ASC");
        return $result;
    }

    public function checksectioncolumnscreated($sectionid) {
        global $wpdb;
        $rows = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}giml_playlistsectioncolumn sectioncolumn WHERE sectioncolumn.playlistsectionid = {$sectionid};");
        return ($rows > 0) ? true : false;
    }

    public function select($table, $ids, $sortbysortorder = 0) {
        global $wpdb;
        $result = "";
        switch ($table) {
            case 'playlistsectioncolumn':
                $ids = split(',', $ids);
                $result = array();
                foreach ($ids as $id) {
                    list($comboitemid, $sectionid) = split("_", $id);
                    $rows = $wpdb->get_results("SELECT sectioncolumn.*, section.playlistsectioncomboitemid comboitemid FROM {$wpdb->prefix}giml_playlistsectioncolumn sectioncolumn, {$wpdb->prefix}giml_playlistsection section
					WHERE sectioncolumn.playlistsectionid = {$sectionid} AND section.playlistsectioncomboitemid = {$comboitemid} AND section.id = sectioncolumn.playlistsectionid ORDER BY createddate DESC");
                    //$rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}giml_" . $table . " WHERE playlistsectionid = {$id} ORDER BY createddate DESC");
                    $tmp = array();
                    foreach ($rows as $row) {
                        $tmp['id'] = $row->id;
                        $tmp['playlistsectioncolumncomboitemid'] = $row->comboitemid;
                        $tmp['playlistsectiontablecolumntext_' . $row->playlisttablecolumnid] = $row->playlistsectiontablecolumntext;
                        $tmp['playlistsectionid'] = $row->playlistsectionid;
                    }
                    $result = array_merge($result, array((object) $tmp));
                }
                break;
            case 'playlistcolumn':
                $result = array();
                list($subgroupid, $ids) = split("::", $ids);
                list($comboitemid, $sectionid, $ids) = split("_", $ids);
                $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}giml_" . $table . " WHERE id IN ({$ids}) ORDER BY rowid ASC");
                $tmp = $this->get_playlisttablecolumnssubgroup($subgroupid);
                $totalcols = $this->get_numrows();
                $col = 1;
                $ids = "";
                //$colid = array();
                $rowid = 0;
                $tmp = "";
                $sectionid = "";
                foreach ($rows as $row) {
                    /* if (in_array($row->playlisttablecolumnid, $colid)) {
                      $tmp['id'] = substr($ids, 0, strlen($ids)-1);
                      $result = array_merge($result, array((object) $tmp));
                      $ids = $row->id . ",";
                      $tmp = array();
                      $tmp['playlistcolumntext_' . $row->playlisttablecolumnid] = $row->playlistcolumntext;
                      $tmp['playlistcolumnsectionid'] = $row->playlistcolumnsectionid;
                      $colid = array();
                      $colid[] = $row->playlisttablecolumnid;
                      }else{
                      $ids .= $row->id . ",";
                      //if (is_Array($tmp)) {
                      //$result = array_merge($result, array((object) $tmp));
                      //	$tmp['playlistcolumntext_' . $row->playlisttablecolumnid] = $row->playlistcolumntext;
                      //	$tmp['playlistcolumnsectionid'] = $row->playlistcolumnsectionid;
                      //}else{
                      //$tmp['id'] = substr($ids, 0, strlen($ids)-1);
                      $tmp['playlistcolumntext_' . $row->playlisttablecolumnid] = $row->playlistcolumntext;
                      $tmp['playlistcolumnsectionid'] = $row->playlistcolumnsectionid;
                      //}
                      $colid[] = $row->playlisttablecolumnid;
                      } */
                    if (intval($rowid) == intval($row->rowid)) {
                        if ($col > $totalcols) {
                            $col = 1;
                            $tmp['id'] = substr($ids, 0, strlen($ids) - 1);
                            $result = array_merge($result, array((object) $tmp));
                            $ids = $row->id . ",";
                        }
                        else
                            $ids .= $row->id . ",";
                        $tmp['playlistcolumntext_' . $row->playlisttablecolumnid] = $row->playlistcolumntext;
                        $tmp['playlistcolumnsectionid'] = $row->playlistcolumnsectionid;
                        $tmp['playlistsortorder'] = $row->playlistsortorder;
                        $tmp['playlistcolumncomboitemid'] = $comboitemid;
                    }else {
                        if (is_Array($tmp)) {
                            $tmp['id'] = substr($ids, 0, strlen($ids) - 1);
                            $result = array_merge($result, array((object) $tmp));
                            $col = 1;
                            $ids = "";
                            $tmp = array();
                        }
                        $ids .= $row->id . ",";
                        $rowid = intval($row->rowid);
                        $tmp['playlistcolumntext_' . $row->playlisttablecolumnid] = $row->playlistcolumntext;
                        $tmp['playlistcolumnsectionid'] = $row->playlistcolumnsectionid;
                        $tmp['playlistsortorder'] = $row->playlistsortorder;
                        $tmp['playlistcolumncomboitemid'] = $comboitemid;
                    }
                    $col++;
                }
                $tmp['id'] = substr($ids, 0, strlen($ids) - 1);
                $result = array_merge($result, array((object) $tmp));
                break;
            default:
                if ($sortbysortorder == 0)
                    $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}giml_" . $table . " WHERE id IN ({$ids}) ORDER BY createddate DESC");
                else
                    $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}giml_" . $table . " WHERE id IN ({$ids}) ORDER BY {$table}sortorder ASC");
        }
        return $result;
    }

    public function get_playlistdata($subgroupid) {
        $mydata = "";
        /*
          $results = $this->get_subgroup($subgroupid);
          $mydata['subgroupdownloadlabel'] = (string)$results[0]->subgroupdownloadlabel;
          $mydata['subgroupdownloadlink'] = (string)$results[0]->subgroupdownloadlink;
          $mydata['subgroupdownloadcss'] = (string)$results[0]->subgroupdownloadcss;
          $mydata['subgroupshowfilter'] = (string)$results[0]->subgroupshowfilter;
          $mydata['subgroupshowcombo'] = (string)$results[0]->subgroupshowcombo;
         */
        $results = $this->get_playlisttablecolumnssubgroup($subgroupid);
        $mydata['playlisttablecss'] = (string) $results[0]->playlisttablecss;
        //$mydata['playlisttablecolumn'] = (string)get_playlisttablecolumnssubgroup($subgroupid);
        $results = $this->get_playlistcomboitemssubgroup($subgroupid);
        $mydata['playlistcombolabel'] = (string) $results[0]->playlistcombolabel;
        $mydata['playlistcombocss'] = (string) $results[0]->playlistcombocss;
        $mydata['playlistcombodirection'] = (string) $results[0]->playlistcombodirection;
        /* $mydata['playlistcomboitem'] = (string)get_playlistcomboitemssubgroup($subgroupid);
          $mydata['playlistsection'] = (string)get_playlistsectionssubgroup($subgroupid);
          $mydata['playlistcolumnsections'] = (string)get_playlistcolumnsectionssubgroup($subgroupid);
          $mydata['playlistsectioncolumn'] = (string)get_playlistsectioncolumnssubgroup($subgroupid);
          $mydata['playlistcolumns'] = (string)get_playlistcolumnssubgroup($subgroupid);
          $mydata['playlistplaylistsections'] = (string)get_playlistplaylistsectionssubgroup($subgroupid);
         */
        return (object) $mydata;
    }

    function increment_registration_id($groupid) {
        $group = $this->get_group($groupid);
        $nextnum = intval($group[0]->groupregistrationnumber);
        $nextnum++;
        global $wpdb;
        $result = $wpdb->update($wpdb->prefix . 'giml_group', array('groupregistrationnumber'=>$nextnum), array('id' => $groupid), null, array('%d'));
    }
}

?>