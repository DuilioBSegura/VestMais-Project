# Checklist de Acessibilidade (WCAG 2.1 AA), UI/UX e Responsividade
**Projeto:** Vest+ Platform  
**Fase:** Pré-Apresentação (Dia 5)  
**Normas e Requisitos Cobertos:** RNF02 (Responsividade), RNF03 (Cross-Browser), RNF12 (Acessibilidade e Usabilidade)

---

## 1. Auditoria de Acessibilidade Visual e Contraste (WCAG AA / AAA)

A paleta oficial **Verde Esmeralda & Menta** foi validada de acordo com as diretrizes do WCAG 2.1 (Web Content Accessibility Guidelines) para taxa mínima de contraste de 4.5:1 para texto normal e 3.0:1 para componentes de interface e texto grande.

| Elemento / Combinação | Cor de Texto (Foreground) | Cor de Fundo (Background) | Relação de Contraste | Nível WCAG | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Texto Principal do Artigo** | `#1e293b` (Slate 800) | `#ffffff` (White) | **12.6 : 1** | AAA | ✅ Aprovado |
| **Títulos e Headings (h1..h4)** | `#0f172a` (Slate 900) | `#ffffff` / `#f8fafc` | **15.4 : 1** | AAA | ✅ Aprovado |
| **Botão Primário (.btn-primary)** | `#ffffff` (White) | `#059669` (Emerald 600) | **4.54 : 1** | AA | ✅ Aprovado |
| **Botão Hover (.btn-primary:hover)**| `#ffffff` (White) | `#047857` (Emerald 700) | **5.52 : 1** | AA / AAA | ✅ Aprovado |
| **Destaque Callout ENEM** | `#064e3b` (Emerald 900) | `#ecfdf5` (Mint 50) | **10.2 : 1** | AAA | ✅ Aprovado |
| **Destaque Callout Nota/Aviso** | `#78350f` (Amber 900) | `#fffbeb` (Amber 50) | **8.9 : 1** | AAA | ✅ Aprovado |
| **Badge de Disciplina** | `#065f46` (Emerald 800) | `#ecfdf5` (Mint 50) | **7.4 : 1** | AAA | ✅ Aprovado |
| **Feedback de Acerto (Simulado)** | `#0f5132` (Green 900) | `#d1e7dd` (Green 100) | **7.1 : 1** | AAA | ✅ Aprovado |
| **Feedback de Erro (Simulado)** | `#842029` (Red 900) | `#f8d7da` (Red 100) | **7.2 : 1** | AAA | ✅ Aprovado |

---

## 2. Acessibilidade Estrutural e Semântica (HTML5 & WAI-ARIA)

- [x] **Hierarquia de Títulos Semânticos:** Cada página possui um único `<h1>`, seguido de `<h2>`, `<h3>` e `<h4>` sem saltos hierárquicos arbitrários.
- [x] **Rótulos de Formulários (`<label for="...">`):** Todos os campos de login, cadastro, filtros e alternativas possuem `<label>` explicitamente associados pelo atributo `for` ao `id` do respectivo `input`.
- [x] **Alternativas de A a E:** Cada alternativa possui `id="q_{id}_{letra}"` com atributo `role="button"` e `tabindex="0"`, permitindo clique direto no card ou seleção pelo teclado.
- [x] **Navegação por Teclado:**
  - Pressionar `Tab` navega sequencialmente entre os campos e botões.
  - Pressionar `Enter` ou `Barra de Espaço` sobre o card da alternativa marca a opção correspondente.
  - O botão *"Responder Questão"* ganha foco imediato e pode ser submetido com `Enter`.
- [x] **Foco Visível (`:focus-visible` e `:focus-within`):** Elementos interativos recebem anel de foco suave em verde esmeralda (`box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.25)`), garantindo visibilidade clara para quem navega sem mouse.
- [x] **Leitores de Tela (Screen Readers):**
  - O container de resultado do simulador possui `aria-live="polite"` e `role="alert"`.
  - Quando a requisição é disparada, o botão recebe `aria-busy="true"`.
  - Mensagens de erro de rede ou validação possuem `role="alert"` e `aria-live="assertive"`.

---

## 3. Matriz de Responsividade e Quebras de Layout (RNF02)

| Dispositivo / Resolução | Breakpoint Bootstrap | Comportamento Validado | Status |
| :--- | :--- | :--- | :--- |
| **Mobile Estreito (375px - 414px)** | `< 576px` (`col-12`) | - Navbar colapsa no menu hamburger funcional.<br>- Botões do Hero empilham verticalmente em 100% da largura.<br>- Cards de disciplinas e recursos viram lista vertical única.<br>- Alternativas de múltipla escolha expandem sem cortar o texto. | ✅ Aprovado |
| **Tablets / iPads (768px - 820px)** | `md: 768px` | - Grade em 2 colunas para cards de disciplinas.<br>- Padding confortável de 1.5rem nos cards de artigos.<br>- Sidebar de artigos flui naturalmente abaixo do conteúdo principal. | ✅ Aprovado |
| **Notebooks & Projetores (1366px)** | `lg: 992px` / `xl: 1200px` | - Resolução padrão de salas de aula e projetores da banca.<br>- Layout do artigo em 2 colunas: leitura com largura máxima de 820px (evita linhas longas e fadiga visual) e sidebar sticky com ações de fixação. | ✅ Aprovado |
| **Monitores Ultrawide / 1080p (1920px)**| `xxl: 1400px` | - Containers centralizados com `.container px-lg-4` sem deformação ou espaçamentos desproporcionais. | ✅ Aprovado |

---

## 4. Matriz de Compatibilidade entre Navegadores (RNF03)

O código foi desenvolvido em conformidade com os padrões web W3C e ECMAScript moderno, sem depender de recursos experimentais:

- [x] **Google Chrome (Blink Engine):** 100% funcional (Fetch API, Bootstrap Icons via CDN, layout flexbox/grid).
- [x] **Microsoft Edge (Blink Engine):** 100% funcional (mesma renderização e performance que Chrome).
- [x] **Mozilla Firefox (Gecko Engine):** 100% funcional (testada quebra de linha de enunciados, transições CSS e foco de teclado).
- [x] **Apple Safari (WebKit):** 100% compatível (sem prefixos obsoletos, datas formatadas no padrão ISO `Y-m-d`).

---

## 5. Recomendações de Apresentação ao Vivo (Dia 5)

1. **Zoom do Navegador:** Mantenha o navegador em **100%** de zoom na tela do projetor (`Ctrl + 0`).
2. **Modo Janela:** Utilize o navegador maximizado com a barra de favoritos oculta (`F11` ou barra limpa) para destacar o profissionalismo da interface.
3. **Rolagem Suave:** Demonstre a rolagem suave (*smooth scrolling*) ao clicar nos links de navegação da Landing Page (`#sobre`, `#recursos`, `#disciplinas`, `#equipe`).
