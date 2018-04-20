<?php

class AddCourseProfiles extends Migration
{
    public function up()
    {
        DBManager::get()->exec("
            CREATE TABLE `evasys_course_profiles` (
                `course_profile_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                `seminar_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `semester_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `form_id` int(11) DEFAULT NULL,
                `begin` int(11) DEFAULT NULL,
                `end` int(11) DEFAULT NULL,
                `mode` enum('paper','online') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `address` text COLLATE utf8mb4_unicode_ci,
                `language` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `number_of_sheets` int(11) DEFAULT NULL,
                `teachers` text COLLATE utf8mb4_unicode_ci,
                `teachers_results` text COLLATE utf8mb4_unicode_ci,
                `results_email` text COLLATE utf8mb4_unicode_ci,
                `applied` tinyint(4) NOT NULL DEFAULT '0',
                `transferred` tinyint(4) DEFAULT NULL,
                `by_dozent` tinyint(4) NOT NULL DEFAULT '0',
                `user_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `chdate` int(11) NOT NULL,
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`course_profile_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        DBManager::get()->exec("
            CREATE TABLE `evasys_forms` (
                `form_id` int(11) NOT NULL,
                `name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `description` text DEFAULT NULL,
                `active` int(11) NOT NULL DEFAULT '0',
                `link` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `chdate` int(11) DEFAULT NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`form_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        DBManager::get()->exec("
            CREATE TABLE `evasys_institute_profiles` (
                `institute_profile_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `institut_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `semester_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `form_id` int(11) DEFAULT NULL,
                `begin` int(11) DEFAULT NULL,
                `end` int(11) DEFAULT NULL,
                `mode` enum('paper','online') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `address` text COLLATE utf8mb4_unicode_ci,
                `antrag_begin` int(11) DEFAULT NULL,
                `antrag_end` int(11) DEFAULT NULL,
                `antrag_info` text COLLATE utf8mb4_unicode_ci,
                `user_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `chdate` int(11) NOT NULL,
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`institute_profile_id`),
                UNIQUE KEY `institute_id` (`institut_id`,`semester_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        DBManager::get()->exec("
            CREATE TABLE `evasys_global_profiles` (
                `semester_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `form_id` int(11) DEFAULT NULL,
                `begin` int(11) DEFAULT NULL,
                `end` int(11) DEFAULT NULL,
                `mode` enum('paper','online') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `address` text COLLATE utf8mb4_unicode_ci,
                `antrag_begin` int(11) DEFAULT NULL,
                `antrag_end` int(11) DEFAULT NULL,
                `antrag_info` text COLLATE utf8mb4_unicode_ci,
                `user_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `chdate` int(11) NOT NULL,
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`semester_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        DBManager::get()->exec("
            CREATE TABLE `evasys_profiles_semtype_forms` (
                `profile_form_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                `profile_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `profile_type` enum('global','institute') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'global',
                `form_id` int(11) NOT NULL,
                `position` int(11) DEFAULT NULL,
                `standard` tinyint(4) NOT NULL DEFAULT '0',
                `chdate` int(11) DEFAULT NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`profile_form_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        DBManager::get()->exec("
            CREATE TABLE `evasys_matchings` (
                `matching_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `item_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `item_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `chdate` int(11) DEFAULT NULL,
                `mkdate` int(11) DEFAULT NULL,
                PRIMARY KEY (`matching_id`),
                KEY `item_id` (`item_id`),
                KEY `item_type` (`item_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        Config::get()->create("EVASYS_ENABLE_PROFILES", array(
            'value' => 1,
            'type' => "boolean",
            'range' => "global",
            'section' => "EVASYS_PLUGIN",
            'description' => "Is the feature to edit the evaluation profiles on in Stud.IP?"
        ));
        Config::get()->create("EVASYS_ENABLE_PROFILES_FOR_ADMINS", array(
            'value' => 1,
            'type' => "boolean",
            'range' => "global",
            'section' => "EVASYS_PLUGIN",
            'description' => "Are admins (not root) allowed to edit their institute-profiles?"
        ));
        Config::get()->create("EVASYS_ENABLE_SPLITTING_COURSES", array(
            'value' => 0,
            'type' => "boolean",
            'range' => "global",
            'section' => "EVASYS_PLUGIN",
            'description' => "May a course with multiple teachers be split into multiple evaluations?"
        ));

    }
    
    public function down()
    {

    }
}
