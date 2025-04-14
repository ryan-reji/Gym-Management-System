// Function to show trainer details and social media links
function showTrainerDetails(name, email, phone, id, socialLinks) {
    document.getElementById("trainerName").innerText = name;
    document.getElementById("trainerEmail").innerText = email;
    document.getElementById("trainerPhone").innerText = phone;
    document.getElementById("trainerId").innerText = id;

    // Generate social media links dynamically
    const socialIcons = document.getElementById("socialLinks");
    socialIcons.innerHTML = ""; // Clear previous content

    for (const [platform, link] of Object.entries(socialLinks)) {
        const icon = document.createElement("a");
        icon.href = link;
        icon.target = "_blank";
        icon.title = platform.charAt(0).toUpperCase() + platform.slice(1);
        icon.innerHTML = `<i class="fa-brands fa-${platform}"></i>`;
        socialIcons.appendChild(icon);
    }

    document.getElementById("trainerModal").style.display = "block"; // Show the modal
}

// Function to close the modal
function closeModal() {
    document.getElementById("trainerModal").style.display = "none"; // Hide the modal
}

// Close modal if user clicks outside
window.onclick = function(event) {
    const modal = document.getElementById("trainerModal");
    if (event.target === modal) {
        closeModal();
    }
};

 


// Function to check availability
document.getElementById("checkAvailabilityBtn").addEventListener("click", function() {
    const modal = document.getElementById("trainerModal");
    const selectedDate = document.getElementById("availabilityDate").value;
    const availableDates = JSON.parse(modal.getAttribute("data-available-dates"));

    const availabilityMessage = document.getElementById("availabilityMessage");
    const availabilityInput = document.getElementById("availabilityDate");
    console.log(selectedDate);
    if (selectedDate) {
        // Check if the selected date is in the available dates array
        if (availableDates.includes(selectedDate)) {
            availabilityMessage.innerText = "The trainer is available on this date.";
            availabilityMessage.style.fontSize = "1.6rem"; // Increase font size for availability message
            availabilityMessage.style.color="green";
            availabilityMessage.style.fontWeight ="bold";
            availabilityInput.style.fontSize = "1.4rem"; // Decrease font size after selecting a date
        } else {
            availabilityMessage.innerText = "The trainer is not available on this date.";
            availabilityMessage.style.fontSize = "1.6rem"; // Increase font size for unavailability message
            availabilityInput.style.fontSize = "1.4rem"; // Decrease font size after selecting a date
        }
    } else {
        availabilityMessage.innerText = "Please select a date.";
        availabilityMessage.style.fontSize = "1.6rem"; // Increase font size for no date selected message
        availabilityInput.style.fontSize = "2rem"; // Increase font size before selecting a date
    }
});

// Add event listener to close modal on click outside
window.onclick = function(event) {
    const modal = document.getElementById("trainerModal");
    if (event.target == modal) {
        closeModal();
    }
};
document.addEventListener("DOMContentLoaded", () => {
    const video = document.getElementById("background-video");
  
    // Ensure video is unmuted
    video.muted = false;
  
    // Attempt to autoplay the video
    video.play().catch((error) => {
      console.log("Autoplay with sound failed. Waiting for user interaction...");
  
      // Show a play button for user interaction
      const playButton = document.createElement("button");
      playButton.innerText = "Play Video with Sound";
      playButton.style.position = "absolute";
      playButton.style.top = "50%";
      playButton.style.left = "50%";
      playButton.style.transform = "translate(-50%, -50%)";
      playButton.style.zIndex = "1000";
      playButton.style.padding = "10px 20px";
      playButton.style.background = "#b74b4b";
      playButton.style.color = "#fff";
      playButton.style.border = "none";
      playButton.style.borderRadius = "5px";
      playButton.style.cursor = "pointer";
  
      document.body.appendChild(playButton);
  
      // Play video when user clicks the button
      playButton.addEventListener("click", () => {
        video.play();
        playButton.remove(); // Remove the play button
      });
    });
  });
 