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

class categories_module
{

	var $u_action;

	public function main($id, $mode)
	{
		global $request, $template, $user, $phpbb_container;

		// Get an instance of the admin controller
		$cmbb = $phpbb_container->get('ger.cmbb.cmbb.driver');

		$user->add_lang_ext('ger/cmbb', 'common');
		$this->tpl_name = 'acp_cmbb_categories';
		$this->page_title = $user->lang('ACP_CMBB_CATEGORIES');
		add_form_key('ger/cmbb');

		// Fetch current categories
		$categories = $cmbb->get_categories(true, true);

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('ger/cmbb'))
			{
				trigger_error('FORM_INVALID');
			}

			// Is a new category added?
			if ($request->is_set_post('add_category'))
			{
				$category_name = $request->variable('add_category', '', true);
				if (!$this->category_name_unique($categories, $category_name))
				{
					trigger_error('CMBB_CATEGORY_NAME_INVALID' . adm_back_link($this->u_action), E_USER_WARNING);
				}

				// Setup article holder for category
				$article_data = array(
					'title'			 => $category_name,
					'alias'			 => $cmbb->generate_article_alias($category_name),
					'user_id'		 => $user->data['user_id'],
					'parent'		 => 1,
					'is_cat'		 => 1,
					'category_id'	 => 1, // We'll change this later
					'content'		 => '',
					'visible'		 => 1,
					'topic_id'		 => 0,
					'datetime'		 => time(),
				);
				$article_id = $cmbb->store_article($article_data);

				// Store category
				$category_data = array(
					'category_name'		=> $category_name,
					'std_parent'		=> $article_id,
					'protected'			=> 1,
					'show_menu_bar'		=> 1,
					'react_forum_id'	=> 0,
				);
				$category_id = $cmbb->store_category($category_data);

				// Update article holder with new category_id
				$article_update = array(
					'article_id' => $article_id,
					'category_id' => $category_id
				);
				$cmbb->store_article($article_update);

			}
			else
			{
				foreach ($categories as $cat)
				{
					// Exclude homepage
					if ($cat['category_id'] > 1)
					{
						$category_name = $request->variable($cat['category_id'] . '_category_name', '', true);
						if (!$this->category_name_unique($categories, $category_name, $cat['category_id']))
						{
							trigger_error('CMBB_CATEGORY_NAME_INVALID');
						}

						$category_data = array(
							'category_id' => $cat['category_id'],
							'category_name' => $category_name,
							'react_forum_id' => $request->variable($cat['category_id'] . '_react_forum_id', 0),
							'show_menu_bar' => strlen($request->variable($cat['category_id'] . '_show_menu_bar', '')) > 0 ? 1 : 0,
							'protected' => strlen($request->variable($cat['category_id'] . '_protected', '')) > 0 ? 1 : 0,
						);
						$cmbb->store_category($category_data);

						// Make sure that the article matches the category name
						$article_update = array(
							'article_id' => $cat['std_parent'],
							'title' => $category_name
						);
						$cmbb->store_article($article_update);
					}
				}
			}
			trigger_error($user->lang('ACP_CMBB_SETTING_SAVED') . adm_back_link($this->u_action));
		}
		else if ($request->variable('action', '') == 'delete')
		{
			$children = $cmbb->get_children($request->variable('std_parent', 0));
			$category_id = $request->variable('category_id', 0);
			if ($children !== false)
			{
				trigger_error($user->lang('ERROR_CATEGORY_NOT_EMPTY') . adm_back_link($this->u_action), E_USER_WARNING);
			}
			if ($cmbb->delete_category($category_id))
			{
				trigger_error($user->lang('ACP_CMBB_SETTING_SAVED') . adm_back_link($this->u_action));
			}
			trigger_error($user->lang('ERROR_FAILED_DELETE') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		// List current
		if (!empty($categories))
		{
			foreach ($categories as $cat)
			{
				// Exclude homepage
				if ($cat['category_id'] > 1)
				{
					// Check for children
					$children = $cmbb->get_children($cat['std_parent']);

					$template->assign_block_vars('categories', array(
						'ID'				 => $cat['category_id'],
						'NAME'				 => $cat['category_name'],
						'S_SHOW_MENU_BAR'	 => $cat['show_menu_bar'],
						'S_PROTECTED'		 => $cat['protected'],
						'CHILDREN'			 => ($children === false) ? 0 : count($children),
						'U_DELETE'			 => ($children === false) ? $this->u_action . "&amp;action=delete&amp;category_id=" . $cat['category_id'] . "&amp;std_parent=" . $cat['std_parent'] : false,
						'S_REACT_OPTIONS'	 => make_forum_select($cat['react_forum_id'], false, false, false, false),
					));
				}
			}
		}

		$template->assign_vars(array(
			'U_ADD_ACTION' => $this->u_action . "&amp;action=add",
		));
	}

	/**
	 * Check if provided category_name is unique
	 * @param array $categories
	 * @param string $check_name
	 * @param int $id
	 */
	private function category_name_unique($categories, $check_name, $id = null)
	{

		if (strlen($check_name) < 2)
		{
			return false;
		}
		foreach ($categories as $cat)
		{
			if (($check_name == $cat['category_name']) && ($id != $cat['category_id']) )
			{
				return false;
			}
		}
		return true;
	}
}
