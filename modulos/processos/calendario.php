<?php
// processos_circulares/calendario.php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'recepcao', 'psicologa']);

// Verificar se há um processo_id na URL
$processo_id = isset($_GET['processo_id']) ? intval($_GET['processo_id']) : null;

// Consulta para buscar todos os processos (para o select do formulário)
$sql_processos = "SELECT id, numero_processo FROM processos_circulares_total";
$result_processos = $conn->query($sql_processos);
$processos = [];
if ($result_processos) {
    $processos = $result_processos->fetch_all(MYSQLI_ASSOC);
} else {
    die("Erro na consulta de processos: " . $conn->error);
}

// Consulta usando MySQLi para eventos
$sql = "SELECT id, numero_processo, facilitador, data_circulo, situacao 
        FROM processos_circulares_total 
        WHERE data_circulo IS NOT NULL 
        ORDER BY data_circulo";
$result = $conn->query($sql);

$eventos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['data_circulo'])) {
            try {
                $data = new DateTime($row['data_circulo']);
                $row['data_circulo_iso'] = $data->format('Y-m-d\TH:i:s');
            } catch (Exception $e) {
                error_log("Erro na data do processo {$row['id']}: " . $e->getMessage());
                continue;
            }
            $eventos[] = $row;
        }
    }
} else {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Processos Circulares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --gray: #95a5a6;
            --light-gray: #f8f9fa;
            --purple: #9b59b6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -30px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 20px 25px;
            border: none;
        }

        .btn-new {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(26, 188, 156, 0.3);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-new:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(26, 188, 156, 0.4);
            color: white;
        }

        .btn-back {
            position: absolute;
            left: 20px;
            top: 20px;
            z-index: 10;
        }

        .page-title {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .page-description {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        #calendar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            min-height: 600px;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 0.9rem;
            border: none !important;
            margin-bottom: 2px;
        }

        .fc-event:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .fc-daygrid-event {
            border-left: 4px solid var(--accent);
        }

        .fc-event-title {
            font-weight: 500;
            white-space: normal !important;
        }

        .fc-toolbar-title {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.4rem;
        }

        .fc-button {
            background-color: var(--secondary) !important;
            border: none !important;
            color: white !important;
            border-radius: 6px !important;
            padding: 6px 12px !important;
        }

        .fc-button:hover {
            background-color: var(--primary) !important;
        }

        .fc-dayHeader {
            background-color: var(--light-gray);
            padding: 8px 0;
            font-weight: 600;
        }

        .fc-day-sun {
            color: #e74c3c;
        }

        .fc-day-sat {
            color: #3498db;
        }

        .fc-daygrid-day-top {
            justify-content: center;
            padding-top: 5px;
        }

        .fc-daygrid-day-number {
            font-weight: 600;
            font-size: 1.1rem;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto;
        }

        .fc-day-today .fc-daygrid-day-number {
            background-color: var(--accent);
            color: white;
        }

        .btn-list {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-list:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(52, 152, 219, 0.4);
            color: white;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d;
        }

        /* Estilo para o modal */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            color: white;
        }

        .btn-save:hover {
            background: linear-gradient(135deg, #16a085, var(--accent));
        }

        /* Novo estilo para botão de agendamento */
        .btn-agendar-cal {
            background: linear-gradient(135deg, var(--purple), #8e44ad);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(155, 89, 182, 0.3);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-agendar-cal:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(155, 89, 182, 0.4);
            color: white;
        }

        /* Estilo para indicadores de status */
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

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }

            .fc-header-toolbar {
                flex-wrap: wrap;
            }

            .fc-toolbar-chunk {
                margin-bottom: 10px;
            }

            #calendar {
                min-height: 400px;
            }

            .card-header {
                flex-direction: column;
                gap: 10px;
            }

            .card-header>div {
                width: 100%;
                text-align: center;
            }

            .stats-card {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="header text-center">
        <div class="container position-relative">
            <a href="index.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar aos Processos
            </a>

            <h1 class="page-title"><i class="fas fa-calendar-alt me-2"></i>Calendário de Processos Circulares</h1>
            <p class="page-description">Visualize as datas agendadas para os círculos</p>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= count($eventos) ?></div>
                    <div class="stats-label">Eventos Agendados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= count(array_filter($eventos, function ($e) {
                                                    return (new DateTime($e['data_circulo_iso'])) > new DateTime() && $e['situacao'] !== 'Cancelado';
                                                })) ?></div>
                    <div class="stats-label">Próximos Eventos</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= count(array_filter($eventos, function ($e) {
                                                    return (new DateTime($e['data_circulo_iso'])) <= new DateTime() && $e['situacao'] !== 'Cancelado';
                                                })) ?></div>
                    <div class="stats-label">Eventos Realizados</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-calendar-day me-2"></i>
                    <span>Calendário de Círculos</span>
                </div>
                <div>
                    <a href="index.php" class="btn btn-list me-2">
                        <i class="fas fa-list me-1"></i>Ver Lista Completa
                    </a>
                    <a href="novo.php" class="btn btn-new">
                        <i class="fas fa-plus me-1"></i>Novo Processo
                    </a>
                    <button class="btn btn-agendar-cal" id="btnAgendar">
                        <i class="fas fa-calendar-plus me-1"></i>Agendar Círculo
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>

        <div class="text-center mt-4 text-muted">
            <p>Sistema de Gestão de Processos Circulares</p>
            <p>Escritividade para obtenção da personalidade de pessoas internas</p>
            <p>&copy; <?= date('Y') ?> Departamento de Licença. Todos os direitos reservados.</p>
        </div>
    </div>

    <!-- Modal de Agendamento -->
    <div class="modal fade" id="agendarModal" tabindex="-1" aria-labelledby="agendarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agendarModalLabel">Agendar Círculo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgendar">
                        <div class="mb-3">
                            <label for="processo_id" class="form-label">Processo</label>
                            <select class="form-select" id="processo_id" name="processo_id" required>
                                <option value="">Selecione um processo</option>
                                <?php foreach ($processos as $processo): ?>
                                    <option value="<?= $processo['id'] ?>" <?= $processo_id == $processo['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($processo['numero_processo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="data_circulo" class="form-label">Data e Hora</label>
                            <input type="datetime-local" class="form-control" id="data_circulo" name="data_circulo" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-save" id="btnSalvarAgendamento">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Status do Círculo -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Status do Círculo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="eventInfo"></div>
                    <div class="mb-3">
                        <label class="form-label">Selecione o status:</label>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <button class="btn btn-success status-btn" data-status="realizado">
                                <i class="fas fa-check me-1"></i> Realizado
                            </button>
                            <button class="btn btn-warning status-btn" data-status="falta">
                                <i class="fas fa-times me-1"></i> Faltou
                            </button>
                            <button class="btn btn-secondary status-btn" data-status="agendado">
                                <i class="fas fa-calendar me-1"></i> Agendado
                            </button>
                            <button class="btn btn-danger status-btn" data-status="cancelado">
                                <i class="fas fa-ban me-1"></i> Cancelar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const eventos = <?php echo json_encode($eventos); ?>;

            // Elementos do modal
            const agendarModal = new bootstrap.Modal(document.getElementById('agendarModal'));
            const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            const btnAgendar = document.getElementById('btnAgendar');
            const btnSalvar = document.getElementById('btnSalvarAgendamento');
            const processoSelect = document.getElementById('processo_id');
            const dataCirculoInput = document.getElementById('data_circulo');
            const eventInfo = document.getElementById('eventInfo');

            // Preencher data atual como padrão
            const now = new Date();
            const formattedNow = now.toISOString().slice(0, 16);
            dataCirculoInput.value = formattedNow;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                timeZone: 'local',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: eventos.map(evento => {
                    // Determinar classe CSS baseada na situação
                    let eventClass = 'event-agendado';
                    if (evento.situacao === 'Círculo realizado') eventClass = 'event-realizado';
                    else if (evento.situacao === 'Faltou') eventClass = 'event-falta';
                    else if (evento.situacao === 'Cancelado') eventClass = 'event-cancelado';

                    return {
                        title: 'Processo ' + evento.numero_processo + ' - ' + evento.facilitador,
                        start: evento.data_circulo_iso,
                        allDay: false,
                        extendedProps: {
                            id: evento.id,
                            situacao: evento.situacao
                        },
                        classNames: [eventClass]
                    };
                }),
                eventClick: function(info) {
                    const evento = info.event;
                    const eventoId = evento.extendedProps.id;
                    const situacao = evento.extendedProps.situacao;

                    // Preencher informações do evento no modal
                    eventInfo.innerHTML = `
                        <p><strong>Processo:</strong> ${evento.title}</p>
                        <p><strong>Data:</strong> ${evento.start.toLocaleString('pt-BR')}</p>
                        <p><strong>Status:</strong> <span class="status-badge ${situacao === 'Círculo realizado' ? 'bg-success' : situacao === 'Faltou' ? 'bg-warning' : situacao === 'Cancelado' ? 'bg-danger' : 'bg-info'}">${situacao}</span></p>
                    `;

                    // Armazenar o id do evento no modal
                    statusModal._element.dataset.eventId = eventoId;

                    // Abrir o modal
                    statusModal.show();
                },
                dateClick: function(info) {
                    // Quando clica em um dia, preencher a data no modal
                    const data = info.date;
                    const dataISO = data.toISOString();
                    const dataLocal = new Date(dataISO);

                    // Formatar para o input datetime-local (removendo os segundos e o Z)
                    const formattedDate = dataLocal.toISOString().slice(0, 16);
                    dataCirculoInput.value = formattedDate;

                    // Abrir o modal
                    agendarModal.show();
                },
                eventContent: function(arg) {
                    const container = document.createElement('div');
                    container.classList.add('fc-event-container');

                    const title = document.createElement('div');
                    title.classList.add('fc-event-title');
                    title.innerHTML = arg.event.title;

                    // Adicionar ícone de status
                    const situacao = arg.event.extendedProps.situacao;
                    let icon = '';
                    if (situacao === 'Círculo realizado') icon = '<i class="fas fa-check-circle me-1"></i>';
                    else if (situacao === 'Faltou') icon = '<i class="fas fa-times-circle me-1"></i>';
                    else if (situacao === 'Cancelado') icon = '<i class="fas fa-ban me-1"></i>';

                    container.innerHTML = icon + title.innerHTML;
                    return {
                        domNodes: [container]
                    };
                },
                dayHeaderClassNames: function(arg) {
                    if (arg.date.getDay() === 0) {
                        return ['fc-day-sun'];
                    }
                    if (arg.date.getDay() === 6) {
                        return ['fc-day-sat'];
                    }
                    return [];
                },
                dayCellClassNames: function(arg) {
                    if (arg.date.getDay() === 0) {
                        return ['fc-day-sun'];
                    }
                    if (arg.date.getDay() === 6) {
                        return ['fc-day-sat'];
                    }
                    return [];
                }
            });

            calendar.render();

            // Abrir modal ao clicar no botão
            btnAgendar.addEventListener('click', function() {
                agendarModal.show();
            });

            // Salvar agendamento
            btnSalvar.addEventListener('click', function() {
                const processoId = processoSelect.value;
                const dataCirculo = dataCirculoInput.value;

                if (!processoId || !dataCirculo) {
                    alert('Preencha todos os campos');
                    return;
                }

                // Enviar dados via AJAX para salvar o agendamento
                const formData = new FormData();
                formData.append('processo_id', processoId);
                formData.append('data_circulo', dataCirculo);

                fetch('salvar_agendamento.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Agendamento salvo com sucesso!');
                            agendarModal.hide();
                            // Recarregar a página para atualizar o calendário
                            location.reload();
                        } else {
                            alert('Erro ao salvar: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao salvar agendamento');
                    });
            });

            // Evento para botões de status
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const status = this.dataset.status;
                    const eventoId = statusModal._element.dataset.eventId;

                    let situacao = 'Agendado';
                    if (status === 'realizado') situacao = 'Círculo realizado';
                    else if (status === 'falta') situacao = 'Faltou';
                    else if (status === 'cancelado') situacao = 'Cancelado';

                    // Atualizar situação no banco de dados
                    fetch('atualizar_situacao.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id=${eventoId}&situacao=${situacao}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Status atualizado com sucesso!');
                                statusModal.hide();
                                // Recarregar a página para atualizar o calendário
                                location.reload();
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