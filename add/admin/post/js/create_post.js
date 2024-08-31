// Import the functions you need from the SDKs you need
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
import { getFirestore, collection, addDoc, updateDoc, doc, getDocs, getDoc } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";
import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-storage.js";

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
const storage = getStorage(app);
const db = getFirestore(app);

async function Create() {
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
    // Upload the file to Firebase Storage
    const snapshot = await uploadBytes(storageRef, file);
    console.log('Uploaded a blob or file!', snapshot);

    // Get the file's URL
    const url = await getDownloadURL(snapshot.ref);
    console.log('File available at', url);
    console.log(file);
    console.log(snapshot.ref);
    
    

    // Add a new document
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
    console.error("Error uploading file or saving document: ", error);
  }
}

async function Update() {
  const fileInput = document.getElementById('imageInput');
  const name = document.getElementById('name').value;
  const about = document.getElementById('about').value;
  const start_date = document.getElementById('start_date').value;
  const end_date = document.getElementById('end_date').value;
  const file = fileInput.files[0];

  
  try {
    if (file) {
      const storageRef = ref(storage, 'posts/' + file.name);
      // Upload the new file to Firebase Storage
      const snapshot = await uploadBytes(storageRef, file);
      console.log('Uploaded a blob or file!', snapshot);

      // Get the file's URL
      const url = await getDownloadURL(snapshot.ref);
      console.log('File available at', url);

      // Fetch all documents from the "posts" collection
      const querySnapshot = await getDocs(collection(db, "posts"));
      let docId = "";

      // Find the document to update
      querySnapshot.forEach((doc) => {
        if (doc.data().name_post === name) { // Check for matching condition
          docId = doc.id; // Store the document ID
        }
      });

      if (docId) {
        // Update the existing document
        const docRef = doc(db, "posts", docId);
        await updateDoc(docRef, {
          name_post: name,
          about_post: about,
          start_date: start_date,
          end_date: end_date,
          name: file.name,
          url: url
        });
        console.log("Document updated successfully");
      } else {
        console.error("No document found to update");
      }
    } else {
      console.log("No file selected for update");
    }
    const querySnapshot = await getDocs(collection(db, "posts"));
    let docId = "";
    // Find the document to update
    querySnapshot.forEach((doc) => {
      if (doc.data().name_post === name) { // Check for matching condition
        docId = doc.id; // Store the document ID
      }
    });
    if (docId) {
      // Update the existing document
      const docRef = doc(db, "posts", docId);
      await updateDoc(docRef, {
        name_post: name,
        about_post: about,
        start_date: start_date,
        end_date: end_date,

      });
      console.log("Document updated successfully");
    } else {
      console.error("No document found to update");
    }

  } catch (error) {
    console.error("Error uploading file or updating document: ", error);
  }
}

async function fetchPosts() {
  try {
    // Get a reference to the "posts" collection
    const querySnapshot = await getDocs(collection(db, "posts"));
    
    // Get the <ul> element to display the list
    const postsList = document.getElementById('postsList');

    // Clear any existing content
    postsList.innerHTML = '';

    // Loop through the documents and append them to the list
    querySnapshot.forEach((doc) => {
      const data = doc.data();
      
      // Create a list item element
      const listItem = document.createElement('li');
      listItem.textContent = `Name: ${data.name_post}`;
      
      // Create a button element
      const button = document.createElement('button');
      button.textContent = 'Update'; // Button text
      button.addEventListener('click', () => UploadPost(doc.id)); // Attach click event

      // Append the list item and button to the <ul> element
      const listItemContainer = document.createElement('div');
      listItemContainer.appendChild(listItem);
      listItemContainer.appendChild(button);
      postsList.appendChild(listItemContainer);
    });
  } catch (error) {
    console.error("Error fetching documents: ", error);
  }
}

async function UploadPost(docId) {
  try {
    // Fetch the document by ID
    const docRef = doc(db, "posts", docId);
    const docSnapshot = await getDoc(docRef);
    if (docSnapshot.exists()) {
      const postData = docSnapshot.data()['url'];

      console.log(postData);
      const fileInput = document.getElementById('imageInput').postData;
      const name = document.getElementById('name').value=docSnapshot.data()['name_post'];
      const about = document.getElementById('about').value=docSnapshot.data()['about_post'];
      const start_date = document.getElementById('start_date').value=docSnapshot.data()['start_date'];
      const end_date = document.getElementById('end_date').value=docSnapshot.data()['end_date'];
      console.log("Document data:", docSnapshot.data());
    } else {
      console.error("No such document!");
    }
  } catch (error) {
    console.error("Error fetching document: ", error);
  }
}

// Call the function to fetch and display posts
fetchPosts();
window.Update = Update;
window.Create = Create;
