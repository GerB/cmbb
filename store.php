<?php
/*
 * Store CMS page
 */
//var_dump($request->variable('content', '', true));
if (!defined('IN_CMBB'))
{
	trigger_error($user->lang('MODULE_NOT_ACCESS'));
}

// Some checks before we do anything
if ( (!$auth->acl_get('m_'))  && (( ($user->data['user_posts'] < MIN_POST_COUNT) && ($cmbb->has_written($user->data['user_id']) == 0)  ) || ($user->data['is_bot'] == TRUE) || (phpbb_is_banned($user->data['user_id'])) ))
{
	trigger_error($user->lang('NOT_AUTHORISED'));
}
if (strlen(trim($request->variable('content', ''))) < MIN_CONTENT_LENGTH)
{
	trigger_error('Je moet minimaal ' . MIN_CONTENT_LENGTH . ' geldige tekens invoeren als artikeltekst; je hebt er ' . strlen(trim($request->variable('content', '', true))) . ' ingevoerd.' );
}
if (strlen(trim($request->variable('title', ''))) < MIN_TITLE_LENGTH)
{
	trigger_error('Je moet minimaal ' . MIN_TITLE_LENGTH . ' geldige tekens invoeren als titel');
}

$article_id = $request->variable('p', '');

if (is_numeric($article_id))
{
	// Check old page info
	$oldpage = $cmbb->get_article($article_id);

	// Check if user is allowed to edit
	if (! (($user->data['user_id'] == $oldpage['user_id']) || $auth->acl_get('m_') ) )
	{
		trigger_error($user->lang('NOT_AUTHORISED'));
	}
	if (empty($oldpage['user_id']) && (!$auth->acl_get('a_')) )
	{
		// Special page, admin only
		trigger_error($user->lang('NOT_AUTHORISED'));
	}
	if (!$title = phpbb_censor_title($request->variable('title', '', true)))
	{
		trigger_error($user->lang('NOT_AUTHORISED'));
	}
	
	// Compare old article content size with new post content size
	$oldsize = strlen($oldpage['content']);
	$newsize = strlen(censor_text($request->variable('content', '', true)));
	
	if ( ($newsize / $oldsize) < 0.7 )
	{
		trigger_error('Je hebt wel heel veel verwijderd. Weet je zeker dat je niets fout hebt gedaan?');
	}
	
	$article_data = array (
		'article_id' => $article_id,
		'title' => $title,
		'parent' => $cmbb->get_std_parent($request->variable('category_id', '1')),
		'category_id' => $request->variable('category_id', '1'),
		'content' => censor_text(htmlspecialchars_decode($request->variable('content', ''),ENT_COMPAT)),
	);
	$redirect = $oldpage['alias'];
	
}
else if ($article_id == '_new_')
{
	if (!$title = phpbb_censor_title($request->variable('title', '', true)))
	{
		trigger_error('Ongeldige titel opgegeven.');	
	}

	$article_data = array (
		'title' => $title,
		'alias' => $cmbb->generate_page_alias($request->variable('title', '', true)),
		'user_id' => $user->data['user_id'],
		'parent' => $cmbb->get_std_parent($request->variable('category_id', '')),
		'is_cat' => 0,
		'template_id' => 1,
		'category_id' => $request->variable('category_id', ''),
		'content' => htmlspecialchars_decode($request->variable('content', '', true), ENT_COMPAT),
		'visible' => 1,
		'datetime' => date('Y-m-d H:i:s'),
	);
	$article_data['topic_id'] = phpbb_create_article_topic($article_data);	
	
	$redirect = $article_data['alias'];
}
else
{
	trigger_error('Je probeert nu iets wat echt niet kan.');
}

$store = $cmbb->store_article($article_data);

// redirect to article
header('Location: ' . CMBB_ROOT . '/' . $redirect);