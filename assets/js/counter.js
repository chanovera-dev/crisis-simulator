/**
 * Script para la animación de contadores numéricos del Simulador de Crisis.
 */
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.sdc-counter');
    
    const animateCounter = (el) => {
        const target = parseInt(el.getAttribute('data-target'), 10);
        const duration = parseInt(el.getAttribute('data-duration'), 10) || 2000;
        const prefix = el.getAttribute('data-prefix') || '';
        const suffix = el.getAttribute('data-suffix') || '';
        
        const decimals = el.getAttribute('data-decimals') || 0;
        
        let startTimestamp = null;
        
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            
            const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            
            const currentCount = easeProgress * target;
            
            // Formatear según decimales
            const formatted = currentCount.toLocaleString('es-ES', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
            
            el.innerText = `${prefix}${formatted}${suffix}`;
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        
        window.requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                animateCounter(entry.target);
                entry.target.classList.add('counted');
            }
        });
    }, { threshold: 0.2 });

    counters.forEach(counter => observer.observe(counter));
});
