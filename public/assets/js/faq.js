document.querySelectorAll('.faq-question').forEach(item => {
    item.addEventListener('click', () => {
        const parent = item.parentElement;
        parent.classList.toggle('active');
        const sign = item.querySelector('span');
        sign.textContent = parent.classList.contains('active') ? '-' : '+';
    });
});
