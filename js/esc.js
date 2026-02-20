(function(){
   var ua = navigator.userAgent || '';
   var isMac = /Macintosh|Mac OS X|Macintosh; Intel Mac OS X/i.test(ua);
   var escHoldStart = 0;
   var escTimer = null;
   var allowExit = false;
   var THRESHOLD = 8000;
   function enterFs(){
     var el = document.documentElement;
     var fn = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
     try{ fn && fn.call(el); }catch(e){}
   }
   function exitFs(){
     var fn = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
     try{ fn && fn.call(document); }catch(e){}
   }
   try{ if (navigator.keyboard && navigator.keyboard.lock) { navigator.keyboard.lock(); } }catch(e){}
   if(isMac){
     document.addEventListener('keydown', function(e){
       if(e.key === 'Escape' || e.key === 'Esc'){ 
         e.preventDefault();
         e.stopPropagation();
         if(!escHoldStart){
           escHoldStart = Date.now();
           if(escTimer){ clearTimeout(escTimer); }
           escTimer = setTimeout(function(){ allowExit = true; exitFs(); }, THRESHOLD);
         }
       }
     }, true);
     document.addEventListener('keyup', function(e){
       if(e.key === 'Escape' || e.key === 'Esc'){
         e.preventDefault();
         e.stopPropagation();
         if(escTimer){ clearTimeout(escTimer); escTimer = null; }
         var held = escHoldStart ? (Date.now() - escHoldStart) : 0;
         escHoldStart = 0;
         if(held >= THRESHOLD){ allowExit = true; exitFs(); }
         else { allowExit = false; if(!document.fullscreenElement){ enterFs(); } }
       }
     }, true);
     document.addEventListener('fullscreenchange', function(){
       if(!document.fullscreenElement && !allowExit){ enterFs(); }
       else if(document.fullscreenElement){ allowExit = false; }
     });
     document.addEventListener('webkitfullscreenchange', function(){
       if(!(document.fullscreenElement || document.webkitFullscreenElement) && !allowExit){ enterFs(); }
       else if(document.fullscreenElement || document.webkitFullscreenElement){ allowExit = false; }
     });
   } else {
     document.addEventListener('keydown', function(e){
       if(e.key === 'Escape' || e.key === 'Esc'){ }
     }, true);
   }
 })();