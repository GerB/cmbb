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

	/** @var \phpbb\files\factory */
	protected $files_factory;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/* @var \ger\cmbb\cmbb\driver */
	protected $cmbb;

	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\user				$user
	 * @param \phpbb\files\factory								$files_factory		File classes factory
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, \phpbb\files\factory $factory, \phpbb\path_helper $path_helper, \ger\cmbb\cmbb\driver $cmbb, $php_ext)
	{
		$this->config = $config;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->files_factory = $factory;
		$this->path_helper = $path_helper;
		$this->cmbb = $cmbb;
		$this->php_ext = $php_ext;
	}

	/**
	 * Controller for route /upload
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse A Symfony Response object
	 */
	public function handle()
	{
		if ($this->cmbb->can_edit($this->auth))
		{
			// We have a separate folder for each user. Let's make sure we have it.
			$user_upload_dir = 'images/cmbb_upload/' . $this->user->data['user_id'];
			$full_upload_dir = $this->path_helper->get_phpbb_root_path() . $user_upload_dir;

			if (!is_dir($full_upload_dir))
			{
				mkdir($full_upload_dir, 0755, true);
			}

			$upload = $this->files_factory->get('upload')
					->set_disallowed_content((isset($this->config['mime_triggers']) ? explode('|', $this->config['mime_triggers']) : false))
					->set_allowed_extensions($this->cmbb->allowed_extensions)
					->set_max_filesize($this->config['max_filesize'])
					->set_error_prefix('CMBB_UPLOAD');

			// Uploading from a form, use form name
			$file = $upload->handle_upload('files.types.form', 'upl');
			$file->clean_filename('real');
			if (file_exists($user_upload_dir . '/' . $file->get('realname')))
			{
				for ($i = 1; $i < 10; $i++)
				{
					$file->clean_filename('real', '1_');
					if (!file_exists($user_upload_dir . '/' . $file->get('realname')))
					{
						$approved = true;
						break;
					}
				}
				if (!isset($approved))
				{
					// Add datetime as last resort
					$file->clean_filename('real', date('YmdHis'));
				}
			}

			$file->move_file($user_upload_dir);

			if (sizeof($file->error))
			{
				$file->remove();
				$response = array('status' => 'error1', $file->error);
			}
			else
			{
				$response = array('status' => 'success');
			}
		}
		else
		{
			$response = array('status' => 'error3');
		}
		return new \Symfony\Component\HttpFoundation\JsonResponse($response);
	}
}
