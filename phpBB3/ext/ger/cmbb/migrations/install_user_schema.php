<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace ger\cmbb\migrations;

class install_user_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'cmbb_article');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'cmbb_article'	=> array(
                                            'COLUMNS'		=> array(
                                                    'article_id'            => array('UINT:10', null, 'auto_increment'),
                                                    'title'                 => array('VCHAR:255', ''),
                                                    'alias'                 => array('VCHAR:255', ''),
                                                    'user_id'               => array('UINT:10', 0),
                                                    'parent'                => array('UINT:10', 0),
                                                    'is_cat'                => array('BOOL', 0),
                                                    'template_id'           => array('TINT:4', 1),
                                                    'topic_id'              => array('UINT:10', 0),
                                                    'category_id'           => array('TINT:4', 1),
                                                    'content'               => array('MTEXT_UNI', ''),
                                                    'visible'               => array('BOOL', 0),
                                                    'datetime'              => array('TIMESTAMP', 0),
                                            ),
                                            'PRIMARY_KEY'   => 'article_id',
                                            'KEYS'          => array(
                                                    'alias'     => array('UNIQUE', 'alias'),
                                            ),
				),
				$this->table_prefix . 'cmbb_category'	=> array(
                                            'COLUMNS'		=> array(
                                                    'category_id'           => array('TINT:4', null, 'auto_increment'),
                                                    'category_name'         => array('VCHAR:45', ''),
                                                    'std_parent'            => array('UINT:10', 0),
                                                    'access'                => array('BOOL', 0),
                                            ),
                                            'PRIMARY_KEY'	=> 'category_id',
				),
				$this->table_prefix . 'cmbb_template'	=> array(
                                            'COLUMNS'           => array(
                                                    'template_id'           => array('TINT:4', null, 'auto_increment'),
                                                    'filename'              => array('VCHAR:45', ''),
                                                    'template_name'         => array('VCHAR:45', ''),
                                                    'description'           => array('VCHAR', ''),
                                                    'access'                => array('BOOL', 0),
                                            ),
                                            'PRIMARY_KEY'	=> 'template_id',
                                            'KEYS'       => array(
                                                    'tpl_nm' => array('UNIQUE', 'template_name'),
                                            ),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'cmbb_article',
				$this->table_prefix . 'cmbb_category',
				$this->table_prefix . 'cmbb_template',
			),
		);
	}
}
