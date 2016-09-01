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

class save
{
	/* @var \phpbb\config\config */

	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var phpbb\log\log */
	protected $log;

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
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\log\log $log, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, $phpbb_root_path, \ger\cmbb\cmbb\driver $cmbb)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->log = $log;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->cmbb = $cmbb;

		include($this->phpbb_root_path . '/ext/ger/cmbb/cmbb/presentation.php');
	}

	/**
	 * Controller for route /save/{article_id}
	 *
	 * @param int		$article_id
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($article_id = 0)
	{
		if (is_numeric($article_id))
		{
			// Check old page info
			$oldpage = $this->cmbb->get_article($article_id);

			// Check if user is allowed to edit
			if (!(($this->user->data['user_id'] == $oldpage['user_id']) || $this->auth->acl_get('m_') ))
			{
				return $this->helper->message('NOT_AUTHORISED', 'NOT_AUTHORISED', 403);
			}
			if (empty($oldpage['user_id']) && (!$this->auth->acl_get('a_')))
			{
				// Special page, admin only
				return $this->helper->message('NOT_AUTHORISED', 'NOT_AUTHORISED', 403);
			}
			if (!$title = phpbb_censor_title($this->request->variable('title', '', true)))
			{
				return $this->helper->message('NOT_AUTHORISED', 'NOT_AUTHORISED', 403);
			}

			// Compare old article content size with new post content size
			$oldsize = strlen($oldpage['content']);
			$newsize = strlen(censor_text($this->request->variable('content', '', true)));

			if (($newsize / $oldsize) < 0.7)
			{
				return $this->helper->message('ERROR_MUCH_REMOVED', 'ERROR', 200);
			}

			$article_data = array(
				'article_id'	 => $article_id,
				'title'			 => $title,
				'parent'		 => $this->cmbb->get_std_parent($this->request->variable('category_id', '1')),
				'category_id'	 => $this->request->variable('category_id', '1'),
				'content'		 => censor_text(htmlspecialchars_decode($this->request->variable('content', ''), ENT_COMPAT)),
			);

			// Delete or restore, but only if we're moderator
			if ($this->auth->acl_get('m_'))
			{
				if ($this->request->is_set('delete'))
				{
					$article_data['visible'] = 0;
				}
				else if ($this->request->is_set('restore'))
				{
					$article_data['visible'] = 1;
				}
				$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_ARTICLE_VISIBILLITY', time(), array('article_id' => $article_id, 'visible' => $article_data['visible']));
			}

			$redirect = $oldpage['alias'];
		}
		else if ($article_id == '_new_')
		{
			if (!$title = phpbb_censor_title($this->request->variable('title', '', true)))
			{
				return $this->helper->message('INVALID_TITLE', 'ERROR', 200);
			}

			$article_data = array(
				'title'			 => $title,
				'alias'			 => $this->cmbb->generate_page_alias($this->request->variable('title', '', true)),
				'user_id'		 => $this->user->data['user_id'],
				'parent'		 => $this->cmbb->get_std_parent($this->request->variable('category_id', '')),
				'is_cat'		 => 0,
				'template_id'	 => 1,
				'category_id'	 => $this->request->variable('category_id', ''),
				'content'		 => htmlspecialchars_decode($this->request->variable('content', '', true), ENT_COMPAT),
				'visible'		 => 1,
				'datetime'		 => time(),
			);
			$article_data['topic_id'] = $this->create_article_topic($article_data);

			$redirect = $article_data['alias'];
		}
		else
		{
			return $this->helper->message('ERROR', 'ERROR', 404);
		}
		$this->cmbb->store_article($article_data);
		redirect($this->helper->route('ger_cmbb_page', array(
					'alias' => $redirect)));
	}

	/**
	 * Create a topic with intro for article
	 * @param array $article_data
	 * @return string
	 */
	private function create_article_topic($article_data)
	{
		if (!function_exists('get_username_string'))
		{
			include($this->phpbb_root_path . 'includes/functions_content.php');
		}
		if (!function_exists('submit_post'))
		{
			include($this->phpbb_root_path . 'includes/functions_posting.php');
		}
		$article_data['user_id'] = filter_var($article_data['user_id'], FILTER_SANITIZE_NUMBER_INT);
		if (empty($article_data['user_id']))
		{
			return false;
		}
		if ($user = $this->cmbb->phpbb_get_user($article_data['user_id']) == false)
		{
			return false;
		}

		$topic_content = '[b][size=150]' . $article_data['title'] . '[/size][/b]
[i]' . $this->user->lang['POST_BY_AUTHOR'] . ' ' . $user['username'] . '[/i]

' . character_limiter(strip_tags($article_data['content'])) . '
[url=' . $this->helper->route('ger_cmbb_page', array(
					'alias' => $article_data['alias'])) . ']' . $this->user->lang['READ_MORE'] . '[/url]';

		$poll = $uid = $bitfield = $options = '';

		// will be modified by generate_text_for_storage
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		generate_text_for_storage($topic_content, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

		$data = array(
			// General Posting Settings
			'forum_id'			 => $this->config['ger_cmbb_react_forum_id'], // The forum ID in which the post will be placed. (int)
			'topic_id'			 => 0, // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
			'icon_id'			 => false, // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
			// Defining Post Options
			'enable_bbcode'		 => true, // Enable BBcode in this post. (bool)
			'enable_smilies'	 => true, // Enabe smilies in this post. (bool)
			'enable_urls'		 => true, // Enable self-parsing URL links in this post. (bool)
			'enable_sig'		 => true, // Enable the signature of the poster to be displayed in the post. (bool)
			// Message Body
			'message'			 => $topic_content, // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
			'message_md5'		 => md5($topic_content), // The md5 hash of your message
			// Values from generate_text_for_storage()
			'bbcode_bitfield'	 => $bitfield, // Value created from the generate_text_for_storage() function.
			'bbcode_uid'		 => $uid, // Value created from the generate_text_for_storage() function.    // Other Options
			'post_edit_locked'	 => 0, // Disallow post editing? 1 = Yes, 0 = No
			'topic_title'		 => $article_data['title'],
			'notify_set'		 => false, // (bool)
			'notify'			 => false, // (bool)
			'post_time'			 => 0, // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
			'forum_name'		 => '', // For identifying the name of the forum in a notification email. (string)    // Indexing
			'enable_indexing'	 => true, // Allow indexing the post? (bool)    // 3.0.6
			'force_visibility'	 => true, // 3.1.x: Allow the post to be submitted without going into unapproved queue, or make it be deleted (replaces force_approved_state)
		);

		$url = submit_post('post', $article_data['title'], 'cmBB', POST_NORMAL, $poll, $data);
		$topic_id = str_replace('&amp;t=', '', strstr($url, '&amp;t='));
		return $topic_id;
	}

}

// EoF
