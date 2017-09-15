<?php

define('PASSEXPIRE_EXCLUDE', 1);
require_once './global.php';

if ($core->input['action']) {
    if ($core->input['action'] == 'do_login') {
        $session->name_phpsession(COOKIE_PREFIX . 'login');
        $session->start_phpsession();
        $session->regenerate_id_phpsession(true);
        /* Ensure that request is genuine, not a CSRF */
        $core->input['token'] = $core->sanitize_inputs($core->input['token']);
        /* 		if(!$session->is_validtoken()) {
          output_xml("<status>false</status><message></message>");
          $session->destroy_phpsession(true);
          exit;
          } */

        $login_details = array(
            'username' => $db->escape_string($core->input['username']),
            'password' => $db->escape_string($core->input['password'])
        );

        $validation = new ValidateAccount();
        $user_details = $validation->get_user_by_username($login_details['username']);
        unset($user_details['password'], $user_details['salt']);

        if ($validation->can_attemptlogin($login_details['username'])) {
            $validation = new ValidateAccount($login_details);

            if ($validation->get_validation_result()) {
                $user_data = $validation->get_userdetails();

                create_cookie('uid', $user_data['uid'], (TIME_NOW + (60 * $core->settings['idletime'])));
                create_cookie('loginKey', $user_data['loginKey'], (TIME_NOW + (60 * $core->settings['idletime'])));
                output_xml("<status>true</status><message>{$lang->loginsuccess}</message>");
            }
            else {
//                if ($validation->get_real_failed_attempts() >= 3) {
//                    $lang->tryresetpassword = '<![CDATA[<br />' . $lang->tryresetpassword . ']]>';
//                }
//                else {
//                    $lang->tryresetpassword = '';
//                }
                $log->record($core->input['username'], 0);
                output_xml("<status>false</status><message>{$lang->invalidlogin}{$lang->tryresetpassword}</message>");
            }
        }
        else {
            if ($validation->get_error_message()) {
                $fail_message = $validation->get_error_message();
            }
            else {
                $login_after = round($core->settings['failedlogintime'] - ((TIME_NOW - $user_details['lastAttemptTime']) / 60), 0);
                $fail_message = $lang->sprint($lang->reachedmaxattempts, $login_after);
            }

            output_xml("<status>false</status><message>{$fail_message}</message>");
        }
    }
    elseif ($core->input['action'] == 'do_logout') {
        $uid = $core->user['uid'];

        $db->update_query('users', array('lastVisit' => TIME_NOW), "uid='$uid'");

        $db->delete_query('sessions', "uid='$uid'");

        create_cookie('sid', '', (TIME_NOW - 3600));
        create_cookie('uid', '', (TIME_NOW - 3600));
        create_cookie('loginKey', '', (TIME_NOW - 3600));

        redirect('users.php?action=login');
    }
    elseif ($core->input['action'] == 'get_popup_loginbox') {
        eval("\$loginbox = \"" . $template->get('popup_loginbox') . "\";");
        echo $loginbox;
    }
    else {
        $session->name_phpsession(COOKIE_PREFIX . 'login');
        $session->start_phpsession();
        $session->regenerate_id_phpsession(true);
        $token = $session->generate_token();
        $session->set_phpsession(array('token' => $token));

        if (isset($core->input['referer']) && !empty($core->input['referer'])) {
            $lastpage = base64_decode($db->escape_string($core->input['referer']));
        }
        else {
            $lastpage = DOMAIN;
        }

        /* Get Help Video */
//        $helpvideo = HelpVideos::get_data(array('alias' => 'how-to-reset-password'));
//        $helplink = $helpvideo->parse_link();

        eval("\$loginpage = \"" . $template->get('loginpage') . "\";");
        output_page($loginpage);
    }
}
?>