<?php
// determine dashboard link for floating button
$dashLink = null;
if (!empty($_SESSION['disability_category'])) {
    if ($_SESSION['disability_category'] === 'Blind') {
        $dashLink = '/pwd-employment-system/pwd/dashboard_blind.php';
    } elseif ($_SESSION['disability_category'] === 'Deaf') {
        $dashLink = '/pwd-employment-system/pwd/dashboard_deaf.php';
    } elseif ($_SESSION['disability_category'] === 'Physical Disability') {
        $dashLink = '/pwd-employment-system/pwd/dashboard_physical.php';
    }
} elseif (!empty($_SESSION['business_permit_no'])) {
    $dashLink = '/pwd-employment-system/employer/dashboard.php';
} elseif (!empty($_SESSION['admin_role'])) {
    $dashLink = '/pwd-employment-system/admin/dashboard.php';
}
?>

<?php if ($dashLink): ?>
    <a href="<?= htmlspecialchars($dashLink) ?>"
       style="
         position:fixed;
         right:1.5rem;
         bottom:1.5rem;
         z-index:50;
         text-decoration:none;
       "
       class="btn btn-outline btn-sm"
       aria-label="Back to dashboard">
        ⌂ Dashboard
    </a>
<?php endif; ?>

</main>
<footer style="padding: 1.2rem 1.25rem 2rem; text-align:center; font-size:0.8rem; color:#6b7280;">
    © <?= date('Y') ?> PWD Employment Information System – Province of Antique
</footer>

<script>
// Voice "back" using Web Speech API (Chrome-only mostly)
(function() {
    const btn = document.getElementById('voiceBackBtn');
    if (!btn) return;

    if (!('webkitSpeechRecognition' in window || 'SpeechRecognition' in window)) {
        btn.style.display = 'none';
        return;
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const recognition = new SpeechRecognition();
    recognition.lang = 'en-US';
    recognition.continuous = false;
    recognition.interimResults = false;

    let listening = false;

    btn.addEventListener('click', () => {
        if (!listening) {
            recognition.start();
            listening = true;
            btn.textContent = '🎙 Listening... say "back"';
        } else {
            recognition.stop();
            listening = false;
            btn.textContent = '🎙 Voice Back';
        }
    });

    recognition.addEventListener('result', (event) => {
        const transcript = event.results[0][0].transcript.toLowerCase().trim();
        if (transcript.includes('back')) {
            history.back();
        }
    });

    recognition.addEventListener('end', () => {
        listening = false;
        btn.textContent = '🎙 Voice Back';
    });
})();

// Blind: TTS for global Back button
(function(){
    if (!('speechSynthesis' in window)) return;
    const backBtn = document.getElementById('globalBackBtn');
    if (!backBtn) return;
    backBtn.addEventListener('focus', () => {
        const utter = new SpeechSynthesisUtterance("Go back button");
        utter.rate = 1;
        window.speechSynthesis.speak(utter);
    });
})();

// Simple swipe back for mobile: swipe from left to right
(function() {
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, {passive:true});

    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        if (touchEndX - touchStartX > 80) {
            history.back();
        }
    }, {passive:true});
})();
</script>
</body>
</html>