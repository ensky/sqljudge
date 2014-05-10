<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$active_group = 'default';
$active_record = TRUE;

/*
 * Database for system
 */

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = '';
$db['default']['password'] = '';
$db['default']['database'] = 'sqljudge_sys';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = False;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

/*
 * Database for scoring student's answer
 */
$db['judge']['hostname'] = 'localhost';
$db['judge']['username'] = '';
$db['judge']['password'] = '';
$db['judge']['database'] = 'sqljudge_problem_judge';
$db['judge']['dbdriver'] = 'mysqli';
$db['judge']['dbprefix'] = '';
$db['judge']['pconnect'] = TRUE;
$db['judge']['db_debug'] = False;
$db['judge']['cache_on'] = FALSE;
$db['judge']['cachedir'] = '';
$db['judge']['char_set'] = 'utf8';
$db['judge']['dbcollat'] = 'utf8_general_ci';
$db['judge']['swap_pre'] = '';
$db['judge']['autoinit'] = TRUE;
$db['judge']['stricton'] = FALSE;

/*
 * Database for testing student's answer
 */
$db['test']['hostname'] = 'localhost';
$db['test']['username'] = '';
$db['test']['password'] = '';
$db['test']['database'] = 'sqljudge_problem_test';
$db['test']['dbdriver'] = 'mysqli';
$db['test']['dbprefix'] = '';
$db['test']['pconnect'] = TRUE;
$db['test']['db_debug'] = False;
$db['test']['cache_on'] = FALSE;
$db['test']['cachedir'] = '';
$db['test']['char_set'] = 'utf8';
$db['test']['dbcollat'] = 'utf8_general_ci';
$db['test']['swap_pre'] = '';
$db['test']['autoinit'] = TRUE;
$db['test']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */