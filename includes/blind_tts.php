<script>
const synth = window.speechSynthesis;

function speak(text) {
    if (!text) return;
    synth.cancel();
    const u = new SpeechSynthesisUtterance(text);
    u.lang = 'en-US';
    u.rate = 0.9;
    synth.speak(u);
}

/* TAB / FOCUS */
document.addEventListener('focusin', e => {
    const text = e.target.getAttribute('aria-label')
        || e.target.innerText
        || e.target.value;
    if (text) speak(text.trim());
});

/* CLICK */
document.addEventListener('click', e => {
    const text = e.target.getAttribute('aria-label')
        || e.target.innerText
        || e.target.value;
    if (text) speak('You clicked ' + text.trim());
});
</script>