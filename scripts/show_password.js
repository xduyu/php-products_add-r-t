const userpassword = document.getElementById('userpassword');
const confirmPassword = document.getElementById('confirm_password');
const showPass = document.getElementById('showPass');

function togglePassword() {
    if (userpassword.getAttribute("type") == "password") {
        userpassword.setAttribute("type", "text")
        confirmPassword.setAttribute("type", "text")
    } else {
        userpassword.setAttribute("type", "password")
        confirmPassword.setAttribute("type", "password")
    }
}

showPass.addEventListener('click', () => {
    togglePassword()
})