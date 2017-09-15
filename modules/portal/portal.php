<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}


if (!$core->input['action']) {


    eval("\$portal = \"" . $template->get('portal') . "\";");
    output_page($portal);
}
else {
    
}
?>