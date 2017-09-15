<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {

    //show create course button depending on user permission
    if ($core->usergroup['can_CreateCourse'] == 0) {
        $hide_createcoursebutton = ' style="display:none"';
    }
//get active courses list
    $courses_objs = Courses::get_data(array('isActive' => 1), array('returnarray' => true, 'simple' => false));
    if (is_array($courses_objs)) {
        foreach ($courses_objs as $course_obj) {
            $course = $course_obj->get();
            $course_link = $course_obj->parse_link();
            $teacher_objs = $course_obj->get_teachers();
            if (is_array($teacher_objs)) {
                $teachername = $course_obj->get_teacheroutput();
            }
            else {
                $teachername = 'N/A';
            }

            if ($course_obj->is_subscribed($core->user['uid'])) {
                $subscribe_cell = 'data-sort="1"';
                $subscribed = '<span style="color:green;font-weight:bold">Yes <span class="glyphicon glyphicon-ok"></span></span>';
            }
            else {
                $subscribe_cell = 'data-sort="0"';
                $subscribed = '<span style="color:red;font-weight:bold"">No <span class="glyphicon glyphicon-remove"></span></span>';
            }

            if ($course_obj->canManageCourse()) {
                $tool_items .= ' <li><a target="_blank" href="' . $course_obj->get_editlink() . '"><span class="glyphicon glyphicon-pencil"></span>&nbsp' . $lang->managecourse . '</a></li>';
            }
            $totalstudents = $course_obj->get_totalstudents();
            eval("\$courses_list .= \"" . $template->get('courses_courselist_courserow') . "\";");
            unset($tool_items, $subscribe_cell);
        }
    }
    eval("\$page= \"" . $template->get('courses_courselist') . "\";");
    output_page($page);
}
else {
    if ($core->input['action'] == 'loadcourses_popup') {
        if (!intval($core->input['id'])) {
            echo ('<span style="color:red">Error Loading Window</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $course_obj = new Courses($id);
        $course = $course_obj->get();
        $id = $course_obj->get_id();
        $hide_managecoursebutton = ' style="display:none"';
        $teacheroutput = $course_obj->get_teacheroutput();
        //show manage course button depending on user permission
        if ($course_obj->canManageCourse()) {
            $editlink = $course_obj->get_editlink();
            $hide_managecoursebutton = '';
        }
        $course_displayname = $course_obj->get_displayname();

        if (!$course['description']) {
            $hide_coursedescription = 'style="display:none"';
        }
        //parse course take/remove button
        if ($core->usergroup['canTakeLessons'] == 1) {
            if ($course_obj->is_subscribed($core->user['uid'])) {
                $addorremovecourse_button = '<div id="subscribedive_' . $id . '" ><button type="button" class="btn btn-danger" id="subscribebutton_' . $id . '_remove"><span class="glyphicon glyphicon-minus"></span>' . $lang->removecourse . '</button></div>';
            }
            else {
                $addorremovecourse_button = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button></div>';
            }
        }

        //parse course lectures based on user permission
        $lecture_section = $course_obj->get_lectureoutput();
        //parse folder link
        if ($course_obj->folderUrl) {
            $course_folder = '<button type="button" class="btn btn-warning"  onclick="window.open(\'' . $course_obj->folderUrl . '\', \'_blank\')">' . $lang->coursefiles . '</button>';
        }
        eval("\$modal= \"" . $template->get('courses_courseprofile') . "\";");

        echo ($modal);
    }
    else if ($core->input['action'] == 'course_subscribe') {
        if (!$core->input['id']) {
            echo('<span style="color:red">' . $lang->error . '</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $course_obj = new Courses($id);
        $assignedcourse_data = array('cid' => $id, 'uid' => $core->user['uid']);
        $assignedcourse_obj = new AssignedCourses();
        $assignedcourse_obj->set($assignedcourse_data);
        $assignedcourse_obj->save();
        if ($assignedcourse_obj->get_errorcode() == 0) {
            $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-danger" id="subscribebutton_' . $id . '_remove"><span class="glyphicon glyphicon-minus"></span>' . $lang->removecourse . '</button>';
        }
        else {
            $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button>';
        }
        echo($output);
        exit;
    }
    elseif ($core->input['action'] == 'course_remove') {
        if (!$core->input['id']) {
            echo('<span style="color:red">' . $lang->error . '</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $course_obj = new Courses($id);
        $assignedcourses = AssignedCourses::get_data(array('uid' => $core->user['uid'], 'cid' => $id), array('returnarray' => true));
        if (is_array($assignedcourses)) {
            foreach ($assignedcourses as $assignedcourse) {
                $assignedcourse->delete();
            }
        }
        $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button></div>';
        echo($output);
        exit;
    }
    elseif ($core->input['action'] == 'get_managelecturedeadlines') {
        $title = $lang->addlecturedeadline;
        if ($core->input['id'] && $core->input['id'] != 'new') {
            $hidetype = 'style="display:none"';
            if ($core->input['type'] == 'lecture') {
                $title = $lang->managelecture;
                $lecture_selected = 'selected';
                $lecture_obj = new Lectures(intval($core->input['id']));
                $event = $lecture_obj->get();
                $event['fromdateoutput'] = $lecture_obj->get_fromdateoutput();
                $event['todateoutput'] = $lecture_obj->get_todateoutput();
                $event['fromtimeoutput'] = $lecture_obj->get_fromtimeoutput();
                $event['totimeoutput'] = $lecture_obj->get_totimeoutput();
            }
            else if ($core->input['type'] == 'deadline') {
                $title = $lang->managedeadline;
                $deadline_selected = 'selected="selected"';
                $deadline_obj = new Deadlines(intval($core->input['id']));
                $event = $deadline_obj->get();
                $event['fromdateoutput'] = $deadline_obj->get_fromdateoutput();
                $event['fromtimeoutput'] = $deadline_obj->get_fromtimeoutput();
            }
        }
        if (!$event['inputChecksum']) {
            $event['inputChecksum'] = generate_checksum();
        }
        $cid = $core->input['courseid'];
        $isactive_list = parse_selectlist2('event[isActive]', 1, array(1 => $lang->yes, 0 => $lang->no), $isactive);
        eval("\$modal= \"" . $template->get('modal_courses_manage_lecturedeadline') . "\";");
        echo ($modal);
    }
    elseif ($core->input['action'] == 'save_lecturedeadline') {
        $eventdadta = $core->input['event'];
        if (!is_empty($eventdadta['fromTime'], $eventdadta['fromDate'])) {
            $eventdadta['fromTime'] = strtotime($eventdadta['fromDate'] . ' ' . $eventdadta['fromTime']);
            unset($eventdadta['fromDate']);
        }
        else {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        if ($core->input['type'] == 'lecture') {
            $managed_obj = new Lectures();
            if (!is_empty($eventdadta['toTime'], $eventdadta['toDate'])) {
                $eventdadta['toTime'] = strtotime($eventdadta['toDate'] . ' ' . $eventdadta['toTime']);
                unset($eventdadta['toDate']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                exit;
            }
        }
        elseif ($core->input['type'] == 'deadline') {
            $managed_obj = new Deadlines();
            $eventdadta['time'] = $eventdadta['fromTime'];
            unset($eventdadta['fromTime'], $eventdadta['location']);
        }
        $managed_obj->set($eventdadta);
        $managed_obj->save();

        switch ($managed_obj->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            default:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
}