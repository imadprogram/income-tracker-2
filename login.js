// LOGIN page script
let register = document.querySelector('#login-form span')
register.addEventListener('click',()=>{
    document.getElementById('login-form').classList.add('hidden')
    document.getElementById('register-form').classList.remove('hidden')
})

let login = document.querySelector('#register-form span')
login.addEventListener('click',()=>{
    document.getElementById('register-form').classList.add('hidden')
    document.getElementById('login-form').classList.remove('hidden')
})