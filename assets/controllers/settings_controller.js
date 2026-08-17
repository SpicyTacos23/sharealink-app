import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["container"];

    async loadProfile(event) {
        console.log("entramos");
        event.preventDefault();

        try {
            const response = await fetch("/settings/user-profile", {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });

            if (!response.ok) {
                throw new Error("Error cargando perfil");
            }

            const html = await response.text();
            this.containerTarget.innerHTML = html;

        } catch (error) {
            console.error(error);
            this.containerTarget.innerHTML = `
                <p style="color:red">Error cargando contenido</p>
            `;
        }
    }
}
