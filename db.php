<?php
if (!defined('IN_CMBB')) {
    trigger_error($user->lang('MODULE_NOT_ACCESS'));
}

class cmbb
{
    /**
     * Prefix for db tables
     * Inherit this from phpBB config, loaded through constructor
     * @var string
     */
    private $table_prefix;

    /**
     * Initialise db connection
     * Inherit settings from phpBB
     *
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpasswd
     * @param string $dbname
     * @param int $dbport
     * @param string $table_prefix
     */
    public function __construct($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, $table_prefix)
    {
        $this->mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname, $dbport);
        if ($this->mysqli->connect_error) {
            die('Er is een fout opgetreden.');
        }
        $this->mysqli->set_charset("utf8");

        $this->table_prefix = $table_prefix;
    }

    /**
     * Get basic article data
     * @param string $find
     * @return array
     */
    public function get_article($find)
    {
        $query = 'SELECT * FROM `'.$this->table_prefix.'cms_article` WHERE `alias` = "'.$this->mysqli->real_escape_string($find).'";';

        if ($result = $this->mysqli->query($query)) {
            $return = $result->fetch_array();
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
        $sql_values = '';

        if (isset($article_data['article_id'])) {
            $action     = 'UPDATE '.$this->table_prefix.'cms_article SET ';
            $where      = ' WHERE `article_id` = "'.$this->mysqli->real_escape_string($article_data['article_id']).'"';
            $article_id = $article_data['article_id'];
            unset($article_data['article_id']);
        }
        else {
            $action = 'INSERT INTO '.$this->table_prefix.'cms_article SET ';
            $where  = '';
        }
        // Build query dynamically
        foreach ($article_data as $field => $val)
        {
            $sql_values .= '`'.$field.'` = "'.$this->mysqli->real_escape_string(utf8_encode($val)).'", ';
        }

        $query = $action.substr($sql_values, 0, -2).$where;

        if (!$this->mysqli->query($query)) {
            return FALSE;
        }
        else {
            return isset($article_id) ? $article_id : $this->mysqli->insert_id;
        }
    }

    /**
     * Get number of pages written by user
     * @param int $user_id
     * @return int
     */
    public function has_written($user_id)
    {
        $query = 'SELECT * FROM `'.$this->table_prefix.'cms_article`
				WHERE `user_id` = "'.$this->mysqli->real_escape_string($user_id).'"
				AND `visible` = 1;';

        if ($result = $this->mysqli->query($query)) {
            return $this->mysqli->num_rows;
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
        $query = 'SELECT * FROM `'.$this->table_prefix.'cms_article`
				WHERE `parent` = "'.$this->mysqli->real_escape_string($parent).'"
				AND `visible` = 1
				ORDER BY `datetime` DESC, `article_id` DESC;';

        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_array())
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
        $query = 'SELECT * FROM `'.$this->table_prefix.'cms_article`
				WHERE `is_cat` = 0  
				AND `visible` = 1
				AND `article_id` <> 1 
				AND `category_id` <> 9 
				ORDER BY `datetime` DESC, `article_id` DESC 
				LIMIT '.$this->mysqli->real_escape_string($limit).' ;';

        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_array())
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
        $query = 'SELECT * FROM `'.$this->table_prefix.'cms_article`
				WHERE `is_cat` = 0  
				AND `visible` = 0
				ORDER BY `datetime` DESC, `article_id` DESC ;';

        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_array())
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

        $query = 'SELECT * FROM `'.$this->table_prefix.'cms_category` ';

        if ($access === 1) {
            $query.= ' WHERE `access` = "1" ';
        }
        $query.= ' ORDER BY `category_name` ASC;';

        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_array())
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
     * Build main menu, homepage and all top level categories
     * @return string
     */
    public function build_html_menu()
    {
        $html   = '';
        $query  = 'SELECT category_name, alias
                FROM '.$this->table_prefix.'cms_category
                JOIN '.$this->table_prefix.'cms_article ON std_parent = article_id
                WHERE std_parent > 1
                GROUP BY article_id;';
        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_array())
            {
                $html.= '<li><a href="'.CMBB_ROOT.'/'.$row['alias'].'">'.$row['category_name'].'</a></li>'."\n";
            }
        }
        return $html;
    }

    /**
     * Get standard parent article_id for category_id
     * @param int $category_id
     * @return int
     */
    public function get_std_parent($category_id)
    {
        $query = 'SELECT `std_parent` FROM `'.$this->table_prefix.'cms_category` WHERE `category_id` = "'.$this->mysqli->real_escape_string($category_id).'";';

        if ($result = $this->mysqli->query($query)) {
            $return = $result->fetch_array();
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
        $query = 'SELECT `filename` FROM `'.$this->table_prefix.'cms_template` WHERE `template_id` = "'.$this->mysqli->real_escape_string($template_id).'";';

        if ($result = $this->mysqli->query($query)) {
            $return = $result->fetch_array();
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
        $query = 'SELECT `category_name` FROM `'.$this->table_prefix.'cms_category` WHERE `category_id` = "'.$this->mysqli->real_escape_string($category_id).'";';

        if ($result = $this->mysqli->query($query)) {
            $return = $result->fetch_array();
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
     * Get config items for auth
     * @return string
     */
    public function get_config()
    {
        $query  = 'SELECT * FROM `'.$this->table_prefix.'cms_config`;';
        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_array())
            {
                $return[$row['config_name']] = $row['config_value'];
            }
            if (!empty($return)) {
                return $return;
            }
        }
        return FALSE;
    }

    /**
     * Get config items for auth
     * @param string $access
     * @return array
     */
    public function get_config_items($access)
    {
        $query = 'SELECT * FROM `'.$this->table_prefix.'cms_config`
				WHERE `access` IN ('.$access.');';

        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_array())
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
     * Store config
     * @param array $config_item
     * @return bool
     */
    public function store_config($config_item)
    {
        $query = 'UPDATE `'.$this->table_prefix.'cms_config`
				SET `config_value` = "'.$this->mysqli->real_escape_string($config_item['config_value']).'"
				WHERE `config_id` = "'.$this->mysqli->real_escape_string($config_item['config_id']).'";';

        if (!$this->mysqli->query($query)) {
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    /**
     * Create DB setup for CMBB
     * Should only be called at first install
     * @param array $schema
     * @return bool
     */
    public function setup_db($schema)
    {
        // Create schema first
        foreach ($schema as $table_name => $structure)
        {
            $query = "CREATE TABLE `".$this->table_prefix.$table_name."`( \n".$structure.") \n DEFAULT CHARSET=utf8";
            $this->mysqli->query($query);
        }
        return TRUE;
    }

    /**
     * Insert data in table
     * @param array $insert
     * @return bool
     */
    public function insert_db($insert)
    {
        foreach ($insert as $table_name => $data)
        {
            foreach ($data as $row)
            {
                $query = 'INSERT INTO '.$this->table_prefix.$table_name.' SET ';
                foreach ($row as $field => $val)
                {
                    $query .= '`'.$field.'` = "'.$this->mysqli->real_escape_string(utf8_encode($val)).'", ';
                }
                $query = substr($query, 0, -2);
                $this->mysqli->query($query);
            }
        }
        return TRUE;
    }
}
// EoF


