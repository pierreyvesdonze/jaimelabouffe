document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('resetFilters').addEventListener('click', function() {
        window.location.href = "{{ path('recipe_index') }}"; // Recharge la page sans filtre
    });
});
