document.addEventListener("DOMContentLoaded", () => {
    const faqItems = document.querySelectorAll(".faq-item");

    faqItems.forEach((item) => {
        item.addEventListener("click", () => {
            const answer = item.querySelector(".faq-answer");
            const icon = item.querySelector(".toggle-icon");

            if (answer.style.display === "none" || answer.style.display === "") {
                answer.style.display = "block";
                icon.textContent = "−";
            } else {
                answer.style.display = "none";
                icon.textContent = "+";
            }
        });
    });
});
