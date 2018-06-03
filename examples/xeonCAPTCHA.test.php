<?php

include 'vendor/autoload.php';

use neto737\xeonCAPTCHA;

$xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, false);

if (isset($_POST['CAPTCHA'])) {
    $captcha = $_POST['CAPTCHA'];

    $verify = $xeon->validateCAPTCHA($captcha);
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>xeonCAPTCHA Test Page</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>
        <main class="container">
            <br>
            <div class="col-md-12">
                <h1>xeonCAPTCHA</h1>
                <hr>
                <div class="col-md-4 offset-md-4">
                    <?php if (isset($verify) && $verify) { ?>
                        <div class="alert alert-success" role="alert">
                            Yeah! Correct CAPTCHA code.
                        </div>
                    <?php } ?>
                    <?php if (isset($verify) && !$verify) { ?>
                        <div class="alert alert-danger" role="alert">
                            Wrong CAPTCHA code, please try again!
                        </div>
                    <?php } ?>
                    <div class="card">
                        <div class="card-header">
                            xeonCAPTCHA Example
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="text-center">
                                    <img src="./displayCAPTCHA.php?type=default" class="img-fluid">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label for="xeonCAPTCHAExample">CAPTCHA:</label>
                                    <input type="text" class="form-control" name="CAPTCHA" id="xeonCAPTCHAExample" placeholder="Type the CAPTCHA here">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Try CAPTCHA!</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>