<?php
/**
 * Simple CMS for phpBB 3.2
 * Built for Sword Facts Benelux in the past, see http://swordfactsbenelux.nl
 * Now rewritten extended for use by everyone
 *
 * @author Ger Bruinsma 
 */

/*
 * index.php is just a startup
 * We require acces by this path
 * so we'll have total control
 */
 
// Initialize
define('IN_CMBB', TRUE);
include('constants.php');
include('cms_functions.php');
include('phpbb_functions.php');
include('db.php');

// Startup CMBB main class with same config as phpBB
$cmbb = new cmbb($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, $table_prefix);
$site_config = $cmbb->get_config();

if ($site_config['site_disabled'] != 0) {
    header('Location: under_construction.html');
}
// What are we doing?
$mode = $request->variable('m', 'view');

if ( (!defined('CMBB_INSTALLED')) && ($mode !== 'install') ) {
    header('Location: index.php?m=install');
}

switch($mode)
{
	case 'e': // edit existing page
	case 'n': // create new page
		include('edit.php');
		break;
	
	case 's':
		include('store.php');
		break;	
		
	case 'h':
		include('hide.php');
		break;
		
	case 'a':
		include('site_config.php');
		break;		
		
	case 'c':
		include('contactform.php');
		break;
		
	case 'install':
		include('install.php');
		break;
		
	case 'view':
	default:
		include('view.php');
		break;
}


// EoF