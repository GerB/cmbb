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
    'ARTICLE_HIDDEN_WARNING'  => 'This page is hidden and therefore only accesible for moderators',
    'BACK'                    => 'Back',
    'CATEGORY'                => 'Category',
    'COMMENTS'                => 'Place and/or view comments',
    'CONTENT'                 => 'Article content',
    'DELETE_ARTICLE'          => 'Hide article',
    'EDIT_ARTICLE'            => 'Edit article',
    'ERROR_MUCH_REMOVED'      => 'You have removed quite a lot from this article. This might be abusive or simple user error. Data is NOT stored.',
    'ERROR_TITLE'             => 'The provided title is not allowed.',
    'LOG_ARTICLE_VISIBILLITY' => 'Changed article visibillity',
    'NEW_ARTICLE'             => 'New article',
    'NO_HIDDEN'               => 'No hidden articles',
    'READ_MORE'               => 'Read more&#8230;',
    'RESTORE_ARTICLE'         => 'Restore article',
    'SHOW_HIDDEN'             => 'Show hidden articles',
    'TITLE'                   => 'Title',
    'WELCOME_USER'            => 'Hello %s!',
        ));
