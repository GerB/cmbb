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

class edit
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


        protected $cmbb_root_path;

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
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, $cmbb_root_path, \ger\cmbb\cmbb\driver $cmbb)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->cmbb_root_path = $cmbb_root_path;
		$this->cmbb = $cmbb;

                include($this->cmbb_root_path . 'cmbb/presentation.php');
	}

	/**
	* Controller for route /edit/{article_id}
	*
	* @param string		$article_id
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($article_id = 0)
	{

            if (is_numeric($article_id)) {
                $page = $this->cmbb->get_article($article_id);
                if ($page === FALSE) {
                    return $this->helper->message('FILE_NOT_FOUND_404', array($article_id), 'FILE_NOT_FOUND_404', 404);
                }
                // Check if user is allowed to edit
                if (!(($this->user->data['user_id'] == $page['user_id']) || $this->auth->acl_get('m_') )) {
                    return $this->helper->message('NOT_AUTHORISED', 'NOT_AUTHORISED', 403);
                }
            }
            else if (!$article_id == '_new_') {
                return $this->helper->message('FILE_NOT_FOUND_404', array($alias), 'FILE_NOT_FOUND_404', 404);
            }
            
            // Wrap it all up
            $this->template->assign_vars(array(
                'CMBB_TITLE'                => (empty($page['title']) ? $this->user->lang('NEW_MESSAGE') : $page['title']),
                'CMBB_CONTENT'              => (empty($page['content']) ? '' : $page['content'] ),
                'CMBB_LEFTBAR'              => $this->cmbb->build_sidebar(NULL, $this->auth, $this->helper, 'edit'),
                'U_FORM_ACTION'             => $this->helper->route('ger_cmbb_save', array('article_id' => (empty($page['article_id']) ? '_new_' : $page['article_id'] ))),
                'CMBB_CATEGORY_DROPDOWN'    => form_dropdown('category_id', $this->cmbb->get_categories(), (empty($page['category_id']) ? 0 : $page['category_id'])),
                'CAN_HIDE'                  => (!empty($page['title']) && $this->auth->acl_get('m_')) ? TRUE : FALSE,
                'IS_VISIBLE'                => empty($page['visible']) ? FALSE : TRUE,
                'CMBB_ROOT_PATH'            => generate_board_url() . substr($this->cmbb_root_path, 1),
                'CMBB_IMG_DIR'              => $this->helper->route('ger_cmbb_folders', array('user_id' => $this->user->data['user_id'])),
                
            ));
            return $this->helper->render('article_form.html', (empty($page['title']) ? $this->user->lang('NEW_MESSAGE') : $page['title']));
	}


} // EoF
