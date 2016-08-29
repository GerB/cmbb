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

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['ger_cmbb_react_forum_id']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
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
					'module_basename'	=> '\ger\cmbb\acp\main_module',
					'modes'				=> array('settings'), // Should correspond to ./acp/main_info.php modes
				),
			)),
		);
	}
}
