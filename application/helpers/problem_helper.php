<?php
function schemaToHint($tables){
	foreach($tables as &$table){
		$hint = array();
		foreach($table as $col_name => $col_meta){
			$hint[]= [
				'text' => $col_name,
				'displayText' => "$col_name | $col_meta[type]"
			];
		}
		$table = $hint;
	}
	return $tables;
}
