<?php
/**
 * @Packge     : Dilabs
 * @Version    : 1.0
 * @Author     : Dilabs
 * @Author URI : https://themeforest.net/user/validthemes/portfolio
 *
 */

// Block direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Include File
 *
 */

// Constants
require_once get_parent_theme_file_path() . '/inc/dilabs-constants.php';

//theme setup
require_once DILABS_DIR_PATH_INC . 'theme-setup.php';

//essential scripts
require_once DILABS_DIR_PATH_INC . 'essential-scripts.php';

//NavWalker
require_once DILABS_DIR_PATH_INC . 'dilabs-navwalker.php';

// plugin activation
require_once DILABS_DIR_PATH_FRAM . 'plugins-activation/dilabs-active-plugins.php';

// meta options
require_once DILABS_DIR_PATH_FRAM . 'dilabs-meta/dilabs-config.php';

// page breadcrumbs
require_once DILABS_DIR_PATH_INC . 'dilabs-breadcrumbs.php';

// sidebar register
require_once DILABS_DIR_PATH_INC . 'dilabs-widgets-reg.php';

//essential functions
require_once DILABS_DIR_PATH_INC . 'dilabs-functions.php';

// theme dynamic css
require_once DILABS_DIR_PATH_INC . 'dilabs-commoncss.php';

// helper function
require_once DILABS_DIR_PATH_INC . 'wp-html-helper.php';

// Demo Data
require_once DILABS_DEMO_DIR_PATH . 'demo-import.php';

// dilabs options
require_once DILABS_DIR_PATH_FRAM . 'dilabs-options/dilabs-options.php';

// hooks
require_once DILABS_DIR_PATH_HOOKS . 'hooks.php';

// hooks funtion
require_once DILABS_DIR_PATH_HOOKS . 'hooks-functions.php';

require_once DILABS_DIR_PATH_INC . '/woocommerce-hooks/woocommerce-hooks.php';

// woocommerce hooks
require_once DILABS_DIR_PATH_INC . '/woocommerce-hooks/woocommerce-hooks-functions.php';

// ===============================
// 1. SMTP Configuration (FIXED)
// ===============================
function custom_smtp_settings($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'mail.motherkevincollege.com'; 
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 465; // Changed from 587 to 465 (SSL)
    $phpmailer->Username = 'info@motherkevincollege.com';
    $phpmailer->Password = 'Mother@Kevin';
    $phpmailer->SMTPSecure = 'ssl';
    
    // Set From to match authentication
    $phpmailer->From = 'info@motherkevincollege.com';
    $phpmailer->FromName = 'Mother Kevin College';
    
    // Enable debugging if needed
    // $phpmailer->SMTPDebug = 2; // 0 = off, 1 = errors, 2 = messages
    // $phpmailer->Debugoutput = 'error_log';
}
add_action('phpmailer_init', 'custom_smtp_settings');

// ===============================
// 2. Set Default Email Headers
// ===============================
add_filter('wp_mail_from', function($email) {
    return 'info@motherkevincollege.com';
});

add_filter('wp_mail_from_name', function($name) {
    return 'Mother Kevin College';
});

add_filter('wp_mail_content_type', function($content_type) {
    return 'text/html';
});


function log_wp_mail_errors($wp_error) {
    $error_message = $wp_error->get_error_message();
    
    // Log to PHP error log
    error_log('WordPress Email Failed: ' . $error_message);
    
    // Log to file
    $log_file = WP_CONTENT_DIR . '/email-errors.log';
    $log_entry = date('Y-m-d H:i:s') . ' - ' . $error_message . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    // Optionally send error notification (but careful not to create infinite loop)
    if (!defined('SENDING_ERROR_EMAIL')) {
        define('SENDING_ERROR_EMAIL', true);
        $error_subject = 'Email Failed on ' . site_url();
        $error_body = "Email error occurred:\n\n" . $error_message . "\n\nTime: " . date('Y-m-d H:i:s');
        
        // Use wp_mail without SMTP to avoid recursion
        remove_action('phpmailer_init', 'custom_smtp_settings');
        wp_mail('obacheisaac@gmail.com', $error_subject, $error_body);
        add_action('phpmailer_init', 'custom_smtp_settings');
    }
}
add_action('wp_mail_failed', 'log_wp_mail_errors');

// ===============================
// 4. Form Submission Handler
// ===============================
function handle_senior_five_form_submission() {
    // Only run on POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }

    // Nonce security check
    if (!isset($_POST['senior_five_form_nonce']) || 
        !wp_verify_nonce($_POST['senior_five_form_nonce'], 'senior_five_form_action')) {
        return false;
    }

    // Required fields check
    $required_fields = ['first_name', 'guardian_phone', 'guardian_name', 'admission_class'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            error_log('Missing required field: ' . $field);
            return false;
        }
    }

    // Sanitize fields
    $first_name  = sanitize_text_field($_POST['first_name']);
    $last_name   = sanitize_text_field($_POST['last_name']);
    $gender      = sanitize_text_field($_POST['gender']);
    $dob         = sanitize_text_field($_POST['dob']);
    $religion    = sanitize_text_field($_POST['religion']);
    $nationality = sanitize_text_field($_POST['nationality']);
    $class       = sanitize_text_field($_POST['admission_class']);
    $school      = sanitize_text_field($_POST['former_school']);
    $uce_result  = sanitize_text_field($_POST['uce_result']);
    $guardian    = sanitize_text_field($_POST['guardian_name']);
    $occupation  = sanitize_text_field($_POST['guardian_occupation']);
    $phone       = sanitize_text_field($_POST['guardian_phone']);
    $address     = sanitize_text_field($_POST['home_address']);
    $nok_name    = sanitize_text_field($_POST['nok_name']);
    $nok_phone   = sanitize_text_field($_POST['nok_phone']);
    $nok_address = sanitize_text_field($_POST['nok_address']);

    // Subjects & Grades
    $subject_list = "";
    if (!empty($_POST['subjects']) && is_array($_POST['subjects'])) {
        $subjects = $_POST['subjects'];
        $grades   = isset($_POST['grades']) ? $_POST['grades'] : [];
        
        for ($i = 0; $i < count($subjects); $i++) {
            $subject = sanitize_text_field($subjects[$i]);
            $grade = isset($grades[$i]) ? sanitize_text_field($grades[$i]) : '';
            $subject_list .= $subject . " - Grade: " . $grade . "<br>";
        }
    }

    // Email setup
    $to = 'info@motherkevincollege.com'; 
    $cc = 'obacheisaac@gmail.com'; 
    $subject = "New Student Application - $first_name $last_name ($class)";

    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .header { background: #4CAF50; color: white; padding: 20px; }
            .section { margin: 20px 0; padding: 15px; border-left: 4px solid #4CAF50; background: #f9f9f9; }
            h2 { color: #333; }
            h3 { color: #4CAF50; }
            .important { color: #d32f2f; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>🎓 New Student Application</h1>
            <p><strong>Submission Time:</strong> " . date('F j, Y, g:i a') . "</p>
        </div>
        
        <div class='section'>
            <h2>👤 Student Information</h2>
            <p><strong>Name:</strong> $first_name $last_name</p>
            <p><strong>Gender:</strong> $gender</p>
            <p><strong>Date of Birth:</strong> $dob</p>
            <p><strong>Religion:</strong> $religion</p>
            <p><strong>Nationality:</strong> $nationality</p>
        </div>
        
        <div class='section'>
            <h2>📚 Admission Details</h2>
            <p><strong>Class Applied For:</strong> <span class='important'>$class</span></p>
            <p><strong>Former School:</strong> $school</p>
            <p><strong>UCE Results:</strong> $uce_result</p>
        </div>
        
        <div class='section'>
            <h2>📊 Subject Performance</h2>
            $subject_list
        </div>
        
        <div class='section'>
            <h2>👨‍👩‍👧‍👦 Parent / Guardian Information</h2>
            <p><strong>Name:</strong> $guardian</p>
            <p><strong>Occupation:</strong> $occupation</p>
            <p><strong>Phone:</strong> <a href='tel:$phone'>$phone</a></p>
            <p><strong>Address:</strong> $address</p>
        </div>
        
        <div class='section'>
            <h2>📞 Next of Kin</h2>
            <p><strong>Name:</strong> $nok_name</p>
            <p><strong>Phone:</strong> <a href='tel:$nok_phone'>$nok_phone</a></p>
            <p><strong>Address:</strong> $nok_address</p>
        </div>
        
        <hr>
        <p><em>This application was submitted via the website form at " . site_url() . "</em></p>
    </body>
    </html>
    ";

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Mother Kevin College <info@motherkevincollege.com>',
        'Reply-To: ' . $first_name . ' ' . $last_name . ' <noreply@motherkevincollege.com>',
        'Cc: Admin <obacheisaac@gmail.com>'
    );

    // Send email
    $sent = wp_mail($to, $subject, $message, $headers);
    
    // If sending to admissions email fails, try sending to admin only
    if (!$sent) {
        error_log('Primary email failed, trying admin only');
        $sent = wp_mail('obacheisaac@gmail.com', $subject . ' [FALLBACK]', $message, $headers);
    }
    
    return $sent;
}


function render_senior_five_application_form() {
    ob_start();
    
    // Show submission status
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senior_five_form_nonce'])) {
        if (handle_senior_five_form_submission()) {
            echo '<div class="alert success" style="background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 20px 0;">
                    <h3 style="margin-top: 0; color: #155724;">✅ Application Submitted Successfully!</h3>
                    <p>Thank you for your application. We have received it and will contact you soon.</p>
                    <p><strong>Application sent to:</strong> info@motherkevincollege.com </p>
                  </div>';
        } else {
            echo '<div class="alert error" style="background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px 0;">
                    <h3 style="margin-top: 0; color: #721c24;">❌ Error Sending Application</h3>
                    <p>Please try again or contact the school directly at <strong>st.cyprianhighschool@yahoo.com</strong></p>
                  </div>';
        }
    }
?>
<!-- Add some CSS -->
<style>
    #senior-five-form label {
        display: block;
        margin: 10px 0 5px;
        font-weight: bold;
    }
    #senior-five-form input[type="text"],
    #senior-five-form input[type="tel"],
    #senior-five-form input[type="date"],
    #senior-five-form select {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .subject-entry {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .subject-entry input {
        flex: 1;
    }
    button[type="submit"] {
        background: #4CAF50;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }
    button[type="submit"]:hover {
        background: #45a049;
    }
    .add-subject-btn {
        background: #2196F3;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-bottom: 20px;
    }
</style>
<div style="margin:auto;width:60%;">
<form method="POST" id="senior-five-form">
    <?php wp_nonce_field('senior_five_form_action', 'senior_five_form_nonce'); ?>
    <p>
	  <b>Welcome to our Application Portal. Please fill out the form below to apply for admission. Provide accurate student information and ensure all required fields are completed before submitting the form</b>
    </p>
    <label>First Name *</label>
    <input type="text" name="first_name" required>
    
    <label>Last Name *</label>
    <input type="text" name="last_name" required>
    
    <label>Gender *</label>
    <select name="gender" required>
        <option value="">Select</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>
    
    <label>Date of Birth *</label>
    <input type="date" name="dob" required>
    
    <label>Religion *</label>
    <select name="religion" required>
        <option value="">Select</option>
        <option value="Catholic">Catholic</option>
        <option value="Anglican">Anglican</option>
        <option value="Muslim">Muslim</option>
        <option value="Born Again">Born Again</option>
        <option value="Other">Other</option>
    </select>
    
    <label>Nationality *</label>
    <input type="text" name="nationality" required>
    
    <h2>Admission Details</h2>
    
    <label>Class to be Admitted To *</label>
    <select name="admission_class" required>
        <option value="">Select Class</option>
        <option value="S1">Senior One</option>
        <option value="S5">Senior Five</option>
    </select>
    
    <h5>Performance Results</h5>
    
    <label>Name of Former School *</label>
    <input type="text" name="former_school" required>
    
    <label>UNEB Result Finals (e.g., 18 Aggregates) *</label>
    <input type="text" name="uce_result" required>
    
    <div id="subjects-container">
        <label>Subjects and Grades</label>
        <div class="subject-entry">
            <input type="text" name="subjects[]" placeholder="Subject (e.g., English)" required>
            <input type="text" name="grades[]" placeholder="Grade (e.g., 1)" required>
        </div>
    </div>
    <button type="button" class="add-subject-btn" onclick="addSubject()">➕ Add Another Subject</button>
    
    <h5>Parent/Guardian Information</h5>
    
    <label>Parent/Guardian Name *</label>
    <input type="text" name="guardian_name" required>
    
    <label>Occupation *</label>
    <input type="text" name="guardian_occupation" required>
    
    <label>Phone Number *</label>
    <input type="tel" name="guardian_phone" required pattern="[0-9]{10,15}">
    
    <label>Home Address</label>
    <input type="text" name="home_address">
    
    <h5>Next of Kin</h5>
    
    <label>Name</label>
    <input type="text" name="nok_name">
    
    <label>Phone</label>
    <input type="tel" name="nok_phone" pattern="[0-9]{10,15}">
    
    <label>Address</label>
    <input type="text" name="nok_address">
    <br><br>
    <button type="submit">Submit Application</button>
</form>
</div>
<script>
function addSubject() {
    const container = document.getElementById('subjects-container');
    const div = document.createElement('div');
    div.className = 'subject-entry';
    div.innerHTML = `
        <input type="text" name="subjects[]" placeholder="Subject (e.g., Mathematics)" required>
        <input type="text" name="grades[]" placeholder="Grade (e.g., 2)" required>
    `;
    container.appendChild(div);
}
</script>
<?php
    return ob_get_clean();
}
add_shortcode('senior_five_form', 'render_senior_five_application_form');

/* Vado Social Share Widget with inline SVG icons */
class Vado_Social_Share_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'vado_social_share_widget',
            __('Vado Social Share Buttons', 'textdomain'),
            array('description' => __('Displays Facebook, X and WhatsApp share icons (SVG)', 'textdomain'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        // Title
        echo '<b class="widget-title">Share on Social Media</b>';

        // Prepare share data
        $post_title = get_the_title();
        $post_url   = get_permalink();

        // Use rawurlencode for query values
        $encoded_title = rawurlencode( $post_title );
        $encoded_url   = rawurlencode( $post_url );

        // Build share URLs
        $facebook_href = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
        $x_href        = 'https://twitter.com/intent/tweet?text=' . $encoded_title . '&url=' . $encoded_url;
        $wa_href       = 'https://api.whatsapp.com/send?text=' . $encoded_title . '%20' . $encoded_url;

        ?>
        <div class="vado-social-share">
            <a class="vado-share-link vado-fb" href="<?php echo esc_url( $facebook_href ); ?>" target="_blank" rel="noopener" aria-label="Share on Facebook" title="Share on Facebook">
                <!-- Facebook SVG -->
                <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2.2V12h2.2V9.6c0-2.1 1.2-3.2 3-3.2.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 2.9h-1.9v7A10 10 0 0 0 22 12z" fill="currentColor"/>
                </svg>
            </a>

            <a class="vado-share-link vado-x" href="<?php echo esc_url( $x_href ); ?>" target="_blank" rel="noopener" aria-label="Share on X (Twitter)" title="Share on X">
                <!-- X / Twitter SVG -->
                <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.2 4.2 0 0 0 1.84-2.33 8.5 8.5 0 0 1-2.68 1.03 4.24 4.24 0 0 0-7.22 3.86A12.02 12.02 0 0 1 3.15 4.6a4.24 4.24 0 0 0 1.31 5.66c-.66-.02-1.28-.2-1.82-.5v.05a4.24 4.24 0 0 0 3.4 4.16c-.34.09-.69.13-1.05.05.3.93 1.17 1.61 2.2 1.63A8.51 8.51 0 0 1 2 19.54 12.03 12.03 0 0 0 8.29 21c7.55 0 11.68-6.26 11.68-11.69v-.53A8.18 8.18 0 0 0 22.46 6z" fill="currentColor"/>
                </svg>
            </a>

            <a class="vado-share-link vado-wa" href="<?php echo esc_url( $wa_href ); ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp" title="Share on WhatsApp">
                <!-- WhatsApp SVG -->
                <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M20.5 3.5A11 11 0 0 0 3.5 20.5l-1.1 4 4-1.1A11 11 0 1 0 20.5 3.5zM12 20a8 8 0 1 1 5.66-13.66A8 8 0 0 1 12 20zM17 14.1c-.2-.1-1.2-.6-1.4-.7-.2-.1-.3-.1-.4.1-.2.2-.8.7-1 .8-.2.1-.3.1-.5-.1-.2-.2-.9-.4-1.7-1.1-.6-.5-1-1.1-1.1-1.3-.1-.2 0-.3.1-.4.1-.1.2-.3.3-.4.1-.1.1-.3 0-.5-.1-.2-.5-1.2-.7-1.6-.2-.4-.4-.3-.6-.3-.2 0-.4 0-.6 0-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1 2.9 1.1 3.1c.1.2 1.9 3 4.6 4.2 3 .9 3 .6 3.5 0 .5-.6 1-1.9 1.1-2.1.1-.2.2-.4.1-.6-.1-.2-1-.6-1.2-.7z" fill="currentColor"/>
                </svg>
            </a>
        </div>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        echo '<p style="font-size:13px">No settings required — this widget auto uses the current post/page.</p>';
    }

    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
}

/* Register the widget */
function vado_register_social_share_widget() {
    register_widget('Vado_Social_Share_Widget');
}
add_action('widgets_init', 'vado_register_social_share_widget');

