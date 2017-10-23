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

class article
{
	/* @var \phpbb\config\config */

	protected $config;

	/* @var $config_text \phpbb\config\db_text */
	protected $config_text;

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

	/* @var \ger\cmbb\cmbb\presentation */
	protected $presentation;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\controller\helper	$helper
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\user				$user
	 * @param \phpbb\auth				$auth
	 * @param \phpbb\request			$request
	 * @param string					$phpbb_root_path
	 * @param \ger\cmbb\cmbb			$cmbb
	 * @param \ger\cmbb\presentation	$presentation
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, $phpbb_root_path, \ger\cmbb\cmbb\driver $cmbb, \ger\cmbb\cmbb\presentation $presentation)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->cmbb = $cmbb;
		$this->presentation = $presentation;
	}

	/**
	 * Controller for route /article/{alias}
	 *
	 * @param string		$alias
	 * @param int			$param
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($alias = 'index', $param = 0)
	{
		$this->user->add_lang('viewtopic');
		$this->user->add_lang_ext('ger/cmbb', 'common');
		if (($alias == 'search') && $param > 0)
		{
			$this->user->add_lang('search');
			$children = $this->cmbb->get_user_articles($param);
			if (empty($children))
			{
				return $this->helper->error($this->user->lang('NO_SEARCH_RESULTS', $alias));
			}
			$article = array(
				'article_id'	=> 0,
				'title'			=> $this->user->lang('SEARCH'),
				'alias'			=> $param,
				'user_id'		=> $param,
				'parent'		=> 1,
				'is_cat'		=> 1,
				'topic_id'		=> 0,
				'category_id'	=> -1,
				'visible'		=> 1,
			);
		}
		else
		{
			$article = $this->cmbb->get_article($alias);		
		}
		if ($article === false)
		{
			return $this->helper->error($this->user->lang('FILE_NOT_FOUND_404', $alias));
		}
		if ($article['visible'] == 0)
		{
			if ($this->auth->acl_get('m_'))
			{
				$article['content'] = '<div class="warning">' . $this->user->lang('ARTICLE_HIDDEN_WARNING') . '</div>' . $article['content'];
			}
			else
			{
				return $this->helper->error($this->user->lang('FILE_NOT_FOUND_404', $alias));
			}
		}

		// List child articles exerpts as content when it's a category
		if ($article['is_cat'])
		{
			$article['content'] = '';
			if ($article['alias'] == 'index')
			{
				if ($this->request->variable('showhidden', '') == 1)
				{
					if (!$this->auth->acl_get('m_'))
					{
						return $this->helper->error($this->user->lang('NOT_AUTHORISED', $alias));

					}
					$children = $this->cmbb->get_hidden();
				}
				else
				{
					$children = $this->cmbb->get_last($this->config['ger_cmbb_number_index_items']);
				}
			}
			else if (empty($children))
			{
				$children = $this->cmbb->get_children($article['article_id']);
			}

			$count = count($children);
			if (empty($children))
			{
				$article['content'] = ' ';
			}
			else
			{
				$counter = 0;
				foreach ($children as $child)
				{
					$counter++;
					$this->template->assign_block_vars('category_children', array(
						'ALIAS'			 => $child['alias'],
						'URL'			 => $this->helper->route('ger_cmbb_article', array('alias' => $child['alias'])),
						'TITLE'			 => $child['title'],
						'FEATURED_IMG'	 => empty($child['featured_img']) ? $this->cmbb->phpbb_user_avatar($child['user_id']) : $this->cmbb->get_featured_img($child['article_id'], $child['title']),
						'EXERPT'		 => $this->presentation->closetags($this->presentation->character_limiter($this->presentation->clean_html($child['content']))) . ' <a href="' . $this->helper->route('ger_cmbb_article', array('alias' => $child['alias'])) . '" class="read_more">' . $this->user->lang('READ_MORE') . '</a>',
						'S_LAST_CHILD'	 => ($counter < $count) ? false : true,
					));
				}
			}
		}
		else
		{
			if ($article['topic_id'] > 0)
			{
				$topic_replies = $this->cmbb->count_reactions($article['topic_id'], $this->auth);
			}
		}

		// Do breadcrumbs
		if ($article['alias'] == 'index')
		{
			// No link on homepage, but remove board index from crumbs
			$this->template->assign_var('CMBB_HOME', true);
		}
		else
		{
			if ($article['parent'] > 0)
			{
				$trail[] = $article;
				if ($article['parent'] > 1)
				{
					$trail[] = $this->cmbb->get_article($article['parent']);
					if ($trail[1]['parent'] > 1)
					{
						$trail[] = $this->cmbb->get_article($trail[1]['parent']);
					}
				}
			}

			if (isset($trail))
			{
				$trail = array_reverse($trail);
				foreach ($trail as $crumb)
				{
					$this->template->assign_block_vars('cmbb_crumbs', array(
						'U_CRUMB_LINK'	 => $crumb['alias'],
						'CRUMB_NAME'	 => $crumb['title'],
					));
				}
			}
		}

		// Wrap it all up
		$title = empty($article['title']) ? (($this->config['site_home_text'] !== '') ? $this->config['site_home_text'] : $this->user->lang('HOME')) : $article['title'];
		$this->template->assign_vars(array(
			'CMBB_CATEGORY_NAME'	 => ($article['category_id'] > 0) ? $this->cmbb->fetch_category($article['category_id']) : $this->user->lang('SEARCH_AUTHOR') . ' ' . $this->cmbb->phpbb_get_user($param, false),
			'S_CMBB_CATEGORY'		 => $article['is_cat'],
			'CMBB_TITLE'			 => $title,
			'CMBB_CONTENT'			 => empty($article['content']) ? '' : $article['content'],
			'CMBB_ARTICLE_TOPIC_ID'	 => ($article['topic_id'] > 0) ? $article['topic_id'] : false,
			'CMBB_ARTICLE_REACTIONS' => isset($topic_replies) ? $topic_replies : false,
			'CMBB_AUTHOR'			 => ($article['user_id'] > 0) ? $this->cmbb->phpbb_get_user($article['user_id']) : '',
			'S_SHOW_RIGHTBAR'		 => $this->config['ger_cmbb_show_rightbar'],
			'CMBB_RIGHTBAR_CONTENT'	 => $this->config_text->get('ger_cmbb_rightbar_html'),
		));

		$this->cmbb->fetch_leftbar($article, $this->auth, 'view');
		$template_file = $article['is_cat'] ? 'category.html' : 'article.html';
		return $this->helper->render($template_file, $title);
	}

}
