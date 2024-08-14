document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('profile-picture-input');
    const preview = document.getElementById('profile-picture-preview');
    const noProfileText = document.getElementById('no-profile-text');

    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
                noProfileText.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
            noProfileText.style.display = 'block';
        }
    });
});

