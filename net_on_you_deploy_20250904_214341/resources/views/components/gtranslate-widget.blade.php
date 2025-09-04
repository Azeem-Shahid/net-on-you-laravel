<!-- GTranslate Widget Component -->
<div class="gtranslate_wrapper"></div>

<style>
    .gtranslate_wrapper {
        position: absolute;
        right: 396px;
        top: 43px;
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: Arial, sans-serif;
        color: #ffffff;
        background-color: #00ff00;
        border: 1px solid #00cc00;
        border-radius: 8px;
        padding: 2px 10px;
        cursor: pointer;
        text-align: center;
        width: auto;
        min-width: 120px;
        transition: all 0.3s ease;
    }
    .gtranslate_wrapper select {
        appearance: none;
        background: none;
        border: none;
        color: #1d003f;
        font-size: 16px;
        padding: 5px 10px;
        cursor: pointer;
        width: 100%;
        font-weight: 600;
    }
    .gtranslate_wrapper::after {
        content: '\25BC';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #1d003f;
        font-size: 12px;
    }
    .gtranslate_wrapper select option {
        background-color: #00ff00;
        color: #1d003f;
        padding: 8px 12px;
        font-weight: 600;
    }
    .gtranslate_wrapper:hover {
        background-color: #00cc00;
        border-color: #1d003f;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 255, 0, 0.3);
    }
    .gtranslate_wrapper select:focus {
        outline: none;
        border-color: #1d003f;
    }
    
    /* Hide all GTranslate default elements */
    .gtranslate_wrapper .gt_flag,
    .gtranslate_wrapper .gt_selected,
    .gtranslate_wrapper .gt_current,
    .gtranslate_wrapper .gt_other {
        display: none !important;
    }
    
    /* Custom language text styling */
    .gtranslate_wrapper select {
        color: #1d003f !important;
        font-weight: 600 !important;
    }
    
    /* Responsive styles */
    @media screen and (max-width: 1200px) {
        .gtranslate_wrapper {
            right: 130px;
        }
    }
    @media screen and (max-width: 992px) {
        .gtranslate_wrapper {
            right: 97px;
            top: 40px;
            min-width: 57px;
            height: 32px;
            width: 87px;
        }
        .gtranslate_wrapper select {
            font-size: 11px;
            padding: 1px;
        }
    }
    @media screen and (max-width: 768px) {
        .gtranslate_wrapper {
            right: 117px;
            top: 39px;
            min-width: 100px;
        }
        .gtranslate_wrapper select {
            font-size: 14px;
            padding: 17px 12px;
        }
    }
    @media screen and (max-width: 576px) {
        .gtranslate_wrapper {
            right: 124px;
            top: 36px;
            min-width: 90px;
            padding: 17px 12px;
        }
        .gtranslate_wrapper select {
            font-size: 13px;
            padding: 2px 6px;
        }
    }
    @media screen and (max-width: 360px) {
        .gtranslate_wrapper {
            right: 97px;
            top: 37px;
            min-width: 57px;
            height: 32px;
            width: 87px;
        }
        .gtranslate_wrapper select {
            font-size: 11px;
            padding: 1px;
        }
    }
</style>

<script>
    window.gtranslateSettings = {
        "default_language": "en",
        "languages": ["en", "es"],
        "wrapper_selector": ".gtranslate_wrapper",
        "flag_style": "3d",
        "native_language_names": true,
        "detect_browser_language": true,
        "flag_size": 0,
        "alt_flags": false,
        "native_name": true,
        "show_flag": false,
        "show_flag_text": false
    };
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dropdown.js" defer></script>
