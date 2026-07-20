# Plano de Refinamento UX/UI — Tema Poli (Universidade Corporativa)

> Auditoria feita sobre o tema `theme/poli` (child do Boost, Moodle 5.x) e os screenshots
> atuais da home, login, dashboard e listagem de cursos. Contexto: **universidade
> corporativa** — colaboradores logados assistindo cursos internos. O design atual já está
> acima da média dos temas Moodle; os pontos abaixo são refinamentos e lacunas, com foco
> na jornada do aluno/colaborador.

---

## 1. Pontos críticos (corrigir primeiro)

### 1.1 Idioma misto PT/EN em toda a interface
A UI mistura português e inglês na mesma tela: hero em PT ("Aprenda sem limites") ao lado
de "View all courses", "Featured courses", "Find the path that fits your goals",
"5 courses", "Welcome back", footer inteiro em EN.

- Definir `pt_br` como idioma padrão do site (e instalar o language pack).
- Completar `lang/pt_br/theme_poli.php` — hoje várias strings do tema só existem em EN.
- Revisar strings core visíveis ao aluno (login, footer, "Log in as guest") via
  customização de idioma (Administração > Idioma > Personalização) quando necessário.

### 1.2 Branding inconsistente (Moodle vs Poliedro)
- Footer exibe logo-texto "**moodle**" e "© 2026 moodle" — deveria ser Poliedro
  (o template usa `{{{ sitename }}}`; o sitename do site está "Moodle"/"Moodle Dev").
- Login: "Log in to **Moodle Dev**" — mesmo problema.
- Ação: ajustar `fullname`/`shortname` do site OU usar logo Poliedro no footer em vez do
  sitename em texto. Padronizar tagline do footer com a do hero.

### 1.3 Login inadequado para contexto corporativo
Para colaboradores, **auto-cadastro e convidado não fazem sentido**:
- Remover/ocultar "Sign up" e "Log in as guest" (desabilitar self-registration e guest
  login nas configs; o template `login.mustache` já respeita `cansignup`).
- Prever **SSO corporativo** (OAuth2 Microsoft Entra/Google Workspace) como botão
  primário: "Entrar com conta corporativa". Login manual vira fallback secundário.
- Trocar "Username or email" por rótulo claro em PT ("E-mail corporativo").

> **Reversibilidade**: tudo aqui é configuração (self-registration, guest login), não
> remoção de código — o template já é condicional (`cansignup`, guest). Se a diretoria
> quiser abrir acesso externo depois, basta religar as configs: guest por curso,
> auto-cadastro por e-mail (com filtro de domínios via `allowemailaddresses`), contas
> manuais ou OAuth2 Google público convivendo com o SSO corporativo. Textos do login
> devem ficar em strings de idioma/settings (nunca hardcoded) para se adaptarem a esse
> cenário.

### 1.4 Badges de notificação exibindo "0"
Navbar logada mostra dois badges vermelhos com "0" — ruído visual que treina o usuário a
ignorar notificações. Ocultar badge quando contagem = 0 (CSS/ajuste no popover region).

### 1.5 Coluna "Support" vazia no footer
A coluna renderiza o título mesmo sem `supportemail`/doc link configurados. Ocultar a
coluna inteira quando vazia (condição no mustache) ou configurar e-mail de suporte/RH —
num contexto corporativo, "Fale com o RH/T&D" é link obrigatório.

### 1.6 Capa dos cards de curso com URL quebrada (404)
**Diagnóstico confirmado**: não é o chip de categoria (é `<span>` texto puro) nem capa
faltando (o fallback gradiente + iniciais já cobre esse caso). A capa existe no curso,
mas `theme_poli_course_image()` (`lib.php`) monta a URL do pluginfile passando
`$file->get_itemid()` (= `0`) — a filearea `overviewfiles` não usa itemid no caminho, e
o `0/` extra faz o `course_pluginfile()` devolver **404**. O browser então exibe o ícone
de imagem quebrada por cima do gradiente.

- Verificado no ambiente: `pluginfile.php/31/course/overviewfiles/0/cover.jpg` → 404;
  sem o `0/` → 200 image/jpeg.
- **Fix (1 linha)**: passar `null` como itemid em `make_pluginfile_url()` dentro de
  `theme_poli_course_image()` — mesmo padrão do renderer core.

---

## 2. Jornada do aluno/colaborador (prioridade máxima)

### 2.1 Dashboard: de "boas-vindas" para "central de ação"
O welcome + stats atual é bonito, mas estático. O colaborador entra para **continuar um
curso ou cumprir um prazo**. Adicionar, nesta ordem:

1. **"Continuar de onde parou"** — card hero com a última atividade acessada
   (deep-link direto para a atividade, não para a página do curso). Fonte:
   `block_recentlyaccesseditems` / logstore.
2. **Cursos em andamento primeiro**, cards com barra de progresso e "X de Y atividades".
3. **Prazos próximos** — atividades com data de entrega/expectativa de conclusão
   (timeline do core reestilizada no padrão poli).
4. **Certificados e conquistas** — contagem no stats + seção (integra `mod_customcert` /
   badges quando existirem).

Ajustes pontuais no bloco de stats atual:
- Ícone `fa-spinner` para "em andamento" parece *loading* — trocar por
  `fa-bars-progress` ou `fa-chart-line`.
- Os 3 stats deveriam ser todos clicáveis (hoje só o primeiro é `<a>`); levar para
  "Meus cursos" filtrado.
- Emoji 👋 no título: adicionar `aria-hidden="true"` num `<span>` (leitor de tela hoje lê
  "waving hand").

### 2.2 "Continue learning" do course hero não retoma nada
`coursehero.mustache` — o CTA "Continue learning" é só uma âncora `#poli-course-content`.
Para aluno matriculado com progresso, deveria **retomar a última atividade** do curso
(deep-link). Manter a âncora apenas como fallback para não matriculado.

### 2.3 Experiência DENTRO do curso (maior lacuna atual)
Só o hero do curso foi customizado; todo o miolo (seções, atividades, completion,
navegação) é Boost puro — é onde o aluno passa 90% do tempo. Refinar:

- **Cards de atividade**: reestilizar `.activity-item` no padrão poli (superfície, hover,
  ícone colorido por tipo de atividade, estado de conclusão mais visível).
- **Completion**: checkmark/estado "concluído" com feedback claro (cor da marca, não o
  cinza padrão); % da seção no cabeçalho de cada seção.
- **Navegação entre atividades**: prev/next persistente no rodapé da atividade
  (`activity navigation` do Boost reestilizada) — fluxo "próxima aula" tipo player.
- **Course index (drawer esquerdo)**: aplicar tokens do tema (hoje destoa do resto),
  marcar atividade atual e concluídas com mais contraste.
- **Página de atividade**: header enxuto, respiro, largura de leitura máx. (~75ch) para
  conteúdo de texto/página.

### 2.4 "Meus cursos" (`/my/courses.php`)
Aplicar os course cards poli (hoje deve estar com cards Boost padrão), com progresso,
ordenação padrão por **último acesso** e filtros (Em andamento / Concluídos / Todos).

### 2.5 Busca e descoberta
- Verificar se o campo "Search courses..." do front page busca no site todo ou só filtra
  os cards já renderizados — colaborador precisa achar qualquer curso do catálogo.
- Estado vazio da busca com sugestão ("Nenhum resultado — veja todas as áreas").

### 2.6 Hero da home: espaço nobre subaproveitado
Os 4 tiles à direita são decorativos (ícones sem rótulo, não clicáveis, `aria-hidden`).
Opções melhores para logado: mini-card "continuar de onde parou", stats reais da
plataforma ("X cursos · Y colaboradores certificados"), ou os tiles virarem atalhos
clicáveis para as áreas (com rótulo).

### 2.7 Vocabulário corporativo
Revisar strings para o contexto: "aluno" → "colaborador" onde couber, "Vestibular/Ensino
Médio" são categorias herdadas do contexto escolar — para universidade corporativa,
áreas tipo "Onboarding", "Liderança", "Compliance", "Técnico". (Conteúdo, não código —
mas os ícones hardcoded por índice em `theme_poli_get_categories()` não vão casar; ver 4.3.)

---

## 3. Acessibilidade (WCAG 2.2 AA)

- **Contraste**: chips brancos sobre âmbar (#FAA41F) reprovam AA; texto
  `rgba(255,255,255,.8x)` sobre gradientes precisa de verificação nos pontos claros do
  gradiente (scrim mais forte resolve). Rodar axe/Lighthouse nas 5 telas principais.
- **`prefers-reduced-motion`**: desligar animações de gradiente/hover/parallax.
- **Foco visível**: cards e tiles clicáveis precisam de `:focus-visible` claro (outline
  na cor da marca), não só hover.
- **Cards clicáveis**: garantir que o card inteiro é um alvo (link estendido) sem aninhar
  links duplicados para leitores de tela.
- **Modo claro**: tema é dark-first; validar paridade do modo claro (`data-bs-theme`) em
  todas as superfícies poli custom — screenshots são todos dark.

## 4. Técnicos / performance (afetam UX)

### 4.1 `theme_poli_get_courses()` carrega o site inteiro
`get_courses('all', ...)` sem limite traz **todos** os cursos do site para memória a cada
render do front page. Com catálogo corporativo crescendo, degrada. Trocar por consulta
limitada (`core_course_category::search_courses` com limit ou SQL com `LIMIT`).

### 4.2 Query de professores no hero a cada pageload
`theme_poli_course_hero()` roda `get_role_users()` por archetype em todo acesso ao curso.
Cachear (MUC request/session cache) — em cursos corporativos com muitos inscritos isso pesa.

### 4.3 Ícones de categoria por índice
`theme_poli_get_categories()` atribui ícone pela **posição** da categoria — se a ordem
muda, o ícone muda. Substituir por mapeamento configurável (setting do tema ou custom
field da categoria) com fallback.

### 4.4 Miscelânea
- Links sociais do footer apontam para `#` — virar settings do tema; ocultar quando vazios.
- `© <ano>` já é corrigido via JS — ok, mas pode ser `date('Y')` server-side no contexto.

---

## 5. Fases de execução propostas

| Fase | Escopo | Itens |
|------|--------|-------|
| **1 — Quick wins** (1-2 dias) | Idioma, branding, login corporativo, badges "0", footer, chip quebrado, ícone spinner | 1.1–1.6, 2.7 (strings) |
| **2 — Dashboard do colaborador** | Continuar de onde parou, progresso, prazos, certificados, stats clicáveis | 2.1, 2.2 |
| **3 — Dentro do curso** | Atividades, completion, navegação prev/next, course index, página de atividade, Meus cursos | 2.3, 2.4 |
| **4 — A11y + perf** | Contraste, foco, reduced-motion, modo claro, queries | 3, 4.1–4.3 |
| **5 — Descoberta e extras** | Busca global, hero funcional, trilhas/áreas corporativas, social links | 2.5, 2.6, 4.4 |

**Critério de pronto por fase**: screenshots antes/depois nas 5 telas-chave (home
deslogada, login, dashboard, listagem, dentro do curso) + axe sem erros críticos +
`grunt` build limpo.
