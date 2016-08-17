<?php
/*
 * Edit CMS page
 */
if (!defined('IN_CMBB')) {
    trigger_error($user->lang('MODULE_NOT_ACCESS'));
}
// General allow check
if ((!$auth->acl_get('m_')) && (( ($user->data['user_posts'] < MIN_POST_COUNT) && ($cmbb->has_written($user->data['user_id']) == 0) ) || ($user->data['is_bot'] == TRUE) || (phpbb_is_banned($user->data['user_id'])) )) {
    trigger_error($user->lang('NOT_AUTHORISED'));
}

// New or existing?
if ($mode === 'e') {
    // Edit existing page, so get the data
    $page = $cmbb->get_article($request->variable('p', ''));
    if ($page === FALSE) {
        trigger_error($user->lang('FORM_INVALID'));
    }
    // Check if user is allowed to edit
    if (!(($user->data['user_id'] == $page['user_id']) || $auth->acl_get('m_') )) {
        trigger_error($user->lang('NOT_AUTHORISED'));
    }
}
else if ($mode !== 'n') {
    trigger_error($user->lang('NOT_AUTHORISED'));
}

// Output the page
page_header((empty($page['title']) ? $user->lang('NEW') : $page['title'] ));

$template->assign_vars(array(
    'CMBB_BREADCRUMBS'          => '',
    'CMBB_MENU'                 => $cmbb->build_html_menu(),
    'CMBB_TITLE'                => (empty($page['title']) ? $user->lang('NEW_MESSAGE') : $page['title']),
    'CMBB_CONTENT'              => (empty($page['content']) ? '' : $page['content'] ),
    'CMBB_LEFTBAR'              => '',
    'U_FORM_ACTION'             => 'index.php?m=s&p=' . (empty($page['article_id']) ? '_new_' : $page['article_id'] ),
    'CMBB_CATEGORY_DROPDOWN'    => form_dropdown('category_id', $cmbb->get_categories(), (empty($page['category_id']) ? 0 : $page['category_id'])),
    'CMBB_IMG_DIR'              => CMBB_ROOT . '/folders.php?u='.$user->data['user_id'],
));

$template->set_filenames(array(
    'body' => 'cmbb/article_form.html')
);

page_footer();

// EoF