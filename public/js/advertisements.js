/**
 * Advertisement Component
 * 
 * Loads and displays advertisements from the server based on position
 */
class AdvertisementManager {
    constructor() {
        this.baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
        this.init();
    }

    /**
     * Initialize the advertisement manager
     */
    init() {
        this.loadAdContainers();
    }

    /**
     * Find all ad containers and load advertisements
     */
    loadAdContainers() {
        // Find all ad containers
        const adContainers = document.querySelectorAll('[data-ad-position]');
        
        adContainers.forEach(container => {
            const position = container.getAttribute('data-ad-position');
            if (position) {
                this.loadAdsForPosition(position, container);
            }
        });
    }

    /**
     * Load advertisements for a specific position
     * 
     * @param {string} position - The advertisement position
     * @param {HTMLElement} container - The container element
     */
    loadAdsForPosition(position, container) {
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch(`${this.baseUrl}/ads/${position}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(ads => {
                if (ads && ads.length > 0) {
                    container.innerHTML = this.renderAds(ads, position);
                    
                    // Initialize sliders if needed
                    if (position === 'homepage_slider' && typeof Swiper !== 'undefined') {
                        new Swiper('.swiper-container', {
                            slidesPerView: 1,
                            spaceBetween: 0,
                            loop: true,
                            autoplay: {
                                delay: 5000,
                            },
                            pagination: {
                                el: '.swiper-pagination',
                                clickable: true,
                            },
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },
                        });
                    }
                } else {
                    console.log(`No advertisements found for position: ${position}`);
                    // Hide the container if no ads
                    container.style.display = 'none';
                }
            })
            .catch(error => {
                console.error(`Error loading advertisements for ${position}:`, error);
                // Hide the container on error
                container.style.display = 'none';
            });
    }

    /**
     * Render advertisements based on position
     * 
     * @param {Array} ads - The advertisements to render
     * @param {string} position - The advertisement position
     * @returns {string} HTML for the advertisements
     */
    renderAds(ads, position) {
        switch (position) {
            case 'homepage_slider':
                return this.renderSliderAds(ads);
            case 'sidebar':
                return this.renderSidebarAds(ads);
            case 'category_page':
                return this.renderBannerAds(ads);
            case 'product_page':
                return this.renderProductAds(ads);
            default:
                return this.renderDefaultAds(ads);
        }
    }

    /**
     * Render slider advertisements (for homepage)
     * 
     * @param {Array} ads - The advertisements to render
     * @returns {string} HTML for the slider
     */
    renderSliderAds(ads) {
        let html = `
            <div class="swiper-container">
                <div class="swiper-wrapper">
        `;
        
        ads.forEach(ad => {
            html += `
                <div class="swiper-slide">
                    <a href="javascript:void(0);" onclick="window.adManager.recordClick(${ad.id})" data-ad-id="${ad.id}">
                        <img src="${ad.image_url}" alt="${ad.title}" class="img-fluid w-100">
                    </a>
                </div>
            `;
        });
        
        html += `
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        `;
        
        return html;
    }

    /**
     * Render sidebar advertisements
     * 
     * @param {Array} ads - The advertisements to render
     * @returns {string} HTML for the sidebar ads
     */
    renderSidebarAds(ads) {
        let html = '';
        
        ads.forEach(ad => {
            html += `
                <div class="sidebar-ad mb-4">
                    <a href="javascript:void(0);" onclick="window.adManager.recordClick(${ad.id})" data-ad-id="${ad.id}">
                        <img src="${ad.image_url}" alt="${ad.title}" class="img-fluid w-100">
                    </a>
                </div>
            `;
        });
        
        return html;
    }

    /**
     * Render banner advertisements (for category pages)
     * 
     * @param {Array} ads - The advertisements to render
     * @returns {string} HTML for the banner ads
     */
    renderBannerAds(ads) {
        let html = '';
        
        ads.forEach(ad => {
            html += `
                <div class="banner-ad mb-4">
                    <a href="javascript:void(0);" onclick="window.adManager.recordClick(${ad.id})" data-ad-id="${ad.id}">
                        <img src="${ad.image_url}" alt="${ad.title}" class="img-fluid w-100">
                    </a>
                </div>
            `;
        });
        
        return html;
    }

    /**
     * Render product page advertisements
     * 
     * @param {Array} ads - The advertisements to render
     * @returns {string} HTML for the product page ads
     */
    renderProductAds(ads) {
        let html = '';
        
        ads.forEach(ad => {
            html += `
                <div class="product-ad mb-4">
                    <a href="javascript:void(0);" onclick="window.adManager.recordClick(${ad.id})" data-ad-id="${ad.id}">
                        <img src="${ad.image_url}" alt="${ad.title}" class="img-fluid w-100">
                    </a>
                </div>
            `;
        });
        
        return html;
    }

    /**
     * Render default advertisements
     * 
     * @param {Array} ads - The advertisements to render
     * @returns {string} HTML for the default ads
     */
    renderDefaultAds(ads) {
        let html = '';
        
        ads.forEach(ad => {
            html += `
                <div class="ad-item mb-3">
                    <a href="javascript:void(0);" onclick="window.adManager.recordClick(${ad.id})" target="_blank" data-ad-id="${ad.id}">
                        <img src="${ad.image_url}" alt="${ad.title}" class="img-fluid w-100">
                    </a>
                </div>
            `;
        });
        
        return html;
    }

    /**
     * Record a click on an advertisement and redirect to its URL
     * 
     * @param {number} adId - The advertisement ID
     */
    recordClick(adId) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch(`${this.baseUrl}/ad/click/${adId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.url) {
                window.open(data.url, '_blank');
            }
        })
        .catch(error => {
            console.error('Error recording ad click:', error);
        });
    }
}

// Initialize the Advertisement Manager when the DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.adManager = new AdvertisementManager();
}); 