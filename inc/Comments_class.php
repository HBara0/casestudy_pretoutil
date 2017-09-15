<?php

class Comments extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;
    protected $usergroup = array();

    const PRIMARY_KEY = 'comid';
    const TABLE_NAME = 'comments';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    protected function create(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        if (is_array($data)) {
            $data['createdOn'] = TIME_NOW;
            $data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $data);
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        if (is_array($data)) {
            $data['modifiedOn'] = TIME_NOW;
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
        return $this;
    }

    public function get_user() {
        return new Users(intval($this->data['uid']));
    }

    public function get_tool() {
        return new Tools(intval($this->data['uid']));
    }

}

?>