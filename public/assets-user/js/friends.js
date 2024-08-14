document.addEventListener('DOMContentLoaded', () => {
    const followButtons = document.querySelectorAll('.follow-button');
    const unfollowButtons = document.querySelectorAll('.unfollow-button');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    followButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const userId = button.getAttribute('data-user-id');
            try {
                const response = await fetch(`/friends/follow/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken 
                    }
                });
                if (response.ok) {
                    button.textContent = 'Following';
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-primary');
                }
            } catch (error) {
                console.error('Error following user:', error);
            }
        });
    });

    unfollowButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const userId = button.getAttribute('data-user-id');
            try {
                const response = await fetch(`/friends/unfollow/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken 
                    }
                });
                if (response.ok) {
                    const userCard = button.closest('.card.nearby-user');
                    if (userCard) {
                        userCard.remove();
                        
                        const remainingCards = document.querySelectorAll('.card.nearby-user');
                        const noFriendsMessage = document.getElementById('no-friends-message');
                        if (remainingCards.length === 0) {
                            if (noFriendsMessage) {
                                noFriendsMessage.style.display = 'block';
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Error unfollowing user:', error);
            }
        });
    });
});
