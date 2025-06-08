import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// noinspection JSAnnotator
let echo = new Echo({
    broadcaster: 'pusher',
    key: 'app-key',
    cluster: 'mt1',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

let soketiMessageElement = document.querySelector('[data-soketi-message]')

echo.channel('import.users_import')
    .listen('.rows_created', (event) => {
        console.log('✅ Событие получено:', event);

        let p = document.createElement('p');
        p.textContent = `Обработано строк: ${event.rows}`;

        soketiMessageElement.appendChild(p);

    });
