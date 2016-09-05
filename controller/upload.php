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

class upload
{
	/* @var \phpbb\config\config */

	protected $config;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\request\request_interface */
	protected $request;

	protected $filesystem;
	/** @var \phpbb\files\factory */
	protected $files_factory;

	protected $phpbb_root_path;

	/* @var \ger\cmbb\cmbb\driver */
	protected $cmbb;
	protected $allowed = array('jpg', 'jpeg', 'gif', 'png');

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\controller\helper	$helper
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\user				$user
	 * @param \phpbb\files\factory								$files_factory		File classes factory
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, \phpbb\filesystem\filesystem_interface $filesystem, \phpbb\files\factory $files_factory, $phpbb_root_path, \ger\cmbb\cmbb\driver $cmbb)
	{
		$this->config = $config;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->cmbb = $cmbb;
	}

	/**
	 * Controller for route /upload
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle()
	{

		if ($this->cmbb->can_edit($this->auth))
		{



		/** @var \phpbb\files\upload $upload */
		$upload = $this->files_factory->get('upload')
			->set_error_prefix('CMBB_UPLOAD')
			->set_allowed_extensions($this->allowed)
			->set_max_filesize($this->config['dir_banner_filesize'])
			->set_disallowed_content((isset($this->config['mime_triggers']) ? explode('|', $this->config['mime_triggers']) : false));
		$file = $upload->handle_upload('files.types.remote', $banner);
		$prefix = unique_id() . '_';
		$file->clean_filename('real', $prefix);
		if (sizeof($file->error))
		{
			$file->remove();
			$error = array_merge($error, $file->error);
			return false;
		}
		$destination = $this->dir_helper->get_banner_path();
		// Move file and overwrite any existing image
		$file->move_file($destination, true);
		return strtolower($file->get('realname'));





			// Determine upload dir, create if needed
			$movedir = 'assets/user_upload/' . $user->data['user_id'];
			if (!is_dir($movedir))
			{
				$test = mkdir($movedir, 0755);
			}
			$movedir .= '/';

			// Now go
			if (isset($request->file('upl')['name']))
			{
				$extension = pathinfo($request->file('upl')['name'], PATHINFO_EXTENSION);

				if (!in_array(strtolower($extension), $allowed))
				{
					echo '{"status":"error1"}';
					exit;
				}
				if ($request->file('upl')['size'] > MAX_UPLOAD_SIZE)
				{
					echo '{"status":"error2"}';
					exit;
				}
				if (move_uploaded_file($request->file('upl')['tmp_name'], $movedir . $request->file('upl')['name']))
				{
					echo '{"status":"success"}';
					exit;
				}
			}
		}
		else
		{
			$response = array('status' => 'error3');
		}
		return new \Symfony\Component\HttpFoundation\JsonResponse($response);
	}

}

// EoF
