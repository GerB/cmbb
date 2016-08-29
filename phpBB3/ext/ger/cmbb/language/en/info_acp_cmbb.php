<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
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
	'ACP_CMBB_TITLE'                                => 'cmBB',
	'CMBB_SETTINGS'                                 => 'cmBB settings',
        'ACP_CMBB_SETTING_SAVED'                        => 'cmBB settings saved',

	'ACP_REACT_FORUM_ID'                            => 'Forum for reaction topics',
	'ACP_REACT_FORUM_ID_EXPLAIN'                    => 'Select the forum to create a topic in for reactions.',
	'NO_REACTIONS'                                  => 'Disable reactions',
	'ACP_NUMBER_INDEX_ITEMS'                        => 'Number of index items',
	'ACP_NUMBER_INDEX_ITEMS_EXPLAIN'                => 'Maximum number of latest items to show on index page. Items are sorted by date (latest on top)',
	'ACP_MIN_POST_COUNT'                            => 'Required posts to create articles',
	'ACP_MIN_POST_COUNT_EXPLAIN'			=> 'Members with this many posts will acquire the permission to create articles. Set to 0 to disable this feature. Moderators and administrators will allways have this permission.',
	'ACP_MIN_TITLE_LENGTH'                          => 'Minumum title length',
	'ACP_MIN_TITLE_LENGTH_EXPLAIN'                  => 'Required minimum length of article titles',
	'ACP_MIN_CONTENT_LENGTH'			=> 'Minumum content length',
	'ACP_MIN_CONTENT_LENGTH_EXPLAIN'		=> 'Required minimum length of article content (body)',
	'ACP_ANNOUNCE_TEXT'                             => 'Announcement text',
	'ACP_ANNOUNCE_TEXT_EXPLAIN'			=> 'Text that will be displayed above all articles and category pages. No BBcode is parsed, use HTML.',
	'ACP_ANNOUNCE_SHOW'                             => 'Show announcement text',
	'ACP_ANNOUNCE_SHOW_EXPLAIN'			=> 'Wether or not to display the text provided above',
));
