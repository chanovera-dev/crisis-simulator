function animateIn(selectors, animationClasses = ['animate-in'], options = {}) {
    const {
        threshold = 0.1,
        stagger = 100
    } = options;

    let targets;
    if (typeof selectors === 'string') {
        targets = Array.from(document.querySelectorAll(selectors));
    } else if (selectors instanceof NodeList || Array.isArray(selectors)) {
        targets = Array.from(selectors);
    } else if (selectors instanceof HTMLElement) {
        targets = [selectors];
    } else {
        return;
    }

    // Filter out already animated or observing elements
    targets = targets.filter(t => !animationClasses.some(cls => t.classList.contains(cls)) && !t.dataset.observing);

    if (!targets.length) return;

    const observer = new IntersectionObserver((entries) => {
        let visibleCount = 0;
        entries.forEach((entry) => {
            // Un chequeo más permisivo para asegurar el disparo
            if (entry.isIntersecting) {
                const target = entry.target;
                delete target.dataset.observing;

                setTimeout(() => {
                    animationClasses.forEach(cls => target.classList.add(cls));
                }, visibleCount * stagger);

                visibleCount++;
                observer.unobserve(target);
            }
        });
    }, { threshold });

    targets.forEach(target => {
        target.dataset.observing = "true";
        observer.observe(target);
    });
}