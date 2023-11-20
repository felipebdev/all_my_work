// Import the functions you need from the SDKs you need
import firebase from 'firebase/compat/app';
import 'firebase/compat/storage';


const firebaseConfig = {
  apiKey: "AIzaSyCtl9xsFS74XS-LBPxcoWP-bT4IimduZ5s",
  authDomain: "redesocialbalvin.firebaseapp.com",
  projectId: "redesocialbalvin",
  storageBucket: "redesocialbalvin.appspot.com",
  messagingSenderId: "430945234240",
  appId: "1:430945234240:web:0a9ac0b99f22e56c059ea5",
  measurementId: "G-N699B0EZM7"
};


if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
}

export { firebase };