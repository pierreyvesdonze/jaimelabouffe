document.addEventListener("DOMContentLoaded", function () {
	const loader = document.getElementById("page-loader");

	function showLoader() {
		loader.removeAttribute("hidden");
	}

	function hideLoader() {
		loader.setAttribute("hidden", true);
	}

	function handleNavigation(event) {
		const target = event.target.closest("a");

		if (target && target.getAttribute("target") !== "_blank" && target.href && !target.href.startsWith("#")) {
			// Vérifier si l'élément 'a' est un lien de navigation valide (pas une image ou un lien interne)
			const isValidNavigation = !target.closest('a[href^="#"], a[href*="image"]'); // Exemple de filtre à adapter à tes besoins

			if (isValidNavigation) {
				showLoader();
			}
		}
	}

	function handleFormSubmit(event) {
		showLoader();
	}

	// Attache les événements à tous les liens et formulaires dynamiquement
	document.body.addEventListener("click", handleNavigation);
	document.body.addEventListener("submit", handleFormSubmit);

	// Cache le loader après le chargement de la nouvelle page (y compris retour arrière)
	window.addEventListener("pageshow", function () {
		hideLoader();
	});
});