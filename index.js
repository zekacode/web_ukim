// Smooth scrolling effect
let scrollPosition = window.scrollY;
let targetScrollPosition = window.scrollY;
let isScrolling = false;

const smoothScroll = () => {
    if (isScrolling) {
        scrollPosition += (targetScrollPosition - scrollPosition) * 0.1;
        window.scrollTo(0, scrollPosition);

        // Stop scrolling when close enough to target position
        if (Math.abs(targetScrollPosition - scrollPosition) < 0.5) {
            isScrolling = false;
        } else {
            requestAnimationFrame(smoothScroll);
        }
    }
};

window.addEventListener('wheel', (event) => {
    // Prevent default scrolling
    event.preventDefault();

    // Calculate the target scroll position based on wheel delta
    targetScrollPosition += event.deltaY * 0.5; // Adjust the multiplier for scroll speed
    targetScrollPosition = Math.max(0, Math.min(document.body.scrollHeight - window.innerHeight, targetScrollPosition));
    
    if (!isScrolling) {
        isScrolling = true;
        smoothScroll();
    }
}, { passive: false });


document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.vision-item');
    const contents = document.querySelectorAll('.content');

    items.forEach(item => {
        item.addEventListener('click', () => {
            // Remove active class from all contents
            contents.forEach(content => content.classList.remove('active'));

            // Add active class to target content
            const target = item.getAttribute('data-target');
            document.getElementById(target).classList.add('active');
        });
    });
});
    
document.addEventListener('DOMContentLoaded', () => {
    const carouselTrack = document.querySelector('.logo-carousel-track');
    const logos = Array.from(carouselTrack.children);

    // Duplicate logos for seamless animation
    logos.forEach(logo => {
        const clone = logo.cloneNode(true);
        carouselTrack.appendChild(clone);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    // Vision Section Animation
    const items = document.querySelectorAll('.vision-item');
    const contents = document.querySelectorAll('.content');

    items.forEach(item => {
        item.addEventListener('click', () => {
            contents.forEach(content => content.classList.remove('active'));
            const target = item.getAttribute('data-target');
            document.getElementById(target).classList.add('active');
        });
    });

    const carouselTrack = document.querySelector('.logo-carousel-track');
    const logos = Array.from(carouselTrack.children);
    logos.forEach(logo => {
        const clone = logo.cloneNode(true);
        carouselTrack.appendChild(clone);
    });

    // Scroll Animation
    const animatedElements = document.querySelectorAll('.vision, .updates, .card, .footer, .hero-content');
    
    const animateOnScroll = () => {
        animatedElements.forEach(el => {
            const elementPosition = el.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            if (elementPosition < windowHeight - 100) {
                el.classList.add('fade-in');
            }
        });
    };

    window.addEventListener('scroll', animateOnScroll);

    // Initial check in case elements are in view on page load
    animateOnScroll();
});

function initializeCountdown(eventDateStr, countdownElementId) {
    const countdownTimerElement = document.querySelector(`#${countdownElementId} .countdown-timer`);
    if (!countdownTimerElement) {
        console.error("Countdown timer element not found for ID:", countdownElementId);
        return;
    }

    const eventDate = new Date(eventDateStr).getTime();

    if (isNaN(eventDate)) {
        countdownTimerElement.innerHTML = "<p>Tanggal event tidak valid.</p>";
        console.error("Invalid Event Date:", eventDateStr);
        return;
    }

    const interval = setInterval(() => {
        const now = new Date().getTime();
        const distance = eventDate - now;

        if (distance < 0) {
            clearInterval(interval);
            // Ganti seluruh section countdown dengan pesan event dimulai
            const countdownSection = document.getElementById(countdownElementId);
            if(countdownSection){
                countdownSection.className = 'event-status-message started'; // Ubah class untuk styling
                countdownSection.innerHTML = "<h3>Event Telah Dimulai!</h3>";
            }
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Gunakan String().padStart(2, '0') untuk format dua digit
        countdownTimerElement.innerHTML = `
            <div class="countdown-item"><h4>${String(days).padStart(2, '0')}</h4><span>Hari</span></div>
            <div class="countdown-item"><h4>${String(hours).padStart(2, '0')}</h4><span>Jam</span></div>
            <div class="countdown-item"><h4>${String(minutes).padStart(2, '0')}</h4><span>Menit</span></div>
            <div class="countdown-item"><h4>${String(seconds).padStart(2, '0')}</h4><span>Detik</span></div>
        `;
    }, 1000);
}

document.addEventListener("DOMContentLoaded", () => {
    const burgerMenu = document.getElementById('burger-menu');
    const navOverlay = document.getElementById('nav-overlay');
    const closeBtn = document.getElementById('close-btn');

    if (burgerMenu && navOverlay && closeBtn) {
        burgerMenu.addEventListener('click', () => {
            navOverlay.classList.add('active');
        });

        closeBtn.addEventListener('click', () => {
            navOverlay.classList.remove('active');
        });

        navOverlay.addEventListener('click', (e) => {
            if (e.target === navOverlay) {
                navOverlay.classList.remove('active');
            }
        });
    }
});
