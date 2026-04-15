<?php
/**
 * Template para el Simulador de Crisis
 * Esta vista maneja las 7 secciones del simulador.
 */
?>
<div class="sdc-simulator-container">
    <!-- SECCIÓN 1: El Impacto del Tiempo -->
    <section class="sdc-section sdc-section-1 active" data-step="1">
        <div class="sdc-nav-top">
            <button class="sdc-nav-btn sdc-btn-prev disabled">Anterior</button>
            <button class="sdc-nav-btn sdc-btn-next" data-next="2">Siguiente</button>
        </div>

        <div class="span-tag alert sdc-animate">
            <?= avante_get_icon('exclamation-triangle-fill'); ?> Alerta de crisis activa
        </div>
        
        <h1 class="sdc-title sdc-animate">
            <span class="sdc-title-white">EL IMPACTO DEL</span> 
            <span class="sdc-title-accent">TIEMPO</span>
        </h1>
        
        <p class="sdc-description sdc-animate">
            En una crisis mediática, cada segundo cuenta. La inacción es el mayor multiplicador de pérdidas.
        </p>
        
        <div class="sdc-cards-grid sdc-animate">
            <div class="sdc-card glass-border-bright sdc-animate">
                <div class="sdc-counter-wrapper">
                    <div class="sdc-card-value red sdc-counter" data-target="5" data-prefix="$" data-decimals="2" data-duration="103000">$0.00</div>
                    <span class="sdc-card-value red">M</span>
                </div>
                <h3 class="sdc-card-label">
                    <?= avante_get_icon('graph-down-arrow')?> COSTO ESTIMADO DE CRISIS
                </h3>
                <p class="sdc-card-text">
                    En solo unas horas, una crisis puede costar un promedio de <strong>$5 millones</strong>.
                </p>
            </div>
            
            <div class="sdc-card glass-border-bright sdc-animate">
                <div class="sdc-card-value orange">11%</div>
                <h3 class="sdc-card-label">
                    <?= avante_get_icon('clock-history'); ?> CAÍDA DE VALOR DE MERCADO
                </h3>
                <p class="sdc-card-text orange">
                    En solo 5 días, el valor de mercado de una empresa puede caer hasta un <strong>11%</strong>.
                </p>
            </div>
        </div>
        
        <button class="btn primary sdc-animate sdc-btn-next" data-next="2">Preparar a la empresa</button>
    </section>

    <!-- SECCIÓN 2: Registro de Datos -->
    <section class="sdc-section sdc-section-2" id="section-2" data-step="2">
        <div class="sdc-nav-top">
            <button class="sdc-nav-btn sdc-btn-prev" data-prev="1">Anterior</button>
            <button class="sdc-nav-btn sdc-btn-next disabled" data-next="3">Siguiente</button>
        </div>

        <div class="sdc-form-container sdc-animate">
            <h2 class="sdc-title">
                <span class="sdc-title-white">PREPARA TU</span> 
                <span class="sdc-title-accent">ESCENARIO</span>
            </h2>
            <p class="sdc-description">
                Ingresa los datos para personalizar la simulación y generar tu reporte de métricas final.
            </p>

            <form id="sdc-init-form" class="sdc-form">
                <div class="sdc-form-grid">
                    <div class="sdc-field-group sdc-animate">
                        <label>Compañía</label>
                        <input type="text" name="company_name" placeholder="Nombre de la empresa" required>
                    </div>
                    <div class="sdc-field-group sdc-animate">
                        <label>Nombre Completo</label>
                        <input type="text" name="user_name" placeholder="Tu nombre" required>
                    </div>
                    <div class="sdc-field-group sdc-animate">
                        <label>Puesto</label>
                        <input type="text" name="user_position" placeholder="Cargo que desempeñas" required>
                    </div>
                    <div class="sdc-field-group sdc-animate">
                        <label>Email de Contacto</label>
                        <input type="email" name="user_email" placeholder="correo@empresa.com" required>
                    </div>
                    <div class="sdc-field-group sdc-animate">
                        <label>Teléfono</label>
                        <input type="tel" name="user_phone" placeholder="+00 000 000 000" required>
                    </div>
                </div>

                <!-- Summary Card / Footer -->
                <div class="sdc-summary-card glass-border-bright sdc-animate">
                    <div class="sdc-summary-header">
                        <div class="sdc-summary-icon">
                            <?= avante_get_icon('shield-check'); ?>
                        </div>
                        <div class="sdc-summary-texts">
                            <h3>PREPARACIÓN COMPLETADA</h3>
                            <p>La estrategia preventiva ha sido activada.</p>
                        </div>
                    </div>
                    
                    <div class="sdc-summary-metrics">
                        <div class="sdc-summary-metric">
                            <span class="sdc-metric-val">-55%</span>
                            <span class="sdc-metric-lbl">REDUCCIÓN DE PÉRDIDAS</span>
                        </div>
                        <div class="sdc-summary-metric">
                            <span class="sdc-metric-val">8x</span>
                            <span class="sdc-metric-lbl">RECUPERACIÓN MÁS RÁPIDA</span>
                        </div>
                    </div>

                    <button type="submit" class="btn primary">Iniciar entrenamiento</button>
                </div>
            </form>
        </div>
    </section>

    <!-- SECCIÓN 3: Triage de Amenazas -->
    <section class="sdc-section sdc-section-3" data-step="3">
        <div class="sdc-nav-top">
            <button class="sdc-nav-btn sdc-btn-prev" data-prev="2">Anterior</button>
            <button class="sdc-nav-btn sdc-btn-next disabled" data-next="4">Siguiente</button>
        </div>

        <div class="sdc-triage-container">
            <header class="sdc-radar-header sdc-animate">
                <h2 class="sdc-title">
                    <?= avante_get_icon('radar'); ?> 
                    <span class="sdc-title-white">RADAR DE</span> 
                    <span class="sdc-title-accent">AMENAZAS</span>
                </h2>
                <p class="sdc-description">Analiza el reporte y clasifícalo en la categoría correcta para activar el protocolo de respuesta.</p>
            </header>

            <div class="sdc-triage-scanner sdc-animate">
                <div class="sdc-scanner-deck" id="sdc-threat-deck">
                    <div class="sdc-active-card" id="current-threat-card">
                        <div class="sdc-card-scanner-line"></div>
                        <div class="sdc-card-content">
                            <span class="sdc-mini-label">ANALIZANDO REPORTE...</span>
                            <div class="sdc-typing-wrapper">
                                <p id="threat-text-display">Iniciando escaneo de seguridad...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="sdc-triage-progress">
                    <div class="sdc-progress-info">
                        REPORTE <span id="triage-count">1</span> / 4 
                    </div>
                    <div class="sdc-progress-dots" id="sdc-triage-dots">
                        <div class="dot active"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                </div>
            </div>

            <div class="sdc-triage-controls sdc-animate">
                <button class="sdc-triage-btn" data-type="fake-news">
                    <span class="btn-icon"><?= avante_get_icon('shield-slash'); ?></span>
                    <span class="btn-text">Fake News</span>
                </button>
                <button class="sdc-triage-btn" data-type="regulatory">
                    <span class="btn-icon"><?= avante_get_icon('file-earmark-ruled'); ?></span>
                    <span class="btn-text">Regulatorio</span>
                </button>
                <button class="sdc-triage-btn" data-type="industry">
                    <span class="btn-icon"><?= avante_get_icon('factory'); ?></span>
                    <span class="btn-text">Industria</span>
                </button>
                <button class="sdc-triage-btn" data-type="corporate">
                    <span class="btn-icon"><?= avante_get_icon('corporate'); ?></span>
                    <span class="btn-text">Corporativo</span>
                </button>
            </div>
            
            <div class="sdc-triage-feedback" id="triage-feedback">
                <div class="feedback-content">
                    <?= avante_get_icon('check-circle-fill'); ?>
                    <span>Amenaza clasificada</span>
                </div>
            </div>

            <footer class="sdc-triage-footer sdc-animate">
                <button class="btn primary sdc-btn-next disabled" data-next="4">Continuar</button>
            </footer>
        </div>
    </section>

    <!-- SECCIÓN 4: Mapa de Stakeholders (Tablero Táctico) -->
    <section class="sdc-section sdc-section-4" data-step="4">
        <div class="sdc-nav-top">
            <button class="sdc-nav-btn sdc-btn-prev" data-prev="3">Anterior</button>
            <button class="sdc-nav-btn sdc-btn-next disabled" data-next="5">Siguiente</button>
        </div>

        <div class="sdc-stakeholder-container">
            <header class="sdc-stakeholder-header sdc-animate">
                <h2 class="sdc-title stakeholders-map">
                    <?= avante_get_icon('people'); ?> 
                    <span class="sdc-title-white">MAPA DE</span> 
                    <span class="sdc-title-accent">STAKEHOLDERS</span>
                </h2>
                <p class="sdc-description stakeholders-map">Configura tu estrategia de respuesta asignando la herramienta más efectiva para cada grupo de interés.</p>
            </header>

            <!-- Grid de Configuración -->
            <div class="sdc-stakeholder-grid sdc-animate" id="sdc-stakeholder-dashboard">
                <div class="sdc-stk-module sdc-animate" data-id="activists" data-target="narrative">
                    <div class="module-header">
                        <h3>Activistas</h3>
                    </div>
                    <p class="module-desc">Preocupados por cambios regulatorios.</p>
                    <div class="module-slot" id="slot-activists">
                        <button class="btn-assign-tool btn primary">
                            <span class="plus"><?= avante_get_icon('plus-circle'); ?></span> Asignar protocolo
                        </button>
                    </div>
                </div>

                <div class="sdc-stk-module sdc-animate" data-id="clients" data-target="human">
                    <div class="module-header">
                        <h3>Clientes</h3>
                    </div>
                    <p class="module-desc">Preocupados por salud y seguridad.</p>
                    <div class="module-slot" id="slot-clients">
                        <button class="btn-assign-tool btn primary">
                            <span class="plus"><?= avante_get_icon('plus-circle'); ?></span> Asignar protocolo
                        </button>
                    </div>
                </div>

                <div class="sdc-stk-module sdc-animate" data-id="investors" data-target="formal">
                    <div class="module-header">
                        <h3>Inversionistas</h3>
                    </div>
                    <p class="module-desc">Preocupados por valor y estabilidad.</p>
                    <div class="module-slot" id="slot-investors">
                        <button class="btn-assign-tool btn primary">
                            <span class="plus"><?= avante_get_icon('plus-circle'); ?></span> Asignar protocolo
                        </button>
                    </div>
                </div>

                <div class="sdc-stk-module sdc-animate" data-id="employees" data-target="qa">
                    <div class="module-header">
                        <h3>Empleados</h3>
                    </div>
                    <p class="module-desc">Incertidumbre laboral y orgullo.</p>
                    <div class="module-slot" id="slot-employees">
                        <button class="btn-assign-tool btn primary">
                            <span class="plus"><?= avante_get_icon('plus-circle'); ?></span> Asignar protocolo
                        </button>
                    </div>
                </div>
            </div>

            <div class="sdc-matching-footer sdc-animate">
                <div class="sdc-info-box sdc-animate">
                    <?= avante_get_icon('info-circle'); ?>
                    <div>
                        <h4 class="sdc-text-blue sdc-bold">AAM: Anger Activism Model</h4>
                        <p>Recuerda: El enojo impulsa la acción inmediata. Calibra tu respuesta según el nivel de activación del stakeholder.</p>
                    </div>
                </div>
                
                <footer class="sdc-triage-footer sdc-animate" style="margin-top: 20px;">
                    <button class="btn primary sdc-btn-next disabled" data-next="5">Continuar</button>
                </footer>
            </div>
        </div>
    </section>

    <!-- WAR ROOM: MISIÓN REPUTACIÓN (Secciones 5, 6, 7) -->
    <section class="sdc-section sdc-warroom-parent" data-step="warroom" style="padding: 0;">
        <div class="sdc-warroom-layout">
            <div class="sdc-warroom-main">
                <header class="sdc-warroom-header sdc-animate">
                    <h2 class="sdc-title war-room">
                        <?= avante_get_icon('broadcast'); ?> 
                        <span class="sdc-title-white">WAR ROOM:</span> 
                        <span class="sdc-title-accent">MISIÓN REPUTACIÓN</span>
                    </h2>
                    <p class="sdc-description war-room">Simulación a contrarreloj. Toma decisiones críticas bajo presión.</p>
                </header>

                <div class="sdc-scenarios-stack">
                    <?php
                    $scenarios = [
                        5 => [
                            'icon' => 'twitter',
                            'speaker' => '@InfluencerSalud',
                            'message' => '"Me acaban de avisar que un producto de @CrisisMasterCorp causó una reacción alérgica grave. ¿Alguien sabe algo? #AlertaAlimentaria"',
                            'options' => [
                                ['text' => 'Responder públicamente con el comunicado oficial.', 'score' => 100, 'feedback' => 'Controlar la narrativa en el mismo canal donde nace el rumor.'],
                                ['text' => 'Ignorar el post para no darle más visibilidad.', 'score' => 0, 'feedback' => 'La indecisión en crisis es una decisión por sí misma.'],
                                ['text' => 'Reportar el post por difamación.', 'score' => 20, 'feedback' => 'La confrontación con influencers suele ser contraproducente.'],
                            ],
                            'time' => 16
                        ],
                        6 => [
                            'icon' => 'phone',
                            'speaker' => 'Director de Planta',
                            'message' => '"¡Tenemos un problema! El lote 402 salió con un error en el etiquetado de alérgenos. No dice que contiene trazas de maní. ¿Qué hacemos?"',
                            'options' => [
                                ['text' => 'Retirar el lote silenciosamente de las tiendas.', 'score' => 20, 'feedback' => 'El retiro silencioso es peligroso y puede ser visto como encubrimiento.'],
                                ['text' => 'Emitir un comunicado de prensa inmediato y retirar el lote.', 'score' => 100, 'feedback' => 'La transparencia y la rapidez salvan vidas y reputación.'],
                                ['text' => 'Esperar a ver si hay reportes de reacciones alérgicas.', 'score' => 0, 'feedback' => 'Arriesgar la salud pública es la peor decisión posible.'],
                            ],
                            'time' => 16
                        ],
                        7 => [
                            'icon' => 'newspaper',
                            'speaker' => 'Diario El Global',
                            'message' => '"EXCLUSIVA: Crisis Master Corp sabía del error de etiquetado y tardó horas en reaccionar. Fuentes internas confirman caos en el comité."',
                            'options' => [
                                ['text' => 'Desmentir la nota y atacar la veracidad de la fuente.', 'score' => 20, 'feedback' => 'La confrontación con medios suele ser una batalla perdida.'],
                                ['text' => 'Solicitar una entrevista con el CEO para aclarar los tiempos.', 'score' => 60, 'feedback' => 'Ganar tiempo con una entrevista es útil pero requiere preparación.'],
                                ['text' => 'Publicar el log de acciones del comité para demostrar transparencia.', 'score' => 100, 'feedback' => 'La evidencia radical es la única forma de desmentir una filtración.'],
                            ],
                            'time' => 16
                        ]
                    ];

                    foreach ($scenarios as $step => $data): ?>
                    <div class="sdc-warroom-step" data-war-step="<?= $step ?>" data-time="<?= $data['time'] ?>" style="display: none;">
                        <div class="sdc-warroom-scenario sdc-animate">
                            <div class="sdc-scenario-card glass-border-bright">
                                <div class="sdc-scenario-meta">
                                    <div class="sdc-scenario-icon"><?= avante_get_icon($data['icon']); ?></div>
                                    <div class="sdc-scenario-info">
                                        <span class="sdc-mini-label war-room">ENTRADA DE DATOS</span>
                                        <h3><?= $data['speaker'] ?></h3>
                                    </div>
                                </div>
                                <div class="sdc-scenario-body">
                                    <p><?= $data['message'] ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="sdc-warroom-options sdc-animate">
                            <?php foreach ($data['options'] as $idx => $opt): ?>
                                <button class="sdc-option-btn glass-border-bright sdc-animate" data-score="<?= $opt['score'] ?>" data-feedback="<?= esc_attr($opt['feedback']) ?>">
                                    <?= $opt['text'] ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="sdc-warroom-sidebar sdc-animate">
                <div class="sdc-timer-wrapper">
                    <div class="sdc-timer-circle">
                        <div class="sdc-timer-text">
                            <span class="sdc-timer-label">TIEMPO</span>
                            <span class="sdc-timer-value">--s</span>
                        </div>
                    </div>
                </div>

                <div class="sdc-status-card glass-border-bright sdc-animate">
                    <span class="sdc-mini-label">ESTADO DE LA MISIÓN</span>
                    <div class="sdc-progress-bar">
                        <div class="sdc-progress-fill" id="sdc-warroom-progress" style="width: 33%"></div>
                    </div>
                    <div class="sdc-progress-text">Progreso <span id="sdc-warroom-step-num">1 / 3</span></div>
                </div>

                <div class="sdc-protocol-card glass-border-bright sdc-animate">
                    <span class="sdc-mini-label-red">PROTOCOLO GREDLER</span>
                    <p>La simulación está diseñada para inducir estrés reputacional. La incertidumbre y el tiempo limitado son variables críticas que miden la cohesión del comité de crisis.</p>
                </div>

                <div class="sdc-transmission-status sdc-animate">
                    TRANSMISIÓN ENCRIPTADA ACTIVA
                </div>
            </aside>
        </div>
    </section>

    <!-- SECCIÓN 8: Dashboard de Resultados -->
    <section class="sdc-section sdc-section-8" data-step="8">
        <div class="sdc-dashboard-container">
            <header class="sdc-dashboard-header sdc-animate">
                <div class="sdc-dashboard-title">
                    <h2 class="sdc-title war-room">
                        <?= avante_get_icon('grid'); ?> 
                        <span class="sdc-title-white">DASHBOARD DE</span> 
                        <span class="sdc-title-accent">RESULTADOS</span>
                    </h2>
                    <p class="sdc-description war-room">Análisis forense de tu desempeño en la simulación de crisis.</p>
                </div>
            </header>

            <div class="sdc-dashboard-grid sdc-animate">
                <!-- Métricas Principales -->
                <div class="sdc-dashboard-metrics">
                    <div class="sdc-metric-card glass-border-bright">
                        <div class="sdc-metric-icon"><?= avante_get_icon('broadcast'); ?></div>
                        <div class="sdc-metric-info">
                            <h3>USO DEL RADAR (URR) <span id="sdc-val-urr">0%</span></h3>
                            <div class="sdc-small-progress"><div class="fill" id="sdc-fill-urr" style="width: 0%"></div></div>
                            <p>Capacidad para identificar y clasificar amenazas.</p>
                        </div>
                    </div>
                    <div class="sdc-metric-card glass-border-bright">
                        <div class="sdc-metric-icon"><?= avante_get_icon('people'); ?></div>
                        <div class="sdc-metric-info">
                            <h3>CONFORMACIÓN COMITÉ (CCC) <span id="sdc-val-ccc">0%</span></h3>
                            <div class="sdc-small-progress"><div class="fill" id="sdc-fill-ccc" style="width: 0%"></div></div>
                            <p>Efectividad en el mapeo de stakeholders.</p>
                        </div>
                    </div>
                    <div class="sdc-metric-card glass-border-bright">
                        <div class="sdc-metric-icon"><?= avante_get_icon('clock-history'); ?></div>
                        <div class="sdc-metric-info">
                            <h3>TIEMPO DE RESPUESTA (TTR) <span id="sdc-val-ttr">0%</span></h3>
                            <div class="sdc-small-progress"><div class="fill" id="sdc-fill-ttr" style="width: 0%"></div></div>
                            <p>Agilidad en la toma de decisiones bajo presión.</p>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="sdc-dashboard-chart glass-border-bright sdc-card">
                    <span class="sdc-mini-label">PERFIL DE GESTIÓN DE CRISIS</span>
                    <canvas id="sdc-radar-chart"></canvas>
                </div>

                <div class="sdc-dashboard-chart glass-border-bright sdc-card">
                    <span class="sdc-mini-label">DESGLOSE DE DECISIONES</span>
                    <canvas id="sdc-bar-chart"></canvas>
                </div>
            </div>

            <div class="sdc-forensic-analysis sdc-animate">
                <span class="sdc-mini-label">ANÁLISIS FORENSE DE DECISIONES</span>
                <div id="sdc-decision-log" class="sdc-decision-list">
                    <!-- Inyectado por JS -->
                </div>
            </div>
        </div>
        <button class="btn primary sdc-btn-next" data-next="9"><?= avante_get_icon('save')?>Guardar</button>
    </section>

    <!-- SECCIÓN 9: RESUMEN DEL PARTICIPANTE -->
    <section class="block sdc-section sdc-section-9" data-step="9">
        <div class="content sdc-results-container">

            <div class="container">
                    <!-- Ficha del Participante -->
                <div class="sdc-participant-card sdc-animate">
                    <span class="sdc-mini-label">DATOS DEL PARTICIPANTE</span>
                    <div class="sdc-participant-grid">
                        <div class="sdc-participant-field">
                            <span class="sdc-field-label"><?= avante_get_icon('building'); ?> Compañía</span>
                            <span class="sdc-field-value" id="sdc-res-company">—</span>
                        </div>
                        <div class="sdc-participant-field">
                            <span class="sdc-field-label"><?= avante_get_icon('person'); ?> Nombre</span>
                            <span class="sdc-field-value" id="sdc-res-name">—</span>
                        </div>
                        <div class="sdc-participant-field">
                            <span class="sdc-field-label"><?= avante_get_icon('clipboard-check'); ?> Puesto</span>
                            <span class="sdc-field-value" id="sdc-res-position">—</span>
                        </div>
                        <div class="sdc-participant-field">
                            <span class="sdc-field-label"><?= avante_get_icon('envelope'); ?> Email</span>
                            <span class="sdc-field-value" id="sdc-res-email">—</span>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Métricas -->
                <div class="sdc-summary-results sdc-animate">
                    <span class="sdc-mini-label">RESUMEN DE DESEMPEÑO</span>
                    <div class="sdc-summary-results-grid">
                        <div class="sdc-result-metric sdc-animate">
                            <div class="sdc-result-icon"><?= avante_get_icon('radar'); ?></div>
                            <div class="sdc-result-data">
                                <span class="sdc-result-label">Radar de Riesgos (URR)</span>
                                <span class="sdc-result-value" id="sdc-res-urr">—</span>
                            </div>
                        </div>
                        <div class="sdc-result-metric sdc-animate">
                            <div class="sdc-result-icon"><?= avante_get_icon('people'); ?></div>
                            <div class="sdc-result-data">
                                <span class="sdc-result-label">Comité de Crisis (CCC)</span>
                                <span class="sdc-result-value" id="sdc-res-ccc">—</span>
                            </div>
                        </div>
                        <div class="sdc-result-metric sdc-animate">
                            <div class="sdc-result-icon"><?= avante_get_icon('stopwatch'); ?></div>
                            <div class="sdc-result-data">
                                <span class="sdc-result-label">Tiempo de Respuesta (TTR)</span>
                                <span class="sdc-result-value" id="sdc-res-ttr">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle de Decisiones -->
            <div class="sdc-results-detail sdc-animate">
                <span class="sdc-mini-label">DECISIONES REGISTRADAS</span>
                <div id="sdc-res-decisions" class="sdc-decision-list">
                    <!-- Inyectado por JS -->
                </div>
            </div>

            <footer class="sdc-results-footer sdc-animate">
                <p>Gracias por completar la simulación. Estos datos han sido almacenados en nuestro sistema.</p>
                <button class="btn primary" onclick="location.reload()">Realizar otra simulación</button>
            </footer>
        </div>
    </section>

    <!-- Modal Selector de Herramientas (Capa Global) -->
    <div id="sdc-tool-picker" class="sdc-picker-overlay">
        <div class="sdc-picker-content">
            <div class="picker-header">
                <span class="sdc-mini-label">SELECTOR TÁCTICO</span>
                <h3>Selecciona Herramienta</h3>
            </div>
            <div class="picker-options">
                <button class="picker-option" data-tool="narrative">
                    <span class="option-icon"><?= avante_get_icon('file-earmark-ruled'); ?></span>
                    <div class="option-info">
                        <strong>Mensajes Clave</strong>
                        <p>Fijación de narrativa oficial.</p>
                    </div>
                </button>
                <button class="picker-option" data-tool="formal">
                    <span class="option-icon"><?= avante_get_icon('megaphone'); ?></span>
                    <div class="option-info">
                        <strong>Postura Oficial</strong>
                        <p>Comunicados institucionales.</p>
                    </div>
                </button>
                <button class="picker-option" data-tool="human">
                    <span class="option-icon"><?= avante_get_icon('person-video'); ?></span>
                    <div class="option-info">
                        <strong>Videos de Vocería</strong>
                        <p>Humanización y empatía.</p>
                    </div>
                </button>
                <button class="picker-option" data-tool="qa">
                    <span class="option-icon"><?= avante_get_icon('question-circle'); ?></span>
                    <div class="option-info">
                        <strong>Q&A Crítico</strong>
                        <p>Preparación ante preguntas.</p>
                    </div>
                </button>
            </div>
            <button id="close-picker" class="btn-cancel">CANCELAR</button>
        </div>
    </div>
</div>
