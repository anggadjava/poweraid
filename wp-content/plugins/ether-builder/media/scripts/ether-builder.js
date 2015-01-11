var ether = ether || {};

var ua = navigator.userAgent.toLowerCase();
var isie = /msie/.test(ua);
var iev = parseFloat((ua.match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]);

ether.ua = ether.ua || ua;
ether.isie = ether.isie || isie;
ether.iev = ether.iev || iev;
ether.prefix = ether.prefix || 'ether';

! window.ether ? window.ether = ether : '';


(function($)
{


	
	jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(e,a,c,b,d){return jQuery.easing[jQuery.easing.def](e,a,c,b,d)},easeInQuad:function(e,a,c,b,d){return b*(a/=d)*a+c},easeOutQuad:function(e,a,c,b,d){return-b*(a/=d)*(a-2)+c},easeInOutQuad:function(e,a,c,b,d){if((a/=d/2)<1)return b/2*a*a+c;return-b/2*(--a*(a-2)-1)+c},easeInCubic:function(e,a,c,b,d){return b*(a/=d)*a*a+c},easeOutCubic:function(e,a,c,b,d){return b*((a=a/d-1)*a*a+1)+c},easeInOutCubic:function(e,a,c,b,d){if((a/=d/2)<1)return b/2*a*a*a+c;return b/2*((a-=2)*a*a+2)+c},easeInQuart:function(e,a,c,b,d){return b*(a/=d)*a*a*a+c},easeOutQuart:function(e,a,c,b,d){return-b*((a=a/d-1)*a*a*a-1)+c},easeInOutQuart:function(e,a,c,b,d){if((a/=d/2)<1)return b/2*a*a*a*a+c;return-b/2*((a-=2)*a*a*a-2)+c},easeInQuint:function(e,a,c,b,d){return b*(a/=d)*a*a*a*a+c},easeOutQuint:function(e,a,c,b,d){return b*((a=a/d-1)*a*a*a*a+1)+c},easeInOutQuint:function(e,a,c,b,d){if((a/=d/2)<1)return b/2*a*a*a*a*a+c;return b/2*((a-=2)*a*a*a*a+2)+c},easeInSine:function(e,
	a,c,b,d){return-b*Math.cos(a/d*(Math.PI/2))+b+c},easeOutSine:function(e,a,c,b,d){return b*Math.sin(a/d*(Math.PI/2))+c},easeInOutSine:function(e,a,c,b,d){return-b/2*(Math.cos(Math.PI*a/d)-1)+c},easeInExpo:function(e,a,c,b,d){return a==0?c:b*Math.pow(2,10*(a/d-1))+c},easeOutExpo:function(e,a,c,b,d){return a==d?c+b:b*(-Math.pow(2,-10*a/d)+1)+c},easeInOutExpo:function(e,a,c,b,d){if(a==0)return c;if(a==d)return c+b;if((a/=d/2)<1)return b/2*Math.pow(2,10*(a-1))+c;return b/2*(-Math.pow(2,-10*--a)+2)+c},

	easeInCirc:function(e,a,c,b,d){return-b*(Math.sqrt(1-(a/=d)*a)-1)+c},easeOutCirc:function(e,a,c,b,d){return b*Math.sqrt(1-(a=a/d-1)*a)+c},easeInOutCirc:function(e,a,c,b,d){if((a/=d/2)<1)return-b/2*(Math.sqrt(1-a*a)-1)+c;return b/2*(Math.sqrt(1-(a-=2)*a)+1)+c},easeInElastic:function(e,a,c,b,d){e=1.70158;var f=0,g=b;if(a==0)return c;if((a/=d)==1)return c+b;f||(f=d*0.3);if(g<Math.abs(b)){g=b;e=f/4}else e=f/(2*Math.PI)*Math.asin(b/g);return-(g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f))+c},easeOutElastic:function(e,
	a,c,b,d){e=1.70158;var f=0,g=b;if(a==0)return c;if((a/=d)==1)return c+b;f||(f=d*0.3);if(g<Math.abs(b)){g=b;e=f/4}else e=f/(2*Math.PI)*Math.asin(b/g);return g*Math.pow(2,-10*a)*Math.sin((a*d-e)*2*Math.PI/f)+b+c},easeInOutElastic:function(e,a,c,b,d){e=1.70158;var f=0,g=b;if(a==0)return c;if((a/=d/2)==2)return c+b;f||(f=d*0.3*1.5);if(g<Math.abs(b)){g=b;e=f/4}else e=f/(2*Math.PI)*Math.asin(b/g);if(a<1)return-0.5*g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f)+c;return g*Math.pow(2,-10*(a-=1))*Math.sin((a*
	d-e)*2*Math.PI/f)*0.5+b+c},easeInBack:function(e,a,c,b,d,f){if(f==undefined)f=1.70158;return b*(a/=d)*a*((f+1)*a-f)+c},easeOutBack:function(e,a,c,b,d,f){if(f==undefined)f=1.70158;return b*((a=a/d-1)*a*((f+1)*a+f)+1)+c},easeInOutBack:function(e,a,c,b,d,f){if(f==undefined)f=1.70158;if((a/=d/2)<1)return b/2*a*a*(((f*=1.525)+1)*a-f)+c;return b/2*((a-=2)*a*(((f*=1.525)+1)*a+f)+2)+c},easeInBounce:function(e,a,c,b,d){return b-jQuery.easing.easeOutBounce(e,d-a,0,b,d)+c},easeOutBounce:function(e,a,c,b,d){return(a/=
	d)<1/2.75?b*7.5625*a*a+c:a<2/2.75?b*(7.5625*(a-=1.5/2.75)*a+0.75)+c:a<2.5/2.75?b*(7.5625*(a-=2.25/2.75)*a+0.9375)+c:b*(7.5625*(a-=2.625/2.75)*a+0.984375)+c},easeInOutBounce:function(e,a,c,b,d){if(a<d/2)return jQuery.easing.easeInBounce(e,a*2,0,b,d)*0.5+c;return jQuery.easing.easeOutBounce(e,a*2-d,0,b,d)*0.5+b*0.5+c}});

})(jQuery);

(function($)
{
	if(jQuery.mousewheel)
		return;
	
	(function(d){var b=["DOMMouseScroll","mousewheel"];if(d.event.fixHooks){for(var a=b.length;a;){d.event.fixHooks[b[--a]]=d.event.mouseHooks}}d.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var e=b.length;e;){this.addEventListener(b[--e],c,false)}}else{this.onmousewheel=c}},teardown:function(){if(this.removeEventListener){for(var e=b.length;e;){this.removeEventListener(b[--e],c,false)}}else{this.onmousewheel=null}}};d.fn.extend({mousewheel:function(e){return e?this.on("mousewheel",e):this.trigger("mousewheel")},unmousewheel:function(e){return this.unbind("mousewheel",e)}});function c(j){var h=j||window.event,g=[].slice.call(arguments,1),k=0,i=true,f=0,e=0;j=d.event.fix(h);j.type="mousewheel";if(h.wheelDelta){k=h.wheelDelta/120}if(h.detail){k=-h.detail/3}e=k;if(h.axis!==undefined&&h.axis===h.HORIZONTAL_AXIS){e=0;f=-1*k}if(h.wheelDeltaY!==undefined){e=h.wheelDeltaY/120}if(h.wheelDeltaX!==undefined){f=-1*h.wheelDeltaX/120}g.unshift(j,k,f,e);return(d.event.dispatch||d.event.handle).apply(this,g)}})(jQuery);
})(jQuery);

(function($)
{
	if ( isie && iev < 9 || jQuery.swipe)
		return;
	
	(function(e){var o="left",n="right",d="up",v="down",c="in",w="out",l="none",r="auto",k="swipe",s="pinch",x="tap",i="doubletap",b="longtap",A="horizontal",t="vertical",h="all",q=10,f="start",j="move",g="end",p="cancel",a="ontouchstart" in window,y="TouchSwipe";var m={fingers:1,threshold:75,cancelThreshold:null,pinchThreshold:20,maxTimeThreshold:null,fingerReleaseThreshold:250,longTapThreshold:500,doubleTapThreshold:200,swipe:null,swipeLeft:null,swipeRight:null,swipeUp:null,swipeDown:null,swipeStatus:null,pinchIn:null,pinchOut:null,pinchStatus:null,click:null,tap:null,doubleTap:null,longTap:null,triggerOnTouchEnd:true,triggerOnTouchLeave:false,allowPageScroll:"auto",fallbackToMouseEvents:true,excludedElements:"button, input, select, textarea, a, .noSwipe"};e.fn.swipe=function(D){var C=e(this),B=C.data(y);if(B&&typeof D==="string"){if(B[D]){return B[D].apply(this,Array.prototype.slice.call(arguments,1))}else{e.error("Method "+D+" does not exist on jQuery.swipe")}}else{if(!B&&(typeof D==="object"||!D)){return u.apply(this,arguments)}}return C};e.fn.swipe.defaults=m;e.fn.swipe.phases={PHASE_START:f,PHASE_MOVE:j,PHASE_END:g,PHASE_CANCEL:p};e.fn.swipe.directions={LEFT:o,RIGHT:n,UP:d,DOWN:v,IN:c,OUT:w};e.fn.swipe.pageScroll={NONE:l,HORIZONTAL:A,VERTICAL:t,AUTO:r};e.fn.swipe.fingers={ONE:1,TWO:2,THREE:3,ALL:h};function u(B){if(B&&(B.allowPageScroll===undefined&&(B.swipe!==undefined||B.swipeStatus!==undefined))){B.allowPageScroll=l}if(B.click!==undefined&&B.tap===undefined){B.tap=B.click}if(!B){B={}}B=e.extend({},e.fn.swipe.defaults,B);return this.each(function(){var D=e(this);var C=D.data(y);if(!C){C=new z(this,B);D.data(y,C)}})}function z(a0,aq){var av=(a||!aq.fallbackToMouseEvents),G=av?"touchstart":"mousedown",au=av?"touchmove":"mousemove",R=av?"touchend":"mouseup",P=av?null:"mouseleave",az="touchcancel";var ac=0,aL=null,Y=0,aX=0,aV=0,D=1,am=0,aF=0,J=null;var aN=e(a0);var W="start";var T=0;var aM=null;var Q=0,aY=0,a1=0,aa=0,K=0;var aS=null;try{aN.on(G,aJ);aN.on(az,a5)}catch(ag){e.error("events not supported "+G+","+az+" on jQuery.swipe")}this.enable=function(){aN.on(G,aJ);aN.on(az,a5);return aN};this.disable=function(){aG();return aN};this.destroy=function(){aG();aN.data(y,null);return aN};this.option=function(a8,a7){if(aq[a8]!==undefined){if(a7===undefined){return aq[a8]}else{aq[a8]=a7}}else{e.error("Option "+a8+" does not exist on jQuery.swipe.options")}};function aJ(a9){if(ax()){return}if(e(a9.target).closest(aq.excludedElements,aN).length>0){return}var ba=a9.originalEvent?a9.originalEvent:a9;var a8,a7=a?ba.touches[0]:ba;W=f;if(a){T=ba.touches.length}else{a9.preventDefault()}ac=0;aL=null;aF=null;Y=0;aX=0;aV=0;D=1;am=0;aM=af();J=X();O();if(!a||(T===aq.fingers||aq.fingers===h)||aT()){ae(0,a7);Q=ao();if(T==2){ae(1,ba.touches[1]);aX=aV=ap(aM[0].start,aM[1].start)}if(aq.swipeStatus||aq.pinchStatus){a8=L(ba,W)}}else{a8=false}if(a8===false){W=p;L(ba,W);return a8}else{ak(true)}}function aZ(ba){var bd=ba.originalEvent?ba.originalEvent:ba;if(W===g||W===p||ai()){return}var a9,a8=a?bd.touches[0]:bd;var bb=aD(a8);aY=ao();if(a){T=bd.touches.length}W=j;if(T==2){if(aX==0){ae(1,bd.touches[1]);aX=aV=ap(aM[0].start,aM[1].start)}else{aD(bd.touches[1]);aV=ap(aM[0].end,aM[1].end);aF=an(aM[0].end,aM[1].end)}D=a3(aX,aV);am=Math.abs(aX-aV)}if((T===aq.fingers||aq.fingers===h)||!a||aT()){aL=aH(bb.start,bb.end);ah(ba,aL);ac=aO(bb.start,bb.end);Y=aI();aE(aL,ac);if(aq.swipeStatus||aq.pinchStatus){a9=L(bd,W)}if(!aq.triggerOnTouchEnd||aq.triggerOnTouchLeave){var a7=true;if(aq.triggerOnTouchLeave){var bc=aU(this);a7=B(bb.end,bc)}if(!aq.triggerOnTouchEnd&&a7){W=ay(j)}else{if(aq.triggerOnTouchLeave&&!a7){W=ay(g)}}if(W==p||W==g){L(bd,W)}}}else{W=p;L(bd,W)}if(a9===false){W=p;L(bd,W)}}function I(a7){var a8=a7.originalEvent;if(a){if(a8.touches.length>0){C();return true}}if(ai()){T=aa}a7.preventDefault();aY=ao();Y=aI();if(a6()){W=p;L(a8,W)}else{if(aq.triggerOnTouchEnd||(aq.triggerOnTouchEnd==false&&W===j)){W=g;L(a8,W)}else{if(!aq.triggerOnTouchEnd&&a2()){W=g;aB(a8,W,x)}else{if(W===j){W=p;L(a8,W)}}}}ak(false)}function a5(){T=0;aY=0;Q=0;aX=0;aV=0;D=1;O();ak(false)}function H(a7){var a8=a7.originalEvent;if(aq.triggerOnTouchLeave){W=ay(g);L(a8,W)}}function aG(){aN.unbind(G,aJ);aN.unbind(az,a5);aN.unbind(au,aZ);aN.unbind(R,I);if(P){aN.unbind(P,H)}ak(false)}function ay(bb){var ba=bb;var a9=aw();var a8=aj();var a7=a6();if(!a9||a7){ba=p}else{if(a8&&bb==j&&(!aq.triggerOnTouchEnd||aq.triggerOnTouchLeave)){ba=g}else{if(!a8&&bb==g&&aq.triggerOnTouchLeave){ba=p}}}return ba}function L(a9,a7){var a8=undefined;if(F()||S()){a8=aB(a9,a7,k)}else{if((M()||aT())&&a8!==false){a8=aB(a9,a7,s)}}if(aC()&&a8!==false){a8=aB(a9,a7,i)}else{if(al()&&a8!==false){a8=aB(a9,a7,b)}else{if(ad()&&a8!==false){a8=aB(a9,a7,x)}}}if(a7===p){a5(a9)}if(a7===g){if(a){if(a9.touches.length==0){a5(a9)}}else{a5(a9)}}return a8}function aB(ba,a7,a9){var a8=undefined;if(a9==k){aN.trigger("swipeStatus",[a7,aL||null,ac||0,Y||0,T]);if(aq.swipeStatus){a8=aq.swipeStatus.call(aN,ba,a7,aL||null,ac||0,Y||0,T);if(a8===false){return false}}if(a7==g&&aR()){aN.trigger("swipe",[aL,ac,Y,T]);if(aq.swipe){a8=aq.swipe.call(aN,ba,aL,ac,Y,T);if(a8===false){return false}}switch(aL){case o:aN.trigger("swipeLeft",[aL,ac,Y,T]);if(aq.swipeLeft){a8=aq.swipeLeft.call(aN,ba,aL,ac,Y,T)}break;case n:aN.trigger("swipeRight",[aL,ac,Y,T]);if(aq.swipeRight){a8=aq.swipeRight.call(aN,ba,aL,ac,Y,T)}break;case d:aN.trigger("swipeUp",[aL,ac,Y,T]);if(aq.swipeUp){a8=aq.swipeUp.call(aN,ba,aL,ac,Y,T)}break;case v:aN.trigger("swipeDown",[aL,ac,Y,T]);if(aq.swipeDown){a8=aq.swipeDown.call(aN,ba,aL,ac,Y,T)}break}}}if(a9==s){aN.trigger("pinchStatus",[a7,aF||null,am||0,Y||0,T,D]);if(aq.pinchStatus){a8=aq.pinchStatus.call(aN,ba,a7,aF||null,am||0,Y||0,T,D);if(a8===false){return false}}if(a7==g&&a4()){switch(aF){case c:aN.trigger("pinchIn",[aF||null,am||0,Y||0,T,D]);if(aq.pinchIn){a8=aq.pinchIn.call(aN,ba,aF||null,am||0,Y||0,T,D)}break;case w:aN.trigger("pinchOut",[aF||null,am||0,Y||0,T,D]);if(aq.pinchOut){a8=aq.pinchOut.call(aN,ba,aF||null,am||0,Y||0,T,D)}break}}}if(a9==x){if(a7===p||a7===g){clearTimeout(aS);if(V()&&!E()){K=ao();aS=setTimeout(e.proxy(function(){K=null;aN.trigger("tap",[ba.target]);if(aq.tap){a8=aq.tap.call(aN,ba,ba.target)}},this),aq.doubleTapThreshold)}else{K=null;aN.trigger("tap",[ba.target]);if(aq.tap){a8=aq.tap.call(aN,ba,ba.target)}}}}else{if(a9==i){if(a7===p||a7===g){clearTimeout(aS);K=null;aN.trigger("doubletap",[ba.target]);if(aq.doubleTap){a8=aq.doubleTap.call(aN,ba,ba.target)}}}else{if(a9==b){if(a7===p||a7===g){clearTimeout(aS);K=null;aN.trigger("longtap",[ba.target]);if(aq.longTap){a8=aq.longTap.call(aN,ba,ba.target)}}}}}return a8}function aj(){var a7=true;if(aq.threshold!==null){a7=ac>=aq.threshold}return a7}function a6(){var a7=false;if(aq.cancelThreshold!==null&&aL!==null){a7=(aP(aL)-ac)>=aq.cancelThreshold}return a7}function ab(){if(aq.pinchThreshold!==null){return am>=aq.pinchThreshold}return true}function aw(){var a7;if(aq.maxTimeThreshold){if(Y>=aq.maxTimeThreshold){a7=false}else{a7=true}}else{a7=true}return a7}function ah(a7,a8){if(aq.allowPageScroll===l||aT()){a7.preventDefault()}else{var a9=aq.allowPageScroll===r;switch(a8){case o:if((aq.swipeLeft&&a9)||(!a9&&aq.allowPageScroll!=A)){a7.preventDefault()}break;case n:if((aq.swipeRight&&a9)||(!a9&&aq.allowPageScroll!=A)){a7.preventDefault()}break;case d:if((aq.swipeUp&&a9)||(!a9&&aq.allowPageScroll!=t)){a7.preventDefault()}break;case v:if((aq.swipeDown&&a9)||(!a9&&aq.allowPageScroll!=t)){a7.preventDefault()}break}}}function a4(){var a8=aK();var a7=U();var a9=ab();return a8&&a7&&a9}function aT(){return !!(aq.pinchStatus||aq.pinchIn||aq.pinchOut)}function M(){return !!(a4()&&aT())}function aR(){var ba=aw();var bc=aj();var a9=aK();var a7=U();var a8=a6();var bb=!a8&&a7&&a9&&bc&&ba;return bb}function S(){return !!(aq.swipe||aq.swipeStatus||aq.swipeLeft||aq.swipeRight||aq.swipeUp||aq.swipeDown)}function F(){return !!(aR()&&S())}function aK(){return((T===aq.fingers||aq.fingers===h)||!a)}function U(){return aM[0].end.x!==0}function a2(){return !!(aq.tap)}function V(){return !!(aq.doubleTap)}function aQ(){return !!(aq.longTap)}function N(){if(K==null){return false}var a7=ao();return(V()&&((a7-K)<=aq.doubleTapThreshold))}function E(){return N()}function at(){return((T===1||!a)&&(isNaN(ac)||ac===0))}function aW(){return((Y>aq.longTapThreshold)&&(ac<q))}function ad(){return !!(at()&&a2())}function aC(){return !!(N()&&V())}function al(){return !!(aW()&&aQ())}function C(){a1=ao();aa=event.touches.length+1}function O(){a1=0;aa=0}function ai(){var a7=false;if(a1){var a8=ao()-a1;if(a8<=aq.fingerReleaseThreshold){a7=true}}return a7}function ax(){return !!(aN.data(y+"_intouch")===true)}function ak(a7){if(a7===true){aN.on(au,aZ);aN.on(R,I);if(P){aN.on(P,H)}}else{aN.unbind(au,aZ,false);aN.unbind(R,I,false);if(P){aN.unbind(P,H,false)}}aN.data(y+"_intouch",a7===true)}function ae(a8,a7){var a9=a7.identifier!==undefined?a7.identifier:0;aM[a8].identifier=a9;aM[a8].start.x=aM[a8].end.x=a7.pageX||a7.clientX;aM[a8].start.y=aM[a8].end.y=a7.pageY||a7.clientY;return aM[a8]}function aD(a7){var a9=a7.identifier!==undefined?a7.identifier:0;var a8=Z(a9);a8.end.x=a7.pageX||a7.clientX;a8.end.y=a7.pageY||a7.clientY;return a8}function Z(a8){for(var a7=0;a7<aM.length;a7++){if(aM[a7].identifier==a8){return aM[a7]}}}function af(){var a7=[];for(var a8=0;a8<=5;a8++){a7.push({start:{x:0,y:0},end:{x:0,y:0},identifier:0})}return a7}function aE(a7,a8){a8=Math.max(a8,aP(a7));J[a7].distance=a8}function aP(a7){return J[a7].distance}function X(){var a7={};a7[o]=ar(o);a7[n]=ar(n);a7[d]=ar(d);a7[v]=ar(v);return a7}function ar(a7){return{direction:a7,distance:0}}function aI(){return aY-Q}function ap(ba,a9){var a8=Math.abs(ba.x-a9.x);var a7=Math.abs(ba.y-a9.y);return Math.round(Math.sqrt(a8*a8+a7*a7))}function a3(a7,a8){var a9=(a8/a7)*1;return a9.toFixed(2)}function an(){if(D<1){return w}else{return c}}function aO(a8,a7){return Math.round(Math.sqrt(Math.pow(a7.x-a8.x,2)+Math.pow(a7.y-a8.y,2)))}function aA(ba,a8){var a7=ba.x-a8.x;var bc=a8.y-ba.y;var a9=Math.atan2(bc,a7);var bb=Math.round(a9*180/Math.PI);if(bb<0){bb=360-Math.abs(bb)}return bb}function aH(a8,a7){var a9=aA(a8,a7);if((a9<=45)&&(a9>=0)){return o}else{if((a9<=360)&&(a9>=315)){return o}else{if((a9>=135)&&(a9<=225)){return n}else{if((a9>45)&&(a9<135)){return v}else{return d}}}}}function ao(){var a7=new Date();return a7.getTime()}function aU(a7){a7=e(a7);var a9=a7.offset();var a8={left:a9.left,right:a9.left+a7.outerWidth(),top:a9.top,bottom:a9.top+a7.outerHeight()};return a8}function B(a7,a8){return(a7.x>a8.left&&a7.x<a8.right&&a7.y>a8.top&&a7.y<a8.bottom)}}})(jQuery);
})(jQuery);

Array.prototype.getRandom = Array.prototype.getRandom || function () {
    return this[Math.floor(this.length * Math.random())];
};


Array.prototype.shuffle = Array.prototype.shuffle || function (o){ //v1.0
	o = o || this;
    for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
},

Array.prototype.forEach = Array.prototype.forEach || function(fun /*, thisArg */)
{
	"use strict";

	if (this === void 0 || this === null)
	
	throw new TypeError();

	var t = Object(this);
	var len = t.length >>> 0;
	
	if (typeof fun !== "function")
		throw new TypeError();

	var thisArg = arguments.length >= 2 ? arguments[1] : void 0;
	
	for (var i = 0; i < len; i++)
	{
		if (i in t)
			fun.call(thisArg, t[i], i, t);
	}
};

Array.isArray = Array.prototype.isArray || function (vArg) 
{
	return Object.prototype.toString.call(vArg) === "[object Array]";
};

(function($)
{
	$.fn.cattr = function(key, value, attribute, explicit_key)
	{


		attribute = attribute || 'className';

		var $object = $(this).eq(0);
		var attr_name = '';
		var attr_found;

		if (key != null)
		{
			var attributes = $object[0][attribute].split(' ');

			attributes.forEach(function (name)
			{
				var attr_data;
				var attr_key;

				if (attr_found)
					return;

				attr_data = name.split('-');

				if (explicit_key)
				{
					attr_key = name.indexOf(key) !== -1 && name.length !== key.length ? key : '';

					if (attr_key == key)
					{
						attr_name = name;
						attr_found = true;
					}
				} else
				{

					attr_key = attr_data[attr_data.length - 2] === '' ? attr_data.slice(0, attr_data.length - 2).join('-') : attr_data.slice(0, attr_data.length - 1).join('-');

					if (attr_key == key)
					{
						attr_name = name;
						attr_found = true;
					}
				}
				
			});
		}

		if (typeof value == 'undefined' || value == null)
		{

			
			return attr_name.substr(key.length+1);
		} else
		{
			if (attr_name != '')
			{
				$object[0][attribute] = $object[0][attribute].replace(attr_name, key + '-' + value);
			} else
			{
				$object[0][attribute] = $object[0][attribute] + ' ' + key + '-' + value;
			}
		}

		return this;
	};
})(jQuery);

(function($)
{
	var _ua = navigator.userAgent.toLowerCase();
	var _isie = /msie/.test(_ua);
	var _iev = parseFloat((_ua.match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]);

	var ua = function ()
	{
		return _ua;
	}

	var isie = function  ()
	{
		return _isie;
	}

	var iev = function  ()
	{
		return _iev;
	}

	var reorder_array = function  (array)
	{
		var result = [];

		array.forEach(function (elem)
		{
			if (elem)
			{
				result.push(elem);
			}
		})

		return result;
	}

	var clean_arr = function  (arr)
	{
		var result = [];

		arr.forEach(function (e)
		{
			e !== null && e !== undefined ? result.push(e) : '';
		});

		return result;
	}

	var shallow_copy = function  (from, to) 
	{
        var key;

        for (key in from) 
        {
            if (from.hasOwnProperty(key)) 
            {
                to[key] = from[key];
            }
        }
    }

	var obj_foreach = function  (thisArg, obj, callback, depth) {
        var key;

        for (key in obj) 
        {
            if (obj.hasOwnProperty(key)) 
            {
                callback.apply(thisArg, [key, obj[key]]);
                
                if (depth && typeof obj[key] === 'object') 
                {
                    obj_foreach(thisArg, obj[key], callback, depth);
                }
            }
        }
    }

    var obj_prop_count = function (obj)
    {
    	var count = 0;

    	obj_foreach(null, obj, function () { count += 1 });

    	return count;
    }

    var prefix = function (name, join)
	{
		var prefix = prefix || (ether ? (ether.prefix || '') : '');

		return (typeof name !== 'string' ? prefix_arr(name, join || '') : (prefix !== '' ? prefix + '-' : '') + name);
	}

	var prefix_arr = function (arr, join)
	{
		var result = '';

		arr.forEach(function (elem)
		{
			result += prefix(elem) + (join || '');
		});

		return result;
	}

	var prefix_class = function  (name, p)
	{
		return '.' + prefix(name);
	}

	var has_col_parent = function  ($elem)
	{
		return $elem.parents('.' + prefix('crumb-wrap')).eq(0).length;
	}

	var get_col_parent = function  ($elem)
	{
		return $elem.parents('.' + prefix('crumb-wrap')).eq(0);
	}

	var get_img_title = function  ($elem)
	{
		return $elem.attr('title') || $elem.attr('alt');
	}

	var is_widget = function  ($elem)
	{
		return $elem.parent().hasClass(prefix('widget'));
	}

	var is_image = function  ($elem)
	{
		return $elem.prop('tagName') === 'IMG';
	}

	var animate_dom = function  ($elem, data, speed)
	{
		speed = (speed !== undefined ? speed : 500);
		$elem.stop(true, true).animate(data, speed);
	}

	var extend_prototype = function  (target, extend)
	{
		obj_foreach(null, extend, function (key, elem)
		{
			target.prototype[key] = extend[key];
		});
	}

	var wrap_dom_groups = function  ($proto_wrap, $elems, capacity)
	{
		var a;
		var count = $elems.length;
		var slice_to;
		var $wrap;

		capacity = capacity || count;

		for (a = 0; a < count; a += capacity)
		{
			$wrap = (typeof $proto_wrap === 'function' ? $proto_wrap() : $proto_wrap.clone(true, true));

			slice_to = a + ((a + capacity < count) ? capacity : count - a);

			$elems.slice(a, slice_to).wrapAll($wrap);
		}
	}

	var scheduler = function ()
	{
		var timeouts = {};

		return {
			clear: function (name)
			{
				clearTimeout(timeouts[name]);
			},

			set: function (name, timeout, fn, this_arg, args)
			{
				clearTimeout(timeouts[name]);
				if (timeout)
				{
					timeouts[name] = setTimeout(function ()
					{
						fn.apply(this_arg, args);
					}, timeout);
				} else
				{
					fn.apply(this_arg, args);
				}
			}
		}
	}();

	var trim = function  (s)
	{
		s = s.replace(/^\s+/, '');
		s = s.replace(/\s+$/, '');

		return s;
	}

	img_get_natural_size = function (src)
	{
	    var newImg = new Image();
	    var s;

	    newImg.src = src;

	    s = {
			height: newImg.height,
			width: newImg.width
		}


	    return s;
	}

	var utils = function ()
	{
		return {
			init_utils: function (data)
			{
				obj_foreach(this, data, function (name, fn)
				{
					! this[name] ? this[name] = fn : '';
				});
			}
		}
	}();

	utils.init_utils({
		ua: ua, 
		isie: isie, 
		iev: iev, 
		reorder_array: reorder_array, 
		clean_arr: clean_arr, 
		shallow_copy: shallow_copy, 
		obj_foreach: obj_foreach, 
		obj_prop_count: obj_prop_count, 
		prefix: prefix, 
		prefix_arr: prefix_arr, 
		prefix_class: prefix_class, 
		has_col_parent: has_col_parent, 
		get_col_parent: get_col_parent, 
		get_img_title: get_img_title, 
		is_widget: is_widget, 
		is_image: is_image, 
		animate_dom: animate_dom, 
		extend_prototype: extend_prototype, 
		wrap_dom_groups: wrap_dom_groups, 
		scheduler: scheduler, 
		trim: trim,
		img_get_natural_size: img_get_natural_size
	});

	ether.utils = ether.utils || utils;
})(jQuery);

(function($)
{
	var image_loader =
	{
		debug: false,
		placeholder_img_data: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjQwNkI5RDRFNjFBQzExRTE5MjJDRjRGMUM2MTdDODUyIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjQwNkI5RDRGNjFBQzExRTE5MjJDRjRGMUM2MTdDODUyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NDA2QjlENEM2MUFDMTFFMTkyMkNGNEYxQzYxN0M4NTIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NDA2QjlENEQ2MUFDMTFFMTkyMkNGNEYxQzYxN0M4NTIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4G1oNaAAAABlBMVEX///8AAABVwtN+AAAADElEQVR42mJgAAgwAAACAAFPbVnhAAAAAElFTkSuQmCC',

		on_image_load_end: function ($img, success_callback, error_callback)
		{
			var self = this;

			$img.each(function ()
			{
				var self = this;

				$(this)
					.on('load', function ()
					{
						success_callback ? success_callback.call(self) : '';
					})
					.on('error', function ()
					{
						$(this)
							.unbind('error')
							.attr('src', self.placeholder_img_data);

						error_callback ? error_callback.call(self) : '';
					});
	
				if ((typeof this.complete != 'undefined' && this.complete) || (typeof this.naturalWidth != 'undefined' && this.naturalWidth > 0))
				{
					$(this)
						.trigger('load')
						.unbind('load');
				}
			});
		},

		on_images_load_end: function ($images, callback)
		{
			var self = this;

			var count = $images.length;
			var all_images_loaded;	
			var count_locked;
			var loaded = 0;
			var error = 0;
			var add_ready = function (success)
			{
				success ? loaded += 1 : error += 1;
				
				self.debug ? console.log('all ' + loaded + ' of ' + count + ' detected images have loaded.' + (error ? ' (' + error + ') links seem to be broken ;(' : '')) : '';

				if ((loaded + error === count && ! count_locked)) //use this if no placeholder is used and broken image is left on image load error
				if ((loaded === count && ! count_locked))
				{
					count_locked = true;
					all_images_loaded = true;

					callback ? callback() : '';
				}
			}

			if (count > 0)
			{
				this.on_image_load_end($images, function ()
				{
					add_ready(true);
				}, function ()
				{
					add_ready(false);
				});
			} else
			{
				callback ? callback() : '';
			}
		},

		preload_image_callback: function (url, callback)
		{
			var img = new Image();

			if (callback)
			{
				img.onload = function () 
				{
					callback.call(null, img);
				}
			}

			img.src = url;

			return img;
		}
	}

	ether.image_loader = ether.image_loader || image_loader;
})(jQuery);

(function($)
 {
	var utils = ether.utils;

	var css_generator =
	{	
		style_destination: (isie && iev < 9 ? 'body' : 'head'),
		styles_data: {
			all: {},
			ie7: {}
		},
		raw_styles: '',

		css: '',

		add_raw_styles: function (string)
		{
			if (this.raw_styles.indexOf(string) === -1)
			{
				this.raw_styles += (' ' + string);
			}
		},

		add_style_data: function (container, selector_name, rules)
		{
			selector_name = (typeof selector_name !== 'string' ? selector_name.join('') : selector_name);
			
			container = (typeof container === 'string' ? (this.styles_data[container] ? this.styles_data[container] : {}) : container);
			container[selector_name] = container[selector_name] || {};


			utils.obj_foreach(this, rules, function (key, elem)
			{
				container[selector_name][key] = elem;
			});

			return container;				
		},

		generate_ruleset: function (obj)
		{
			var result = '';
	
			utils.obj_foreach(null, obj, function (selector, elem)
			{
				var prop;
				
				result += selector + ' { \n';

				for(prop in elem)
				{
					result += '	' + prop + ': ' + elem[prop] + '; \n';
				}

				result += '} \n';
			});
	
			return result;
		},

		generate_styles: function ()
		{
			var cfg = this.cfg;
			var result = '';

			utils.obj_foreach(this, this.styles_data, function (key, elem)
			{
				result += this.generate_ruleset(elem)
			});

			result += this.raw_styles;

				
			return result;
		},

		update_css: function ()
		{
			var name = 'stylesheet';
			var selector = utils.prefix_class(name);

			this.css = this.generate_styles();


			if (this.css && this.css !== '')
			{
				$(this.style_destination)
					.find(selector).remove().end()
					.append('<style class="' + utils.prefix(name) + '">' + this.css + '</style>');
			}

		}		
	}

	ether.css_generator = ether.css_generator || css_generator;
})(jQuery);

(function ($)
{
	var utils = ether.utils;

	var event_manager =
	{
		debug: false, 

		on: function ($trigger, e, callback)
		{
			e = e.split(' ');
			$trigger = $trigger && $trigger.length ? $trigger : $(document);

			e.forEach(function (evt)
			{
				this.debug ? console.log('register event', evt, $trigger.length, $trigger[0] instanceof Window ? 'WINDOW' : $trigger[0] instanceof Document ? 'DOCUMENT' : $trigger.attr('class') ? $trigger.attr('class').substr(0, 10) : $trigger[0].nodeName) : '';

				$trigger.on(evt, callback);
			}, this);

			return this;
		},

		trigger: function ($trigger, e, args)
		{
			$trigger = $trigger && $trigger.length ? $trigger : $(document);
			! Array.isArray(args) ? args = [args] : '';

			this.debug ? console.log('trigger event', e, $trigger.length, ($trigger.attr('class') || 'empty').substr(0, 10)) : '';

			$trigger.trigger(e, args);

			return this;
		}
	}

	ether.event_manager = ether.event_manager || event_manager;

})(jQuery);


(function ($)
{
	var utils = ether.utils;
	var prefix = utils.prefix;

	var widget_manager = {
		widget: {},

		get_next_id: function (namespace)
		{
			this.widget[namespace] = this.widget[namespace] || [];

			return this.widget[namespace].length;
		},

		register: function (elem)
		{
			var namespace = elem.namespace;

			if (elem.id !== undefined && elem.id !== null)
			{
				return;
			}

			elem.id = this.get_next_id(namespace);

			this.widget[namespace] = this.widget[namespace] || [];

			this.widget[namespace].push(elem);

			return elem.id;
		}, 
		
		unregister: function (elem)
		{
			this.widget[elem.namespace] ? this.widget[elem.namespace][elem.id] ? delete this.widget[elem.namespace][elem.id] : '' : '';
		},

		get_elem: function (namespace, id)
		{
			var elem;

			if (id instanceof jQuery)
			{
				id = id.cattr(prefix('id'));
			}

			elem = (this.widget[namespace] && this.widget[namespace][id] ? this.widget[namespace][id] : undefined);

			return elem;
		}
	}

	ether.widget_manager = widget_manager;
})(jQuery);

(function ($)
{
	var utils = ether.utils;
	var event_manager = ether.event_manager;
	var widget_manager = ether.widget_manager;

	var obj_foreach = utils.obj_foreach;
	var prefix_class = utils.prefix_class;

	var Ether_widget = function Ether_widget ()
	{

	}

	utils.extend_prototype(Ether_widget,
	{
		construct: function (cfg)
		{
			this.id = null;
			this.namespace = null;
			this.dom = {};

			this.defaults = $.extend(true, this.defaults, cfg || {});

			this.cfg = $.extend(true, this.cfg, cfg || {});

			$.extend(true, this, cfg || {});
		},

		get_defaults: function (prop)
		{
			return prop ? this.defaults[prop] : this.defaults;
		},

		get_cfg: function (prop)
		{
			return prop ? this.cfg[prop] : this.cfg;
		},

		set_cfg: function (prop, val)
		{
			this.cfg[prop] = val;
		},


		clean_cfg: function ()
		{
			var cfg = this.get_cfg();

			obj_foreach(this, cfg, function (key)
			{
				delete this[key];
			});
		},

		get_namespace: function (name)
		{
			return (name ? name + '.' : '') + this.namespace + '_' + this.id;
		},

		set_ready: function (state)
		{
			var old_state = this.ready;
			this.ready = state;
		},

		is_ready_widget: function ()
		{
			return this.ready;
		},

		register: function ()
		{
			if (widget_manager.register(this) === undefined)
			{
				return;
			}
		},

		unregister: function ()
		{
			widget_manager.unregister(this);
		},

		get_dom: function (name, live, selector)
		{
			var result;
			var $elem = $(this.elem);

			if (live && $elem.find(prefix_class(name + '-' + this.id)).length)
			{
				result = $elem.find(prefix_class(name + '-' + this.id));
			}

			if (( ! result || ! result.length) && selector)
			{
				result = $elem.find(selector);
			}

			if ( ! result || ! result.length)
			{
				result = (this.has_set_dom(name) ? this.dom[name] : $());
			}

			return result;
		},

		has_set_dom: function (name)
		{
			return this.dom && this.dom[name] && this.dom[name].length;
		},

		set_dom: function (name, value, selector)
		{


			value = (value && value.length ? value : this.get_dom(name, true, selector));

			this.dom[name] = value;
		},

		unset_dom: function (name)
		{
			this.dom[name] = undefined;
		},

		unwrap_dom: function (name)
		{
			var $elem = this.get_dom(name);

			if ($elem.length)
			{
				$elem.children().unwrap();
				this.unset_dom(name);
			}
		},

		remove_dom: function (name, id)
		{
			if (this.get_dom(name).length)
			{
				if (id !== undefined)
				{
					this.get_dom(name).eq(id).remove();
					this.set_dom(name);
				} else
				{
					this.get_dom(name).remove();
					this.unset_dom(name);
				}
			} else
			{
				this.unset_dom(name);
			}
		},

		format_dom_data: function (d, args)
		{
			if ( ! d)
				return;

			d = (typeof d === 'function' ? d.apply(this, args) : d);
			d = (Array.isArray(d) ? d.join(' ') : d);


			return d;
		},

		set_dom_classes: function ($elem, data, args, deconstruct)
		{
			var data = this.format_dom_data(data, args);

			if ( ! data)
				return;

			$elem[deconstruct ? 'removeClass' : 'addClass'](data);
		},

		set_dom_attributes: function ($elem, attrs, args, deconstruct)
		{
			if ( ! attrs)
				return;
			
			obj_foreach(this, attrs, function (key, attr)
			{
				attr.forEach(function (a)
				{
					var match;
					var source = $elem.attr(key) || '';
					var val = this.format_dom_data(a, args);
					match = (source.indexOf(val) !== -1 ? true : false);
					source = ! deconstruct ? source + (match ? '' : val) : source.replace(val, '');

					$elem.attr(key, source);
				}, this);
			});
		},

		set_dom_props: function (name, $elem, deconstruct, args)
		{
			var self = this;
			var data = (typeof name === 'string' ? this.get_dom_data(name) : name);

			$elem = $elem || $('<' + (data.tag || 'div') + '>');
			$elem.each(function (id)
			{
				var $self = $(this);
				args = [id].concat(args || []);

				if (data.classes)
				{
					data.classes.forEach(function (d)
					{
						self.set_dom_classes($self, d, args, deconstruct);
					});
				}

				if (data.attrs)
				{
					self.set_dom_attributes($self, data.attrs, args, deconstruct);
				}
			});

			return $elem;
		},

		deinit_dom_elem: function (name, method)
		{
			var $elem = this.get_dom(name);

			if ($elem.length)
			{
				this.set_dom_props(name, $elem, true);
				method ? method === 'remove' ? this.remove_dom(name) : this.unwrap_dom(name) : this.unset_dom(name);
			}
		},

		update_dom_elem: function (name, $elem, deconstruct, selector)
		{
			$elem = $elem || this.get_dom(name, true, selector);


			if ( ! $elem.length)
			{
				console.error('update dom elem: "' + name + '" does not exist');
				return;
			} else
			{
				this.set_dom_props(name, $elem, deconstruct);
				this.set_dom(name, undefined, selector);
			}			
		},

		load_dom_data: function (data)
	    {
	    	this.dom_data = this.dom_data || {};

	    	obj_foreach(this, data, function (elem_key, dom_data)
	    	{
	    		var d;

	    		this.dom_data[elem_key] = this.dom_data[elem_key] || { classes: [], attrs: {}};
	    		d = this.dom_data[elem_key];

	    		obj_foreach(this, dom_data, function (prop_key, prop)
	    		{
		    		switch (prop_key)
		    		{
		    			case 'classes':
		    			{
		    				d.classes.push(prop);
		    				break;
		    			}
		    			case 'attrs':
		    			{
		    				obj_foreach(this, prop, function (key, val)
		    				{
		    					d.attrs[key] = (d.attrs[key] ? d.attrs[key] : []);
		    					d.attrs[key].push(val);
		    				});
		    				break;
		    			}
		    			default:
		    			{
		    				d[prop_key] = prop;
		    				break;
		    			}
		    		}
	    		});
	    	});
	    },

	    load_dom_constructors: function (data)
	    {
	    	this.dom_constructors = this.dom_constructors || {};

	    	$.extend(this.dom_constructors, data);
	    },

	    load_dom_constructor_callback: function (callback)
	    {
	    	callback ? this.dom_constructor_callback = callback : '';
	    },

		get_dom_data: function (name)
		{
			return this.dom_data[name];
		},

		get_selector: function (name)
		{
			return this.get_dom_data(name).selector;
		},

		get_constructor_state: function (name)
		{

			return this.constructor_states[name];
		},

	    has_dom_constructor: function (name)
	    {
	    	var cfg = this.cfg;
	    	
	    	cfg = (this.dom_constructors && this.dom_constructors[name] ? this.dom_constructors[name] : undefined);

			return cfg || undefined;
	    },

	    apply_constructor_callback: function (name, deconstruct, $elem)
	    {
	    	this.dom_constructor_callback && this.dom_constructor_callback[name] ? this.dom_constructor_callback[name].apply(this, arguments) : '';
	    },

		construct_dom: function (name, deconstruct, $elem)
		{
			var args;
			var prev_state;


			$elem = $elem || this.get_dom(name, true, this.get_selector(name));
			args = [name, deconstruct, $elem]; //mind updated $elem

			if ( ! this.has_dom_constructor(name))
			{
				return;
			}

			deconstruct ? this.apply_constructor_callback.apply(this, args) : '';
			this.dom_constructors[name].apply(this, args);
			deconstruct ? '' : this.apply_constructor_callback.apply(this, args);

			this.constructor_states = this.constructor_states || {};
			prev_state = this.constructor_states[name];
			this.constructor_states[name] = (deconstruct ? false : true);



			$elem = this.$self ? this.$self : this.get_dom(name, true, this.get_selector(name));

			event_manager.trigger($elem, name + '_' + ( ! prev_state ? 'init' : prev_state === true && deconstruct ? 'deconstruct' : prev_state === this.get_constructor_state(name) ? 'update' : 'bork!!!'), [deconstruct]);
		},

		get: function (key)
		{
			return (key !== undefined ? this[key] !== undefined ? this[key] : this.cfg !== undefined ? this.cfg[key] : undefined : this);
		}
		
	});

	ether.Widget = ether.Widget || Ether_widget;
})(jQuery);


(function($)
{
	var utils = ether.utils;
	var image_loader = ether.image_loader;
	var event_manager = ether.event_manager;
	var widget_manager = ether.widget_manager;
	var Widget = ether.Widget;

	var scheduler = utils.scheduler;
	var prefix = utils.prefix;
	var prefix_class = utils.prefix_class;
	var obj_foreach = utils.obj_foreach;
	var img_get_natural_size = utils.img_get_natural_size;

	var DEFAULTS = {
		use_parent_wrap: undefined,
		align: 'none',
		wrap_width: 'auto',
		wrap_height: 'auto',
		wrap_height_ratio: 100,
		image_width: 'auto',
		image_height: 'auto',
		image_stretch_mode: 'auto',
		image_align_x: 'middle',
		image_align_y: 'middle',




	}

	var image_stretch_mode_re = new RegExp('\\b' + prefix('image-stretch-mode-') + '\\w+\\b', 'g');
	var widget_align_re = new RegExp('\\b' + prefix('align') + '\\w+\\b', 'g');

	var Media_widget = function (cfg)
	{

		cfg.wrap_width = cfg.width || cfg.wrap_width;
		cfg.wrap_height = cfg.height || cfg.wrap_height;
		cfg.wrap_height_ratio = cfg.height_ratio || cfg.wrap_height_ratio;

		this.defaults = $.extend(true, this.defaults, cfg || {});

		this.cfg = $.extend(true, this.cfg, cfg || {});

		$.extend(true, this, cfg || {});
	}
	Media_widget.prototype = new Widget();

	utils.extend_prototype(Media_widget, 
	{
		construct: function (cfg)
		{
			Widget.prototype.construct.call(this, cfg);

			this.namespace = 'media_widget';
		},

		set_w_h_attrs: function ($img, w, h)
		{
			if ( utils.isie())
			{
				w !== undefined && w !== '' && w !== 'auto' ? $img.attr('width', w) : '';
				h !== undefined && h !== '' && h !== 'auto' ? $img.attr('height', h) : '';
			} else
			{
				w !== undefined ? $img.attr('width', w) : '';
				h !== undefined ? $img.attr('height', h) : '';
			}
		},

		reset_image: function ($img)
		{
			$img
				.css({
					width: '',
					height: '',
					'margin-top': '',
					'margin-left': ''
				})

			this.set_w_h_attrs($img, 'auto', 'auto');

		},

		reset_wrap: function ($wrap)
		{
			$wrap.css({
				width: 'auto',
				height: 'auto'
			});


		},

		init_dynamic_title: function (cfg)
		{
			var options = {
				show_img_title: this.show_img_title,
				img_title_alignment_y: this.img_title_alignment_y,
				img_title_alignment_x: this.img_title_alignment_x,
				img_title_custom_class: this.img_title_custom_class
			}

			cfg ? jQuery.extend(options, cfg) : '';

			if (this.$img.siblings(prefix_class('img-title')).length)
			{
				return;
			}

			this.dynamic_title = this.dynamic_title || this.$img.dynamicTitle(options);
		},

		deinit_dynamic_title: function ($img)
		{
			if (this.dynamic_title)
			{
				this.dynamic_title.deinit();
				delete this.dynamic_title;
			}
		},

		set_title: function ($img)
		{
			this.dynamic_title ? this.dynamic_title.update() : '';
		},

		set_wrap: function ($wrap, $img, wrap_width, wrap_height, wrap_height_ratio, wrap_align, image_stretch_mode)
		{
			wrap_align = wrap_align || this.align;
			wrap_width = wrap_width || this.wrap_width;
			wrap_height = wrap_height || this.wrap_height;
			wrap_height_ratio = wrap_height_ratio || this.wrap_height_ratio;
			image_stretch_mode = image_stretch_mode || this.image_stretch_mode;

			this.reset_wrap($wrap);

			if (image_stretch_mode === 'auto')
			{
				wrap_width = 'auto';

			}

			if ((wrap_width === 'auto' || wrap_width.toString().indexOf('%') === -1) && this.image_width.toString().indexOf('%') !== -1)
			{
				wrap_width = this.image_width;
			}

			if (wrap_align === 'center' && wrap_width === 'auto')
			{
				wrap_width = this.image_width !== 'auto' ? this.image_width : this.image_height !== 'auto' ? this.image_height * (img_get_natural_size($img.attr('src')).width / img_get_natural_size($img.attr('src')).height) : img_get_natural_size($img.attr('src')).width;
			}







			$wrap.css({
				width: wrap_width
			});

			wrap_height = (wrap_height === 'constrain' ? $wrap.width() * parseInt(wrap_height_ratio) / 100 : wrap_height);


			$wrap.css({
				height: wrap_height
			});
		},

		set_image: function ($img, $wrap, stretch_mode, align_x, align_y)
		{
			$wrap = $wrap || $img.parent();
			stretch_mode = stretch_mode || this.image_stretch_mode;
			align_x = align_x || this.image_align_x;
			align_y = align_y || this.image_align_y;


			if (stretch_mode !== 'auto')
			{
				this.reset_image($img);
			}	
			this.fit_image($wrap, $img, stretch_mode);
			this.align_image($wrap, $img, align_x, align_y, stretch_mode);
		},

		fit_image: function ($wrap, $img, stretch_mode)
		{
			var wrap_w = $wrap.width();
			var wrap_h = $wrap.height();
			var s = img_get_natural_size($img.attr('src'));
			var img_w;
			var img_h;

			var img_ratio = s.width / s.height;
			var wrap_ratio = wrap_w / wrap_h;
			var img_to_wrap_ratio = wrap_ratio / img_ratio;


			img_w = undefined;
			img_h = undefined;

			switch (stretch_mode)
			{
				case 'x':
				{
					img_w = wrap_w;
					break; 
				}
				case 'y':
				{
					img_h = wrap_h;
					break;
				}
				case 'fill':
				{
					if (img_to_wrap_ratio >= 1)
					{
						img_w = wrap_w;
						img_h = wrap_w / img_ratio;
					} else
					{
						img_w = wrap_h * img_ratio;
						img_h = wrap_h;
					}

					break;
				}
				case 'fit':
				{




					if (img_to_wrap_ratio < 1)
					{
						img_w = wrap_w;
						img_h = wrap_w / img_ratio;
					} else
					{
						img_w = wrap_h * img_ratio;
						img_h = wrap_h;
					}

					break;
				}
				case 'auto':
				{
					img_w = (this.image_width.toString().indexOf('%') !== -1 ? '100%' : this.image_width);
					img_h = this.image_height;
					break;
				}
			}


			this.set_w_h_attrs($img, img_w, img_h);

			return $img;
		},

		align_image: function ($wrap, $img, align_x, align_y, stretch_mode)
		{
			if (stretch_mode === 'auto' || stretch_mode === 'fit') //handle 'fit' centering via css

				return;

			$wrap = $wrap || $img.parent();

			var wrap_h = $wrap.height();
			var wrap_w = $wrap.width();
			var img_w = $img.width();
			var img_h = $img.height();

			var w_delta = wrap_w - img_w;
			var h_delta = wrap_h - img_h;

			var img_styles = {};
			var img_style = '';


			img_styles['margin-left'] = [w_delta * (align_x === 'left' ? 0 : align_x === 'right' ? 1 : 0.5), true];
			img_styles['margin-top'] = [h_delta * (align_y === 'top' ? 0 : align_y === 'bottom' ? 1 : 0.5), true];			

			obj_foreach(null, img_styles, function (key, elem)
			{
				img_style += key + ': ' + elem[0] + 'px' + (elem[1] ? ' !important' : '') + '; ';
			});

			$img.attr('style', ($img.attr('style') || '') + img_style);

			return $img;
		},

		has_media_wrap: function ($img)
		{
			if (
				this.$img && this.$img.length === 1 && this.$wrap || //single image
				$img.hasClass(prefix('has-media-wrap')) && $img.parent().hasClass(prefix('media-wrap')) || 
				$img.parent().hasClass(prefix('widget')) && $img.parent().hasClass(prefix('img')))// || //hackor for image widget

			{
				return true;
			}
		},

		init_media_dom: function ($img)
		{
			var $wrap = (this.use_parent_wrap || this.has_media_wrap($img) ? $img.parent() : $img.wrap('<span>').parent());

			this.$wrap = (this.$wrap ? this.$wrap.add($wrap) : $wrap);		

			this.update_media_dom($wrap, $img);


			
			return $img.parent();
		},

		update_media_dom: function ($wrap, $img, image_stretch_mode, custom_class, align, show_img_title)
		{
			$wrap = $wrap || this.$wrap;
			$img = $img || this.$img;
			image_stretch_mode = image_stretch_mode || this.image_stretch_mode;
			custom_class = custom_class || this.custom_class;
			align = align || this.align;
			show_img_title = show_img_title || this.show_img_title;

			$wrap.attr('class', ($wrap.attr('class') || '').replace(image_stretch_mode_re, ''));
			$wrap.attr('class', ($wrap.attr('class') || '').replace(widget_align_re, ''));

			$img
				.addClass(prefix([
					'has-media-wrap',
					'id' + '-' + this.id,
				], ' '));

			$wrap
				.addClass(prefix([
					'id' + '-' + this.id,
					'media-wrap-' + this.id,
					'media-wrap',
					'media-img',
					'image-stretch-mode-' + image_stretch_mode,
					'align' + align
				], ' '))
				.addClass(custom_class || '')
				.css({
					overflow: (image_stretch_mode !== 'auto' || show_img_title ? 'hidden' : 'visible') //auto instead of visible causes scrollbars on image_stretch_mode: 'auto'
				});

			$wrap.css('height', 20);
		},

		deinit_media_dom: function ($img)
		{
			$img
				.removeClass(prefix('has-media-wrap'))
				.unwrap();
		},

		update: function (args, $img)
		{
			var self = this;

			$.extend(this, args || {});

			$img ? this.$img = this.$img.add($img) : ''; //non-uniform hack shall apply only for elements added after init!
			$img = $img || this.$img; 

			if (self.deinitialized)
			{
				return;
			}

			image_loader.on_image_load_end($img, function ()
			{
				var $wrap;

				if (self.deinitialized)
				{
					return;
				}

				$wrap = $(this).parent();

				$wrap.addClass(self.custom_class || '');

				self.update_media_dom($wrap, $(this));
				self.set_wrap($wrap, $(this));
				self.set_image($(this));
				self.set_title($(this));

				event_manager.trigger($(this), 'media-widget-update', [this]);
			});

			image_loader.on_images_load_end($img, function ()
			{
				self.set_ready(true);
			});
		},

		add_elem: function ($img)
		{
			$img = $img.filter('img').add($img.find('img'));
			
			this.$img = this.$img.add($img); //non-uniform hack shall apply only for elements added after init!
			this.init_media_dom($img);	
			this.update(undefined, $img);
		},

		resize_callback: function ()
		{
			var self = this;

			scheduler.set(self.get_namespace() + '_' + 'resize', 100, function ()
			{
				self.update();
			});
		},

		init_resize_callback: function ()
		{
			var self = this;

			event_manager.on($(window), 'resize.' + self.get_namespace(), function ()
			{
				self.resize_callback();
			});
		},

		deinit_resize_callback: function ()
		{
			$(window).off('resize.' + this.get_namespace());
		},

		init: function (cfg)
		{
			var self = this;
			cfg = cfg || this.cfg;

			if (this.$img.hasClass(prefix('has-media-wrap')) || this.$img.hasClass(prefix('media-wrap')))
			{
				return;
			}

			this.construct(cfg);

			this.register();


			this.$img.each(function ()
			{
				self.init_media_dom($(this));				
			});

			this.init_dynamic_title();

			this.update();

			this.init_resize_callback();

			return this;
		},

		deinit: function ()
		{
			var self = this;

			this.deinitialized = true;

			this.deinit_dynamic_title();

			this.$img.each(function ()
			{
				self.unset_image($(this));
			}, this);
			this.deinit_media_dom(this.$img);
			this.unregister();
		},

		set: function (key, value)
		{


			this[key] = value;

			if (key === 'show_img_title' || key === 'img_title_alignment_y' || key === 'img_title_alignment_x' || key === 'img_title_custom_class')
			{
				! this.dynamic_title ? this.init_dynamic_title({key: value}) : this.dynamic_title[key] = value;
			}

			this.update();
		}
	});

	$.fn.mediaWidget = function (options) 
	{
		var options;
        var defaults;

        if (options)
        {
	        defaults = jQuery.extend(true, {}, DEFAULTS);
			options = $.extend(defaults, options);

	        options.elem_selector = $(this).selector;
	        options.$img = options.$img || $(this);
	        
			return new Media_widget(jQuery.extend(true, {}, options)).init();
		} else
		{
			return widget_manager.get_elem('media_widget', $(this));
		}
    }

	ether.Media_widget = ether.Media_widget || Media_widget;
})(jQuery);

(function($)
{
	var utils = ether.utils;
	var Widget = ether.Widget;
	var widget_manager = ether.widget_manager;

	var prefix = utils.prefix;
	var prefix_class = utils.prefix_class;
	var obj_foreach = utils.obj_foreach;
	var get_img_title = utils.get_img_title;

	var DEFAULTS = {

		show_img_title: false,
		img_title_alignment_y: 'bottom',
		img_title_alignment_x: 'middle',
		img_title_custom_class: ''
	}

	var Dynamic_title = function (cfg)
	{
		$.extend(this, cfg);

		this.show_img_title = this.show_img_title === true ? 'on-hover' : this.show_img_title;
	}

	Dynamic_title.prototype = new Widget();

	utils.extend_prototype(Dynamic_title, {
		construct: function (cfg)
		{
			Widget.prototype.construct.call(this, cfg);

			$.extend(this.cfg, cfg || {});

			this.namespace = 'dynamic_title';
		},

		has_only_img_child: function ($elem)
		{
			$elem.children().length === 1 && $elem.children().prop('tagName') === 'IMG';
		},

		has_title_elem: function ($elem)
		{
			return $elem.siblings(prefix_class('img-title')).length;
		},

		append_title_elem: function ($parent, title)
		{
			if ( ! this.has_title_elem($parent))
			{
				this.$title = this.$title.add(
					$('<span class="' + prefix('img-title') + '">' + title + '</span>')
						.appendTo($parent));
			}

			return $parent.children(prefix_class('img-title'));
		},

		has_img_title_wrap: function ($elem)
		{
			return $elem.parent().hasClass(prefix('img-title-wrap'));
		},

		init_img_title_wrap: function ($img)
		{
			var $wrap;

			$wrap = $img.parent();

			if ( ! this.has_img_title_wrap($img))
			{
				this.$wrap = this.$wrap.add($wrap);
				
				$wrap
					.addClass(prefix('img-title-wrap'));
			}


			return $wrap;
		},

		deinit_img_title_wrap: function ($img)
		{
			$img.parent()
				.removeClass(prefix('img-title-wrap'))
		},

		init_title_structure: function ($img)
		{
			var title = get_img_title($img) || '';




			this.init_img_title_wrap($img);	
			this.append_title_elem($img.parent(), title);
		},

		update: function ($img)
		{
			var self = this;

			$img = $img || this.$img;

			$img.each(function ()
			{
				self.update_title($(this));
			});
		},

		update_title: function ($img)
		{
			var $title = $img.siblings(prefix_class('img-title'));
			var $wrap = $img.parent();

			var show_img_title = this.show_img_title;
			var img_title_alignment_y = this.img_title_alignment_y;
			var img_title_custom_class = this.img_title_custom_class;

			var title_height = $title.outerHeight();
			var title_pos_y_hidden = -title_height;
			var title_pos_y_visible = 0;


			if ( ! show_img_title)
			{


				$title.hide();
			} else
			{
				$title
					.addClass(img_title_custom_class || '')
					.css({
						opacity: (show_img_title === 'always' ? 1 : 0),
						top: (img_title_alignment_y === 'top' ? (show_img_title === 'always' ? title_pos_y_visible : title_pos_y_hidden) : 'auto'),
						bottom: (img_title_alignment_y === 'bottom' ? (show_img_title === 'always' ? title_pos_y_visible : title_pos_y_hidden) : 'auto'),
					});

				$title.text().length ? $title.show() : '';
			}

			$wrap.off('.' + this.get_namespace());

			if (show_img_title === 'on-hover' && $title.text().length)
			{
				$wrap
					.on('mouseenter' + '.' + this.get_namespace(), function ()
					{
						$title
							.stop(true, true).animate({
								opacity: 1,
								top: (img_title_alignment_y === 'top' ? title_pos_y_visible : 'auto'),
								bottom: (img_title_alignment_y === 'bottom' ? title_pos_y_visible : 'auto')
							}, 500);
					})
					.on('mouseleave' + '.' + this.get_namespace(), function ()
					{
						$title 
							.delay(250)
							.animate({
								opacity: 0,
								top: (img_title_alignment_y === 'top' ? title_pos_y_hidden : 'auto'),
								bottom: (img_title_alignment_y === 'bottom' ? title_pos_y_hidden : 'auto')
							}, 500);
					});
			}

			$wrap.trigger('dynamic-title-update', [$wrap, $img, $title]);
		},

		add_elem: function ($img)
		{
			$img = $img.filter('img').add($img.find('img'));
			
			this.init_title_structure($img);
			this.update($img);
		},

		init: function (cfg)
		{
			var self = this;

			this.construct();

			this.register();

			this.$wrap = $();
			this.$title = $();

			this.$img.each(function ()
			{
				self.init_title_structure($(this));
			});

			this.update();

			this.set_ready(true);


			return this;
		},

		deinit: function ()
		{

			this.deinit_img_title_wrap(this.$img);
			this.unregister();
			this.$title.remove();
		},

		set: function (key, value, stop_update)
		{
			var self = this;


			key = (typeof key === 'string' ? {key: value} : key);

			obj_foreach(this, key, function (key, value)
			{
				value !== null && value !== undefined ? self[key] = value : '';
			});

			this.update();
		}
	});

	$.fn.dynamicTitle = function (options) 
	{
		var options;
        var defaults;

        if (options)
        {
	        defaults = jQuery.extend(true, {}, DEFAULTS);
			options = $.extend(defaults, options);

	        options.elem_selector = $(this).selector;
	        options.$img = options.$img || $(this);
	        
			return new Dynamic_title(jQuery.extend(true, {}, options)).init();
		} else
		{

			return widget_manager.get_elem('dynamic_title', $(this));
		}
    }

	ether.Dynamic_title = ether.Dynamic_title || Dynamic_title;
})(jQuery);

(function ($)
{	
	var utils = ether.utils;
	var image_loader = ether.image_loader;
	var css_generator = ether.css_generator;
	var event_manager = ether.event_manager;
	var widget_manager = ether.widget_manager;
	var Widget = ether.Widget;

	var scheduler = utils.scheduler;
	var obj_foreach = utils.obj_foreach;
	var prefix = utils.prefix;
	var prefix_class = utils.prefix_class;
	var wrap_dom_groups = utils.wrap_dom_groups;
	var animate_dom = utils.animate_dom;

	var grid_manager = function ()
	{
		return {

			prefix: 'ether',
		    namespace: 'ether',
		    ROW_WRAP_MODE: {
		    	ROW: 0,
		    	GROUP: 1,
		    	ALL: 2
		    },

			default_framework: 'ether',
			FRAMEWORKS: 
			{





				'core':
				{
					row_wrap_mode: 2,
					dom_data:
					{
						elem:
						{
							classes: function () 
							{
								var classes = [
									'id' + '-' + this.id,
									'grid',
									'align' + this.cfg.align
								];

								return prefix(classes, ' ');
							},

							attrs: 
							{
								'data-grid_slider_init': true,
								style: function ()
								{
									return 'width: ' + (typeof this.cfg.width === 'number' ? this.cfg.width + 'px' : this.cfg.width);
								}
							}
						},

						crumb: 
						{
							classes: function () 
							{
								return prefix([
									'crumb' + '-' + this.id
								], ' ');
							}
						},

						'crumb-wrap':
						{
							
							classes: function (id)
							{
								var result = prefix([
									'crumb-wrap' + '-' + this.id,
								], ' ');

								return result;
							},

							attrs: {}	
						},

						crumbs:
						{
							classes: function () 
							{
								var classes = prefix([
									'crumbs' + '-' + this.id
								], ' ');

								return classes;
							},

							attrs: {}	
						},

						'crumb-group':
						{
							classes: function ()
							{
								return prefix([
									'crumb-group' + '-' + this.id,
									'crumb-group',
									'col-group' //temp backward compatibility
								], ' ');
							}
						},

						'slider-window':
						{
							classes: function ()
							{
								return prefix([
									'slider-window',
									'slider-window' + '-' + this.id
								], ' ');
							},

							attrs: {
								style: 'height: 20px; overflow: hidden;'
							}
						},

						'load-overlay':
						{
							classes: function ()
							{
								return prefix([
									'load-overlay',
									'load-overlay' + '-' + this.id
								], ' ');
							}
						},

						'ctrl-wrap':
						{
							classes: function ()
							{
								var cfg = this.cfg;

								return prefix([
									'ctrl-wrap' + '-' + this.id,
									'ctrl-style' + '-' + cfg.ctrl_style + (cfg.theme === 'light' ? '' : '-light'),
									'scroll-axis' + '-' + cfg.scroll_axis
								], ' ');
							},

							attrs:
							{
								style: 'z-index: 20;'
							}
						},

						'ctrl-arrows':
						{
							classes: function ()
							{
								return prefix([
									'ctrl-car', //temp backward compatibility
									'ctrl-car' + '-' + this.id, //temp backward compatibility
									'ctrl-arrows',
									'ctrl-arrows' + '-' + this.id
								], ' ');
							},

							attrs:
							{
								style: function ()
								{
									var cfg = this.cfg;
									var top = 0, bottom = 0, left = 0, right = 0;
									var shift_x = cfg.ctrl_arrows_align_x === 'right' ? 'right' : 'left';
									var shift_y = cfg.ctrl_arrows_align_y === 'bottom' ? 'bottom' : 'top';

									cfg.ctrl_arrows_align_y === 'bottom' ? top = 'auto' : bottom = 'auto';
									cfg.ctrl_arrows_align_x === 'right' ? left = 'auto' : right = 'auto';

									return [
										'top: ' + top,
										'left: ' + left,
										' bottom: ' + bottom,
										'right: ' + right,
										'z-index: 20',
										'margin-' + shift_y + ': ' + cfg.ctrl_arrows_pos_shift_y + 'px',
										'margin-' + shift_x + ': ' + cfg.ctrl_arrows_pos_shift_x + 'px',

										''
									].join('; ');
								}
							}
						},

						'ctrl-arrow':
						{
							classes: function (id, dir)
							{
								return prefix([
									'ctrl',
									'ctrl' + '-' + this.id,
									'ctrl' + '-' + (dir ? 'next' : 'prev')
								], ' ');
							},

							attrs: 
							{
								'data-shifttype': 'relative',
								'data-shiftdest': function (id, dir) 
								{
									return (dir ? 1 : -1);
								}
							}
						},

						'ctrl-pag':
						{
							classes: function ()
							{
								return prefix([
									'ctrl-pag',
									'ctrl-pag' + '-' + this.id
								], ' ');
							},

							attrs:
							{
								style: function ()
								{
									var cfg = this.cfg;
									var top = 0, bottom = 0, left = 0, right = 0;
									var shift_x = cfg.ctrl_pag_align_x === 'right' ? 'right' : 'left';
									var shift_y = cfg.ctrl_pag_align_y === 'bottom' ? 'bottom' : 'top';

									cfg.ctrl_pag_align_y === 'bottom' ? top = 'auto' : bottom = 'auto';
									cfg.ctrl_pag_align_x === 'right' ? left = 'auto' : right = 'auto';

									return [
										'top: ' + top,
										'left: ' + left,
										' bottom: ' + bottom,
										'right: ' + right,
										'margin-' + shift_y + ': ' + cfg.ctrl_pag_pos_shift_y + 'px',
										'margin-' + shift_x + ': ' + cfg.ctrl_pag_pos_shift_x + 'px',
										'z-index: 20',
										'overflow: hidden',
										''
									].join('; ');
								}
							}
						},

						'ctrl-pag-crumb':
						{
							classes: function ()
							{
								return prefix([
									'ctrl',
									'ctrl' + '-' + this.id,
									'ctrl-pag-crumb',
									'ctrl-pag-crumb' + '-' + this.id,
								], ' ');
							},

							attrs:
							{
								'data-shifttype': 'absolute',
								'data-shiftdest': function (id, dest)
								{
									return dest;
								},

								style: function ()
								{
									return 'top: 0; left: 0; ' + 'margin: ' + this.cfg.ctrl_pag_spacing / 2 + 'px; ';
								}
							}
						}
					}
				},

				'unsemantic': 
				{
					row_wrap_mode: 0,
					dom_data:
					{
						elem:
						{
							classes: function () 
							{
								var classes = [
									'unsemantic' + '-' + this.id,
								];

								return classes;
							}
						},

						'crumb-wrap':
						{
							selector: '> .grid-container > *',
							classes: function (id)
							{
								var result = [
									'column'
								];

								result.push('grid' + '-' + (100 / this.cfg.cols).toFixed(0));

								return result;
							}
						},

						crumbs:
						{
							selector: '> .grid-container',
							classes: function () 
							{
								var classes = [
									'grid-container'
								];

								return classes;
							},

						},

						crumb:
						{
							selector: '> .grid-container > * > *'
						}
					},

					grid_detector_data: 
					{
						row_filter: /\bgrid-container\b/,
						col_filter: /\bgrid-(\d+)\b/,
						depth: 3,
						count_fn: function ($row, $col)
						{
							var row_count = 1;
							var col_count = $col.length;

							return {
								rows: row_count,
								cols: col_count
							}
						}
					}
				},

				'semantic-ui': 
				{
					row_wrap_mode: 0,
					dom_data:
					{
						elem:
						{
							classes: function () 
							{
								var classes = [
									'semantic-ui' + '-' + this.id,
									'ui',
									'doubling',
									'grid',
									'column'
								];

								var col_marker;
								var c = this.cfg.cols;
								var markers = {1: 'one', 2: 'two', 3: 'three', 4: 'four', 5: 'five', 6: 'six', 7: 'seven', 8: 'eight', 9: 'nine', 10: 'ten', 11: 'eleven', 12: 'twelve', 13: 'thirteen', 14: 'fourteen', 15: 'fifteen', 16: 'sixteen'};

								if (typeof c === 'number')
								{
									col_marker = markers[c];
								}

								classes.push(col_marker);

								return classes;
							}
						},

						'crumb-group':
						{
							classes: function () 
							{
								var classes = [
									'ui',
									'doubling',
									'grid',
									'column'
								];
								var col_marker;
								var c = this.cfg.cols;
								var markers = {1: 'one', 2: 'two', 3: 'three', 4: 'four', 5: 'five', 6: 'six', 7: 'seven', 8: 'eight', 9: 'nine', 10: 'ten', 11: 'eleven', 12: 'twelve', 13: 'thirteen', 14: 'fourteen', 15: 'fifteen', 16: 'sixteen'};

								if (typeof c === 'number')
								{
									col_marker = markers[c];
								}

								classes.push(col_marker);

								return classes;
							}
						},

						'crumb-wrap':
						{
							selector: '> .row > .column',
							classes: function (id)
							{
								var result = [
									'column'
								];

								return result;
							}
						},

						crumbs:
						{
							selector: '> .row',
							classes: function () 
							{
								var classes = [
									'row'
								];

								return classes;
							},

						},

						crumb:
						{
							selector: '> .row > * > *'
						}
					},

					grid_detector_data: 
					{
						row_filter: /\brow\b/,
						col_filter: /\bcolumn\b/,
						depth: 3,
						count_fn: function ($row, $col)
						{
							var row_count = 1;
							var col_count = $col.length;

							return {
								rows: row_count,
								cols: col_count
							}
						}
					}
				},

				'skeleton':
				{
					row_wrap_mode: 0,
					dom_data:
					{
						elem:
						{
							classes: function () 
							{
								var classes = [
									'skeleton' + '-' + this.id,
									'container'
								];

								return classes;
							}
						},

						'crumb-wrap':
						{
							selector: '> .row > *',
							classes: function (id)
							{
								var result = [];
								var c = this.cfg.cols
								var col_marker = (c === 1 ? 'sixteen' : c === 2 ? 'eight' : c === 3 ? 'one-third' : c < 7 ? 'four' : c < 12 ? 'two' : 'one');

								result.push(col_marker);
								result.push(c === 3 || c === 1 ? 'column' : 'columns'); //good nig?


								return result;
							}
						},

						crumbs:
						{
							selector: '> .row',
							classes: function () 
							{
								var classes = [
									'row'
								];

								return classes;
							},

						},

						crumb:
						{
							selector: '> .row > * > *'
						}
					},

					callback:
					{
						crumbs: function (name, deconstruct, $elem)
						{
							var cfg = this.cfg;
							var a;

							if ( ! deconstruct)
							{
								this.get_dom('crumb-wrap').removeClass('alpha omega');
								for (a = 0; a < this.crumb_count; a += 1)
								{
									if (a % this.real_col_count === 0)
									{
										this.get_dom('crumb-wrap').eq(a).addClass('alpha');
									} else if (a % this.real_col_count === this.real_col_count - 1) 
									{
										this.get_dom('crumb-wrap').eq(a).addClass('omega');
									};
								}
							}
						}
					},

					grid_detector_data: 
					{
						row_filter: /\brow\b/,
						col_filter: /\bcolumn\b/,
						depth: 3,
						count_fn: function ($row, $col)
						{
							var row_count = 1;
							var col_count = $col.length;

							return {
								rows: row_count,
								cols: col_count
							}
						}
					}
				},

				'foundation': 
				{
					row_wrap_mode: 0,
					dom_data:
					{
						elem:
						{
							classes: function () 
							{
								var classes = [
									'foundation' + '-' + this.id
								];

								return classes;
							}
						},

						'crumb-wrap':
						{
							selector: '> .row > .columns',
							classes: function (id)
							{
								var result = [];
								var col_marker;
								var c = this.cfg.cols;

								if (typeof c === 'number')
								{
									col_marker = (c === 1 ? 12 : c === 2 ? 6 : c < 6 ? 4 : c < 10 ? 2 : 1);
								}						

								result.push('columns');
								result.push('medium-' + col_marker);
								result.push('large-' + col_marker);
								result.push('small-' + col_marker);

								return result;
							}
						},

						crumbs:
						{
							selector: '> .row',
							classes: function () 
							{
								var classes = [
									'row'
								];

								return classes;
							},

						},

						crumb:
						{
							selector: '> .row > * > *'
						}
					},

					grid_detector_data: 
					{
						row_filter: /\brow\b/,
						col_filter: /\bcolumns\b/,
						depth: 3,
						count_fn: function ($row, $col)
						{
							var row_count = 1;
							var col_count = $col.length;

							return {
								rows: row_count,
								cols: col_count
							}
						}
					}
				},

				'bootstrap': 
				{
					row_wrap_mode: 0,
					dom_data:
					{
						elem:
						{
							classes: function () 
							{
								var classes = [
									'bootstrap' + '-' + this.id
								];

								return classes;
							}
						},

						'crumb-wrap':
						{
							selector: '> .row > *',
							classes: function (id)
							{
								var result = [];
								var col_marker;
								var c = this.cfg.cols;

								if (typeof c === 'number')
								{
									col_marker = (c === 1 ? 12 : c === 2 ? 6 : c < 6 ? 4 : c < 10 ? 2 : 1);
								}							

								result.push('col-md-' + col_marker);

								return result;
							}
						},

						crumbs:
						{
							selector: '> .row',
							classes: function () 
							{
								var cfg = this.cfg;
								var classes = [
									'row'
								];

								return classes;
							},

						},

						crumb:
						{
							selector: '> .row > * > *'
						}
					},

					grid_detector_data: 
					{
						row_filter: /\brow\b/,
						col_filter: /\bcol-(\w+)-(\d+)\b/,
						depth: 3,
						count_fn: function ($row, $col)
						{
							var row_count = 1
							var col_count = $col.length;

							return {
								rows: row_count,
								cols: col_count
							}
						}
					}
				},

				'ether': 
				{
					cfg: 
					{
						mark_first_col: true
					},

					row_wrap_mode: 1,
					dom_data:
					{
						elem:
						{
							classes: function () 
							{
								var classes = [];

								isie && iev === 7 ? classes.push(prefix('ie7-grid-fix')) : '';
								this.is_slider() ? classes.push(prefix('slider')) : '';

								return classes;
							},

						},

						'crumb-wrap':
						{
							selector: '> .ether-cols > .ether-col',
							classes: function () 
							{
								return prefix([
									'col'
								], ' ');
							}
						},

						crumbs:
						{
							selector: '> .ether-cols',
							classes: function () 
							{
								var cfg = this.cfg;
								var classes = [
									'cols',
									'cols' + '-' + cfg.cols,
									'rows' + '-' + cfg.rows,
									'spacing' + '-' + (cfg.col_spacing_enable === true ? 1 : 0)
								];

								return prefix(classes, ' ');
							}
						},

						crumb:
						{
							selector: '> .ether-cols > .ether-col > *'
						}
					},

					callback:
					{
						crumb: function (name, deconstruct, $elem)
						{
							$elem = $elem || this.get_dom(name);

							if (iev !== 7)
							{
								return;
							}

							if ( ! deconstruct)
							{
								$elem.wrap('<div>').parent().addClass(prefix([
									'ie7-inner-col-wrap',
									'ie7-inner-col-wrap' + '-' + this.id
								], ' '));

								this.set_dom(name, $(this.elem).find(prefix_class('ie7-inner-col-wrap' + '-' + this.id)), this.get_selector(name));
							} else
							{
								$elem = $elem || $(this.elem).find(prefix_class('ie7-inner-col-wrap' + '-' + this.id));

								$elem.children().length ? $elem.children().unwrap() : $elem.remove();

								this.set_dom(name, undefined, this.get_selector(name));
							}
						},

						crumbs: function (name, deconstruct, $elem)
						{
							var cfg = this.cfg;
							var a;
							var first_col = prefix('first-col');
							var last_col = prefix('last-col');

							if ( ! cfg.mark_first_col)
							{
								return;
							}

							if ( ! deconstruct)
							{
								this.get_dom('crumb-wrap').removeClass(first_col);
								this.get_dom('crumb-wrap').removeClass(last_col);
								
								for (a = 0; a < this.crumb_count; a += 1)
								{
									if (a % this.real_col_count === 0)
									{
										this.get_dom('crumb-wrap').eq(a).addClass(first_col);
									}

									if (a === this.real_col_count - 1 && iev < 9)
									{
										this.get_dom('crumb-wrap').eq(a).addClass(last_col)
									}
								}
							}
						}
					},

					grid_detector_data: 
					{
						row_filter: /\bether-cols\b/,
						col_filter: /\bether-col\b/,
						depth: 3,
						count_fn: function ($row, $col)
						{
							var row_count = $col.length / $row.attr('class').match(/\bether-cols-(\d)/)[1];
							var col_count = $col.length / row_count;

							return {
								rows: row_count,
								cols: col_count
							}
						}
					},

					css_styles: function ()
					{
						var cfg = this.cfg;
						var pattern;

						if (this.has_custom_col_spacing())
						{
							pattern = '[["uid"]] .[["prefix"]]cols { margin: [["half_spacing"]]px -[["half_spacing"]]px; } [["uid"]] .[["prefix"]]cols + * { margin-top: 0px !important; } [["uid"]] * + .[["prefix"]]cols { margin-top: 0px; } [["uid"]] .[["prefix"]]cols + .[["prefix"]]cols { margin-top: -[["half_spacing"]]px; } [["uid"]] .[["prefix"]]col > .[["prefix"]]cols:first-child { margin-top: -[["half_spacing"]]px !important; } [["uid"]] .[["prefix"]]col { padding: [["half_spacing"]]px; }'; 

							if (utils.isie() && utils.iev() == '7')
							{
								pattern += ' [["uid"]] .[["prefix"]]cols { left: -[["half_spacing"]]px !important; } [["uid"]] .[["prefix"]]cols .[["prefix"]]ie7-padding-maker { padding: 0px [["half_spacing"]]px !important; } [["uid"]] .[["prefix"]]cols .[["prefix"]]ie7-padding-maker > :first-child { margin-top: 0 !important; } [["uid"]] .[["prefix"]]cols .[["prefix"]]ie7-padding-maker > .[["prefix"]]last-child { margin-bottom: 0 !important; } [["uid"]] .[["prefix"]]cols.[["prefix"]]spacing-0 .[["prefix"]]ie7-padding-maker { padding: 0 !important; }';
							}

							pattern = pattern.replace(/\[\["uid"\]\]/g, '.' + utils.prefix('id-' + this.id));
							pattern = pattern.replace(/\[\["prefix"\]\]/g, utils.prefix(''));
							pattern = pattern.replace(/\[\["half_spacing"\]\]/g, cfg.col_spacing_size/2);

							css_generator.add_raw_styles(pattern);

							/*selector_name = [
								cfg.elem_selector,
								prefix_class('grid' + ' '),
								prefix_class('crumbs' + id_suffix),
							];
							rules = {
								margin: -(cfg.col_spacing_size/2) + 'px'
							}
							css_generator.add_style_data('all', selector_name, rules);

							selector_name = [
								cfg.elem_selector,
								prefix_class('grid' + ' '),
								prefix_class('crumb-wrap' + id_suffix)
							];
							rules = {
								padding: (cfg.col_spacing_size/2) + 'px'
							}
							css_generator.add_style_data('all', selector_name, rules);
							
							if (isie && parseInt(iev) === 7)
							{
								selector_name = [
									cfg.elem_selector,
									prefix_class('grid'),
									prefix_class('ie7-grid-fix' + ' '),
									prefix_class('crumb-wrap' + id_suffix)
								];
								rules = {

									padding: '0 !important'
								};
								css_generator.add_style_data('ie7', selector_name, rules);
								
								selector_name = [
									cfg.elem_selector,
									prefix_class('grid' + ' '),
									prefix_class('ie7-inner-col-wrap' + id_suffix)
								];
								rules = {
									margin: (cfg.col_spacing_size/2) + 'px !important'
								};
								css_generator.add_style_data('ie7', selector_name, rules);
							}*/
						}
					}
				}
			},

			grid_detector: function (row_filter, col_filter, depth, count_fn)
			{



				var row_count;
				var col_count;

				var $next;
				var $row;
				var $col;

				while (depth > 0)
				{
					if ( ! $row || ! $row.length)
					{
						$next = ($next ? $next.children() : $(this.elem));
						$row = $next.filter(function ()
						{
							return ($(this).attr('class') || '').match(row_filter);
						});
					} else if ( ! $col || ! $col.length)
					{
						$next = $next.eq(0).children();
						$col = $next.filter(function () {
							return $(this).attr('class').match(col_filter);
						});

						if ($col.length)
							break;
					}

					if( ! $next.length)
						break;

					depth -= 1;
				}

				if ($row && $row.length && $col && $col.length)
				{
					row_count = 1;
					col_count = $col.length;
				} else
				{
					return;
				}

				return count_fn($row, $col);
			},

			dom_constructors: 
			{
				elem: function (name, deconstruct, $elem)
				{
					if ( ! deconstruct)
					{
						this.update_dom_elem(name, $(this.elem))

						this['elem_width'] = $(this.elem).outerWidth();
						this['real_row_count'] = ($(window).width() < 580 ? 1 : this.cfg.rows); //limit row count on small screens. Affects slider only.
					} else
					{
						this.set_dom_props(name, $(this.elem), true)
					}
				},

				crumbs: function (name, deconstruct, $elem)
				{
					var $new_wrap = this.set_dom_props(name);

					if ( ! deconstruct)
					{
						if ( ! $elem.length)
						{
							wrap_dom_groups($new_wrap, this.get_dom('crumb-wrap'), this.get_crumbs_capacity());
						}

						this.update_dom_elem(name);

						this['real_col_count'] = Math.round(this.elem_width / this.get_dom('crumb-wrap').outerWidth());
					} else
					{
						this.deinit_dom_elem(name, 'unwrap');
					}
				},

				'crumb-wrap': function (name, deconstruct, $elem)
				{
					var $new_wrap = this.set_dom_props(name);
					if ( ! deconstruct)
					{
						if ( ! $elem.length)
						{
							wrap_dom_groups($new_wrap, this.get_dom('crumb'), 1);	
						} else if ($elem.length === 1 && ! $elem.hasClass(prefix(name))) //only when adding an element after init
						{

							if (this.crumb_count === 2)
							{
								this.get_dom('crumb', true).filter(prefix_class('phantom-crumb')).parent().remove();
								this.set_dom('crumb');
							}

							wrap_dom_groups($new_wrap, $elem, 1);
						}

						this.update_dom_elem(name);
					} else
					{
						this.deinit_dom_elem(name, 'unwrap');
					}
				},

				crumb: function (name, deconstruct, $elem)
				{
					if ( ! deconstruct)
					{







						$elem = $elem.length ? $elem : this.get_dom(name, false, this.get_selector(name)).length ? this.get_dom(name, false, this.get_selector(name)) : $(this.elem).children(); 

						if ( ! $elem.length) //apply phantom crumb
						{
							$('<div>')
								.addClass(prefix('phantom-crumb'))
								.appendTo($(this.elem)) 

							$elem = $(this.elem).children(prefix_class('phantom-crumb'));
						}

						this.update_dom_elem(name, $elem);

						this.crumb_count = this.get_dom('crumb').length;
					} else
					{
						this.deinit_dom_elem(name);
					}
				},

				'crumb-group': function (name, deconstruct, $elem)
				{
					var cfg = this.cfg;
					var $new_wrap = this.set_dom_props(name);
					var capacity;

					if ( ! deconstruct)
					{
						if ( ! $elem.length)
						{


							if (this.row_wrap_mode = grid_manager.ROW_WRAP_MODE.ALL)
							{
								this.row_wrap_mode_default = this.row_wrap_mode //enable fallback to default (ALL) on crumb-group deconstruct
								this.row_wrap_mode = grid_manager.FRAMEWORKS[this.cfg.framework].row_wrap_mode;
								this.construct_dom('crumbs', true);
								this.construct_dom('crumbs');
							}

							capacity = (this.row_wrap_mode === grid_manager.ROW_WRAP_MODE.GROUP ? 1 : this.real_row_count);

							wrap_dom_groups($new_wrap, this.get_dom('crumbs'), capacity);
						}

						this.update_dom_elem(name);

						this.crumb_group_count = this.get_crumb_group_count();
						this.set_view_pos(this.get_next_view_pos(this.view_pos || cfg.view_pos, 'relative', 0, cfg.loop, this.crumb_group_count));
						this.set_visible_crumb_group();

					} else
					{
						this.deinit_dom_elem(name, 'unwrap');

						if (this.row_wrap_mode_default)
						{
							this.row_wrap_mode = this.row_wrap_mode_default; //fallback to default (ALL)


						}
					}
				},

				'load-overlay': function (name, deconstruct, $elem)
				{
					if ( ! deconstruct)
					{
						if ( ! $elem.length)
						{
							$elem = this.set_dom_props(name);
							this.get_dom('slider-window').append($elem);
						}

						this.update_dom_elem(name);
					} else
					{
						this.deinit_dom_elem(name, 'remove');
					}
				},

				'slider-window': function (name, deconstruct, $elem)
				{
					if ( ! deconstruct)
					{
						if ( ! $elem.length)
						{
							$elem = this.set_dom_props(name);
							this.get_dom('crumb-group').wrapAll($elem);
						}

						this.update_dom_elem(name);
					} else
					{
						this.deinit_dom_elem(name, 'unwrap');
					}
				},

				'ctrl-wrap': function (name, deconstruct, $elem)
				{
					if ( ! deconstruct)
					{
						if ( ! $elem.length)
						{
							$elem = this.set_dom_props(name);
							$elem
								.appendTo($(this.elem))
								.hide();
						}

						this.update_dom_elem(name);
					} else
					{
						this.deinit_dom_elem(name, 'remove');
					}
				},

				'ctrl-arrows': function (name, deconstruct, $elem)
				{
					if ( ! deconstruct)
					{
						if ( ! $elem.length)
						{
							$elem = this.set_dom_props(name);
							$elem
								.appendTo(this.get_dom('ctrl-wrap'))
								.append(this.set_dom_props('ctrl-arrow', undefined, undefined, [0]))
								.append(this.set_dom_props('ctrl-arrow', undefined, undefined, [1]));
						}

						this.update_dom_elem(name);
					} else
					{
						this.deinit_dom_elem(name, 'remove');
					}
				},

				'ctrl-pag': function (name, deconstruct, $elem)
				{
					var count;

					if ( ! deconstruct)
					{
						if ( ! $elem.length)
						{
							$elem = this.set_dom_props(name);
							count = this.get_dom('crumb-wrap').length;

							$elem
								.appendTo(this.get_dom('ctrl-wrap'));

							for (a = 0; a < count; a += 1)
							{
								$elem.append(this.set_dom_props('ctrl-pag-crumb', undefined, undefined, [a]));
							}
						}

						this.update_dom_elem(name);
					} else
					{
						this.deinit_dom_elem(name, 'remove');
					}
				}
			},

			dom_constructors_init_order:
			{
				grid: function (mode)
				{
					var order =
					[
						'elem',
						'crumb',
						'crumb-wrap',
						'crumbs'
					];

					return mode ? order : order.reverse(); 
				},

				slider: function (mode)
				{
					var cfg = this.cfg;

					var order =
					[
						'crumb-group',
						'slider-window',
						'load-overlay'
					];

					cfg.ctrl ? order.push('ctrl-wrap') : '';
					cfg.ctrl_arrows ? order.push('ctrl-arrows') : '';
					cfg.ctrl_pag ? order.push('ctrl-pag') : '';

					return mode ? order : order.reverse();
				}
			},

			scroll_axes: ['x', 'y', 'z'],
			scroll_axes_dir_map: 
			{
				'x': {x: 1, y: 0}, 
				'y': {x: 0, y: 1},
				'z': {x: 0, y: 0}
			},

			scroll_transitions: ['slide', 'slideIn', 'slideOut', 'switch', 'swap'],
			DEFAULTS:  
			{
				auto_detect_framework: false,
				framework: 'ether',
				dom_data: null, //use this for hardcoded grid with custom structrue when using a default framework //most likely won't be necessary; use a custom framework instead
				grid_hardcoded: false, //shortcut to framework auto detect //not yet incorporated

				width: 'auto',
				align: 'auto',

				init_grid: true,
				cols: 1,
				rows: 1,
				col_spacing_enable: true,
				col_spacing_size: 30,
				grid_height: 'auto', //
				grid_height_ratio: 100, //
				hide_grid_cell_overflow: false, //

				slider: false,
				advanced_responsive: true,
				random_order: false,
				view_pos: 0,
				view_pos_inner: 0,
				view_pos_inner_step: 'auto',
				scroll_axis: 'x',
				transition: 'slide',
				easing: 'swing',
				scroll_speed: 500,
				loop: false,
				scroll_on_mousewheel: false,
				shift_fade: false,

				ctrl: false,
				ctrl_arrows: false,
				ctrl_pag: false,
				ctrl_external: [],
				ctrl_arrows_pos_x: 'center',//
				ctrl_arrows_pos_y: 'top',//
				ctrl_arrows_align_x: 'center',
				ctrl_arrows_align_y: 'top',
				ctrl_arrows_spacing: 2,
				ctrl_arrows_pos_shift_x: 0,
				ctrl_arrows_pos_shift_y: 0,
				ctrl_arrows_full_width: false,
				ctrl_pag_pos_x: 'center',//
				ctrl_pag_pos_y: 'top',//
				ctrl_pag_align_x: 'center',
				ctrl_pag_align_y: 'top',
				ctrl_pag_spacing: 2,
				ctrl_pag_pos_shift_x: 0,
				ctrl_pag_pos_shift_y: 0,
				ctrl_always_visible: false,
				ctrl_padding: 8,
				ctrl_style: 0,
				ctrl_hide_delay: 1000,
				
				autoplay_enable: false,//
				autoplay: false,
				autoplay_interval: 3,
				autoplay_shift_dir: 1,
				autoplay_random_order: false,
				pause_autoplay_on_hover: true,

				init_media_widget: false,
				media_widget_custom_class: '',
				media_selector: null,
				media_height: 'auto',
				media_height_ratio: 100,
				image_stretch_mode: 'auto',
				image_align_x: 'middle',
				image_align_y: 'middle',

				gallery_img_title: false, //
				always_show_img_title: false, //
				img_title: false, 
				show_img_title: false,

				theme: 'light',

				elem_selector: null,
				elem: null,
		    },

		    elems: [],
		    get_framework: function (name)
		    {
		    	return (this.FRAMEWORKS[name] ? this.FRAMEWORKS[name] : this.FRAMEWORKS[this.default_framework]);
		    },

		    get_elem: function (namespace, id)
		    {
		    	widget_manager.get_elem(namespace, id);
		    },

		    extend_defaults: function (ext)
		    {
		    	if ( ! ext)
		    		return;

		    	obj_foreach(this, this.DEFAULTS, function (k, v)
		    	{
		    		if (ext[k])
		    		{
		    			this.DEFAULTS[k] = ext[k];
		    		}
		    	});

		    }
		}
	}();
    ether.builder_defaults ? grid_manager.extend_defaults(ether.builder_defaults.gridslider) : '';
    ether.grid_manager = ether.grid_manager || grid_manager;

	var Grid_slider = function (cfg) 
	{
		var DEF = grid_manager.DEFAULTS;
		var diff = function (a, o1, o2)
		{
			o1 = o1 || cfg;
			o2 = o2 || DEF;


			return o1[a] !== undefined && o1[a] !== o2[a];
		}

		this.cfg = this.cfg || {};


		cfg.ctrl_arrows_align_x = cfg.ctrl_arrows_pos_x;
		cfg.ctrl_arrows_align_y = cfg.ctrl_arrows_pos_y;
		cfg.ctrl_pag_align_x = cfg.ctrl_pag_pos_x;
		cfg.ctrl_pag_align_y = cfg.ctrl_pag_pos_y;

		if (cfg.ctrl)
		{
			cfg.ctrl_arrows = true;
			cfg.ctrl_pag = true;
		} else
		{
			cfg.ctrl_arrows = cfg.ctrl_arrows || diff('ctrl_arrows_align_x') || diff('ctrl_arrows_align_y') || diff('ctrl_arrows_spacing') || diff('ctrl_arrows_pos_shift_x') || diff('ctrl_arrows_pos_shift_y') || cfg.ctrl_arrows_full_width;
			cfg.ctrl_pag = cfg.ctrl_pag || diff('ctrl_pag_align_x') || diff('ctrl_pag_align_y') || diff('ctrl_pag_spacing') || diff('ctrl_pag_pos_shift_x') || diff('ctrl_pag_pos_shift_y') || cfg.ctrl_pag_full_width;

			cfg.ctrl = cfg.ctrl_arrows || cfg.ctrl_pag;

		}

		cfg.slider = cfg.slider || cfg.ctrl;

		cfg.autoplay = (cfg.autoplay || cfg.autoplay_enable) || (cfg.autoplay_interval || cfg.autoplay_shift_dir) !== undefined;

		cfg.img_title = (cfg.img_title || cfg.gallery_img_title);
		cfg.show_img_title = (cfg.always_show_img_title ? 'always' : cfg.show_img_title || cfg.img_title);
		cfg.show_img_title = (cfg.show_img_title === true ? 'on-hover' : cfg.show_img_title);

		cfg.media_height_ratio = (cfg.grid_height_ratio !== 100 ? cfg.grid_height_ratio : cfg.media_height_ratio);
		cfg.media_height = (cfg.grid_height !== 'auto' ? cfg.grid_height : cfg.media_height);
		cfg.init_media_widget = cfg.init_media_widget || cfg.image_stretch_mode !== 'auto' || cfg.media_height !== 'auto' || cfg.image_align_x !== 'middle' || cfg.image_align_y !== 'middle' || cfg.media_height_ratio !== 100;
		cfg.media_width = cfg.image_stretch_mode && cfg.image_stretch_mode !== 'auto' ? '100%' : 'auto';

		
		cfg.col_spacing_size = parseInt(cfg.col_spacing_enable ? (cfg.col_spacing_size !== '' && cfg.col_spacing_size !== undefined ? cfg.col_spacing_size :  DEF.col_spacing_size) : 0);



		$.extend(this.cfg, cfg || {});
	}

	Grid_slider.prototype = new Widget();

	utils.extend_prototype(Grid_slider, {
		construct: function (cfg)
		{
			Widget.prototype.construct.apply(this, cfg);

			$.extend(this.cfg, cfg || {});

			this.namespace = 'gridslider';
			this.elem = this.cfg.elem;
			this.$self = this.cfg.$self;
		},

		update_dom_elem: function (name, $elem, deconstruct, selector)
		{
			selector = selector || this.get_selector(name);

			Widget.prototype.update_dom_elem.apply(this, [name, $elem, deconstruct, selector]);
		},

		is_slider: function ()
		{
			return this.cfg.slider;
		},	

		elem_in_view: function($elem)
		{
			var e_mid = $elem.offset().top + $elem.height() / 2;
			var w_start = $(window).scrollTop();
			var w_end = w_start + $(window).height();


			return ! (e_mid < w_start || e_mid > w_end);
		},

		load_framework: function (name)
		{
			var f = grid_manager.get_framework(name);
			var data = f.dom_data;
			var callback = f.callback;
			var css_styles = f.css_styles;
			var cfg = f.cfg;

			$.extend(this.cfg, cfg);

			this.frameworks ? this.frameworks.push(f) : this.frameworks = [f];

			this.load_dom_data(data);
			this.load_dom_constructor_callback(callback);
			this.generate_stylesheet_content(css_styles);
		},

		detect_starting_setup: function ()
		{
			var detected_framework;
			var detector_result;
			var finish_early;

			this.row_wrap_mode = grid_manager.ROW_WRAP_MODE.ALL;

			if ( ! this.cfg.auto_detect_framework)
				return;



			obj_foreach(this, grid_manager.FRAMEWORKS, function (name, f)
			{
				var d, r;

				if (finish_early)
					return;

				d = f.grid_detector_data;
				r = d ? grid_manager.grid_detector.apply(this, [d.row_filter, d.col_filter, d.depth, d.count_fn]) : undefined;

				if (d && r)
				{
					detected_framework = name;
					detector_result = r;
					finish_early = true;
				}
			});

			if (detected_framework)
			{
				$.extend(true, this.cfg, detector_result);

				this.framework = detected_framework;				
				this.row_wrap_mode = grid_manager.FRAMEWORKS[detected_framework].row_wrap_mode;

			} else
			{

			}
		},

		is_structure_ready: function (type)
		{
			var result = true;
			var constructors = 
			{
				grid: ['elem', 'crumb', 'crumb-wrap', 'crumbs' ],
				slider: ['crumb-group', 'slider-window', 'load-overlay'],
				'slider-controls': ['ctrl-wrap', 'ctrl-pag', 'ctrl-arrows']
			}

			constructors[type].forEach(function (name)
			{

				! this.get_constructor_state(name) ? result = false : '';
			}, this);


			return result;
		},

		is_ready: function (type)
		{
			switch (type)
			{
				case 'slider-window':
				{


					return this.is_structure_ready('slider')// && ( ! this.cfg.advanced_responsive || this.first_rewrap_crumb_group || this.first_advanced_responsiveness_check);
				}
			}
		},

		set_load_overlay: function (state, speed)
		{
			speed = speed || this.cfg.scroll_speed

			this.get_dom('load-overlay')[(state ? 'fadeIn' : 'fadeOut')](speed);
		},

		set_slider_window_height: function (speed, force_reset)
		{
			var self = this;
			var cfg = this.cfg;

			if ( ! this.is_slider())
			{
				return;
			}


			speed = (speed !== undefined ? speed : cfg.scroll_speed);

			if ( ! this.slider_window_height_allow_reset && ! force_reset)
			{
				return;
			}

			this.get_dom('slider-window')
				.stop(true, false)
				.css({
					overflow: 'hidden'
				})
				.animate({
					height: this.get_elem_height()
				}, speed)
				.queue(function ()
				{
					$(this)
						.css({overflow: this.shift_in_progress ? 'hidden' : 'visible'})
						.dequeue();
					self.slider_window_height_allow_reset = true;
				});	
		},

		shift_elem: function (elem_id)
		{
			var cfg = this.cfg;
			var data = this.request_shift_data(elem_id);

			var start_pos = data.start_pos;
			var end_pos = data.end_pos;
			var start_opacity = data.start_opacity;
			var end_opacity = data.end_opacity;
			var $elem = data.$elem;
			var scroll_axis = data.scroll_axis;

			$elem.css({ 
				left: start_pos.x, 
				top: start_pos.y, 
				visibility: 'visible', 
				'z-index': 10, 
				opacity: (scroll_axis !== 'z' ? (cfg.shift_fade ? start_opacity : 1) : start_opacity)
			})
			.animate({ 
				left: end_pos.x, 
				top: end_pos.y, 
				opacity: (scroll_axis !== 'z' ? (cfg.shift_fade ? end_opacity : 1) : end_opacity)
			}, cfg.scroll_speed, cfg.easing, function ()
			{
				if ( cfg.view_pos_inner_step === 'auto')
				{
					$(this).css({
						visibility: end_opacity ? 'visible' : 'hidden',
						'z-index': end_opacity ? 10 : -1
					});
				}
			});
		},

		update_scroll_transition: function ()
		{
			this.scroll_transition = (this.cfg.transition === 'random' ? grid_manager.scroll_transitions.getRandom() : this.cfg.transition);
		},

		update_scroll_axis: function ()
		{
			this.scroll_axis = (this.cfg.scroll_axis === 'random' ? grid_manager.scroll_axes.getRandom() : this.cfg.scroll_axis);
		},

		is_shift_data_obj: function (obj)
		{
			return obj.start_pos !== undefined;
		},

		request_shift_data: function (elem_id, scroll_axis, scroll_transition)
		{
			var cfg = this.cfg;
			var slider_w = this.elem_width + cfg.col_spacing_size;
			var slider_h = this.elem_height + cfg.col_spacing_size;
			var elem_w = this.get_crumb_wrap_width();
			var elem_h = this.get_crumb_wrap_height();
			var pos_out;
			var pos_in;
			var start_pos;
			var end_pos;
			var $elem;
			var x_mod;
			var y_mod;



			elem_id === undefined ? elem_id = 1 : '';

			scroll_axis = scroll_axis || this.scroll_axis;
			scroll_transition = scroll_transition || this.scroll_transition;

			x_mod = grid_manager.scroll_axes_dir_map[scroll_axis]['x'];
			y_mod = grid_manager.scroll_axes_dir_map[scroll_axis]['y'];

			$elem = this.get_dom('crumb-group').eq(elem_id);

			if (cfg.view_pos_inner_step === 'auto')
			{
				pos_in = 
				{
					x: 0,
					y: 0
				}

				pos_out =
				{
					x: this.shift_dir * slider_w * x_mod,
					y: this.shift_dir * slider_h * y_mod
				}

				start_pos = (elem_id === this.view_pos ? pos_out : pos_in);
				end_pos = (elem_id === this.view_pos ? pos_in : pos_out);

				if (elem_id !== this.view_pos)
				{
					if (scroll_transition === 'switch' || scroll_transition === 'swap' || scroll_transition === 'shuffle')	
					{
						start_pos.x *= -1;
						start_pos.y *= -1;
					}

					end_pos.x *= -1;
					end_pos.y *= -1;
				}
			} else
			{
				pos_in = { //both ways are dirty // second works as long as inner_view_pos delta === 1


					x: (this.view_pos_inner - this.shift_dir) * elem_w * x_mod * -1,
					y: (this.view_pos_inner - this.shift_dir) * elem_h * y_mod * -1
				}

				pos_out = {
					x: this.view_pos_inner * elem_w * x_mod * -1,
					y: this.view_pos_inner * elem_h * y_mod * -1
				}

				start_pos = pos_in;
				end_pos = pos_out;

				if (elem_id !== this.view_pos)
				{
					start_pos.x += slider_w * x_mod;
					start_pos.y += slider_h * y_mod;
					end_pos.x += slider_w * x_mod;
					end_pos.y += slider_h * y_mod;
				} 
			}


			return {
				start_pos: start_pos,
				end_pos: end_pos,
				start_opacity: elem_id === this.view_pos ? 0 : 1, // fix? 
				end_opacity: elem_id === this.view_pos ? 1 : 0, // fix?
				$elem: $elem,
				scroll_axis: scroll_axis
			}
		},

		update_shift_data: function ()
		{
			this.update_scroll_transition();
			this.update_scroll_axis();
		},

		on_shift_start: function ()
		{
			this.shift_in_progress = 1;
			event_manager.trigger(this.$self, 'apply_shift_start');
		},

		on_shift_end: function ()
		{
			this.shift_in_progress = 0;


			event_manager.trigger(this.$self, 'apply_shift_end');
		},

		apply_shift: function (dir)
		{	
			var self = this;
			var cfg = this.cfg;
			var $self = this.$self;

			this.update_shift_data();

			this.on_shift_start();

			this.shift_elem(this.view_pos);
			this.shift_elem(this.prev_view_pos);

			this.set_slider_window_height();
			this.update_slider_controls(undefined, undefined, true);
	
			scheduler.set('shift_timeout_' + self.id, cfg.scroll_speed, function ()
			{
				self.on_shift_end();
			});

			event_manager.trigger($self, 'apply_shift');
		},

		get_shift_dir: function (view_pos, shift_type, shift_dest)
		{
			return (shift_type === 'absolute' ? (shift_dest > view_pos ? 1 : shift_dest < view_pos ? -1 : 0) : shift_dest);
		},

		is_first_view_pos: function (view_pos)
		{
			view_pos === undefined ? view_pos = this.view_pos : '';
			return view_pos === 0 || view_pos === undefined;
		},

		is_last_view_pos: function (view_pos)
		{
			view_pos === undefined ? view_pos = this.view_pos : '';
			return view_pos === this.crumb_group_count - 1;
		},

		is_outermost_view_pos: function (view_pos, side)
		{
			view_pos === undefined ? view_pos = this.view_pos : '';

			return ! side ? (this.is_first_view_pos(view_pos) || this.is_last_view_pos(view_pos)) : (side === -1 ? this.is_first_view_pos(view_pos) : side === 1 ? this.is_last_view_pos(view_pos) : undefined);
		},

		is_loop: function (forced_loop)
		{
			return this.cfg.loop || forced_loop;
		},

		update_view_pos: function ()
		{
			if (this.view_pos === undefined || this.view_pos >= this.crumb_group_count)
			{
				this.init_shift('absolute', this.crumb_group_count - 1);
			} else
			{
				this.set_visible_crumb_group();
			}

		},

		get_next_view_pos: function (view_pos, shift_type, shift_dest, loop, count)
		{


			if (shift_type === 'relative')
			{
				view_pos += shift_dest;

				view_pos = view_pos < 0 ? loop ? count - 1 : 0 : view_pos >= count ? loop ? 0 : count - 1 : view_pos;
			} else if (shift_type === 'absolute')
			{
				view_pos = shift_dest;
			}

			return view_pos;
		},

		set_shift_dir: function (dir)
		{
			this.shift_dir = dir;
		},

		set_view_pos_inner: function (view_pos_inner)
		{
			this.prev_view_pos_inner = this.view_pos_inner || 0;
			this.view_pos_inner = view_pos_inner;

		},

		set_view_pos: function (view_pos)
		{
			this.prev_view_pos = this.view_pos;
			this.view_pos = view_pos;

		},





		init_shift: function (shift_type, shift_dest, forced_loop)
		{
			var cfg = this.cfg;
			var view_pos = this.view_pos;
			var next_view_pos;
			var view_pos_inner = this.view_pos_inner || 0;
			var next_view_pos_inner = view_pos_inner;
			var loop = this.is_loop(forced_loop);
			var cols = cfg.cols;
			var rows = cfg.rows;
			var pos_inner_max = cfg.scroll_axis === 'x' ? cfg.cols : cfg.scroll_axis === 'y' ? cfg.rows : -1; 

			if (
				shift_type === 'absolute' && shift_dest === this.view_pos
				|| this.get_crumb_group_count() === 1
				|| this.shift_in_progress
			)
			{
				return false;
			}

			if (shift_type === 'relative' && cfg.random_order)
			{
				shift_type = 'absolute';
				shift_dest = Math.floor(Math.random() * this.crumb_group_count);
			}

			this.set_shift_dir(this.get_shift_dir(view_pos, shift_type, shift_dest));

			next_view_pos = this.get_next_view_pos(view_pos, shift_type, shift_dest, loop, this.crumb_group_count);

			if (
				cfg.view_pos_inner_step === 'auto' || 
				cfg.view_pos_inner_step !== 'auto' && (cfg.transition !== 'slide' || cfg.scroll_axis === 'z')
				)
			{
				if (next_view_pos === view_pos)
				{
					return;
				} else
				{
					this.set_view_pos_inner(0);
					this.set_view_pos(next_view_pos);
				}
			} else
			{



				next_view_pos_inner += this.shift_dir * cfg.view_pos_inner_step;

				if (
					! loop && this.is_last_view_pos(next_view_pos) && next_view_pos_inner > cols ||
					! loop && this.is_first_view_pos(view_pos) && next_view_pos_inner < 0
				)
				{

					return;
				} else
				{
					if (next_view_pos_inner > cols)
					{

						view_pos_inner = 1;
						this.set_view_pos_inner(view_pos_inner);
						this.set_view_pos(next_view_pos);
					} else if (next_view_pos_inner < 0)
					{

						view_pos_inner = cols - 1;
						this.set_view_pos_inner(view_pos_inner);
						this.set_view_pos(next_view_pos);
					} else
					{

						view_pos_inner = next_view_pos_inner;
						this.set_view_pos_inner(view_pos_inner);
					}
				}
			}


			this.apply_shift();
			this.resume_autoplay();//pending update
		},

		init_slider_mousewheel_support: function ()
		{
			var self = this;
			var cfg = this.cfg;

			$(this.elem)
				.on('mousewheel', function (event, delta, deltaX, deltaY)
				{
					var shift_dir;

					if (cfg.scroll_on_mousewheel)
					{
						shift_dir = (deltaY !== 0 && deltaY < 0 || deltaX !== 0 && deltaX > 0 ? 1 : -1);
						self.init_shift('relative', shift_dir);
						event.preventDefault();
					}
				});	
		},

		init_slider_swipe_support: function ()
		{
			if (
				(! isie || iev > 8)
				|| ! $(this.elem).swipe
			)
			{
				return;
			}

			$(this.elem)
				.swipe({
				     swipeLeft: function()
				     {
				     	this.init_shift('relative', 1);
				     },

				     swipeRight: function() {
				     	this.init_shift('relative', -1);
				     }
				});
		},

		init_external_controls: function ()
		{
			var self = this;
			var cfg = this.cfg;

			if (! cfg.ctrl_external)
				return;

			cfg.ctrl_external.forEach(function (data)
			{
				var $elem = data[0];
				var destination = data[1];
				var shifttype = (typeof destination === 'number' ? 'absolute' : 'relative');
				var destination = (typeof destination === 'number' ? destination : (destination === 'prev' ? '-1' : '1'));

				$elem
					.attr('data-shifttype', shifttype)
					.attr('data-shiftdest', destination)
					.on('click', function (e)
					{
						self.init_shift($(this).data('shifttype'), $(this).data('shiftdest'));
						self.set_slider_controls_wrap_visibility(true);
						e.preventDefault();
					});
			});
		},

		init_slider_functions: function ()
		{
			var cfg = this.cfg;
			var self = this;

			this.init_autoplay();	
			this.init_slider_swipe_support();
			this.init_slider_mousewheel_support();
			this.init_external_controls();
		},

		set_visible_crumb_group: function ()
		{
			this.get_dom('crumb-group')
				.css({
					'z-index': -1, visibility: 'hidden'
				})
				.eq(this.view_pos)
					.css({
						'z-index': 10, visibility: 'visible'
					});
		},

		get_elem_height: function (id)
		{
			id = id || this.view_pos;

			this.elem_height = ( ! this.is_slider() ? $(this.elem).outerHeight() : this.get_crumb_group_height(id));


			return this.elem_height;
		},

		get_elem_width: function ()
		{
			return $(this.elem).outerWidth()
		},

		get_crumbs_capacity: function ()
		{
			var mode = grid_manager.ROW_WRAP_MODE;

			var col_count = (this.is_slider() ? this.real_col_count : this.cfg.cols);
			var row_count = (this.is_slider() ? this.real_row_count : this.cfg.rows);

			var capacity = (this.row_wrap_mode === mode.ROW ? col_count : (this.row_wrap_mode === mode.GROUP ? col_count * row_count : this.get_dom('crumb-wrap').length));  //ignore responsiveness when not a slider


			return capacity;
		},

		get_crumb_wrap_width: function ()
		{
			return this.get_dom('crumb-wrap').outerWidth();
		},

		get_crumb_wrap_height: function ()
		{
			return this.get_dom('crumb-wrap').outerHeight();
		},
		
		get_crumb_group_height: function (id)
		{

			return this.get_dom('crumb-group').eq(id || this.view_pos).outerHeight(); //mind the responsiveness
		},

		get_crumb_group_count: function ()
		{
			return this.get_dom('crumb-group').length;
		},

		get_crumb_group_capacity: function ()
		{
			return this.real_row_count * this.real_col_count;
		},

		get_crumb_group_size: function (id)
		{
			return this.get_dom('crumb-group').eq(id).find(prefix_class('crumb-wrap' + '-' + this.id)).length
		},

		is_over_capacity_crumb_group: function (id)
		{
			return this.get_crumb_group_size(id || 0) > this.get_crumb_group_capacity();
		},

		is_not_full_crumb_group: function (id)
		{
			return this.get_crumb_group_size(id || 0) > 0 && this.get_crumb_group_size(id || 0) < this.get_crumb_group_capacity()
		},

		is_empty_crumb_group: function (id)
		{
			return ! this.get_crumb_group_size(id || 0); //fix checking crumb groups nob
		},





		update_advanced_responsiveness: function ()
		{
			var $self = this.$self;
			
			if ( ! this.cfg.advanced_responsive)
				return;

			this.cache_key_change('real_row_count', $(window).width() < 580 ? 1 : this.cfg.rows);
			this.is_key_changed('real_row_count') ? event_manager.trigger($self, 'real_row_count_change') : '';
			this.clear_key_change_cache(); 

			if (this.real_row_count > 1 && ($(window).height() / this.get_elem_height() < 1.25 || this.get_elem_height() / this.elem_width > 1.5))
			{
				this.cache_key_change('real_row_count', this.real_row_count > 1 ? this.real_row_count - 1 : this.real_row_count);
				this.is_key_changed('real_row_count') ? event_manager.trigger($self, 'real_row_count_change') : '';
				this.clear_key_change_cache(); 
			}

			if ( ! this.first_advanced_responsiveness_check)
			{
				this.first_advanced_responsiveness_check = true;
				event_manager.trigger($self, 'first_advanced_responsiveness_check');
			}
		},

		rewrap_crumb_group: function ()
		{
			var self = this;
			var $self = this.$self;
			var update;



			this.get_dom('crumb-group').each(function (id)
			{
				if (update)
				{
					return;
				}


				if (self.is_over_capacity_crumb_group(id) || self.is_empty_crumb_group(id) || self.is_not_full_crumb_group(id) && id < self.get_crumb_group_count() - 1)
				{
					update = true;
				}
			});

			if ( ! update)
			{

				return;
			}


			this.get_dom('slider-window').css({
				overflow: 'hidden'
			});

			this.construct_dom('crumb-group', true);
			this.construct_dom('crumb-group');

			event_manager.trigger($self, 'rewrap_crumb_group_end');

			if ( ! this.first_rewrap_crumb_group)
			{
				this.first_rewrap_crumb_group = true;
				event_manager.trigger($self, 'first_rewrap_crumb_group_end');				
			}
		},

		set_slider_controls_wrap_visibility: function (type, speed, force_reset)
		{
			var self = this;
			var cfg = this.cfg;



			if ( ! cfg.ctrl || ! this.slider_controls_allow_reset && ! force_reset)// || ! this.is_structure_ready('slider-controls'))
			{
				return;
			}


			type = (this.crumb_group_count > 1 && type ? true : false);
			speed === undefined ? speed = cfg.scroll_speed : '';
			speed = 500


			scheduler.set('controls_wrap_visibility_timeout_' + self.id,  type ? 1 : cfg.ctrl_hide_delay || 1000, function ()
			{
				self.get_dom('ctrl-wrap')
					.stop(true, true)
					['fade' + (type ? 'In' : 'Out')](
						speed,
						function () 
						{

							self.ctrl_visible = type;
							self.slider_controls_allow_reset = true;
						});
			});
		},

		slider_controls_have_equal_alignment: function ()
		{
			var cfg = this.cfg;
			
			return (cfg.ctrl_arrows_align_x === cfg.ctrl_pag_align_x && cfg.ctrl_arrows_align_y === cfg.ctrl_pag_align_y && ! cfg.ctrl_arrows_full_width);
		},

		get_slider_controls_width: function (type)
		{
			var cfg = this.cfg;

			return (type === 'pag' ? this[type].child_width * this.crumb_group_count : (cfg.ctrl_arrows_full_width ? this.elem_width - 2 * cfg.ctrl_padding : this[type].child_width * 2))
		},

		calculate_slider_controls_pos: function (type)
		{
			var cfg = this.cfg;
			var type_cfg = this[type];
			var elem_height = this.get_elem_height();

			if ( ! this.cfg['ctrl_' + type])// || ! this.is_structure_ready('slider-controls'))
			{
				return;
			}

			type_cfg['width'] = this.get_slider_controls_width(type);

			type_cfg.pos.left = (type_cfg.align.x === 'left' ? cfg.ctrl_padding : (type_cfg.align.x === 'right' ? 'auto' : this.elem_width / 2 - type_cfg['width'] / 2));
			type_cfg.pos.right = (type_cfg.align.x === 'right' ? cfg.ctrl_padding : (type_cfg.align.x === 'left' ? 'auto' : this.elem_width / 2 - type_cfg['width'] / 2));
			type_cfg.pos.top = (type_cfg.align.y === 'top' ? cfg.ctrl_padding : (type_cfg.align.y === 'bottom' ? 'auto' : this.elem_height / 2 - type_cfg['height'] / 2)) + (type_cfg.align.y === 'top' ? (type === 'pag' && type_cfg.align.y === 'top' && this.slider_controls_have_equal_alignment() ? this.arrows.height : 0) : '');
			type_cfg.pos.bottom = (type_cfg.align.y === 'bottom' ? cfg.ctrl_padding : (type_cfg.align.y === 'top' ? 'auto' : this.elem_height / 2 - type_cfg['height'] / 2)) + (type_cfg.align.y === 'bottom' ? (type === 'arrows' && type_cfg.align.y === 'bottom' && this.slider_controls_have_equal_alignment() ? this.pag.height : 0) : '');

		},

		update_slider_controls_type: function (type, speed)
		{	
			if ( ! this.cfg['ctrl_' + type])// || ! this.is_structure_ready('slider-controls'))
			{
				return;
			}

			speed = (speed === undefined ? this.cfg.scroll_speed : speed);

			this.calculate_slider_controls_pos(type);

			animate_dom(this.get_dom('ctrl-' + type), {
				width: this[type].width + 1, //hackor
				height: this[type].height,
				top: this[type].pos.top,
				right: this[type].pos.right,
				bottom: this[type].pos.bottom,
				left: this[type].pos.left
			}, speed);

			this['update_slider_controls_' + type + '_dom_state']();
		},

		update_slider_controls_pag_dom_state: function ()
		{
			var self = this;
			var cfg = this.cfg;

			if ( ! cfg.ctrl_pag)// || ! this.is_structure_ready('slider-controls'))
			{
				return;
			}

			if (this.get_dom('ctrl-pag').length && this.get_dom('ctrl-pag').children().length < this.crumb_group_count)
			{

				this.construct_dom('ctrl-pag', true);
				this.construct_dom('ctrl-pag');
				this.update_slider_controls_type('pag', true);
			}

			this.get_dom('ctrl-pag')
				.children()
					.removeClass(prefix('current'))
					.eq(this.view_pos).addClass(prefix('current'))
					.end()
					.css({display: 'block'})
					.slice(this.crumb_group_count)
						.css({display: 'none'})
					.end()
				.end()
				.css({
					width: function ()
					{
						return self.pag.width;
					}
				});
		},

		update_slider_controls_arrows_dom_state: function ()
		{
			var cfg = this.cfg;

			var $ctrl_prev;
			var $ctrl_next;
	
			if (cfg.ctrl_arrows)// && ! cfg.loop && this.is_structure_ready('slider-controls'))
			{
				$ctrl_prev = this.get_dom('ctrl-arrows').children(prefix_class('ctrl-prev'));
				$ctrl_next = this.get_dom('ctrl-arrows').children(prefix_class('ctrl-next'));

				{
					$ctrl_next[this.view_pos === this.crumb_group_count - 1 ? 'addClass' : 'removeClass'](prefix('disabled'));
					$ctrl_prev[this.view_pos === 0 ? 'addClass' : 'removeClass' ](prefix('disabled'));
				}
			}
		},

		update_slider_controls: function (speed, force_reset, keep_visibility)
		{
			var self = this;
			var cfg = this.cfg;



			if ( ! cfg.ctrl || ! this.is_slider() || ! this.slider_ctrl_initialized)// || ! this.is_structure_ready('slider-controls')) //check why not ready when slider ready
			{
				return false;
			}
			
			this.update_slider_controls_type('pag', speed);							
			this.update_slider_controls_type('arrows', speed);
			this.set_slider_controls_wrap_visibility(( ! keep_visibility ? (cfg.ctrl_always_visible ? true : false) : self.ctrl_visible), speed, force_reset);
		},

		init_slider_controls_data: function ()
		{
			var self = this;
			var cfg = this.cfg;

			if ( ! cfg.ctrl)// || ! this.is_structure_ready('slider-controls'))
			{
				return;
			}

			this.pag = this.pag || {};
			this.arrows = this.arrows || {};

			this.pag.child_width = this.get_dom('ctrl-pag').children().outerWidth() + cfg.ctrl_pag_spacing;
			this.pag.width = this.pag.child_width * this.crumb_group_count;
			this.pag.height = this.pag.child_width;
			this.pag.pos = {};

			this.arrows.child_width = this.get_dom('ctrl-arrows').children().outerWidth() + cfg.ctrl_arrows_spacing; //clean up names nitter
			this.arrows.width = (cfg.ctrl_arrows_full_width ? this.elem_width - 2 * cfg.ctrl_padding : this.arrows.child_width * 2); //clean up names nitter
			this.arrows.height = this.arrows.child_width + cfg.ctrl_arrows_spacing; //clean up names nitter
			this.arrows.pos = {};

			this.pag.align = 
			{
				x: cfg.ctrl_pag_align_x,
				y: cfg.ctrl_pag_align_y,
			},

			this.arrows.align =
			{
				x: cfg.ctrl_arrows_align_x,
				y: cfg.ctrl_arrows_align_y
			}
		},

		init_slider_controls_functions: function ()
		{
			var self = this;
			var cfg = this.cfg;

			if ( ! cfg.ctrl)// || ! this.is_structure_ready('slider-controls'))
			{
				return;
			}


			this.get_dom('ctrl-wrap')
				.on('click', prefix_class('ctrl' + '-' + this.id), function()
				{
					this.onselectstart = function()
					{
						return false;
					}


					self.init_shift($(this).data('shifttype'), $(this).data('shiftdest'));


					return false;
				})
				.find(prefix_class('ctrl' + '-' + this.id))
					.attr('unselectable', 'on')
					.css({
						'-ms-user-select':'none',
						'-moz-user-select':'none',
						'-webkit-user-select':'none',
						'user-select':'none'
					});				
		},

		init_slider_controls_behaviour: function ()
		{
			var self = this;
			var cfg = this.cfg;
	
			if (cfg.ctrl_always_visible)
				return;


			$(this.elem)
				.on('mouseenter', function ()
				{
					self.set_slider_controls_wrap_visibility(true);
				})
				.on('mouseleave', function ()
				{
					self.set_slider_controls_wrap_visibility();
				});
		},

		init_slider_controls: function ()
		{
			var cfg = this.cfg;
	
			if ( ! cfg.ctrl)// || ! this.is_structure_ready('slider'))
			{
				return false;
			}


			this.slider_ctrl_initialized = true;

			this.init_slider_controls_data();
			this.init_slider_controls_behaviour();
			this.init_slider_controls_functions();
		},

		clear_key_change_cache: function (key)
		{
			var self = this;

			if (key)
			{

				key = key.replace('_change', '');
				delete this.changed_key_cache[key];
			} else
			{
				obj_foreach(this, this.changed_key_cache, function (key, elem) { self.clear_key_change_cache(key); });
			}
		},

		cache_key_change: function (key, value)
		{
			var current;
			var set;

			this.changed_key_cache = this.changed_key_cache || {};

			set = ( ! Array.isArray(key) ? [[key, value]] : key);

			set.forEach(function (elem)
			{
				key = elem[0];
				value = elem[1];
				value = (typeof value === 'function' ? value.apply(this) : value);

				current = this[key];

				this[key] = value


				if ( ! this.changed_key_cache[key] && current !== this[key])// && current !== undefined) //assume first init when current === undefined and don't record change
				{

					this.changed_key_cache[key] = true;
				}
			}, this);
		},

		is_key_changed: function (key)
		{

			return this.changed_key_cache[key];
		},

		update_responsive_data: function ()
		{
			var self = this;
			var cfg = this.cfg;
			var $self = cfg.$self;

			var a = [
				['elem_width', function () { return $(self.elem).outerWidth();}], //that good nig or use width() instead?
				['real_col_count', function () { return Math.round(self.elem_width / self.get_dom('crumb-wrap').outerWidth()); }],
				['real_row_count', function () { return ($(window).width() < 580 ? 1 : self.cfg.rows); }], //limit row count on small screens. Affects slider only.
				['crumb_group_capacity', function () { return self.get_crumb_group_capacity(); }], //assume responsiveness of the structure
			];

			self.cache_key_change(a);

			var events = [];
			obj_foreach(self, self.changed_key_cache, function (key, elem)
			{
				events.push(key + '_change');
			});

			events.forEach(function (event)
			{
				event_manager.trigger($self, event)
			});

			self.clear_key_change_cache(); 
		},

		has_custom_col_spacing: function ()
		{
			var cfg = this.cfg;

			return cfg.col_spacing_size !== grid_manager.DEFAULTS.col_spacing_size && cfg.col_spacing_enable;
		},

		is_empty_slider: function ()
		{
			return (this.is_slider() && ! this.get_dom('crumb-group').length);
		},

		add_element: function ($elem, id, callback, remove_source)
		{
			var $crumb = $elem.clone(true, true);
			var append = (id >= 0 ? 'before' : 'after');
			var $added;
			var $self = this.$self;

			id = (id !== undefined ? id : -1);

			this.get_dom('slider-window').css({
				overflow: 'hidden'
			});


			$added = this.get_dom('crumb-wrap').eq(id)[append]($crumb)[append === 'before' ? 'prev' : 'next']();

			this.construct_dom('crumb', undefined, $added);
			this.construct_dom('crumb-wrap', undefined, $added);

			this.crumb_count = this.get_dom('crumb-wrap').length; //dirt!

			callback ? callback.apply(this) : '';
			remove_source ? $elem.remove() : '';

			event_manager.trigger($self, 'add_crumb', [$added]);
		},

		add_elements: function ($add, start_id, remove_source, callback)
		{
			var self = this;
			var cfg = this.cfg;

			$add.each(function (id)
			{
				var insert_id = (start_id !== undefined ? start_id + id : -1)

				self.add_element($(this), insert_id);
			});

			callback ? callback.apply(this) : '';
			remove_source ? $add.remove() : '';
		},

		remove_element: function (id, callback)
		{
			var $self = this.$self;

			if (this.get_dom('crumb-wrap').eq(id))
			{
				this.remove_dom('crumb-wrap', id, this.get_dom('crumbs'));

				this.crumb_count = this.get_dom('crumb-wrap').length;  //HHHM

				callback ? callback.apply(this) : '';

				event_manager.trigger($self, 'remove_crumb');
			}
		},

		remove_elements: function (id, callback)
		{
			var self = this;
			var cfg = this.cfg;

			id = (typeof id === 'number' ? [id] : id);

			id.forEach(function (elem_id)
			{
				self.remove_element(elem_id);
			});	

			callback ? callback.apply(this) : '';
		},		
		pause_autoplay: function ()
		{
			var cfg = this.cfg;

			if ( ! this.autoplay_in_progress || ! cfg.autoplay_enable)
			{
				return;
			}


			this.autoplay_in_progress = false;

			scheduler.clear('autoplay_timeout_' + this.id);
		},		
		resume_autoplay: function ()
		{
			var self = this;
			var cfg = this.cfg;

			if ( this.autoplay_in_progress || ! cfg.autoplay_enable)
			{
				return;
			}


			scheduler.clear('autoplay_timeout_' + this.id);

			if (cfg.autoplay_enable)
			{
				this.autoplay_in_progress = true;

				scheduler.set('autoplay_timeout_' + self.id, cfg.autoplay_interval * 1000, function ()
				{
					var shift_type = 'relative';
					var shift_dest = cfg.autoplay_shift_dir;

					if (cfg.autoplay_random_order)
					{
						shift_type = 'absolute';
						shift_dest = Math.floor(Math.random() * this.crumb_group_count);
					}

					self.init_shift(shift_type, shift_dest, true);
					self.autoplay_in_progress = false;
					self.resume_autoplay();
				});
			}
		},

		update_autoplay: function ()
		{

			this.elem_in_view($(this.elem)) ? this.resume_autoplay() : this.pause_autoplay();
		},

		init_autoplay: function ()
		{
			var self = this;
			var cfg = this.cfg;

			if ( ! cfg.autoplay_enable)
			{
				return;
			}


			$(window)
				.on(self.get_namespace('load'), function ()
				{
					if (self.elem_in_view($(self.elem)))
					{
						self.resume_autoplay();
					}
				})
				.on(self.get_namespace('blur'), function ()
				{
					self.pause_autoplay();
				})
				.on(self.get_namespace('focus'), function ()
				{
					self.resume_autoplay();
				})
				.on(self.get_namespace('scroll'), function ()
				{
					scheduler.set('autoplay_scroll_timeout_' + self.id, 500, function ()
					{
						self.update_autoplay();	
					});
				});

			if (cfg.pause_autoplay_on_hover)
			{
				$(this.elem)
					.on(self.get_namespace('mouseenter'), function ()
					{
						self.pause_autoplay();
					})
					.on(self.get_namespace('mouseleave'), function ()
					{
						self.resume_autoplay();
					});
			}
		},

		generate_stylesheet_content: function (callback)
		{
			if ( ! callback)
				return;

			callback.apply(this);
			css_generator.update_css();
		},

		init_events: function ()
		{
			var self = this;
			var $self = this.$self;

			event_manager
				.on($self, 'slider_structure_init', function ()
				{
					self.update_view_pos();
					self.init_slider_controls();
					self.init_slider_functions();
				})
				.on($self, 'slider_structure_init_crumb_group', function ()
				{
					self.update_view_pos();
				})
				.on($self, 'crumb_group_capacity_change real_row_count_change, add_crumb remove_crumb', function ()
				{
					if ( ! self.is_structure_ready('slider'))
					{
						return false;
					}

					self.rewrap_crumb_group();
				})
				.on($self, 'elem_width_change rewrap_crumb_group_end', function ()
				{
					if ( ! self.is_structure_ready('slider'))
					{
						return;
					}

					self.update_slider_controls(0); //slave
					self.set_slider_window_height(0); //slave
				})
				.on($self, 'slider_structure_init first_rewrap_crumb_group_end first_advanced_responsiveness_check', function ()
				{
					self.is_ready('slider-window') ? event_manager.trigger($self, 'slider_window_ready') : '';
				})
				.on($self, 'slider_window_ready', function () 
				{







					image_loader.on_images_load_end($(self.elem).find('img'), function ()
					{
						if (self.first_slider_window_height_set)
						{
							return false;
						}

						self.first_slider_window_height_set = true;
						self.update_slider_controls(undefined, true); //master
						self.set_slider_window_height(undefined, true); //master
						self.set_load_overlay();
					});
				})
				.on($self, 'slider_window_update', function () 
				{
					scheduler.set('slider_window_update.' + self.get_namespace(), 500, function ()
					{
						if ( ! self.first_slider_window_height_set)
						{
							return false;
						}

						self.update_slider_controls();
						self.set_slider_window_height();
					});
				})
				.on($(window), self.get_namespace('resize'), function ()
				{
					scheduler.set('update_responsive_data_' + self.id, 150, self.update_responsive_data, self);
					scheduler.set('update_advanced_responsiveness_' + self.id, 150, self.update_advanced_responsiveness, self);








					self.get_dom('slider-window').css({
						overflow: 'hidden'
					});
				});
		},

		deinit_events: function ()
		{
			$(window).unbind(this.get_namespace('resize'));
		},

		set_structure: function (type, mode)
		{
			var $self = this.$self;
			var order = grid_manager.dom_constructors_init_order[type].apply(this, [mode]);

			if (type === 'grid' && ! this.cfg.init_grid ||
				type === 'slider' && ! this.cfg.slider)
				return;

			order.forEach(function (elem)
			{
				this.construct_dom(elem, ! mode);	
			}, this);

			event_manager.trigger($self, type + '_' + 'structure_init');
			this[type + '_' + 'structure_init'] = mode;
		},

		set_media_widget: function ()
		{
			var self = this;
			var cfg = this.cfg;
			var $self = this.$self;
			var $img;

			if ( ! (cfg.init_media_widget || cfg.img_title))
				return;

			$img = ( ! cfg.media_selector ? (this.get_dom('crumb').length ? this.get_dom('crumb') : $self.children('img')) : $self.find(cfg.media_selector));
			
			$img = $img.filter('img').add($img.find('img'));

			if ( ! $img.length)
				return;

			event_manager.on($img, 'media-widget-update', function (media)
			{
				event_manager.trigger($self, 'slider_window_update');
			});

			$img.mediaWidget({
				$img: $img, 
				use_parent_wrap: cfg.use_parent_wrap,
				wrap_width: cfg.media_width,
				wrap_height: cfg.media_height, //mind the name difference!
				wrap_height_ratio: cfg.media_height_ratio, //mind the name difference!
				image_stretch_mode: cfg.image_stretch_mode, 
				image_align_x: cfg.image_align_x, 
				image_align_y: cfg.image_align_y, 
				media_widget_custom_class: cfg.media_widget_custom_class,

				show_img_title: cfg.show_img_title, 
				img_title_alignment_y: cfg.img_title_alignment_y,
				img_title_custom_class: cfg.img_title_custom_class
			})
		},

		set_external_widgets: function ()
		{
			this.set_media_widget();
		},

		init: function (cfg)
		{
			var self = this;

			this.construct(cfg);

			this.register();


			this.init_events();
			
			this.detect_starting_setup();
			this.load_dom_constructors(grid_manager.dom_constructors);
			this.load_framework('core');
			this.load_framework(this.cfg.framework);

			this.set_structure('grid', true);
			this.set_structure('slider', true);
			this.set_external_widgets();


		},

		deinit: function ()
		{
			this.unregister();
			this.set_grid_structure(false);
			this.set_slider_structure(false);
		 	this.deinit_events();
		},		
		reinit: function ()
		{



		}
	});

	$.fn.ether_grid_slider = function (options) 
	{
		var options;
        var defaults;

        if (options)
        {
	        defaults = jQuery.extend(true, {}, grid_manager.DEFAULTS);
			options = $.extend(defaults, options);

	        options.elem_selector = $(this).selector;

			return this.each(function()
			{
				var self = this;

				options.elem = self;
				options.self = self;
				options.$self = $(self);

				new Grid_slider(jQuery.extend(true, {}, options)).init();
			});
		} else
		{
			return widget_manager.get_elem('gridslider', $(this));
		}
    }

    $.fn.gridSlider = $.fn.ether_grid_slider; //temp backward compatibility
	ether.Grid_slider = ether.Grid_slider || Grid_slider;

})(jQuery);

(function ($)
{
	var utils = ether.utils;
	var widget_manager = ether.widget_manager;
	var Widget = ether.Widget;

	var prefix = utils.prefix;
	var prefix_class = utils.prefix_class;
	var scheduler = utils.scheduler;

	var DEFAULTS = {
		user_id: undefined,
		start_tabs: undefined,
		current_tabs: undefined,
		constrain: true,
		show_tab_state_marker: false, //accordion only
		enable_current_toggle: false,
		type: 'tab',
		scroll_speed: 500
	};

	var Tab_widget = function Tab_widget ()
	{
	}

	Tab_widget.prototype = new Widget();

	utils.extend_prototype(Tab_widget, 
	{
		construct: function (cfg)
		{
			this.constructor.prototype.construct ? this.constructor.prototype.construct.call(this, cfg) : '';

			this.namespace = this.get_cfg('type') + '_' + 'widget';
		},

		get_namespace: function (name)
		{
			var id = this.get_cfg('user_id');

			id === undefined ? id = this.id : '';

			return (name ? name + '.' : '') + this.namespace + '_' + id;
		},

		init_ctrl_container: function ()
		{
			var $elem = $('<div>')
				.addClass(prefix([
					'block-ctrl',
					'block-ctrl' + '-' + this.id,
					'block-ctrl-style-1'
				], ' '));
			var $title = this.get_dom('title');

			this.get_cfg('type') === 'tab' ? $elem.insertBefore($(this.get_cfg('elem')).children().eq(0)).append($title) : $title.wrap($elem.clone(true, true));

			this.set_dom('block-ctrl');

			return $elem;
		},

		init_content_container: function ()
		{
			var $elem = $('<div>')
				.addClass(prefix([
					'block-content',
					'block-content' + '-' + this.id,
					'block-content-style-1'
				], ' '));

			this.get_cfg('type') === 'tab' ? $elem.insertBefore($(this.get_cfg('elem')).children().eq(0)).append(this.get_dom('content')) : this.get_dom('content').wrap($elem.clone(true, true));

			this.set_dom('block-content', $elem);

			return $elem;
		},

		init_content: function ()
		{
			var self = this;
			var $elem = $(this.get_cfg('elem'));

			$(this.get_cfg('elem')).addClass(prefix('id' + '-' + this.id));

			this.set_dom('elem', $elem);
			this.set_dom('title', $elem.children(prefix_class('title')));
			this.set_dom('content', $elem.children(prefix_class('content')));

			this.init_content_container();
			this.init_ctrl_container();

			this.get_dom('content')
				.hide()
			this.get_dom('title')
				.on('click', function ()
				{
					self.update_tab_data(self.get_cfg('type') === 'tab' ? $(this).index() : $(this).parent().index() / 2, true, true);
				});

			if (this.get_cfg('type') === 'acc' && this.get_cfg('show_tab_state_marker') === true)
			{
				this.get_dom('title').append('<span class="' + prefix('state-marker') + ' dashicons dashicons-arrow-left"></span>');




			}
		},

		slug_expr: function ()
		{
			return new RegExp('(' + this.get_namespace() + ')' + '(?:\\d|_)+');
		},

		generate_slug: function (data)
		{
			data = data.map(function (tab_id)
			{
				return tab_id ? 1 : 0;
			});

			var slug = this.get_namespace() + '_' + data.join('_');

			return slug;
		},

		parse_slug: function (string)
		{
			var slug = string.match(this.slug_expr());
			var id;
			var tabs = [];

			if ( ! slug)
				return;

			id = slug[1];

			slug[0].replace(id, '').split('_').forEach(function (id)
			{
				var tab_state = id == 0 ? false : true;

				id !== '' ? tabs.push(tab_state) : '';
			});

			return tabs;
		},

		update_hash: function ()
		{
			var hash = document.location.hash || '#';
			var slug = this.generate_slug(this.get_tab_data());

			if (this.parse_slug(hash))
			{
				hash = hash.replace(this.slug_expr(), slug);
			} else
			{
				hash += slug;
			}

			history.pushState({ tabs_update: true }, '', hash);
		},

		refresh_by_hash: function ()
		{
			var hash = document.location.hash || '';
			var data = this.parse_slug(hash);

			if (data)
			{
				this.set_cfg('current_tabs', data);
				this.update_dom_state(true);

				return true;
			}
		},

		reset_tab_data: function (to_defaults)
		{
			var start_tabs = this.get_defaults('start_tabs');
			var current_tabs = [];

			this.get_dom('title').each(function ()
			{
				current_tabs.push(false);
			});

			if (to_defaults)
			{
				if (start_tabs)
				{
					! Array.isArray(start_tabs) ? start_tabs = [start_tabs] : '';

					start_tabs.forEach(function (tab_index)
					{
						current_tabs[tab_index] = true;
					}, this);
				}
			}

			this.set_cfg('current_tabs', current_tabs);

			return current_tabs;
		},

		get_tab_data: function (id)
		{
			var current_tabs = this.get_cfg('current_tabs');

			return id !== undefined ? current_tabs[id] : current_tabs;
		},

		update_tab_data: function (id, update_hash, update_dom)
		{
			var is_active_tab;
			var current_tabs = this.get_cfg('current_tabs');
			var constrain = this.get_cfg('constrain');
			var enable_current_toggle = this.get_cfg('enable_current_toggle');

			if (id === undefined)
				return;

			if (Array.isArray(id))
			{
				this.update_tabs_data(id, update_hash, speed, false);
				return;
			}

			is_active_tab = this.get_tab_data(id);


			if (
				! constrain ||
				! is_active_tab ||
				constrain && enable_current_toggle 
			)
			{
				if (constrain)
				{
					current_tabs = this.reset_tab_data();
				}

				current_tabs[id] = ! is_active_tab;
			}

			if (update_dom !== false && update_dom !== undefined) //typeof update_number == 'number' acts as a speed
			{
				if (constrain)
				{
					this.update_dom_state(current_tabs, typeof update_dom === 'number' ? update_dom : undefined);
				} else
				{
					this.update_dom_state(id, current_tabs[id], typeof update_dom === 'number' ? update_dom : undefined);
				}
			}

			if (update_hash)
			{
				this.update_hash();
			}

		},

		update_tabs_data: function (id, update_hash, update_dom)
		{
			id.forEach(function (i)
			{
				this.update_tab_data(i, update_hash, update_dom);
			}, this);

			! update_dom ? this.update_dom_state() : ''; 
			! update_hash ? this.update_hash() : '';
		},

		update_dom_state: function (id, state, speed, force_update)
		{
			if (id === true)
			{
				force_update = true;
				id = undefined;
				speed = 0;
			}

			if (id === undefined)
			{
				id = this.get_tab_data(); //will update all
			}

			if (Array.isArray(id))
			{
				state = undefined;
				speed = speed !== undefined ? speed : arguments[1];

				id.forEach(function (state, tab_index)
				{
					this.update_dom_state(tab_index, state, speed, force_update);
				}, this);

				return;
			}

			speed === undefined ? speed = this.get_cfg('scroll_speed') : '';
			state === undefined ? state = this.get_tab_data(id) : '';

			var $widget = $(this.get_dom('elem'));
			var $title = this.get_dom('title').eq(id);
			var $content = this.get_dom('content').eq(id);
			var current_state = $title.hasClass(prefix('current'));


			if ((current_state != state) || force_update)
			{
				$title.toggleClass(prefix('current'), state);
				$content.stop(true);
				state ? $content.slideDown(speed) : $content.slideUp(speed);

				if (state && (current_state != state) && force_update) //do scrollTo only when delegating via hash
				{
					$('html, body').stop(true).animate({
				        scrollTop: $widget.offset().top - 50
				    }, speed);
				}
			}
		},

		update_responsive: function ()
		{
			var $elem = $(this.get_cfg('elem'));

			if (this.get_cfg('type') === 'tab')
			{
				if ($elem.hasClass(prefix('tabs-orientation-y')) && $elem.outerWidth() < 500)
				{
					$elem
						.removeClass(prefix('tabs-orientation-y'))
						.addClass(prefix([
							'tabs-orientation-x',
							'tabs-orientation-y-marker'
						], ' '));
				} else if ($elem.hasClass(prefix('tabs-orientation-y-marker')) && $elem.outerWidth() >= 500)
				{
					$elem
						.removeClass(prefix([
							'tabs-orientation-x',
							'tabs-orientation-y-marker'
						], ' '))
						.addClass(prefix('tabs-orientation-y'));
				}
			}
		},

		reset_all: function ()
		{
			this.reset_tab_data(true);
			this.update_dom_state(true);

		},

		update_constraints: function ()
		{
			this.get_cfg('type') === 'tab' ? this.set_cfg('constrain', true) : '';
			this.get_dom('title').length == 1 ? this.set_cfg('enable_current_toggle', true) : '';
		},

		init: function (cfg)
		{
			var self = this;
			this.construct(cfg);
			this.clean_cfg(); //temp?; cleans up legacy cfg props in the main object

			this.register();
			this.init_content();
			this.update_constraints();

			if ( ! this.refresh_by_hash())
			{
				this.reset_all();
			}

			this.update_responsive();

			$(window)
				.on('popstate', function (evt)
				{
					if ( ! evt.state || evt.state && ! evt.state.tabs_update)
					{
						self.refresh_by_hash();
					}
				})
				.on('resize', function ()
				{
					self.update_responsive();
				});


			return this;
		}
	});

	$.fn.etherTabs = function (options) 
	{
		var options;
        var defaults;

        if (options)
        {
	        defaults = jQuery.extend(true, {}, DEFAULTS);
			options = $.extend(defaults, options);

	        options.elem_selector = $(this).selector;

			return this.each(function()
			{
				var self = this;
				options.elem = self;

				new Tab_widget().init(jQuery.extend(true, {}, options));
			});
		}  else
		{
			return widget_manager.get_elem('acc_widget', $(this)) || widget_manager.get_elem('tab_widget', $(this));
		}
    }

    ether.Tab_widget = ether.Tab_widget || Tab_widget;
})(jQuery);

(function($)
{
	var utils = ether.utils;
	var image_loader = ether.image_loader;

	var prefix = utils.prefix;
	var prefix_class = utils.prefix_class;

	var roundabout = function ()
	{
		return {
			init: function ($elem, props, images_loaded)
			{


				var self = this;

				if ( ! images_loaded)
				{
					$elem.hide();

					image_loader.on_images_load_end($elem.find('img'), function ()
					{
						self.init($elem, props, true);
					});
					
					return;
				} else
				{



					if ( ! props.height && ! props.imageCropHeight)
					{
						$elem
							.on('childrenUpdated', self.adjust_height)
							.on('ready', self.adjust_height);
					}
					
					$elem
						.show()
						.roundabout(props);
					
					if ( ! props.height && ! props.imageCropHeight)
					{
						$elem
							.roundabout('relayoutChildren');
					}
				}
			},

			adjust_height: function(e) 
			{ 
				var roundabout_height = 0; 
				var $children = $(e.target).children(); 

				$children.each( function() 
				{ 
					if ($(this).height() > roundabout_height)
					{
						roundabout_height = $(this).outerHeight(); 
					}
				}); 

				$(e.target).height(roundabout_height);

			}
		}
	}();

	var builder = 
	{
		roundabout: roundabout,

		twitter_fetcher_callback: function (tweets, target_name_id)
		{
			var count = tweets.length;
			var index = 0;
			var html = '';

			while (index < count)
			{
				var $tweet = $(tweets[index]);
				var $user = $tweet.filter('.user');
				var $content = $tweet.filter('.tweet');
				var $time_posted = $tweet.filter('.timePosted');
				var $interact = $tweet.filter('.interact');

				var $user_name, $user_avatar;


				html += '<div class="' + prefix('twitter-feed-item') + '">';


				if ($user.length)
				{
					$user.addClass('user');

					$user_name = $user.clone();
					$user_name
						.addClass('user-name')
						.find('img').remove().end()
						.find('span').eq(0).remove();

					$user_avatar = $user.clone();
					$user_avatar
						.addClass('user-avatar')
						.find('span').remove();

					$user_avatar.add($user_name).wrapAll('<div>');

					html += $user_name.parent().html();
				}

				if ($content.length)
				{
					$content
						.wrapInner('<div class="content">')
						.find('a').each( function() 
						{ 
							$(this).text($(this).text()); // get rid of span element in the links
						});

					html += $content.html();
				}

				if ($time_posted.length)
				{
					$time_posted
						.wrapInner('<div class="time-posted">');

					html += $time_posted.html();
				}

				if ($interact.length)
				{
					$interact
						.wrapInner('<div class="tweet-menu">')
						.find('a')
							.eq(0).addClass('reply').wrapInner('<span class="item-label">').append('<span class="icon dashicons dashicons-undo">').end()
							.eq(1).addClass('retweet').wrapInner('<span class="item-label">').append('<span class="icon dashicons dashicons-randomize">').end()
							.eq(2).addClass('favourite').wrapInner('<span class="item-label">').append('<span class="icon dashicons dashicons-star-filled">');

					html += $interact.html();
				}

				html += '</div>';

				index++;
			}


			ether.widget_manager.get_elem('gridslider', $('#' + prefix(target_name_id))).add_elements($(html));
		},

		init_media_widgets: function ()
		{
			var self = this;
			var $elem = $(prefix_class('widget') + prefix_class('img'));
			
			var width_re = /width:\s*(\d+(?:\w+|%))/;
			var height_re = /height:\s*(\d+(?:\w+|%))/;
			var align_re = new RegExp(prefix('align') + '(\\w+)\\b');

			$elem.each(function ()
			{
				var $img = $(this).find('img');
				var wrap_width_match = ($img.parent().attr('style') || '').match(width_re);
				var image_width = $img.attr('width') || (wrap_width_match !== null ? wrap_width_match[1] : undefined);
				var wrap_height_match = ($img.parent().attr('style') || '').match(height_re);
				var image_height = $img.attr('height') || (wrap_height_match !== null ? wrap_height_match[1] : undefined);
				var align = ($(this).attr('class') || '').match(new RegExp(align_re));

				var img_title_alignment_y = $(this).cattr(prefix('img-title-alignment-y'));
				var show_img_title = $(this).cattr(prefix('show-img-title'), undefined, undefined, true);
				var img_title_custom_class = $(this).cattr(prefix('img-title-custom-class'), undefined, undefined, true);

				img_title_alignment_y = (img_title_alignment_y === '' ? undefined : img_title_alignment_y);
				show_img_title = (show_img_title !== '' ? show_img_title : false);

				align = (align != null ? align[1] : undefined);

				var options =
				{
					$img: $img,
					align: align,
					image_width: image_width,
					image_height: image_height,








					show_img_title: show_img_title, 
					img_title_alignment_y: img_title_alignment_y,
					img_title_custom_class: img_title_custom_class
				}


				$img.mediaWidget(options);
			});
		},

		init_tab_widgets: function ()
		{
			var self = this;
			var $elem = $(prefix_class('multi')); //clean up this class

			if ( ! $elem.length)
				return;

			$elem.each(function()
			{
				var type = $(this).cattr(prefix('type'));
				var constrain = ($(this).cattr(prefix('constrain')) == 1 ? true : false);
				var enable_current_toggle = ($(this).cattr(prefix('enable-current-toggle')) == 1 ? true : false);
				var show_tab_state_marker = $(this).hasClass(prefix('show-tab-state-marker'));
				var user_id = $(this).attr('id');
				var start_tabs = [];

				$(this).find(
					prefix_class('title') +
					prefix_class('current')
				).each(function ()
				{
					var id = $(this).index() / 2;
					start_tabs.push(id);
				});

				$(this).etherTabs({
					type: type === 'tabs' ? 'tab' : 'acc',
					user_id: user_id,
					constrain: constrain,
					enable_current_toggle: enable_current_toggle,
					show_tab_state_marker: show_tab_state_marker,
					start_tabs: start_tabs
				}).init();
			});
		},

		init_syntax_highlighter: function ()
		{
			if (typeof SyntaxHighlighter != 'undefined')
			{
				var sh_base_path = $('script[src*="shCore.js"]').attr('src').split('shCore.js')[0];

				function sh_path(data)
				{
					for (var i = 0; i < data.length; i++)
					{
						data[i] = data[i].replace('@', sh_base_path);
					}

					return data;
				}

				SyntaxHighlighter.autoloader.apply(null, sh_path
				([
					'applescript @shBrushAppleScript.js',
					'actionscript3 as3 @shBrushAS3.js',
					'bash shell @shBrushBash.js',
					'coldfusion cf @shBrushColdFusion.js',
					'cpp c @shBrushCpp.js',
					'c# c-sharp csharp @shBrushCSharp.js',
					'css @shBrushCss.js',
					'delphi pascal @shBrushDelphi.js',
					'diff patch pas @shBrushDiff.js',
					'erl erlang @shBrushErlang.js',
					'groovy @shBrushGroovy.js',
					'java @shBrushJava.js',
					'jfx javafx @shBrushJavaFX.js',
					'js jscript javascript @shBrushJScript.js',
					'perl pl @shBrushPerl.js',
					'php @shBrushPhp.js',
					'text plain @shBrushPlain.js',
					'py python @shBrushPython.js',
					'ruby rails ror rb @shBrushRuby.js',
					'sass scss @shBrushSass.js',
					'scala @shBrushScala.js',
					'sql @shBrushSql.js',
					'vb vbnet @shBrushVb.js',
					'xml xhtml xslt html @shBrushXml.js'
				]));

				SyntaxHighlighter.all();
			}
		},

		init_grid_slider_widget: function ()
		{
		    $(prefix_class('grid')).each( function()
			{
				var props = {};

				var re_width = new RegExp('width:\\s*(.*?)(?:\\s*;|\\s*$)');
				var width = ($(this).attr('style') || '').match(re_width);
				var re_align = new RegExp(prefix('align') + '(\\w+)\\b');
				var align = $(this).attr('class').match(re_align);

				var cols = $(this).cattr(prefix('cols'));
				var rows = $(this).cattr(prefix('rows'));
				var col_spacing_enable = $(this).cattr(prefix('spacing'));
				var col_spacing_size = $(this).cattr(prefix('spacing-size'));

				width = (width != null ? width[1] : undefined);
				align = (align != null ? align[1] : 'auto');
				align && ! width && (align === 'left' || align === 'right') ? width = '100%' : ''; //need this or sliders get 0 width when left/right aligned with no width specified				

				cols = (cols === undefined || cols === '' ? $(this).find(prefix_class('cols')).eq(0).cattr(prefix('cols')) : cols);
				rows = (rows === undefined || rows === '' ? $(this).find(prefix_class('cols')).eq(0).cattr(prefix('rows')) : rows);

				if ($(this).find(prefix_class('cols')).length)
				{
					col_spacing_enable = ((col_spacing_enable === undefined || col_spacing_enable) === '' ? $(this).find(prefix_class('cols')).eq(0).cattr(prefix('spacing')) : col_spacing_enable);
					
					col_spacing_size = ((col_spacing_size === undefined || col_spacing_size === '') ? $(this).find(prefix_class('cols')).eq(0).cattr(prefix('spacing-size')) : col_spacing_size);
				}

				col_spacing_enable = (col_spacing_enable == 1 ? true : col_spacing_enable == 0 ? false : ''); //ignore this param without explicit definition

				$(this)
					.removeClass(prefix('cols'))
					.removeClass(prefix('cols' + '-' + cols))
					.removeClass(prefix('rows'))
					.removeClass(prefix('rows' + '-' + rows))
					.removeClass(prefix('spacing'))
					.removeClass(prefix('spacing-size'));

				props['grid_hardcoded'] = true;
				props['width'] = width;
				props['align'] = align;

				props['cols'] = parseInt(cols);
				props['rows'] = parseInt(rows);
				col_spacing_enable !== '' ? props['col_spacing_enable'] = col_spacing_enable : ''; //ignore this param without explicit definition
				col_spacing_size !== '' ? props['col_spacing_size'] = col_spacing_size : ''; //ignore this param without explicit definition

				$(this).cattr(prefix('autoplay')) !== '' ? props['autoplay_enable'] = $(this).cattr(prefix('autoplay')) == 1 : ''; //inconsistent naming
				$(this).cattr(prefix('autoplay-invert')) !== '' ? props['autoplay_shift_dir'] = $(this).cattr(prefix('autoplay-invert')) == 1 ? 1 : -1 : ''; //inconsistent naming

				props['show_img_title'] = $(this).cattr(prefix('show-img-title'), undefined, undefined, true);
				props['img_title_alignment_y'] = $(this).cattr(prefix('img-title-alignment-y'));
				props['img_title_alignment_x'] = $(this).cattr(prefix('img-title-alignment-x'));

				props['use_parent_wrap'] = $(this).hasClass(prefix('use-parent-wrap'));

				for (var key in ether.grid_manager.DEFAULTS)
				{
					var attr = $(this).cattr(prefix(key.replace(/_/g, '-')));

					if (attr !== '')
					{
						if (typeof parseInt(attr) === 'number' && ! isNaN(parseInt(attr)))
						{
							attr = parseInt(attr);
						}

						switch (key)
						{
							case 'slider':
							case 'ctrl_always_visible':
							case 'ctrl_arrows':
							case 'ctrl_arrows_full_width':
							case 'ctrl_pag':
							case 'autoplay_enable':
							case 'pause_autoplay_on_hover':
							case 'gallery_img_title':
							case 'hide_grid_cell_overflow':
							case 'scroll_on_mousewheel':
							case 'loop':
							case 'random_order':
							case 'ctrl_style':
							{
								attr = attr == 1;

								break;
							}
						}

						props[key] = attr;
					}
				};

				
				$(this).gridSlider(props);
			});
		},

		init_back_to_top_widget: function ()
		{
			var $elem = $('.ether-back-to-top');
			
			if ($elem.hasClass(prefix('initialized')))
				return; 

			$elem
				.on('click', function()
				{
					$('html,body').scrollTop(0);

				})
				.addClass(prefix('initialized'));

		},

		/*
			scrolls to the current link if it's not visible in the menu wrapper
		*/
		scrollspy_menu_scroll_to_link: function ($link)
		{
			var $parent = $link.closest('.ether-menu-items-wrap');
			var scroll_top = $parent.scrollTop();
			var link_h = $link.outerHeight(true);
			var link_pos = parseInt($link.index() * link_h);
			var padding = link_h / 2;


			if ( link_pos + link_h + padding > $parent.height() + scroll_top ||
				 link_pos - padding < scroll_top
				)
			{
				$parent.stop().animate({ scrollTop: link_pos - $parent.height() / 2}, 300);
			}
		},

		heading_menu_set_menu_items_wrap_height: function ($menu)
		{
			if ( $menu.hasClass('ether-heading-menu-fixed') )
			{
				$menu.find('.ether-menu-items-wrap').height($menu.height() - $menu.find('.ether-heading-menu-title').outerHeight(true));
			}
		},

		init_scrollspy_menu: function ()
		{
			var self = this;

			if ($('body').hasClass(prefix('scrollspy-initialized')))
				return;

			var $heading_menu = $('.ether-heading-menu');
			var $scrollspy = $('.ether-scrollspy');
			var $scrollspy_link = $scrollspy.find('a');
			var $scrollspy_elements = $scrollspy_link.map( function()
			{
				var $item = $($(this).attr('href'));

				if ($item.length)
				{
					return $item;
				}
			});

			
			this.heading_menu_set_menu_items_wrap_height($heading_menu);

			$scrollspy_link.click( function(e)
			{
				var $link = $(this);
				var $target = $($link.attr('href'));
				var offset = $target.offset().top - $target.outerHeight(true);

				$('html, body').stop().animate({ scrollTop: offset }, 300, function()
				{
					$link.parent()
						.siblings().removeClass('ether-current').end()
						.addClass('ether-current');

					self.scrollspy_menu_scroll_to_link($link.parent());
				});

				return false;
			});

			$scrollspy
				.on('mouseenter', function ()
				{
					$(this).addClass('active');
				})
				.on('mouseleave', function ()
				{
					$(this).removeClass('active');
				});

			$scrollspy.find('.ether-heading-menu-title')
				.addClass('togglable')
				.on('click', function ()
				{
					$(this).siblings('.ether-menu-items-wrap').toggle(500);
				});

			$(window).scroll( function()
			{
				var top = $(this).scrollTop();

				var current = $scrollspy_elements.map( function()
				{
					if ($(this).offset().top < top)
					{
						return this;
					}
				});

				if (current.length > 0)
				{
					current = current[current.length - 1];

					var id = current[0].id;

					$scrollspy_link.parent().removeClass('ether-current').end().filter('[href=#' + id + ']').parent().addClass('ether-current');

					if ( ! $scrollspy.hasClass('active') )
					{
						self.scrollspy_menu_scroll_to_link($scrollspy_link.parent().filter('.ether-current'));
					}
				}
			});

			$(window).resize(function ()
			{
				self.heading_menu_set_menu_items_wrap_height($heading_menu);
			});

			$('body').addClass(prefix('scrollspy-initialized'));
		},

		init_lightbox: function ()
		{
			if (typeof $.fn.colorbox)
			{
				if (ether && ether.lightbox_initialized)
					return;

				var cbox_rels = {};

				$('a[rel*=lightbox]').each( function()
				{
					var rel = $(this).attr('rel');

					if (typeof cbox_rels[rel] == 'undefined')
					{
						cbox_rels[rel] = true;
					}
				});

				for (var rel in cbox_rels)
				{
					rel = rel.replace('[', '\\[').replace(']', '\\]');

					if ( ! $('a[rel=' + rel + ']').eq(0).hasClass('cboxElement'))
					{
						$('a[rel=' + rel + ']').colorbox({ rel: rel, maxWidth: '80%', maxHeight: '80%', fixed: true });
					}
				}

				ether.lightbox_initialized = true;
			}
		},

		fix_ie7_grid: function ()
		{
			var self = this;
			var $elem;
			var ie7_class;

			if(iev > 7)
				return;

			$elem = $(prefix('cols'));
			ie7_class = prefix('ie7');

			$elem = $(prefix('cols')).filter(function ()
			{
				return ! $(this).parents(prefix('grid')).length;
			});

			$elem.find(prefix_class('col')).each(function ()
			{
				var nested_cols;
				var current_col_width;
				var current_col_padding;

				if ( ! $(this).hasClass(ie7_class))
				{
					$(this)
						.addClass(ie7_class)
						.children().wrapAll('<div class="' + prefix('ie7-padding-maker')+ '"></div>');
				}

				nested_cols = $(this).find('.ether-cols').eq(0);
				nested_cols = nested_cols.add(nested_cols.siblings('.ether-cols'));

				if (nested_cols.length > 0)
				{
					current_col_width = $(this).width();
					current_col_padding = parseInt($(this).css('padding-left'), 10);

					nested_cols.each(function ()
					{
						$(this).css({
							'width': current_col_width + 2 * current_col_padding,
							'margin-left': -current_col_padding
						});
					});
				}
			});

			$elem.each(function ()
			{
				if ($(this).hasClass(prefix('cols')))
				{
					$(this).width('');
					$(this).width($(this).width() + parseInt($(this).find(prefix_class('ie7-padding-maker')).css('padding-left'), 10) * 2);


				}
			});
		},

		fix_ltie9_grid: function ()
		{
			var self = this;
			var $elem;

			if (! isie || iev > 8)
				return;
			
			$elem = $(prefix('cols')).filter(function ()
			{
				return ! $(this).parents(prefix('grid')).length;
			});

			$elem.children('.ether-col').each(function ()
			{
				$(this).children().eq(-1)
					.addClass('last-child');
			});

			$(window).on('resize', function () 
			{
				self.fix_ie7_grid();
			});
		},

		init_msg_boxes: function ()
		{
			var $elem = $(prefix_class('message-close-button'));

			if ($elem.hasClass(prefix('message-initialized')))
				return;

			if ($elem.length > 0)
			{
				$('<div class="' + prefix('ctrl-close') + '">close this window</div>')
					.appendTo($elem)
					.bind('click', function () {
						$(this).parent().hide(250);
					});

				$elem
					.bind('mouseenter', function () {
						$(this).children(prefix_class('ctrl-close')).stop(true, true).fadeIn(250);
					})
					.bind('mouseleave', function () {
						$(this).children(prefix_class('ctrl-close')).stop(true, true).fadeOut(250);
					})
					.addClass(prefix('message-initialized'));
			}
		},

		init_pricing_table: function ()
		{
			var $elem = $(prefix_class('prc'));

			if ($elem.length > 0)
			{
				$elem.each(function () {
					var $prc = $(this);
					var $tr = $(this).find('tr');
					var $td = $(this).find('td');
					var $button_wrap = $(this).find(prefix_class('prc-button'));
					var $button = $($button_wrap).children('a');
					var h = $button.height();
					var timeout = [];

					if ($prc.hasClass('prc-initialized'))
						return;

					if (($(this)).hasClass(prefix('prc-2'))) {
						$button.css({height: 0});
					}

					var width = 100 / $(this).find('tr').eq(0).children().length;
					$(this).find('tr').eq(0).children().each(function ()
					{
						$(this).css({width: width + '%'});
					});

					$td
						.bind('mouseenter', function () {
							var id = $(this).index();
							clearTimeout(timeout[id]);
							delete timeout[id];
							if ( ! $(this).hasClass(prefix('prc-col-hover'))) {
								$tr.each(function () {
									$(this).find('td').eq(id).addClass(prefix('prc-col-hover'));
								});
								if (($prc).hasClass(prefix('prc-2'))) {
									$button_wrap.eq(id).find('a')
										.stop(true, true)
										.animate({height: h}, 250);
								}
							}

						})
						.bind('mouseleave', function () {
							var id = $(this).index();
							timeout[id] = setTimeout(function () {
								$tr.each(function () {
									$(this).find('td').eq(id).removeClass(prefix('prc-col-hover'));
								});
								if (($prc).hasClass(prefix('prc-2'))) {
									$button_wrap.eq(id).find('a')
										.stop(true, true)
										.animate({height: 0}, 250);
								}
							}, 250);
						});

					$prc.addClass(prefix('prc-initialized'));
				});
			}
		},

		init_video_widget: function ()
		{
			var $elem = $(prefix_class('media-wrap') + prefix_class('aligncenter'));

			$elem.each(function ()
			{
				if ($(this).hasClass(prefix('video-initialized')))
					return;

				$(this)
					.width($(this).children().eq().width())
					.addClass(prefix('video-initialized'))
			});

			$(window).resize(function ()
			{
				$elem.each(function ()
				{
					$(this).width($(this).children().eq().width());
				});
			});
		},

		init_widgets: function ()
		{
			this.init_syntax_highlighter();
			this.init_media_widgets();
			this.init_grid_slider_widget();
			this.init_back_to_top_widget();
			this.init_scrollspy_menu();
			this.init_lightbox();
			this.init_tab_widgets();
			this.init_msg_boxes();
			this.init_pricing_table();
			this.init_video_widget();
		},

		init: function ()
		{
			this.init_widgets();
			this.fix_ltie9_grid();
			this.fix_ie7_grid();
		}
	}

	ether.builder = ether.builder || builder;

	$(function ()
	{
		builder.init();
	});
})(jQuery);

