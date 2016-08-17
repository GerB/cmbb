<?php
if (!defined('IN_CMBB')) {
    trigger_error($user->lang('MODULE_NOT_ACCESS'));
}
if (!$auth->acl_get('a_')) {
    trigger_error($user->lang('NOT_AUTHORISED'));
}
if (defined('CMBB_INSTALLED')) {
    exit('Please delete this install file');
}

$stage = $request->variable('stage', 'form');

if ($stage == 'form') {
    page_header('CMBB Installer');

    $install_form = '<form class="edit_form install" method="post" action="index.php?m=install&stage=process">
            <p><label for="react_forum_id">Forum id for article topics</label><input name="react_forum_id" type="number" value="2" /></p>
            <p><label for="contact_forum_id">Forum id for contact forms</label><input name="contact_forum_id" type="number" value="2" /></p>
            <p><label for="number_index_items">Max number of items to show on homepage</label><input name="number_index_items" type="number" value="10" /></p>
            <p><label for="min_post_count">Minimal number of posts to create an article</label><input name="min_post_count" type="number" value="10" /></p>
            <p><label for="min_title_length">Required minimal characters for article title</label><input name="min_title_length" type="number" value="4" /></p>
            <p><label for="min_content_length">Required minimal characters for article content</label><input name="min_content_length" type="number" value="20" /></p>
            <p><label for="max_upload_size">Max size for uploaded images [default vaue is 2MB]</label><input name="max_upload_size" type="number" value="2097152" /></p>
            <fieldset class="submit-buttons">
                <input name="submit" class="button1" value="' . $user->lang('SUBMIT') . '" type="submit">
            </fieldset>
        </form>';

    $template->assign_vars(array(
        'CMBB_BREADCRUMBS'          => '',
        'CMBB_MENU'                 => '',
        'CMBB_CATEGORY_NAME'        => 'CMBB Installer',
        'CMBB_TITLE'                => 'CMBB Installer',
        'CMBB_CONTENT'              => $install_form,
        'CMBB_LEFTBAR'              => '',
    ));

    $template->set_filenames(array(
        'body' => 'cmbb/base.html')
    );

    page_footer();
}
else if ($stage == 'process') {
    // We need some constants that do not exist yet. Define here
    define('REACT_FORUM_ID', 1);
    define('CMBB_ROOT', str_replace($request->server('document_root'), '', dirname(__FILE__)));

    /*
     * Database schema
     */
    $schema = array(
        "cms_article" => "
            `article_id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL DEFAULT '',
            `alias` varchar(255) NOT NULL,
            `user_id` int(11) NOT NULL DEFAULT '0',
            `parent` int(11) NOT NULL DEFAULT '0',
            `is_cat` tinyint(1) NOT NULL DEFAULT '0',
            `template_id` tinyint(4) NOT NULL,
            `topic_id` int(11) NOT NULL DEFAULT '0',
            `category_id` tinyint(4) NOT NULL,
            `content` text NOT NULL,
            `visible` tinyint(1) NOT NULL DEFAULT '0',
            `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`article_id`),
            UNIQUE KEY `alias_UNIQUE` (`alias`)
          ",
        "cms_category" => "
            `category_id` tinyint(4) NOT NULL AUTO_INCREMENT,
            `category_name` varchar(45) NOT NULL DEFAULT '',
            `std_parent` int(11) NOT NULL DEFAULT '0',
            `access` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`category_id`)
          ",
        "cms_config" => "
            `config_id` int(11) NOT NULL AUTO_INCREMENT,
            `access` char(1) NOT NULL DEFAULT 'a',
            `config_name` varchar(70) NOT NULL,
            `config_explain` varchar(255) DEFAULT NULL,
            `config_value` text NOT NULL,
            `form` varchar(45) DEFAULT NULL,
            PRIMARY KEY (`config_id`)
      ",
        "cms_template" => "
            `template_id` tinyint(4) NOT NULL AUTO_INCREMENT,
            `filename` varchar(45) DEFAULT NULL,
            `template_name` varchar(45) NOT NULL,
            `description` varchar(255) DEFAULT NULL,
            `access` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`template_id`),
            UNIQUE KEY `template_name_UNIQUE` (`template_name`)
      ",
        );

    /*
     * Initial inserts
     */
    $inserts = array(
        "cms_article" => array(
            array(
                "title" => "Home",
                "alias" => "index",
                "user_id" => $user->data['user_id'],
                "is_cat" => 1,
                "template_id" => 1,
                "category_id" => 1,
                "content" => "",
                "visible" => 1,
                "topic_id" => 0,
            ),
            array(
                "title" => "Articles",
                "alias" => "articles",
                "user_id" => $user->data['user_id'],
                "parent" => 1,
                "is_cat" => 1,
                "template_id" => 1,
                "category_id" => 2,
                "content" => "",
                "visible" => 1,
                "topic_id" => 0,
            ),
        ),

        "cms_category"  => array(
            array(
                "category_name" => "News &amp; updates",
                "std_parent" => "1",
                "access" => "0",
            ),
            array(
                "category_name" => "Articles",
                "std_parent" => "2",
                "access" => "1",
            ),
        ),
        "cms_config" => array(
            array(
                "access" => "a",
                "config_name" => "site_disabled",
                "config_explain" => "Disable CMBB for maintenance",
                "config_value" => "0",
                "form" => "checkbox",
            ),
            array(
                "access" => "m",
                "config_name" => "announce_text",
                "config_explain" => "Announcement text to be displayed above articles",
                "config_value" => "<p>Hello World</p>",
                "form" => "textarea ckeditor",
            ),
            array(
                "access" => "m",
                "config_name" => "announce_show",
                "config_explain" => "Show announcement",
                "config_value" => "0",
                "form" => "checkbox",
            ),
        ),
        "cms_template" => array(
            array(
                "filename" => "index.html",
                "template_name" => "Index",
                "description" => "Listing article exertps",
                "access" => "0",
            ),
            array(
                "filename" => "article.html",
                "template_name" => "Article",
                "description" => "All basic articles can fit in this template",
                "access" => "0",
            ),
            array(
                "filename" => "base.html",
                "template_name" => "Base",
                "description" => "Basic template, nothing fancy",
                "access" => "0",
            ),
        ),
    );

    // Do the DB magic
    $cmbb->setup_db($schema);
    $cmbb->insert_db($inserts);

    // Create index page
    $article_data =  array (
        "title" => "Your first article",
        "alias" => "your-first-article",
        "user_id" => $user->data['user_id'],
        "parent" => "2",
        "template_id" => "2",
        "topic_id" => "1",
        "category_id" => "2",
        "content" => "This is a first article to get you started. CMBB is successfully installed. You may hide this article.",
        "visible" => "1",
    );

    $article_data['topic_id'] = phpbb_create_article_topic($article_data);
    $cmbb->store_article($article_data);


    // Now create the constants file
    $constants_content = "<?php\n";
    $constants_content.= "// CMBB auto-generated configuration file\n\n";

    $constants_content.= "define('CMBB_INSTALLED',        TRUE);\n";
    $constants_content.= "define('CMBB_ROOT',             '" . CMBB_ROOT . "');     // Root path for CMBB installation \n";
    $constants_content.= "define('REACT_FORUM_ID',        " . $request->variable('react_forum_id', 1) . "); // Forum ID for article topics \n";
    $constants_content.= "define('CONTACT_FORUM_ID',      " . $request->variable('contact_forum_id', 1) . "); // Forum ID for contact forms \n";
    $constants_content.= "define('NUMBER_INDEX_ITEMS',    " . $request->variable('number_index_items', 10) . "); // Number of items to show on the homepage \n";
    $constants_content.= "define('MIN_POST_COUNT',        " . $request->variable('min_post_count', 1) . "); // Minimal number of posts to create an article \n";
    $constants_content.= "define('MIN_TITLE_LENGTH',      " . $request->variable('min_title_length', 4) . "); // Required minimal characters for an arcticle title \n";
    $constants_content.= "define('MIN_CONTENT_LENGTH',    " . $request->variable('min_content_length', 20) . "); // Required minimal characters for article content \n";
    $constants_content.= "define('MAX_UPLOAD_SIZE',       " . $request->variable('max_upload_size', 2097152) . "); // Max size for uploaded images [2MB default] \n";

    file_put_contents ("constants.php", $constants_content);
    //chmod("constants.php", 644);

    // All done, redirect to index
    header('Location: index.php');
}
else
{
    trigger_error('No valid option');
}