function togglePasswordVisibility() {
    const passwordInput = document.getElementById("passwordInput");
    const eyeClosed = document.getElementById("eyeClosed");
    const eyeOpen = document.getElementById("eyeOpen");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeClosed.style.display = "none";
        eyeOpen.style.display = "inline";
    } else {
        passwordInput.type = "password";
        eyeClosed.style.display = "inline";
        eyeOpen.style.display = "none";
    }
}

document.addEventListener('DOMContentLoaded', function() {
    function handleInputFocusAndBlur(input, labelId) {
        const label = document.getElementById(labelId);

        input.addEventListener('focus', function() {
            if (label && input.value === '') {
                label.querySelector('span').style.opacity = '0';
            }
        });

        input.addEventListener('blur', function() {
            if (label) {
                if (input.value === '') {
                    label.querySelector('span').style.opacity = '1';
                } else {
                    label.querySelector('span').style.opacity = '0';
                }
            }
        });

        input.addEventListener('input', function() {
            if (label && input.value === '') {
                label.querySelector('span').style.opacity = '1';
            } else {
                label.querySelector('span').style.opacity = '0';
            }
        });
    }

    const usernameInput = document.querySelector('.input-container.username input');
    const passwordInput = document.querySelector('.input-container.password input');

    handleInputFocusAndBlur(usernameInput, 'usernameLabel'); // Corrected label ID
    handleInputFocusAndBlur(passwordInput, 'passwordLabel'); // Corrected label ID

    //document.querySelector('.login-button').addEventListener('click', login);
    document.querySelector('.likha-button').addEventListener('click', likha);
    document.querySelector('.hypefive-button').addEventListener('click', hypefive);
    document.querySelector('.signup-button').addEventListener('click', signUp);
});

function login(event) {
    event.preventDefault();

    const emailInput = document.getElementById("email").value;
    const passwordInput = document.getElementById("password").value;

    const formData = new FormData();
    formData.append('email', emailInput);
    formData.append('password', passwordInput);

    fetch('http://localhost/systematica/test/app/controller/login.php', {
        method: 'POST',
        body: formData
    })
    
    
    .then(response => response.text()) // Use text() to get the raw response
    .then(text => {
        try {
            const data = JSON.parse(text); // Try parsing the JSON
            if (data.success) {
                window.location.href = "home/homepage.php";
            } else {
                alert('Login failed: ' + (data.message || 'Unknown error'));
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
            console.error('Response text:', text);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
