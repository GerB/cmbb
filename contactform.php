<?php

if (!defined('IN_CMBB'))
{
	trigger_error($user->lang('MODULE_NOT_ACCESS'));
}
// Load extra language file
$user->add_lang('memberlist');
if ($request->is_set('submit'))
{
	// Some checks first
	if (strlen($request->variable('message', '')) < 10)
	{
		trigger_error('Te weinig tekens ingevoerd als bericht.');
	}
	if(! $user->data['is_registered']) 
	{
		if (filter_var($request->variable('digipost', ''), FILTER_VALIDATE_EMAIL) === false)
		{
			trigger_error($user->lang('EMAIL_INVALID_EMAIL'));
		}
		if (strlen($request->variable('name', '')) < 2)
		{
			trigger_error($user->lang('TOO_SHORT_USERNAME'));
		}
		
		$email = $request->variable('digipost', '');
		$name = $request->variable('name', '');
		
	}
	else
	{
		$email = $user->data['user_email'];
		$name = $user->data['username'];
	}

	// Prep topic
	$subject = $user->lang('CONTACT_US') . ': ' . $request->variable('subject', '');
	$message = '[b]' . $user->lang('USERNAME') . ':[/b]' . $name . '
	[b]E-' . $user->lang('EMAIL') . ':[/b]' . $email . '
	[b]' . $user->lang('SUBJECT') . ':[/b]' . $request->variable('about', '') . '
	
	[b]' . $user->lang('MESSAGE') . ':[/b]
	[quote]' . $request->variable('message', '') . '[/quote]';
	
	// Insert topic in moderator forum
	if (phpbb_create_contact_topic($name, $subject, $message))
	{
		trigger_error($user->lang('EMAIL_SENT'));
	}
	else
	{
		trigger_error($user->lang('ERROR'));
	}
	
	
}
else
{
    $personfields = '';
    // Fetch some extra fields if user is not logged in
    if(! $user->data['is_registered'])
    {
            $personfields.= '<p><label for="name">' . $user->lang('SENDER_NAME') . ':</label> <input name="name" type="text"></p>';
            $personfields.= '<p><label for="digipost">' . $user->lang('SENDER_EMAIL_ADDRESS') . ':</label> <input name="digipost" type="text"></p>';
    }

    // Build breadcrumb path
    $path = '<div class="bread"><a href="' . CMBB_ROOT . '">' . (($config['site_home_text'] !== '') ? $config['site_home_text'] : $user->lang('HOME')) . '</a> &raquo; ' . $user->lang('CONTACT_US') . '</div>';

    // Left bar contains forum related stuff, fetch it here
    // We might have come from view. Force mode
    $mode = 'c';
    include('sidebar.php');

    page_header($user->lang('CONTACT_US'));

    $template->assign_vars(array(
        'CMBB_BREADCRUMBS'          => $path,
        'CMBB_MENU'                 => $cmbb->build_html_menu(),
        'CMBB_CATEGORY_NAME'        => $user->lang('CONTACT_US'),
        'CMBB_TITLE'                => $user->lang('CONTACT_US'),
        'CMBB_PERSONFIELDS'         => $personfields,
        'CMBB_LEFTBAR'              => str_replace('{CMBB_ALIAS}', 'contact', $cmbb_sidebar),
    ));

    $template->set_filenames(array(
        'body' => 'cmbb/contact.html')
    );

    page_footer();
}