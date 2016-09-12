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

class categories_info
{

	function module()
	{
		return array(
			'filename'	 => '\ger\cmbb\acp\categories_module',
			'title'		 => 'ACP_CMBB_CATEGORIES',
			'modes'		 => array(
				'categories' => array(
					'title'	 => 'ACP_CMBB_CATEGORIES',
					'auth'	 => 'ext_ger/cmbb && acl_a_board',
					'cat'	 => array('ACP_CMBB_TITLE')
				),
			),
		);
	}

}
