<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>food</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
        /* general styles */

body {
  font-family: "Raleway", sans-serif !important;
}

ul {
  list-style-type: none;
}

a {
  text-decoration: none !important;
}

h1 {
  color: #3b5ffd;
  font-weight: bold;
}

p {
  font-size: 20px;
  color: #484848;
}

.section {
  text-align: center;
}

.skillbar {
  cursor: pointer;
}

.hide {
  display: none;
}

.display {
  display: block;
}

/* flash alert styles */

.flash-container {
  width: 100%;
  position: fixed;
  z-index: 700;
  padding-top: 20px;
  font-weight: bold;
}

.alert {
  text-align: center !important;
  width: 500px !important;
  margin-left: auto !important;
  margin-right: auto !important;
  color: #211809 !important;
}

/* Nav Screen */

.nav-screen {
  height: 100%;
  z-index: 500;
  position: fixed;
  background-color: #000;
  opacity: 1;
  right: -285px; /* start off behind the scenes */
  -webkit-font-smoothing: antialiased;
  position: fixed;
  width: 285px;
}

.nav-screen .active {
  display: block;
}

/* nav header links */

.nav-brand {
  position: fixed;
  left: 0;
  z-index: 600;
  padding: 10px;
}

.nav-brand img {
  width: 50px;
}

.fa-bars {
  display: none !important;
  color: #ff6363;
  float: right;
  padding: 20px;
  position: fixed;
  right: 0;
  z-index: 500;
  cursor: pointer;
}

.fa-times {
  color: #ff6363;
  float: right;
  padding: 20px;
  right: 0;
  z-index: 600;
  cursor: pointer;
}

.header-links {
  position: fixed;
  width: 100%;
  z-index: 500;
}

.header-links li {
  color: white;
  display: inline;
  float: right;
  padding: 15px;
  font-size: 16px;
}

.header-links a {
  color: #757575;
  -webkit-transition: all 0.2s ease-in;
}

.header-links a:hover {
  color: #ff6363 !important;
}

/* slide out nav menu styles */

.nav-container {
  padding-top: 10%;
}

.nav-links {
  width: 250px;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
}

.nav-links ul {
  margin-top: 15%;
  text-align: center;
  padding-top: 10%;
}

.nav-links a {
  color: #ffffff;
  font-size: 30px;
  line-height: 2.5;
  font-weight: bold;
  text-decoration: none;
  margin-left: auto;
  margin-right: auto;
}

.nav-links a:hover {
  color: #ff6363;
  -webkit-transition: all 0.6s ease-in-out;
  -o-transition: all 0.6s ease-in-out;
  -ms-transition: all 0.6s ease-in-out;
  -moz-transition: all 0.6s ease-in-out;
  transition: all 0.6s ease-in-out;
}

#fp-nav ul li .fp-tooltip {
  color: #5f5f5f !important;
}

/* Expanding border effect */

.header-links a::after {
  display: block;
  margin: 0 auto;
  margin-top: 5px;
  width: 0;
  height: 2px;
  background-color: #fff;
  content: "";
  opacity: 0;
  -webkit-transition: width 0.6s, opacity 0.8s;
  -moz-transition: width 0.6s, opacity 0.8s;
  transition: width 0.6s, opacity 0.8s;
}

.header-links a:hover::after,
.header-links a:focus::after {
  opacity: 1;
  width: 100%;
}

/* main header section */

.opaque-bg {
  margin-left: auto;
  margin-right: auto;
  max-width: 380px;
  padding: 1px 1px 5px 5px;
}

.aboutme {
  background: url("https://s3-us-west-2.amazonaws.com/files.bradengelhardt.com/brad-bg-main.jpg")
    no-repeat;
  background-size: cover;
}

.aboutme p {
  color: white;
  font-weight: 100;
  font-size: 17.4px;
}

/* bouncing arrow */

.fa-chevron-down {
  color: #ff6363 !important;
  bottom: 10px;
  margin-left: -30px;
  cursor: pointer;
  position: absolute;
  opacity: 0;
  -webkit-transition: 1.2s ease;
}

/* Scroll down indicator (bouncing) */

@-webkit-keyframes bounce {
  0%,
  20%,
  50%,
  80%,
  100% {
    -webkit-transform: translateY(0);
    transform: translateX(0);
  }
  40% {
    -webkit-transform: translateY(-30px);
    transform: translateY(-30px);
  }
  60% {
    -webkit-transform: translateY(-15px);
    transform: translateY(-15px);
  }
}

@-moz-keyframes bounce {
  0%,
  20%,
  50%,
  80%,
  100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-30px);
  }
  60% {
    transform: translateY(-15px);
  }
}

@keyframes bounce {
  0%,
  20%,
  50%,
  80%,
  100% {
    -ms-transform: translateY(0);
    transform: translateY(0);
  }
  40% {
    -ms-transform: translateY(-30px);
    transform: translateY(-30px);
  }
  60% {
    -ms-transform: translateY(-15px);
    transform: translateY(-15px);
  }
}

.bounce {
  -webkit-animation: bounce 2s infinite;
  animation: bounce 2s infinite;
}

/* about section */

/* Skill bars */

.container-skillbar {
  width: 100%;
  padding-top: 30px;
  padding-right: 2%;
  padding-left: 2%;
  height: auto;
  overflow: none;
}
.skillbar {
  position: relative;
  display: block;
  margin-bottom: 15px;
  width: 100%;
  background: #efefef;
  height: 30px;
  border-radius: 3px;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
  -webkit-transition: 0.4s linear;
  -moz-transition: 0.4s linear;
  -ms-transition: 0.4s linear;
  -o-transition: 0.4s linear;
  transition: 0.4s linear;
  -webkit-transition-property: width, background-color;
  -moz-transition-property: width, background-color;
  -ms-transition-property: width, background-color;
  -o-transition-property: width, background-color;
  transition-property: width, background-color;
}

.skillbar-title {
  position: absolute;
  top: 0;
  left: 0;
  width: 110px;
  font-weight: bold;
  font-size: 13px;
  color: #fff;
  background: #6adcfa;
  -webkit-border-top-left-radius: 3px;
  -webkit-border-bottom-left-radius: 4px;
  -moz-border-radius-topleft: 3px;
  -moz-border-radius-bottomleft: 3px;
  border-top-left-radius: 3px;
  border-bottom-left-radius: 3px;
}

.skillbar-title span {
  display: block;
  background: rgba(0, 0, 0, 0.15);
  padding: 0 20px;
  height: 30px;
  line-height: 30px;
  -webkit-border-top-left-radius: 3px;
  -webkit-border-bottom-left-radius: 3px;
  -moz-border-radius-topleft: 3px;
  -moz-border-radius-bottomleft: 3px;
  border-top-left-radius: 3px;
  border-bottom-left-radius: 3px;
}

.skillbar-bar {
  height: 30px;
  width: 0px;
  border-radius: 3px;
  -moz-border-radius: 3px;
  -webkit-border-radius: 3px;
}

.skill-bar-percent {
  position: absolute;
  right: 10px;
  top: 0;
  font-size: 11px;
  height: 30px;
  line-height: 30px;
  color: #ffffff;
  color: rgba(0, 0, 0, 0.5);
}

/* portfolio section */

.content {
  margin-left: auto;
  margin-right: auto;
  max-width: 800px;
}

.content-slide {
  margin-left: auto;
  margin-right: auto;
  max-width: 800px;
}

.content-slide img {
  width: 70%;
}

.fp-controlArrow.fp-next {
  border-color: transparent transparent transparent #ff6363 !important;
  right: 45px !important;
}

.fp-controlArrow.fp-prev {
  border-color: transparent #ff6363 transparent transparent !important;
  left: 45px !important;
}

#fp-nav ul li a span,
.fp-slidesNav ul li a span {
  background: rgba(0, 0, 0, 0.62) !important;
}

/* contact us styles */

.fa-paper-plane {
  color: #484848;
}

/* paper plane shake on hover */

.plane-animated {
  animation-duration: 6s;
  animation-fill-mode: both;
  animation-iteration-count: infinite;
  animation-play-state: paused;
}

.fa-paper-plane:hover {
  /* Toggle our animation play state to running when we are hovering over our sticker */
  animation-play-state: running;
}

@keyframes shake {
  0%,
  100% {
    transform: translateX(0);
  }
  10%,
  30%,
  50%,
  70%,
  90% {
    transform: translateX(-10px);
  }
  20%,
  40%,
  60%,
  80% {
    transform: translateX(10px);
  }
}
.shake {
  animation-name: shake;
}

/* input field styles */

.contact-form {
  padding: 0px 15px 0px 15px;
}

.form-control {
  border: 2px solid rgba(0, 0, 0, 0.27);
  width: 100%;
  max-width: 100%;
  height: 3em;
  font-size: 16px;
  padding: 1em;
  margin: 0.5em 0 2em 0;
  font-weight: bold;
}

textarea {
  min-height: 10em;
}

label {
  display: inline-block;
  max-width: 100%;
  margin-bottom: 5px;
  font-size: 18px;
  text-transform: uppercase;
  font-weight: 500;
}

#submit {
  outline: none;
  padding: 12px;
  min-width: 200px;
  border-radius: 2px;
  border: 2px solid #ff6363;
  text-align: center;
  font-size: 1em;
  color: #ff6363;
  background-color: white;
  text-decoration: none;
  text-transform: uppercase;
  font-weight: bold;
}

#submit:hover {
  background-color: #ff6363;
  color: white;
  -webkit-transition: all 0.3s ease-out;
  -o-transition: all 0.3s ease-out;
  -ms-transition: all 0.3s ease-out;
  -moz-transition: all 0.3s ease-out;
  transition: all 0.3s ease-out;
}

/* footer styles */

.footer {
  background-color: #484848;
  height: 100px;
  padding-top: 10px;
}

.footer p {
  color: white;
  font-weight: bold;
  font-size: 16px;
}

.social-links a {
  color: white;
  padding: 0px 5px 0px 5px;
}

.social-links a:hover {
  color: #ff6363;
  -webkit-transition: all 0.2s ease-in-out;
  -o-transition: all 0.2s ease-in-out;
  -ms-transition: all 0.2s ease-in-out;
  -moz-transition: all 0.2s ease-in-out;
  transition: all 0.2s ease-in-out;
}

/* smaller resolution styles */

@media (min-width: 100px) and (max-width: 500px) {
  .fa-bars {
    display: block !important;
  }

  .header-links {
    display: none;
  }
}

/* mobile styles */

@media (min-width: 100px) and (max-width: 400px) {
  .content {
    max-width: 300px;
  }

  .aboutme h1 {
    font-size: 28px;
  }
}

/* loading screen animation */

.spinner {
  position: fixed;
  width: 100%;
  height: 100%;
  padding: 25%;
  background-color: #2f2f2f;
  text-align: center;
  font-size: 10px;
  z-index: 1000;
}

.spinner > div {
  background-color: #ff6363;
  height: 100%;
  width: 6px;
  display: inline-block;

  -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
  animation: sk-stretchdelay 1.2s infinite ease-in-out;
}

.spinner .rect2 {
  -webkit-animation-delay: -1.1s;
  animation-delay: -1.1s;
}

.spinner .rect3 {
  -webkit-animation-delay: -1s;
  animation-delay: -1s;
}

.spinner .rect4 {
  -webkit-animation-delay: -0.9s;
  animation-delay: -0.9s;
}

.spinner .rect5 {
  -webkit-animation-delay: -0.8s;
  animation-delay: -0.8s;
}

@-webkit-keyframes sk-stretchdelay {
  0%,
  40%,
  100% {
    -webkit-transform: scaleY(0.4);
  }
  20% {
    -webkit-transform: scaleY(1);
  }
}

@keyframes sk-stretchdelay {
  0%,
  40%,
  100% {
    transform: scaleY(0.4);
    -webkit-transform: scaleY(0.4);
  }
  20% {
    transform: scaleY(1);
    -webkit-transform: scaleY(1);
  }
}

.blinking-cursor {
  font-weight: 500;
  margin-left: 4px;
  font-size: 20px;
  color: white !important;
  -webkit-animation: 1s blink step-end infinite;
  -moz-animation: 1s blink step-end infinite;
  -ms-animation: 1s blink step-end infinite;
  -o-animation: 1s blink step-end infinite;
  animation: 1s blink step-end infinite;
}

@keyframes "blink" {
  from,
  to {
    color: transparent;
  }
  50% {
    color: white;
  }
}

@-moz-keyframes blink {
  from,
  to {
    color: transparent;
  }
  50% {
    color: white;
  }
}

@-webkit-keyframes "blink" {
  from,
  to {
    color: transparent;
  }
  50% {
    color: white;
  }
}

@-ms-keyframes "blink" {
  from,
  to {
    color: transparent;
  }
  50% {
    color: white;
  }
}

@-o-keyframes "blink" {
  from,
  to {
    color: transparent;
  }
  50% {
    color: white;
  }
}

.success {
  padding: 1em;
  margin-bottom: 0.75rem;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
  color: #468847;
  background-color: #dff0d8;
  border: 1px solid #d6e9c6;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}

.error {
  padding: 1em;
  margin-bottom: 0.75rem;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
  color: #b94a48;
  background-color: #f2dede;
  border: 1px solid rgba(185, 74, 72, 0.3);
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}
</style>
    </head>
    <body>
  <!-- navbar header -->
  <div class="nav-header">
    <div class="nav-brand">
      <img src="https://s3-us-west-2.amazonaws.com/parallax-theme/assets/bradlogo2.png">
    </div>
    <i class="fa fa-bars fa-3x"></i>
    <div class="header-links">
      <ul>
        <li data-menuanchor="fourthPage"><a href="#contact">CONTACT</a></li>
        <li data-menuanchor="thirdPage"><a href="#portfolio">PORTFOLIO</a></li>
        <li data-menuanchor="secondPage"><a href="#about">ABOUT ME</a></li>
      </ul>
    </div>
  </div>
  <!-- end navbar header -->

  <!-- sidebar slider -->
  <div class="nav-screen">
    <i class="fa fa-times fa-3x"></i>
    <div class="nav-container">
      <div class="nav-links">
        <ul id='myMenu'>
          <li data-menuanchor="secondPage"><a href="#about">ABOUT</a></li>
          <li data-menuanchor="thirdPage"><a href="#portfolio">PORTFOLIO</a></li>
          <li data-menuanchor="fourthPage"><a href="#contact">CONTACT ME</a></li>
        </ul>
      </div>
    </div>
  </div>
  <!-- end navbar slider -->

  <!-- begin fullpage -->

  <div id="fullpage">

    <!-- begin section -->

    <div class="section aboutme" data-anchor="aboutme">
      <div class="opaque-bg animated fadeInDown">
        <h1 style="color:white">food<span style="color:#FF6363">/</span>App</h1>
        <p><span id="holder"></span><span class="blinking-cursor">|</span></p>
      </div>
      <i id="moveDown" class="fa fa-chevron-down fa-3x bounce"></i>
    </div>
    <!-- end section -->

    <!-- begin section -->

    <div class="section" data-anchor="skills">
      <div class="content">
        <h1 class="wow fadeInDown" data-wow-delay="0.2s">ABOUT ME</h1>
        <p class="wow fadeInDown" data-wow-delay="0.2s">Hi, I'm food ! I'm a South African based Front-end Web Engineer and SEO Specialist focused on creating clean, responsive web designs that laods fast and are a pleasure to use!</p>

        <div class=" wow fadeInUp container-skillbar" data-wow-delay="0.2s">
          <div class="skillbar clearfix " data-percent="90%">
            <div class="skillbar-title" style="background: #DD1E2F;"><span>HTML5</span></div>
            <div class="skillbar-bar" style="background: #DD1E2F;"></div>
            <div class="skill-bar-percent">90%</div>
          </div> <!-- End Skill Bar -->

          <div class="skillbar clearfix " data-percent="80%">
            <div class="skillbar-title" style="background: #EBB035;"><span>CSS3</span></div>
            <div class="skillbar-bar" style="background: #EBB035;"></div>
            <div class="skill-bar-percent">80%</div>
          </div> <!-- End Skill Bar -->

          <div class="skillbar clearfix " data-percent="75%">
            <div class="skillbar-title" style="background: #218559;"><span>Photoshop</span></div>
            <div class="skillbar-bar" style="background: #218559;"></div>
            <div class="skill-bar-percent">75%</div>
          </div> <!-- End Skill Bar -->

          <div class="skillbar clearfix " data-percent="45%">
            <div class="skillbar-title" style="background: #6840D4;"><span>Rails</span></div>
            <div class="skillbar-bar" style="background: #6840D4;"></div>
            <div class="skill-bar-percent">45%</div>
          </div> <!-- End Skill Bar -->

          <div class="skillbar clearfix " data-percent="30%">
            <div class="skillbar-title" style="background: #06A2CB;"><span>jQuery</span></div>
            <div class="skillbar-bar" style="background: #06A2CB;"></div>
            <div class="skill-bar-percent">30%</div>
          </div> <!-- End Skill Bar -->

          <div class="skillbar clearfix " data-percent="20%">
            <div class="skillbar-title" style="background: #AB4DD2;"><span>PHP</span></div>
            <div class="skillbar-bar" style="background: #AB4DD2;"></div>
            <div class="skill-bar-percent">20%</div>
          </div> <!-- End Skill Bar -->
        </div>
      </div>
    </div>
    <!-- end section -->

    <!-- begin section -->

    <div class="section" data-anchor="projects">
      <div class="content-slide">
        <div class="slide">
          <h1 class="wow fadeInDown" data-wow-delay="0.2s">XFAIRE</h1>
          <p class="wow fadeInDown" data-wow-delay="0.2s">Digital streaming service</p>
          <img class="wow fadeInUp" data-wow-delay="0.2s" data-src="https://s3-us-west-2.amazonaws.com/bradengelhardt/assets/xfaire.jpg">
        </div>
        <div class="slide">
          <h1>MACHETE ENERGY</h1>
          <p>Energy drink website</p>
          <img data-src="https://s3-us-west-2.amazonaws.com/bradengelhardt/assets/machete.jpg">
        </div>
        <div class="slide">
          <h1>PORTFOLIO</h1>
          <p>Mockup of a personal portfolio I was working on for a friend.</p>
          <img data-src="https://s3-us-west-2.amazonaws.com/bradengelhardt/assets/cascioportfolio.jpg">
        </div>
        <div class="slide">
          <h1>THATONEBLOG</h1>
          <p>Minimalist blogging platform.</p>
          <img data-src="https://s3-us-west-2.amazonaws.com/bradengelhardt/assets/blog.jpg">
        </div>
        <div class="slide">
          <h1>PIXBORED</h1>
          <p>Website for sharing images and gifs.</p>
          <img data-src="https://s3-us-west-2.amazonaws.com/bradengelhardt/assets/pixbored.jpg">
        </div>
        <div class="slide">
          <h1>PERSONAL WEBSITE</h1>
          <p>Responsive vertical/horizontal header website theme.</p>
          <img data-src="https://s3-us-west-2.amazonaws.com/bradengelhardt/assets/personalsite.jpg">
        </div>
      </div>
    </div>
    <!-- end section -->

    <!-- begin section -->

    <div class="section" data-anchor="contact">
      <div class="content wow fadeInDown" data-wow-delay="0.2s">
        <h1>CONTACT ME</h1>
        <p>If you are looking for a great website, online store or more organic traffic for your site, you have come to the right place! I am a professional web developer and SEO expert who can help you achieve your online goals. Contact me today and let's get started!</p>
        <br>
        <div class="contact-form">

          <div id="form-messages"></div>
          <form id="ajax-contact" method="post" role="form" action="contact.php" data-toggle="validator">
            <div class="form-group has-feedback wow fadeInLeft" data-wow-delay="0.6s">
              <input type="text" class="form-control" id="name" name="name" placeholder="NAME" data-error="Field can't be blank!" required>
              <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback wow fadeInRight" data-wow-delay="0.8s">
              <input type="email" class="form-control" id="email" name="email" placeholder="EMAIL" data-error="Field can't be blank!" required>
              <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="form-group has-feedback wow fadeInLeft" data-wow-delay="1s">
              <textarea data-minlength="10" class="form-control" id="message" name="message" placeholder="MESSAGE" data-error="Minimum of 10 characters" required></textarea>
              <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
              <div class="help-block with-errors"></div>
            </div>

            <div class="hidden">
              <input type="text" class="form-control" id="human" name="human" placeholder="">
            </div>
            <div class="wow fadeInUp" data-wow-delay="1s">
              <button type="submit" id="submit" name="submit" class="btn btn-lg">SEND MESSAGE</button>
            </div>
          </form>
          <br>
        </div>
      </div>
    </div>
    <!-- end section -->

    <!-- begin section -->

    <div class="section fp-auto-height">
      <div class="footer">
        <p>FOLLOW MY SOCIAL MEDIA PAGES!</p>
        <div class="social-links">
          <span><a href="https://www.linkedin.com/in/food-le-roux" target="_blank"><i class="fa fa-linkedin fa-2x"></i></a></span>
          <span><a target="_blank" href="https://codepen.io/wlleroux84" target="_blank"><i class="fa fa-codepen fa-2x"></i></a></span>
          <span><a href="https://www.facebook.com/Digimatics-Web-Digital-Solutions" target="_blank"><i class="fa fa-facebook fa-2x"></i></a></span>
          <span><a href="https://github.com/wlleroux84"><i class="fa fa-github fa-2x"></i></a></span>
        </div>
      </div>
    </div>
    <!-- end section -->

  </div>
  <!-- end fullpage -->
    
    <script>
        $(document).ready(function () {
  // typing animation
  (function ($) {
    $.fn.writeText = function (content) {
      var contentArray = content.split(""),
        current = 0,
        elem = this;
      setInterval(function () {
        if (current < contentArray.length) {
          elem.text(elem.text() + contentArray[current++]);
        }
      }, 80);
    };
  })(jQuery);

  // input text for typing animation
  $("#holder").writeText("Front-end Web Engineer + SEO Specialist");

  // initialize wow.js
  new WOW().init();

  // Push the body and the nav over by 285px over
  var main = function () {
    $(".fa-bars").click(function () {
      $(".nav-screen").animate(
        {
          right: "0px"
        },
        200
      );

      $("body").animate(
        {
          right: "285px"
        },
        200
      );
    });

    // Then push them back */
    $(".fa-times").click(function () {
      $(".nav-screen").animate(
        {
          right: "-285px"
        },
        200
      );

      $("body").animate(
        {
          right: "0px"
        },
        200
      );
    });

    $(".nav-links a").click(function () {
      $(".nav-screen").animate(
        {
          right: "-285px"
        },
        500
      );

      $("body").animate(
        {
          right: "0px"
        },
        500
      );
    });
  };

  $(document).ready(main);

  // initiate full page scroll

  $("#fullpage").fullpage({
    scrollBar: true,
    responsiveWidth: 400,
    navigation: true,
    navigationTooltips: ["home", "about", "portfolio", "contact", "connect"],
    anchors: ["home", "about", "portfolio", "contact", "connect"],
    menu: "#myMenu",
    fitToSection: false,

    afterLoad: function (anchorLink, index) {
      var loadedSection = $(this);

      //using index
      if (index == 1) {
        /* add opacity to arrow */
        $(".fa-chevron-down").each(function () {
          $(this).css("opacity", "1");
        });
        $(".header-links a").each(function () {
          $(this).css("color", "white");
        });
        $(".header-links").css("background-color", "transparent");
      } else if (index != 1) {
        $(".header-links a").each(function () {
          $(this).css("color", "black");
        });
        $(".header-links").css("background-color", "white");
      }

      //using index
      if (index == 2) {
        /* animate skill bars */
        $(".skillbar").each(function () {
          $(this)
            .find(".skillbar-bar")
            .animate(
              {
                width: $(this).attr("data-percent")
              },
              2500
            );
        });
      }
    }
  });

  // move section down one
  $(document).on("click", "#moveDown", function () {
    $.fn.fullpage.moveSectionDown();
  });

  // fullpage.js link navigation
  $(document).on("click", "#skills", function () {
    $.fn.fullpage.moveTo(2);
  });

  $(document).on("click", "#projects", function () {
    $.fn.fullpage.moveTo(3);
  });

  $(document).on("click", "#contact", function () {
    $.fn.fullpage.moveTo(4);
  });

  // smooth scrolling
  $(function () {
    $("a[href*=#]:not([href=#])").click(function () {
      if (
        location.pathname.replace(/^\//, "") ==
          this.pathname.replace(/^\//, "") &&
        location.hostname == this.hostname
      ) {
        var target = $(this.hash);
        target = target.length
          ? target
          : $("[name=" + this.hash.slice(1) + "]");
        if (target.length) {
          $("html,body").animate(
            {
              scrollTop: target.offset().top
            },
            700
          );
          return false;
        }
      }
    });
  });

  //ajax form
  $(function () {
    // Get the form.
    var form = $("#ajax-contact");

    // Get the messages div.
    var formMessages = $("#form-messages");

    // Set up an event listener for the contact form.
    $(form).submit(function (e) {
      // Stop the browser from submitting the form.
      e.preventDefault();

      // Serialize the form data.
      var formData = $(form).serialize();

      // Submit the form using AJAX.
      $.ajax({
        type: "POST",
        url: $(form).attr("action"),
        data: formData
      })
        .done(function (response) {
          // Make sure that the formMessages div has the 'success' class.
          $(formMessages).removeClass("error");
          $(formMessages).addClass("success");

          // Set the message text.
          $(formMessages).text(response);

          // Clear the form.
          $("#name").val("");
          $("#email").val("");
          $("#message").val("");
        })
        .fail(function (data) {
          // Make sure that the formMessages div has the 'error' class.
          $(formMessages).removeClass("success");
          $(formMessages).addClass("error");

          // Set the message text.
          if (data.responseText !== "") {
            $(formMessages).text(data.responseText);
          } else {
            $(formMessages).text(
              "Oops! An error occured and your message could not be sent."
            );
          }
        });
    });
  });
});

    </script>
    
  
  
</body>

</html>
