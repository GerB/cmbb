<?php
/*
 * Construct left sidebar of CMS frontend pages
 */
if (!defined('IN_CMBB')) {
    trigger_error($user->lang('MODULE_NOT_ACCESS'));
}

// Either greet or show login box
if ($user->data['is_registered']) {
    $cmbb_sidebar = '<div id="login">';
    $cmbb_sidebar.= "<h3>Welkom ".$user->data['username']."</h3>";
    $cmbb_sidebar.= ' (<a href="'. append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=logout', true, $user->session_id) . '">' . $user->lang('LOGOUT') . '</a>)';

    if ($mode != 'c') {
        // Show link to editor when not in contact mode
        if (( $auth->acl_get('m_') || ($user->data['user_posts'] >= MIN_POST_COUNT) || $cmbb->has_written($user->data['user_id']) ) && ($user->data['is_bot'] == FALSE) && (!phpbb_is_banned($user->data['user_id']))) {
            $cmbb_sidebar.= '<p class="fakebutton"><a href="index.php?m=n">+ Nieuw artikel</a></p>';
        }
        if (( ($user->data['user_id'] == $page['user_id']) || $auth->acl_get('m_') ) && ($page['is_cat'] == 0)) {
            $cmbb_sidebar.= '<p class="fakebutton"><a href="index.php?m=e&p='.$page['article_id'].'">Bewerk artikel</a></p>';
        }
        if ($auth->acl_get('m_')) {
            $cmbb_sidebar.= '<br /><hr /><br />';
            if ($page['is_cat'] == 0) {
                if ($page['visible'] == 1) {
                    $cmbb_sidebar.= '<p class="fakebutton"><a onclick="return confirm(\'Weet je zeker dat je deze pagina wil verbergen?\')" href="index.php?m=h&a=h&p='.$page['article_id'].'">Verberg artikel</a></p>';
                }
                else {
                    $cmbb_sidebar.= '<p class="fakebutton"><a onclick="return confirm(\'Weet je zeker dat je deze pagina zichtbaar wil maken?\')" href="index.php?m=h&a=u&p='.$page['article_id'].'">Terugzetten</a></p>';
                }
            }
            if ($cmbb->get_hidden()) {
                $cmbb_sidebar.= '<p class="fakebutton"><a href="index?showhidden=1">Toon verborgen artikelen</a></p>';
            }
            else {
                $cmbb_sidebar.= '<p class="fakebutton inactive"><a>Geen verborgen artikelen</a></p>';
            }
            $cmbb_sidebar.= '<p class="fakebutton"><a href="index?m=a">Site beheer</a></p>';
        }
    }

    $cmbb_sidebar.= '</div>';
}
else {
    $cmbb_sidebar = '<div id="login"><h3>' . $user->lang('LOGIN') . ':</h3><br />
			<form method="post" action="'.$phpbb_root_path.'ucp.php?mode=login">
				<p><span>' . $user->lang('USERNAME') . ':</span> <input type="text" name="username" size="14" /><br />
				<span>' . $user->lang('PASSWORD') . ':</span> <input type="password" name="password" size="14" /><br />
				<span>' . $user->lang('LOG_ME_IN') . ':</span> <input type="checkbox" name="autologin" /><br />
				<input type="submit" class="btnmain" value="' . $user->lang('LOGIN') . '" name="login" /></p>
				<p><input type="hidden" name="redirect" value="' . CMBB_ROOT . '/{CMBB_ALIAS}" /></p>
			</form>
		</div>';
}

/*
 * Fetch newest topics
 */
$cmbb_sidebar.=
    '<div id="topiclist"><hr />
		<h3>' . $user->lang('FEED_TOPICS_NEW') . '</h3><br />
		<ul class="lt">';

$latest = phpbb_latest_topics(5);
foreach ($latest as $row)
{

    $url = $phpbb_root_path . '/viewtopic.php?f='.$row['forum_id'].'&amp;t='.$row['topic_id'].'&amp;view=unread#unread';
    $cmbb_sidebar.= '<li class="lt"><a href="'.$url.'">'.$row['topic_title'].'</a> </li>';
}

$cmbb_sidebar.=
    '</ul>
</div>';

/*
 * Fetch stats
 */
$whosonline = obtain_users_online_string(obtain_users_online());

$cmbb_sidebar.=
    '<div id="stats"><hr />
        <h3> ' . $user->lang('STATISTICS') . '</h3>
        <p><a href="'.$phpbb_root_path.'viewonline.php">' . $user->lang('WHO_IS_ONLINE') . '</a>:<br>'. $whosonline['online_userlist'] .'</p>';

$cmbb_sidebar.= '<p>' . $user->lang('TOTAL_USERS', (int) $config['num_users']) . '<br />' .
        $user->lang('NEWEST_USER', get_username_string('full', $config['newest_user_id'], $config['newest_username'], $config['newest_user_colour'])) .
        '</p></div>';
