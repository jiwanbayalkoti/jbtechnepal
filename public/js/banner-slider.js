/**
 * Banner Slider Component
 * Fetches and displays banners from the API
 */
class BannerSlider {
    /**
     * Constructor
     * @param {string} containerId - ID of container element where banners will be displayed
     * @param {Object} options - Configuration options
     */
    constructor(containerId, options = {}) {
        // Default options
        this.options = {
            autoplay: true,
            interval: 5000, // 5 seconds
            arrows: true,
            indicators: true,
            pageId: null,
            limit: null,
            ...options
        };
        
        // Get container element
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Banner slider container with ID "${containerId}" not found`);
            return;
        }
        
        this.currentSlide = 0;
        this.banners = [];
        this.autoplayInterval = null;
        
        // Initialize
        this.init();
    }
    
    /**
     * Initialize the slider
     */
    init() {
        // Add loading spinner
        this.showLoading();
        
        // Fetch banners from API
        this.fetchBanners()
            .then(() => {
                // Initialize slider
                this.renderSlider();
                this.initEvents();
                
                // Start autoplay if enabled
                if (this.options.autoplay) {
                    this.startAutoplay();
                }
                
                // Hide loading spinner
                this.hideLoading();
            })
            .catch(error => {
                console.error('Error fetching banners:', error);
                this.container.innerHTML = `
                    <div class="alert alert-danger">
                        Failed to load banners. Please try again later.
                    </div>
                `;
                this.hideLoading();
            });
    }
    
    /**
     * Show loading spinner
     */
    showLoading() {
        this.container.innerHTML = `
            <div class="d-flex justify-content-center align-items-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
    }
    
    /**
     * Hide loading spinner
     */
    hideLoading() {
        const loader = this.container.querySelector('.spinner-border');
        if (loader) {
            loader.parentElement.remove();
        }
    }
    
    /**
     * Fetch banners from API
     */
    async fetchBanners() {
        let url = '/api/banners';
        const params = new URLSearchParams();
        
        if (this.options.pageId) {
            params.append('page_id', this.options.pageId);
        }
        
        if (this.options.limit) {
            params.append('limit', this.options.limit);
        }
        
        if (params.toString()) {
            url += `?${params.toString()}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (!data.success || !data.banners) {
            throw new Error('Failed to fetch banners');
        }
        
        this.banners = data.banners;
        
        // No banners found
        if (this.banners.length === 0) {
            this.container.innerHTML = '';
            return;
        }
    }
    
    /**
     * Render slider HTML
     */
    renderSlider() {
        if (this.banners.length === 0) {
            return;
        }
        
        // Create slider container
        const sliderContainer = document.createElement('div');
        sliderContainer.className = 'banner-slider';
        
        // Create slides container
        const slidesContainer = document.createElement('div');
        slidesContainer.className = 'banner-slider-slides';
        
        // Create slides
        this.banners.forEach((banner, index) => {
            const slide = document.createElement('div');
            slide.className = `banner-slide ${index === 0 ? 'active' : ''}`;
            
            // Get image URL (either primary image or first image)
            const imageUrl = banner.image_url;
            
            // Create slide content
            slide.innerHTML = `
                <div class="banner-image" style="background-image: url('${imageUrl}')"></div>
                <div class="banner-content">
                    <h2 class="banner-title">${banner.title}</h2>
                    ${banner.subtitle ? `<p class="banner-subtitle">${banner.subtitle}</p>` : ''}
                    ${banner.link ? `<a href="${banner.link}" class="btn btn-primary">Learn More</a>` : ''}
                </div>
            `;
            
            slidesContainer.appendChild(slide);
        });
        
        // Add slides to slider
        sliderContainer.appendChild(slidesContainer);
        
        // Add navigation arrows
        if (this.options.arrows && this.banners.length > 1) {
            const prevButton = document.createElement('button');
            prevButton.className = 'banner-slider-prev';
            prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
            prevButton.setAttribute('aria-label', 'Previous slide');
            
            const nextButton = document.createElement('button');
            nextButton.className = 'banner-slider-next';
            nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
            nextButton.setAttribute('aria-label', 'Next slide');
            
            sliderContainer.appendChild(prevButton);
            sliderContainer.appendChild(nextButton);
        }
        
        // Add indicators
        if (this.options.indicators && this.banners.length > 1) {
            const indicatorsContainer = document.createElement('div');
            indicatorsContainer.className = 'banner-slider-indicators';
            
            this.banners.forEach((_, index) => {
                const indicator = document.createElement('button');
                indicator.className = `indicator ${index === 0 ? 'active' : ''}`;
                indicator.setAttribute('data-index', index);
                indicator.setAttribute('aria-label', `Go to slide ${index + 1}`);
                indicatorsContainer.appendChild(indicator);
            });
            
            sliderContainer.appendChild(indicatorsContainer);
        }
        
        // Clear container and add slider
        this.container.innerHTML = '';
        this.container.appendChild(sliderContainer);
        
        // Store slider elements references
        this.slidesContainer = slidesContainer;
        this.slides = slidesContainer.querySelectorAll('.banner-slide');
        this.prevButton = sliderContainer.querySelector('.banner-slider-prev');
        this.nextButton = sliderContainer.querySelector('.banner-slider-next');
        this.indicators = sliderContainer.querySelectorAll('.indicator');
    }
    
    /**
     * Initialize event listeners
     */
    initEvents() {
        if (this.banners.length <= 1) {
            return;
        }
        
        // Previous button click
        if (this.prevButton) {
            this.prevButton.addEventListener('click', () => {
                this.goToSlide(this.currentSlide - 1);
                if (this.options.autoplay) {
                    this.resetAutoplay();
                }
            });
        }
        
        // Next button click
        if (this.nextButton) {
            this.nextButton.addEventListener('click', () => {
                this.goToSlide(this.currentSlide + 1);
                if (this.options.autoplay) {
                    this.resetAutoplay();
                }
            });
        }
        
        // Indicator clicks
        if (this.indicators) {
            this.indicators.forEach(indicator => {
                indicator.addEventListener('click', () => {
                    const index = parseInt(indicator.getAttribute('data-index'));
                    this.goToSlide(index);
                    if (this.options.autoplay) {
                        this.resetAutoplay();
                    }
                });
            });
        }
        
        // Pause autoplay on hover
        if (this.options.autoplay) {
            this.container.addEventListener('mouseenter', () => {
                this.stopAutoplay();
            });
            
            this.container.addEventListener('mouseleave', () => {
                this.startAutoplay();
            });
        }
        
        // Swipe support for touch devices
        let touchStartX = 0;
        let touchEndX = 0;
        
        this.slidesContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        this.slidesContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            
            // Detect swipe direction
            if (touchEndX < touchStartX) {
                // Swiped left - go to next slide
                this.goToSlide(this.currentSlide + 1);
            } else if (touchEndX > touchStartX) {
                // Swiped right - go to previous slide
                this.goToSlide(this.currentSlide - 1);
            }
            
            if (this.options.autoplay) {
                this.resetAutoplay();
            }
        }, { passive: true });
    }
    
    /**
     * Go to a specific slide
     * @param {number} index - Slide index
     */
    goToSlide(index) {
        // Handle wrap-around
        if (index < 0) {
            index = this.slides.length - 1;
        } else if (index >= this.slides.length) {
            index = 0;
        }
        
        // Update slide classes
        this.slides[this.currentSlide].classList.remove('active');
        this.slides[index].classList.add('active');
        
        // Update indicators
        if (this.indicators) {
            this.indicators[this.currentSlide].classList.remove('active');
            this.indicators[index].classList.add('active');
        }
        
        // Update current slide
        this.currentSlide = index;
    }
    
    /**
     * Start autoplay
     */
    startAutoplay() {
        if (this.banners.length <= 1) {
            return;
        }
        
        this.autoplayInterval = setInterval(() => {
            this.goToSlide(this.currentSlide + 1);
        }, this.options.interval);
    }
    
    /**
     * Stop autoplay
     */
    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
    
    /**
     * Reset autoplay timer
     */
    resetAutoplay() {
        this.stopAutoplay();
        this.startAutoplay();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const bannerContainers = document.querySelectorAll('[data-banner-slider]');
    
    bannerContainers.forEach(container => {
        const options = {};
        
        // Parse data attributes
        if (container.dataset.autoplay === 'false') {
            options.autoplay = false;
        }
        
        if (container.dataset.interval) {
            options.interval = parseInt(container.dataset.interval, 10);
        }
        
        if (container.dataset.arrows === 'false') {
            options.arrows = false;
        }
        
        if (container.dataset.indicators === 'false') {
            options.indicators = false;
        }
        
        if (container.dataset.pageId) {
            options.pageId = container.dataset.pageId;
        }
        
        if (container.dataset.limit) {
            options.limit = parseInt(container.dataset.limit, 10);
        }
        
        // Initialize slider
        new BannerSlider(container.id, options);
    });
}); 