document.addEventListener('DOMContentLoaded', () => {
var options = {
  accessibility: true,
  prevNextButtons: true,
  pageDots: true,
  setGallerySize: false,
  wrapAround: true,
  autoPlay: true,
arrowShape: {
    x0: 10,
    x1: 60,
    y1: 50,
    x2: 60,
    y2: 45,
    x3: 15
  }};

var carousel = document.querySelector('[data-carousel]');
var slides = document.getElementsByClassName('carousel-cell');
var flkty = new Flickity(carousel, options);

flkty.on('scroll', function () {
  flkty.slides.forEach(function (slide, i) {
    var image = slides[i];
    var x = (slide.target + flkty.x) * -1/3;
    image.style.backgroundPosition = x + 'px';
  });
});
    /* --- Mobile Navigation Toggle --- */
    const mobileToggle = document.getElementById('mobile-toggle');
    const mobileNav = document.getElementById('mobile-nav');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            if (mobileNav.style.display === 'grid') {
                mobileNav.style.display = 'none';
            } else {
                mobileNav.style.display = 'grid';
            }
        });
    }

    /* --- Intro Slider Logic (Simple Mockup) --- */
const slidez = [
    {
        // General Mission 
        title: "Restoring Hope in Uganda",
        desc: "We create, provide, and advocate for health and economic empowerment services for marginalized communities. Our vision is to enable children and youth to attain their full life potential.",
        img: "../res/school.jpg"
    },
    {
        // Education & Girl Child 
        title: "Supporting the Girl Child",
        desc: "In Kamuli and Eastern districts, we focus on sanitation for the girl child. By providing sanitary pads and renovating schools, we ensure girls can stay in class with dignity.",
        img: "../res/ss4.jpg"
    },
    {
        // Livelihoods & Skills 
        title: "Empowering Livelihoods",
        desc: "From tailoring skills in Kawempe slums to agro-business in Mukono, we empower women and youth with the skills to build sustainable, independent futures.",
        img: "../res/ss7.jpg"
    }
];

    let currentSlide = 0;
    const slideTitle = document.getElementById('slider-title');
    const slideDesc = document.getElementById('slider-desc');
    const slideCount = document.getElementById('slide-count');
    const slideImg = document.querySelector('.active-slide-img');
    const prevBtn = document.getElementById('prev-slide');
    const nextBtn = document.getElementById('next-slide');

    function updateSlide() {
    const elementsToFade = [slideImg, slideTitle, slideDesc];

    // 1. Start the fade out
    elementsToFade.forEach(el => el.classList.add('fade-out'));

    // 2. Wait for the fade-out duration (400ms) before changing content
    setTimeout(() => {
        // Update the content
        slideTitle.textContent = slidez[currentSlide].title;
        slideDesc.textContent = slidez[currentSlide].desc;
        slideImg.src = slidez[currentSlide].img;
        slideCount.textContent = `${currentSlide + 1} / ${slidez.length}`;

        // 3. Fade back in
        elementsToFade.forEach(el => el.classList.remove('fade-out'));
    }, 400); 
}


    if (nextBtn && prevBtn) {
        nextBtn.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % slidez.length;
            updateSlide();
        });

        prevBtn.addEventListener('click', () => {
            currentSlide = (currentSlide - 1 + slidez.length) % slidez.length;
            updateSlide();
        });
    }
    const wwdNav = document.querySelector('.wwd-nav');
    const prevChevron = wwdNav.querySelector('.fa-chevron-left');
    const nextChevron = wwdNav.querySelector('.fa-chevron-right');
    const buttons = Array.from(wwdNav.querySelectorAll('.wwd-btn'));

    function navigateTabs(direction) {
        // Find the index of the currently active button
        const currentIndex = buttons.findIndex(btn => btn.classList.contains('active'));
        let newIndex;

        if (direction === 'next') {
            // Move to next, or back to 0 if at the end
            newIndex = (currentIndex + 1) % buttons.length;
        } else {
            // Move to prev, or to the last item if at 0
            newIndex = (currentIndex - 1 + buttons.length) % buttons.length;
        }

        // Trigger the click logic already defined for the buttons
        buttons[newIndex].click();

        // Optional: Scroll the button into view if the nav is scrollable on mobile
        buttons[newIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    if (nextChevron && prevChevron) {
        nextChevron.addEventListener('click', () => navigateTabs('next'));
        prevChevron.addEventListener('click', () => navigateTabs('prev'));
    }
    /* --- "What We Do" Tabs Logic --- */
    const tabBtns = document.querySelectorAll('.wwd-btn');
    const tabPanels = document.querySelectorAll('.wwd-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons
            tabBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            btn.classList.add('active');

            // Hide all panels
            tabPanels.forEach(panel => {
                panel.classList.remove('active');
                panel.style.display = 'none'; // Ensure strictly hidden
            });

            // Show target panel
            const targetId = btn.getAttribute('data-target');
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.add('active');
                // Use grid if desktop, or block/flex based on css
                targetPanel.style.display = (window.innerWidth > 900) ? 'grid' : 'block';
            }
        });
    });

    // Handle resize to fix display property of active tab
    window.addEventListener('resize', () => {
        const activePanel = document.querySelector('.wwd-panel.active');
        if (activePanel) {
            activePanel.style.display = (window.innerWidth > 900) ? 'grid' : 'block';
        }
    });
});
