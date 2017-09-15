<?php

class CreateAccount extends Accounts {

    private $user = array();

    public function __construct($data) {
        if (is_array($data)) {
            $this->perform_registration($data);
        }
    }

    private function perform_registration(array $data) {
        global $db, $core, $lang;

        if (empty($data['email'])) {
            output_xml("<status>false</status><message>Specify correct email</message>");
            exit;
        }
        $data['username'] = $data['email'];
        if (!parent::username_exists($data['username'])) {
            $db_encrypt_fields = '';

            if (!parent::validate_password_complexity($data['password'])) {
                output_xml("<status>false</status><message>{$lang->pwdpatternnomatch}</message>");
                exit;
            }

            $data['salt'] = parent::create_salt();
            $data['password'] = parent::create_password($data['password'], $data['salt']);
            $data['loginKey'] = parent::create_loginkey();

            if ($core->validate_email($data['email'])) {
                $data['email'] = $core->sanitize_email($data['email']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->invalidemail}</message>");
                exit;
            }


            if (empty($data['firstName']) || empty($data['lastName'])) {
                output_xml("<status>false</status><message>{$lang->fillinfirstlastname}</message>");
                exit;
            }
            $data['firstName'] = ucfirst($data['firstName']);
            $data['lastName'] = ucfirst($data['lastName']);
            $data['displayName'] = $data['firstName'] . ' ' . $data['lastName'];
            $data['dateAdded'] = time();
            if (is_array($data['program'])) {
                $programs_array = $data['program'];
            }
            unset($data['program']);

            $query = $db->insert_query('users', $data);
            $uid = $db->last_id();
            //adjust program assignments
            $user_obj = new Users(intval($uid));
            $user_obj->deactivate_assignedprograms();
            if (is_array($programs_array)) {
                foreach ($programs_array as $progid) {
                    $assignprograms_array = array('isActive' => 1, 'uid' => intval($uid), 'progid' => intval($progid));
                    $assignprogram_obj = new AssignedPrograms();
                    $assignprogram_obj->set($assignprograms_array);
                    $assignprogram_obj->save();
                }
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->usernameexists}</message>");
            exit;
        }
    }

    private function set_employeenum($number) {
        global $db;

        if (empty($number)) {
            return false;
        }

        $db->insert_query('userhrinformation', array('employeeNum' => $number, 'uid' => $this->user['uid']));
    }

}

?>