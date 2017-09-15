<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {
    $events_objs = Events::get_data('isActive =1 AND (isPublic =1 OR createdBy =' . intval($core->user['uid']) . ')', array('returnarray' => true, 'order' => 'fromTime DESC'));
    if (is_array($events_objs)) {
        foreach ($events_objs as $events_obj) {
            $event = $events_obj->get();
            $event_link = $events_obj->parse_link();
            $fromtime = $events_obj->get_fromtime();
            $totime = $events_obj->get_totime();

            $from_output = date($core->settings['dateformat'] . ' ' . $core->settings['timeformate'], $fromtime);
            $to_output = date($core->settings['dateformat'] . ' ' . $core->settings['timeformate'], $totime);
            if ($events_obj->is_subscribed($core->user['uid'])) {
                $subscribe_cell = 'data-sort="1"';
                $subscribed = '<span style="color:green;font-weight:bold">Yes <span class="glyphicon glyphicon-ok"></span></span>';
            }
            else {
                $subscribe_cell = 'data-sort="0"';
                $subscribed = '<span style="color:red;font-weight:bold"">No <span class="glyphicon glyphicon-remove"></span></span>';
            }

            if ($events_obj->canManageEvent()) {
                $managelink = '<button type="button" class="btn btn-warning" onclick="window.open(\'' . $events_obj->get_editlink() . '\', \'_blank\')">' . $lang->manage . '</button>';
            }
            eval("\$tools = \"" . $template->get('tools_buttonselectlist') . "\";");
            eval("\$courses_list .= \"" . $template->get('events_eventslist_eventrow') . "\";");
            unset($tool_items, $subscribe_cell);
        }
    }
    eval("\$page= \"" . $template->get('events_eventslist') . "\";");
    output_page($page);
}
else {
    if ($core->input['action'] == 'loadevents_popup') {
        if (!intval($core->input['id'])) {
            echo ('<span style="color:red">Error Loading Window</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $event_obj = new Events($id);
        echo ($event_obj->parse_popup('events_modal'));
    }
    elseif ($core->input['action'] == 'events_subscribe') {
        if (!$core->input['id']) {
            echo('<span style="color:red">' . $lang->error . '</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $event_obj = new Events($id);
        $event_obj->do_assignuser();

        //parse course take/remove button
        $addorremovecourse_button = $event_obj->parse_addremove_button();

        echo($addorremovecourse_button);
        exit;
    }
    elseif ($core->input['action'] == 'events_remove') {
        if (!$core->input['id']) {
            echo('<span style="color:red">' . $lang->error . '</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $event_obj = new Events($id);
        $event_obj->do_removeuser();

        //parse course take/remove button
        $addorremovecourse_button = $event_obj->parse_addremove_button();
        echo($addorremovecourse_button);
        exit;
    }
}