<!-- GTranslate Widget Component -->
<div class="gtranslate_wrapper">
    
</div>

<style>
    .gtranslate_wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: Arial, sans-serif;
        color: #00ff00;
        background-color: #1d003f;
        border: 2px solid #00ff00;
        border-radius: 8px;
        padding: 8px 16px;
        cursor: pointer;
        text-align: center;
        min-width: 140px;
        transition: all 0.3s ease;
        margin: 0 10px;
    }
    .gtranslate_wrapper:hover {
        background-color: #2a0057;
        border-color: #00ff00;
        box-shadow: 0 0 15px rgba(0, 255, 0, 0.3);
    }
    .gtranslate_wrapper select {
        appearance: none;
        background: none;
        border: none;
        color: #00ff00;
        font-size: 16px;
        padding: 5px 10px;
        cursor: pointer;
        width: 100%;
        font-weight: 600;
    }
    .gtranslate_wrapper select:focus {
        outline: none;
        color: #00ff00;
    }
    .gtranslate_wrapper select option {
        background-color: #1d003f;
        color: #00ff00;
        font-weight: 600;
    }
    .gtranslate_wrapper select option:hover {
        background-color: #2a0057;
    }
    .netonyou-logo {
        color: #00ff00;
        font-weight: bold;
        font-size: 16px;
        text-shadow: 0 0 10px rgba(0, 255, 0, 0.5);
    }
    
    /* Responsive styles */
    @media screen and (max-width: 1200px) {
        .gtranslate_wrapper {
            min-width: 130px;
            padding: 6px 12px;
        }
        .netonyou-logo {
            font-size: 15px;
        }
    }
    @media screen and (max-width: 992px) {
        .gtranslate_wrapper {
            min-width: 120px;
            padding: 6px 10px;
        }
        .gtranslate_wrapper select {
            font-size: 14px;
        }
        .netonyou-logo {
            font-size: 14px;
        }
    }
    @media screen and (max-width: 768px) {
        .gtranslate_wrapper {
            min-width: 110px;
            padding: 5px 8px;
        }
        .gtranslate_wrapper select {
            font-size: 12px;
            padding: 4px;
        }
        .netonyou-logo {
            font-size: 13px;
        }
    }
    @media screen and (max-width: 576px) {
        .gtranslate_wrapper {
            min-width: 100px;
            padding: 4px 6px;
        }
        .gtranslate_wrapper select {
            font-size: 11px;
            padding: 2px 4px;
        }
        .netonyou-logo {
            font-size: 12px;
        }
    }
    @media screen and (max-width: 480px) {
        .gtranslate_wrapper {
            min-width: 90px;
            padding: 3px 5px;
        }
        .gtranslate_wrapper select {
            font-size: 10px;
            padding: 1px 2px;
        }
        .netonyou-logo {
            font-size: 11px;
        }
    }
</style>

<script>
  window.gtranslateSettings = {
    "default_language": "en",
    "languages": ["en", "es"],
    "wrapper_selector": ".gtranslate_wrapper"
};
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dropdown.js" defer></script>

