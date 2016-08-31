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
			'core.user_setup'				=> 'load_language_on_setup',
			'core.page_header'				=> 'page_header_add_menu',
		);
	}

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

        /* @var \ger\cmbb\cmbb\driver */
        protected $cmbb;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper		Controller helper object
	* @param \phpbb\template\template	$template	Template object
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template, \ger\cmbb\cmbb\driver $cmbb)
	{
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
			'ext_name' => 'ger/cmbb',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

        public function page_header_add_menu($event)
        {
            $items = $this->cmbb->list_menu_items();
            $menu = '';
            if ($items)
            {
                foreach($items as $row)
                {
                    $menu.= '<li><a href="'. $this->helper->route('ger_cmbb_page', array('alias' => $row['alias'])).'">'.$row['category_name'].'</a></li>'."\n";
                }
            }
            $this->template->assign_vars(array(
			'CMBB_MENU'	=> $menu
               ));
        }


}
