/* raymondsbrowniii.com homepage hero: reads JSON from [data-rsb-hx] data-rsb-hx-data (module 139). Bump ?v= in Helix before_body after editing. */
(function(){function init(){
var root=document.querySelector('[data-rsb-hx]');if(!root||root.dataset.ready)return;root.dataset.ready='1';
var D,raw=root.getAttribute('data-rsb-hx-data'),el=root.querySelector('.rsb-hx__data');try{D=JSON.parse(raw||(el&&el.textContent)||'')}catch(e){return}
var P=D.pieces||[],N=D.now||[],secs=D.cycle||5,reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
var words=[].slice.call(root.querySelectorAll('.rsb-hx__w')),item=root.querySelector('.rsb-hx__item');
function esc(s){return String(s==null?'':s).replace(/[&<>"]/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]})}
var n=0;
function setNow(){var x=N[n];if(!x)return;var txt=innerWidth<768&&x.short?x.short:x.text;item.classList.remove('is-in');void item.offsetWidth;item.innerHTML='<span style="color:'+esc(x.color)+'">'+esc(x.tag)+'</span> '+esc(txt);item.classList.add('is-in')}
setNow();if(N.length>1)setInterval(function(){n=(n+1)%N.length;setNow()},4000);
var host=document.querySelector('[data-rsb-hx-strip]');if(!host)return;
var A='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>',B='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>';
host.innerHTML='<div class="rsb-hx-strip__head"><p>Latest work</p><div class="rsb-hx-strip__nav"><button type="button" data-d="-1" aria-label="Previous">'+A+'</button><button type="button" data-d="1" aria-label="Next">'+B+'</button></div></div><div class="rsb-hx-strip__track">'+P.map(function(p,k){return '<a class="rsb-hx__s'+(k?'':' is-on')+'" href="'+esc(p.href)+'" style="--c:'+esc(p.color)+'"><span class="rsb-hx__media" style="--bg:'+esc(p.bg||'#1B1A17')+';--fit:'+esc(p.fit||'cover')+';--pos:'+esc(p.pos||'center')+'"><img src="'+esc(p.img)+'" alt="'+esc(p.alt)+'" loading="lazy"><span class="rsb-hx__bar"></span><span class="rsb-hx__site">'+esc(p.site)+'</span></span><span class="rsb-hx__cap"><span class="rsb-hx__k">'+esc(p.kicker)+'</span><span class="rsb-hx__t">'+esc(p.title)+'</span><span class="rsb-hx__m">'+esc(p.meta)+'</span></span></a>'}).join('')+'</div>';
var track=host.querySelector('.rsb-hx-strip__track'),slides=[].slice.call(track.children),bars=slides.map(function(s){return s.querySelector('.rsb-hx__bar')}),nav=[].slice.call(host.querySelectorAll('.rsb-hx-strip__nav button'));
var audio=null;
var PLAY='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>',PAUSE='<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="6" y="5" width="4" height="14"/><rect x="14" y="5" width="4" height="14"/></svg>';
P.forEach(function(p,k){if(!p.audio)return;audio=new Audio();audio.preload='none';audio.src=p.audio;var b=document.createElement('button');b.type='button';b.className='rsb-hx__play';b.innerHTML=PLAY;b.setAttribute('aria-label','Play '+p.title);slides[k].querySelector('.rsb-hx__media').appendChild(b);
 b.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();show(k);if(audio.paused)audio.play().catch(function(){});else audio.pause()});
 audio.addEventListener('play',function(){b.innerHTML=PAUSE;b.setAttribute('aria-label','Pause '+p.title)});
 audio.addEventListener('pause',function(){b.innerHTML=PLAY;b.setAttribute('aria-label','Play '+p.title)});});
var cur=0,t=0,hold=false;
function step(){return slides[1]?slides[1].offsetLeft-slides[0].offsetLeft:track.clientWidth}
function navState(){var max=track.scrollWidth-track.clientWidth-2;nav[0].disabled=track.scrollLeft<=2;nav[1].disabled=track.scrollLeft>=max}
function reveal(i){var s=slides[i],l=s.offsetLeft-slides[0].offsetLeft;if(l<track.scrollLeft||l+s.offsetWidth>track.scrollLeft+track.clientWidth+2)track.scrollTo({left:Math.min(l,track.scrollWidth-track.clientWidth)})}
function show(i,noScroll){cur=i;t=0;
 words.forEach(function(w,k){var on=k===i;w.classList.toggle('is-on',on);w.setAttribute('aria-pressed',on);w.querySelector('b').style.color=on?P[k].color:''});
 slides.forEach(function(s,k){s.classList.toggle('is-on',k===i)});
 bars.forEach(function(b){b.style.width='0'});
 if(audio&&!audio.paused&&!P[i].audio)audio.pause();
 if(!noScroll)reveal(i);}
words.forEach(function(w,k){w.setAttribute('aria-pressed',k===0);
 w.addEventListener('mouseenter',function(){hold=true;show(k)});
 w.addEventListener('mouseleave',function(){hold=false});
 w.addEventListener('focus',function(){hold=true;show(k)});
 w.addEventListener('blur',function(){hold=false});
 w.addEventListener('click',function(){show(k)});});
slides.forEach(function(s,k){s.addEventListener('mouseenter',function(){hold=true;show(k,true)});s.addEventListener('mouseleave',function(){hold=false});s.addEventListener('focus',function(){show(k,true)})});
nav.forEach(function(b){b.addEventListener('click',function(){track.scrollBy({left:step()*+b.dataset.d})})});
var st;track.addEventListener('scroll',function(){clearTimeout(st);st=setTimeout(navState,80)},{passive:true});
var tt;track.addEventListener('touchstart',function(){hold=true;clearTimeout(tt)},{passive:true});
track.addEventListener('touchend',function(){clearTimeout(tt);tt=setTimeout(function(){hold=false;var k=Math.max(0,Math.min(P.length-1,Math.round(track.scrollLeft/step())));if(k!==cur)show(k,true)},600)},{passive:true});
navState();addEventListener('resize',navState);
var vis=true;if('IntersectionObserver' in window){new IntersectionObserver(function(e){vis=e[0].isIntersecting}).observe(track)}
setInterval(function(){
 if(reduce||hold||!vis||(audio&&!audio.paused)||document.hidden)return;
 t+=100/(secs*10);if(bars[cur])bars[cur].style.width=Math.min(t,100)+'%';
 if(t>=100)show((cur+1)%P.length);
},100);
}if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();})();
