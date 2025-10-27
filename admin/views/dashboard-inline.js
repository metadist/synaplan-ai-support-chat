jQuery(document).ready(function($) {
    var apiKeyHidden = $('#api-key-display');
    var apiKeyRevealed = $('#api-key-revealed');
    var toggleBtn = $('#toggle-api-key');
    var copyBtn = $('#copy-api-key');
    var toggleText = $('#toggle-text');
    var isRevealed = false;
    
    // Toggle API key visibility
    toggleBtn.on('click', function() {
        isRevealed = !isRevealed;
        
        if (isRevealed) {
            apiKeyHidden.hide();
            apiKeyRevealed.show();
            copyBtn.show();
            toggleBtn.find('.dashicons').removeClass('dashicons-visibility').addClass('dashicons-hidden');
            toggleText.text(toggleBtn.data('hide-text'));
        } else {
            apiKeyHidden.show();
            apiKeyRevealed.hide();
            copyBtn.hide();
            toggleBtn.find('.dashicons').removeClass('dashicons-hidden').addClass('dashicons-visibility');
            toggleText.text(toggleBtn.data('show-text'));
        }
    });
    
    // Copy API key
    copyBtn.on('click', function() {
        var apiKey = apiKeyRevealed.text().trim();
        navigator.clipboard.writeText(apiKey).then(function() {
            var originalText = copyBtn.html();
            copyBtn.html('<span class="dashicons dashicons-yes"></span> ' + copyBtn.data('copied-text'));
            setTimeout(function() {
                copyBtn.html(originalText);
            }, 2000);
        });
    });
    
    // Copy embed code
    $('#copy-embed-code').on('click', function() {
        var embedCode = $('#embed-code');
        embedCode.select();
        document.execCommand('copy');
        
        var btn = $(this);
        var originalText = btn.html();
        btn.html('<span class="dashicons dashicons-yes"></span> ' + btn.data('copied-text'));
        setTimeout(function() {
            btn.html(originalText);
        }, 2000);
    });
});

