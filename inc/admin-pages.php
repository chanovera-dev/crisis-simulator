<?php
/**
 * Lógica del panel de administración para el Simulador de Crisis.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registra el menú de administración y sus submenús.
 */
function sdc_register_admin_menu() {
    // Menú Principal
    add_menu_page(
        'Simulador de Crisis',      // Page title
        'Simulador Crisis',        // Menu title
        'manage_options',           // Capability
        'sdc-admin',                // Menu slug
        'sdc_admin_dashboard',      // Callback function
        'dashicons-performance',    // Icon
        25                          // Position
    );

    // Submenú: Dashboard (mismo que el principal para que aparezca primero)
    add_submenu_page(
        'sdc-admin',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'sdc-admin',
        'sdc_admin_dashboard'
    );

    // Submenú: Aplicantes
    add_submenu_page(
        'sdc-admin',
        'Lista de Aplicantes',
        'Aplicantes',
        'manage_options',
        'sdc-applicants',
        'sdc_admin_applicants_list'
    );
}
add_action( 'admin_menu', 'sdc_register_admin_menu' );

/**
 * Renderiza el dashboard con gráficas y métricas globales.
 */
function sdc_admin_dashboard() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sdc_applicants';
    
    // Obtener total
    $total = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    
    // Datos para gráfico (últimos 15 días)
    $chart_data = $wpdb->get_results( "SELECT DATE(created_at) as date, COUNT(*) as count FROM $table_name GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 15" );
    $chart_labels = array();
    $chart_values = array();
    foreach( array_reverse($chart_data) as $d ) {
        $chart_labels[] = date('d M', strtotime($d->date));
        $chart_values[] = $d->count;
    }
    ?>
    <div class="wrap">
        <h1>Dashboard Global</h1>
        
        <div class="sdc-admin-grid" style="display: grid; grid-template-columns: 1fr 3fr; gap: 20px; margin-top: 20px;">
            <!-- KPIS -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="card" style="padding: 20px; background: #fff; margin: 0;">
                    <h2 style="margin: 0; color: #666; font-size: 14px; text-transform: uppercase;">Total Acumulado</h2>
                    <p style="font-size: 48px; font-weight: 800; margin: 10px 0; color: #2271b1;"><?php echo $total; ?></p>
                </div>
                <div class="card" style="padding: 20px; background: #fff; margin: 0;">
                    <h2 style="margin: 0; color: #666; font-size: 14px; text-transform: uppercase;">Promedio Diario</h2>
                    <p style="font-size: 24px; font-weight: 700; margin: 10px 0;">
                        <?php echo $chart_data ? round($total / count($chart_data), 1) : 0; ?>
                    </p>
                </div>
            </div>

            <!-- CHART -->
            <div class="card" style="padding: 20px; background: #fff; margin: 0;">
                <h2 style="margin: 0 0 20px 0; font-size: 14px; text-transform: uppercase;">Actividad del Simulador (Últimos 15 días)</h2>
                <canvas id="sdcChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('sdcChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Registros',
                    data: <?php echo json_encode($chart_values); ?>,
                    borderColor: '#2271b1',
                    backgroundColor: 'rgba(34, 113, 177, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#2271b1'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    });
    </script>
    <?php
}

/**
 * Renderiza la lista completa de aplicantes.
 */
function sdc_admin_applicants_list() {
    global $wpdb;
    $table_applicants = $wpdb->prefix . 'sdc_applicants';
    $table_simulations = $wpdb->prefix . 'sdc_simulations';
    
    // Procesar eliminación de aplicante (y todas sus simulaciones)
    if ( isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) ) {
        $applicant_id = intval($_GET['id']);
        check_admin_referer( 'sdc_delete_applicant_' . $applicant_id );
        $wpdb->delete( $table_simulations, array( 'applicant_id' => $applicant_id ) );
        $wpdb->delete( $table_applicants, array( 'id' => $applicant_id ) );
        echo '<div class="updated"><p>Participante y todas sus simulaciones eliminadas.</p></div>';
    }

    // Procesar exportación de PDF (Reporte Impresion) — sigue operando por simulation_id
    if ( isset($_GET['action']) && $_GET['action'] === 'export' && isset($_GET['id']) ) {
        $id = intval($_GET['id']);
        $query = "SELECT s.*, a.* FROM $table_simulations s JOIN $table_applicants a ON s.applicant_id = a.id WHERE s.id = %d";
        $data = $wpdb->get_row( $wpdb->prepare($query, $id) );

        if ( $data ) {
            $results = json_decode($data->results, true);
            $scores = $results['final_scores'] ?? ['urr' => 0, 'ccc' => 0, 'ttr' => 0];
            $decisions = $results['decisions'] ?? [];
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Reporte_Simulacion_<?php echo $data->user_name; ?></title>
                <style>
                    body { font-family: 'Helvetica', Arial, sans-serif; line-height: 1.6; color: #333; margin: 40px; }
                    .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ffb800; padding-bottom: 20px; }
                    .brand { color: #ffb800; font-weight: 800; font-size: 24px; }
                    .report-title { text-align: right; }
                    .section { margin-top: 40px; }
                    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
                    .card { background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee; }
                    .metric-box { text-align: center; padding: 20px; background: #333; color: #fff; border-radius: 8px; }
                    .metric-val { font-size: 32px; font-weight: 800; color: #ffb800; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
                    th { background: #f4f4f4; }
                    .correct { color: #28a745; font-weight: bold; }
                    .wrong { color: #d63638; font-weight: bold; }
                    @media print { .no-print { display: none; } }
                </style>
            </head>
            <body>
                <div class="no-print" style="margin-bottom: 20px;">
                    <button onclick="window.print()" style="padding: 10px 20px; background: #ffb800; border: none; cursor: pointer; font-weight: bold;">Imprimir / Guardar como PDF</button>
                </div>

                <div class="header">
                    <div class="brand">THE CRISIS ACADEMY</div>
                    <div class="report-title">
                        <h2>REPORTE FORENSE DE SIMULACIÓN</h2>
                        <p>ID Sesión: #<?php echo $data->id; ?> | <?php echo date('d/m/Y H:i', strtotime($data->created_at)); ?></p>
                    </div>
                </div>

                <div class="section grid">
                    <div class="card">
                        <h3>DATOS DEL PARTICIPANTE</h3>
                        <p><strong>Nombre:</strong> <?php echo esc_html($data->user_name); ?></p>
                        <p><strong>Empresa:</strong> <?php echo esc_html($data->company_name); ?></p>
                        <p><strong>Cargo:</strong> <?php echo esc_html($data->user_position); ?></p>
                        <p><strong>Email:</strong> <?php echo esc_html($data->user_email); ?></p>
                    </div>
                    <div class="grid" style="grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div class="metric-box">
                            <div class="metric-val"><?php echo $scores['urr']; ?>%</div>
                            <small>RADAR</small>
                        </div>
                        <div class="metric-box">
                            <div class="metric-val"><?php echo $scores['ccc']; ?>%</div>
                            <small>COMITÉ</small>
                        </div>
                        <div class="metric-box">
                            <div class="metric-val"><?php echo round($scores['ttr']); ?>%</div>
                            <small>TIEMPO</small>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3>ANÁLISIS DE DECISIONES (WAR ROOM)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Escenario</th>
                                <th>Decisión Tomada</th>
                                <th>Score</th>
                                <th>Tiempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($decisions as $idx => $d): ?>
                                <tr>
                                    <td>Escenario <?php echo ($idx + 1); ?></td>
                                    <td><?php echo esc_html($d['text']); ?></td>
                                    <td class="<?php echo $d['score'] >= 100 ? 'correct' : 'wrong'; ?>">
                                        <?php echo $d['score']; ?> pts
                                    </td>
                                    <td><?php echo $d['time']; ?>s</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="section" style="margin-top: 60px; text-align: center; opacity: 0.5; font-size: 12px;">
                    <p>Este reporte fue generado automáticamente por el Simulador de Crisis Academy.<br>
                    &copy; <?php echo date('Y'); ?> The Crisis Academy. Todos los derechos reservados.</p>
                </div>

                <script>
                    window.onload = function() {
                        // Opcional: auto disparar print si quieres
                        // window.print();
                    }
                </script>
            </body>
            </html>
            <?php
            exit;
        }
    }

    // Procesar eliminación en lote (por applicant_id)
    if ( (isset($_POST['action']) && $_POST['action'] === 'bulk-delete') || (isset($_POST['action2']) && $_POST['action2'] === 'bulk-delete') ) {
        if ( isset($_POST['bulk-ids']) && is_array($_POST['bulk-ids']) ) {
            check_admin_referer( 'bulk-applicants' );
            foreach ( $_POST['bulk-ids'] as $id ) {
                $aid = intval($id);
                $wpdb->delete( $table_simulations, array( 'applicant_id' => $aid ) );
                $wpdb->delete( $table_applicants, array( 'id' => $aid ) );
            }
            echo '<div class="updated"><p>' . count($_POST['bulk-ids']) . ' participantes y sus simulaciones eliminados correctamente.</p></div>';
        }
    }

    // Paginación simple
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $limit = 20;
    $offset = ($paged - 1) * $limit;
    
    // Query agrupada por aplicante
    $query = "SELECT a.*,
                     COUNT(s.id) AS simulation_count,
                     SUM(CASE WHEN s.results != '' AND s.results IS NOT NULL THEN 1 ELSE 0 END) AS completed_count,
                     (SELECT s2.id FROM $table_simulations s2 
                      WHERE s2.applicant_id = a.id AND s2.results != '' AND s2.results IS NOT NULL 
                      ORDER BY s2.id DESC LIMIT 1) AS latest_sim_id,
                     (SELECT s3.results FROM $table_simulations s3 
                      WHERE s3.applicant_id = a.id AND s3.results != '' AND s3.results IS NOT NULL 
                      ORDER BY s3.id DESC LIMIT 1) AS latest_results
              FROM $table_applicants a
              LEFT JOIN $table_simulations s ON s.applicant_id = a.id
              GROUP BY a.id
              ORDER BY a.created_at DESC
              LIMIT %d OFFSET %d";
    
    $applicants = $wpdb->get_results( $wpdb->prepare($query, $limit, $offset) );
    $total_records = $wpdb->get_var( "SELECT COUNT(*) FROM $table_applicants" );
    $total_pages = ceil($total_records / $limit);
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Lista de Aplicantes</h1>
        <button onclick="window.print()" class="page-title-action">Exportar a PDF</button>
        <hr class="wp-header-end">

        <p>A continuación se muestran todos los profesionales que han iniciado el simulador.</p>

        <form method="post">
            <?php wp_nonce_field( 'bulk-applicants' ); ?>
            
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <select name="action" id="bulk-action-selector-top">
                        <option value="-1">Acciones en lote</option>
                        <option value="bulk-delete">Eliminar permanentemente</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="Aplicar">
                </div>
            </div>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"></td>
                    <th style="width: 150px;">Fecha Registro</th>
                    <th>Nombre</th>
                    <th>Compañía</th>
                    <th>Email</th>
                    <th style="width: 90px; text-align: center;">Iteraciones</th>
                    <th style="width: 80px;">Score</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 60px;">PDF</th>
                    <th style="width: 80px;">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( $applicants ) : ?>
                    <?php foreach ( $applicants as $row ) : 
                        $sim_count = intval($row->simulation_count);
                        $completed = intval($row->completed_count);
                        $has_results = !empty($row->latest_results);
                        $score = '—';
                        $status = 'Sin simulaciones';

                        if ($sim_count > 0) {
                            $status = $completed > 0 ? 'Completado' : 'Iniciado/Incompleto';
                        }

                        if ($has_results) {
                            $res = json_decode($row->latest_results, true);
                            if (is_array($res)) {
                                if (!empty($res['final_scores'])) {
                                    $fs = $res['final_scores'];
                                } else {
                                    // Calcular URR desde radar_selections
                                    $radar = $res['radar_selections'] ?? [];
                                    $radar_correct = 0;
                                    foreach ($radar as $r) { if ($r['correct_type'] === $r['selected_type']) $radar_correct++; }
                                    $urr = count($radar) > 0 ? round(($radar_correct / count($radar)) * 100) : 0;

                                    // Calcular CCC desde stakeholder_selections
                                    $stk = $res['stakeholder_selections'] ?? [];
                                    $stk_correct = 0;
                                    foreach ($stk as $s) { if ($s['correct_tool'] === $s['selected_tool']) $stk_correct++; }
                                    $ccc = count($stk) > 0 ? round(($stk_correct / count($stk)) * 100) : 0;

                                    // Calcular TTR desde decisions
                                    $decs = $res['decisions'] ?? [];
                                    $total_time = 0;
                                    foreach ($decs as $d) { $total_time += $d['time'] ?? 0; }
                                    $avg_time = count($decs) > 0 ? ($total_time / count($decs)) : 0;
                                    $ttr = max(0, 100 - ($avg_time * 8));

                                    $fs = ['urr' => $urr, 'ccc' => $ccc, 'ttr' => $ttr];
                                }
                                $score = round(( ($fs['urr'] ?? 0) + ($fs['ccc'] ?? 0) + ($fs['ttr'] ?? 0) ) / 3) . '%';
                            }
                        }

                        $status_color = '#999';
                        if ($status === 'Completado') $status_color = '#28a745';
                        elseif ($status === 'Iniciado/Incompleto') $status_color = '#ffb800';
                    ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <input id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="bulk-ids[]" value="<?php echo $row->id; ?>">
                            </th>
                            <td><?php echo date( 'd/m/Y H:i', strtotime( $row->created_at ) ); ?></td>
                            <td><strong><?php echo esc_html( $row->user_name ); ?></strong></td>
                            <td><?php echo esc_html( $row->company_name ); ?></td>
                            <td><?php echo esc_html( $row->user_email ); ?></td>
                            <td style="text-align: center;">
                                <span style="background: #2271b1; color: #fff; padding: 2px 10px; border-radius: 10px; font-size: 12px; font-weight: 700;"><?php echo $sim_count; ?></span>
                            </td>
                            <td><strong><?php echo $score; ?></strong></td>
                            <td>
                                <span class="badge" style="background: <?php echo $status_color; ?>; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 10px;"><?php echo $status; ?></span>
                            </td>
                            <td>
                                <?php if ($row->latest_sim_id): ?>
                                    <a href="<?php echo admin_url('admin.php?page=sdc-applicants&action=export&id=' . $row->latest_sim_id); ?>" 
                                       class="button button-secondary" 
                                       target="_blank">PDF</a>
                                <?php else: ?>
                                    <span style="opacity: 0.3;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo wp_nonce_url( admin_url('admin.php?page=sdc-applicants&action=delete&id=' . $row->id), 'sdc_delete_applicant_' . $row->id ); ?>" 
                                   class="button button-link-delete" 
                                   onclick="return confirm('¿Eliminar este participante y todas sus simulaciones?');"
                                   style="color: #d63638;">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="10">No se encontraron registros.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </form>

        <!-- Paginación -->
        <?php if ($total_pages > 1): ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo $total_records; ?> participantes</span>
                    <?php
                    echo paginate_links( array(
                        'base' => add_query_arg( 'paged', '%#%' ),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $paged
                    ) );
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <style>
        @media print {
            #adminmenuback, #adminmenuwrap, #wpadminbar, .wp-header-end, .wp-heading-inline, .page-title-action, .tablenav {
                display: none !important;
            }
            #wpbody-content { margin-left: 0 !important; }
            .wrap { margin: 0; padding: 20px; }
            .wp-list-table { border: 1px solid #eee; width: 100%; border-collapse: collapse; }
            .wp-list-table th, .wp-list-table td { border: 1px solid #eee; padding: 8px; text-align: left; }
        }
    </style>
    <?php
}

