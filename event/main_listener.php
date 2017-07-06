<?php

/**
 *
 * cmBB
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'					=> 'load_language_on_setup',
			'core.page_header'					=> 'page_header_add_menu',
			'core.permissions'					=> 'set_permissions',
			'core.viewtopic_modify_post_row'	=> 'add_postrow_search_link',
			'core.memberlist_view_profile'		=> 'add_memberlist_search_link',
		);
	}

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \ger\cmbb\cmbb\driver */
	protected $cmbb;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config		Config object
	 * @param \phpbb\controller\helper	$helper		Controller helper object
	 * @param \phpbb\template\template	$template	Template object
	 * @param \ger\cmbb\cmbb\driver		$cmbb		cmBB driver object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \ger\cmbb\cmbb\driver $cmbb)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->cmbb = $cmbb;
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name'	 => 'ger/cmbb',
			'lang_set'	 => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add menubar
	 */
	public function page_header_add_menu($event)
	{
		if ($this->config['ger_cmbb_show_menubar'] == 1)
		{
			$items = $this->cmbb->list_menu_items();
			$menu = '';
			if ($items)
			{
				foreach ($items as $row)
				{
					if ($this->cmbb->get_children($row['article_id']))
					{
						$menu.= '<li><a href="' . $this->helper->route('ger_cmbb_article', array('alias' => $row['alias'])) . '">' . $row['category_name'] . '</a></li>' . "\n";
					}
				}
			}
			$this->template->assign_vars(array(
				'CMBB_MENU' => $menu
			));
		}
	}

	public function set_permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions['u_cmbb_post_article'] = array('lang' => 'ACL_U_CMBB_POST_ARTICLE', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}
	
	/**
	 * Add search link to miniprofile
	 */
	public function add_postrow_search_link($event)
	{
		$count = $this->cmbb->get_user_articles($event['poster_id'], true);
		$postrow = $event['post_row'];
		$postrow['POSTER_ARTICLES'] = empty($count) ? '0' : '<a href="' . $this->helper->route('ger_cmbb_article_search_user', array('param' => $event['poster_id'])) . ' ">' . $count . '</a>';
		$event['post_row'] = $postrow;
	}
	
	/**
	 * Add search link to miniprofile
	 */
	public function add_memberlist_search_link($event)
	{
		$count = $this->cmbb->get_user_articles($event['member']['user_id'], true);
		$this->template->assign_vars(array(
				'U_ARTICLES_SEARCH' => $this->helper->route('ger_cmbb_article_search_user', array('param' => $event['member']['user_id'])),
				'ARTICLES_TOTAL' => $count
			));
	}

}
