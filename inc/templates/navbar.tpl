<nav class="navbar navbar-default navbar-fixed-top hidden-print" id="main_navbar">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{$core->settings[rootdir]}">
                <img src="{$core->settings[rootdir]}/images/magister.png" alt="Pret d'outil" border="0" class="systemlogo">
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="{$core->settings[rootdir]}" style='font-size:18px;'><span id="home" class="glyphicon glyphicon-home" title="Home"></span> <span class="hidden-sm hidden-md hidden-sm hidden-lg">Home</span></a></li>

                {$modules_list}

            </ul>
            <ul class="nav navbar-nav navbar-right" id="freqmdl">
                <!--   <li id="tooltip" data-toggle="tooltip" data-placement="left" title="Frequently Used" class="hidden-xs">
                       <span  style="font-size:20px; margin-top:15px;" class="glyphicon glyphicon-star-empty" id="frequentlyused_icons"></span>
                   </li>
                -->
                <!--  <li id='updates' style='margin-left: 10px;'><a href="../navbar-static-top/"><span class="glyphicon glyphicon-alert"></span> Updates <span class="badge">42</span></a></li>-->
                <li class="dropdown" id="userprofile_menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="font-size:14px;"><span class="glyphicon glyphicon-user"></span> {$core->user[displayName]}<span class="sr-only">(current)</span> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li ><a href="{$settings[rootdir]}/users.php?action=profile&amp;do=edit">{$lang->edityouraccount}</a></li>
                        <li>{$admincplink}{$mainpageslink}</li>
                        <li  class="divider" role="separator"></li>
                        <li><a href='{$settings[rootdir]}/users.php?action=do_logout'>{$lang->logout}</a></li>

                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>