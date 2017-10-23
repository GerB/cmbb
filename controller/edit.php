<?php

/**
 *
 * cmBB
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\controller;

class edit
{
	/* @var \phpbb\config\config */

	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\path_helper */
	protected $path_helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\request\request_interface */
	protected $request;

	/* @var \ger\cmbb\cmbb_root_path */
	protected $cmbb_root_path;

	/* @var \ger\cmbb\cmbb\driver */
	protected $cmbb;

	/* @var \ger\cmbb\cmbb\presentation */
	protected $presentation;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config
	 * @param \phpbb\controller\helper $helper
	 * @param \phpbb\template\template $template
	 * @param \phpbb\user $user
	 * @param \phpbb\user $auth
	 * @param \phpbb\user $request
	 * @param \phpbb\user $cmbb_root_path
	 * @param \ger\cmbb\cmbb $cmbb
	 * @param \ger\cmbb\presentation $presentation
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\path_helper $path_helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, $cmbb_root_path, \ger\cmbb\cmbb\driver $cmbb, \ger\cmbb\cmbb\presentation $presentation)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->path_helper = $path_helper;
		$this->template = $template;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->cmbb_root_path = $cmbb_root_path;
		$this->cmbb = $cmbb;
		$this->presentation = $presentation;
	}

	/**
	 * Controller for route /edit/{article_id}
	 *
	 * @param mixed $article_id
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($article_id = 0)
	{
		$this->user->add_lang_ext('ger/cmbb', 'common');
		if (is_numeric($article_id))
		{
			$article = $this->cmbb->get_article($article_id);
			if ($article === false)
			{
				return $this->helper->error($this->user->lang('FILE_NOT_FOUND_404', $article_id));
			}
			// Check if user is allowed to edit
			if (!(($this->user->data['user_id'] == $article['user_id']) || $this->auth->acl_get('m_') ))
			{
				return $this->helper->error($this->user->lang('NOT_AUTHORISED', $article_id));
			}
		}
		else if ($article_id != '_new_')
		{
			return $this->helper->error($this->user->lang('FILE_NOT_FOUND_404', $article_id));
		}
		else
		{
			// New article, build basic array to statisfy template and leftbar
			$article = [
				'title' => $this->user->lang('NEW_ARTICLE'),
				'content' => '',
				'article_id' => '_new_',
				'alias' => 'index',
				'category_id' => 0,
				'featured_img' => '',
			];
		}

		// Wrap it all up
		$this->template->assign_vars(array(
			'CMBB_TITLE'			 => $article['title'],
			'CMBB_CONTENT'			 => $article['content'],
			'U_FORM_ACTION'			 => $this->helper->route('ger_cmbb_save', array('article_id' => $article['article_id'])),
			'U_UPLOAD_ACTION'		 => $this->helper->route('ger_cmbb_upload'),
			'CMBB_CATEGORY_DROPDOWN' => $this->presentation->form_dropdown('category_id', $this->cmbb->get_categories($this->auth->acl_get('m_')), $article['category_id']),
			'IMAGE_DROPDOWN'		 => $this->presentation->form_dropdown('featured_img', $this->get_imagelist(), $article['featured_img']),
			'CAN_HIDE'				 => (!empty($article['content']) && $this->auth->acl_get('m_')) ? true : false,
			'IS_VISIBLE'			 => empty($article['visible']) ? false : true,
			'CMBB_ROOT_PATH'		 => generate_board_url() . substr($this->cmbb_root_path, 1),
			'CMBB_IMG_DIR'			 => $this->helper->route('ger_cmbb_folders', array('user_id' => $this->user->data['user_id'])),
			'ALLOWED_EXT'			 => implode(', ', $this->cmbb->allowed_extensions),
			'S_IS_NEW'				 => ($article_id == '_new_') ? true : false,
			'S_SHOW_RIGHTBAR'		 => $this->config['ger_cmbb_show_rightbar'],
			'CMBB_RIGHTBAR_CONTENT'	 => $this->config['ger_cmbb_rightbar_html'],
		));
		$this->cmbb->fetch_leftbar($article, $this->auth, 'edit');
		return $this->helper->render('article_form.html', (empty($article['title']) ? $this->user->lang('NEW_ARTICLE') : $article['title']));
	}
	
	/**
	 * Fetch list of user images
	 * @return array
	 */
	private function get_imagelist()
	{
		$return[''] = $this->user->lang('USE_AVATAR');
		$dir = $this->path_helper->get_phpbb_root_path() . 'images/cmbb_upload/' . $this->user->data['user_id'];
		if (is_dir($dir)) 
		{
			$dh = scandir($dir);
			foreach ($dh as $item)
			{
				if ($item != '.' && $item != '..' && $item != 'index.html' && strtolower($item) != 'thumbs.db')
				{
					$dir = str_replace('//', '/', $dir);
					$return[$item] = $this->presentation->character_limiter($item);			
				}
			}
		}
		return $return;
	}

}
