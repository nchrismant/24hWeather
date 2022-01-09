function img_create(src, alt, id) {
  var img = new Image();
  img.src = src;
  if (alt != null) img.alt = alt;
  if (id != null) img.id = id;
  return img;
}

function removeElement(ele) {
  ele.parentNode.removeChild(ele);
}

function addClasstoElem(elem, classname) {
  for(var i = 0; i < elem.length; i++) {
    elem[i].classList.add(classname);
  }
}

function removeClasstoElem(elem, classname) {
  for(var i = 0; i < elem.length; i++) {
    elem[i].classList.remove(classname);
  }
}

function addStyletoElem(elem, stylename, value) {
  for(var i = 0; i < elem.length; i++) {
    elem[i].style[stylename] = value;
  }
}

let moon = document.getElementById('moon');
let stars = document.getElementById('stars');

var body = document.querySelector('body');
var h2 = document.getElementsByTagName('h2');
var text = document.getElementsByClassName('text');
var logo = document.getElementById('logoimg');

var focus_input = document.querySelectorAll('.form-inner form .field input:focus');
var slider = document.querySelectorAll('.slide-controls .slider-tab');
var btn_layer = document.querySelectorAll('form .btn-submit .btn-layer');
var link = document.querySelectorAll('.form-inner form .pass-link a');
var link2 = document.querySelectorAll('.form-inner form .signup-link a');
var slide = document.querySelectorAll('.slide-controls .slide');
var btn_submit = document.querySelectorAll('form .btn-submit');
var search_form = document.getElementsByClassName('search-form');
var dis_title = document.querySelectorAll('.dis-title');
var sub_title = document.querySelectorAll('.sub-title');


const toggle = document.getElementById('toggle');

if(toggle) {
  toggle.onclick = function () {
    toggle.classList.toggle('active-toggle');
    if(toggle.classList.contains('active-toggle')){
      Cookies.set('mode', 'light', { expires: 30, path: '/' });
      body.classList.add("light");
      logo.src = './images/logoalt.svg';
      addClasstoElem(h2, 'light-h2');
      addClasstoElem(text, 'light-text');
      addClasstoElem(focus_input, 'light-input');
      addClasstoElem(slider, 'light-tab');
      addClasstoElem(btn_layer, 'light-btn-layer');
      addClasstoElem(link, 'light-a');
      addClasstoElem(link2, 'light-a');
      addClasstoElem(search_form, 'active-search-form');
      addClasstoElem(dis_title, 'dis-title-light');
      addClasstoElem(sub_title, 'dis-title-light');
      if(moon){
        moon.src = "./images/bird1.png";
        moon.id = 'bird1';
        stars.src = "./images/bird2.png";
        stars.id = 'bird2';
        moon.style.mixBlendMode = "initial";
        var clouds1 = img_create('./images/clouds_1.png', 'clouds1', 'clouds1');
        var clouds2 = img_create('./images/clouds_2.png', 'clouds2', 'clouds2');
        var sun = img_create('./images/sun.png', 'sun', 'sun');
        document.getElementById('background').appendChild(sun);
        document.getElementById('background').appendChild(clouds1);
        document.getElementById('background').appendChild(clouds2);
      }
    }
    else {
      Cookies.set('mode', 'dark', { expires: 30, path: '/' });
      body.classList.remove("light");
      logo.src = './images/logo.svg';
      removeClasstoElem(h2, 'light-h2');
      removeClasstoElem(text, 'light-text');
      removeClasstoElem(focus_input, 'light-input');
      removeClasstoElem(slider, 'light-tab');
      removeClasstoElem(btn_layer, 'light-btn-layer');
      removeClasstoElem(link, 'light-a');
      removeClasstoElem(link2, 'light-a');
      removeClasstoElem(search_form, 'active-search-form');
      removeClasstoElem(dis_title, 'dis-title-light');
      removeClasstoElem(sub_title, 'dis-title-light');
      if(moon) {
        moon.src = "./images/moon.png";
        moon.id = 'moon';
        stars.src = "./images/stars.png";
        stars.id = 'stars';
        removeElement(document.getElementById('sun'));
        removeElement(document.getElementById('clouds1'));
        removeElement(document.getElementById('clouds2'));
        moon.style.mixBlendMode = "screen";
      }
    }
  }
}

if(toggle){
  if((Cookies.get('mode')) == 'light') {
    toggle.classList.add('active-toggle');
  }
  else {
    toggle.classList.remove('active-toggle');
  }

  if(toggle.classList.contains('active-toggle')){
    body.classList.add("light");
    logo.src = './images/logoalt.svg';
    addClasstoElem(h2, 'light-h2');
    addClasstoElem(text, 'light-text');
    addClasstoElem(focus_input, 'light-input');
    addClasstoElem(slider, 'light-tab');
    addClasstoElem(btn_layer, 'light-btn-layer');
    addClasstoElem(link, 'light-a');
    addClasstoElem(link2, 'light-a');
    addClasstoElem(search_form, 'active-search-form');
    addClasstoElem(dis_title, 'dis-title-light');
    addClasstoElem(sub_title, 'dis-title-light');
    if(moon){
      moon.src = "./images/bird1.png";
      moon.id = 'bird1';
      stars.src = "./images/bird2.png";
      stars.id = 'bird2';
      moon.style.mixBlendMode = "initial";
      var clouds1 = img_create('./images/clouds_1.png', 'clouds1', 'clouds1');
      var clouds2 = img_create('./images/clouds_2.png', 'clouds2', 'clouds2');
      var sun = img_create('./images/sun.png', 'sun', 'sun');
      document.getElementById('background').appendChild(sun);
      document.getElementById('background').appendChild(clouds1);
      document.getElementById('background').appendChild(clouds2);
    }
  }
  else {
    body.classList.remove("light");
    logo.src = './images/logo.svg';
    removeClasstoElem(h2, 'light-h2');
    removeClasstoElem(text, 'light-text');
    removeClasstoElem(focus_input, 'light-input');
    removeClasstoElem(slider, 'light-tab');
    removeClasstoElem(btn_layer, 'light-btn-layer');
    removeClasstoElem(link, 'light-a');
    removeClasstoElem(link2, 'light-a');
    removeClasstoElem(search_form, 'active-search-form');
    removeClasstoElem(dis_title, 'dis-title-light');
    removeClasstoElem(sub_title, 'dis-title-light');
    if(moon){
      moon.src = "./images/moon.png";
      moon.id = 'moon';
      stars.src = "./images/stars.png";
      stars.id = 'stars';
      moon.style.mixBlendMode = "screen";
    }
  }

  window.addEventListener('scroll', function(){
    let value = window.scrollY;
    if(toggle.classList.contains('active-toggle')) {
      if(moon){
        var clouds1 = document.getElementById("clouds1");
        var clouds2 = document.getElementById("clouds2");
        var sun = document.getElementById("sun");
        sun.style.top = value * 0.5 + ('px');
        clouds1.style.left = value * 0.25 + ('px');
        clouds2.style.left = value * -0.25 + ('px');
        moon.style.top = value * -1.5 + ('px');
        moon.style.left = value * 1.5 + ('px');
        stars.style.top = value * -1.5 + ('px');
        stars.style.left = value * -4 + ('px');
      }
    }
    else {
      if(moon){
        stars.style.left = value * 0.25 + ('px');
        moon.style.top = value * 0.5 + ('px');
      }
    }
  });
}

$('.search-input').focus(function(){
    $(this).parent().addClass('focus');
  }).blur(function(){
    $(this).parent().removeClass('focus');
  });

$(document).ready(function() {	
  $('.btn').delay(400).queue(function(next) {
    $(this).addClass('hover').delay(1600).queue(function(next) {
      $(this).removeClass('hover');
    });
    next();
  });
});

$(document).ready(function() {
  $("#newuser").blur(function() {
     var username = $(this).val().trim();
     if(username != '') {
        $.ajax({
           url: 'ajax.php',
           type: 'post',
           data: {newuser: username},
           success: function(response){
              $('#response').html(response);
              if(response != '') {
                $("#newuser").addClass("form-error");
                $("#error-user").hide(0);
              }
              else {
                $("#newuser").removeClass("form-error");
                $("#error-user").show(0);
              }
            }
        });
     }else{
        $("#response").html("");
     }
   });

  $("#newmail").blur(function() {
    var mail = $(this).val().trim();
    if(mail != '') {
      $.post('ajax.php', {
        newmail: mail
      }, function(data, status) {
        $('#responseMail').html(data);
        if(data != '') {
          $("#newmail").addClass("form-error");
          $("#error-mail").hide(0);
        }
        else {
          $("#newmail").removeClass("form-error");
          $("#error-mail").show(0);
        }
      });
    }else {
      $("#responseMail").html("");
    }
  });
});

var navbar = document.getElementsByClassName('nav-bar');
var navbar_ul = document.querySelectorAll('.nav-bar ul')
var menu = document.getElementsByClassName('menu');
var li_menu = document.getElementById('li-menu');
const menuBtn = document.querySelector('.menu-btn');
let menuOpen = false;

if(menuBtn) {
  menuBtn.addEventListener('click', () => {
    if(!menuOpen) {
      menuBtn.classList.add('open');
      addStyletoElem(menu, 'left', '0%');
      addStyletoElem(slide, 'zIndex', 0);
      addStyletoElem(btn_submit, 'zIndex', 0);
      addStyletoElem(navbar, 'background', 'rgb(32, 100, 226)');
      addStyletoElem(navbar, 'transition', 'all .5s');
      addStyletoElem(navbar_ul, 'overflow', 'auto');
      body.style.overflow = 'hidden';
      var newli = document.createElement("li");
      newli.id ='new-li';
      li_menu.appendChild(newli);
      newli.innerHTML = "&#x00A0;";
      menuOpen = true;
    } else {
      menuBtn.classList.remove('open');
      addStyletoElem(menu, 'left', '-100%');
      addStyletoElem(slide, 'zIndex', 1);
      addStyletoElem(btn_submit, 'zIndex', 1);
      addStyletoElem(navbar, 'background', 'none');
      addStyletoElem(navbar_ul, 'overflow', 'initial');
      body.style.overflow = 'initial';
      removeElement(document.getElementById('new-li'));
      menuOpen = false;
    }
  });
}

const ratio = .15;
const options = {
  root: null,
  rootMargin: '0px',
  threshold: ratio
}

const handleIntersect = function (entries, observer) {
  entries.forEach(function (entry) {
    if (entry.intersectionRatio > ratio) {
      entry.target.classList.add('reveal-visible');
      observer.unobserve(entry.target);
    }    
  });
}

const observer = new IntersectionObserver(handleIntersect, options);
document.querySelectorAll('.reveal').forEach(function (r) {
  observer.observe(r);
});

function scroll_to(e){
  e > 500 ? $("#scrolltotop").fadeIn() : $("#scrolltotop").fadeOut();
}

$(document).ready(function(){
  scroll_to($(this).scrollTop()),
  $(window).scroll(function(){
    scroll_to($(this).scrollTop())
  }),
  $("#scrolltotop").on("click",function(){
    return $("html, body").animate({scrollTop:0},1200),!1;
  })
});

const loginText = document.querySelector(".title-text .login");
const loginForm = document.querySelector("form.login");
const loginBtn = document.querySelector("label.login");
const signupBtn = document.querySelector("label.signup");
const signupLink = document.querySelector("form .signup-link a");

if(signupBtn) {
  signupBtn.onclick = (()=>{
    loginForm.style.marginLeft = "-50%";
    loginText.style.marginLeft = "-50%";
  });

  loginBtn.onclick = (()=>{
    loginForm.style.marginLeft = "0%";
    loginText.style.marginLeft = "0%";
  });

  signupLink.onclick = (()=>{
    signupBtn.click();
    return false;
  });
}

var btn_pop = document.getElementById("pop-button");
var overlay = document.getElementsByClassName("overlay");
var close_btn = document.getElementsByClassName("close-btn");

function togglePopup() {
  document.getElementById('popup-1').classList.toggle('active-pop');
}

for (var i = 0; i < overlay.length; i++) {
  overlay[i].addEventListener('click', function() {
    togglePopup();
  });
}

for (var i = 0; i < close_btn.length; i++) {
  close_btn[i].addEventListener('click', function() {
    togglePopup();
  });
}

if(btn_pop) {
  btn_pop.addEventListener('click', function() {
    togglePopup();
  });
}

document.addEventListener('keydown', function(event){
	if(event.key === "Escape") {
    document.getElementById("popup-1").classList.toggle("active-pop");
	}
});

function capitalizeFirstLetter(string) {
  return string[0].toUpperCase() + string.slice(1);
}

var tabLinks = document.querySelectorAll(".tablinks");
var tabContent = document.querySelectorAll(".tabcontent");

$(document).ready(function(){
  if(sessionStorage.getItem('page')) {
    tabContent.forEach(function(el) {
      el.classList.remove("active");
    });
  
    tabLinks.forEach(function(el) {
      el.classList.remove("active");
    });
    var id = sessionStorage.getItem('page');
    var id_btn = capitalizeFirstLetter(id);
    var page = document.getElementById(id);
    var btn_page = document.getElementById(id_btn);
    if(page) {
      page.classList.add("active");
      btn_page.classList.add("active");
    }
  }
  else {
    var profil = document.getElementById('profil');
    var Profil = document.getElementById('Profil');
    if(profil && Profil) {
      profil.classList.add("active");
      Profil.classList.add("active");
    }
  }
});

tabLinks.forEach(function(el) {
   el.addEventListener("click", openTabs);
});

function openTabs(el) {
  var btnTarget = el.currentTarget;
  var title = btnTarget.dataset.title;

  tabContent.forEach(function(el) {
    el.classList.remove("active");
  });

  tabLinks.forEach(function(el) {
    el.classList.remove("active");
  });

  if(document.querySelector("#" + title)) {
    document.querySelector("#" + title).classList.add("active");
  };
   
  btnTarget.classList.add("active");
  sessionStorage.setItem('page', btnTarget.id);
}


let back = document.getElementById('back');
if(back) {
  back.addEventListener('click', function () {
    history.back();
  });
}

if(document.getElementById("file")) {
  document.getElementById("file").onchange = function() {
    document.getElementById("file-form").submit();
  }
}

$('input[type="file"]').each(function() {
  var label = $(this).parents('.form-group').find('label').text();
  label = (label) ? label : '';

  $(this).wrap('<div class="input-file"></div>');
  $(this).before('<span class="btn">'+label+'</span>');
  $(this).before('<span class="file-selected"></span>');

  $(this).change(function(e){
      var val = $(this).val();
      var filename = val.replace(/^.*[\\\/]/, '');
      $(this).siblings('.file-selected').text(filename);
  });
});

$('.input-file .btn').click(function() {
  $(this).siblings('input[type="file"]').trigger('click');
});

$(document).ready(function() {
  $('#ville').keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});

$(document).ready(function(){
  $.ajaxSetup({ cache: false });
  $('#ville').keydown(function(e){
    if(e.which == 13) {
      $('#result').html('');
      $('#state').val('');
      var searchField = $('#ville').val();
      var expression = new RegExp('^'+searchField, "i");
      $.getJSON('city.list.json', function(data) {
        $.each(data, function(key, value){
          if(value.name.search(expression) != -1) {
            $('#result').append('<a href="meteo.php?lat='+value.coord.lat+'&amp;lon='+value.coord.lon+'"><li class="list-group-item link-class"> '+value.name+' | <span class="text-muted">'+value.country+'</span></li></a>');
          }
        });   
      });
    }
  });

  $('#submit').on('click', function(){
    $('#result').html('');
    $('#state').val('');
    var searchField = $('#ville').val();
    var expression = new RegExp('^'+searchField, "i");
    $.getJSON('city.list.json', function(data) {
      $.each(data, function(key, value){
        if(value.name.search(expression) != -1) {
          $('#result').append('<a href="meteo.php?lat='+value.coord.lat+'&amp;lon='+value.coord.lon+'"><li class="list-group-item link-class"> '+value.name+' | <span class="text-muted">'+value.country+'</span></li></a>');
        }
      });   
    });
  });
  
  $('#result').on('click', 'li', function() {
    var click_text = $(this).text().split('|');
    $('#ville').val($.trim(click_text[0]));
    $("#result").html('');
  });
});

$('#ville').focusout(function(e) {
  $('#result').hide(1100);
});

$('#ville').focusin(function(e) {
  $('#result').show();
});

$(document).ready(function() {
  $('#addfav').keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $.ajaxSetup({ cache: false });
  $('#addfav').keydown(function(e){
    if(e.which == 13) {
      $('#resultfavs').html('');
      $('#state').val('');
      var searchFav = $('#addfav').val();
      var expressionFav = new RegExp('^'+searchFav, "i");
      $.getJSON('city.list.json', function(data) {
        $.each(data, function(key, value){
          if(value.name.search(expressionFav) != -1) {
            $('#resultfavs').append('<a href="profil.php?addlat='+value.coord.lat+'&amp;addlon='+value.coord.lon+'"><li class="list-group-item link-class"> '+value.name+' | <span class="text-muted">'+value.country+'</span></li></a>');
          }
        });   
      });
    }
  });
  
  $('#resultfavs').on('click', 'li', function() {
    var click_fav = $(this).text().split('|');
    $('#addfav').val($.trim(click_fav[0]));
    $("#resultfavs").html('');
  });
});

$('#addfav').focusout(function(e) {
  $('#resultfavs').hide(1100);
});

$('#addfav').focusin(function(e) {
  $('#resultfavs').show();
});

$('#star').on('click', function () {
  var citylat = citypos[0].lat
  var citylon = citypos[0].lon
  if(typeof id === 'undefined') {
    window.location.href = "connexion.html";
  } else {
    if(!$('#star-icon').hasClass('checked-star')) {
      $('#star-icon').addClass('checked-star');
      $.get('ajax.php', {
        latitude: citylat,
        longitude: citylon,
        id: id
      });
    } else if($('#star-icon').hasClass('checked-star')) {
      $('#star-icon').removeClass('checked-star');
      $.get('ajax.php', {
        suplatitude: citylat,
        suplongitude: citylon,
        id: id
      });
    }
  }  
});

var checkbox = document.getElementById("switchRappel");
if(checkbox) {
  checkbox.addEventListener('click', function() {
    if(checkbox.checked == true) {
      document.getElementById('daterappel').removeAttribute('readonly');
      document.getElementById('villerappel').removeAttribute('readonly');
    } else {
      document.getElementById('daterappel').setAttribute('readonly', true);
      document.getElementById('villerappel').setAttribute('readonly', true);
    }
  });

  $(document).ready(function() {
    if(checkbox.checked == true) {
      document.getElementById('daterappel').removeAttribute('readonly');
      document.getElementById('villerappel').removeAttribute('readonly');
    }
  });
}

