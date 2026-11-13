/* Random rotation for cards with data-rsb-pool="url|url|..."
   <img data-rsb-pool>   -> random image on each load (the src in the markup is the fallback)
   <video data-rsb-pool> -> random muted loop; poster = same name .jpg; reduced-motion visitors get the poster only */
(function () {
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function pick(list) { return list[Math.floor(Math.random() * list.length)]; }
  var root = (window.Joomla && Joomla.getOptions && (Joomla.getOptions('system.paths') || {}).root) || '';
  function abs(u) { return /^(https?:)?\/\/|^\//.test(u) ? u : root + '/' + u; }
  function run() {
    var els = document.querySelectorAll('[data-rsb-pool]');
    for (var i = 0; i < els.length; i++) {
      var el = els[i];
      var pool = el.getAttribute('data-rsb-pool').split('|').filter(Boolean);
      if (!pool.length) continue;
      var choice = abs(pick(pool));
      if (el.tagName === 'IMG') {
        el.src = choice;
      } else if (el.tagName === 'VIDEO') {
        el.poster = choice.replace(/\.mp4$/, '.jpg');
        if (!reduce) {
          el.muted = true;
          el.src = choice;
          var p = el.play();
          if (p && p.catch) p.catch(function () {});
        }
      }
    }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run); else run();
})();
