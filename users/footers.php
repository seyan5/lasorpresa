<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Untitled</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="../css/footer.css">
    <style>
        /* Ensures the body takes full height and uses flexbox layout */
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Content section takes available space */
        .content {
            flex: 1;
        }

        /* Footer styles */
        .footer-basic footer {
            background-color: #333;
            color: white;
            padding: 30px 0;
            text-align: center;
            position: relative;
            width: 100%;
        }

        .footer-basic .social {
            padding: 10px;
            text-align: center;
        }

        .footer-basic .list-inline {
            padding-left: 0;
            list-style: none;
            text-align: center;
        }

        .footer-basic .list-inline-item {
            display: inline-block;
            margin: 0 10px;
        }

        .footer-basic .list-inline-item a {
            color: white;
            text-decoration: none;
        }

        .footer-basic .copyright {
            color: white;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    

    <!-- Footer section -->
    <div class="footer-basic">
        <footer>
            <div class="social">
                <a href="#"><i class="icon ion-social-instagram"></i></a>
                <a href="#"><i class="icon ion-social-snapchat"></i></a>
                <a href="#"><i class="icon ion-social-twitter"></i></a>
                <a href="#"><i class="icon ion-social-facebook"></i></a>
            </div>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#">Home</a></li>
                <li class="list-inline-item"><a href="#">Services</a></li>
                <li class="list-inline-item"><a href="#">About</a></li>
                <li class="list-inline-item"><a href="#">Terms</a></li>
                <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
            </ul>
            <p class="copyright">Company Name © 2018</p>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.bundle.min.js"></script>

</body>

</html>
