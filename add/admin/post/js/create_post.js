// Import the functions you need from the SDKs you need
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
import { getFirestore, collection, addDoc } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";
import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-storage.js";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyDHU4xYC86SyeydbK8GPuAl40Jafh2XDPY",
  authDomain: "test-posts-1faa0.firebaseapp.com",
  projectId: "test-posts-1faa0",
  storageBucket: "test-posts-1faa0.appspot.com",
  messagingSenderId: "45372316395",
  appId: "1:45372316395:web:46da7b9d262b5f17f8fcae"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
// Initialize Cloud Firestore and get a reference to the service
const storage = getStorage(app);
const db = getFirestore(app);


// [END add_alan_turing_modular]
async function uploadImage() {
  const fileInput = document.getElementById('imageInput');
  const name = document.getElementById('name').value;
  const about = document.getElementById('about').value;
  const start_date = document.getElementById('start_date').value;
  const end_date = document.getElementById('end_date').value;
  const file = fileInput.files[0];

  if (!file) {
    alert("Please select a file first.");
    return;
  }

  const storageRef = ref(storage, 'posts/' + file.name);
  try {
    // Upload the file
    const snapshot = await uploadBytes(storageRef, file);
    console.log('Uploaded a blob or file!', snapshot);

    // Get the file's URL
    const url = await getDownloadURL(snapshot.ref);
    console.log('File available at', url);

    // Add file URL to Firestore
    const docRef = await addDoc(collection(db, "posts"), {
      name_post: name,
      about_post: about,
      start_date: start_date,
      end_date: end_date,
      name: file.name,
      url: url
    });
    console.log("Document written with ID: ", docRef.id);
  } catch (error) {
    console.error("Error uploading file: ", error);
  }
}
window.uploadImage = uploadImage;
