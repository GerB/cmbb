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

namespace ger\cmbb\controller;

class page
{
    /* @var \phpbb\config\config */
    protected $config;

    /* @var \phpbb\controller\helper */
    protected $helper;

    /* @var \phpbb\template\template */
    protected $template;

    /* @var \phpbb\user */
    protected $user;

    /* @var \phpbb\auth\auth */
    protected $auth;

    /* @var \phpbb\request\request_interface */
    protected $request;
    protected $phpbb_root_path;

    /* @var \ger\cmbb\cmbb\driver */
    protected $cmbb;

    /**
     * Constructor
     *
     * @param \phpbb\config\config		$config
     * @param \phpbb\controller\helper	$helper
     * @param \phpbb\template\template	$template
     * @param \phpbb\user				$user
     */
    public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, $phpbb_root_path, \ger\cmbb\cmbb\driver $cmbb)
    {
        $this->config          = $config;
        $this->helper          = $helper;
        $this->template        = $template;
        $this->user            = $user;
        $this->auth            = $auth;
        $this->request         = $request;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->cmbb            = $cmbb;

        include($this->phpbb_root_path.'/ext/ger/cmbb/cmbb/presentation.php');
//        include($this->phpbb_root_path.'/ext/ger/cmbb/cmbb/sidebar.php');
//        $this->sidebar = new \ger\cmbb\cmbb\sidebar();
    }

    /**
     * Controller for route /page/{alias}
     *
     * @param string		$alias
     * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
     */
    public function handle($alias = 'index')
    {
        $page = $this->cmbb->get_article($alias);
        if ($page === FALSE) {
            return $this->helper->message('FILE_NOT_FOUND_404', array(
                    $alias), 'FILE_NOT_FOUND_404', 404);
        }
        if ($page['visible'] == 0) {
            if ($this->auth->acl_get('m_')) {
                $page['content'] = '<div class="warning">Deze pagina is verborgen en daarom alleen zichtbaar voor teamleden.</div>'.$page['content'];
            }
            else {
                return $this->helper->message('FILE_NOT_FOUND_404', array(
                        $alias), 'FILE_NOT_FOUND_404', 404);
            }
        }

        // List child pages exerpts as content when it's a category
        if ($page['is_cat']) {
            $page['content'] = '';
            if ($page['alias'] == 'index') {
                if ($this->request->variable('showhidden', '') == 1) {
                    if (!$this->auth->acl_get('m_')) {
//                            trigger_error('Je beschikt niet over de juiste permissies voor deze actie.');
                        return $this->helper->message('FILE_NOT_FOUND_404', array(
                                $alias), 'FILE_NOT_FOUND_404', 404);
                    }
                    $children = $this->cmbb->get_hidden();
                }
                else {
                    $children = $this->cmbb->get_last($this->config['ger_cmbb_number_index_items']);
                    if ($this->config['ger_cmbb_announce_show'] == 1) {
                        $page['content'] = '<div class="box">'.htmlspecialchars_decode($this->config['ger_cmbb_announce_text']).'</div><hr>';
                    }
                }
            }
            else {
                $children = $this->cmbb->get_children($page['article_id']);
            }

            $count = count($children);
            if (empty($children)) {
                $page['content'] = ' ';
            }
            else {
                $counter = 0;
                foreach ($children as $child)
                {
                    $counter++;
                    $page['content'] .= '<div class="box"><a href="'.$child['alias'].'"><h2>'.$child['title'].'</h2></a>';
                    $page['content'] .= '<div><div class="exerpt_img"><a href="'.$child['alias'].'">'.$this->cmbb->phpbb_user_avatar($child['user_id']).'</a></div>';
                    $page['content'] .= closetags(character_limiter(clean_html($child['content'])));
                    $page['content'] .= ' <a href="'.$child['alias'].'">Lees verder&#8230;</a></div></div>';

                    if ($counter < $count) {
                        $page['content'] .= '<hr>';
                    }
                }
            }
        }
        // Do breadcrumbs
        if ($page['alias'] == 'index') {
            $path = '';
        }
        else {
            $parents[] = $this->cmbb->get_article(intval($page['parent']));

            if ($parents[0]['parent'] != 0) {
                $parents[] = $this->cmbb->get_article($parents[0]['parent']);
                if ($parents[1]['parent'] != 0) {
                    $parents[] = $this->cmbb->get_article($parents[1]['parent']);
                    if ($parents[2]['parent'] != 0) {
                        $parents[] = $this->cmbb->get_article($parents[0]['parent']);
                        if ($parents[3]['parent'] != 0) {
                            $parents[] = $this->cmbb->get_article($parents[3]['parent']);
                        }
                    }
                }
            }
            $parents = array_reverse($parents);
            $path    = '<a href="'.CMBB_ROOT.'">'.(($this->config['site_home_text'] !== '') ? $this->config['site_home_text'] : $this->user->lang('HOME')).'</a>  &raquo; ';
            foreach ($parents as $parent)
            {
                if ($parent['title'] != 'Home') {
                    $path.= '<a href="'.$parent['alias'].'">'.$parent['title'].'</a>  &raquo; ';
                }
            }
            $path = '<div class="bread">'.$path.$page['title'].'</div>';
        }

        // Wrap it all up
        $title = empty($page['title']) ? (($this->config['site_home_text'] !== '') ? $this->config['site_home_text'] : $this->user->lang('HOME')) : $page['title'];

        $this->template->assign_vars(array(
            'CMBB_BREADCRUMBS' => $path,
            'CMBB_CATEGORY_NAME' => $this->cmbb->fetch_category($page['category_id']),
            'S_CMBB_CATEGORY' => $page['is_cat'],
            'CMBB_TITLE' => $title,
            'CMBB_CONTENT' => $page['content'],
            'CMBB_LEFTBAR' => $this->cmbb->build_sidebar($page, $this->auth, $this->helper, 'view'),
            'CMBB_ARTICLE_TOPIC_ID' => $page['topic_id'],
            'CMBB_AUTHOR' => ($page['user_id'] > 0) ? $this->cmbb->phpbb_get_user($page['user_id']) : '',
        ));


        return $this->helper->render('article.html', $title);
    }

    /**
     * This probably won't survive but we need to shove it somewhere we don't forget
     *
     */
    private function build_sidebar($page)
    {
        // Either greet or show login box
        if ($this->user->data['is_registered']) {
            $cmbb_sidebar = '<div id="login">';
            $cmbb_sidebar.= "<h3>Welkom ".$this->user->data['username']."</h3>";
            $cmbb_sidebar.= ' (<a href="'.append_sid("{$this->phpbb_root_path}ucp.php", 'mode=logout', true, $this->user->session_id).'">'.$this->user->lang('LOGOUT').'</a>)';


            // Show link to editor
            if (( $this->auth->acl_get('m_') || ($this->user->data['user_posts'] >= MIN_POST_COUNT) || $this->cmbb->has_written($this->user->data['user_id']) ) && ($this->user->data['is_bot'] == FALSE) && (!$this->cmbb->phpbb_is_banned($this->user->data['user_id']))) {
                $cmbb_sidebar.= '<p class="fakebutton"><a href="' . $this->helper->route('ger_cmbb_page_edit', array('article_id' => '_new_')) . '">+ Nieuw artikel</a></p>';
            }
            if (( ($this->user->data['user_id'] == $page['user_id']) || $this->auth->acl_get('m_') ) && ($page['is_cat'] == 0)) {
                $cmbb_sidebar.= '<p class="fakebutton"><a href="' . $this->helper->route('ger_cmbb_page_edit', array('article_id' => $page['article_id'])) . '">Bewerk artikel</a></p>';
            }
            if ($this->auth->acl_get('m_')) {
                $cmbb_sidebar.= '<br /><hr /><br />';
                if ($page['is_cat'] == 0) {
                    if ($page['visible'] == 1) {
                        $cmbb_sidebar.= '<p class="fakebutton"><a onclick="return confirm(\'Weet je zeker dat je deze pagina wil verbergen?\')" href="index.php?m=h&a=h&p='.$page['article_id'].'">Verberg artikel</a></p>';
                    }
                    else {
                        $cmbb_sidebar.= '<p class="fakebutton"><a onclick="return confirm(\'Weet je zeker dat je deze pagina zichtbaar wil maken?\')" href="index.php?m=h&a=u&p='.$page['article_id'].'">Terugzetten</a></p>';
                    }
                }
                if ($this->cmbb->get_hidden()) {
                    $cmbb_sidebar.= '<p class="fakebutton"><a href="index?showhidden=1">Toon verborgen artikelen</a></p>';
                }
                else {
                    $cmbb_sidebar.= '<p class="fakebutton inactive"><a>Geen verborgen artikelen</a></p>';
                }
            }


            $cmbb_sidebar.= '</div>';
        }
        else {
            $cmbb_sidebar = '<div id="login"><h3>'.$this->user->lang('LOGIN').':</h3><br />
                                    <form method="post" action="'.$this->phpbb_root_path.'ucp.php?mode=login">
                                            <p><span>'.$this->user->lang('USERNAME').':</span> <input type="text" name="username" size="14" /><br />
                                            <span>'.$this->user->lang('PASSWORD').':</span> <input type="password" name="password" size="14" /><br />
                                            <span>'.$this->user->lang('LOG_ME_IN').':</span> <input type="checkbox" name="autologin" /><br />
                                            <input type="submit" class="btnmain" value="'.$this->user->lang('LOGIN').'" name="login" /></p>
                                            <p><input type="hidden" name="redirect" value="'.CMBB_ROOT.'/{CMBB_ALIAS}" /></p>
                                    </form>
                            </div>';
        }

        /*
         * Fetch newest topics
         */
        $cmbb_sidebar.=
            '<div id="topiclist"><hr />
                            <h3>'.$this->user->lang('FEED_TOPICS_NEW').'</h3><br />
                            <ul class="lt">';

        $latest = $this->cmbb->phpbb_latest_topics(array_unique(array_keys($this->auth->acl_getf('f_read', true))), 5);
        foreach ($latest as $row)
        {

            $url = $this->phpbb_root_path.'/viewtopic.php?f='.$row['forum_id'].'&amp;t='.$row['topic_id'].'&amp;view=unread#unread';
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
                    <h3> '.$this->user->lang('STATISTICS').'</h3>
                    <p><a href="'.$this->phpbb_root_path.'viewonline.php">'.$this->user->lang('WHO_IS_ONLINE').'</a>:<br>'.$whosonline['online_userlist'].'</p>';

        $cmbb_sidebar.= '<p>'.$this->user->lang('TOTAL_USERS', (int) $this->config['num_users']).'<br />'.
            $this->user->lang('NEWEST_USER', get_username_string('full', $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour'])).
            '</p></div>';

        return $cmbb_sidebar;
    }
}