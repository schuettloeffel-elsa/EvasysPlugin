<?php

class EvaluationController extends PluginController
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $tab = Navigation::getItem("/course/evasys");
        $tab->setImage(Icon::create("evaluation", "info"));
        Navigation::activateItem("/course/evasys");
        $this->profile = EvasysCourseProfile::findBySemester(Context::get()->id);
        PageLayout::addScript($this->plugin->getPluginURL()."/assets/qrcode.js");
    }

    public function show_action()
    {
        if ($this->profile && $this->profile['split']) {
            $this->redirect("evaluation/split");
            return;
        }

        $this->evasys_seminars = EvasysSeminar::findBySeminar(Context::get()->id);
        $this->surveys = array();
        foreach ($this->evasys_seminars as $evasys_seminar) {
            $survey_information = $evasys_seminar->getSurveyInformation();
            if (is_array($survey_information)) {
                foreach ($survey_information as $info) {
                    $this->surveys[] = $info;
                }
            }
        }
    }

    public function split_action()
    {
        if (!Config::get()->EVASYS_ENABLE_PROFILES || !$this->profile || !$this->profile['split']) {
            $this->redirect("evaluation/show");
            return;
        }
        $this->evasys_seminars = array();
        if ($this->profile['teachers']) {
            $teachers = $this->profile['teachers']->getArrayCopy();
        } else {
            $statement = DBManager::get()->prepare("
                SELECT seminar_user.user_id 
                FROM seminar_user 
                WHERE seminar_user.Seminar_id = ?
                    AND seminar_user.status = 'dozent' 
                ORDER BY seminar_user.position ASC 
            ");
            $statement->execute(array(Context::get()->id));
            $teachers = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        }
        foreach ($teachers as $dozent_id) {
            $seminar = new EvasysSeminar();
            $seminar['seminar_id'] = Context::get()->id . $dozent_id;
            $this->evasys_seminars[$dozent_id] = $seminar;
        }

        $this->surveys = array();
        foreach ($this->evasys_seminars as $dozent_id => $evasys_seminar) {
            $survey_information = $evasys_seminar->getSurveyInformation();
            if (is_array($survey_information)) {
                foreach ($survey_information as $info) {
                    $this->surveys[$dozent_id][] = $info;
                }
            }
        }





        /*$active = false;
        foreach ($this->surveys as $dozent_id => $surveys) {
            foreach ($surveys as $survey) {
                if ($survey->m_nState > 0) {
                    $active = true;
                    break;
                }
            }
        }*/

        $this->open_surveys = array();

        //$user_can_participate = array();

        if (count($this->evasys_seminars)
            && !$GLOBALS['perm']->have_studip_perm("dozent", Context::get()->id)) {
            foreach ($this->evasys_seminars as $dozent_id => $seminar) {
                $this->open_surveys[$dozent_id] = $seminar->getSurveys($GLOBALS['user']->id);
                /*if (is_array($this->open_surveys)) {
                    foreach ($this->open_surveys as $one) {
                        if (is_object($one)) {
                            $user_can_participate[] = count($this->open_surveys) - 1;
                            break;
                        }
                    }
                }*/
            }
        }

    }

    public function toggle_publishing_action()
    {
        if (Request::get("dozent_vote")) {
            $evasys_seminar = EvasysSeminar::find(Context::get()->id);
            $evasys_seminar->allowPublishing(Request::get("dozent_vote") === "y");
        }
        $this->redirect("evaluation/show");
    }

}
