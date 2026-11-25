/* raymondsbrowniii.com: From the Walk strip + gallery lightbox (versioned, see css/walk.css) */
/* From the Walk strip: Previous / Next step through the entries listed in data-rsb-walk (newest first) */
(function () {
  function init(el) {
    var data;
    try { data = JSON.parse(el.getAttribute('data-rsb-walk')); } catch (e) { return; }
    if (!data || !data.length) return;
    var i = 0;
    var link = el.querySelector('.rsb-walk__entry'), text = el.querySelector('.rsb-walk__text'), date = el.querySelector('.rsb-walk__date');
    var btns = el.querySelectorAll('.rsb-walk__btn');
    function show() {
      var d = data[i];
      link.href = d.link; text.textContent = d.text; date.textContent = d.date; date.setAttribute('datetime', d.iso);
      for (var b = 0; b < btns.length; b++) { var n = i + Number(btns[b].getAttribute('data-dir')); btns[b].disabled = n < 0 || n >= data.length; }
    }
    for (var b = 0; b < btns.length; b++) {
      btns[b].addEventListener('click', function () { var n = i + Number(this.getAttribute('data-dir')); if (n >= 0 && n < data.length) { i = n; show(); } });
    }
    show();
  }
  function run() { var els = document.querySelectorAll('[data-rsb-walk]'); for (var k = 0; k < els.length; k++) init(els[k]); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run); else run();
})();

/* Walk gallery photos open full size in a simple lightbox (falls back to the plain image link) */
document.addEventListener('click', function (e) {
  var a = e.target.closest && e.target.closest('.rsb-walk-photos a');
  if (!a || !window.HTMLDialogElement) return;
  e.preventDefault();
  var d = document.getElementById('rsb-lightbox');
  if (!d) {
    d = document.createElement('dialog'); d.id = 'rsb-lightbox'; d.className = 'rsb-lightbox';
    d.innerHTML = '<img alt=""><button type="button">Close</button>';
    document.body.appendChild(d);
    d.addEventListener('click', function () { d.close(); });
  }
  var thumb = a.querySelector('img');
  d.querySelector('img').src = a.href; d.querySelector('img').alt = thumb ? thumb.alt : '';
  d.showModal();
});
