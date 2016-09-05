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
	protected $request;
	protected $user;
	protected $db;
	protected $article_table;
	protected $category_table;
	protected $config_table;
	protected $template_table;
	protected $phpbb_root_path;
	public $site_config;
	public $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth							$auth					Auth object
	 * @param \phpbb\config\config						$config					Config object
	 * @param ContainerInterface						$phpbb_container		Service container
	 * @param \phpbb\request\request_interface			$request				Request object
	 * @param \phpbb\template\template					$template				Template object
	 * @param \phpbb\user								$user					User object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\request\request_interface $request, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $article_table, $category_table, $config_table, $template_table)
	{
		$this->config = $config;
		$this->request = $request;
		$this->user = $user;
		$this->db = $db;
		$this->article_table = $article_table;
		$this->category_table = $category_table;
		$this->config_table = $config_table;
		$this->template_table = $template_table;
		$this->phpbb_root_path = generate_board_url() . substr($phpbb_root_path, 1);
	}

	/**
	 * Build main menu, homepage and all top level categories
	 * @return string
	 */
	public function list_menu_items()
	{
		$html = '';

		$sql_array = array(
			'SELECT'	 => 'category_name, alias',
			'FROM'		 => array(
				$this->category_table	 => 'c',
				$this->article_table	 => 'a',
			),
			'WHERE'		 => 'std_parent > 1
                    AND std_parent = article_id',
			'GROUP_BY'	 => 'article_id',
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$return[] = $row;
		}

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
			$query = 'SELECT * FROM ' . $this->article_table . ' WHERE `article_id` = "' . intval($find) . '";';
		}
		else
		{
			$query = 'SELECT * FROM ' . $this->article_table . ' WHERE `alias` = "' . $this->db->sql_escape($find) . '";';
		}

		if ($result = $this->db->sql_query($query))
		{
			$return = $this->db->sql_fetchrow($result);
			if (!empty($return))
			{
				return $return;
			}
		}
		return false;
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
			$action = 'UPDATE ' . $this->article_table . ' SET ' . $this->db->sql_build_array('UPDATE', $article_data) . ' WHERE `article_id` = "' . $article_id . '"';
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
	 * Get number of pages written by user
	 * @param int $user_id
	 * @return int
	 */
	public function has_written($user_id)
	{
		$query = 'SELECT count(*) AS counted FROM ' . $this->article_table . '
				WHERE `user_id` = "' . filter_var($user_id, FILTER_SANITIZE_NUMBER_INT) . '"
				AND `visible` = 1;';

		if ($result = $this->db->sql_query($query))
		{
			$return = $this->db->sql_fetchrow($result);
			return $return['counted'];
		}
		return false;
	}

	/**
	 * Get children pages for parent article_id
	 * @paramt int $parent
	 * @return array
	 */
	public function get_children($parent)
	{
		$query = 'SELECT * FROM ' . $this->article_table . '
				WHERE `parent` = "' . $this->db->sql_escape($parent) . '"
				AND `visible` = 1
				ORDER BY `datetime` DESC, `article_id` DESC;';

		if ($result = $this->db->sql_query($query))
		{
			while ($row = $this->db->sql_fetchrow($result))
			{
				$return[] = $row;
			}
			if (!empty($return))
			{
				return $return;
			}
		}
		return false;
	}

	/**
	 * Get last n visible items
	 * @param int $limit
	 * @return array
	 */
	public function get_last($limit)
	{
		$query = 'SELECT * FROM ' . $this->article_table . '
				WHERE `is_cat` = 0
				AND `visible` = 1
				AND `article_id` <> 1
				AND `category_id` <> 9
				ORDER BY `datetime` DESC, `article_id` DESC
				LIMIT ' . filter_var($limit, FILTER_SANITIZE_NUMBER_INT) . ' ;';

		if ($result = $this->db->sql_query($query))
		{
			while ($row = $this->db->sql_fetchrow($result))
			{
				$return[] = $row;
			}
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
				WHERE `is_cat` = 0
				AND `visible` = 0
				ORDER BY `datetime` DESC, `article_id` DESC ;';

		if ($result = $this->db->sql_query($query))
		{
			while ($row = $this->db->sql_fetchrow($result))
			{
				$return[] = $row;
			}
			if (!empty($return))
			{
				return $return;
			}
		}
		return false;
	}

	/**
	 * Get array of categories
	 * @param int $access
	 * @return array
	 */
	public function get_categories($access = 1)
	{

		$query = 'SELECT * FROM ' . $this->category_table . ' ';

		if ($access === 1)
		{
			$query.= ' WHERE `access` = "1" ';
		}
		$query.= ' ORDER BY `category_name` ASC;';

		if ($result = $this->db->sql_query($query))
		{
			while ($row = $this->db->sql_fetchrow($result))
			{
				$return[$row['category_id']] = $row['category_name'];
			}
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
		$query = 'SELECT `std_parent` FROM ' . $this->category_table . ' WHERE `category_id` = "' . filter_var($category_id, FILTER_SANITIZE_NUMBER_INT) . '";';

		if ($result = $this->db->sql_query($query))
		{
			$return = $this->db->sql_fetchrow($result);
			if (!empty($return))
			{
				return $return['std_parent'];
			}
		}
		return '1';
	}

	/**
	 * Get filename for template, default to article.html
	 * @param int $template_id
	 * @return string
	 */
	public function get_template_content($template_id)
	{
		$query = 'SELECT `filename` FROM ' . $this->template_table . ' WHERE `template_id` = "' . filter_var($template_id, FILTER_SANITIZE_NUMBER_INT) . '";';

		if ($result = $this->db->sql_query($query))
		{
			$return = $this->db->sql_fetchrow($result);
			if (!empty($return))
			{
				return $return['filename'];
			}
		}
		return 'article.html';
	}

	/**
	 * Fetch category name, default to Articles
	 * @param int $category_id
	 * @return string
	 */
	public function fetch_category($category_id)
	{
		$query = 'SELECT `category_name` FROM ' . $this->category_table . ' WHERE `category_id` = "' . filter_var($category_id, FILTER_SANITIZE_NUMBER_INT) . '";';

		if ($result = $this->db->sql_query($query))
		{
			$return = $this->db->sql_fetchrow($result);
			if (!empty($return))
			{
				return $return['category_name'];
			}
		}
		return 'Articles';
	}

	/**
	 * Generate an alias for a title
	 * @param string $title
	 * @return string
	 */
	public function generate_page_alias($title)
	{
		// Basic cleanup
		$try = trim(strtolower(str_replace('-', ' ', $title)));

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$try = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $try);

		// Trim dashes at beginning and end of alias
		$try = trim($try, '-');

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
	 * @return string
	 */
	public function phpbb_get_user($userid)
	{

		$user_id = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);
		if (empty($user_id))
		{
			return false;
		}

		$sql = 'SELECT `username`, `user_colour` , `user_id`, `group_id`
                   FROM ' . USERS_TABLE . '
                   WHERE user_id =' . $userid;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		if (empty($row))
		{
			return false;
		}
		if ($row['group_id'] != "754")
		{
			return'<a href="' . $this->phpbb_root_path . 'memberlist.php?mode=viewprofile&amp;u=' . $row['user_id'] . '" style="color:#' . $row['user_colour'] . '; font-weight: bold;">' . $row['username'] . '</a>';
		}
		else
		{
			return '<span style="color:#' . $row['user_colour'] . ';">' . $row['username'] . '</span>';
		}
	}

	/**
	 * Get formatted user avatar or default avatar
	 *
	 * @param int $user_id
	 * @param string $style = ''
	 *
	 * @return string
	 */
	function phpbb_user_avatar($userid, $style = '')
	{
		$user_id = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);
		if (empty($user_id))
		{
			return false;
		}

		$sql = 'SELECT `username`, `user_avatar`, `user_avatar_type` , `user_avatar_width`, `user_avatar_height`
                   FROM ' . USERS_TABLE . '
                   WHERE user_id = ' . $userid;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

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
			$path = '../../download/file.php?avatar=' . $row['user_avatar'];
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
	 * Fetch latest forum topics
	 * @param array $forums
	 * @param int $topic_limit
	 * @return array
	 */
	public function phpbb_latest_topics($forums, $topic_limit = 5)
	{
		$topic_limit = filter_var($topic_limit, FILTER_SANITIZE_NUMBER_INT);
		if (empty($topic_limit))
		{
			return false;
		}

		// Select the last topics to which we have permissions
		$sql = 'SELECT t.topic_id, t.forum_id, t.topic_title
                            FROM ' . TOPICS_TABLE . ' t , ' . USERS_TABLE . ' u
                            WHERE topic_visibility = 1
                            AND ' . $this->db->sql_in_set('forum_id', $forums) . '
                            AND u.user_id = t.topic_poster
                            ORDER BY topic_time DESC
                            LIMIT 0,' . $topic_limit;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$return[] = array(
				'topic_id'		 => $row['topic_id'],
				'forum_id'		 => $row['forum_id'],
				'topic_title'	 => strlen($row['topic_title']) > 30 ? substr($row['topic_title'], 0, 30) . '&hellip;' : $row['topic_title'],
			);
		}
		return empty($return) ? false : $return;
	}

	/**
	 * Check if user is in ban table
	 * @param int $user_id
	 * @return bool
	 */
	public function phpbb_is_banned($userid)
	{
		$user_id = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);
		if (empty($user_id))
		{
			return false;
		}
		$sql = 'SELECT count(*) as banned FROM ' . BANLIST_TABLE . ' WHERE ban_userid = ' . $userid;
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
	 * Build sidebar for front-facing pages
	 */
	public function build_sidebar($page, $auth, $helper, $mode)
	{
		$cmbb_sidebar = '<div id="login">';
		// Either greet or show login box
		if ($this->user->data['is_registered'])
		{

			$cmbb_sidebar.= "<h3>" . $this->user->lang('WELCOME_USER', $this->user->data['username']) . "</h3>";
			$cmbb_sidebar.= ' (<a href="' . append_sid("{$this->phpbb_root_path}ucp.php", 'mode=logout', true, $this->user->session_id) . '">' . $this->user->lang('LOGOUT') . '</a>)';

			// Show link to editor
			if ($this->can_edit($auth))
			{
				$cmbb_sidebar.= '<p class="fakebutton"><a href="' . $helper->route('ger_cmbb_page_edit', array('article_id' => '_new_')) . '">+ ' . $this->user->lang('NEW_ARTICLE') . '</a></p>';
			}
			if ($this->can_edit($auth, $page) && $mode == 'view')
			{
				$cmbb_sidebar.= '<p class="fakebutton"><a href="' . $helper->route('ger_cmbb_page_edit', array('article_id' => $page['article_id'])) . '">' . $this->user->lang('EDIT_ARTICLE') . '</a></p>';
			}
			if ($auth->acl_get('m_'))
			{
				$cmbb_sidebar.= '<br /><hr /><br />';
				if ($this->get_hidden())
				{
					$cmbb_sidebar.= '<p class="fakebutton"><a href="index?showhidden=1">' . $this->user->lang('SHOW_HIDDEN') . '</a></p>';
				}
				else
				{
					$cmbb_sidebar.= '<p class="fakebutton inactive"><a>' . $this->user->lang('NO_HIDDEN') . '</a></p>';
				}
			}
		}
		else
		{
			$cmbb_sidebar.= $this->login_box($page['alias']);
		}
		$cmbb_sidebar.= '</div>';

		/*
		 * Fetch newest topics
		 */
		$cmbb_sidebar.=
				'<div id="topiclist"><hr />
                            <h3>' . $this->user->lang('FEED_TOPICS_NEW') . '</h3><br />
                            <ul class="lt">';

		$latest = $this->phpbb_latest_topics(array_unique(array_keys($auth->acl_getf('f_read', true))), 5);
		foreach ($latest as $row)
		{
			$url = $this->phpbb_root_path . '/viewtopic.php?f=' . $row['forum_id'] . '&amp;t=' . $row['topic_id'] . '&amp;view=unread#unread';
			$cmbb_sidebar.= '<li class="lt"><a href="' . $url . '">' . $row['topic_title'] . '</a> </li>';
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
                    <h3> ' . $this->user->lang('STATISTICS') . '</h3>
                    <p><a href="' . $this->phpbb_root_path . 'viewonline.php">' . $this->user->lang('WHO_IS_ONLINE') . '</a>:<br>' . $whosonline['online_userlist'] . '</p>';

		$cmbb_sidebar.= '<p>' . $this->user->lang('TOTAL_USERS', (int) $this->config['num_users']) . '<br />' .
				$this->user->lang('NEWEST_USER', get_username_string('full', $this->config['newest_user_id'], $this->config['newest_username'], $this->config['newest_user_colour'])) .
				'</p></div>';

		return $cmbb_sidebar;
	}

	/**
	 * Fetch formatted login box
	 * @return type
	 */
	private function login_box($alias = 'index')
	{
		return '<h3>' . $this->user->lang('LOGIN') . ':</h3><br />
                <form method="post" action="' . $this->phpbb_root_path . 'ucp.php?mode=login">
                    <p><span>' . $this->user->lang('USERNAME') . ':</span> <input type="text" name="username" size="14" /><br />
                    <span>' . $this->user->lang('PASSWORD') . ':</span> <input type="password" name="password" size="14" /><br />
                    <span>' . $this->user->lang('LOG_ME_IN') . ':</span> <input type="checkbox" name="autologin" /><br />
                    <input type="submit" class="btnmain" value="' . $this->user->lang('LOGIN') . '" name="login" /></p>
                    <input type="hidden" name="redirect" value="' . $this->phpbb_root_path . 'app.php/page/' . $alias . '" />
                </form>';
	}

	/**
	 * Is user allowed to edit or not
	 * @param obj $auth
	 * @param array $page
	 * @return bool
	 */
	public function can_edit($auth, $page = null)
	{
		// Disallow bots and banned
		if ($this->user->data['is_bot'] || $this->phpbb_is_banned($this->user->data['user_id']))
		{
			return false;
		}

		if (is_array($page))
		{
			if ($page['is_cat'])
			{
				return false;
			}
			else if (($this->user->data['user_id'] == $page['user_id']) || $auth->acl_get('m_'))
			{
				return true;
			}
		}

		// Allow moderators and users with enough posts or that have written before
		if ($auth->acl_get('m_') || ($this->user->data['user_posts'] >= $this->config['ger_cmbb_min_post_count']) || $this->has_written($this->user->data['user_id']))
		{
			return true;
		}
	}

}

// EoF
