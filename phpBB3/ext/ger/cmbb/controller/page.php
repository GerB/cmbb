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
                $page['content'] = '<div class="warning">' . $this->user->lang('ARTICLE_HIDDEN_WARNING') . '</div>'.$page['content'];
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
                        return $this->helper->message('NOT_AUTHORISED', array(
                                $alias), 'NOT_AUTHORISED', 404);
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
                    $page['content'] .= ' <a href="'.$child['alias'].'">' . $this->user->lang('READ_ON') . '</a></div></div>';

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
            if ($page['parent'] > 1)
            {
                $parents[] = $this->cmbb->get_article(intval($page['parent']));
                    if ($parents[0]['parent'] > 1) {
                        $parents[] = $this->cmbb->get_article($parents[0]['parent']);
                        if ($parents[1]['parent'] > 1) {
                            $parents[] = $this->cmbb->get_article($parents[1]['parent']);
                        }
                    }
            }
            if (isset($parents)) {
                $parents = array_reverse($parents);
                foreach ($parents as $parent)
                {
                    $this->template->assign_block_vars('cmbb_crumbs', array(
                        'U_CRUMB_LINK' =>  $parent['alias'],
                        'CRUMB_NAME' =>  $parent['title'],
                    ));
                }
            }
            // Also add breadcrumb for current page
            $this->template->assign_block_vars('cmbb_crumbs', array(
                'U_CRUMB_LINK' =>  $page['alias'],
                'CRUMB_NAME' =>  $page['title'],
            ));

        }

        // Wrap it all up
        $title = empty($page['title']) ? (($this->config['site_home_text'] !== '') ? $this->config['site_home_text'] : $this->user->lang('HOME')) : $page['title'];

        $this->template->assign_vars(array(
//            'CMBB_BREADCRUMBS' => $path,
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

} // EoF