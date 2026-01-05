<?php
// pwd/qr_login_scan.php
// PWD QR login scanner page – uses camera to read QR and redirect back to login

require_once __DIR__ . '/../config/auth.php';
// no need for DB here
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PWD QR Login</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            text-align: center;
        }
        #reader {
            width: 320px;
            margin: 0 auto;
        }
        .info {
            margin-bottom: 10px;
        }
        a.back-link {
            display: inline-block;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <h2>PWD QR Login</h2>
    <p class="info">
        I-allow ang camera, tapos i-scan ang QR code sa PWD ID.<br>
        Kapag na-scan, automatic babalik sa login na may naka-fill na PWD ID.
    </p>

    <div id="reader"></div>

    <a href="/pwd-employment-system/index.php" class="back-link">&larr; Back to Login</a>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Expecting format: "PWDQR:06-01-0001-0000123"
            if (decodedText.startsWith('PWDQR:')) {
                const pwdId = decodedText.substring(6); // remove 'PWDQR:'
                // Redirect back to main login page with ?qr_pwd=<PWD ID>
                window.location.href = "/pwd-employment-system/index.php?qr_pwd=" + encodeURIComponent(pwdId);
            } else {
                alert("Invalid QR code. This QR is not for PWD login.");
            }
        }

        function onScanFailure(error) {
            // tahimik lang; gagalaw lang pag may success
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: 250 }
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>

</body>
</html>