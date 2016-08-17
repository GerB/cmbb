<?php
/*
 * View CMS page
 */

if (!defined('IN_CMBB')) {
    trigger_error($user->lang('MODULE_NOT_ACCESS'));
}
// What page do we have?
$alias = $request->variable('q', 'index', true);

if ($alias == 'contact') {
    include ('contactform.php');
    exit();
}

// Load extra language file
$user->add_lang('viewtopic');

// Get general page data
$page = $cmbb->get_article($alias);
if ($page === FALSE) {
    trigger_error($user->lang('FILE_NOT_FOUND_404', $alias));
}

if ($page['visible'] == 0) {
    if ($auth->acl_get('m_')) {
        $page['content'] = '<div class="warning">Deze pagina is verborgen en daarom alleen zichtbaar voor teamleden.</div>'.$page['content'];
    }
    else {
        trigger_error($user->lang('FILE_NOT_FOUND_404', $alias));
    }
}


// List child pages exerpts as content when it's a category
if ($page['is_cat']) {
    $page['content'] = '';
    if ($page['alias'] == 'index') {
        if ($request->variable('showhidden', '') == 1) {
            if (!$auth->acl_get('m_')) {
                trigger_error('Je beschikt niet over de juiste permissies voor deze actie.');
            }
            $children = $cmbb->get_hidden();
        }
        else {
            $children = $cmbb->get_last(NUMBER_INDEX_ITEMS);
            if ($site_config['announce_show'] == 1) {
                $page['content'] = '<div class="box">'.htmlspecialchars_decode($site_config['announce_text']).'</div><hr>';
            }
        }
    }
    else {
        $children = $cmbb->get_children($page['article_id']);
    }

    $count = count($children);
    if (empty($children)) {
        $page['content'] = ' ';
    }
    else {
        $counter = 0;
        foreach ($children as $child)
        {
            $counter++;
            $page['content'] .= '<div class="box"><a href="'.$child['alias'].'"><h2>'.$child['title'].'</h2></a>';
            $page['content'] .= '<div><div class="exerpt_img"><a href="'.$child['alias'].'">'.phpbb_user_avatar($child['user_id']).'</a></div>';
            $page['content'] .= closetags(character_limiter(clean_html($child['content'])));
            $page['content'] .= ' <a href="'.$child['alias'].'">Lees verder&#8230;</a></div></div>';

            if ($counter < $count) {
                $page['content'] .= '<hr>';
            }
        }
    }
}

// Do breadcrumbs
if ($page['alias'] == 'index') {
    $path = '';
}
else {
    $parents[] = $cmbb->get_article($page['parent']);

    if ($parents[0]['parent'] != 0) {
        $parents[] = $cmbb->get_article($parents[0]['parent']);
        if ($parents[1]['parent'] != 0) {
            $parents[] = $cmbb->get_article($parents[1]['parent']);
            if ($parents[2]['parent'] != 0) {
                $parents[] = $cmbb->get_article($parents[0]['parent']);
                if ($parents[3]['parent'] != 0) {
                    $parents[] = $cmbb->get_article($parents[3]['parent']);
                }
            }
        }
    }
    $parents = array_reverse($parents);
    $path    = '<a href="' . CMBB_ROOT . '">' . (($config['site_home_text'] !== '') ? $config['site_home_text'] : $user->lang('HOME')) . '</a>  &raquo; ';
    foreach ($parents as $parent)
    {
        if ($parent['title'] != 'Home') {
            $path.= '<a href="'.$parent['alias'].'">'.$parent['title'].'</a>  &raquo; ';
        }
    }
    $path = '<div class="bread">'.$path.$page['title'].'</div>';
}

// Left bar contains forum related stuff, fetch it here
include('sidebar.php');

// Fetch general page items into template
$title = empty($page['title']) ? (($config['site_home_text'] !== '') ? $config['site_home_text'] : $user->lang('HOME')) : $page['title'];
page_header($page['title']);

$template->assign_vars(array(
    'CMBB_BREADCRUMBS'          => $path,
    'CMBB_MENU'                 => $cmbb->build_html_menu(),
    'CMBB_CATEGORY_NAME'        => $cmbb->fetch_category($page['category_id']),
    'CMBB_TITLE'                => $page['title'],
    'CMBB_CONTENT'              => $page['content'],
    'CMBB_LEFTBAR'              => str_replace('{CMBB_ALIAS}', $alias, $cmbb_sidebar),
    'CMBB_ARTICLE_TOPIC_ID'     => $page['topic_id'],
    'CMBB_AUTHOR'               => ($page['user_id'] > 0) ? phpbb_get_user($page['user_id']) : '',
));

$template->set_filenames(array(
    'body' => 'cmbb/' . $cmbb->get_template_content($page['template_id']))
);

page_footer();

// EoF