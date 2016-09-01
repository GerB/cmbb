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
						'template_id'	 => array('TINT:4', 1),
						'topic_id'		 => array('UINT:10', 0),
						'category_id'	 => array('TINT:4', 1),
						'content'		 => array('MTEXT_UNI', ''),
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
						'access'		 => array('BOOL', 0),
					),
					'PRIMARY_KEY'	 => 'category_id',
				),
				$this->table_prefix . 'cmbb_template'	 => array(
					'COLUMNS'		 => array(
						'template_id'	 => array('TINT:4', null, 'auto_increment'),
						'filename'		 => array('VCHAR:45', ''),
						'template_name'	 => array('VCHAR:45', ''),
						'description'	 => array('VCHAR', ''),
						'access'		 => array('BOOL', 0),
					),
					'PRIMARY_KEY'	 => 'template_id',
					'KEYS'			 => array(
						'tpl_nm' => array('UNIQUE', 'template_name'),
					),
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
				$this->table_prefix . 'cmbb_template',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('ger_cmbb_react_forum_id', 2)),
			array('config.add', array('ger_cmbb_number_index_items', 10)),
			array('config.add', array('ger_cmbb_min_post_count', 100)),
			array('config.add', array('ger_cmbb_min_title_length', 4)),
			array('config.add', array('ger_cmbb_min_content_length', 200)),
			array('config.add', array('ger_cmbb_announce_text', '')),
			array('config.add', array('ger_cmbb_announce_show', 0)),
			array('module.add', array(
					'acp',
					'ACP_CAT_DOT_MODS',
					'ACP_CMBB_TITLE'
				)),
			array('module.add', array(
					'acp',
					'ACP_CMBB_TITLE',
					array(
						'module_basename'	 => '\ger\cmbb\acp\main_module',
						'modes'				 => array('settings'), // Should correspond to ./acp/main_info.php modes
					),
				)),
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
					"template_id"	 => 1,
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
					"template_id"	 => 1,
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
					"template_id"	 => 1,
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
					"access"		 => "0",
				),
				array(
					"category_name"	 => "News",
					"std_parent"	 => "2",
					"access"		 => "1",
				),
				array(
					"category_name"	 => "Articles",
					"std_parent"	 => "3",
					"access"		 => "1",
				),
			),
			"cmbb_template"	 => array(
				array(
					"filename"		 => "index.html",
					"template_name"	 => "Index",
					"description"	 => "Listing article exerpts",
					"access"		 => "0",
				),
				array(
					"filename"		 => "article.html",
					"template_name"	 => "Article",
					"description"	 => "All basic articles can fit in this template",
					"access"		 => "0",
				),
				array(
					"filename"		 => "base.html",
					"template_name"	 => "Base",
					"description"	 => "Basic template, nothing fancy",
					"access"		 => "0",
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
