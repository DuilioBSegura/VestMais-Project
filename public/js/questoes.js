/**
 * Vest+ Platform - Motor de Resolução Assíncrona de Questões (Sprint 3 & QA)
 * Vanilla JavaScript (Fetch API) sem dependências externas.
 * Otimizado para Acessibilidade (WCAG AA), Prevenção de Cliques Duplos e Resiliência de Rede.
 */

document.addEventListener('DOMContentLoaded', function () {
    const questoesForms = document.querySelectorAll('.form-questao');

    questoesForms.forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            // Bloqueio de cliques duplos / debouncing
            if (form.dataset.submitting === 'true') {
                return;
            }

            const questaoId = parseInt(this.getAttribute('data-questao-id'), 10);
            const apiUrl = this.getAttribute('data-api-url') || (window.VEST_API_URL || '/api/questoes/verificar');
            const submitBtn = this.querySelector('.btn-responder');
            const feedbackContainer = this.querySelector('.feedback-container');
            const alertaFeedback = this.querySelector('.alerta-resultado');
            const tituloFeedback = this.querySelector('.titulo-resultado');
            const textoGabarito = this.querySelector('.texto-gabarito');
            const textoExplicacao = this.querySelector('.texto-explicacao');
            const alternativasCards = this.querySelectorAll('.card-alternativa');

            // Verifica se uma alternativa foi selecionada
            const selectedRadio = this.querySelector('input[name="resposta"]:checked');
            if (!selectedRadio) {
                this.classList.add('border-warning');
                let avisoEl = this.querySelector('.aviso-selecao');
                if (!avisoEl) {
                    avisoEl = document.createElement('div');
                    avisoEl.className = 'aviso-selecao alert alert-warning py-2 px-3 small mt-2 mb-0 d-flex align-items-center gap-2 shadow-sm';
                    avisoEl.setAttribute('role', 'alert');
                    avisoEl.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> Selecione uma das alternativas de A a E antes de responder.';
                    this.querySelector('.alternativas-wrapper').after(avisoEl);
                }
                avisoEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                return;
            }

            // Remove avisos e erros prévios
            const avisoExistente = this.querySelector('.aviso-selecao');
            if (avisoExistente) {
                avisoExistente.remove();
            }
            const erroExistente = this.querySelector('.erro-requisicao');
            if (erroExistente) {
                erroExistente.remove();
            }

            const respostaSelecionada = selectedRadio.value.toUpperCase();

            // Ativa trava de envio e estado de carregamento acessível
            form.dataset.submitting = 'true';
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.setAttribute('aria-busy', 'true');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Verificando resposta...';

            try {
                // AbortController com timeout de 10s para resiliência de rede
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 10000);

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    signal: controller.signal,
                    body: JSON.stringify({
                        questao_id: questaoId,
                        resposta: respostaSelecionada
                    })
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.erro || `O servidor retornou status HTTP ${response.status}.`);
                }

                const data = await response.json();

                if (!data.sucesso) {
                    throw new Error(data.erro || 'Não foi possível validar a resposta no momento.');
                }

                // Desativa inputs da questão após responder
                this.querySelectorAll('input[name="resposta"]').forEach(function (radio) {
                    radio.disabled = true;
                });

                // Atualiza o visual e atributos de acessibilidade das alternativas
                alternativasCards.forEach(function (card) {
                    const letraCard = card.getAttribute('data-letra');
                    const badge = card.querySelector('.badge-alternativa');

                    card.classList.remove('border-primary', 'border-success', 'border-danger', 'bg-success-subtle', 'bg-danger-subtle');
                    card.style.cursor = 'default';

                    // Alternativa correta (sempre destacada em verde)
                    if (letraCard === data.gabarito) {
                        card.classList.add('border-success', 'bg-success-subtle');
                        card.setAttribute('aria-label', `Alternativa ${letraCard} - Gabarito Correto`);
                        if (badge) {
                            badge.className = 'badge bg-success text-white badge-alternativa';
                        }
                    }

                    // Alternativa incorreta marcada pelo aluno (destacada em vermelho)
                    if (!data.correta && letraCard === respostaSelecionada) {
                        card.classList.add('border-danger', 'bg-danger-subtle');
                        card.setAttribute('aria-label', `Alternativa ${letraCard} - Sua resposta incorreta`);
                        if (badge) {
                            badge.className = 'badge bg-danger text-white badge-alternativa';
                        }
                    }
                });

                // Prepara a caixa de feedback
                if (data.correta) {
                    alertaFeedback.className = 'alerta-resultado alert alert-success border-success-subtle shadow-sm mb-3';
                    tituloFeedback.innerHTML = '<i class="bi bi-check-circle-fill text-success fs-5 me-2"></i><strong>Excelente! Você acertou!</strong>';
                    textoGabarito.innerHTML = `Sua resposta (<strong>Letra ${respostaSelecionada}</strong>) é o gabarito oficial da questão.`;
                } else {
                    alertaFeedback.className = 'alerta-resultado alert alert-danger border-danger-subtle shadow-sm mb-3';
                    tituloFeedback.innerHTML = '<i class="bi bi-x-circle-fill text-danger fs-5 me-2"></i><strong>Resposta Incorreta</strong>';
                    textoGabarito.innerHTML = `Você marcou a <strong>Letra ${respostaSelecionada}</strong>, mas a alternativa correta é a <strong>Letra ${data.gabarito}</strong>.`;
                }

                textoExplicacao.textContent = data.explicacao || 'Nenhum comentário adicional disponível para esta questão.';

                // Revela a caixa de explicação com atributos ARIA para leitura imediata
                feedbackContainer.classList.remove('d-none');
                feedbackContainer.setAttribute('aria-live', 'polite');
                feedbackContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                // Atualiza o botão para indicar questão concluída
                submitBtn.disabled = true;
                submitBtn.removeAttribute('aria-busy');
                submitBtn.className = 'btn btn-secondary btn-sm disabled';
                submitBtn.innerHTML = '<i class="bi bi-check2-all me-1"></i> Questão Respondida';

                form.dataset.submitting = 'false';

            } catch (error) {
                // Destrava submissão e restaura botão em caso de falha
                form.dataset.submitting = 'false';
                submitBtn.disabled = false;
                submitBtn.removeAttribute('aria-busy');
                submitBtn.innerHTML = originalBtnHtml;

                let msgErro = error.name === 'AbortError' 
                    ? 'A requisição excedeu o tempo limite (10s). Verifique a conexão com o servidor.' 
                    : error.message;

                let erroContainer = this.querySelector('.erro-requisicao');
                if (!erroContainer) {
                    erroContainer = document.createElement('div');
                    erroContainer.className = 'erro-requisicao alert alert-danger py-2 px-3 small mt-3 mb-0 d-flex align-items-center justify-content-between shadow-sm';
                    erroContainer.setAttribute('role', 'alert');
                    erroContainer.setAttribute('aria-live', 'assertive');
                    this.appendChild(erroContainer);
                }
                erroContainer.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-6 text-danger"></i>
                        <span><strong>Falha ao verificar:</strong> ${msgErro}</span>
                    </div>
                    <button type="button" class="btn-close btn-sm" aria-label="Fechar" onclick="this.parentElement.remove()"></button>
                `;
            }
        });
    });

    // Melhora de Acessibilidade & UX: clique e teclado (Enter/Espaço) selecionam o radio
    document.querySelectorAll('.card-alternativa').forEach(function (card) {
        // Torna o card navegável pelo teclado se não tiver inputs desabilitados
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');

        const selectAlternative = function () {
            const radio = card.querySelector('input[type="radio"]');
            if (radio && !radio.disabled) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };

        card.addEventListener('click', selectAlternative);

        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectAlternative();
            }
        });
    });
});
