<?php
if (!defined('IN_CMBB'))
{
    trigger_error($user->lang('MODULE_NOT_ACCESS'));
}

// Some checks before we do anything
if (! (($auth->acl_get('m_') ) || ($auth->acl_get('a_'))) )
{
	trigger_error($user->lang('NOT_AUTHORISED'));
}

// List the config items we can edit
if ($auth->acl_get('a_') )
{
	$access = '"a", "m"';
}
else
{
	$access = '"m"';
}
if (! $items = $cmbb->get_config_items($access))
{
	trigger_error($user->lang('NOT_AUTHORISED'));
}

if ($request->is_set('submit'))
{
	// Validate, keep only the ones we have access to
	foreach($items as $row)
	{
		$config_value = $request->variable($row['config_id'], '');
		$config_value = empty($config_value) ? ( ($row['form'] == 'checkbox') ? 0 : '') : $config_value;
		$store = array(
			'config_id' => $row['config_id'],
			'config_value' => $config_value,
		);
		$cmbb->store_config($store);
		add_log('admin', 'cms config aangepast',  $row['config_name'] . ' - ' . $store['config_value'] );
	}
	header('Refresh: 1; URL=index');
	echo 'Saving...';
}
else
{
    page_header('CMBB Config');

    $form = '<script src="assets/ckeditor/ckeditor.js"></script>'
        . '<form class="edit_form install" method="post" action="index.php?m=a">';
    foreach($items as $row)
    {

            $form .= '<p><label for="' . $row['config_id'] . '">' . $row['config_explain'] . '</label>';
            switch ($row['form'])
            {
                    case 'text':
                            $form .= '<input name="' . $row['config_id'] . '" type="text" value= "' . $row['config_value'] . '" /></p>' . "\n";
                            break;
                    case 'textarea':
                            $form .= '</p><textarea class="content" name="' . $row['config_id'] . '">' . $row['config_value'] . '</textarea><br />'  . "\n";
                            break;
                    case 'textarea ckeditor':
                            $form .= '</p><textarea class="content ckeditor" name="' . $row['config_id'] . '">' . $row['config_value'] . '</textarea><br />'  . "\n";
                            break;
                    case 'checkbox':
                            $form .= '<input name="' . $row['config_id'] . '" type="checkbox" value= "1" ' . (empty($row['config_value'])? '' : 'checked') .' /></p>' . "\n";
                            break;
            }

    }
    $form.= '<button value="true" class="cancelbutton" type="button" name="button" onclick = "window.location = document.referrer;">Annuleren</button>
                    <input type="submit" name= "submit" value="Opslaan" />
                    </form>';
    $form.= '<script>
                    CKEDITOR.replace(".ckeditor");
            </script>';

    $template->assign_vars(array(
        'CMBB_BREADCRUMBS'          => '',
        'CMBB_MENU'                 => '',
        'CMBB_CATEGORY_NAME'        => 'CMBB Config',
        'CMBB_TITLE'                => 'CMBB Config',
        'CMBB_CONTENT'              => $form,
        'CMBB_LEFTBAR'              => '',
    ));

    $template->set_filenames(array(
        'body' => 'cmbb/base.html')
    );

    page_footer();
}
