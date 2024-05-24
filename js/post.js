// Import the functions you need from the SDKs you need
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
import { getFirestore, collection, getDocs } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

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
const db = getFirestore(app);

async function fetchPosts() {
  const postsContainer = document.getElementById('postsContainer');
  postsContainer.innerHTML = ''; // Clear previous content

  try {
    const querySnapshot = await getDocs(collection(db, "posts"));
    querySnapshot.forEach((doc) => {
      const postData = doc.data();
      const postElement = document.createElement('div');
      postElement.innerHTML = `
        <h2>${postData.name_post}</h2>
        <p>${postData.about_post}</p>
        <p>Start Date: ${postData.start_date}</p>
        <p>End Date: ${postData.end_date}</p>
        <img src="${postData.url}" alt="${postData.name_post}" width="200" />
      `;
      postsContainer.appendChild(postElement);
    });
  } catch (error) {
    console.error("Error fetching posts: ", error);
  }
}

// Fetch and display posts when the page loads
fetchPosts();
