// config/firebase.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { 
    getAuth, 
    GoogleAuthProvider, 
    signInWithPopup, 
    signOut, 
    onAuthStateChanged 
} from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
import { 
    getFirestore, 
    collection, 
    addDoc, 
    getDocs, 
    doc, 
    getDoc, 
    updateDoc, 
    deleteDoc,
    query,
    where 
} from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

// ⚠️ COLE AQUI AS CREDENCIAIS QUE VOCÊ COPIOU NO PASSO 2
const firebaseConfig = {
  apiKey: "AIzaSyANDZLnB1Ta50MUm6EzRvbfh4e0a4gjfts",
  authDomain: "samptech-e5d45.firebaseapp.com",
  projectId: "samptech-e5d45",
  storageBucket: "samptech-e5d45.firebasestorage.app",
  messagingSenderId: "97076231380",
  appId: "1:97076231380:web:0a28b9ff86852d24408f41"
};
// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);
const googleProvider = new GoogleAuthProvider();

// Exportar para usar em outros arquivos
export { 
    auth, 
    db, 
    googleProvider, 
    signInWithPopup, 
    signOut, 
    onAuthStateChanged,
    collection,
    addDoc,
    getDocs,
    doc,
    getDoc,
    updateDoc,
    deleteDoc,
    query,
    where
};