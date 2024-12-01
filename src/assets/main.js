document.addEventListener('DOMContentLoaded', function() {

    console.log("Entroo xd");
    // Validaciónes Generales 
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });
    });

 
});