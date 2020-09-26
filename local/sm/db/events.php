<?PHP


$observers = array(
    array(
        'eventname' => 'mod_quiz\event\attempt_submitted',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_attempt_submitted',
        'internal' => false
     ),
    array(
        'eventname' => 'core\event\course_section_updated',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_course_section_update',
        'internal' => false
    ),
    array(
        'eventname' => 'core\event\course_updated',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_course_update',
        'internal' => false
    ),
     array(
        'eventname' => 'core\event\user_loggedin',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_enrole',
    ),

);
