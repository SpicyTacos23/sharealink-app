import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["container", "iframeContainer"];

    async openLink(event) {
        try {
            const row = event.currentTarget.closest("tr");
            if (!row) {
                throw new Error("No row found");
            }

            const url = row.dataset.mediaStreamFindLinkUrlValue;

            // Validación previa
            const response = await fetch(url, { method: "GET" });

            // LOGIN (tu backend redirige a login parcial)
            if (response.redirected) {
                const html = await response.text();
                this.containerTarget.innerHTML = html;
                return;
            }

            // 401 / 403 explícito
            if (response.status === 401 || response.status === 403) {
                const html = await response.text();
                this.containerTarget.innerHTML = html;
                return;
            }

            // OK → abrir en nueva pestaña
            if (response.status === 200) {
                window.open(url, "_blank");
                return;
            }

            alert("Error: " + response.status);
        } catch (error) {
            alert(error);
        }
    }
}
