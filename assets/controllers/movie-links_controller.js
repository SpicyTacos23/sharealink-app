import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["containerMovieLinks"];
    static values = {
        getMovieLinksUrl: String,
        loadFormUrl: String,
        currentMovie: String,
        currentMovieImage: String,
        currentMovieTitle: String,
    };

    connect() {
        this.loadLinks();
    }

    async loadLinks() {
        try {
            const response = await fetch(this.getMovieLinksUrlValue);

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const html = await response.text();
            this.containerMovieLinksTarget.innerHTML = html;
        } catch (error) {
            console.error("Error cargando películas:", error);
            this.containerMovieLinksTarget.innerHTML = `
                <p style="color:red">Error cargando links</p>
            `;
        }
    }

    _loadForm() {
        this.loadForm();
    }

    async loadForm() {
        try {
            let loadFormUrl =
                this.loadFormUrlValue +
                `?currentMovie=` +
                this.currentMovieValue +
                `&currentMovieImage=` +
                this.currentMovieImageValue +
                `&currentMovieTitle=` +
                this.currentMovieTitleValue;

            const response = await fetch(loadFormUrl);

            // Si Symfony devuelve 403 → mostrar el HTML del login
            if (response.status === 403) {
                const html = await response.text();
                this.containerMovieLinksTarget.innerHTML = html;
                return;
            }

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const html = await response.text();
            this.containerMovieLinksTarget.innerHTML = html;
        } catch (error) {
            console.error("Error cargando películas:", error);
            this.containerMovieLinksTarget.innerHTML = `
            <p style="color:red">Error cargando links</p>
        `;
        }
    }
}
