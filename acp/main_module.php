<?php

/**
 *
 * cmBB
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace ger\cmbb\acp;

class main_module
{

    var $u_action;

    public function main($id, $mode)
    {
        global $config, $request, $template, $user;

        $user->add_lang_ext('ger/cmbb', 'common');
        $this->tpl_name = 'acp_cmbb_body';
        $this->page_title = $user->lang('ACP_CMBB_TITLE');
        add_form_key('ger/cmbb');

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('ger/cmbb'))
            {
                trigger_error('FORM_INVALID');
            }

            // Store values.
            $config->set('ger_cmbb_react_forum_id', $request->variable('react_forum_id', 0));
            $config->set('ger_cmbb_number_index_items', $request->variable('number_index_items', 0));
            $config->set('ger_cmbb_min_post_count', $request->variable('min_post_count', 0));
            $config->set('ger_cmbb_min_title_length', $request->variable('min_title_length', 0));
            $config->set('ger_cmbb_min_content_length', $request->variable('min_content_length', 0));
            $config->set('ger_cmbb_announce_text', $request->variable('announce_text', ''));
            $config->set('ger_cmbb_announce_show', $request->variable('announce_show', 0));

            trigger_error($user->lang('ACP_CMBB_SETTING_SAVED') . adm_back_link($this->u_action));
        }

        $template->assign_vars(array(
            'U_ACTION' => $this->u_action,
            'S_REACT_FORUM_ID'   => $config['ger_cmbb_react_forum_id'],
            'NUMBER_INDEX_ITEMS' => $config['ger_cmbb_number_index_items'],
            'MIN_POST_COUNT'     => $config['ger_cmbb_min_post_count'],
            'MIN_TITLE_LENGTH'   => $config['ger_cmbb_min_title_length'],
            'MIN_CONTENT_LENGTH' => $config['ger_cmbb_min_content_length'],
            'ANNOUNCE_TEXT'      => $config['ger_cmbb_announce_text'],
            'S_ANNOUNCE_SHOW'    => $config['ger_cmbb_announce_show'],
            'S_REACT_OPTIONS' => make_forum_select($config['ger_cmbb_react_forum_id'], false, false, false, false),
        ));
    }

}
