let limit = 20;



// Function to calculate the current university week
function calculateWeek(startDate) {
    const dateElement = document.getElementById('current-date');
    const weekElement = document.getElementById('current-week');
    const today = new Date();
    const formattedDate = today.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
    dateElement.textContent = formattedDate;

    const oneWeek = 7 * 24 * 60 * 60 * 1000; // Milliseconds in a week
    const currentWeek = Math.floor((today - startDate) / oneWeek) + 1;
    weekElement.textContent = "University Week: " + currentWeek;
}

// Set the current date
document.addEventListener("DOMContentLoaded", function () {
    const dateElement = document.getElementById("current-date");
    const weekElement = document.getElementById("current-week");

    // Display today's date
    const today = new Date();
    const formattedDate = today.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    dateElement.textContent = formattedDate;

    // Calculate and display the current university week
    const startDate = '2024-08-05'; // Start date of university week 1
    const currentWeek = calculateWeek(startDate);
    weekElement.textContent = "University Week: " + currentWeek;

    // Example of manipulating the lectures array
    console.log("Lectures from PHP:", lectures);
    filterAndDisplayLectures();
});


// JSON data for lectures


// Mapping of module codes to image filenames
const moduleImages = {
    "COMP3370_COMP5810": "clouds.jpg",
    "COMP3220": "wcomp.jpg",
    "COMP5200": "furtheroop.jpg",
    "COMP3280_COMP5820": "hci.jpg",
    "COMP3200": "oop.jpg",
    "COMP3230": "databases.jpg",
    "COMP3250": "maths_2.jpg",
    "COMP3830": "problem_solving.jpg"
};

// Function to get the current day in UK time
function getCurrentDayUK() {
    try {
        const now = new Date();
        const options = { timeZone: 'Europe/London', weekday: 'long' };
        const currentDay = now.toLocaleDateString('en-GB', options);
        console.log('Current Day (UK):', currentDay);
        return currentDay;
    } catch (error) {
        console.error('Error getting current day in UK time:', error);
        return null;
    }
}

// Function to get the current time in UK time
function getCurrentTimeUK() {
    try {
        const now = new Date();
        const options = { timeZone: 'Europe/London', hour: '2-digit', minute: '2-digit', hour12: false };
        const currentTime = now.toLocaleTimeString('en-GB', options);
        console.log('Current Time (UK):', currentTime);
        return currentTime;
    } catch (error) {
        console.error('Error getting current time in UK time:', error);
        return null;
    }
}

// Function to compare times
function isBeforeTime(currentTime, endTime) {
    try {
        const [currentHour, currentMinute] = currentTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);
        const isBefore = currentHour < endHour || (currentHour === endHour && currentMinute < endMinute);
        console.log(`Comparing times: ${currentTime} < ${endTime} = ${isBefore}`);
        return isBefore;
    } catch (error) {
        console.error('Error comparing times:', error);
        return false;
    }
}


function filterAndDisplayLectures() {
    try {
        // Get the current day and time in UK time
        const currentDate = new Date();
        const currentTime = getCurrentTimeUK();
        if (!currentDate || !currentTime) throw new Error('Failed to get current day or time');
        console.log(`Total lectures fetched: ${lectures.length}`);

        // Add a fake lecture as a filler for 18th feb 2025 at 12:00 AM
        const fakeLecture = {
            module_name: "Beginning of the Week",
            module_code: "FAKE101",
            day: "18th February 2025",
            start_time: "00:00",
            end_time: "01:00",
            location: "Nowhere",
            onedrive_link: "#",
            moodle_link: "#",
            presto_link: "#",
            maps_link: "#",
        };

        sortedLectures = lectures;
        // Populate the lecture items with the filtered lectures
        const lectureContainer = document.getElementById('lecture-container');
        lectureContainer.innerHTML = ''; // Clear existing lectures

        sortedLectures.forEach((lecture, index) => {
            const item = document.createElement('div');
            item.className = 'item';
            item.style.backgroundImage = 'url(image/default.jpg)';

            const content = document.createElement('div');
            content.className = 'content';

            const name = document.createElement('div');
            name.className = 'name';
            name.textContent = lecture.module_name;

            const des = document.createElement('div');
            des.className = 'des';
            des.innerHTML = `
                <p><strong>${lecture.module_code}</strong></p>
                <p>${lecture.day}</p>
                <p>${lecture.start_time} - ${lecture.end_time}</p>
                <p>${lecture.location}</p>
            `;

            // Set the background image based on the module code
            const imageUrl = moduleImages[lecture.module_code];
            if (imageUrl) {
                item.style.backgroundImage = `url(image/${imageUrl})`;
            } else {
                console.warn(`Image URL not found for module code: ${lecture.module_code}`);
                item.style.backgroundImage = 'url(image/WIP_image.jpg)';
            }

            // Set the href attributes for the buttons
            const onedriveBtn = document.createElement('a');
            onedriveBtn.className = 'onedrive-btn';
            onedriveBtn.href = lecture.onedrive_link || '#';
            onedriveBtn.target = '_blank';
            onedriveBtn.innerHTML = '<img src="image/onedrive_icon.png" alt="OneDrive Icon"> OneDrive';

            const moodleBtn = document.createElement('a');
            moodleBtn.className = 'moodle-btn';
            moodleBtn.href = lecture.moodle_link || '#';
            moodleBtn.target = '_blank';
            moodleBtn.innerHTML = '<img src="image/moodle_icon.png" alt="Moodle Icon"> Moodle';

            const prestoBtn = document.createElement('a');
            prestoBtn.className = 'presto-btn';
            prestoBtn.href = lecture.presto_link || '#';
            prestoBtn.target = '_blank';
            prestoBtn.innerHTML = '<img src="image/presto_icon.png" alt="Presto Icon"> Presto';

            const mapBtn = document.createElement('a');
            mapBtn.className = 'map-btn';
            mapBtn.href = lecture.maps_link || '#';
            mapBtn.target = '_blank';
            mapBtn.innerHTML = '<img src="image/maps_icon.png" alt="Map Icon"> Map';

            content.appendChild(name);
            content.appendChild(des);
            content.appendChild(onedriveBtn);
            content.appendChild(moodleBtn);
            content.appendChild(prestoBtn);
            content.appendChild(mapBtn);

            item.appendChild(content);
            lectureContainer.appendChild(item);

            // Add fade-in effect
            des.classList.add('fade-in');
            onedriveBtn.classList.add('fade-in');
            moodleBtn.classList.add('fade-in');
            prestoBtn.classList.add('fade-in');
            mapBtn.classList.add('fade-in');
        });

        // Add "Load More" button if there are more lectures to load
        if (sortedLectures.length === limit) {
            const loadMoreItem = document.createElement('div');
            loadMoreItem.className = 'item';
            loadMoreItem.style.backgroundImage = 'url(image/no_lecture.jpeg)';
            loadMoreItem.innerHTML = `
                <div class="content">
                    <div class="name">Lecture limit reached</div>
                    <div class="des">Would you like to load more?</div>
                    <a class="load-more-btn" href="#" id="load-more-btn">Load More</a>
                </div>
            `;
            lectureContainer.appendChild(loadMoreItem);

            document.getElementById('load-more-btn').addEventListener('click', function (e) {
                e.preventDefault();
                limit += 20;
                fetchLectures(limit);
                loadMoreItem.remove(); // Remove the "Load More" item after clicking
            });
        }
    } catch (error) {
        console.error('Error filtering and displaying lectures:', error);
    }
}









// Function to set the current date and week
function setCurrentDateAndWeek() {
    const currentDateElement = document.getElementById('current-date');
    const currentWeekElement = document.getElementById('current-week');

    // Set current date
    const now = new Date();
    currentDateElement.innerText = now.toLocaleDateString('en-GB', {
        year: 'numeric', month: '2-digit', day: '2-digit'
    });

    // Calculate university week based on the first week starting on 5th August
    const firstWeekStart = new Date(now.getFullYear(), 7, 5); // 5th August
    const weekNumber = Math.floor((now - firstWeekStart) / (1000 * 60 * 60 * 24 * 7)) + 1; // Add 1 for week count

    currentWeekElement.innerText = `University Week: ${weekNumber}`;
}

