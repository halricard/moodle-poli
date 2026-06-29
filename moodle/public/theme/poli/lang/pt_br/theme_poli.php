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
 * Poli theme strings (Brazilian Portuguese).
 *
 * @package    theme_poli
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Poli';
$string['choosereadme'] = 'Poli é um tema filho do Boost, moderno e refinado, criado para a experiência de aprendizagem do Poliedro. Oferece uma home do aluno com banner hero, explorador de categorias e cards de cursos com imagens, uma página de apresentação de curso elegante e suporte completo a modo claro/escuro — mantendo a área administrativa no Boost nativo.';
$string['configtitle'] = 'Configurações do Poli';

// Setting pages.
$string['generalsettings'] = 'Geral';
$string['frontpagesettings'] = 'Página inicial';
$string['advancedsettings'] = 'Avançado';

// Preset.
$string['preset'] = 'Predefinição do tema';
$string['preset_desc'] = 'Escolha uma predefinição para alterar amplamente a aparência do tema.';
$string['presetfiles'] = 'Arquivos de predefinição adicionais';
$string['presetfiles_desc'] = 'Arquivos de predefinição podem alterar drasticamente a aparência do tema.';

// Brand colours.
$string['brandcolor'] = 'Cor principal da marca';
$string['brandcolor_desc'] = 'A cor de destaque principal (azul-petróleo do Poliedro).';
$string['brandaccent'] = 'Cor de destaque secundária';
$string['brandaccent_desc'] = 'A cor secundária usada em destaques e gradientes (magenta do Poliedro).';
$string['brandtertiary'] = 'Cor terciária';
$string['brandtertiary_desc'] = 'Uma terceira cor de destaque usada com moderação (âmbar do Poliedro).';

// Navbar appearance.
$string['navbarstyle'] = 'Estilo da barra de navegação';
$string['navbarstyle_desc'] = 'Como a barra de navegação é decorada com a cor da marca.';
$string['navbarstyle_accentgradient'] = 'Linha de acento — gradiente';
$string['navbarstyle_accentsolid'] = 'Linha de acento — cor sólida';
$string['navbarstyle_fillgradient'] = 'Barra preenchida — gradiente';
$string['navbarstyle_fillsolid'] = 'Barra preenchida — cor sólida';
$string['navbarstyle_none'] = 'Nenhum';
$string['navbarcolor'] = 'Cor da barra de navegação';
$string['navbarcolor_desc'] = 'Qual cor da paleta usar no acento/preenchimento da barra.';
$string['palette_primary'] = 'Primária (azul-petróleo)';
$string['palette_accent'] = 'Destaque (magenta)';
$string['palette_tertiary'] = 'Terciária (âmbar)';

// Logos.
$string['logolight'] = 'Logo (modo claro)';
$string['logolight_desc'] = 'Logo exibido na barra de navegação no tema claro. Use um logo com texto/cores escuras, legível sobre fundo claro.';
$string['logodark'] = 'Logo (modo escuro)';
$string['logodark_desc'] = 'Logo exibido na barra de navegação no tema escuro. Use um logo com texto claro/branco, legível sobre fundo escuro. Se apenas um logo for enviado, ele é usado nos dois modos.';

// Images.
$string['backgroundimage'] = 'Imagem de fundo';
$string['backgroundimage_desc'] = 'A imagem exibida como fundo do site. Esta imagem substitui a definida na predefinição do tema.';
$string['loginbackgroundimage'] = 'Imagem de fundo do login';
$string['loginbackgroundimage_desc'] = 'A imagem exibida como fundo da página de login.';
$string['herobackgroundimage'] = 'Imagem de fundo do hero';
$string['herobackgroundimage_desc'] = 'Imagem opcional exibida atrás do banner hero da página inicial. Quando vazia, é usado um gradiente da marca.';

// Hero.
$string['heroheading'] = 'Título do hero';
$string['heroheading_desc'] = 'O título grande exibido no banner hero da página inicial.';
$string['heroheading_default'] = 'Aprenda sem limites com o Poliedro';
$string['herosubheading'] = 'Subtítulo do hero';
$string['herosubheading_desc'] = 'O texto de apoio exibido abaixo do título do hero.';
$string['herosubheading_default'] = 'Conteúdos de excelência, professores dedicados e uma plataforma feita para você ir além. Comece agora a sua jornada de aprendizado.';
$string['herobuttontext'] = 'Texto do botão do hero';
$string['herobuttontext_desc'] = 'O texto do botão principal de chamada para ação do hero.';
$string['herobuttontext_default'] = 'Explorar meus cursos';
$string['herobuttonurl'] = 'URL do botão do hero';
$string['herobuttonurl_desc'] = 'O destino do botão principal de chamada para ação.';

// Section toggles.
$string['showcategories'] = 'Exibir seção de categorias';
$string['showcategories_desc'] = 'Exibir o explorador de categorias de cursos na página inicial.';
$string['showcourses'] = 'Exibir seção de cursos em destaque';
$string['showcourses_desc'] = 'Exibir os cards de cursos em destaque na página inicial.';
$string['courselimit'] = 'Número de cursos em destaque';
$string['courselimit_desc'] = 'Quantos cards de curso exibir na grade de cursos em destaque.';

// Raw SCSS.
$string['rawscsspre'] = 'SCSS inicial';
$string['rawscsspre_desc'] = 'Código SCSS injetado antes de tudo. Geralmente usado para definir variáveis.';
$string['rawscss'] = 'SCSS bruto';
$string['rawscss_desc'] = 'Código SCSS ou CSS injetado no final da folha de estilos.';

// Front page section headings.
$string['exploretitle'] = 'Explore por área';
$string['exploresubtitle'] = 'Encontre o caminho ideal para os seus objetivos';
$string['coursestitle'] = 'Cursos em destaque';
$string['coursessubtitle'] = 'Selecionados para você começar';
$string['viewallcourses'] = 'Ver todos os cursos';
$string['coursecount'] = '{$a} cursos';
$string['onecourse'] = '1 curso';
$string['nocourses'] = 'Nenhum curso disponível ainda.';

// Course cards / hero.
$string['enrolled'] = 'Inscrito';
$string['viewcourse'] = 'Ver curso';
$string['continuelearning'] = 'Continuar estudando';
$string['accesscourse'] = 'Acessar curso';
$string['courseprogress'] = 'Seu progresso';
$string['taughtby'] = 'Com';

// Colour scheme toggle.
$string['togglecolorscheme'] = 'Alternar modo claro/escuro';
$string['lightmode'] = 'Modo claro';
$string['darkmode'] = 'Modo escuro';

// Login panel.
$string['loginpaneltitle'] = 'Sua jornada de conhecimento começa aqui';
$string['loginpanellead'] = 'Acesse seus cursos, acompanhe seu progresso e aprenda com a qualidade Poliedro — a qualquer hora, em qualquer lugar.';
$string['loginfeature1'] = 'Conteúdo de excelência e professores dedicados';
$string['loginfeature2'] = 'Acompanhe seu progresso em tempo real';
$string['loginfeature3'] = 'Estude em qualquer dispositivo';

// Course listing / category pages.
$string['allcoursestitle'] = 'Todos os cursos';
$string['allcoursesdesc'] = 'Navegue pelo catálogo completo e encontre seu próximo curso.';

// Dashboard welcome.
$string['goodmorning'] = 'Bom dia';
$string['goodafternoon'] = 'Boa tarde';
$string['goodevening'] = 'Boa noite';
$string['welcomesub'] = 'Aqui está um resumo dos seus estudos. Continue assim!';
$string['statcourses'] = 'Meus cursos';
$string['statinprogress'] = 'Em andamento';
$string['statcompleted'] = 'Concluídos';

// Footer.
$string['footertagline'] = 'Excelência em educação. Aprenda sem limites, a qualquer hora e em qualquer lugar, com a qualidade Poliedro.';
$string['footerexplore'] = 'Explorar';
$string['footerhelp'] = 'Suporte';
$string['footerhome'] = 'Início';
$string['footercourses'] = 'Todos os cursos';
$string['footercategories'] = 'Categorias';
$string['footermycourses'] = 'Meus cursos';
$string['footercontact'] = 'Falar com o suporte';
$string['footerrights'] = 'Todos os direitos reservados.';

// Privacy.
$string['privacy:metadata'] = 'O tema Poli não armazena nenhum dado pessoal.';
