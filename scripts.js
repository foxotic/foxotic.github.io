/*$('#exampleModal').on('hide.bs.modal', function(e) {    
  var $if = $(e.delegateTarget).find('iframe');
  var src = $if.attr("src");
  $if.attr("src", '/empty.html');
  $if.attr("src", src);
});
*/
/*
if (history.scrollRestoration) {
  history.scrollRestoration = 'manual';
} else {
  window.onbeforeunload = function () {
      window.scrollTo(0, 0);
  }
}
*/

$(document).ready(function(){
	$('#vid-icon,.video-play-button1').click(function(){
  		$('#exampleModal').modal('show')
	});
});

$(document).ready(function(){
	$('#vid-icon').hover(function(){
    $(this).addClass("animate__bounce");
	});
});


$(document).ready(function(){
	$('#vid-icon,.booknow').click(function(){
  		$('#EpicModal').modal('show')
	});
});






  var url = $("#landing-vid").attr('src');
    
  /* Remove iframe src attribute on page load to
  prevent autoplay in background */
  $("#landing-vid").attr('src', '');

/* Assign the initially stored url back to the iframe src
  attribute when modal is displayed */
  $("#exampleModal").on('shown.bs.modal', function(){
      $("#landing-vid").attr('src', url);
  });
  
  /* Assign empty url value to the iframe src attribute when
  modal hide, which stop the video playing */
  $("#exampleModal").on('hide.bs.modal', function(){
      $("#landing-vid").attr('src', '');
  });







  



/* NAVIGATION LOGO SCROLL TOP */

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
      e.preventDefault();

      document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
      });
  });
});




const faders = document.querySelectorAll('.fade-in');

const appearOptions = {
  threshold: 1
};


const appearOnScroll = new IntersectionObserver
(function(
  entries,
  appearOnScroll
  ) {
    entries.forEach(entry => {
      if (!entry.isIntersecting) {
         return;
      } else {
       entry.target.classList.add("appear");
       appearOnScroll.unobserve(entry.target);
      }
    })
  },
  appearOptions);

  faders.forEach(fader => {
    appearOnScroll.observe(fader);

  });



  // Scroll down 150 px
  
  $(document).ready(function(){
    $('.mouseA').click(function(){
      window.scrollBy(0,1000);
      
    }); 
  });

  