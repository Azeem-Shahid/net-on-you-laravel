/**
 * Language Handler for Custom Language Widget Integration
 * This script handles saving user language preferences when the custom language widget changes the language
 */

class LanguageHandler {
    constructor() {
        this.currentLanguage = 'en';
        this.init();
    }

    init() {
        // Wait for custom language widget to be loaded
        this.waitForCustomWidget();
        
        // Listen for language changes
        this.observeLanguageChanges();
        
        // Check if user is logged in and save current language
        this.checkUserLanguage();
    }

    waitForCustomWidget() {
        // Check if custom language widget is loaded every 100ms
        const checkInterval = setInterval(() => {
            if (document.querySelector('.custom-language-widget')) {
                clearInterval(checkInterval);
                this.onCustomWidgetLoaded();
            }
        }, 100);
    }

    onCustomWidgetLoaded() {
        console.log('Custom language widget loaded, initializing language handler');
        
        // Get current language from custom widget
        this.currentLanguage = this.getCustomWidgetLanguage();
        
        // Save initial language if user is logged in
        if (this.isUserLoggedIn()) {
            this.saveUserLanguage(this.currentLanguage);
        }
    }

    observeLanguageChanges() {
        // Listen for language changes in the custom widget
        const widget = document.querySelector('.custom-language-widget');
        if (widget) {
            // Listen for clicks on language options
            widget.addEventListener('click', (e) => {
                if (e.target.closest('button') && e.target.closest('button').textContent.includes('English') || e.target.closest('button').textContent.includes('Español')) {
                    setTimeout(() => {
                        this.handleLanguageChange();
                    }, 100);
                }
            });
        }
        
        // Also listen for popstate events
        window.addEventListener('popstate', () => {
            this.handleLanguageChange();
        });
    }

    handleLanguageChange() {
        const newLanguage = this.getGTranslateLanguage();
        
        if (newLanguage && newLanguage !== this.currentLanguage) {
            console.log(`Language changed from ${this.currentLanguage} to ${newLanguage}`);
            this.currentLanguage = newLanguage;
            
            // Save language preference if user is logged in
            if (this.isUserLoggedIn()) {
                this.saveUserLanguage(newLanguage);
            }
        }
    }

    getCustomWidgetLanguage() {
        // Try to get language from URL first
        const urlLang = this.getLanguageFromUrl();
        if (urlLang) {
            return urlLang;
        }
        
        // Try to get from custom language widget
        const widget = document.querySelector('.custom-language-widget');
        if (widget) {
            const button = widget.querySelector('button');
            if (button) {
                const text = button.textContent.trim();
                if (text.includes('Español')) {
                    return 'es';
                } else if (text.includes('English')) {
                    return 'en';
                }
            }
        }
        
        // Default to English
        return 'en';
    }

    getLanguageFromUrl() {
        const url = location.href;
        const langMatch = url.match(/\/[a-z]{2}\//);
        if (langMatch) {
            return langMatch[0].replace(/\//g, '');
        }
        return null;
    }

    isUserLoggedIn() {
        // Check if user is logged in by looking for auth indicators
        return document.querySelector('[data-user-id]') !== null || 
               document.querySelector('.user-menu') !== null ||
               document.querySelector('[href*="logout"]') !== null;
    }

    async saveUserLanguage(language) {
        try {
            const response = await fetch('/user/language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({ language: language })
            });

            if (response.ok) {
                console.log(`Language preference saved: ${language}`);
            } else {
                console.error('Failed to save language preference');
            }
        } catch (error) {
            console.error('Error saving language preference:', error);
        }
    }

    getCsrfToken() {
        // Get CSRF token from meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    }

    async checkUserLanguage() {
        if (this.isUserLoggedIn()) {
            try {
                const response = await fetch('/user/language');
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.language) {
                        this.currentLanguage = data.language;
                        console.log(`User language preference: ${this.currentLanguage}`);
                    }
                }
            } catch (error) {
                console.error('Error checking user language:', error);
            }
        }
    }
}

// Initialize language handler when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.languageHandler = new LanguageHandler();
});

// Also initialize if DOM is already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.languageHandler = new LanguageHandler();
    });
} else {
    window.languageHandler = new LanguageHandler();
}
