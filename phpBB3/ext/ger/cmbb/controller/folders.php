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

namespace ger\cmbb\controller;

class folders
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\user */
	protected $user;

        /* @var \phpbb\auth\auth */
	protected $auth;

        /* @var \phpbb\request\request_interface */
	protected $request;

        
        protected $phpbb_root_path;

        /* @var \ger\cmbb\cmbb\driver */
	protected $cmbb;

	/**
	* Constructor
	*
	* @param \phpbb\config\config		$config
	* @param \phpbb\controller\helper	$helper
	* @param \phpbb\template\template	$template
	* @param \phpbb\user				$user
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, $phpbb_root_path, \ger\cmbb\cmbb\driver $cmbb)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->cmbb = $cmbb;

                include($this->phpbb_root_path . '/ext/ger/cmbb/cmbb/presentation.php');
                if (!defined('CMBB_ROOT')) {
                    define('CMBB_ROOT', '/cmbb');
                }
	}

	/**
	* Controller for route /folders/{user_id}
	*
	* @param int		$user_id
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle($user_id = 0)
	{
            if (empty($user_id)) {
                $structure = '';
            }
            else
            {
                $root = $this->request->server('DOCUMENT_ROOT') . str_replace('app.php', 'images/cmbb_upload/', $this->request->server('SCRIPT_NAME'));
                $dir       = $root . $user_id ;
                $structure = $this->listfolders($dir);
            }
            return new \Symfony\Component\HttpFoundation\JsonResponse($structure);
	}


        /**
         * List folder in directory recursively
         * @param string $dir
         * @return array
         */
        private function listfolders($dir)
        {
            $dh     = scandir($dir);
            $return = array();
            foreach ($dh as $folder)
            {
                if ($folder != '.' && $folder != '..' && $folder != 'index.html' && strtolower($folder) != 'thumbs.db') {
                    if (is_dir($dir.'/'.$folder)) {
                        $subs = listfolders($dir.'/'.$folder);
                        foreach ($subs as $sub)
                        {
                            $return[] = $sub;
                        }
                    }
                    else {
                        $dir = str_replace('//', '/', $dir);
                        $return[] = array(
                            'image' => CMBB_ROOT.'/'.$dir.'/'.$folder,
                            'folder' => '',
                        );
                    }
                }
            }
            return $return;
        }

}




