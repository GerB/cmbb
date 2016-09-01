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

class main_info
{

	function module()
	{
		return array(
			'filename'	 => '\ger\cmbb\acp\main_module',
			'title'		 => 'ACP_CMBB_TITLE',
			'modes'		 => array(
				'settings' => array(
					'title'	 => 'ACP_CMBB_TITLE',
					'auth'	 => 'ext_ger/cmbb && acl_a_board',
					'cat'	 => array('ACP_CMBB_TITLE')
				),
			),
		);
	}

}
