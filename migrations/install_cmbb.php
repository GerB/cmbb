<?php

/**
 *
 * cmBB
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\migrations;

use phpbb\db\migration\container_aware_migration;

class install_cmbb extends container_aware_migration
{

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'cmbb_article'	 => array(
					'COLUMNS'		 => array(
						'article_id'	 => array('UINT:10', null, 'auto_increment'),
						'title'			 => array('VCHAR:255', ''),
						'alias'			 => array('VCHAR:255', ''),
						'user_id'		 => array('UINT:10', 0),
						'parent'		 => array('UINT:10', 0),
						'is_cat'		 => array('BOOL', 0),
						'topic_id'		 => array('UINT:10', 0),
						'category_id'	 => array('TINT:4', 1),
						'content'		 => array('MTEXT_UNI', ''),
						'featured_img'	 => array('VCHAR:255', ''),
						'visible'		 => array('BOOL', 0),
						'datetime'		 => array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY'	 => 'article_id',
					'KEYS'			 => array(
						'alias' => array('UNIQUE', 'alias'),
					),
				),
				$this->table_prefix . 'cmbb_category'	 => array(
					'COLUMNS'		 => array(
						'category_id'	 => array('TINT:4', null, 'auto_increment'),
						'category_name'	 => array('VCHAR:45', ''),
						'std_parent'	 => array('UINT:10', 0),
						'react_forum_id' => array('UINT:10', 2),
						'protected'		 => array('BOOL', 0),
						'show_menu_bar'	 => array('BOOL', 1),
					),
					'PRIMARY_KEY'	 => 'category_id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'cmbb_article',
				$this->table_prefix . 'cmbb_category',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('ger_cmbb_number_index_items', 10)),
			array('config.add', array('ger_cmbb_min_title_length', 4)),
			array('config.add', array('ger_cmbb_min_content_length', 200)),
			array('config.add', array('ger_cmbb_show_menubar', 1)),
			array('config.add', array('ger_cmbb_show_rightbar', 0)),
			array('config_text.add', array('ger_cmbb_rightbar_html', '<h3>cmBB is the best! :)</h3>' . "\n" . '<p>Cats are cute</p>')),
			array('module.add', array(
					'acp',
					'ACP_CAT_DOT_MODS',
					'ACP_CMBB_TITLE'
				)),
			array('module.add', array(
					'acp',
					'ACP_CMBB_TITLE',
					array(
						'module_basename'	 => '\ger\cmbb\acp\settings_module',
						'modes'				 => array('settings'), // Should correspond to ./acp/main_info.php modes
					),
				)),
			array('module.add', array(
					'acp',
					'ACP_CMBB_TITLE',
					array(
						'module_basename'	 => '\ger\cmbb\acp\categories_module',
						'modes'				 => array('categories'), // Should correspond to ./acp/main_info.php modes
					),
				)),
			array('permission.add', array('u_cmbb_post_article')),
			array('permission.permission_set', array('ROLE_USER_FULL', 'u_cmbb_post_article', 'role')),
			array('permission.permission_set', array('ROLE_USER_NEW_MEMBER', 'u_cmbb_post_article', 'role', false)),
			array('custom', array(array($this, 'add_default_values'))),
		);
	}

	/**
	 * Add default values
	 *
	 */
	public function add_default_values()
	{
		$user = $this->container->get('user');

		$inserts = array(
			"cmbb_article"	 => array(
				array(
					"title"			 => "Home",
					"alias"			 => "index",
					"user_id"		 => $user->data['user_id'],
					"is_cat"		 => 1,
					"category_id"	 => 1,
					"content"		 => "",
					"visible"		 => 1,
					"topic_id"		 => 0,
				),
				array(
					"title"			 => "News",
					"alias"			 => "news",
					"user_id"		 => $user->data['user_id'],
					"parent"		 => 1,
					"is_cat"		 => 1,
					"category_id"	 => 2,
					"content"		 => "",
					"visible"		 => 1,
					"topic_id"		 => 0,
				),
				array(
					"title"			 => "Articles",
					"alias"			 => "articles",
					"user_id"		 => $user->data['user_id'],
					"parent"		 => 1,
					"is_cat"		 => 1,
					"category_id"	 => 3,
					"content"		 => "",
					"visible"		 => 1,
					"topic_id"		 => 0,
				),
			),
			"cmbb_category"	 => array(
				array(
					"category_name"	 => "Home",
					"std_parent"	 => "1",
					"protected"		 => "0",
					"show_menu_bar"	 => "1",
				),
				array(
					"category_name"	 => "News",
					"std_parent"	 => "2",
					"protected"		 => "1",
					"show_menu_bar"	 => "1",
				),
				array(
					"category_name"	 => "Articles",
					"std_parent"	 => "3",
					"protected"		 => "1",
					"show_menu_bar"	 => "1",
				),
			),
		);

		foreach ($inserts as $table_name => $record)
		{
			foreach ($record as $data)
			{
				$query = 'INSERT INTO ' . $this->table_prefix . $table_name . ' ' . $this->db->sql_build_array('INSERT', $data);
				$this->db->sql_query($query);
			}
		}
	}

}
