document.addEventListener('DOMContentLoaded', function() {
    // Dropdown functionality
    const dropdownTriggers = document.querySelectorAll('.nav-item');
    
    dropdownTriggers.forEach(trigger => {
        const dropdown = trigger.querySelector('.dropdown');
        if (dropdown) {
            trigger.addEventListener('mouseenter', () => {
                dropdown.classList.add('active');
            });
            
            trigger.addEventListener('mouseleave', () => {
                dropdown.classList.remove('active');
            });
        }
    });
    
    // Card click functionality
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            const href = this.getAttribute('href');
            if (href) {
                window.location.href = href;
            }
        });
    });
    
    // Mobile menu toggle (if needed later)
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }
});
