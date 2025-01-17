<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/loading.css">
  <style>
    /* Updated loading design */
    .loading-screen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: white;
      z-index: 9999;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    /* Flower loader */
    .flower {
      width: 300px;
      height: 300px;
      display: flex;
      justify-content: center;
      align-items: center;
      animation: rotateFlower 5s infinite ease-in-out;
    }

    .petal {
      position: absolute;
      width: 35px;
      height: 60px;
      background: linear-gradient(180deg, #fcdbdf, #fd688d);
      border-radius: 50%;
    }

   .petal1 {
  transform: rotate(0deg) translateY(-50%);
  animation-delay: 0.1s;
}

.petal2 {
  transform: rotate(45deg) translateY(-50%);
  animation-delay: 0.2s;
}

.petal3 {
  transform: rotate(90deg) translateY(-50%);
  animation-delay: 0.3s;
}

.petal4 {
  transform: rotate(135deg) translateY(-50%);
  animation-delay: 0.4s;
}

.petal5 {
  transform: rotate(180deg) translateY(-50%);
  animation-delay: 0.5s;
}

.petal6 {
  transform: rotate(225deg) translateY(-50%);
  animation-delay: 0.6s;
}

.petal7 {
  transform: rotate(270deg) translateY(-50%);
  animation-delay: 0.7s;
}

.petal8 {
  transform: rotate(315deg) translateY(-50%);
  animation-delay: 0.8s;
}
    .center {
      position: absolute;
      width: 30px;
      height: 30px;
      background-color: #f1d2d2;
      border-radius: 50%;
    }

    flower:hover .petal {
  animation-name: changeColor;
  animation-duration: 8s;
  animation-direction: reverse;
  animation-iteration-count: infinite;
}

.flower:hover {
  animation-name: rotateFlower;
  animation-duration: 8s;
  animation-iteration-count: infinite;
  animation-timing-function: ease;
}

    @keyframes rotateFlower {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }
  </style>
</head>
<body>
  <div class="loading-screen">
    <div class="flower">
      <div class="petal petal1"></div>
      <div class="petal petal2"></div>
      <div class="petal petal3"></div>
      <div class="petal petal4"></div>
      <div class="petal petal5"></div>
      <div class="petal petal6"></div>
      <div class="petal petal7"></div>
      <div class="petal petal8"></div>
      <div class="center"></div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(window).on('load', function () {
      // Keep the loading screen for a short duration before fading it out
      setTimeout(function () {
        $(".loading-screen").fadeOut(1000, function () {
          $("#content").fadeIn(1000);
        });
      }, 1500);
    });

    $(document).ajaxStart(function () {
      $(".loading-screen").fadeIn(1000);
    }).ajaxStop(function () {
      $(".loading-screen").fadeOut(1000);
    });
  </script>
</body>
</html>
