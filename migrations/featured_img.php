<?php

/**
 *
 * Add featured_img to db
 *
 * @copyright (c) 2017 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\migrations;

use phpbb\db\migration\container_aware_migration;

class featured_img extends container_aware_migration
{

	static public function depends_on()
	{
		return array('\ger\cmbb\migrations\install_cmbb');
	}

	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'cmbb_article', 'featured_img');
	}	
	
	public function update_schema()
	{
		return array(
			'add_columns'   => array(
                $this->table_prefix . 'cmbb_article'  => array(
                    'featured_img'  => array('VCHAR:255', ''),
                ),
            ),
		);
	}
}
