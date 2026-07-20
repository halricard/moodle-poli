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
 * Strings for local_polisetup (Brazilian Portuguese).
 *
 * @package    local_polisetup
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Configuração Poliedro';
$string['applyintro'] = 'Aplica a configuração base da universidade corporativa Poliedro ao site. Roda automaticamente na instalação do plugin; use o botão abaixo para reaplicar. Tudo abaixo é uma configuração padrão do site e pode ser revertido no Administração.';
$string['applybutton'] = 'Aplicar configuração Poliedro';
$string['applied'] = 'Configuração Poliedro aplicada.';
$string['applyitem_theme'] = 'Definir o Poli como tema ativo (todos os dispositivos).';
$string['applyitem_login'] = 'Login corporativo: desativar auto-cadastro, o botão de convidado e o autologin de convidado.';
$string['applyitem_lang'] = 'Definir Português do Brasil (pt_br) como idioma padrão.';
$string['applyitem_sitename'] = 'Definir o nome do site como Poliedro.';
$string['applyitem_langpack'] = 'Baixar o pacote de idioma pt_br (requer internet; ignorado se indisponível).';

$string['logset'] = 'Definido {$a->name} = {$a->value}';
$string['logsitename'] = 'Nome do site definido como “{$a}”';
$string['loglangpresent'] = 'Pacote de idioma pt_br já instalado';
$string['loglanginstalled'] = 'Pacote de idioma pt_br instalado';
$string['loglangfailed'] = 'Não foi possível baixar o pacote pt_br — instale manualmente em Pacotes de idioma';
$string['logskip'] = 'Ignorado: {$a}';
$string['logdone'] = 'Concluído. Caches limpos.';

$string['privacy:metadata'] = 'O plugin de configuração Poliedro não armazena nenhum dado pessoal.';
