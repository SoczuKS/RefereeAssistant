/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/reset.css';
import './styles/app.css';
import './styles/form.css';
import './styles/flash_message.css';
import './styles/table.css';

// start the Stimulus application
//import './bootstrap';

function showNextFlash()
{
    let flash_messages = document.getElementById("flash_messages");

    if (flash_messages.children.length === 0) {
        return;
    }

    let flash = flash_messages.firstElementChild;

    flash.classList.add("show")

    setTimeout(function () {
        flash_messages.removeChild(flash);
        showNextFlash();
    }, 5000);
}

$(document).ready(() => {
    showNextFlash();
});
