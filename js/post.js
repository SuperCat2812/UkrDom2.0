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

  let list_ivent = [];

  try {
    const querySnapshot = await getDocs(collection(db, "posts"));

    querySnapshot.forEach((doc) => {
      const postData = doc.data();
      list_ivent.push(postData); // Store data into list_ivent
    });

    // Получаем текущую дату (год, месяц, день)
    const currentDate = new Date();
    const currentDateWithoutTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());

    console.log('Текущая дата без времени:', currentDateWithoutTime);

    // Фильтрация по дате начала (если start_date в тот же день или позже)
    const filteredEvents = list_ivent.filter(event => {
      const eventStartDate = new Date(event.start_date);
      const eventStartDateWithoutTime = new Date(eventStartDate.getFullYear(), eventStartDate.getMonth(), eventStartDate.getDate());
      return eventStartDateWithoutTime >= currentDateWithoutTime;
    });
    const filteredEvents_last = list_ivent.filter(event_last => {
      const eventStartDate = new Date(event_last.start_date);
      const eventStartDateWithoutTime = new Date(eventStartDate.getFullYear(), eventStartDate.getMonth(), eventStartDate.getDate());
      return eventStartDateWithoutTime <= currentDateWithoutTime;
    });
    console.log('Отфильтрованные события:', filteredEvents, `i`, filteredEvents_last);

    // Отображение отфильтрованных событий
    filteredEvents.forEach((postData) => {
      const postElement = document.createElement('div');
      let date_start = new Date(postData.start_date).toLocaleDateString();
      let date_end = new Date(postData.end_date).toLocaleDateString();
      postElement.innerHTML = `
        <h2>${postData.name_post}</h2>
        <img src="${postData.url}" alt="${postData.name_post}" width="200" />
        <p>${postData.about_post}</p>
        <p class="start_day">Start Date: ${date_start}</p>
        <p class="end_day">End Date: ${date_end}</p>
      `;
      postsContainer.appendChild(postElement);
    });
    filteredEvents_last.forEach((postData) => {
      const postElement = document.createElement('div');
      let date_start = new Date(postData.start_date).toLocaleDateString();
      let date_end = new Date(postData.end_date).toLocaleDateString();
      postElement.innerHTML = `
        <h2>${postData.name_post}</h2>
        <img src="${postData.url}" alt="${postData.name_post}" width="200" />
        <p>${postData.about_post}</p>
        <p class="start_day">Start Date: ${date_start}</p>
        <p class="end_day">End Date: ${date_end}</p>
      `;
      postsContainer.appendChild(postElement);
    });

  } catch (error) {
    console.error("Error fetching posts: ", error);
  }
}

// Fetch and display posts when the page loads
fetchPosts();
