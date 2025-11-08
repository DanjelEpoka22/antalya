// JavaScript për Rreze Antalya

document.addEventListener('DOMContentLoaded', function() {
    // Aktivizo tooltips të Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Validimi i formave
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Llogaritja automatikë e çmimit total në booking
    const guestsSelect = document.getElementById('guests');
    if(guestsSelect) {
        guestsSelect.addEventListener('change', function() {
            const guests = this.value;
            const pricePerPerson = parseFloat(this.getAttribute('data-price')) || 0;
            const totalPriceElement = document.getElementById('total_price');
            if(totalPriceElement) {
                totalPriceElement.textContent = (guests * pricePerPerson).toFixed(2);
            }
        });
    }

    // Filtro turat me AJAX (opsional - për përmirësim)
    const searchForm = document.getElementById('searchForm');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Implementimi i kërkimit me AJAX mund të shtohet këtu
            this.submit();
        });
    }

    // Shfaq/fsheh fjalëkalimin
    const togglePassword = document.querySelector('.toggle-password');
    if(togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.querySelector(this.getAttribute('toggle'));
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
});

// Funksion për të shfaqur modal për konfirmim
function confirmAction(message) {
    return confirm(message || 'Are you sure you want to perform this action?');
}

// Funksion për të shfaqur mesazhe
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.prepend(alertDiv);
    
    // Fshi automatikisht pas 5 sekondash
    setTimeout(() => {
        if(alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}