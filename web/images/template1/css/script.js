var xpos;
var ypos;
function imouse(evt){
  ypos=evt.clientY+document.body.scrollTop;
  xpos=evt.clientX+document.body.scrollLeft;
  }
  
function showSubmenu(menu) {
	var submenu = document.getElementById('sub'+menu.name);
	if (submenu!= null) {
		if (submenu.style.display=='block')  return;
		submenu.style.width='150px';
		submenu.style.top=getPosition(menu).top+menu.offsetHeight+2+'px';
		submenu.style.left=getPosition(menu).left+'px';
		if (parseInt(submenu.style.left)+parseInt(submenu.style.width)+20 > parseInt(document.body.clientWidth)) submenu.style.left = parseInt(document.body.clientWidth) - parseInt(submenu.style.width)-20+'px';
		submenu.style.display='block';
		fadeIn(submenu,0);
		}
	}

function hideSubmenu(menu,evt) {
	var submenu = document.getElementById('sub'+menu.name);
	if (submenu!= null) {
		imouse(evt);
		if (xpos-2>getPosition(menu).left && xpos<getPosition(menu).right 
		&& ypos-2>getPosition(menu).top && ypos<getPosition(submenu).bottom) return;
		fadeOut(submenu,submenu.style.opacity);
		}
	}

function hideSubmenu2(submenu,evt) {
	if (submenu!= null) {
		var submenu = document.getElementById(submenu);
		imouse(evt);
		if (xpos-2>getPosition(submenu).left && xpos<getPosition(submenu).right 
		&& ypos-2>getPosition(submenu).top && ypos<getPosition(submenu).bottom) return;
		fadeOut(submenu,submenu.style.opacity);
		}
	}

function fadeIn(submenu,op) {
	if (op<0.9) {
		op+=0.05;
		submenu.style.opacity=op;
		submenu.style.filter='alpha(opacity='+(op*100)+')';
	setTimeout(function(){fadeIn(submenu,op)},15);
	}
}

function fadeOut(submenu,op) {
	if (op>0.1) {
		op-=0.05;
		submenu.style.opacity=op;
		submenu.style.filter='alpha(opacity='+(op*100)+')';
	setTimeout(function(){fadeOut(submenu,op)},15);
	} else submenu.style.display='none';

}

function getPosition(elem)
{
    var w = elem.offsetWidth;
    var h = elem.offsetHeight;
    var l = 0;
    var t = 0;
    while (elem)
    {
        l += elem.offsetLeft;
        t += elem.offsetTop;
        elem = elem.offsetParent;
    }
//    if (l==0) l = 
    return {"left":l, "top":t, "width": w, "height":h, "right":w+l, "bottom":t+h};
}
