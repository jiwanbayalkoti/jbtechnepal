// Mega Menu functionality
document.addEventListener('DOMContentLoaded', function () {
    // Handle submenu display in mega menu
    const dropdownSubmenus = document.querySelectorAll('.dropdown-submenu');
    
    dropdownSubmenus.forEach(submenu => {
        const submenuToggle = submenu.querySelector('.dropdown-item');
        const submenuContent = submenu.querySelector('.submenu');
        
        if (submenuToggle && submenuContent) {
            submenuToggle.addEventListener('mouseenter', function() {
                // Close any open submenus first
                document.querySelectorAll('.submenu.show').forEach(menu => {
                    if (menu !== submenuContent) {
                        menu.classList.remove('show');
                    }
                });
                
                // Show this submenu
                submenuContent.classList.add('show');
            });
            
            submenu.addEventListener('mouseleave', function() {
                submenuContent.classList.remove('show');
            });
        }
    });
    
    // Handle mega menu dropdowns on hover for desktop
    if (window.innerWidth >= 992) {
        const megaMenuItems = document.querySelectorAll('.dropdown-mega');
        
        megaMenuItems.forEach(menuItem => {
            menuItem.addEventListener('mouseenter', function() {
                const dropdown = this.querySelector('.dropdown-toggle');
                const dropdownMenu = this.querySelector('.dropdown-menu');
                
                if (dropdown && dropdownMenu) {
                    dropdown.setAttribute('aria-expanded', 'true');
                    dropdown.classList.add('show');
                    dropdownMenu.classList.add('show');
                }
            });
            
            menuItem.addEventListener('mouseleave', function() {
                const dropdown = this.querySelector('.dropdown-toggle');
                const dropdownMenu = this.querySelector('.dropdown-menu');
                
                if (dropdown && dropdownMenu) {
                    dropdown.setAttribute('aria-expanded', 'false');
                    dropdown.classList.remove('show');
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    }
}); 