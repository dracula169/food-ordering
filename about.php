<!-- PHP INCLUDES -->

<?php

    include "connect.php";
    include 'Includes/functions/functions.php';
    include 'Includes/templates/header.php';
    include 'Includes/templates/navbar.php';

    // Load website settings
    $stmt_web_settings = $con->prepare("SELECT * FROM website_settings");
    $stmt_web_settings->execute();
    $web_settings = $stmt_web_settings->fetchAll();

    $restaurant_name = "";
    $restaurant_email = "";
    $restaurant_address = "";
    $restaurant_phonenumber = "";
    $about_text = "";

    foreach ($web_settings as $option)
    {
        if($option['option_name'] == 'restaurant_name')
        {
            $restaurant_name = $option['option_value'];
        }
        elseif($option['option_name'] == 'restaurant_email')
        {
            $restaurant_email = $option['option_value'];
        }
        elseif($option['option_name'] == 'restaurant_phonenumber')
        {
            $restaurant_phonenumber = $option['option_value'];
        }
        elseif($option['option_name'] == 'restaurant_address')
        {
            $restaurant_address = $option['option_value'];
        }
        elseif($option['option_name'] == 'about_us')
        {
            $about_text = $option['option_value'];
        }
    }

    // Apply the same overrides used elsewhere so contact info matches
    $restaurant_address = "+255 posta Dar es salaam.";
    $restaurant_headquarters = "Dar es salaam Tanzania";
    $restaurant_email = "tumainigalla@gmail.com";
    $restaurant_phonenumber = "0740013976";

?>

<section class="about-page" style="padding:100px 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>ABOUT TUSGUN RESTAURANT</h1>
                <p style="font-size:18px;line-height:1.8;">
                    <?php
                        if (!empty($about_text)) {
                            echo nl2br(htmlspecialchars($about_text));
                        } else {
                            echo "TUSGUN RESTAURANT is committed to serving delicious, authentic dishes made from fresh ingredients. We combine traditional recipes with modern techniques to bring you memorable flavours. Our team is dedicated to excellent service and creating a warm dining experience for every guest.";
                        }
                    ?>
                </p>

                <h4>Headquarters</h4>
                <p><?php echo isset($restaurant_headquarters) ? htmlspecialchars($restaurant_headquarters) : htmlspecialchars($restaurant_address); ?></p>

                <h4>Contact</h4>
                <p>Email: <?php echo htmlspecialchars($restaurant_email); ?><br>Phone: <?php echo htmlspecialchars($restaurant_phonenumber); ?></p>
                <?php
                    // Admin-editable form (protected by admin session)
                    session_start();
                    $isAdmin = isset($_SESSION['username_restaurant_qRewacvAqzA']) && isset($_SESSION['password_restaurant_qRewacvAqzA']);
                    if ($isAdmin) {
                        // Handle form submission
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['about_save'])) {
                            $new_about = trim($_POST['about_text']);
                            if ($new_about === '') {
                                $msg = array('type' => 'danger', 'text' => 'About text cannot be empty.');
                            } else {
                                // Upsert into website_settings
                                $check = $con->prepare("SELECT option_id FROM website_settings WHERE option_name = ?");
                                $check->execute(array('about_us'));
                                if ($check->rowCount() > 0) {
                                    $up = $con->prepare("UPDATE website_settings SET option_value = ? WHERE option_name = ?");
                                    $up->execute(array($new_about, 'about_us'));
                                } else {
                                    $ins = $con->prepare("INSERT INTO website_settings (option_name, option_value) VALUES (?, ?)");
                                    $ins->execute(array('about_us', $new_about));
                                }
                                $about_text = $new_about;
                                $msg = array('type' => 'success', 'text' => 'About text updated successfully.');
                            }
                        }

                        if (isset($msg)) {
                            echo '<div class="alert alert-' . ($msg['type'] === 'success' ? 'success' : 'danger') . '">' . htmlspecialchars($msg['text']) . '</div>';
                        }

                        echo '<form method="post" style="margin-top:20px">';
                        echo '<label for="about_text"><strong>Edit About Text</strong></label>';
                        echo '<textarea id="about_text" name="about_text" rows="6" class="form-control" style="margin-top:8px">' . htmlspecialchars($about_text) . '</textarea>';
                        echo '<div style="margin-top:10px"><button type="submit" name="about_save" class="bttn_style_2">Save</button></div>';
                        echo '</form>';
                    }
                ?>
            </div>
            <div class="col-md-6" style="display:flex;align-items:center;justify-content:center;">
                <img src="Design/images/pizza_image.png" alt="Delicious Dish" style="width:100%;max-width:520px;border-radius:12px;box-shadow:0 12px 36px rgba(0,0,0,0.4);">
            </div>
        </div>

        <div class="row" style="margin-top:40px;">
            <div class="col-md-12">
                <h3>Our Story</h3>
                <p>We began as a small neighborhood kitchen and grew through passion for food and community. Our chefs craft every dish with care — from sourcing ingredients to the final presentation.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'Includes/templates/footer.php'; ?>
