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

	/* @var \ger\cmbb\cmbb\presentation */
	protected $presentation;

	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\controller\helper	$helper
	 * @param \phpbb\log\log			$log
	 * @param \phpbb\user				$user
	 * @param \phpbb\auth				$auth
	 * @param \phpbb\request			$request
	 * @param string					$phpbb_root_path
	 * @param \ger\cmbb\cmbb			$cmbb
	 * @param \ger\cmbb\presentation	$presentation
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\log\log $log, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, $phpbb_root_path, \ger\cmbb\cmbb\driver $cmbb, \ger\cmbb\cmbb\presentation $presentation, $php_ext)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->log = $log;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->cmbb = $cmbb;
		$this->presentation = $presentation;
		$this->php_ext = $php_ext;
	}

	/**
	 * Controller for route /save/{article_id}
	 *
	 * @param int		$article_id
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($article_id = 0)
	{
		$this->user->add_lang_ext('ger/cmbb', 'common');
		$content = $this->sanitize_content($this->request->variable('content', '', true));

		if (is_numeric($article_id))
		{
			// Check old article info
			$oldarticle = $this->cmbb->get_article($article_id);

			// Check if user is allowed to edit
			if (!(($this->user->data['user_id'] == $oldarticle['user_id']) || $this->auth->acl_get('m_') ))
			{
				return $this->helper->error('NOT_AUTHORISED');
			}
			if (empty($oldarticle['user_id']) && (!$this->auth->acl_get('a_')))
			{
				// Special article, admin only
				return $this->helper->error('NOT_AUTHORISED');
			}
			if (!$title = $this->presentation->phpbb_censor_title($this->request->variable('title', '', true)))
			{
				return $this->helper->error('NOT_AUTHORISED');
			}

			// Compare old article content size with new post content size
			$oldsize = strlen($oldarticle['content']);
			$newsize = strlen(censor_text($content));

			if ( ($oldsize > 0) && ($newsize / $oldsize) < 0.7)
			{
				return $this->helper->error('ERROR_MUCH_REMOVED');
			}

			$article_data = array(
				'article_id'	 => $article_id,
				'title'			 => $title,
				'parent'		 => $this->cmbb->get_std_parent($this->request->variable('category_id', 1)),
				'category_id'	 => $this->request->variable('category_id', 1),
				'content'		 => censor_text($content),
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
				if (isset($article_data['visible']))
				{
					$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_ARTICLE_VISIBILLITY', time(), array('article_id' => $article_id, 'visible' => $article_data['visible']));
				}
			}

			$redirect = $oldarticle['alias'];
		}
		else if ($article_id == '_new_')
		{
			if (!$title = $this->presentation->phpbb_censor_title($this->request->variable('title', '', true)))
			{
				return $this->helper->error('ERROR_TITLE');
			}

			$article_data = array(
				'title'			 => $title,
				'alias'			 => $this->cmbb->generate_article_alias($title),
				'user_id'		 => $this->user->data['user_id'],
				'parent'		 => $this->cmbb->get_std_parent($this->request->variable('category_id', 0)),
				'is_cat'		 => 0,
				'category_id'	 => $this->request->variable('category_id', 0),
				'content'		 => $content,
				'visible'		 => 1,
				'datetime'		 => time(),
			);
			if (!$this->request->is_set('disable_comments'))
			{
				$article_data['topic_id'] = $this->create_article_topic($article_data, $this->cmbb->fetch_category($this->request->variable('category_id', '1'), true)['react_forum_id']);
			}
			$redirect = $article_data['alias'];
		}
		else
		{
			return $this->helper->error('ERROR');
		}
		$article_id = $this->cmbb->store_article($article_data);
		$featured_img = $this->request->variable('featured_img', '');

		if (!empty($featured_img))
		{
			$this->store_featured_img($featured_img, $article_id);
		}
		redirect($this->helper->route('ger_cmbb_article', array(
					'alias' => $redirect)));
	}
	
	/**
	 * Store a featured image
	 * @param type $orig_path
	 * @param type $article_id
	 * @return boolean
	 */
	private function store_featured_img($orig_filename, $article_id)
	{
		if (empty($orig_filename)) 
		{
			$article_data = array(
				'article_id' => $article_id,
				'featured_img' => ''
			);
			return $this->cmbb->store_article($article_data);
		}
		
		$orig_path = $this->phpbb_root_path . 'images/cmbb_upload/' . $this->user->data['user_id'] . '/' . $orig_filename;
		if (!file_exists($orig_path))
		{
			return false;
		}
		$dest_dir = 'images/cmbb_upload/featured'; 
		if (!is_dir($dest_dir)) 
		{
			mkdir($dest_dir, 0755, true);
		}
		
		// What 'up?
		$orig = getimagesize($orig_path);
		switch(strtolower($orig['mime']))
		{
			case 'image/png':
				$image = imagecreatefrompng($orig_path);
				break;
			case 'image/jpeg':
				$image = imagecreatefromjpeg($orig_path);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($orig_path);
				break;
			default: 
				return FALSE;
		}
		
		// Resize
		$max = 90;
		list($width, $height) = getimagesize($orig_path);
		if ($width > $height) 
		{
			$new_width = $max;
			$ratio = $new_width / $width;
			$new_height = abs($ratio * $height);
		}
		else
		{
			$new_height = $max;
			$ratio = $new_height / $height;
			$new_width = abs($ratio * $width);
		}
		$image_p = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		imagejpeg($image_p, $dest_dir . '/' . $article_id . '.jpg'); 
			
		// Write down
		$article_data = array(
			'article_id' => $article_id,
			'featured_img' => $orig_filename
		);
		return $this->cmbb->store_article($article_data);
	}

	/**
	 * Create a topic with intro for article
	 * @param array $article_data
	 * @return string
	 */
	private function create_article_topic($article_data, $forum_id)
	{
		if (empty($forum_id))
		{
			return false;
		}
		if (!function_exists('get_username_string'))
		{
			include($this->phpbb_root_path . 'includes/functions_content.' . $this->php_ext);
		}
		if (!function_exists('submit_post'))
		{
			include($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		}
		$article_data['user_id'] = (int) $article_data['user_id'];
		if (empty($article_data['user_id']))
		{
			return false;
		}
		if ($user = $this->cmbb->phpbb_get_user($article_data['user_id']) == false)
		{
			return false;
		}

		$topic_content = '[b][size=150]' . $article_data['title'] . '[/size][/b]
[i]' . $this->user->lang['POST_BY_AUTHOR'] . ' ' . $this->user->data['username'] . '[/i]

' . $this->presentation->character_limiter(strip_tags(html_entity_decode($article_data['content'], ENT_COMPAT))) . '
[url=' . $this->helper->route('ger_cmbb_article', array(
					'alias' => $article_data['alias'])) . ']' . $this->user->lang['READ_MORE'] . '[/url]';

		$poll = $uid = $bitfield = $options = '';

		// will be modified by generate_text_for_storage
		$allow_bbcode = $allow_urls = $allow_smilies = true;

		generate_text_for_storage($topic_content, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

		$data = array(
			// General Posting Settings
			'forum_id'			 => $forum_id, // The forum ID in which the post will be placed. (int)
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
		);

		$url = submit_post('post', $article_data['title'], 'cmBB', POST_NORMAL, $poll, $data);
		if (strpos($url, 'sid=') !== false)
		{
			$url = substr($url, 0, strpos($url, 'sid='));
		}
		$topic_id = str_replace('&amp;t=', '', strstr($url, '&amp;t='));
		return (int) $topic_id;
	}
	
	/**
	 * Strip empty lines from beginning and end
	 * @param string $content
	 * @return string 
	 */
	private function sanitize_content($content)
	{
		$content = trim(str_replace("&nbsp;", " ", html_entity_decode($content, ENT_COMPAT, 'UTF-8')));

		if (substr($content, -13, 13) == '<p>&nbsp;</p>')
		{
		  $content = substr($content, 0, -13);
		}
		if (substr($content, 0, 13) == '<p>&nbsp;</p>')
		{
		  $content = substr($content, 13);
		}
		$content = trim($content);

		// Might occur more than once
		if ( (substr($content, -13, 13) == '<p>&nbsp;</p>') || (substr($content, 0, 13) == '<p>&nbsp;</p>') )
		{
			return $this->sanitize_content($content);
		}
		return $content;
	}
	

}
