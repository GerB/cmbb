<?php

/**
 *
 * cmBB
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\acp;

class settings_info
{

	function module()
	{
		return array(
			'filename'	 => '\ger\cmbb\acp\settings_module',
			'title'		 => 'CMBB_SETTINGS',
			'modes'		 => array(
				'settings' => array(
					'title'	 => 'CMBB_SETTINGS',
					'auth'	 => 'ext_ger/cmbb && acl_a_board',
					'cat'	 => array('ACP_CMBB_TITLE')
				),
			),
		);
	}

}
