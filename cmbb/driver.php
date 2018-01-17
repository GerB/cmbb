<?php

/**
 *
 * cmBB
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\cmbb;

class driver
{

	protected $config;
	protected $template;
	protected $helper;
	protected $user;
	protected $db;
	protected $article_table;
	protected $category_table;
	protected $phpbb_root_path;
	public $php_ext;

	/* array of allowed extensions */
	public $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config						$config					Config object
	 * @param \phpbb\request\request_interface			$request				Request object
	 * @param \phpbb\template\template					$template				Template object
	 * @param \phpbb\controller\helper					$helper					Helper object
	 * @param \phpbb\user								$user					User object
	 * @param \phpbb\user								$db						DB object
	 * @param string									$phpbb_root_path
	 * @param string									$article_table
	 * @param string									$category_table
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\controller\helper $helper, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $article_table, $category_table, $php_ext)
	{
		$this->config = $config;
		$this->template = $template;
		$this->helper = $helper;
		$this->user = $user;
		$this->db = $db;
		$this->article_table = $article_table;
		$this->category_table = $category_table;
		$this->phpbb_root_path = generate_board_url() . substr($phpbb_root_path, 1);
		$this->php_ext = $php_ext;

		// phpBB likes some constants...
		define('CMBB_FALSE', 0);
		define('CMBB_TRUE', 1);
		define('CMBB_INDEX_ID', 1);

	}

	/**
	 * Build main menu, homepage and all top level categories
	 * @return string
	 */
	public function list_menu_items()
	{
		$sql_array = array(
			'SELECT'	 => 'category_name, alias, article_id',
			'FROM'		 => array(
				$this->category_table	 => 'c',
				$this->article_table	 => 'a',
			),
			'WHERE'		 => 'std_parent > ' . CMBB_INDEX_ID . '
                    AND std_parent = article_id
					AND show_menu_bar = ' . CMBB_TRUE . '',
			'GROUP_BY'	 => 'category_name, alias, article_id',
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$return[] = $row;
		}
		$this->db->sql_freeresult($result);
		return $return;
	}

	/**
	 * Get basic article data
	 * @param mixed $find
	 * @return array
	 */
	public function get_article($find)
	{
		if (is_numeric($find))
		{
			$query = 'SELECT * FROM ' . $this->article_table . ' WHERE article_id = ' . (int) $find;
		}
		else
		{
			$query = 'SELECT * FROM ' . $this->article_table . " WHERE alias = '" . $this->db->sql_escape($find) . "'";
		}

		if ($result = $this->db->sql_query($query))
		{
			$return = $this->db->sql_fetchrow($result);
			if (!empty($return))
			{
				$this->db->sql_freeresult($result);
				return $return;
			}
		}
		$this->db->sql_freeresult($result);
		return false;
	}

	/**
	 * Get all articles written by a user
	 * @param int $user_id
	 * @return array | int
	 */
	public function get_user_articles($user_id, $count = false)
	{
		if ($count === true)
		{
			$query = 'SELECT count(*) as counted FROM ' . $this->article_table . ' 
				  WHERE user_id  = ' . (int) $user_id . " 
				  AND visible = " . ITEM_APPROVED . "
				  AND is_cat = " . CMBB_FALSE;
			
		}
		else 
		{
			$query = 'SELECT * FROM ' . $this->article_table . ' 
				  WHERE user_id  = ' . (int) $user_id . " 
				  AND visible = " . ITEM_APPROVED . "
				  AND is_cat = " . CMBB_FALSE . "
				  ORDER BY datetime DESC, article_id DESC";
		}
		if ($result = $this->db->sql_query($query))
		{
			if ($count === true)
			{
				$return = (int) $this->db->sql_fetchrow($result)['counted'];
			}
			else 
			{
				while ($row = $this->db->sql_fetchrow($result))
				{
					$return[] = $row;
				}
			}
		}
		$this->db->sql_freeresult($result);
		return empty($return) ? false : $return;
	}
	
	/**
	 * Store new or edited article data
	 * @param array $article_data
	 * @return int
	 */
	public function store_article($article_data)
	{
		if (isset($article_data['article_id']))
		{
			$article_id = $article_data['article_id'];
			unset($article_data['article_id']);
			$action = 'UPDATE ' . $this->article_table . ' SET ' . $this->db->sql_build_array('UPDATE', $article_data) . ' WHERE article_id =  ' . (int) $article_id;
		}
		else
		{
			$action = 'INSERT INTO ' . $this->article_table . ' ' . $this->db->sql_build_array('INSERT', $article_data);
		}

		if (!$this->db->sql_query($action))
		{
			return false;
		}
		else
		{
			return isset($article_id) ? $article_id : $this->db->sql_nextid();
		}
	}

	/**
	 * Get children articles for parent article_id
	 * @paramt int $parent
	 * @return array
	 */
	public function get_children($parent)
	{
		$query = 'SELECT * FROM ' . $this->article_table . "
				WHERE parent = '" . $this->db->sql_escape($parent) . "'
				AND visible = " . ITEM_APPROVED . "
				ORDER BY datetime DESC, article_id DESC";

		if ($result = $this->db->sql_query($query))
		{
			while ($row = $this->db->sql_fetchrow($result))
			{
				$return[] = $row;
			}
		}
		$this->db->sql_freeresult($result);
		return empty($return) ? false : $return;
	}

	/**
	 * Get last n visible items
	 * @param int $limit
	 * @return array
	 */
	public function get_last($limit)
	{
		$query = 'SELECT * FROM ' . $this->article_table . '
				WHERE is_cat = ' . CMBB_FALSE . '
				AND visible = ' . ITEM_APPROVED . '
				AND article_id <> ' . CMBB_INDEX_ID . '
				ORDER BY datetime DESC, article_id DESC
				LIMIT ' . (int) $limit;

		if ($result = $this->db->sql_query($query))
		{
			while ($row = $this->db->sql_fetchrow($result))
			{
				$return[] = $row;
			}
			$this->db->sql_freeresult($result);
			if (!empty($return))
			{
				return $return;
			}
		}
		return false;
	}

	/**
	 * Get hidden items
	 * @return array
	 */
	public function get_hidden()
	{
		$query = 'SELECT * FROM ' . $this->article_table . '
				WHERE is_cat = ' . CMBB_FALSE . '
				AND visible = ' . ITEM_UNAPPROVED . '
				ORDER BY datetime DESC, article_id DESC';

		if ($result = $this->db->sql_query($query))
		{
			while ($row = $this->db->sql_fetchrow($result))
			{
				$return[] = $row;
			}
			$this->db->sql_freeresult($result);
			if (!empty($return))
			{
				return $return;
			}
		}
		return false;
	}

	/**
	 * Get array of categories
	 * @param bool $show_protected
	 * @param bool $full_specs
	 * @return array
	 */
	public function get_categories($show_protected = false, $full_specs = false)
	{

		$query = 'SELECT * FROM ' . $this->category_table . ' WHERE std_parent > ' . CMBB_INDEX_ID;

		if (empty($show_protected))
		{
			$query .= ' AND protected = "' . CMBB_FALSE . '" ';
		}
		$query .= ' ORDER BY category_name ASC';

		if ($result = $this->db->sql_query($query))
		{

			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($full_specs == true)
				{
					$return[] = $row;
				}
				else
				{
					$return[$row['category_id']] = $row['category_name'];
				}
			}
			$this->db->sql_freeresult($result);
			if (!empty($return))
			{
				return $return;
			}
		}
		return false;
	}

	/**
	 * Get standard parent article_id for category_id
	 * @param int $category_id
	 * @return int
	 */
	public function get_std_parent($category_id)
	{
		$query = 'SELECT std_parent FROM ' . $this->category_table . ' WHERE category_id = ' . (int) $category_id ;

		if ($result = $this->db->sql_query($query))
		{
			$return = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if (!empty($return))
			{
				return $return['std_parent'];
			}
		}
		return '1';
	}

	/**
	 * Fetch category name, default to Articles
	 * @param int $category_id
	 * @return string
	 */
	public function fetch_category($category_id, $full_specs = false)
	{
		$query = 'SELECT * FROM ' . $this->category_table . ' WHERE category_id = ' . (int) $category_id ;

		if ($result = $this->db->sql_query($query))
		{
			$return = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if (!empty($return))
			{
				if ($full_specs === true)
				{
					return $return;
				}
				return $return['category_name'];
			}
		}
		return 'Articles';
	}

	/**
	 * Store category data
	 * @param array $category_data
	 * @return int
	 */
	public function store_category($category_data)
	{
		if (isset($category_data['category_id']))
		{
			$category_id = $category_data['category_id'];
			unset($category_data['category_id']);
			$action = 'UPDATE ' . $this->category_table . ' SET ' . $this->db->sql_build_array('UPDATE', $category_data) . ' WHERE category_id = ' . (int) $category_id;
		}
		else
		{
			$action = 'INSERT INTO ' . $this->category_table . ' ' . $this->db->sql_build_array('INSERT', $category_data);
		}

		if (!$this->db->sql_query($action))
		{
			return false;
		}
		else
		{
			return isset($category_id) ? $category_id : $this->db->sql_nextid();
		}
	}

	/**
	 * Delete a category
	 * @param int $category_id
	 * @return int
	 */
	public function delete_category($category_id)
	{
		$sql = 'DELETE FROM ' . $this->category_table . "
				WHERE category_id = {$category_id}";
		$this->db->sql_query($sql);

		return $this->db->sql_affectedrows();
	}

	/**
	 * Generate an alias for a title
	 * @param string $title
	 * @return string
	 */
	public function generate_article_alias($title)
	{
		// Basic cleanup
		$try = str_replace(' ', '-', utf8_clean_string($title));

		$try = trim(preg_replace('~[^\\pL\d]+~u', '-', $try), '-');
		
		// No double-dash please
		$try = preg_replace('#-{2,}#', '-', $try);
		
		// Now see if this alias already exists
		if (!$this->get_article($try))
		{
			// Ok
			return $try;
		}
		else
		{
			// Try adding a standard suffix
			for ($i = 2; $i < 10; $i++)
			{
				$next_try = $try . '-' . $i;
				if (!$this->get_article($next_try))
				{
					// Ok
					return $next_try;
				}
			}
			// Still here? Not gentile, but this will always work
			return $try . '-' . date('YmdHis');
		}
	}

	/**
	 * Get formatted link to user
	 * @param int $userid
	 * @param bool $formatted
	 * @return string
	 */
	public function phpbb_get_user($userid, $formatted = true)
	{

		$sql = 'SELECT username, user_colour , user_id, group_id
                   FROM ' . USERS_TABLE . '
                   WHERE user_id =' . (int) $userid;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		if (empty($row))
		{
			return false;
		}
		if (!$formatted)
		{
			return $row['username'];
		}
		if ($row['group_id'] != "754")
		{
			return'<a href="' . $this->phpbb_root_path . 'memberlist.' . $this->php_ext . '?mode=viewprofile&amp;u=' . $row['user_id'] . '" style="color:#' . $row['user_colour'] . '; font-weight: bold;">' . $row['username'] . '</a>';
		}
		else 
		{
			return '<span style="color:#' . $row['user_colour'] . ';">' . $row['username'] . '</span>';
		}
	}

	/**
	 * Get formatted featured img
	 * @param int $article_id
	 * @param string $title
	 * @param string $style = ''
	 * @return string
	 */	
	public function get_featured_img($article_id, $title, $style = '')
	{
		$full_url = generate_board_url() . '/images/cmbb_upload/featured/' . $article_id . '.jpg';		
		return '<img src="' . $full_url . '" title="' . $title . '" style="' . $style . '" />';
	}

	/**
	 * Get formatted user avatar or default avatar
	 * @param int $userid
	 * @param string $style = ''
	 *
	 * @return string
	 */
	public function phpbb_user_avatar($userid, $style = '')
	{
		$sql = 'SELECT username, user_avatar, user_avatar_type , user_avatar_width, user_avatar_height
                   FROM ' . USERS_TABLE . '
                   WHERE user_id = ' . (int) $userid;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		if (!$row)
		{
			$row = array(
				'username'			 => '',
				'user_avatar'		 => '',
				'user_avatar_type'	 => '',
				'user_avatar_width'	 => 0,
				'user_avatar_height' => 0,
			);
		}
		if (substr($row['user_avatar'], 0, 4) == 'http')
		{
			$path = $row['user_avatar'];
		}
		else if (empty($row['user_avatar']))
		{
			$path = generate_board_url() . '/styles/' . rawurlencode($this->user->style['style_path']) . '/theme/images/no_avatar.gif';
			$row['user_avatar_width'] = $row['user_avatar_height'] = 90;
		}
		else
		{
			$path = generate_board_url() . '/download/file.' . $this->php_ext . '?avatar=' . $row['user_avatar'];
		}

		if ($row['user_avatar_width'] > $row['user_avatar_height'])
		{
			$width_height = ' width="90" ';
		}
		else
		{
			$width_height = ' height="90" ';
		}

		return '<img src="' . $path . '" ' . $width_height . ' alt= "' . $row['username'] . '" title="' . $row['username'] . '" style="' . $style . '" />';
	}

	/**
	 * Count number of reactions on article from phpBB
	 * @param int $article_topic_id
	 * @return int
	 */
	public function count_reactions($article_topic_id, $auth)
	{
		// Simplified query borrowed from ./viewtopic.php
		$sql_array = array(
			'SELECT' => 't.*, f.*',
			'FROM' => array(FORUMS_TABLE => 'f'),
		);
		$sql_array['FROM'][TOPICS_TABLE] = 't';
		$sql_array['WHERE'] = "t.topic_id = " . (int) $article_topic_id;
		$sql_array['WHERE'] .= ' AND f.forum_id = t.forum_id';

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$topic_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// If topic is hidden, don't go any further
		if ($topic_data['topic_visibility'] != ITEM_APPROVED && !$auth->acl_get('m_approve', $topic_data['forum_id']))
		{
			return 0;
		}
		// We're a mere user. Show only approved
		if (!$auth->acl_get('m_approve', $topic_data['forum_id']))
		{
			return (int) $topic_data['topic_posts_approved'] - 1;
		}
		// Sum of all statusses
		return (int) $topic_data['topic_posts_approved'] + (int) $topic_data['topic_posts_unapproved'] + (int) $topic_data['topic_posts_softdeleted'] - 1;
	}

	/**
	 * Fetch latest forum topics
	 * @param array $forums
	 * @param int $topic_limit
	 * @return array
	 */
	public function phpbb_latest_topics($forums, $topic_limit = 5)
	{
		// Select the last topics to which we have permissions
		$sql = 'SELECT t.topic_id, t.forum_id, t.topic_title
                            FROM ' . TOPICS_TABLE . ' t , ' . USERS_TABLE . ' u
                            WHERE topic_visibility = ' . ITEM_APPROVED . '
                            AND ' . $this->db->sql_in_set('forum_id', $forums) . '
                            AND u.user_id = t.topic_poster
                            ORDER BY topic_time DESC
                            LIMIT 0,' . (int) $topic_limit;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$return[] = array(
				'topic_id'		 => $row['topic_id'],
				'forum_id'		 => $row['forum_id'],
				'topic_title'	 => strlen($row['topic_title']) > 30 ? substr($row['topic_title'], 0, 30) . '&hellip;' : $row['topic_title'],
			);
		}
		$this->db->sql_freeresult($result);
		return empty($return) ? false : $return;
	}

	/**
	 * Check if user is in ban table
	 * @param int $user_id
	 * @return bool
	 */
	public function phpbb_is_banned($userid)
	{
		$sql = 'SELECT count(*) as banned FROM ' . BANLIST_TABLE . ' WHERE ban_userid = ' . (int) $userid;
		$result = $this->db->sql_query($sql);
		$banned = (int) $this->db->sql_fetchfield('banned');
		$this->db->sql_freeresult($result);

		if (!empty($banned))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Fetch left sidebar
	 * @param array $article
	 * @param obj $auth
	 * @param string $mode
	 * @return void
	 */
	public function fetch_leftbar($article, $auth, $mode)
	{
		$latest = $this->phpbb_latest_topics(array_unique(array_keys($auth->acl_getf('f_read', true))), 5);
		if ($latest)
		{
			foreach ($latest as $row)
			{
				$this->template->assign_block_vars('latest_topic_feed', array(
					'U_TOPIC'		 => $this->phpbb_root_path . 'viewtopic.' . $this->php_ext . '?f=' . $row['forum_id'] . '&amp;t=' . $row['topic_id'] . '&amp;view=unread#unread',
					'TOPIC_TITLE'	 => $row['topic_title'],
				));
			}
		}
		$this->template->assign_vars(array(
			'CMBB_LEFTBAR'		 => true,
			'S_WELCOME_USER'	 => $this->user->lang('WELCOME_USER', $this->user->data['username']),
			'U_LOGIN_REDIRECT'	 => $this->helper->route('ger_cmbb_article', array('alias' => $article['alias'])),
			'S_CAN_EDIT'		 => $this->can_edit($auth),
			'U_NEW_ARTICLE'		 => $this->helper->route('ger_cmbb_article_edit', array('article_id' => '_new_')),
			'U_EDIT_ARTICLE'	 => ($this->can_edit($auth, $article) && $mode == 'view') ? $this->helper->route('ger_cmbb_article_edit', array('article_id' => $article['article_id'])) : false,
			'S_CAN_SEE_HIDDEN'	 => $auth->acl_get('m_'),
			'S_HIDDEN'			 => $this->get_hidden(),
			'U_HIDDEN'			 => $this->helper->route('ger_cmbb_article', array('alias' => 'index')) . '?showhidden=1',   //$this->get_hidden(),index?showhidden=1
			'U_VIEWONLINE'		 => $this->phpbb_root_path . 'viewonline.' . $this->php_ext,
			'USERS_ONLINE'		 => obtain_users_online_string(obtain_users_online())['online_userlist'],
			'USERS_TOTAL'		 => $this->user->lang('TOTAL_USERS', (int) $this->config['num_users']),
			'USER_NEWST'		 => $this->user->lang('NEWEST_USER', get_username_string('full', $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour'])),
		));
		return;
	}

	/**
	 * Is user allowed to edit or not
	 * @param obj $auth
	 * @param array $article
	 * @return bool
	 */
	public function can_edit($auth, $article = null)
	{
		// Disallow bots and banned
		if ($this->user->data['is_bot'] || $this->phpbb_is_banned($this->user->data['user_id']))
		{
			return false;
		}

		if (is_array($article) && $article['article_id'] != '_new_')
		{
			if ($article['is_cat'])
			{
				return false;
			}
			else if ((($this->user->data['user_id'] == $article['user_id']) && $auth->acl_get('u_cmbb_post_article') ) || $auth->acl_get('m_'))
			{
				return true;
			}
		}
		else
		{
			$cats = $this->get_categories();
			if (empty($cats))
			{
				return $auth->acl_get('m_');
			}
			else
			{
				return $auth->acl_get('u_cmbb_post_article');
			}
		}
	}

}