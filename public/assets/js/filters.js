document.addEventListener('DOMContentLoaded', function () {
    const resetButton = document.getElementById('resetFilters');
    const form = document.getElementById('filterForm');
    const spinner = document.querySelector('.spinner');

    if (form) {
        const filterButton = form.querySelector('button[type="submit"]');

        // Fonction pour vérifier si un filtre est sélectionné
        function checkFilters() {
            let isActive = false;
            const inputs = form.querySelectorAll('input, select');

            inputs.forEach(input => {
                if ((input.type === 'checkbox' || input.type === 'radio') && input.checked) {
                    isActive = true;
                } else if (input.value.trim() !== '') {
                    isActive = true;
                }
            });

            // Active ou désactive le bouton selon si un filtre est sélectionné
            filterButton.disabled = !isActive;
        }

        // Désactive le bouton "Filtrer" dès le chargement de la page si aucun filtre n'est sélectionné
        checkFilters();

        // Ajoute un écouteur d'événements sur les champs du formulaire
        form.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('change', checkFilters);  // Met à jour quand un filtre est modifié
        });

        // Réinitialise les filtres et désactive le bouton
        if (resetButton) {
            resetButton.addEventListener('click', function (event) {
                event.preventDefault();

                // Afficher le spinner lors de la réinitialisation
                if (spinner) {
                    spinner.style.display = 'block'; // Affiche le spinner
                }

                // Réinitialiser les champs du formulaire
                form.reset();

                checkFilters();  // Vérifie si le bouton doit être activé ou non

                setTimeout(() => {
                    if (spinner) {
                        spinner.style.display = 'none'; // Cache le spinner après réinitialisation
                    }
                }, 500);
            });
        }
    }
});
