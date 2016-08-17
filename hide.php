<?php
/*
 * Hide CMS page
 */
if (!defined('IN_CMBB'))
{
	trigger_error($user->lang('MODULE_NOT_ACCESS'));
}

// Some checks before we do anything
if (! ($auth->acl_get('m_') ) && ($page['is_cat'] == 0) )
{
	trigger_error($user->lang('NOT_AUTHORISED'));
}

$article_id = $_GET['p'];

$article_data = array (
	'article_id' => $article_id,
	'visible' => (($_GET['a'] == 'h') ? 0 : 1),
	);
	
$store = $cmbb->store_article($article_data);

add_log('admin', 'Zichtbaarheid pagina aangepast', 'article_id ' . $_GET['p'] . ' ' . ( ($article_data['visible'] == '1') ? 'zichtbaar' : 'onzichtbaar') . ' gemaakt' );

// Go home
header('Location: ' . CMBB_ROOT);