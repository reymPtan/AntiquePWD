(function(){
  if (!('speechSynthesis' in window)) return;
  function speak(text) {
    var msg = new SpeechSynthesisUtterance(text);
    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(msg);
  }
  document.addEventListener('click', function(e){
    var target = e.target.closest('[data-voice-label]');
    if (!target) return;
    var label = target.getAttribute('data-voice-label');
    if (label) speak(label);
  });
})();
