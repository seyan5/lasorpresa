<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/loading.css">
  <style>
    /* Fallback inline CSS for the loading screen */
    /* Loading screen: covers the entire viewport and is pure white */
    .loading-screen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: white; /* Pure white background */
      z-index: 9999; /* Ensure it stays on top of all other elements */
      display: flex;
      justify-content: center;
      align-items: center;
    }

    /* Optional loader style */
    .flower-loader {
      overflow: hidden;
      position: relative;
      text-indent: -9999px;
      display: inline-block;
      width: 16px;
      height: 16px;
      background: #e96;
      border-radius: 100%;
      box-shadow: white 0 0 15px 0, #e84393 -12px -12px 0 4px,
      #e84393 12px -12px 0 4px, #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px;
      animation: flower-loader 5s infinite ease-in-out;
      transform-origin: 50% 50%;
    }


@-moz-keyframes flower-loader {
    0% {
      -moz-transform: rotate(0deg);
      transform: rotate(0deg);
      -moz-box-shadow: white 0 0 15px 0, #e84393 -12px -12px 0 4px,
      #e84393 12px -12px 0 4px, #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px;
      box-shadow: white 0 0 15px 0, #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px,
      #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px;
    }
    50% {
      -moz-transform: rotate(1080deg);
      transform: rotate(1080deg);
      -moz-box-shadow: white 0 0 15px 0, #e84393 12px 12px 0 4px,
      #e84393 -12px 12px 0 4px, #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px;
      box-shadow: white 0 0 15px 0, #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px,
        #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px;
    }
  }
  @-webkit-keyframes flower-loader {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
      -webkit-box-shadow: white 0 0 15px 0, #e84393 -12px -12px 0 4px,
        #e84393 12px -12px 0 4px, #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px;
      box-shadow: white 0 0 15px 0, #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px,
        #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px;
    }
    50% {
      -webkit-transform: rotate(1080deg);
      transform: rotate(1080deg);
      -webkit-box-shadow: white 0 0 15px 0, #e84393 12px 12px 0 4px,
        #e84393 -12px 12px 0 4px, #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px;
      box-shadow: white 0 0 15px 0, #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px,
        #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px;
    }
  }
  @keyframes flower-loader {
    0% {
      -moz-transform: rotate(0deg);
      -ms-transform: rotate(0deg);
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
      -moz-box-shadow: white 0 0 15px 0, #e84393 -12px -12px 0 4px,
        #e84393 12px -12px 0 4px, #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px;
      -webkit-box-shadow: white 0 0 15px 0, #e84393 -12px -12px 0 4px,
        #e84393 12px -12px 0 4px, #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px;
      box-shadow: white 0 0 15px 0, #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px,
        #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px;
    }
    50% {
      -moz-transform: rotate(1080deg);
      -ms-transform: rotate(1080deg);
      -webkit-transform: rotate(1080deg);
      transform: rotate(1080deg);
      -moz-box-shadow: white 0 0 15px 0, #e84393 12px 12px 0 4px,
        #e84393 -12px 12px 0 4px, #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px;
      -webkit-box-shadow: white 0 0 15px 0, #e84393 12px 12px 0 4px,
        #e84393 -12px 12px 0 4px, #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px;
      box-shadow: white 0 0 15px 0, #e84393 12px 12px 0 4px, #e84393 -12px 12px 0 4px,
        #e84393 -12px -12px 0 4px, #e84393 12px -12px 0 4px;
    }
  }
  </style>
</head>
<body>
  <div class="loading-screen">
    <span class="flower-loader">Loading…</span>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(window).on('load', function () {
    // Keep the loading screen for an extended duration before fading it out
    setTimeout(function () {
      $(".loading-screen").fadeOut(1000, function () { // Fade-out duration: 2 seconds
        $("#content").fadeIn(1000); // Show the main content after fade-out
      });
    }, 1500); // Delay of 3000ms (3 seconds) before fading out
  });

  // Optional: Handle AJAX requests with a global loading indicator
  $(document).ajaxStart(function () {
    $(".loading-screen").fadeIn(1000); // Show loading screen during AJAX
  }).ajaxStop(function () {
    $(".loading-screen").fadeOut(1000); // Hide loading screen after AJAX completes
  });
</script>

</body>
</html>
