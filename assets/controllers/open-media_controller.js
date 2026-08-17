import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        movieUrl: String,
        showUrl: String,
        personUrl: String,
        id: String,
    };

    openMovie(event) {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = this.movieUrlValue;

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "id";
        input.value = this.idValue;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    openShow(event) {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = this.showUrlValue;

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "id";
        input.value = this.idValue;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    openPerson(event) {
        const url = event.params.personUrl;
        const id = event.params.id;

        const form = document.createElement("form");
        form.method = "POST";
        form.action = url;

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "id";
        input.value = id;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
