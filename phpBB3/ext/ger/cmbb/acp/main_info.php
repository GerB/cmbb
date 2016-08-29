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

namespace ger\cmbb\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\ger\cmbb\acp\main_module',
			'title'		=> 'ACP_CMBB_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_CMBB_TITLE',
					'auth'	=> 'ext_ger/cmbb && acl_a_board',
					'cat'	=> array('ACP_CMBB_TITLE')
				),
			),
		);
	}
}
