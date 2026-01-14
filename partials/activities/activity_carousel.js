document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".activity-photos-carousel").forEach(carousel => {

        const track = carousel.querySelector(".carousel-track");
        const slides = carousel.querySelectorAll(".carousel-slide");
        const prevBtn = carousel.querySelector(".carousel-btn.prev");
        const nextBtn = carousel.querySelector(".carousel-btn.next");

        let index = 0;

        function updateCarousel() {
            track.style.transform = `translateX(-${index * 100}%)`;
        }

        if (nextBtn) {
            nextBtn.addEventListener("click", () => {
                index = (index + 1) % slides.length;
                updateCarousel();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener("click", () => {
                index = (index - 1 + slides.length) % slides.length;
                updateCarousel();
            });
        }
    });
});
