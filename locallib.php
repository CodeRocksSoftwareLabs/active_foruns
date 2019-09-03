<?php
defined('MOODLE_INTERNAL') || die;

require_once(dirname(dirname(dirname(__FILE__))).'/course/lib.php');

class active_foruns{

    public static function get_forum_info() {
        global $DB;

        $courses = get_courses();

        $foruns = [];

        foreach ($courses as $i => $course) {
            if (($course->format == "topics") && (!in_array($course->category, [0, 3]))) {
                $foruns[$i]['sigla'] = $course->shortname;

                $context = context_course::instance($course->id);
                $activities = get_array_of_activities($course->id);

                foreach ($activities as $activity) {
                    if ($activity->mod == "forum") {

                        $perguntas = $DB->get_records('forum_discussions', array('forum' => $activity->id, 'course' => $course->id));
                        if (!empty($perguntas)) {
                            foreach ($perguntas as $pergunta) {
                                $respostas = $DB->get_records('forum_posts', array('discussion' => $pergunta->id));
                                $roles = get_user_roles($context, $pergunta->userid);
                                $foruns[$i]['atividades'][$activity->name][$pergunta->id]['pergunta'] = $pergunta->name;
                                $foruns[$i]['atividades'][$activity->name][$pergunta->id]['papel_do_autor'] = reset($roles)->roleid;
                                $foruns[$i]['atividades'][$activity->name][$pergunta->id]['respostas'] = $respostas;

                                if (in_array($foruns[$i]['atividades'][$activity->name][$pergunta->id]['papel_do_autor'], ['3', '1'])) {
                                    unset($foruns[$i]['atividades'][$activity->name]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $foruns;
    }

    public static function dd($arr)
    {
        global $PAGE;

        # Verificando se o usuário é admin
        /*$coursecontext = context_course::instance($PAGE->course->id);
        if(has_capability('moodle/site:config', $coursecontext)) 
        {*/
            echo "<pre>";
            var_dump($arr);
            echo "</pre>";
            die;
        #}
    }
}