<?php

/**
 *
 * cmBB [English]
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_CATEGORIES_MANAGE'				=> 'Manage categories',
	'ACP_CATEGORIES_MANAGE_EXPLAIN'		=> 'Here you can add, modify or delete categories. You should first create a category and submit, and change the setting next.'
											. '<br />Please note: cmBB uses the category name for the URL. Once you have chosen a category name, it is therefore recommended NOT to change the name afterwards since the URL will NOT be changed accordingly.',
	'ACP_CMBB_CATEGORIES'				=> 'Categories',
	'ACP_CMBB_SETTING_SAVED'			=> 'cmBB settings saved',
	'ACP_CMBB_TITLE'					=> 'cmBB',
	'ACP_MIN_TITLE_LENGTH'				=> 'Minumum title length',
	'ACP_MIN_TITLE_LENGTH_EXPLAIN'		=> 'Required minimum length of article titles',
	'ACP_MIN_CONTENT_LENGTH'			=> 'Minumum content length',
	'ACP_MIN_CONTENT_LENGTH_EXPLAIN'	=> 'Required minimum length of article content (body). Prevents gibberish articles.',
	'ACP_NO_ARTICLES'					=> 'You have no (active) articles. Create a new article using the link below:',
	'ACP_NUMBER_INDEX_ITEMS'			=> 'Number of index items',
	'ACP_NUMBER_INDEX_ITEMS_EXPLAIN'	=> 'Maximum number of latest items to show on index page. Items are sorted by date (latest on top)',
	'ACP_REACT_FORUM_ID'				=> 'Forum for comment topics',
	'ACP_REACT_FORUM_ID_EXPLAIN'		=> 'Select the forum to create a topic in for comments.',
	'ACP_SHOW_MENUBAR'					=> 'Show menubar',
	'ACP_SHOW_MENUBAR_EXPLAIN'			=> 'Menubar is added to the header, contains all categories with children alongside homepage (if any), board index and contact us (if enabled).',
	'ACP_SHOW_RIGHTBAR'					=> 'Show right sidebar',
	'ACP_SHOW_RIGHTBAR_EXPLAIN'			=> 'You can choose to show a sidebar on the right, containg any HTML you want. Useful for advertisements or any other content you might want to show.',
	'ACP_RIGHTBAR_HTML'					=> 'Right sidebar content.',
	'ACP_RIGHTBAR_HTML_EXPLAIN'			=> 'If you have the right sidebar enabled, the content entered here will be shown. You can use any HTML/JS you want, just make sure it is valid.',
	'CHILDREN'							=> 'Children',
	'CHILDREN_EXPLAIN'					=> 'Number of articles in this category',
	'CMBB_CATEGORY_NAME_INVALID'		=> 'Category name invalid',
	'CMBB_SETTINGS'						=> 'cmBB settings',
	'CMBB_DELETE_CAT_EXPLAIN'			=> 'A category can only be deleted when it holds no articles',
	'CREATE_CATEGORY'					=> 'Add category',
	'ERROR_CATEGORY_NOT_EMPTY'			=> 'Category not empty',
	'ERROR_FAILED_DELETE'				=> 'Failed to delete.',
	'NO_REACTIONS'						=> 'Disable comments',
	'PROTECTED'							=> 'Protected',
	'PROTECTED_EXPLAIN'					=> 'Only moderators are allowed to post',
	'SHOW_MENU_BAR'						=> 'Show in menu bar',
	'SHOW_MENU_BAR_EXPLAIN'				=> 'Wether or not to show this category in the menu bar (only when it has children). Useful to disable if you do not like the category listings or you have just some loose articles.',

		));
