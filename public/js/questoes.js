/**
 * Vest+ Platform - Motor de Resolução Assíncrona de Questões (Sprint 3)
 * Vanilla JavaScript (Fetch API) sem dependências externas.
 */

document.addEventListener('DOMContentLoaded', function () {
    const questoesForms = document.querySelectorAll('.form-questao');

    questoesForms.forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

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
                // Alerta suave caso o aluno não tenha marcado nenhuma opção
                this.classList.add('border-warning');
                let avisoEl = this.querySelector('.aviso-selecao');
                if (!avisoEl) {
                    avisoEl = document.createElement('div');
                    avisoEl.className = 'aviso-selecao alert alert-warning py-2 px-3 small mt-2 mb-0 d-flex align-items-center gap-2';
                    avisoEl.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> Selecione uma das alternativas de A a E antes de responder.';
                    this.querySelector('.alternativas-wrapper').after(avisoEl);
                }
                return;
            }

            // Remove avisos prévios
            const avisoExistente = this.querySelector('.aviso-selecao');
            if (avisoExistente) {
                avisoExistente.remove();
            }

            const respostaSelecionada = selectedRadio.value.toUpperCase();

            // Estado de Carregamento (Loading State)
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Verificando resposta...';

            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        questao_id: questaoId,
                        resposta: respostaSelecionada
                    })
                });

                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.erro || `Falha na requisição (HTTP ${response.status})`);
                }

                const data = await response.json();

                if (!data.sucesso) {
                    throw new Error(data.erro || 'Não foi possível validar a resposta.');
                }

                // Desativa inputs da questão após responder para focar na explicação
                this.querySelectorAll('input[name="resposta"]').forEach(function (radio) {
                    radio.disabled = true;
                });

                // Atualiza o visual das alternativas
                alternativasCards.forEach(function (card) {
                    const letraCard = card.getAttribute('data-letra');
                    const badge = card.querySelector('.badge-alternativa');

                    card.classList.remove('border-primary', 'border-success', 'border-danger', 'bg-success-subtle', 'bg-danger-subtle');

                    // Alternativa correta (sempre destacada em verde)
                    if (letraCard === data.gabarito) {
                        card.classList.add('border-success', 'bg-success-subtle');
                        if (badge) {
                            badge.className = 'badge bg-success text-white badge-alternativa';
                        }
                    }

                    // Alternativa incorreta marcada pelo aluno (destacada em vermelho)
                    if (!data.correta && letraCard === respostaSelecionada) {
                        card.classList.add('border-danger', 'bg-danger-subtle');
                        if (badge) {
                            badge.className = 'badge bg-danger text-white badge-alternativa';
                        }
                    }
                });

                // Prepara a caixa de feedback
                if (data.correta) {
                    alertaFeedback.className = 'alert alert-success border-success-subtle shadow-sm mb-3';
                    tituloFeedback.innerHTML = '<i class="bi bi-check-circle-fill text-success fs-5 me-2"></i><strong>Excelente! Você acertou!</strong>';
                    textoGabarito.innerHTML = `Sua resposta (<strong>Letra ${respostaSelecionada}</strong>) é o gabarito oficial da questão.`;
                } else {
                    alertaFeedback.className = 'alert alert-danger border-danger-subtle shadow-sm mb-3';
                    tituloFeedback.innerHTML = '<i class="bi bi-x-circle-fill text-danger fs-5 me-2"></i><strong>Resposta Incorreta</strong>';
                    textoGabarito.innerHTML = `Você marcou a <strong>Letra ${respostaSelecionada}</strong>, mas a alternativa correta é a <strong>Letra ${data.gabarito}</strong>.`;
                }

                textoExplicacao.textContent = data.explicacao || 'Nenhum comentário adicional disponível.';

                // Revela a caixa de explicação
                feedbackContainer.classList.remove('d-none');
                feedbackContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                // Atualiza o botão para indicar questão concluída
                submitBtn.disabled = true;
                submitBtn.className = 'btn btn-secondary btn-sm disabled';
                submitBtn.innerHTML = '<i class="bi bi-check2-all me-1"></i> Questão Respondida';

            } catch (error) {
                // Tratamento de erro de rede ou validação
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                let erroContainer = this.querySelector('.erro-requisicao');
                if (!erroContainer) {
                    erroContainer = document.createElement('div');
                    erroContainer.className = 'erro-requisicao alert alert-danger py-2 px-3 small mt-3 mb-0';
                    this.appendChild(erroContainer);
                }
                erroContainer.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> Erro: ${error.message}`;
            }
        });
    });

    // Melhora interativa: ao clicar em qualquer parte do card da alternativa, seleciona o radio
    document.querySelectorAll('.card-alternativa').forEach(function (card) {
        card.addEventListener('click', function (e) {
            const radio = this.querySelector('input[type="radio"]');
            if (radio && !radio.disabled) {
                radio.checked = true;
                // Dispara evento change para atualizar estilos se houver listeners
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
});
