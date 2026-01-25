// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyC3aNPjWDUdanjUZRB_WOzHIqTeg771Cgc",
  authDomain: "samptech-fc9a4.firebaseapp.com",
  projectId: "samptech-fc9a4",
  storageBucket: "samptech-fc9a4.firebasestorage.app",
  messagingSenderId: "548249646574",
  appId: "1:548249646574:web:2315e21776e1c087efcaff"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Google login function
function handleGoogleLogin() {
    const provider = new firebase.auth.GoogleAuthProvider();
    provider.setCustomParameters({
        prompt: 'select_account'
    });
    
    firebase.auth().signInWithPopup(provider)
        .then((result) => {
            const user = result.user;
            return user.getIdToken();
        })
        .then((idToken) => {
            // Send to backend
            fetch('../api/login-google-simple.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    idToken: idToken,
                    user: {
                        uid: firebase.auth().currentUser.uid,
                        email: firebase.auth().currentUser.email,
                        displayName: firebase.auth().currentUser.displayName,
                        photoURL: firebase.auth().currentUser.photoURL
                    }
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'minha-conta.php';
                } else {
                    alert('Erro no login: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro de conexão');
            });
        })
        .catch((error) => {
            console.error('Google login error:', error);
            if (error.code === 'auth/popup-closed-by-user') {
                // User closed popup, do nothing
                return;
            }
            alert('Erro no login com Google');
        });
}

// Add event listener
document.addEventListener('DOMContentLoaded', function() {
    const googleLoginBtn = document.getElementById('google-login-btn');
    if (googleLoginBtn) {
        googleLoginBtn.addEventListener('click', handleGoogleLogin);
    }
});