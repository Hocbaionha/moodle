<?PHP


$observers = array(
    array(
        'eventname' => 'mod_quiz\event\attempt_submitted',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_attempt_submitted',
        'internal' => false
     ),
     array(
        'eventname' => 'mod_quiz\event\quiz_attempt_submitted',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_attempt_submitted',
        'internal' => false
     ),
     array(
        'eventname' => 'core\event\user_loggedin',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_enrole',
    ),
);