const sideMenu = document.querySelector('aside');
const menuBtn = document.querySelector('#menu_bar');
const closeBtn = document.querySelector('#close_btn');
const themeToggler = document.querySelector('.theme-toggler');

// Function to apply the saved theme
function applyTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme-variables');
        themeToggler.querySelector('span:nth-child(1)').classList.remove('active');
        themeToggler.querySelector('span:nth-child(2)').classList.add('active');
    } else {
        document.body.classList.remove('dark-theme-variables');
        themeToggler.querySelector('span:nth-child(1)').classList.add('active');
        themeToggler.querySelector('span:nth-child(2)').classList.remove('active');
    }
}

// Apply theme on page load
document.addEventListener('DOMContentLoaded', applyTheme);

menuBtn.addEventListener('click', () => {
    sideMenu.style.display = "block";
});

closeBtn.addEventListener('click', () => {
    sideMenu.style.display = "none";
});

themeToggler.addEventListener('click', () => {
    // Toggle dark theme class
    document.body.classList.toggle('dark-theme-variables');
    
    // Toggle active states of theme toggle buttons
    themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
    themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');
    
    // Save theme preference to localStorage
    const isDarkTheme = document.body.classList.contains('dark-theme-variables');
    localStorage.setItem('theme', isDarkTheme ? 'dark' : 'light');
});

// Function to update the progress bars
function updateProgressBars() {
    const membersCircle = document.getElementById('members-circle');
    const equipmentCircle = document.getElementById('equipment-circle');
    const trainersCircle = document.getElementById('trainers-circle');
 
    // Set the stroke-dashoffset based on the percentage
    membersCircle.style.setProperty('--dash-offset', 150 - (150 * 0.6)); // 60%
    equipmentCircle.style.setProperty('--dash-offset', 150 - (150 * 0.94)); // 94%
    trainersCircle.style.setProperty('--dash-offset', 150 - (150 * 0.95)); // 95%
}
 
// Call the function to update the progress bars
updateProgressBars();
