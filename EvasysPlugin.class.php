<?php
/*
 *  Copyright (c) 2011-2018  Rasmus Fuhse <fuhse@data-quest.de>
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 */

require_once dirname(__file__)."/classes/EvaSysSeminar.class.php";

class EvasysPlugin extends StudIPPlugin implements SystemPlugin, StandardPlugin, AdminCourseAction
{

    public function useLowerPermissionLevels()
    {
        return (bool) Config::get()->EVASYS_PLUGIN_USE_LOWER_PERMISSION_LEVELS;
    }

    public function __construct()
    {
        parent::__construct();
        
        //The user must be root
        /*if ($GLOBALS['perm']->have_perm('root')) {
            $nav = new Navigation($this->getDisplayName(), PluginEngine::getURL($this, array(), "admin/index"));
            Navigation::addItem("/admin/evasys", $nav);
        }*/

        //Infofenster für Server-Angaben:
        if ($GLOBALS['perm']->have_perm('root')
            && (strpos($_SERVER['REQUEST_URI'], "dispatch.php/plugin_admin") || strpos($_SERVER['REQUEST_URI'], "dispatch.php/admin/plugin")) ) {
            $config = Config::get();
            if (count($_POST) && Request::get("EVASYS_WSDL") && Request::get("EVASYS_USER") && Request::get("EVASYS_PASSWORD")) {
                $config->store("EVASYS_WSDL", Request::get("EVASYS_WSDL"));
                $config->store("EVASYS_URI", Request::get("EVASYS_URI"));
                $config->store("EVASYS_USER", Request::get("EVASYS_USER"));
                $config->store("EVASYS_PASSWORD", Request::get("EVASYS_PASSWORD"));
                $config->store("EVASYS_CACHE", Request::int("EVASYS_CACHE"));
                $config->store("EVASYS_PUBLISH_RESULTS", Request::int("EVASYS_PUBLISH_RESULTS"));
            }

            $EVASYS_WSDL = $config->getValue("EVASYS_WSDL");
            $EVASYS_USER = $config->getValue("EVASYS_USER");
            $EVASYS_PASSWORD = $config->getValue("EVASYS_PASSWORD");
            $EVASYS_URI = $config->getValue("EVASYS_URI");
            $EVASYS_CACHE = $config->getValue("EVASYS_CACHE");
            if (!$EVASYS_WSDL || !$EVASYS_USER || !$EVASYS_PASSWORD || !$EVASYS_URI || $EVASYS_CACHE === "" || $EVASYS_CACHE === null) {
                PageLayout::addBodyElements($this->getTemplate("settings_window.php", null)->render());
            }
        }
    }


    public function getIconNavigation($course_id, $last_visit, $user_id = null) {
        $evasys_seminars = EvaSysSeminar::findBySeminar($course_id);
        $activated = false;
        foreach ($evasys_seminars as $evasys_seminar) {
            if ($evasys_seminar['activated']) {
                $activated = true;
            }
        }
        if ($activated) {
            $tab = new AutoNavigation(_("Evaluation"), PluginEngine::getLink($this, array(), "evaluation/show"));
            $tab->setImage(Icon::create("evaluation", "inactive"), array('title' => _("Evaluationen")));
            $number = $evasys_seminar->getEvaluationStatus();
            if ($number > 0) {
                $tab->setImage(Icon::create("evaluation", "new"), array('title' => sprintf(_("%s neue Evaluation"), $number)));
            }
            return $tab;
        }
    }

    public function getTabNavigation($course_id) {
        $evasys_seminars = EvaSysSeminar::findBySeminar($course_id);
        $activated = false;
        foreach ($evasys_seminars as $evasys_seminar) {
            if ($evasys_seminar['activated']) {
                $activated = true;
            }
        }
        if ($activated) {
            $tab = new AutoNavigation(_("Evaluation"), PluginEngine::getLink($this, array(), "evaluation/show"));
            $tab->setImage(Icon::create("evaluation", "info_alt"));
            return array('evasys' => $tab);
        }
    }

    public function getNotificationObjects($course_id, $since, $user_id) {
        return null;
    }

    public function getInfoTemplate($course_id) {
        return null;
    }

    public function getDisplayName() {
        if (Navigation::hasItem("/course") && Navigation::getItem("/course")->isActive()) {
            return $GLOBALS['SessSemName'][0].": "._("Evaluation");
        } else {
            return _("EvaSys");
        }
    }

    public function getAdminActionURL()
    {
        return $GLOBALS['perm']->have_perm("admin") ? PluginEngine::getURL($this, array(), "admin/upload_courses") : null;
    }

    public function useMultimode() {
        return _("EvaSys aktivieren");
    }

    public function getAdminCourseActionTemplate($course_id, $values = null, $semester = null) {
        $factory = new Flexi_TemplateFactory(__DIR__."/views");
        $template = $factory->open("admin/_admin_checkbox.php");
        $template->set_attribute("course_id", $course_id);
        $template->set_attribute("plugin", $this);
        return $template;
    }

    protected function getTemplate($template_file_name, $layout = "without_infobox") {
        if (!$this->template_factory) {
            $this->template_factory = new Flexi_TemplateFactory(dirname(__file__)."/templates");
        }
        $template = $this->template_factory->open($template_file_name);
        if ($layout) {
            if (method_exists($this, "getDisplayName")) {
                PageLayout::setTitle($this->getDisplayName());
            } else {
                PageLayout::setTitle(get_class($this));
            }
            $template->set_layout($GLOBALS['template_factory']->open($layout ? 'layouts/base' : null));
        }
        return $template;
    }
}