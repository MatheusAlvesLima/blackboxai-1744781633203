// validation.js - Frontend form validation for MailMarket

// Validate email format
function validateEmail(email) {
    const re = /^[\\w-\\.]+@([\\w-]+\\.)+[\\w-]{2,4}$/;
    return re.test(email);
}

// Validate password strength (min 6 chars)
function validatePassword(password) {
    return password.length >= 6;
}

// Show Bootstrap alert message
function showAlert(message, type = 'danger', containerId = 'alert-container') {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}

// Clear alert messages
function clearAlert(containerId = 'alert-container') {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = '';
    }
}
