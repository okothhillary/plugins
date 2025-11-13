//confirm JS is running
console.log('%cCustom plugins are queued', 'color: red; font-size: 20px;');

window.onload = function() {

    // Create the Scroll-to-Top button
    const scrollBtn = document.createElement('button');
    scrollBtn.id = 'scrollToTop';
    scrollBtn.textContent = '↑ Top';
    document.body.appendChild(scrollBtn);

    // Basic inline style to ensure visibility
    scrollBtn.style.display = 'none'; // hidden initially
    scrollBtn.style.zIndex = '9999';
    scrollBtn.style.cursor = 'pointer';
    scrollBtn.style.position = 'fixed';
    scrollBtn.style.bottom = '40px';
    scrollBtn.style.right = '40px';

    // Show/hide button on scroll
    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            scrollBtn.style.display = 'block';
        } else {
            scrollBtn.style.display = 'none';
        }
    });

    // Scroll to top
    scrollBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
};
