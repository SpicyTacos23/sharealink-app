import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['track'];
    static values = {
        openMovieUrl: String,
        openShowUrl: String
    }

    connect() {
        this.items = Array.from(this.trackTarget.children);
        this.windowSize = 10;
        this.step = 1;
        this.start = 0;

        this.render();
    }

    animate(direction, callback) {
        const className = direction === 'left' ? 'slide-left' : 'slide-right';

        this.trackTarget.classList.add(className);

        setTimeout(() => {
            this.trackTarget.classList.remove(className);
            callback();
        }, 200); // duración de la animación
    }

    render() {
        const total = this.items.length;

        this.items.forEach(item => item.style.display = 'none');

        for (let i = 0; i < this.windowSize; i++) {
            const index = (this.start + i) % total;
            this.items[index].style.display = 'flex';
        }
    }

    next() {
        this.animate('left', () => {
            const total = this.items.length;
            this.start = (this.start + this.step) % total;
            this.render();
        });
    }

    prev() {
        this.animate('right', () => {
            const total = this.items.length;
            this.start = (this.start - this.step + total) % total;
            this.render();
        });
    }

    openShow(event) {
        const id = event.params.id;

        // Construir la URL final
        const url = this.openShowUrlValue.replace('ID_PLACEHOLDER', id);
        console.log(url);
        window.location.href = url;
    }
}
