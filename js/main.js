// assets/js/main.js - JavaScript principal

document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartButton = document.getElementById('cartButton');
    const closeCart = document.querySelector('.close-cart');
    
    // Menu Mobile
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
            document.body.style.overflow = 'hidden';
        });
    }
    
    if (closeMobileMenu) {
        closeMobileMenu.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
            document.body.style.overflow = 'auto';
        });
    }
    
    // Fechar menu ao clicar em link
    document.querySelectorAll('#mobileMenu a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
            document.body.style.overflow = 'auto';
        });
    });
    
    // Carrinho
    if (cartButton) {
        cartButton.addEventListener('click', () => {
            if (cartSidebar) {
                cartSidebar.classList.remove('translate-x-full');
                document.body.style.overflow = 'hidden';
            }
        });
    }
    
    if (closeCart) {
        closeCart.addEventListener('click', () => {
            if (cartSidebar) {
                cartSidebar.classList.add('translate-x-full');
                document.body.style.overflow = 'auto';
            }
        });
    }
    
    // Animações ao scroll
    const fadeElements = document.querySelectorAll('.fade-in-up');
    
    const checkFade = () => {
        fadeElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.style.opacity = "1";
                element.style.transform = "translateY(0)";
            }
        });
    };
    
    // Configurar estado inicial
    fadeElements.forEach(element => {
        element.style.opacity = "0";
        element.style.transform = "translateY(30px)";
        element.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    });
    
    window.addEventListener('scroll', checkFade);
    checkFade();
});