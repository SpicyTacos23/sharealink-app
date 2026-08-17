import { Application } from '@hotwired/stimulus';

const application = Application.start();

// Autocarga de controladores Stimulus con Webpack Encore
const context = require.context('./controllers', true, /_controller\.js$/);

context.keys().forEach((key) => {
    const controller = context(key).default;

    // ejemplo: ./movies_controller.js → movies
    const identifier = key
        .replace('./', '')
        .replace('_controller.js', '')
        .replace(/\//g, '--');

    application.register(identifier, controller);
});

export default application;
