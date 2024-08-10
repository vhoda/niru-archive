<nav class="navbar navbar-expand-lg" id=navbar>

   <div class="container-fluid">
      <div class="navbar-nav d-flex flex-row clearfix">
         <a class="nav-link" href="http://vhoda.cl"><span class="fw-bolder" style="margin-right: -5px; font-size:30px;">vhoda</span></a>
         <a class="nav-link" href="/"><span class="fw-light" style="font-size:20px;">Archive</span></a>
      </div>
      <div class="d-flex align-items-center ms-auto">
         <a class="btn btn-secondary rounded-pill" href="https://github.com/vhoda"><i class="fa-brands fa-github px-1"></i> Github</a>
      </div>
   </div>
</nav>

<!-- If you want to change anything the ui-->

<script>
   // Array of the image every f5
   var backgroundImages = [
      'img/1.gif',
      'img/2.gif',
      'img/3.gif',
      'img/4.gif',
      'img/5.gif'
   ];
   function getRandomIndex(max) {
      return Math.floor(Math.random() * max);
   }
   function changeBackgroundImage() {
      var randomIndex = getRandomIndex(backgroundImages.length);
      var randomImage = backgroundImages[randomIndex];
      $('#navbar').css('background-image', 'url(' + randomImage + ')');
   }

   $(document).ready(function() {
      changeBackgroundImage();
   });
</script>