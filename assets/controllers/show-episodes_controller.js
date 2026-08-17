import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["containerShowEpisodes"];
    static values = {
        getShowEpisodesUrl: String,
        season: { type: Number, default: 1 },
        showId: String,
    };

    connect() {
        // Primera carga con la temporada por defecto
        this.load();
    }

    load(season = this.seasonValue) {
        this.seasonValue = season;
        this.loadEpisodes();
    }

    changeSeason(event) {
        const season = parseInt(event.target.value, 10);
        this.load(season);
    }

    async loadEpisodes() {
        try {
            const url = `${this.getShowEpisodesUrlValue}?season=${this.seasonValue}`;

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const html = await response.text();
            this.containerShowEpisodesTarget.innerHTML = html;
        } catch (error) {
            console.error("Error cargando episodios:", error);
            this.containerShowEpisodesTarget.innerHTML = `
                <p style="color:red">Error cargando episodios</p>
            `;
        }
    }

    selectEpisode(event) {
        // Buscar id de episodio en dataset del card o en atributo data-episode-id
        const card = event.currentTarget;
        const episodeId = card.dataset.episodeId || card.getAttribute('data-episode-id');
        const season = this.seasonValue;
        const showId = this.showIdValue;

        this.element.dispatchEvent(new CustomEvent('episode:selected', {
            bubbles: true,
            detail: {
                episodeId: episodeId,
                season: season,
                showId: showId,
            }
        }));
    }

    reload() {
        // Called from UI to reload the episodes list (e.g., back button)
        this.load(this.seasonValue);
    }
}
