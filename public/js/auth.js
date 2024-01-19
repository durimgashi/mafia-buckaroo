
const initHandlers = async () => {
    $("body .register_button").on('click', async () => {

        let data = {
            fullName: $("body #fullName").val(),
            username: $("body #username").val(),
            password: $("body #password").val()
        }

        if (data.fullName && data.username && data.password) {
            let response = await register(data) 

            if(response.error) {
                return alert(response.message)
            } 

            return window.location.href = LOBBY

        } else {
            return alert("Please fill in all fields.");
        }
    })

    $("body .login_button").on('click', async () => {

        let data = {
            username: $("body #username").val(),
            password: $("body #password").val()
        } 

        if (data.username && data.password) {
            let response = await login(data)

            console.log(response)
        

            if(response.error) {
                return alert(response.message)
            } 

            return window.location.href = '/'

        } else {
            return alert("Please fill in all fields.");
        }
    })
}




(async () => {
    await initHandlers()

    // await register(data)
})()





