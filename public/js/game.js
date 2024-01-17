const nightMessage = "Night fell on the city..."
const dayMessage = "The sun rose over the city..."

const ROLES = {
    COP: 'Cop',
    DOCTOR: 'Doctor',
    MAFIA: 'Mafia',
    TOWNSPERSON: 'Townsperson'
}

const ACTION = {
    VOTE: 'vote',
    KILL: 'kill',
    SAVE: 'save',
    INVESTIGATE: 'investigate'
}

const ALIVE = 'alive'
const DEAD = 'dead' 

const NIGHT = 'night'
const DAY = 'day'

let myRole = ''
let cycle = ''

const toggleCycle = (mode) => {
    const body = document.body
    
    cycle = mode

    if (mode === 'day') {
      body.classList.remove('night')
      body.classList.add('day')
      $('.first-message').text(dayMessage)
    } else if (mode === 'night') {
      body.classList.remove('day')
      body.classList.add('night')
      $('.first-message').text(nightMessage)
    }
}


const pickPlayer = async (player_id) => {
    let response

    if (cycle === DAY) {
        response = await nextRound(player_id, ACTION.VOTE)
    } else if (cycle === NIGHT) {
        if(myRole === ROLES.TOWNSPERSON) {
            response = await nextRound(player_id, ACTION.KILL)
        } else if (myRole === ROLES.DOCTOR) {
            response = await nextRound(player_id, ACTION.SAVE)
        } else if (myRole === ROLES.COP) {
            response = await nextRound(player_id, ACTION.INVESTIGATE)
        } else {
            response = await nextRound(player_id, ACTION.KILL)
        }
    } else {
        return alert("Error cycle not set")
    }

    await initializeGame(response)
}

const shuffleArray = (array) => {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}


const initializeGame = async (new_data) => {

    let data = {}

    if(new_data) {
        data = new_data
    } else {
        data = await startGame()
        shuffleArray(data.players)
    }

    toggleCycle(data.cycle)

    $('body .round').text(`Round: ${data.round}`)
    $('body .second-message').text(data.second_message)
    $('body .progress_messages').empty()

    // Hide the players if the game is over

    data.progress_messages.forEach(e => {
        $('body .progress_messages').append(`<div class="message">${e}</div>`)
    })

    $('body .circle-container').text('')

    if (data.game_over) {
        $('.first-message').addClass('d-none')
        $('.second-message').addClass('is_over')
        return $('.players-container').addClass('d-none')
    }

    data.players.forEach((e, i) => {
        let additional_classes = ''

        if(e.status === DEAD)
            additional_classes = 'invisible'

        if (e.is_bot === '0') {
            myRole = e.role
            additional_classes += ' is_me'
            $('body .role').text(`Role: ${myRole}`)
        }

        $('body .circle-container').append(`<div class="player-circle card ${additional_classes}" data-player-id="${e.player_id}"><div class="card-text">${e.full_name}</div></div>`)
    })

    data.fellow_mafia.forEach(e => {
        $(`body .card[data-player-id="${e.player_id}"]`).addClass('is_mafia')
    })



    if (myRole === ROLES.TOWNSPERSON && data.cycle === NIGHT) {
        setTimeout(async () => {
            await pickPlayer(null)
        }, 4000)
    }
}

const initHandlers = async () => {
    $('body').on('click', '.player-circle:not(.is_mafia):not(.is_me)', async (e) => {

        if (myRole === ROLES.TOWNSPERSON && cycle === NIGHT)
            return alert("Hmm impatient you are")

        let player_id = $(e.currentTarget).attr('data-player-id')

        await pickPlayer(player_id)
    })
}


(async () => {
    await initHandlers()
    await initializeGame()
})()