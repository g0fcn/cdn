/*! Service Worker script for Wordpress Luxeritas Theme */
try{"serviceWorker"in navigator&&window.addEventListener("load",function(){navigator.serviceWorker.register("https://xkj.93665.xin/luxe-serviceworker.js").then(function(e){console.log("PWA: service worker registered"),e.update()}).catch(function(e){console.log("PWA: registration failed with "+e)})
var pwa_install_event=function(e){console.log("PWA: beforeinstallprompt Event prevented"),e.preventDefault(),!1}
window.addEventListener("beforeinstallprompt",pwa_install_event,!1);})}catch(e){}