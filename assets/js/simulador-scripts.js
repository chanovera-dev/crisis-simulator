document.addEventListener('DOMContentLoaded', () => {
    console.log('SDC: Sistema inicializado.');

    // Limpiar datos de simulaciones anteriores al cargar
    sessionStorage.removeItem('sdc_results');
    sessionStorage.removeItem('sdc_simulation_id');

    // Iconos SVG reutilizables
    const iconCheck = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>';

    const triggerAnimations = (container) => {
        const animatables = container.querySelectorAll('.sdc-animate');
        if (typeof animateIn === "function") {
            animateIn(animatables, ['animate-in'], { threshold: 0.01, stagger: 100 });
        } else {
            animatables.forEach(el => el.classList.add('animate-in'));
        }
    };

    window.sdc_move_to_step = (stepNumber) => {
        const sections = document.querySelectorAll('.sdc-section');
        const targetSection = document.querySelector(`.sdc-section[data-step="${stepNumber}"]`);

        if (targetSection) {
            sections.forEach(s => s.classList.remove('active'));
            targetSection.classList.add('active');
            triggerAnimations(targetSection);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    document.addEventListener('click', (e) => {
        const btnNext = e.target.closest('.sdc-btn-next');
        const btnPrev = e.target.closest('.sdc-btn-prev');
        if (btnNext && !btnNext.classList.contains('disabled')) {
            const nextStep = btnNext.getAttribute('data-next');
            if (nextStep) window.sdc_move_to_step(nextStep);
        }
        if (btnPrev && !btnPrev.classList.contains('disabled')) {
            const prevStep = btnPrev.getAttribute('data-prev');
            if (prevStep) window.sdc_move_to_step(prevStep);
        }
    });

    // --- SECCIÓN 2: VALIDACIÓN REFORZADA ---
    const initForm = document.getElementById('sdc-init-form');
    if (initForm) {
        const checkFormValidity = () => {
            const isValid = initForm.checkValidity();
            const nextBtns = document.querySelectorAll('.sdc-section-2 .sdc-btn-next');
            const submitBtn = initForm.querySelector('button[type="submit"]');

            const applyState = (el) => {
                if (!el) return;
                el.classList.toggle('disabled', !isValid);
                el.style.opacity = isValid ? '1' : '0.5';
                el.style.pointerEvents = isValid ? 'all' : 'none';
            };

            nextBtns.forEach(applyState);
            applyState(submitBtn);
        };

        initForm.addEventListener('input', checkFormValidity);
        initForm.onsubmit = (e) => {
            e.preventDefault();
            if (!initForm.checkValidity()) return;
            const formData = new FormData(initForm);
            formData.append('action', 'sdc_save_applicant');
            fetch(sdc_ajax.ajax_url, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Limpiar datos de simulaciones anteriores
                        sessionStorage.removeItem('sdc_results');
                        sessionStorage.setItem('sdc_simulation_id', data.data.simulation_id);

                        // Guardar datos del participante
                        sessionStorage.setItem('sdc_participant', JSON.stringify({
                            company: initForm.querySelector('[name="company_name"]').value,
                            name: initForm.querySelector('[name="user_name"]').value,
                            position: initForm.querySelector('[name="user_position"]').value,
                            email: initForm.querySelector('[name="user_email"]').value
                        }));

                        window.sdc_move_to_step(3);
                    }
                });
        };

        // Ejecución inmediata y retardada (por si el navegador autocompleta)
        checkFormValidity();
        setTimeout(checkFormValidity, 500);
    }

    // --- SECCIÓN 3: TRIAGE ---
    window.sdc_init_triage = () => {
        const threatData = [
            { id: 'fake-news', text: 'Noticia falsa sobre ingredientes tóxicos en redes sociales.' },
            { id: 'regulatory', text: 'Nueva regulación europea sobre etiquetado de alérgenos.' },
            { id: 'industry', text: 'Brote de salmonela detectado en planta de procesamiento.' },
            { id: 'corporate', text: 'Filtración de correos internos criticando la calidad del producto.' }
        ];
        let currentIndex = 0;
        const totalThreats = threatData.length;
        const card = document.getElementById('current-threat-card');
        const textDisplay = document.getElementById('threat-text-display');
        const countDisplay = document.getElementById('triage-count');
        const dots = document.querySelectorAll('#sdc-triage-dots .dot');
        const feedback = document.getElementById('triage-feedback');
        const btns = document.querySelectorAll('.sdc-triage-btn');

        if (!card || !textDisplay) return;

        const updateCard = () => {
            if (currentIndex >= totalThreats) {
                textDisplay.textContent = 'ESCANEO COMPLETADO. PROTOCOLOS ACTIVADOS.';
                dots.forEach(dot => dot.classList.add('active'));
                document.querySelectorAll('.sdc-section-3 .sdc-btn-next').forEach(btn => btn.classList.remove('disabled'));
                return;
            }
            const threat = threatData[currentIndex];
            textDisplay.textContent = threat.text;
            countDisplay.textContent = currentIndex + 1;
            dots.forEach((dot, idx) => dot.classList.toggle('active', idx === currentIndex));
            card.classList.remove('card-exit-fake-news', 'card-exit-regulatory', 'card-exit-industry', 'card-exit-corporate');
        };

        btns.forEach(btn => {
            btn.onclick = () => {
                const type = btn.getAttribute('data-type');

                // Guardar selección en sessionStorage
                const results = JSON.parse(sessionStorage.getItem('sdc_results') || '{}');
                if (!results.radar_selections) results.radar_selections = [];
                results.radar_selections.push({
                    text: threatData[currentIndex].text,
                    correct_type: threatData[currentIndex].id,
                    selected_type: type
                });
                sessionStorage.setItem('sdc_results', JSON.stringify(results));

                if (feedback) {
                    feedback.classList.add('show');
                    setTimeout(() => feedback.classList.remove('show'), 600);
                }
                card.classList.add(`card-exit-${type}`);
                setTimeout(() => { currentIndex++; updateCard(); }, 600);
            };
        });
        updateCard();
    };

    // --- SECCIÓN 4: STAKEHOLDERS ---
    window.sdc_init_stakeholders = () => {
        const modules = document.querySelectorAll('.sdc-stk-module');
        const picker = document.getElementById('sdc-tool-picker');
        const closePicker = document.getElementById('close-picker');
        let activeModule = null;
        let selections = {};

        const openPicker = (module) => {
            activeModule = module;
            picker.classList.add('show');
            picker.style.display = 'flex';
        };

        const closePickerFunc = () => {
            picker.classList.remove('show');
            picker.style.display = 'none';
            activeModule = null;
        };

        modules.forEach(mod => {
            const btn = mod.querySelector('.btn-assign-tool');
            if (btn) btn.onclick = (e) => { e.preventDefault(); openPicker(mod); };
        });

        if (closePicker) closePicker.onclick = closePickerFunc;

        // Cerrar con tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && picker.classList.contains('show')) {
                closePickerFunc();
            }
        });

        picker.onclick = (e) => {
            // Cerrar con botón CANCELAR
            if (e.target.closest('#close-picker') || e.target.id === 'close-picker') {
                e.stopPropagation();
                closePickerFunc();
                return;
            }

            // Cerrar al hacer clic en el fondo oscuro (fuera del contenido)
            if (e.target === picker) {
                closePickerFunc();
                return;
            }

            const option = e.target.closest('.picker-option');
            if (option) {
                const toolId = option.getAttribute('data-tool');
                const toolLabel = option.querySelector('strong').textContent;
                if (!activeModule) return;
                const stakeholderId = activeModule.getAttribute('data-id');
                const stakeholderName = activeModule.querySelector('h3').textContent;
                const target = activeModule.getAttribute('data-target');

                activeModule.classList.add('assigned');
                const btn = activeModule.querySelector('.btn-assign-tool');
                if (btn) btn.innerHTML = `${iconCheck} ${toolLabel}`;

                selections[stakeholderId] = {
                    stakeholder: stakeholderName,
                    correct_tool: target,
                    selected_tool: toolId,
                    tool_label: toolLabel
                };

                const results = JSON.parse(sessionStorage.getItem('sdc_results') || '{}');
                results.stakeholder_selections = Object.values(selections);
                if (Object.keys(selections).length === 4) {
                    document.querySelectorAll('.sdc-section-4 .sdc-btn-next').forEach(btn => {
                        btn.classList.remove('disabled');
                        btn.style.opacity = '1';
                        btn.style.pointerEvents = 'all';
                    });
                }
                sessionStorage.setItem('sdc_results', JSON.stringify(results));
                closePickerFunc();
            }
        };
    };

    // --- WAR ROOM (PASOS 5, 6, 7) ---
    window.sdc_init_warroom = (step) => {
        const subStep = document.querySelector(`.sdc-warroom-step[data-war-step="${step}"]`);
        if (!subStep) return;

        document.querySelectorAll('.sdc-warroom-step').forEach(s => s.style.display = 'none');
        subStep.style.display = 'block';

        const progressFill = document.getElementById('sdc-warroom-progress');
        const progressText = document.getElementById('sdc-warroom-step-num');
        if (progressFill) progressFill.style.width = ((step - 4) / 3 * 100) + '%';
        if (progressText) progressText.textContent = (step - 4) + ' / 3';

        const duration = parseInt(subStep.getAttribute('data-time'));
        const timerVal = document.querySelector('.sdc-timer-value');
        const options = subStep.querySelectorAll('.sdc-option-btn');

        let timeLeft = duration;
        if (window.sdc_warroom_timer) clearInterval(window.sdc_warroom_timer);

        if (timerVal) timerVal.textContent = timeLeft + 's';

        window.sdc_warroom_timer = setInterval(() => {
            timeLeft--;
            if (timerVal) timerVal.textContent = timeLeft + 's';
            if (timeLeft <= 0) {
                clearInterval(window.sdc_warroom_timer);
                window.sdc_record_decision(step, "Tiempo agotado", 0, duration);
                window.sdc_move_to_step(parseInt(step) + 1);
            }
        }, 1000);

        options.forEach(btn => {
            btn.onclick = () => {
                clearInterval(window.sdc_warroom_timer);
                const score = parseInt(btn.getAttribute('data-score'));
                const text = btn.textContent.trim();
                window.sdc_record_decision(step, text, score, duration - timeLeft);
                window.sdc_move_to_step(parseInt(step) + 1);
            };
        });
    };

    window.sdc_record_decision = (step, text, score, time) => {
        const results = JSON.parse(sessionStorage.getItem('sdc_results') || '{}');
        if (!results.decisions) results.decisions = [];
        // Reemplazar si ya existe una decisión para este paso
        const existing = results.decisions.findIndex(d => d.step === step);
        if (existing !== -1) {
            results.decisions[existing] = { step, text, score, time };
        } else {
            results.decisions.push({ step, text, score, time });
        }
        sessionStorage.setItem('sdc_results', JSON.stringify(results));
    };

    // --- DASHBOARD (PASO 8) ---
    window.sdc_init_dashboard = () => {
        const results = JSON.parse(sessionStorage.getItem('sdc_results') || '{}');
        const simulation_id = sessionStorage.getItem('sdc_simulation_id');
        const decisions = results.decisions || [];
        const radarSels = results.radar_selections || [];
        const stakeholderSels = results.stakeholder_selections || [];

        const radarCorrect = radarSels.filter(s => s.correct_type === s.selected_type).length;
        const urrScore = Math.round((radarCorrect / (radarSels.length || 1)) * 100);
        const stakeholderCorrect = stakeholderSels.filter(s => s.correct_tool === s.selected_tool).length;
        const cccScore = Math.round((stakeholderCorrect / (stakeholderSels.length || 1)) * 100);
        const avgTime = decisions.reduce((acc, d) => acc + d.time, 0) / (decisions.length || 1);
        const ttrScore = Math.max(0, 100 - (avgTime * 8));

        const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        const setWidth = (id, val) => { const el = document.getElementById(id); if (el) el.style.width = val; };

        setVal('sdc-val-urr', urrScore + '%');
        setVal('sdc-val-ccc', cccScore + '%');
        setVal('sdc-val-ttr', Math.round(ttrScore) + '%');
        setWidth('sdc-fill-urr', urrScore + '%');
        setWidth('sdc-fill-ccc', cccScore + '%');
        setWidth('sdc-fill-ttr', Math.round(ttrScore) + '%');

        results.final_scores = { urr: urrScore, ccc: cccScore, ttr: ttrScore };
        sessionStorage.setItem('sdc_results', JSON.stringify(results));

        if (simulation_id) {
            console.log('SDC Dashboard: Guardando con simulation_id =', simulation_id);
            const formData = new FormData();
            formData.append('action', 'sdc_save_results');
            formData.append('simulation_id', simulation_id);
            formData.append('results_json', JSON.stringify(results));
            fetch(sdc_ajax.ajax_url, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => console.log('SDC Dashboard: Respuesta servidor =', data))
                .catch(err => console.error('SDC Dashboard: Error guardando =', err));
        } else {
            console.warn('SDC Dashboard: NO HAY simulation_id');
        }

        const log = document.getElementById('sdc-decision-log');
        if (log) {
            let logHTML = '';
            radarSels.forEach(s => {
                const ok = s.correct_type === s.selected_type;
                logHTML += `<div class="sdc-decision-item"><div class="sdc-decision-icon ${ok ? 'correct' : 'wrong'}">${ok ? '✓' : '✕'}</div><div class="sdc-decision-info"><span class="sdc-mini-label">RADAR DE RIESGOS</span><h3>${s.text}</h3></div></div>`;
            });
            stakeholderSels.forEach(s => {
                const ok = s.correct_tool === s.selected_tool;
                logHTML += `<div class="sdc-decision-item"><div class="sdc-decision-icon ${ok ? 'correct' : 'wrong'}">${ok ? '✓' : '✕'}</div><div class="sdc-decision-info"><span class="sdc-mini-label">MAPA DE STAKEHOLDERS</span><h3>${s.stakeholder}</h3><p>${ok ? 'Herramienta óptima: ' + s.tool_label : 'Herramienta seleccionada (' + s.tool_label + ') no es la ideal.'}</p></div></div>`;
            });
            decisions.forEach(d => {
                const ok = d.score >= 100;
                logHTML += `<div class="sdc-decision-item"><div class="sdc-decision-icon ${ok ? 'correct' : 'wrong'}">${ok ? '✓' : '✕'}</div><div class="sdc-decision-info"><span class="sdc-mini-label">WAR ROOM: ESCENARIO ${d.step - 4}</span><h3>${d.text}</h3></div><div class="sdc-decision-score">Score: ${d.score} | ${d.time}s</div></div>`;
            });
            log.innerHTML = logHTML;
        }

        if (window.Chart) {
            new Chart(document.getElementById('sdc-radar-chart'), {
                type: 'radar',
                data: { labels: ['Radar (URR)', 'Comité (CCC)', 'Tiempo (TTR)'], datasets: [{ label: 'Perfil de Gestión', data: [urrScore, cccScore, ttrScore], backgroundColor: 'rgba(255, 184, 0, 0.2)', borderColor: '#ffb800', borderWidth: 2 }] },
                options: { scales: { r: { beginAtZero: true, max: 100, ticks: { display: false } } } }
            });
            new Chart(document.getElementById('sdc-bar-chart'), {
                type: 'bar',
                data: { labels: decisions.map(d => 'Escenario ' + (d.step - 4)), datasets: [{ label: 'Puntuación War Room', data: decisions.map(d => d.score), backgroundColor: decisions.map(d => d.score >= 100 ? '#28a745' : '#ff3b30'), borderRadius: 5 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100 } } }
            });
        }
    };

    // --- SECCIÓN 9: RESUMEN DEL PARTICIPANTE ---
    window.sdc_init_results = () => {
        // Datos del participante
        const participant = JSON.parse(sessionStorage.getItem('sdc_participant') || '{}');
        const setField = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || '—'; };
        setField('sdc-res-company', participant.company);
        setField('sdc-res-name', participant.name);
        setField('sdc-res-position', participant.position);
        setField('sdc-res-email', participant.email);

        // Métricas
        const results = JSON.parse(sessionStorage.getItem('sdc_results') || '{}');
        const scores = results.final_scores || {};
        setField('sdc-res-urr', (scores.urr || 0) + '%');
        setField('sdc-res-ccc', (scores.ccc || 0) + '%');
        setField('sdc-res-ttr', Math.round(scores.ttr || 0) + '%');

        // Decisiones
        const log = document.getElementById('sdc-res-decisions');
        if (log) {
            let html = '';
            const radarSels = results.radar_selections || [];
            const stakeholderSels = results.stakeholder_selections || [];
            const decisions = results.decisions || [];

            radarSels.forEach(s => {
                const ok = s.correct_type === s.selected_type;
                html += `<div class="sdc-decision-item"><div class="sdc-decision-icon ${ok ? 'correct' : 'wrong'}">${ok ? '✓' : '✕'}</div><div class="sdc-decision-info"><span class="sdc-mini-label">RADAR</span><h3>${s.text}</h3></div></div>`;
            });
            stakeholderSels.forEach(s => {
                const ok = s.correct_tool === s.selected_tool;
                html += `<div class="sdc-decision-item"><div class="sdc-decision-icon ${ok ? 'correct' : 'wrong'}">${ok ? '✓' : '✕'}</div><div class="sdc-decision-info"><span class="sdc-mini-label">STAKEHOLDERS</span><h3>${s.stakeholder}: ${s.tool_label}</h3></div></div>`;
            });
            decisions.forEach(d => {
                const ok = d.score >= 100;
                html += `<div class="sdc-decision-item"><div class="sdc-decision-icon ${ok ? 'correct' : 'wrong'}">${ok ? '✓' : '✕'}</div><div class="sdc-decision-info"><span class="sdc-mini-label">WAR ROOM</span><h3>${d.text}</h3></div></div>`;
            });
            log.innerHTML = html;
        }

        // Guardar en servidor
        const simulation_id = sessionStorage.getItem('sdc_simulation_id');
        console.log('SDC Results: simulation_id =', simulation_id);
        console.log('SDC Results: results =', results);

        if (simulation_id) {
            const formData = new FormData();
            formData.append('action', 'sdc_save_results');
            formData.append('simulation_id', simulation_id);
            formData.append('results_json', JSON.stringify(results));
            fetch(sdc_ajax.ajax_url, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => console.log('SDC Results: Respuesta servidor =', data))
                .catch(err => console.error('SDC Results: Error guardando =', err));
        } else {
            console.warn('SDC Results: NO HAY simulation_id, los datos NO se guardarán en el servidor.');
        }
    };

    // --- CARGA INICIAL ---
    const initSimulator = () => {
        const firstSection = document.querySelector('.sdc-section.active');
        if (firstSection) triggerAnimations(firstSection);
    };
    setTimeout(initSimulator, 300);

    window.sdc_warroom_timer = null;
    const originalMove = window.sdc_move_to_step;
    window.sdc_move_to_step = (stepNumber) => {
        const step = parseInt(stepNumber);

        // War Room (pasos 5, 6, 7)
        if (step >= 5 && step <= 7) {
            document.querySelectorAll('.sdc-section').forEach(s => s.classList.remove('active'));
            const parent = document.querySelector('.sdc-warroom-parent');
            if (parent) {
                parent.classList.add('active');
                parent.style.display = 'flex';
                triggerAnimations(parent);
            }
            setTimeout(() => window.sdc_init_warroom(step), 100);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        } else {
            const parent = document.querySelector('.sdc-warroom-parent');
            if (parent) {
                parent.classList.remove('active');
                parent.style.display = '';
            }
        }

        if (typeof originalMove === "function") originalMove(stepNumber);
        if (step === 3) setTimeout(window.sdc_init_triage, 100);
        if (step === 4) setTimeout(window.sdc_init_stakeholders, 100);
        if (step === 8) setTimeout(window.sdc_init_dashboard, 100);
        if (step === 9) setTimeout(window.sdc_init_results, 100);
    };
});
