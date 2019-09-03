<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * active_foruns block caps.
 *
 * @package    block_active_foruns
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/blocks/active_foruns/locallib.php");

class block_active_foruns extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_active_foruns');
    }

    function get_content() {
        global $CFG, $OUTPUT, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (! empty($this->config->text)) {
            $this->content->text = $this->config->text;
        }

        $this->content = '';

        #Buscando os dados do usuário no wordpress
        $result = active_foruns::get_forum_info();

        #active_foruns::dd($result);

        $table = '<table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Curso</th>
                    <th scope="col">Fórum</th>
                    <th scope="col">Tópico</th>
                    <th scope="col" class="text-center">Respostas</th>
                </tr>
            </thead>
            <tbody>';

        $i = 0;
        foreach($result as $curso) {
            $curso_moodle = $DB->get_record('course', array('shortname' => $curso['sigla']));

            foreach ($curso['atividades'] as $nome => $atividades) {

                foreach ($atividades as $discussao => $atividade) {
                    $bgcolor = '';
                    if (count($atividade['respostas']) < 2) {
                        $bgcolor = 'style="background: rgba(165, 42, 42, 0.6);" title="Crítico: Esta é uma pergunta sem nenhuma resposta."';
                    }

                    $table .= '<tr '. $bgcolor .'>
                            <th scope="row">' . ($i + 1) . '</th>
                            <td><a href="' . $CFG->wwwroot . '/course/view.php?id=' . $curso_moodle->id . '" target="_blank">' . $curso['sigla'] . '</a></td>
                            <td><a href="' . $CFG->wwwroot . '/mod/forum/discuss.php?d=' . $discussao . '" target="_blank">' . $nome . '</a></td>
                            <td>' . substr($atividade['pergunta'], 0, 150) . '</td>
                            <td class="text-center">' . (count($atividade['respostas']) -1) . '</td>
                        </tr>';
                    $i++;
                }
            }
        }
                
        $table .= '</tbody>
        </table>';


        $this->content->text = $table;


        return $this->content;
    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    public function applicable_formats() {
        return array('all' => true,
                     'site' => true,
                     'site-index' => true,
                     'course-view' => true, 
                     'course-view-social' => false,
                     'mod' => true, 
                     'mod-quiz' => false);
    }

    public function instance_allow_multiple() {
          return true;
    }

    function has_config() {return true;}

    public function cron() {
            mtrace( "Hey, my cron script is running" );
             
                 // do something
                  
                      return true;
    }
}
