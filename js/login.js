// js/login.js
import { auth, provider } from "./firebase.js";
import { signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

const btnGoogle = document.getElementById("login-google");
let emExecucao = false;

if (btnGoogle) {
    btnGoogle.addEventListener("click", async (e) => {
        e.preventDefault();
        
        if (emExecucao) return;
        emExecucao = true;
        
        // Adicionar loading state
        const originalText = btnGoogle.innerHTML;
        btnGoogle.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Conectando...';
        btnGoogle.disabled = true;

        try {
            // Login com Google
            const result = await signInWithPopup(auth, provider);
            const user = result.user;
            
            // 🔑 Obter token do Firebase
            const idToken = await user.getIdToken();
            
            console.log('Usuário Google:', {
                uid: user.uid,
                email: user.email,
                name: user.displayName,
                photo: user.photoURL
            });

            // Enviar para API PHP
            const response = await fetch("/SampTech/api/login-google.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ token: idToken })
            });

            const data = await response.json();
            console.log("Resposta da API:", data);

            if (data.success) {
                // Login bem sucedido
                console.log("Redirecionando para minha-conta.php...");
                
                // Pequeno delay para garantir que a sessão foi configurada
                setTimeout(() => {
                    window.location.href = "/SampTech/pages/minha-conta.php";
                }, 500);
                
            } else {
                // Mostrar erro
                alert(data.message || "Erro no login com Google");
                console.error("Erro da API:", data);
                
                // Resetar botão
                btnGoogle.innerHTML = originalText;
                btnGoogle.disabled = false;
                emExecucao = false;
            }

        } catch (err) {
            console.error("Erro no login Google:", err);
            
            if (err.code === 'auth/popup-blocked') {
                alert("O popup foi bloqueado pelo navegador. Por favor, permita popups para este site.");
            } else if (err.code === 'auth/popup-closed-by-user') {
                // Usuário fechou o popup, não precisa mostrar erro
                console.log("Popup fechado pelo usuário");
            } else {
                alert("Erro no login com Google: " + err.message);
            }
            
            // Resetar botão
            btnGoogle.innerHTML = originalText;
            btnGoogle.disabled = false;
            emExecucao = false;
        }
    });
}