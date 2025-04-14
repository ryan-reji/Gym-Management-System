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

// Keep your existing code

// Add event listeners for delete buttons
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if(confirm('Are you sure you want to delete this equipment?')) {
                const equipmentId = this.getAttribute('data-id');
                
                // Send delete request
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_equipment.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                
                xhr.onload = function() {
                    if(this.responseText === 'success') {
                        // Remove the card from the DOM
                        const card = button.closest('.equipment-card');
                        card.remove();
                    } else {
                        alert('Error deleting equipment. Please try again.');
                    }
                };
                
                xhr.send('id=' + equipmentId);
            }
        });
    });
});

// Add event listeners for edit buttons
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const equipmentId = this.getAttribute('data-id');
            window.location.href = 'edit_equipment.php?id=' + equipmentId;
        });
    });
});

// Add this to your existing script.js file
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const equipmentCards = document.querySelectorAll('.equipment-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const statusFilter = this.getAttribute('data-status');
            
            equipmentCards.forEach(card => {
                // Use setTimeout to ensure smooth transitions
                if (statusFilter === 'all' || card.getAttribute('data-status') === statusFilter) {
                    card.classList.remove('hidden');
                    setTimeout(() => {
                        card.style.display = 'block';
                    }, 0);
                } else {
                    card.classList.add('hidden');
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300); // Match this with the CSS transition time
                }
            });
        });
    });
});

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




