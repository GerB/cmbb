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

	/* @var \ger\cmbb\cmbb\driver */
	protected $cmbb;

	/* array of allowed extensions */
	protected $allowed = array('jpg', 'jpeg', 'gif', 'png');

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\user				$user
	 * @param \phpbb\files\factory								$files_factory		File classes factory
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\request\request_interface $request, \phpbb\files\factory $factory, \ger\cmbb\cmbb\driver $cmbb)
	{
		$this->config = $config;
		$this->user = $user;
		$this->auth = $auth;
		$this->request = $request;
		$this->files_factory = $factory;
		$this->cmbb = $cmbb;
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
			$full_upload_dir = $this->request->server('DOCUMENT_ROOT') . str_replace('app.php', $user_upload_dir, $this->request->server('SCRIPT_NAME'));
			if (!is_dir($full_upload_dir))
			{
				$test = mkdir($full_upload_dir, 0755, true);
			}
			$full_upload_dir .= '/';

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
				$response = array('status' => 'error1');
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

// EoF
