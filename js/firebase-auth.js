function handleGoogleLogin() {
    const provider = new firebase.auth.GoogleAuthProvider();
    
    firebase.auth().signInWithPopup(provider)
        .then((result) => {
            const user = result.user;
            
            // Get ID token
            return user.getIdToken();
        })
        .then((idToken) => {
            // Send ID token and user data to backend
            fetch('/api/login-google-simple.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    idToken: idToken,
                    user: {
                        uid: user.uid,
                        displayName: user.displayName,
                        photoURL: user.photoURL
                    }
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to dashboard or home page
                    window.location.href = '/minha-conta.php';
                } else {
                    console.error('Login failed:', data.message);
                    alert('Erro no login: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro de conexão');
            });
        })
        .catch((error) => {
            console.error('Google login error:', error);
            alert('Erro no login com Google');
        });
}
// Add event listener to Google login button
document.addEventListener('DOMContentLoaded', function() {
    const googleLoginBtn = document.getElementById('google-login-btn');
    if (googleLoginBtn) {
        googleLoginBtn.addEventListener('click', handleGoogleLogin);
    }
});