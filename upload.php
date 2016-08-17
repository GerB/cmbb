<?php

// Get into phpBB for user
define('IN_CMBB', TRUE);

include('phpbb_functions.php');
include('constants.php');
$allowed = array('jpg', 'jpeg', 'gif', 'png');

if ( ($auth->acl_get('m_')) || (( ($user->data['user_posts'] >= MIN_POST_COUNT) || $cmbb->has_written($user->data['user_id']) ) && ($user->data['is_bot'] == FALSE) && (!phpbb_is_banned($user->data['user_id'])) ))
{
	// Determine upload dir, create if needed
	$movedir = 'assets/user_upload/' . $user->data['user_id'];
	if (!is_dir($movedir))
	{
		$test = mkdir($movedir, 0755, TRUE);
	}
	$movedir .= '/';

	// Now go
	if(isset($request->file('upl')['name'])) 
	{
		$extension = pathinfo($request->file('upl')['name'], PATHINFO_EXTENSION);

		if(!in_array(strtolower($extension), $allowed))
		{
			echo '{"status":"error1"}';
			exit;
		}
		if ($request->file('upl')['size'] > MAX_UPLOAD_SIZE)
		{
			echo '{"status":"error2"}';
			exit;			
		}
		if(move_uploaded_file($request->file('upl')['tmp_name'], $movedir . $request->file('upl')['name']))
		{
			echo '{"status":"success"}';
			exit;
		}
	}
}
echo '{"status":"error3"}';
exit;


// EoF