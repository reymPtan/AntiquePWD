<?php
require_once __DIR__.'/../config/database.php';

/*
 QR LOGIN VIA IMAGE UPLOAD

 Flow:
 1. Upload QR image
 2. API decodes QR
 3. Extract PWD ID
 4. Redirect to index.php?qr_pwd=PWD_ID
*/

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['qr_image']) || $_FILES['qr_image']['error'] !== 0) {
        $error = "Please select a valid QR image.";
    } else {

        $tmpPath = $_FILES['qr_image']['tmp_name'];

        // ---------- API Decoder ----------
        // Using api.qrserver.com
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.qrserver.com/v1/read-qr-code/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file' => new CURLFile($tmpPath)
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($response, true);

        $decodedText = $json[0]['symbol'][0]['data'] ?? '';

        // ---------- Check result ----------
        if (!$decodedText) {
            $error = "QR could not be read. Please try a clearer photo.";
        } else {

            // Expect QR contains PWD ID only
            $pwdId = trim($decodedText);

            // Validate PWD format
            if (!preg_match('/^06-[0-9]{2}-[0-9]{4}-[0-9]{7}$/', $pwdId)) {
                $error = "QR does not contain a valid PWD ID.";
            } else {
                // Redirect to main unified login page
                header("Location: /pwd-employment-system/index.php?qr_pwd=$pwdId");
                exit;
            }
        }

    }

}

$pageTitle = "PWD QR Upload Login";
require_once __DIR__.'/../includes/header.php';
?>

<section class="card">
    <h2>Login Using QR Code</h2>

    <p>
        Upload a photo of your PWD QR Code to login automatically.
    </p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Upload QR Image</label>
        <input type="file" name="qr_image" accept="image/*" required>

        <button type="submit">Scan QR & Login</button>
    </form>

    <p class="small-note">
        Tip: Make sure the QR is clear and well-lit.
    </p>

    <p>
        <a href="/pwd-employment-system/index.php">Back to normal login</a>
    </p>

</section>

<?php require_once __DIR__.'/../includes/footer.php'; ?>