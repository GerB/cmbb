<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ger\cmbb\cmbb;

//use Symfony\Component\DependencyInjection\ContainerInterface;

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
        $this->config          = $config;
        $this->request         = $request;
        $this->user            = $user;
        $this->db              = $db;
        $this->article_table   = $article_table;
        $this->category_table  = $category_table;
        $this->config_table    = $config_table;
        $this->template_table  = $template_table;
        $this->phpbb_root_path = $phpbb_root_path;

        if (!defined('CMBB_ROOT')) {
            define('CMBB_ROOT', '/cmbb');
        }
    }

    /**
     * Build main menu, homepage and all top level categories
     * @return string
     */
    public function list_menu_items()
    {
        $html = '';

        $sql_array = array(
            'SELECT' => 'category_name, alias',
            'FROM' => array(
                $this->category_table => 'c',
                $this->article_table => 'a',
            ),
            'WHERE' => 'std_parent > 1
                    AND std_parent = article_id',
            'GROUP_BY' => 'article_id',
        );

        $sql    = $this->db->sql_build_query('SELECT', $sql_array);
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $html.= '<li><a href="'.$this->phpbb_root_path.'app.php/page/'.$row['alias'].'">'.$row['category_name'].'</a></li>'."\n";
        }

        return $html;
    }

    /**
     * Get basic article data
     * @param mixed $find
     * @return array
     */
    public function get_article($find)
    {
        if (is_numeric($find)) {
            $query = 'SELECT * FROM '.$this->article_table.' WHERE `article_id` = "'.intval($find).'";';
        }
        else {
            $query = 'SELECT * FROM '.$this->article_table.' WHERE `alias` = "'.$this->db->sql_escape($find).'";';
        }

        if ($result = $this->db->sql_query($query)) {
            $return = $this->db->sql_fetchrow($result);
            if (!empty($return)) {
                return $return;
            }
        }
        return FALSE;
    }

    /**
     * Store new or edited article data
     * @param array $article_data
     * @return int
     */
    public function store_article($article_data)
    {
        if (isset($article_data['article_id'])) {
            $article_id = $article_data['article_id'];
            unset($article_data['article_id']);
            $action        = 'UPDATE '.$this->article_table.' '.$this->db->sql_build_array('UPDATE', $article_data).' WHERE `article_id` = "'.$article_id.'"';
        }
        else {
            $action = 'INSERT INTO '.$this->article_table.' '.$this->db->sql_build_array('INSERT', $article_data);
        }

        if (!$this->db->sql_query($action)) {
            return FALSE;
        }
        else {
            return isset($article_id) ? $article_id :  $this->db->sql_nextid();
        }
    }

    /**
     * Get number of pages written by user
     * @param int $user_id
     * @return int
     */
    public function has_written($user_id)
    {
        $query = 'SELECT count(*) AS counted FROM '.$this->article_table.'
				WHERE `user_id` = "'.filter_var($user_id, FILTER_SANITIZE_NUMBER_INT).'"
				AND `visible` = 1;';

        if ($result = $this->db->sql_query($query)) {
            $return = $this->db->sql_fetchrow($result);
            return $return['counted'];
        }
        return FALSE;
    }

    /**
     * Get children pages for parent article_id
     * @paramt int $parent
     * @return array
     */
    public function get_children($parent)
    {
        $query = 'SELECT * FROM '.$this->article_table.'
				WHERE `parent` = "'.$this->db->sql_escape($parent).'"
				AND `visible` = 1
				ORDER BY `datetime` DESC, `article_id` DESC;';

        if ($result = $this->db->sql_query($query)) {
            while ($row = $this->db->sql_fetchrow($result))
            {
                $return[] = $row;
            }
            if (!empty($return)) {
                return $return;
            }
        }
        return FALSE;
    }

    /**
     * Get last n visible items
     * @param int $limit
     * @return array
     */
    public function get_last($limit)
    {
        $query = 'SELECT * FROM '.$this->article_table.'
				WHERE `is_cat` = 0
				AND `visible` = 1
				AND `article_id` <> 1
				AND `category_id` <> 9
				ORDER BY `datetime` DESC, `article_id` DESC
				LIMIT '.filter_var($limit, FILTER_SANITIZE_NUMBER_INT).' ;';

        if ($result = $this->db->sql_query($query)) {
            while ($row = $this->db->sql_fetchrow($result))
            {
                $return[] = $row;
            }
            if (!empty($return)) {
                return $return;
            }
        }
        return FALSE;
    }

    /**
     * Get hidden items
     * @return array
     */
    public function get_hidden()
    {
        $query = 'SELECT * FROM '.$this->article_table.'
				WHERE `is_cat` = 0
				AND `visible` = 0
				ORDER BY `datetime` DESC, `article_id` DESC ;';

        if ($result = $this->db->sql_query($query)) {
            while ($row = $this->db->sql_fetchrow($result))
            {
                $return[] = $row;
            }
            if (!empty($return)) {
                return $return;
            }
        }
        return FALSE;
    }

    /**
     * Get array of categories
     * @param int $access
     * @return array
     */
    public function get_categories($access = 1)
    {

        $query = 'SELECT * FROM '.$this->category_table.' ';

        if ($access === 1) {
            $query.= ' WHERE `access` = "1" ';
        }
        $query.= ' ORDER BY `category_name` ASC;';

        if ($result = $this->db->sql_query($query)) {
            while ($row = $this->db->sql_fetchrow($result))
            {
                $return[$row['category_id']] = $row['category_name'];
            }
            if (!empty($return)) {
                return $return;
            }
        }
        return FALSE;
    }

    /**
     * Get standard parent article_id for category_id
     * @param int $category_id
     * @return int
     */
    public function get_std_parent($category_id)
    {
        $query = 'SELECT `std_parent` FROM '.$this->category_table.' WHERE `category_id` = "'.filter_var($category_id, FILTER_SANITIZE_NUMBER_INT).'";';

        if ($result = $this->db->sql_query($query)) {
            $return = $this->db->sql_fetchrow($result);
            if (!empty($return)) {
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
        $query = 'SELECT `filename` FROM '.$this->template_table.' WHERE `template_id` = "'.filter_var($template_id, FILTER_SANITIZE_NUMBER_INT).'";';

        if ($result = $this->db->sql_query($query)) {
            $return = $this->db->sql_fetchrow($result);
            if (!empty($return)) {
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
        $query = 'SELECT `category_name` FROM '.$this->category_table.' WHERE `category_id` = "'.filter_var($category_id, FILTER_SANITIZE_NUMBER_INT).'";';

        if ($result = $this->db->sql_query($query)) {
            $return = $this->db->sql_fetchrow($result);
            if (!empty($return)) {
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
        if (!$this->get_article($try)) {
            // Ok
            return $try;
        }
        else {
            // Try adding a standard suffix
            for ($i = 2; $i < 10; $i++)
            {
                $next_try = $try.'-'.$i;
                if (!$this->get_article($next_try)) {
                    // Ok
                    return $next_try;
                }
            }
            // Still here? Not gentile, but this will always work
            return $try.'-'.date('YmdHis');
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
        if (empty($user_id)) {
            return FALSE;
        }

        $sql    = 'SELECT `username`, `user_colour` , `user_id`, `group_id`
                   FROM '.USERS_TABLE.'
                   WHERE user_id ='.$userid;
        $result = $this->db->sql_query($sql);
        $row    = $this->db->sql_fetchrow($result);

        if (empty($row))
        {
            return FALSE;
        }
        if ($row['group_id'] != "754") {
            return'<a href="'.$this->phpbb_root_path.'memberlist.php?mode=viewprofile&amp;u='.$row['user_id'].'" style="color:#'.$row['user_colour'].'; font-weight: bold;">'.$row['username'].'</a>';
        }
        else {
            return '<span style="color:#'.$row['user_colour'].';">'.$row['username'].'</span>';
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
        if (empty($user_id)) {
            return FALSE;
        }

        $sql    = 'SELECT `username`, `user_avatar`, `user_avatar_type` , `user_avatar_width`, `user_avatar_height`
                   FROM '.USERS_TABLE.'
                   WHERE user_id = '.$userid;
        $result = $this->db->sql_query($sql);
        $row    = $this->db->sql_fetchrow($result);

        if (!$row) {
            $row = array(
                'username' => '',
                'user_avatar' => '',
                'user_avatar_type' => '',
                'user_avatar_width' => 0,
                'user_avatar_height' => 0,
            );
        }
        if (substr($row['user_avatar'], 0, 4) == 'http') {
            $path = $row['user_avatar'];
        }
        else if (empty($row['user_avatar'])) {
            $path                      = CMBB_ROOT.'/assets/site/default_avatar.jpg';
            $row['user_avatar_width']  = $row['user_avatar_height'] = 90;
        }
        else {
            $path = '../../download/file.php?avatar='.$row['user_avatar'];
        }

        if ($row['user_avatar_width'] > $row['user_avatar_height']) {
            $width_height = ' width="90" ';
        }
        else {
            $width_height = ' height="90" ';
        }

        return '<img src="'.$path.'" '.$width_height.' alt= "'.$row['username'].'" title="'.$row['username'].'" style="'.$style.'" />';
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
        if (empty($topic_limit)) {
            return FALSE;
        }

        // Select the last topics to which we have permissions
        $sql    = 'SELECT t.topic_id, t.forum_id, t.topic_title
                            FROM '.TOPICS_TABLE.' t , '.USERS_TABLE.' u
                            WHERE topic_visibility = 1
                            AND '.$this->db->sql_in_set('forum_id', $forums).'
                            AND u.user_id = t.topic_poster
                            ORDER BY topic_time DESC
                            LIMIT 0,'.$topic_limit;
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $return[] = array(
                'topic_id' => $row['topic_id'],
                'forum_id' => $row['forum_id'],
                'topic_title' => character_limiter($row['topic_title'], 30, '&hellip;'),
            );
        }
        return empty($return) ? FALSE : $return;
    }

    /**
     * Check if user is in ban table
     * @param int $user_id
     * @return bool
     */
    public function phpbb_is_banned($userid)
    {
        $user_id = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);
        if (empty($user_id)) {
            return FALSE;
        }
        $sql    = 'SELECT count(*) as banned FROM '.BANLIST_TABLE.' WHERE ban_userid = '.$userid;
        $result = $this->db->sql_query($sql);
        $banned = (int) $this->db->sql_fetchfield('banned');
        $this->db->sql_freeresult($result);

        if (!empty($banned)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
}
// EoF
