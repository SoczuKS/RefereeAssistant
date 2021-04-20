function onLoad() {
    datetimePolyfill();

    pastMatches();
    futureMatches();

    pastTournaments();
    futureTournaments();

    setPostponeDatePickerListener();

    matchNumberCopy();
}

matchNumberCopy = () => {
    let matchNumbers = document.getElementsByClassName('matchNumberCol');

    for(let i = 0; i < matchNumbers.length; i++) {
        matchNumbers[i].addEventListener('click', copyMatchNumberToClipboard, false);
    }
};

copyMatchNumberToClipboard = (event) => {
    event.preventDefault();
    event.stopPropagation();

    let txt = event.target.innerText;
    let promise = navigator.clipboard.writeText(txt);
};

function pastMatches() {
    const rows = document.getElementsByClassName('pastMatch');

    for(let i = 0; i < rows.length; i++) {
        rows[i].addEventListener('click', () => {
            window.location = "index.php?page=match&id=" + rows[i].dataset.id;
        })
    }
}

function futureMatches() {
    const rows = document.getElementsByClassName('futureMatch');

    for(let i = 0; i < rows.length; i++) {
        rows[i].addEventListener('click', () => {
            window.location = "index.php?page=postpone&id=" + rows[i].dataset.id;
        })
    }
}

function futureTournaments() {
    const rows = document.getElementsByClassName('futureTournament');

    for(let i = 0; i < rows.length; i++) {
        rows[i].addEventListener('click', () => {
            window.location = "index.php?page=postpone&what=tournament&id=" + rows[i].dataset.id;
        })
    }
}

function pastTournaments() {
    const rows = document.getElementsByClassName('pastTournament');

    for(let i = 0; i < rows.length; i++) {
        rows[i].addEventListener('click', () => {
            window.location = "index.php?page=tournament&id=" + rows[i].dataset.id;
        })
    }
}

function datetimePolyfill() {
    flatpickr('input[type="datetime-local"]', {
        enableTime: true,
        altInput: true,
        altFormat: 'd.m.Y H:i',
        dateFormat: 'Y-m-dTH:i',
        locale: 'pl',
        time_24hr: true,
        minDate: 'today',
        minTime: '08:00',
        maxTime: '23:00'
    });
}

function setPostponeDatePickerListener() {
    const picker = document.getElementById('postponeDatePicker');

    if(picker == null) return;

    picker.addEventListener('change', () => {
        const button = document.getElementById('postponeButton');

        button.disabled = false;
    })
}