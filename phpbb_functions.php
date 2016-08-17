<?php
if (!defined('IN_CMBB')) {
    trigger_error($user->lang('MODULE_NOT_ACCESS'));
}

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : 'phpBB3/';
$phpEx           = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path.'common.'.$phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

/**
 * Get formatted link to user
 * @param int $userid
 * @return string 
 */
function phpbb_get_user($userid)
{
    global $db, $phpbb_root_path;

    $user_id = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);
    if (empty($user_id)) {
        return FALSE;
    }

    $sql    = 'SELECT `username`, `user_colour` , `user_id`, `group_id`
		FROM '.USERS_TABLE.' 
		WHERE user_id ='.$userid;
    $result = $db->sql_query($sql);
    $row    = $db->sql_fetchrow($result);

    if ($row['group_id'] != "754") {
        return'<a href="'.$phpbb_root_path.'memberlist.php?mode=viewprofile&amp;u='.$row['user_id'].'" style="color:#'.$row['user_colour'].'; font-weight: bold;">'.$row['username'].'</a>';
    }
    else {
        return '<span style="color:#'.$row['user_colour'].';">'.$row['username'].'</span>';
    }
}
/*
 * Get formatted user avatar or default avatar
 */

function phpbb_user_avatar($userid, $style = '')
{
    global $db, $phpbb_root_path;
    $user_id = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);
    if (empty($user_id)) {
        return FALSE;
    }

    $sql    = 'SELECT `username`, `user_avatar`, `user_avatar_type` , `user_avatar_width`, `user_avatar_height`
		FROM '.USERS_TABLE.' 
		WHERE user_id = '.$userid;
    $result = $db->sql_query($sql);
    $row    = $db->sql_fetchrow($result);

    if (!$row) {
        $row = array(
            'username' => '',
            'user_avatar' => '',
            'user_avatar_type' => '',
            'user_avatar_width' => 0,
            'user_avatar_height' => 0,
        );
    }
    if (substr($row['user_avatar'], 0, 4) == 'http') {
        $path = $row['user_avatar'];
    }
    else if (empty($row['user_avatar'])) {
        $path                      = CMBB_ROOT.'/assets/site/default_avatar.jpg';
        $row['user_avatar_width']  = $row['user_avatar_height'] = 90;
    }
    else {
        $path = $phpbb_root_path.'/download/file.php?avatar='.$row['user_avatar'];
    }

    if ($row['user_avatar_width'] > $row['user_avatar_height']) {
        $width_height = ' width="90" ';
    }
    else {
        $width_height = ' height="90" ';
    }

    return '<img src="'.$path.'" '.$width_height.' alt= "'.$row['username'].'" title="'.$row['username'].'" style="'.$style.'" />';
}

/**
 * Fetch newest topics
 * @param int $topic_limit = 5
 * @return array
 */
function phpbb_latest_topics($topic_limit = 5)
{
    global $db, $auth;
    $topic_limit = filter_var($topic_limit, FILTER_SANITIZE_NUMBER_INT);
    if (empty($topic_limit)) {
        return FALSE;
    }

    // Grabbing permissions
    $forums = array_unique(array_keys($auth->acl_getf('f_read', true)));

    // Select the last topics to which we have permissions
    $sql    = 'SELECT t.topic_id, t.forum_id, t.topic_title
			FROM '.TOPICS_TABLE.' t , '.USERS_TABLE.' u
			WHERE topic_visibility = 1
			AND '.$db->sql_in_set('forum_id', $forums).'
			AND u.user_id = t.topic_poster
			ORDER BY topic_time DESC
			LIMIT 0,'.$topic_limit;
    $result = $db->sql_query($sql);

    while ($row = $db->sql_fetchrow($result))
    {
        $return[] = array(
            'topic_id' => $row['topic_id'],
            'forum_id' => $row['forum_id'],
            'topic_title' => character_limiter($row['topic_title'], 30, '&hellip;'),
        );
    }
    return empty($return) ? FALSE : $return;
}

/**
 * Check if user is in ban table
 * @param int $user_id
 * @return bool
 */
function phpbb_is_banned($userid)
{
    global $db;
    $user_id = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);
    if (empty($user_id)) {
        return FALSE;
    }
    $sql    = 'SELECT count(*) as banned FROM '.BANLIST_TABLE.' WHERE ban_userid = '.$userid;
    $result = $db->sql_query($sql);
    $banned = (int) $db->sql_fetchfield('banned');
    $db->sql_freeresult($result);

    if (!empty($banned)) {
        return TRUE;
    }
    else {
        return FALSE;
    }
}

/**
 * Validate and cleanup title
 * Uses default censor_text from phpBB with some added blacklist words for CMS
 * @param string $title
 * @return string
 */
function phpbb_censor_title($title)
{
    $disallowed = array(
        'index',
        'home',
        'homepage',
        'test',
    );
    if (in_array(strtolower(trim($title)), $disallowed)) {
        return FALSE;
    }
    return trim(censor_text($title));
}

/**
 * Create a topic with intro for article
 * @param array $article_data
 * @return string
 */
function phpbb_create_article_topic($article_data)
{

    global $db, $config, $phpbb_root_path, $phpEx;

    if (!function_exists('get_username_string')) {
        include($phpbb_root_path.'includes/functions_content.'.$phpEx);
    }
    if (!function_exists('submit_post')) {
        include($phpbb_root_path.'includes/functions_posting.'.$phpEx);
    }
    $article_data['user_id'] = filter_var($article_data['user_id'], FILTER_SANITIZE_NUMBER_INT);
    if (empty($article_data['user_id'])) {
        return FALSE;
    }
    $sql      = 'SELECT *
		FROM '.USERS_TABLE."
		WHERE user_id = ".intval($article_data['user_id'])."";
    $dbresult = $db->sql_query($sql);
    $row      = $db->sql_fetchrow($dbresult);
    $db->sql_freeresult($dbresult);

    if (empty($row)) {
        return false;
    }

    $topic_content = '[b][size=150]'.$article_data['title'].'[/size][/b]
[i]Auteur: '.$row['username'].'[/i] 

'.character_limiter(strip_tags($article_data['content'])).'
[url='.CMBB_ROOT.'/'.$article_data['alias'].']Lees verder...[/url]';

    $poll     = $uid      = $bitfield = $options  = '';

    // will be modified by generate_text_for_storage
    $allow_bbcode  = $allow_urls    = $allow_smilies = true;
    generate_text_for_storage($topic_content, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

    $data = array(
        // General Posting Settings
        'forum_id' => REACT_FORUM_ID, // The forum ID in which the post will be placed. (int)
        'topic_id' => 0, // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
        'icon_id' => false, // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
        // Defining Post Options
        'enable_bbcode' => true, // Enable BBcode in this post. (bool)
        'enable_smilies' => true, // Enabe smilies in this post. (bool)
        'enable_urls' => true, // Enable self-parsing URL links in this post. (bool)
        'enable_sig' => true, // Enable the signature of the poster to be displayed in the post. (bool)
        // Message Body
        'message' => $topic_content, // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
        'message_md5' => md5($topic_content), // The md5 hash of your message
        // Values from generate_text_for_storage()
        'bbcode_bitfield' => $bitfield, // Value created from the generate_text_for_storage() function.
        'bbcode_uid' => $uid, // Value created from the generate_text_for_storage() function.    // Other Options
        'post_edit_locked' => 0, // Disallow post editing? 1 = Yes, 0 = No
        'topic_title' => $article_data['title'],
        'notify_set' => false, // (bool)
        'notify' => false, // (bool)
        'post_time' => 0, // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
        'forum_name' => '', // For identifying the name of the forum in a notification email. (string)    // Indexing
        'enable_indexing' => true, // Allow indexing the post? (bool)    // 3.0.6
        'force_visibility' => true, // 3.1.x: Allow the post to be submitted without going into unapproved queue, or make it be deleted (replaces force_approved_state)
    );

    $url      = submit_post('post', $article_data['title'], 'Hoofdpagina', POST_NORMAL, $poll, $data);
    $topic_id = str_replace('&amp;t=', '', strstr($url, '&amp;t='));
    return $topic_id;
}

/**
 * Create a forum topic with contact message
 * @param string $name
 * @param string $subject
 * @param string $message
 * @return int
 */
function phpbb_create_contact_topic($name, $subject, $message)
{

    global $db, $config, $phpbb_root_path, $phpEx;

    if (!function_exists('get_username_string')) {
        include($phpbb_root_path.'includes/functions_content.'.$phpEx);
    }
    if (!function_exists('submit_post')) {
        include($phpbb_root_path.'includes/functions_posting.'.$phpEx);
    }

    $poll     = $uid      = $bitfield = $options  = '';

    // will be modified by generate_text_for_storage
    $allow_bbcode  = $allow_urls    = $allow_smilies = true;
    generate_text_for_storage($message, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

    $data = array(
        // General Posting Settings
        'forum_id' => CONTACT_FORUM_ID, // The forum ID in which the post will be placed. (int)
        'topic_id' => 0, // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
        'icon_id' => false, // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
        // Defining Post Options
        'enable_bbcode' => true, // Enable BBcode in this post. (bool)
        'enable_smilies' => true, // Enabe smilies in this post. (bool)
        'enable_urls' => true, // Enable self-parsing URL links in this post. (bool)
        'enable_sig' => true, // Enable the signature of the poster to be displayed in the post. (bool)
        // Message Body
        'message' => $message, // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
        'message_md5' => md5($message), // The md5 hash of your message
        // Values from generate_text_for_storage()
        'bbcode_bitfield' => $bitfield, // Value created from the generate_text_for_storage() function.
        'bbcode_uid' => $uid, // Value created from the generate_text_for_storage() function.    // Other Options
        'post_edit_locked' => 0, // Disallow post editing? 1 = Yes, 0 = No
        'topic_title' => $subject,
        'notify_set' => false, // (bool)
        'notify' => false, // (bool)
        'post_time' => 0, // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
        'forum_name' => '', // For identifying the name of the forum in a notification email. (string)    // Indexing
        'enable_indexing' => true, // Allow indexing the post? (bool)    // 3.0.6
        'force_visibility' => true, // 3.1.x: Allow the post to be submitted without going into unapproved queue, or make it be deleted (replaces force_approved_state)
    );

    $url      = submit_post('post', $subject, $name, POST_NORMAL, $poll, $data);
    $topic_id = str_replace('&amp;t=', '', strstr($url, '&amp;t='));
    return $topic_id;
}
// EoF