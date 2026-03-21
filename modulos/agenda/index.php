<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Agenda de Escutas e Processos Circulares</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/agenda.css" />
    <style>
        /* Estilos para eventos de processos circulares */
        .event-realizado {
            background-color: #e8f5e9 !important;
            border-left: 4px solid #4caf50 !important;
        }

        .event-falta {
            background-color: #ffebee !important;
            border-left: 4px solid #f44336 !important;
        }

        .event-agendado {
            background-color: #e3f2fd !important;
            border-left: 4px solid #2196f3 !important;
        }

        .event-cancelado {
            background-color: #f5f5f5 !important;
            border-left: 4px solid #9e9e9e !important;
            text-decoration: line-through;
        }

        .event-solicitacao {
            background-color: #fff3e0 !important;
            border-left: 4px solid #ff9800 !important;
        }

        .event-pre-circulo {
            background-color: #e0f7fa !important;
            border-left: 4px solid #00bcd4 !important;
        }

        /* Estilo para eventos da agenda */
        .event-agenda {
            background-color: #e6e6fa !important;
            border-left: 4px solid #8a2be2 !important;
        }

        /* Estilos para a seção de status do círculo */
        .status-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid #2c3e50;
        }

        .status-btn {
            flex: 1;
            min-width: 120px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="header text-center">
        <div class="container position-relative">
            <a href="../../painel.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
            </a>

            <h1><i class="fas fa-calendar-check me-3"></i> Calendário de Escutas e Processos Circulares</h1>
            <p class="lead">Controle de agendamentos para atendimentos psicológicos e processos circulares</p>
        </div>
    </div>

    <div class="container">
        <div class="mb-4">
            <h2><i class="far fa-calendar-alt me-2"></i> Calendário de Agendamentos</h2>
        </div>

        <div id="debug" class="alert alert-info mb-3" style="display: none;">
            <strong>Debug:</strong> <span id="debugMsg"></span>
        </div>

        <div id="calendar"></div>
    </div>

    <!-- Modal Novo Evento -->
    <div class="modal fade" id="novoEventoModal" tabindex="-1" aria-labelledby="novoEventoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoEventoModalLabel">
                        <i class="fas fa-plus-circle me-2"></i> Novo Agendamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNovoEvento">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="novoNome" class="form-label">
                                        <i class="fas fa-user me-1"></i> Nome Completo
                                    </label>
                                    <input type="text" class="form-control" id="novoNome" required />
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="novaData" class="form-label">
                                                <i class="far fa-calendar me-1"></i> Data
                                            </label>
                                            <input type="date" class="form-control" id="novaData" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="novaHora" class="form-label">
                                                <i class="far fa-clock me-1"></i> Hora
                                            </label>
                                            <input type="time" class="form-control" id="novaHora" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="novoFacilitador" class="form-label">
                                        <i class="fas fa-user-md me-1"></i> Facilitador(a)
                                    </label>
                                    <select class="form-control" id="novoFacilitador" required>
                                        <?php
                                        if ($_SESSION['usuario_tipo'] === 'Facilitador') {
                                            echo '<option value="' . htmlspecialchars($_SESSION['usuario_nome']) . '" selected>' . htmlspecialchars($_SESSION['usuario_nome']) . '</option>';
                                        } else {
                                            echo '<option value="">Selecione...</option>';
                                            echo '<option value="Katia">Katia</option>';
                                            echo '<option value="Camilla">Camilla</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="novaPrioridade" class="form-label">
                                        <i class="fas fa-exclamation-circle me-1"></i> Prioridade
                                    </label>
                                    <select class="form-control" id="novaPrioridade" required>
                                        <option value="">Selecione...</option>
                                        <option value="Alta">Alta</option>
                                        <option value="Média">Média</option>
                                        <option value="Baixa">Baixa</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="novaObservacoes" class="form-label">
                                        <i class="fas fa-sticky-note me-1"></i> Observações
                                    </label>
                                    <textarea class="form-control" id="novaObservacoes" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Salvar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detalhes -->
    <div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventoModalLabel">
                        <i class="fas fa-info-circle me-2"></i> Detalhes do Evento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="fas fa-user me-2"></i> Nome
                                </div>
                                <div class="detail-value fs-5" id="modalNome"></div>
                            </div>

                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="far fa-calendar me-2"></i> Data
                                </div>
                                <div class="detail-value fs-5" id="modalData"></div>
                            </div>

                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="far fa-clock me-2"></i> Hora
                                </div>
                                <div class="detail-value fs-5" id="modalHora"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="fas fa-user-md me-2"></i>Facilitador
                                </div>
                                <div class="detail-value fs-5" id="modalFacilitador"></div>
                            </div>

                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="fas fa-exclamation-circle me-2"></i> Prioridade
                                </div>
                                <div class="detail-value fs-5" id="modalPrioridade"></div>
                            </div>

                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="fas fa-tasks me-2"></i> Status / Situação
                                </div>
                                <div class="detail-value fs-5" id="modalStatus"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção para alterar status do círculo (apenas para processos circulares) -->
                    <div class="status-section" id="statusSection" style="display: none;">
                        <h6><i class="fas fa-sync-alt me-2"></i> Alterar Status do Círculo</h6>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button class="btn btn-success status-btn" data-status="realizado">
                                <i class="fas fa-check me-1"></i> Realizado
                            </button>
                            <button class="btn btn-warning status-btn" data-status="falta">
                                <i class="fas fa-times me-1"></i> Faltou
                            </button>
                            <button class="btn btn-info status-btn" data-status="agendado">
                                <i class="fas fa-calendar me-1"></i> Agendado
                            </button>
                            <button class="btn btn-danger status-btn" data-status="cancelado">
                                <i class="fas fa-ban me-1"></i> Cancelar
                            </button>
                        </div>
                    </div>

                    <div class="detail-item mt-4">
                        <div class="detail-label">
                            <i class="fas fa-sticky-note me-2"></i> Observações
                        </div>
                        <div class="detail-value" id="modalObservacoes"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="editarLink" class="btn btn-warning" style="display:none;">
                        <i class="fas fa-edit me-1"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" id="excluirBtn" style="display:none;">
                        <i class="fas fa-trash-alt me-1"></i> Excluir
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/pt-br.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let currentEventId = null;
        let currentEventType = null;
        let calendar; // Variável global para o calendário

        function showDebug(msg) {
            document.getElementById('debugMsg').innerText = msg;
            document.getElementById('debug').style.display = 'block';
            console.log('Debug:', msg);
        }

        // Função para mostrar/ocultar a seção de status
        function toggleStatusSection(show) {
            const statusSection = document.getElementById('statusSection');
            statusSection.style.display = show ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            showDebug('Iniciando carregamento do calendário...');

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                height: 600,
                events: '../agenda/agenda.php?action=list',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                eventDisplay: 'block',
                selectable: true,
                dateClick: function(info) {
                    document.getElementById('novaData').value = info.dateStr;
                    const modal = new bootstrap.Modal(document.getElementById('novoEventoModal'));
                    modal.show();
                },
                loading: function(isLoading) {
                    if (isLoading) {
                        showDebug('Carregando eventos...');
                    } else {
                        showDebug('Eventos carregados com sucesso!');
                    }
                },
                eventDidMount: function(info) {
                    const eventType = info.event.extendedProps.type;
                    if (eventType === 'agenda') {
                        const prioridade = info.event.extendedProps.prioridade;
                        if (prioridade === 'Alta') {
                            info.el.classList.add('priority-high');
                        } else if (prioridade === 'Média') {
                            info.el.classList.add('priority-medium');
                        } else {
                            info.el.classList.add('priority-low');
                        }
                    } else if (eventType === 'processo_circular') {
                        const situacao = info.event.extendedProps.situacao;
                        if (situacao === 'Círculo realizado') {
                            info.el.classList.add('event-realizado');
                        } else if (situacao === 'Faltou') {
                            info.el.classList.add('event-falta');
                        } else if (situacao === 'Cancelado') {
                            info.el.classList.add('event-cancelado');
                        } else if (situacao === 'Agendado') {
                            info.el.classList.add('event-agendado');
                        } else if (situacao === 'Solicitação') {
                            info.el.classList.add('event-solicitacao');
                        } else if (situacao === 'Pré-círculo') {
                            info.el.classList.add('event-pre-circulo');
                        }
                    }
                },
                eventClick: function(info) {
                    console.log('Evento clicado:', info.event);
                    console.log('Tipo do evento:', info.event.extendedProps.type);

                    currentEventId = info.event.id;
                    const props = info.event.extendedProps;
                    currentEventType = props.type;

                    // Limpar modal
                    document.getElementById('modalNome').innerText = '';
                    document.getElementById('modalData').innerText = '';
                    document.getElementById('modalHora').innerText = '';
                    document.getElementById('modalFacilitador').innerText = '';
                    document.getElementById('modalPrioridade').innerText = '';
                    document.getElementById('modalStatus').innerText = '';
                    document.getElementById('modalObservacoes').innerText = '';
                    document.getElementById('editarLink').style.display = 'none';
                    document.getElementById('excluirBtn').style.display = 'none';

                    if (currentEventType === 'agenda') {
                        document.getElementById('eventoModalLabel').innerHTML = '<i class="fas fa-info-circle me-2"></i> Detalhes do Agendamento de Escuta';
                        document.getElementById('modalNome').innerText = props.titulo;
                        document.getElementById('modalData').innerText = props.data_agendamento;
                        document.getElementById('modalHora').innerText = props.hora_agendamento;
                        document.getElementById('modalFacilitador').innerText = props.participantes;
                        document.getElementById('modalObservacoes').innerText = props.observacoes || 'Nenhuma observação';

                        const prioridadeElement = document.getElementById('modalPrioridade');
                        prioridadeElement.innerText = props.prioridade;
                        prioridadeElement.className = 'detail-value fs-5';
                        if (props.prioridade === 'Alta') {
                            prioridadeElement.classList.add('text-danger');
                        } else if (props.prioridade === 'Média') {
                            prioridadeElement.classList.add('text-warning');
                        } else {
                            prioridadeElement.classList.add('text-success');
                        }

                        const statusElement = document.getElementById('modalStatus');
                        statusElement.innerText = props.status;
                        statusElement.className = 'detail-value fs-5 badge bg-primary';

                        document.getElementById('editarLink').href = `../escuta/editar.php?id=${props.id}`;
                        document.getElementById('editarLink').style.display = 'inline-block';
                        document.getElementById('excluirBtn').style.display = 'inline-block';

                        // Excluir via AJAX
                        document.getElementById('excluirBtn').onclick = function() {
                            if (confirm('Tem certeza que deseja excluir este agendamento?')) {
                                axios.post('../agenda/agenda.php?action=delete', {
                                        id: props.id
                                    })
                                    .then(() => {
                                        calendar.refetchEvents();
                                        bootstrap.Modal.getInstance(document.getElementById('eventoModal')).hide();
                                    })
                                    .catch(error => {
                                        showDebug('Erro ao excluir: ' + error.message);
                                        console.error(error);
                                    });
                            }
                        };

                        // Ocultar seção de status para eventos de agenda
                        toggleStatusSection(false);
                    } else if (currentEventType === 'processo_circular') {
                        document.getElementById('eventoModalLabel').innerHTML = '<i class="fas fa-info-circle me-2"></i> Detalhes do Processo Circular';
                        document.getElementById('modalNome').innerText = 'Processo: ' + props.numero_processo;

                        // Formatar data e hora
                        const dataEvento = new Date(props.data_circulo);
                        document.getElementById('modalData').innerText = dataEvento.toLocaleDateString('pt-BR');
                        document.getElementById('modalHora').innerText = dataEvento.toLocaleTimeString('pt-BR', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        document.getElementById('modalFacilitador').innerText = props.facilitador || props.Facilitador;
                        document.getElementById('modalObservacoes').innerText = 'Situação: ' + props.situacao;

                        document.getElementById('modalPrioridade').innerText = 'N/A';
                        document.getElementById('modalPrioridade').className = 'detail-value fs-5 text-muted';

                        const statusElement = document.getElementById('modalStatus');
                        statusElement.innerText = props.situacao;
                        statusElement.className = 'detail-value fs-5 badge';
                        if (props.situacao === 'Círculo realizado') statusElement.classList.add('bg-success');
                        else if (props.situacao === 'Faltou') statusElement.classList.add('bg-warning');
                        else if (props.situacao === 'Cancelado') statusElement.classList.add('bg-danger');
                        else if (props.situacao === 'Agendado') statusElement.classList.add('bg-info');
                        else if (props.situacao === 'Solicitação') statusElement.classList.add('bg-secondary');
                        else if (props.situacao === 'Pré-círculo') statusElement.classList.add('bg-primary');

                        document.getElementById('editarLink').href = `../processos_circulares/editar.php?id=${props.id}`;
                        document.getElementById('editarLink').style.display = 'inline-block';

                        // Excluir via link com confirmação
                        const excluirBtn = document.getElementById('excluirBtn');
                        excluirBtn.style.display = 'inline-block';
                        excluirBtn.removeAttribute('onclick');
                        excluirBtn.removeAttribute('href');
                        excluirBtn.onclick = function() {
                            if (confirm('Tem certeza que deseja excluir este processo circular?')) {
                                window.location.href = `../processos_circulares/excluir.php?id=${props.id}`;
                            }
                        };

                        // Mostrar seção de status para processos circulares
                        toggleStatusSection(true);
                    }

                    const modal = new bootstrap.Modal(document.getElementById('eventoModal'));
                    modal.show();
                }
            });

            calendar.render();
            showDebug('Calendário renderizado!');

            // Formulário para novo evento
            document.getElementById('formNovoEvento').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = {
                    nome_completo: document.getElementById('novoNome').value,
                    participantes: document.getElementById('novoFacilitador').value,
                    data_agendamento: document.getElementById('novaData').value,
                    hora_agendamento: document.getElementById('novaHora').value,
                    prioridade: document.getElementById('novaPrioridade').value,
                    observacoes: document.getElementById('novaObservacoes').value,
                    status: 'Agendado'
                };

                showDebug('Enviando novo agendamento...');
                console.log('Dados sendo enviados:', formData);

                axios.post('../agenda/agenda.php?action=create', formData, {
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Resposta completa:', response);
                        console.log('Dados da resposta:', response.data);

                        // Verificar se a resposta tem os dados esperados
                        if (response.data && (response.data.id || response.data.success)) {
                            showDebug('Agendamento criado com sucesso!');
                            calendar.refetchEvents();

                            const modal = bootstrap.Modal.getInstance(document.getElementById('novoEventoModal'));
                            modal.hide();

                            document.getElementById('formNovoEvento').reset();
                        } else {
                            const errorMsg = response.data && response.data.error ? response.data.error : 'Resposta inválida do servidor';
                            showDebug('Erro ao criar agendamento: ' + errorMsg);
                            console.error('Resposta inesperada:', response.data);
                        }
                    })
                    .catch(error => {
                        showDebug('Erro ao criar agendamento: ' + error.message);
                        console.error('Erro completo:', error);

                        // Mostrar mais detalhes do erro se disponível
                        if (error.response) {
                            console.error('Status:', error.response.status);
                            console.error('Headers:', error.response.headers);
                            console.error('Data:', error.response.data);
                            showDebug('Erro do servidor: ' + (error.response.data || error.response.statusText));
                        }
                    });
            });

            // Evento para botões de status
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const status = this.dataset.status;
                    let situacao = 'Agendado';

                    if (status === 'realizado') situacao = 'Círculo realizado';
                    else if (status === 'falta') situacao = 'Faltou';
                    else if (status === 'cancelado') situacao = 'Cancelado';

                    // Atualizar situação no banco de dados
                    fetch('../processos_circulares/atualizar_situacao.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id=${currentEventId}&situacao=${situacao}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Status atualizado com sucesso!');
                                // Fechar o modal e recarregar os eventos
                                bootstrap.Modal.getInstance(document.getElementById('eventoModal')).hide();
                                calendar.refetchEvents();
                            } else {
                                alert('Erro ao atualizar: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Erro ao atualizar status');
                        });
                });
            });
        });
    </script>
</body>

</html>