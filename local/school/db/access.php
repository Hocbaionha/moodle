<?php


defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/school:write' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager'=> CAP_ALLOW,
        ),
    ),

    'local/school:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager'=> CAP_ALLOW,
        ),
    )
);
