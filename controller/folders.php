<?php

/**
 *
 * cmBB
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\controller;

class folders
{
	/* @var \phpbb\config\config */

	protected $config;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\request\request_interface */
	protected $request;
	protected $phpbb_root_path;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\controller\helper	$helper
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\user				$user
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\user $user, \phpbb\request\request_interface $request, $phpbb_root_path, \ger\cmbb\cmbb\driver $cmbb)
	{
		$this->config = $config;
		$this->user = $user;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		include($this->phpbb_root_path . '/ext/ger/cmbb/cmbb/presentation.php');
	}

	/**
	 * Controller for route /folders/{user_id}
	 *
	 * @param int		$user_id
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($user_id = 0)
	{
		if (empty($user_id))
		{
			$structure = '';
		}
		else
		{
			$file_root = $this->request->server('DOCUMENT_ROOT') . str_replace('app.php', 'images/cmbb_upload/', $this->request->server('SCRIPT_NAME'));
			$url_root = generate_board_url() . '/images/cmbb_upload/';
			$structure = $this->listfolders($file_root, $url_root, $user_id);
		}
		return new \Symfony\Component\HttpFoundation\JsonResponse($structure);
	}

	/**
	 * List folder in directory recursively
	 * @param string $dir
	 * @return array
	 */
	private function listfolders($file_root, $url_root, $user_id)
	{
		$dir = $file_root . $user_id;
		$dh = scandir($dir);
		$return = array();
		foreach ($dh as $folder)
		{
			if ($folder != '.' && $folder != '..' && $folder != 'index.html' && strtolower($folder) != 'thumbs.db')
			{
				if (is_dir($dir . '/' . $folder))
				{
					$subs = listfolders($dir . '/' . $folder);
					foreach ($subs as $sub)
					{
						$return[] = $sub;
					}
				}
				else
				{
					$dir = str_replace('//', '/', $dir);
					$return[] = array(
						'image'	 => $url_root . $user_id . '/' . $folder,
						'folder' => '',
					);
				}
			}
		}
		return $return;
	}

}
