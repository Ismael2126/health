import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";
import { getStorage } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-storage.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

const firebaseConfig = {
  apiKey: "AIzaSyBiVSJ2Uv_dMV0o0IfJqKjmaMw0wxTmA9M",
  authDomain: "health-aid-maldives.firebaseapp.com",
  projectId: "health-aid-maldives",
  storageBucket: "health-aid-maldives.firebasestorage.app",
  messagingSenderId: "1097832497714",
  appId: "1:1097832497714:web:e03fd12619d6b470c5b22b",
  measurementId: "G-9WEP5N0PME"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);
const storage = getStorage(app);
const auth = getAuth(app);

export { app, db, storage, auth };