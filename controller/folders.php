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
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\user				$user
	 * @param \phpbb\request\request_interface $request
	 * @param $phpbb_root_path
	 * @param $php_ext
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\user $user, \phpbb\request\request_interface $request, $phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->user = $user;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
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
//			$file_root = $this->request->server('DOCUMENT_ROOT') . str_replace('app.' . $this->php_ext, 'images/cmbb_upload/', $this->request->server('SCRIPT_NAME'));
			$file_root = $this->phpbb_root_path . 'images/cmbb_upload/';
			$url_root = generate_board_url() . '/images/cmbb_upload/';
			$structure = $this->listfolders($file_root, $url_root, $user_id);
		}
		return new \Symfony\Component\HttpFoundation\JsonResponse($structure);
	}

	/**
	 * List content in directory but skip subfolders
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
				if (!is_dir($dir . '/' . $folder))
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