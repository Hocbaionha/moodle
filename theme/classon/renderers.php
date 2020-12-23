<?PHP
class theme_classon_core_renderer extends core_renderer {


    public function custom_menu_frontend($custommenuitems = '') {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu_frontend($custommenu);
    }

    protected function render_custom_menu_frontend(custom_menu $menu) {
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

        return $content;
    }

    public function user_menu($user = null, $withlinks = null) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = 'usermenu';
        if (!$withlinks) {
            $usermenuclasses .= ' withoutlinks';
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            $returnstr = get_string('loggedinnot', 'moodle');
            if (!$loginpage) {
                $returnstr .= " (<a href=\"$loginurl\">" . get_string('login') . '</a>)';
            }
            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );

        }

        // If logged in as a guest user, show a string to that effect.
        if (isguestuser()) {
            $returnstr = get_string('loggedinasguest');
            if (!$loginpage && $withlinks) {
                $returnstr .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
            }

            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );
        }

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page);

        $avatarclasses = "avatars";
        $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
        $usertextcontents = $opts->metadata['userfullname'];

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $avatarcontents .= html_writer::span(
                $opts->metadata['realuseravatar'],
                'avatar realuser'
            );
            $usertextcontents = $opts->metadata['realuserfullname'];
            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                        $opts->metadata['userfullname'],
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['rolename'],
                'meta role role-' . $role
            );
        }

        // User login failures.
        if (!empty($opts->metadata['userloginfail'])) {
            $usertextcontents .= html_writer::span(
                $opts->metadata['userloginfail'],
                'meta loginfailures'
            );
        }

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        $returnstr .= html_writer::span(
            html_writer::span($usertextcontents, 'usertext mr-1') .
            html_writer::span($avatarcontents, $avatarclasses),
            'userbutton'
        );

        // Create a divider (well, a filler).
        $divider = new action_menu_filler();
        $divider->primary = false;

        $am = new action_menu();
        $am->set_menu_trigger(
            $returnstr
        );
        $am->set_action_label(get_string('usermenu'));
        $am->set_alignment(action_menu::TR, action_menu::BR);
        $am->set_nowrap_on_items();
        if ( isloggedin() && !isguestuser() ) {

            //cuongpt add function change password
            $link_change_password =$CFG->wwwroot .'/local/sm/change_password.php';
            $new_url = new moodle_url($link_change_password);
            $change_password = '{"itemtype":"link","title":"Đổi mật khẩu","titleidentifier":"changepass","url":{},"pix":"t\/edit"}';
            $change_password =  json_decode($change_password);
            $change_password->url = $new_url;
            $opts->navitems[] = $change_password;

            //anhnn add student_code
            $add_url = new moodle_url("#");
            global $DB;
            $codeField = $DB->get_record("user_info_field",array("shortname"=>"student_code"))->id;
            $check = $DB->get_record("user_info_data",array("userid"=>$user->id,"fieldid"=>$codeField));
            if($check){
                $code=$check->data;
                $bosung='{"itemtype":"link",
                          "title":"Mã liên kết:<br/> '.$code.'",
                          "titleidentifier":"B\u1ed5-sung",
                          "url":{}}';
                $bosung = json_decode($bosung);
                $bosung->url = $add_url;
                $opts->navitems[] = $bosung;
            }

            // $checkadd=0;
            // $uid = $USER->id;
            // $table = 'hbon_add_info_user';
            // global $DB;

            // $has_add_phone = $DB->record_exists($table, array('user_id' => $uid , 'signup_method' => 'phone'));


            // if(!$has_add_phone) {
            //     $checkadd=1;
            // }else {
            //     $user_phone_info =  $DB->get_record($table, array('user_id'=>$uid, 'signup_method' => 'phone'));
            //     if($user_phone_info->has_confirm == 0) {
            //         $checkadd=1;
            //     }else {
            //         $has_add_email = $DB->record_exists($table, array('user_id' => $uid , 'signup_method' => 'email'));

            //         if(!$has_add_email) {
            //             $checkadd=1;
            //         }else {
            //             $user_email_info =  $DB->get_record($table, array('user_id'=>$uid, 'signup_method' => 'email'));

            //             if($user_email_info->has_confirm == 0) {
            //                 $checkadd=1;
            //             }
            //         }
            //     }
            // }
            // if($checkadd){
            //     $add_url = new moodle_url("/?page=additional");
            //     $bosung='{"itemtype":"link","title":"B\u1ed5 sung","titleidentifier":"B\u1ed5-sung","url":{},"pix":"t\/edit"}';
            //     $bosung = json_decode($bosung);
            //     $bosung->url = $add_url;
            //     $opts->navitems[] = $bosung;
            // }
        }

        // Bổ sung|/?page=additional
        if ($withlinks) {
            $navitemcount = count($opts->navitems);
            $idx = 0;
            foreach ($opts->navitems as $key => $value) {

                switch ($value->itemtype) {
                    case 'divider':
                        // If the nav item is a divider, add one and skip link processing.
                        $am->add($divider);
                        break;

                    case 'invalid':
                        // Silently skip invalid entries (should we post a notification?).
                        break;

                    case 'link':
                        // Process this as a link item.
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) {
                            $pix = new pix_icon($value->pix, '', null, array('class' => 'iconsmall'));
                        } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                $value->imgsrc,
                                $value->title,
                                array('class' => 'iconsmall')
                            ) . $value->title;
                        }

                        $al = new action_menu_link_secondary(
                            $value->url,
                            $pix,
                            $value->title,
                            array('class' => 'icon')
                        );
                        if (!empty($value->titleidentifier)) {
                            $al->attributes['data-title'] = $value->titleidentifier;
                        }
                        $am->add($al);
                        break;
                }

                $idx++;

                // Add dividers after the first item and before the last item.
                if ($idx == 1 || $idx == $navitemcount - 1) {
                    $am->add($divider);
                }
            }
        }

        return html_writer::div(
            $this->render($am),
            $usermenuclasses
        );
    }

    /**
     * Secure layout login info.
     *
     * @return string
     */
    public function secure_layout_login_info() {
        if (get_config('core', 'logininfoinsecurelayout')) {
            return $this->login_info(false);
        } else {
            return '';
        }
    }
}
