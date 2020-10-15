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
        'callback' => 'local_sm_check_session',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_book\event\chapter_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_book_chapter_viewed',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_book\event\course_module_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_book_module_viewed',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_assign\event\submission_created',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_assign_submission_created',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_feedback\event\course_module_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_feedback_view_feedback',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_forum\event\forum_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_view_forum',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_forum\event\forum_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_view_forum',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_forum\event\discussion_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_view_forum_discussion',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_wiki\event\page_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_wiki_page_viewed',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_wiki\event\course_module_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_wiki_course_module_viewed',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_resource\event\course_module_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_resource_course_module_viewed',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_resource\event\course_module_instance_list_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_resource_course_module_instance_list_viewed',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_page\event\course_module_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_page_course_module_viewed',
        'internal' => false
    ),
    array(
        'eventname' => 'mod_url\event\course_module_viewed',
        'includefile' => '/local/sm/lib.php',
        'callback' => 'local_sm_mod_url_course_module_viewed',
        'internal' => false
    ),

);
