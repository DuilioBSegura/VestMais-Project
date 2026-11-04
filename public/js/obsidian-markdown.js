/**
 * ==============================================================================
 * Vest+ Platform - Obsidian Markdown Renderer Engine
 * ==============================================================================
 * Suporta:
 *  - GitHub Flavored Markdown (GFM) completo via Marked.js (Tabelas, Checklists, etc.)
 *  - Obsidian Callouts / Admonitions (> [!note], > [!tip], > [!warning], etc.)
 *  - Obsidian Highlight (==texto destacado==)
 *  - Fórmulas Matemáticas LaTeX via KaTeX ($inline$ e $$block$$)
 *  - Bloco de Códigos com Botão de Copiar
 *  - Listas de Tarefas Interativas (- [ ] / - [x])
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.ObsidianMarkdown = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    /**
     * Mapa de Metadados e Ícones para Obsidian Callouts
     */
    const CALLOUT_CONFIG = {
        note: {
            title: 'Nota',
            icon: 'bi-info-circle-fill',
            colorClass: 'callout-note'
        },
        info: {
            title: 'Informação',
            icon: 'bi-info-circle-fill',
            colorClass: 'callout-note'
        },
        tip: {
            title: 'Dica ENEM & Vestibulares',
            icon: 'bi-lightbulb-fill',
            colorClass: 'callout-tip'
        },
        dica: {
            title: 'Dica ENEM & Vestibulares',
            icon: 'bi-lightbulb-fill',
            colorClass: 'callout-tip'
        },
        enem: {
            title: 'Foco no ENEM',
            icon: 'bi-stars',
            colorClass: 'callout-tip'
        },
        important: {
            title: 'Ponto Importante',
            icon: 'bi-exclamation-circle-fill',
            colorClass: 'callout-important'
        },
        importante: {
            title: 'Ponto Importante',
            icon: 'bi-exclamation-circle-fill',
            colorClass: 'callout-important'
        },
        warning: {
            title: 'Atenção / Cuidado',
            icon: 'bi-exclamation-triangle-fill',
            colorClass: 'callout-warning'
        },
        atencao: {
            title: 'Atenção / Pegadinha',
            icon: 'bi-exclamation-triangle-fill',
            colorClass: 'callout-warning'
        },
        caution: {
            title: 'Cuidado',
            icon: 'bi-cone-striped',
            colorClass: 'callout-warning'
        },
        danger: {
            title: 'Perigo / Erro Comum',
            icon: 'bi-x-octagon-fill',
            colorClass: 'callout-danger'
        },
        erro: {
            title: 'Erro Frequente',
            icon: 'bi-x-octagon-fill',
            colorClass: 'callout-danger'
        },
        example: {
            title: 'Exemplo Resolvido',
            icon: 'bi-journal-bookmark-fill',
            colorClass: 'callout-example'
        },
        exemplo: {
            title: 'Exemplo Resolvido',
            icon: 'bi-journal-bookmark-fill',
            colorClass: 'callout-example'
        },
        question: {
            title: 'Questão Reflexiva',
            icon: 'bi-question-circle-fill',
            colorClass: 'callout-question'
        },
        duvida: {
            title: 'Pergunta Frequente',
            icon: 'bi-question-circle-fill',
            colorClass: 'callout-question'
        },
        success: {
            title: 'Sucesso / Conclusão',
            icon: 'bi-check-circle-fill',
            colorClass: 'callout-success'
        },
        check: {
            title: 'Gabarito',
            icon: 'bi-check-circle-fill',
            colorClass: 'callout-success'
        },
        quote: {
            title: 'Citação',
            icon: 'bi-quote',
            colorClass: 'callout-quote'
        }
    };

    /**
     * Pré-processador de Callouts no formato Obsidian:
     * > [!tipo] Título Opcional
     * > Linha 1
     * > Linha 2
     */
    function processObsidianCallouts(markdown) {
        if (!markdown) return '';

        const lines = markdown.split('\n');
        const outputLines = [];
        let inCallout = false;
        let currentType = '';
        let currentTitle = '';
        let calloutContent = [];

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            const calloutMatch = line.match(/^>\s*\[!([a-zA-Z0-9_\-]+)\](?:\s*(.*))?$/);

            if (calloutMatch) {
                // Se já estávamos em outro callout, fecha o anterior
                if (inCallout) {
                    outputLines.push(renderCalloutHtml(currentType, currentTitle, calloutContent.join('\n')));
                    calloutContent = [];
                }

                inCallout = true;
                currentType = calloutMatch[1].toLowerCase();
                currentTitle = (calloutMatch[2] || '').trim();
                continue;
            }

            if (inCallout) {
                if (line.startsWith('>')) {
                    // Remove o '>' e espaço inicial do conteúdo do callout
                    const cleanLine = line.replace(/^>\s?/, '');
                    calloutContent.push(cleanLine);
                } else if (line.trim() === '') {
                    // Linha em branco vazia dentro do callout ou fim do bloco
                    if (i + 1 < lines.length && lines[i + 1].startsWith('>')) {
                        calloutContent.push('');
                    } else {
                        outputLines.push(renderCalloutHtml(currentType, currentTitle, calloutContent.join('\n')));
                        outputLines.push('');
                        inCallout = false;
                        calloutContent = [];
                    }
                } else {
                    // Fim do bloco de callout
                    outputLines.push(renderCalloutHtml(currentType, currentTitle, calloutContent.join('\n')));
                    outputLines.push(line);
                    inCallout = false;
                    calloutContent = [];
                }
            } else {
                outputLines.push(line);
            }
        }

        // Se o arquivo terminou dentro do callout
        if (inCallout) {
            outputLines.push(renderCalloutHtml(currentType, currentTitle, calloutContent.join('\n')));
        }

        return outputLines.join('\n');
    }

    /**
     * Gera o HTML elegante de um Callout estilo Obsidian
     */
    function renderCalloutHtml(type, customTitle, innerMarkdown) {
        const config = CALLOUT_CONFIG[type] || {
            title: type.charAt(0).toUpperCase() + type.slice(1),
            icon: 'bi-info-circle-fill',
            colorClass: 'callout-note'
        };

        const finalTitle = customTitle && customTitle.trim() !== '' ? customTitle : config.title;
        
        // Marcador especial para ser processado no pós-render
        // Renderizamos o conteúdo interno com o parser posteriormente
        const contentPlaceholder = encodeURIComponent(innerMarkdown);

        return `\n<div class="obsidian-callout ${config.colorClass}" data-callout-content="${contentPlaceholder}">
    <div class="callout-header">
        <i class="bi ${config.icon} callout-icon"></i>
        <span class="callout-title">${escapeHtml(finalTitle)}</span>
    </div>
    <div class="callout-body">__CALLOUT_CONTENT_${contentPlaceholder}__</div>
</div>\n`;
    }

    /**
     * Pré-processador de Obsidian Highlight (==texto destacado==)
     */
    function processObsidianHighlights(text) {
        if (!text) return '';
        return text.replace(/==([^=\n]+)==/g, '<mark class="obsidian-highlight">$1</mark>');
    }

    /**
     * Pré-processador de Task Lists Obsidian / GFM
     */
    function processTaskLists(text) {
        if (!text) return '';
        // Substitui - [ ] por checkbox desmarcado e - [x] por marcado
        let res = text.replace(/^- \[ \] (.*)$/gm, '<li class="obsidian-task-item"><input type="checkbox" disabled class="obsidian-task-check"> <span>$1</span></li>');
        res = res.replace(/^- \[(?:x|X)\] (.*)$/gm, '<li class="obsidian-task-item task-done"><input type="checkbox" checked disabled class="obsidian-task-check"> <span class="task-text-done">$1</span></li>');
        return res;
    }

    /**
     * Sanitizador rápido de HTML
     */
    function escapeHtml(str) {
        return (str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /**
     * Função Principal de Renderização
     * @param {string} rawMarkdown Texto bruto em Markdown
     * @param {HTMLElement|string} targetElement Elemento destino onde será inserido o HTML ou null
     * @returns {string} HTML renderizado
     */
    function render(rawMarkdown, targetElement) {
        if (!rawMarkdown || rawMarkdown.trim() === '') {
            const emptyHtml = '<p class="text-muted fst-italic">Comece a digitar no editor para visualizar a formatação em tempo real...</p>';
            if (targetElement) {
                const el = typeof targetElement === 'string' ? document.getElementById(targetElement) : targetElement;
                if (el) el.innerHTML = emptyHtml;
            }
            return emptyHtml;
        }

        // 1. Processa Destaques Obsidian (==texto==)
        let processed = processObsidianHighlights(rawMarkdown);

        // 2. Processa Callouts Obsidian (> [!tip] etc.)
        processed = processObsidianCallouts(processed);

        // 3. Processa Checklists (- [ ])
        processed = processTaskLists(processed);

        // 4. Renderiza Markdown com Marked.js (se disponível) ou fallback robusto
        let htmlResult = '';
        if (typeof marked !== 'undefined' && typeof marked.parse === 'function') {
            marked.setOptions({
                gfm: true,
                breaks: true,
                pedantic: false
            });
            htmlResult = marked.parse(processed);
        } else {
            // Fallback nativo rápido se Marked ainda não tiver carregado
            htmlResult = processed
                .replace(/^### (.*$)/gim, '<h4 class="fw-bold mt-3 mb-2">$1</h4>')
                .replace(/^## (.*$)/gim, '<h3 class="fw-bold mt-4 mb-2 border-bottom pb-1">$1</h3>')
                .replace(/^# (.*$)/gim, '<h2 class="fw-bold mt-4 mb-3">$1</h2>')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`([^`]+)`/g, '<code class="obsidian-inline-code">$1</code>')
                .replace(/\n\n/g, '</p><p class="mb-3">');
            htmlResult = '<p class="mb-3">' + htmlResult + '</p>';
        }

        // 5. Restaura e renderiza o conteúdo interno de cada Callout
        htmlResult = htmlResult.replace(/__CALLOUT_CONTENT_([a-zA-Z0-9%_-]+)__/g, function (_, encoded) {
            try {
                const rawCalloutInner = decodeURIComponent(encoded);
                if (typeof marked !== 'undefined' && typeof marked.parse === 'function') {
                    return marked.parse(rawCalloutInner);
                }
                return rawCalloutInner.replace(/\n/g, '<br>');
            } catch (e) {
                return '';
            }
        });

        // 6. Aplica no elemento alvo caso fornecido
        const container = typeof targetElement === 'string' 
            ? document.getElementById(targetElement) 
            : targetElement;

        if (container) {
            container.innerHTML = htmlResult;

            // 7. Renderiza Fórmulas KaTeX no elemento
            if (typeof renderMathInElement === 'function') {
                renderMathInElement(container, {
                    delimiters: [
                        { left: '$$', right: '$$', display: true },
                        { left: '$', right: '$', display: false },
                        { left: '\\(', right: '\\)', display: false },
                        { left: '\\[', right: '\\]', display: true }
                    ],
                    throwOnError: false,
                    ignoredTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
                });
            }

            // 8. Adiciona botões de cópia elegante em blocos de código
            setupCodeCopyButtons(container);
        }

        return htmlResult;
    }

    /**
     * Adiciona botão "Copiar" elegante estilo Obsidian nos blocos de código
     */
    function setupCodeCopyButtons(container) {
        if (!container) return;
        const preBlocks = container.querySelectorAll('pre');

        preBlocks.forEach(function (pre) {
            if (pre.querySelector('.btn-copy-code')) return;

            const codeEl = pre.querySelector('code');
            const codeText = codeEl ? codeEl.innerText : pre.innerText;

            const copyBtn = document.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'btn-copy-code';
            copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> Copiar';
            copyBtn.title = 'Copiar código para a área de transferência';

            copyBtn.addEventListener('click', function () {
                navigator.clipboard.writeText(codeText).then(function () {
                    copyBtn.innerHTML = '<i class="bi bi-check2 text-success"></i> Copiado!';
                    copyBtn.classList.add('copied');
                    setTimeout(function () {
                        copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> Copiar';
                        copyBtn.classList.remove('copied');
                    }, 2000);
                }).catch(function () {
                    copyBtn.innerText = 'Falha ao copiar';
                });
            });

            pre.style.position = 'relative';
            pre.appendChild(copyBtn);
        });
    }

    return {
        render: render,
        CALLOUT_CONFIG: CALLOUT_CONFIG
    };
}));
