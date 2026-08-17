import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["containerShows"];
    static values = { getShowsUrl: String };

    connect() {
        this.loadShows();
    }

    async loadShows() {
        const board = document.getElementById("boardShow");

        try {
            // Skeleton visible desde el principio
            // (ya no existe hideAtFirst en CSS)

            // Delay opcional para ver el loader
            await this.delay(1000);

            // Fetch del contenido real
            const response = await fetch(this.getShowsUrlValue);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

            const html = await response.text();

            // Contenedor temporal para precargar imágenes
            const temp = document.createElement("div");
            temp.innerHTML = html;

            // Esperar a que todas las imágenes carguen
            await this.waitForImages(temp);

            // Reemplazar skeletons por contenido real
            this.containerShowsTarget.innerHTML = temp.innerHTML;

            // Fade-in suave
            board.classList.add("fade-in-ready");
        } catch (error) {
            console.error("Error cargando shows:", error);
            this.containerShowsTarget.innerHTML = `<p style="color:red">Error cargando shows</p>`;
        }
    }

    waitForImages(container) {
        const imgs = [...container.querySelectorAll("img")];
        if (imgs.length === 0) return Promise.resolve();

        return Promise.all(
            imgs.map((img) => {
                if (img.complete && img.naturalWidth > 0)
                    return Promise.resolve();
                return new Promise((resolve) => {
                    img.addEventListener("load", resolve, { once: true });
                    img.addEventListener("error", resolve, { once: true });
                });
            }),
        );
    }

    delay(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }
}
