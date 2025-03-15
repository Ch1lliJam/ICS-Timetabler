function validateForm(event, isLogin = false) {
    let user_name = document.getElementById("user_name").value.trim();
    let password = document.getElementById("password").value.trim();
    let email = isLogin ? "" : document.getElementById("email").value.trim();
    let ics_link = isLogin ? "" : document.getElementById("ics_link").value.trim();

    let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    let passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    let usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
    let icsRegex = /^webcal:\/\/www\.kent\.ac\.uk\/timetabling\/ical\/\d+\.ics$/;

    if (!user_name.match(usernameRegex)) {
        alert("Username must be 3-20 characters long, using only letters, numbers, and underscores.");
        event.preventDefault();
        return false;
    }

    if (!isLogin && !email.match(emailRegex)) {
        alert("Invalid email format.");
        event.preventDefault();
        return false;
    }

    if (!password.match(passwordRegex)) {
        alert("Password must be at least 8 characters long, contain at least one letter, one number, and one special character.");
        event.preventDefault();
        return false;
    }

    if (!isLogin && !ics_link.match(icsRegex)) {
        alert("Invalid ICS link. It must follow the format: webcal://www.kent.ac.uk/timetabling/ical/123456.ics");
        event.preventDefault();
        return false;
    }

    return true;
}

function validateModuleLinksForm(event) {
    const urlRegex = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/i;
    const modules = document.querySelectorAll('.lecture-item');

    for (const module of modules) {
        const moduleCode = module.querySelector('h3').textContent;
        const onedriveLink = module.querySelector(`[id^="onedrive_link_"]`).value.trim();
        const moodleLink = module.querySelector(`[id^="moodle_link_"]`).value.trim();
        const pictureLink = module.querySelector(`[id^="picture_link_"]`).value.trim();

        if (!onedriveLink.match(urlRegex)) {
            alert(`Invalid OneDrive link for module code: ${moduleCode}`);
            event.preventDefault();
            return false;
        }

        if (!moodleLink.match(urlRegex)) {
            alert(`Invalid Moodle link for module code: ${moduleCode}`);
            event.preventDefault();
            return false;
        }

        if (pictureLink && !pictureLink.match(urlRegex)) {
            alert(`Invalid Picture link for module code: ${moduleCode}`);
            event.preventDefault();
            return false;
        }
    }

    return true;
}