const register = async (data) => {
    return await $.ajax({
        url: REGISTER,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(data)
    })
}


const login = async (data) => {
    return await $.ajax({
        url: LOGIN,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(data)
    })
}

const startGame = async () => {
    return await $.ajax({
        url: START_GAME,
        type: "GET",
    })
}

const nextRound = async (player_id, action) => {
    return await $.ajax({
        url: PICK_PLAYER,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
            player_id,
            action
        })
    })
}






