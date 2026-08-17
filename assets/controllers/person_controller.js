import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = [
        "filmographyContainer",
        "bioText",
        "bioFade",
        "bioToggle",
    ];
    static values = {
        getFilmographyUrl: String,
        personId: Number,
    };

    connect() {
        this.loadFilmography();
        this.initBio();
    }

    initBio() {
        if (!this.hasBioTextTarget) return;

        const text = this.bioTextTarget;
        const lineHeight = parseFloat(getComputedStyle(text).lineHeight) || 22;
        this.collapsedHeight = lineHeight * 4;
        this.bioExpanded = false;

        if (text.scrollHeight <= this.collapsedHeight + 10) {
            if (this.hasBioFadeTarget)
                this.bioFadeTarget.style.display = "none";
            if (this.hasBioToggleTarget)
                this.bioToggleTarget.style.display = "none";
            return;
        }

        text.style.maxHeight = this.collapsedHeight + "px";
        text.style.overflow = "hidden";
        text.style.transition = "max-height 0.4s ease";
    }

    toggleBio() {
        if (!this.hasBioTextTarget) return;

        this.bioExpanded = !this.bioExpanded;
        const text = this.bioTextTarget;
        const fade = this.hasBioFadeTarget ? this.bioFadeTarget : null;
        const toggle = this.hasBioToggleTarget ? this.bioToggleTarget : null;

        if (this.bioExpanded) {
            text.style.maxHeight = text.scrollHeight + "px";
            if (fade) fade.style.opacity = "0";
            if (toggle)
                toggle.innerHTML =
                    'Read less <i class="fa-solid fa-chevron-up"></i>';
        } else {
            text.style.maxHeight = this.collapsedHeight + "px";
            if (fade) fade.style.opacity = "1";
            if (toggle)
                toggle.innerHTML =
                    'Read more <i class="fa-solid fa-chevron-down"></i>';
        }
    }

    async loadFilmography() {
        try {
            const response = await fetch(this.getFilmographyUrlValue);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const html = await response.text();
            this.filmographyContainerTarget.innerHTML = html;
        } catch (error) {
            console.error("Error cargando películas:", error);
            this.filmographyContainerTarget.innerHTML =
                '<p style="color:red">Error cargando películas</p>';
        }
    }
}
