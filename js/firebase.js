// Firebase core
import { initializeApp } from
  "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";

// Firestore
import { getFirestore } from
  "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

// Auth
import { getAuth } from
  "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

// Storage
import { getStorage  } from
  "https://www.gstatic.com/firebasejs/10.7.1/firebase-storage.js";

// 🔑 Firebase configuration (CORRECT)
const firebaseConfig = {
  apiKey: "AIzaSyB5IMCxjOXHZIqkG-WWN4jCOAG39RZN7X0",
  authDomain: "health-aid-maldives.firebaseapp.com",
  projectId: "health-aid-maldives",
  storageBucket: "health-aid-maldives.appspot.com",
  messagingSenderId: "1097832497714",
  appId: "1:1097832497714:web:8e297c2f45df15ccc5b22b"
};

// Initialize Firebase ONCE
const app = initializeApp(firebaseConfig);

// Export services
export const db = getFirestore(app);
export const auth = getAuth(app);
export const storage = getStorage(app);
