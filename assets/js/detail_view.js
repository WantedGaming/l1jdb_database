/**
 * Detail View Enhancement Script
 * Adds interactivity to the detail view pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add type-specific class to the detail container based on the page URL
    const detailContainer = document.querySelector('.detail-container');
    if (detailContainer) {
        const currentUrl = window.location.pathname;
        
        if (currentUrl.includes('item_detail_view.php')) {
            detailContainer.classList.add('item-specific');
        } else if (currentUrl.includes('npc_detail_view.php')) {
            detailContainer.classList.add('npc-specific');
        } else if (currentUrl.includes('spell_detail_view.php')) {
            detailContainer.classList.add('spell-specific');
        } else if (currentUrl.includes('craft_detail_view.php')) {
            detailContainer.classList.add('craft-specific');
        } else if (currentUrl.includes('entermaps_detail_view.php')) {
            detailContainer.classList.add('entermap-specific');
        } else if (currentUrl.includes('ndl_detail_view.php')) {
            detailContainer.classList.add('ndl-specific');
        }
    }
    
    // Create Back to Top button
    const backToTopBtn = document.createElement('div');
    backToTopBtn.className = 'back-to-top';
    document.body.appendChild(backToTopBtn);
    
    // Show/hide back to top button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
    
    // Scroll to top when clicking the button
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Add tooltips to elements with data-tooltip attribute
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        const tooltip = document.createElement('span');
        tooltip.className = 'info-tooltip';
        tooltip.setAttribute('data-tooltip', element.getAttribute('data-tooltip'));
        element.appendChild(tooltip);
    });
    
    // Add highlighting for special values
    const detailValues = document.querySelectorAll('.detail-item span, .info-item span');
    detailValues.forEach(value => {
        const text = value.textContent.trim();
        
        // Highlight positive values
        if (text.includes('+') && !isNaN(parseFloat(text.replace('+', '')))) {
            value.classList.add('value-positive');
        }
        
        // Highlight negative values
        if (text.includes('-') && !isNaN(parseFloat(text.replace('-', '')))) {
            value.classList.add('value-negative');
        }
        
        // Highlight special keywords
        const keywords = ['Unique', 'Rare', 'Legendary', 'Epic', 'Uncommon', 'Common'];
        keywords.forEach(keyword => {
            if (text.includes(keyword)) {
                value.classList.add('value-highlight');
            }
        });
    });
    
    // Enhance image preview with zoom effect
    const imageContainer = document.querySelector('.weapon-image-container');
    if (imageContainer) {
        const image = imageContainer.querySelector('img');
        
        imageContainer.addEventListener('mouseenter', function() {
            imageContainer.style.transform = 'scale(1.1)';
            image.style.filter = 'drop-shadow(0 4px 8px rgba(0, 0, 0, 0.6))';
        });
        
        imageContainer.addEventListener('mouseleave', function() {
            imageContainer.style.transform = '';
            image.style.filter = '';
        });
    }
    
    // Add stat badges for elements with specific classes
    const statElements = document.querySelectorAll('.stat-element');
    statElements.forEach(element => {
        const statType = element.getAttribute('data-stat-type');
        if (statType) {
            const badge = document.createElement('span');
            badge.className = `stat-badge stat-badge-${statType.toLowerCase()}`;
            badge.textContent = statType.toUpperCase();
            element.prepend(badge);
        }
    });
    
    // Handle collapsible sections
    const sectionHeaders = document.querySelectorAll('.detail-section h2');
    sectionHeaders.forEach(header => {
        // Make section collapsible
        header.style.cursor = 'pointer';
        
        // Add indicator
        const indicator = document.createElement('span');
        indicator.textContent = '▼';
        indicator.style.marginLeft = '0.5rem';
        indicator.style.fontSize = '0.8rem';
        indicator.style.opacity = '0.7';
        header.appendChild(indicator);
        
        // Get the content of this section (all elements until the next section)
        const section = header.closest('.detail-section');
        const content = section.querySelectorAll('.detail-grid, .related-items-grid, .detail-text');
        
        // Toggle section on click
        header.addEventListener('click', function() {
            const isCollapsed = section.classList.contains('collapsed');
            
            if (isCollapsed) {
                // Expand
                content.forEach(el => {
                    el.style.display = '';
                });
                indicator.textContent = '▼';
                section.classList.remove('collapsed');
            } else {
                // Collapse
                content.forEach(el => {
                    el.style.display = 'none';
                });
                indicator.textContent = '►';
                section.classList.add('collapsed');
            }
        });
    });
});