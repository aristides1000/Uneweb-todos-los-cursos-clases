document.addEventListener('DOMContentLoaded', () => {
    // Confirmación visual para eliminar o vaciar elementos
    const btnVaciar = document.querySelector('a[href*="accion=vaciar"]');
    if (btnVaciar) {
        btnVaciar.addEventListener('click', (e) => {
            if (!confirm('¿Estás seguro de vaciar todo el carro de compras?')) {
                e.preventDefault();
            }
        });
    }

    // Efecto sutil en las tarjetas de productos
    const cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.borderColor = 'var(--secondary-color)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.borderColor = 'transparent';
        });
    });
});