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
	'ACL_U_CMBB_POST_ARTICLE'	 => 'Can post cmBB articles',
	'ARTICLE_HIDDEN_WARNING'	 => 'This article is hidden and therefore only accesible for moderators',
	'ARTICLES'					 => 'Articles',
	'ARTICLES_TOTAL'			 => 'Total articles',
	'BACK'						 => 'Back',
	'CATEGORY'					 => 'Category',
	'CMBB_UPLOAD_BROWSE'		 => 'Or browse',
	'CMBB_UPLOAD_DRAG'			 => 'Drag and drop your files here',
	'CMBB_UPLOAD_EXPLAIN'		 => 'Upload files through the upload box below. <br /> Allowed filetypes are: ',
	'COMMENTS'					 => 'Place and/or view comments',
	'COMMENTS_DISABLED'			 => 'Comments disabled',
	'CONTENT'					 => 'Article content',
	'DELETE_ARTICLE'			 => 'Hide article',
	'EDIT_ARTICLE'				 => 'Edit article',
	'ERROR_MUCH_REMOVED'		 => 'You have removed quite a lot from this article. This might be abusive or simple user error. Data is NOT stored.',
	'ERROR_TITLE'				 => 'The provided title is not allowed.',
	'FEATURED_IMG'				 => 'Featured image',
	'LOG_ARTICLE_VISIBILLITY'	 => 'Changed article visibillity',
	'NEW_ARTICLE'				 => 'New article',
	'NO_HIDDEN'					 => 'No hidden articles',
	'NO_REACTIONS_ARTICLE'		 => 'Disable comments <small>(comments might already be disabled through category setting)</small>',
	'READ_MORE'					 => 'Read more...',
	'RESTORE_ARTICLE'			 => 'Restore article',
	'SEARCH_USER_ARTICLES'		 => 'Search user’s articles',
	'SHOW_HIDDEN'				 => 'Show hidden articles',
	'TITLE'						 => 'Title',
	'USE_AVATAR'				 => '-use avatar-',
	'WELCOME_USER'				 => 'Hello %s!',
		));
