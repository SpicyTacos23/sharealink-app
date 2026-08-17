import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["modal", "avatarList"];

    openModal() {
        this.modalTarget.classList.add("open");
        this.loadAvatars();
    }

    closeModal() {
        this.modalTarget.classList.remove("open");
    }

    async loadAvatars() {
        const response = await fetch("/settings/avatar/list");
        const html = await response.text();

        this.avatarListTarget.innerHTML = html;

        this.avatarListTarget
            .querySelectorAll(".avatar-option")
            .forEach((option) => {
                option.addEventListener("click", () => {
                    const avatar = option.dataset.avatarName;
                    this.selectAvatar(avatar);
                });
            });
    }

    async selectAvatar(avatar) {
        try {
            const response = await fetch("/settings/avatar/change", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ avatar }),
            });

            if (!response.ok) {
                const text = await response.text();
                console.error("Error al cambiar avatar:", text);

                this.showError(
                    "No se pudo cambiar el avatar. Inténtalo más tarde.",
                );
                return;
            }

            this.closeModal();
            window.location.reload();
        } catch (error) {
            console.error("Error de red:", error);
            this.showError(
                "Error de conexión. Revisa tu red o inténtalo más tarde.",
            );
        }
    }

    showError(message) {
        let errorBox = this.modalTarget.querySelector(".avatar-error");

        if (!errorBox) {
            errorBox = document.createElement("div");
            errorBox.classList.add("avatar-error");
            this.modalTarget
                .querySelector(".avatar-modal-content")
                .prepend(errorBox);
        }

        errorBox.textContent = message;
    }
}
