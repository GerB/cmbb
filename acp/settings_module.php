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

class settings_module
{

	public $u_action;

	public function main($id, $mode)
	{
		global $config, $request, $template, $user, $phpbb_container;

		$user->add_lang_ext('ger/cmbb', 'common');
		$this->tpl_name = 'acp_cmbb_body';
		$this->page_title = $user->lang('CMBB_SETTINGS');
		add_form_key('ger/cmbb');

		$config_text = $phpbb_container->get('config_text');
		$helper = $phpbb_container->get('controller.helper');
		$cmbb = $phpbb_container->get('ger.cmbb.cmbb.driver');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('ger/cmbb'))
			{
				trigger_error('FORM_INVALID');
			}

			// Store values
			$config->set('ger_cmbb_number_index_items', $request->variable('number_index_items', 0));
			$config->set('ger_cmbb_min_title_length', $request->variable('min_title_length', 0));
			$config->set('ger_cmbb_min_content_length', $request->variable('min_content_length', 0));
			$config->set('ger_cmbb_show_menubar', $request->variable('show_menubar', 0));
			$config->set('ger_cmbb_show_rightbar', $request->variable('show_rightbar', 0));
			$config_text->set('ger_cmbb_rightbar_html', htmlspecialchars_decode($request->variable('rightbar_html', '', true), ENT_COMPAT));

			trigger_error($user->lang('ACP_CMBB_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		// Check if we have any articles yet.
		$has_articles = false;
		if ($cmbb->get_last(1)) {
			$has_articles = true;
		}

		$template->assign_vars(array(
			'U_ACTION'			 => $this->u_action,
			'U_NEW_ARTICLE'		 => $helper->route('ger_cmbb_article_edit', array('article_id' => '_new_')),
			'NUMBER_INDEX_ITEMS' => $config['ger_cmbb_number_index_items'],
			'MIN_TITLE_LENGTH'	 => $config['ger_cmbb_min_title_length'],
			'MIN_CONTENT_LENGTH' => $config['ger_cmbb_min_content_length'],
			'S_NO_ARTICLES'		 => empty($has_articles) ? false : true,
			'S_SHOW_MENUBAR'	 => $config['ger_cmbb_show_menubar'],
			'S_SHOW_RIGHTBAR'	 => $config['ger_cmbb_show_rightbar'],
			'RIGHTBAR_HTML'		 => $config_text->get('ger_cmbb_rightbar_html'),
		));
	}

}
