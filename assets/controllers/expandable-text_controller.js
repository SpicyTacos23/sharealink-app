import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["text", "toggle"];

    connect() {
        if (!this.hasTextTarget) return;

        this.textElement = this.textTarget;

        // Inyectamos el texto traducido desde Twig
        if (this.textElement.dataset.translatedOverview) {
            this.textElement.textContent = this.textElement.dataset.translatedOverview;
        }

        const lineHeight = parseFloat(getComputedStyle(this.textElement).lineHeight) || 22;
        this.collapsedHeight = lineHeight * 4;
        this.expanded = false;

        if (this.textElement.scrollHeight <= this.collapsedHeight + 10) {
            if (this.hasToggleTarget) {
                this.toggleTarget.style.display = "none";
            }
            return;
        }

        this.textElement.style.display = "block";
        this.textElement.style.overflow = "hidden";
        this.textElement.style.maxHeight = `${this.collapsedHeight}px`;
        this.textElement.style.transition = "max-height 0.3s ease";

        this.updateButtonText();
    }

    toggle() {
        this.expanded = !this.expanded;

        this.textElement.style.maxHeight = this.expanded
            ? `${this.textElement.scrollHeight}px`
            : `${this.collapsedHeight}px`;

        this.updateButtonText();
    }

    updateButtonText() {
        if (!this.hasToggleTarget) return;

        this.toggleTarget.textContent = this.expanded
            ? this.toggleTarget.dataset.collapseText
            : this.toggleTarget.dataset.expandText;
    }
}
