const ViewsManager = {
    currentView: null,

    // Afficher une vue déjà chargée
    showView: function(viewId) {
        if(this.currentView) {
            document.getElementById(this.currentView).style.display = 'none';
        }
        const view = document.getElementById(viewId);
        if(view) {
            view.style.display = 'block';
            this.currentView = viewId;
        }
    },

    // Lazy loading : charger le contenu via fetch si pas encore chargé
    showViewLazy: async function(viewId, url) {
        const view = document.getElementById(viewId);
        if(view && !view.dataset.loaded) {
            const response = await fetch(url);
            const html = await response.text();
            view.innerHTML = html;
            view.dataset.loaded = "true"; // marque la vue comme chargée
        }
        this.showView(viewId);
    }
};

// Afficher la vue par défaut après chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    if(document.getElementById('view-dashboard')) {
        ViewsManager.showView('view-dashboard');
    }
});
