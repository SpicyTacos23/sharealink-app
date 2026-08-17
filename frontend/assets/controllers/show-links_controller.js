import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        getShowLinksUrl: String,
        currentShow: String,
    };

    connect() {
        // Escucha eventos personalizados dispatch desde show-episodes
        this.element.addEventListener('episode:selected', this.onEpisodeSelected.bind(this));
    }

    async onEpisodeSelected(event) {
        const detail = event.detail || {};
        const season = detail.season ?? 1;
        const episode = detail.episodeId ?? 0;
        const showId = detail.showId ?? this.currentShowValue;

        await this.loadLinks(showId, season, episode);
    }

    // Buscar el target de episodios dentro del scope del componente
    _getEpisodesContainer() {
        return this.element.querySelector('[data-show-episodes-target="containerShowEpisodes"]');
    }

    async loadLinks(showId, season = 1, episode = 0) {
        try {
            const url = `${this.getShowLinksUrlValue}?season=${season}&episode=${episode}`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const html = await response.text();
            const container = this._getEpisodesContainer();
            if (!container) {
                console.error('No episodes container found to inject links');
                return;
            }
            container.innerHTML = html;
        } catch (error) {
            console.error("Error cargando show links:", error);
            const container = this._getEpisodesContainer();
            if (container) container.innerHTML = `<p style="color:red">Error cargando links</p>`;
        }
    }
}
