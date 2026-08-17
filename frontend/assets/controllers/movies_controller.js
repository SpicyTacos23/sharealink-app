import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["containerMovies"];
    static values = { getMoviesUrl: String };

    connect() {
        this.loadMovies();
    }

    async loadMovies() {
        const board = document.getElementById("boardMovie");

        try {
            // 1. Mostrar skeletons inmediatamente
            board.classList.remove("hideAtFirst");

            // 2. Delay artificial para ver el loader
            await this.delay(1000);

            // 3. Fetch del contenido real
            const response = await fetch(this.getMoviesUrlValue);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

            const html = await response.text();

            // 4. Preparamos contenedor temporal
            const temp = document.createElement("div");
            temp.innerHTML = html;

            // 5. Esperamos a que todas las imágenes estén cargadas
            await this.waitForImages(temp);

            // 6. Reemplazamos skeletons por contenido real
            this.containerMoviesTarget.innerHTML = temp.innerHTML;

            // 7. Fade-in suave
            board.classList.add("fade-in-ready");
        } catch (error) {
            console.error("Error cargando películas:", error);
            this.containerMoviesTarget.innerHTML = `<p style="color:red">Error cargando películas</p>`;
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
