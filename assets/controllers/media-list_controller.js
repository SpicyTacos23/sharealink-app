import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = [
        "loaderOverlay",
        "content",
        "filters"
    ];
    static values = { url: String };

    selectedGenres = new Set();
    selectedInterests = new Set();
    genreColors = {};

    // Initializes the controller: creates the Extra container and loads filters from URL.
    connect() {
        this.initializeFromURL();
    }

    // Reads initial filters from the URL and loads content accordingly.
    initializeFromURL() {
        const params = new URLSearchParams(window.location.search);
        const genres = params.getAll("genres");

        if (genres.length > 0) {
            genres.forEach((g) => this.selectedGenres.add(g));
            this.markPreselectedBadges();

            this.loadContent(
                Array.from(this.selectedGenres),
                Array.from(this.selectedInterests),
            );

            history.replaceState({}, "", window.location.pathname);

            //genres.forEach((g) => this.displayInterests(g));
        } else {
            this.loadContent();
        }
    }

    // Highlights genre badges that were preselected from the URL.
    markPreselectedBadges() {
        this.element.querySelectorAll("[data-genre]").forEach((badge) => {
            if (this.selectedGenres.has(badge.dataset.genre)) {
                badge.classList.add("badge--active");
            }
        });
    }

    /**
     * Loads the main content area based on selected genres and interests.
     */
    async loadContent(genres = [], interests = []) {
        try {
            this.toggleLoader();

            await new Promise((resolve) => setTimeout(resolve, 500));

            const params = [];

            genres.forEach(
                (g) =>
                    params.push("with_genres[]=" + encodeURIComponent(g)),
            );

            const query = params.length ? "?" + params.join("&") : "";

            const response = await fetch(this.urlValue + query);

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const html = await response.text();
            this.contentTarget.innerHTML = html;

            this.toggleLoader();
            this.contentTarget.classList.remove("hidden");
            this.filtersTarget.classList.remove("hidden");
        } catch (error) {
            console.error("Error loading content:", error);
            this.contentTarget.innerHTML = `<p style="color:red">Error loading content</p>`;
            this.toggleLoader();
        }
    }

    // Shows or hides the loading overlay and applies blur to the content.
    toggleLoader() {
        if (this.loaderOverlayTarget.classList.contains("hidden")) {
            this.loaderOverlayTarget.classList.remove("hidden");
            this.contentTarget.classList.add("content--blur");
        } else {
            this.loaderOverlayTarget.classList.add("hidden");
            this.contentTarget.classList.remove("content--blur");
        }
    }

    // Handles clicking on a genre badge: toggles selection and loads interests.
    filterByGenre(event) {
        const badge = event.currentTarget;
        const genre = badge.dataset.genre;

        if (this.selectedGenres.has(genre)) {
            this.selectedGenres.delete(genre);
            badge.classList.remove("badge--active");
        } else {
            this.selectedGenres.add(genre);
            badge.classList.add("badge--active");
        }

        this.loadContent(
            Array.from(this.selectedGenres),
            Array.from(this.selectedInterests),
        );
    }

    //Removes all filters
    removeAllFilters() {
        // Clear sets
        this.selectedGenres.clear();

        // Remove the active effect on badges
        this.element.querySelectorAll("[data-genre]").forEach((badge) => {
            badge.classList.remove("badge--active");
        });

        // Load content without filters
        this.loadContent([], []);
    }

    // @TODO: MOVE TO DIFFERENT CONTROLLER
    // Handles the search form submit. Requires 3+ chars. Only triggers on submit (Enter/click).
    async search(event) {
        event.preventDefault();

        const form = event.target;
        const params = new URLSearchParams(new FormData(form));

        const term = (params.get("search_title[title]") || "").trim();
        if (term.length < 3) {
            return;
        }

        try {
            this.toggleLoader();

            const response = await fetch(form.action + "?" + params.toString());
            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            this.contentTarget.innerHTML = await response.text();

            this.toggleLoader();
            this.contentTarget.classList.remove("hidden");
        } catch (error) {
            console.error("Error searching media:", error);
            this.contentTarget.innerHTML = `<p style="color:red">No se pudo completar la búsqueda</p>`;
            this.toggleLoader();
        }
    }
}
