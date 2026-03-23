// Tab switching
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

// Image preview functionality
document.getElementById('embed-image').addEventListener('change', function(e) {
    handleImagePreview(e, 'embed-preview');
});

document.getElementById('extract-image').addEventListener('change', function(e) {
    handleImagePreview(e, 'extract-preview');
});

function handleImagePreview(event, previewId) {
    const file = event.target.files[0];
    const preview = document.getElementById(previewId);
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}

// Embed message form submission
document.getElementById('embed-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const imageFile = document.getElementById('embed-image').files[0];
    const message = document.getElementById('embed-message').value;
    const encryptionKey = document.getElementById('embed-key').value;
    
    if (!imageFile || !message.trim()) {
        showResult('embed-result', 'Please select an image and enter a message', 'error');
        return;
    }
    
    formData.append('image', imageFile);
    formData.append('message', message);
    if (encryptionKey.trim()) {
        formData.append('encryption_key', encryptionKey);
    }
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading"></span>Processing...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('backend/api/embed.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showResult('embed-result', 
                `<h3>Success!</h3>
                <p>${result.message}</p>
                <button class="download-btn" onclick="window.open('${result.downloadUrl}', '_blank')">Download Stego Image</button>`, 
                'success'
            );
        } else {
            showResult('embed-result', `<h3>Error</h3><p>${result.error}</p>`, 'error');
        }
    } catch (error) {
        showResult('embed-result', `<h3>Error</h3><p>Failed to connect to server. Please make sure the backend is running.</p>`, 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Extract message form submission
document.getElementById('extract-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const imageFile = document.getElementById('extract-image').files[0];
    const encryptionKey = document.getElementById('extract-key').value;
    
    if (!imageFile) {
        showResult('extract-result', 'Please select an image', 'error');
        return;
    }
    
    formData.append('image', imageFile);
    if (encryptionKey.trim()) {
        formData.append('encryption_key', encryptionKey);
    }
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading"></span>Processing...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('backend/api/extract.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showResult('extract-result', 
                `<h3>Message Extracted Successfully!</h3>
                <p>${result.message}</p>
                <div class="message-box">
                    <strong>Hidden Message:</strong>
                    <p>${result.extractedMessage}</p>
                </div>
                <button class="copy-btn" onclick="copyToClipboard('${result.extractedMessage.replace(/'/g, "\\'")}')">Copy Message</button>`, 
                'success'
            );
        } else {
            showResult('extract-result', `<h3>Error</h3><p>${result.error}</p>`, 'error');
        }
    } catch (error) {
        showResult('extract-result', `<h3>Error</h3><p>Failed to connect to server. Please make sure the backend is running.</p>`, 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Show result message
function showResult(elementId, message, type) {
    const resultDiv = document.getElementById(elementId);
    resultDiv.innerHTML = message;
    resultDiv.className = `result ${type}`;
    resultDiv.style.display = 'block';
    
    // Scroll to result
    resultDiv.scrollIntoView({ behavior: 'smooth' });
}

// Copy to clipboard functionality
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Message copied to clipboard!');
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy message');
    });
}

// Clear results when switching tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.result').forEach(result => {
            result.style.display = 'none';
        });
    });
});
