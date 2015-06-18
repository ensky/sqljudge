<?php
function schemaToHint($schema){
	$tables = array();

	foreach($schema as $table_name => &$table_schema){
		$table = ['text' =>  $table_name, 'columns' => []];

		foreach($table_schema as $col_name => $col_meta){
			array_push($table['columns'], [
				'text' => $col_name,
				'displayText' => "$col_name | $col_meta[type]"
			]);
		}
		array_push($tables, $table);
	}
	return $tables;
}
