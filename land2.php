<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St Cyprian High School - Admissions 2026</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>
        /* Popup Styles */
        .admissions-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999999;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }
        
        .admissions-popup {
            background: white;
            border-radius: 20px;
            width: 95%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: slideUp 0.5s ease;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
        
        .popup-header {
            background: linear-gradient(135deg, #2C5AA0, #1a3a7a);
            color: white;
            padding: 30px;
            border-radius: 20px 20px 0 0;
            text-align: center;
            position: relative;
        }
        
        .popup-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            color: white;
            font-size: 20px;
        }
        
        .popup-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        .popup-badge {
            display: inline-block;
            background: #FF6B35;
            color: white;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 14px;
            animation: pulse 2s infinite;
        }
        
        .popup-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .popup-header p {
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .popup-content {
            padding: 30px;
        }
        
        .popup-feature {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }
        
        .popup-feature:hover {
            transform: translateX(5px);
            background: #e9ecef;
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #2C5AA0, #4A7BC8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .feature-text h4 {
            color: #2C5AA0;
            margin-bottom: 5px;
            font-size: 1rem;
        }
        
        .feature-text p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .deadline-counter {
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin: 25px 0;
        }
        
        .deadline-counter h4 {
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .counter-numbers {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .counter-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 10px;
            min-width: 60px;
        }
        
        .counter-number {
            font-size: 1.8rem;
            font-weight: bold;
            display: block;
            font-family: 'Montserrat', sans-serif;
        }
        
        .counter-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }
        
        .popup-form {
            margin-top: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2C5AA0;
            font-size: 0.9rem;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4ECDC4;
        }
        
        .popup-submit-btn {
            background: linear-gradient(135deg, #2C5AA0, #1a3a7a);
            color: white;
            border: none;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .popup-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(44, 90, 160, 0.3);
        }
        
        .privacy-note {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 0.8rem;
        }
        
        .privacy-note i {
            color: #4ECDC4;
            margin-right: 5px;
        }
        
        /* Trigger Button Styles */
        .admissions-trigger {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99999;
            animation: bounce 2s infinite;
        }
        
        .trigger-btn {
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            color: white;
            border: none;
            padding: 18px 25px;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4);
            transition: all 0.3s ease;
        }
        
        .trigger-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.5);
        }
        
        .trigger-icon {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FF6B35;
            font-size: 20px;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admissions-popup {
                max-width: 95%;
            }
            
            .popup-header {
                padding: 25px 20px;
            }
            
            .popup-header h2 {
                font-size: 1.5rem;
            }
            
            .popup-content {
                padding: 25px 20px;
            }
            
            .admissions-trigger {
                bottom: 20px;
                right: 20px;
            }
            
            .trigger-btn {
                padding: 15px 20px;
                font-size: 14px;
            }
            
            .trigger-text {
                display: none;
            }
            
            .trigger-btn:hover .trigger-text {
                display: block;
                position: absolute;
                right: 70px;
                background: #FF6B35;
                padding: 8px 15px;
                border-radius: 8px;
                white-space: nowrap;
            }
        }
        
        /* Success Message */
        .success-message {
            display: none;
            text-align: center;
            padding: 40px 30px;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            margin: 0 auto 25px;
        }
        
        .success-message h3 {
            color: #2C5AA0;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .success-message p {
            color: #666;
            margin-bottom: 25px;
        }
        
        .continue-btn {
            background: #2C5AA0;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .continue-btn:hover {
            background: #1a3a7a;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Trigger Button (Place on homepage) -->
    <div class="admissions-trigger">
        <button class="trigger-btn" onclick="showAdmissionsPopup()">
            <div class="trigger-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span class="trigger-text">Admissions 2026</span>
        </button>
    </div>

    <!-- Popup Overlay -->
    <div class="admissions-popup-overlay" id="admissionsPopup">
        <div class="admissions-popup">
            <!-- Header -->
            <div class="popup-header">
                <button class="popup-close" onclick="hideAdmissionsPopup()">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="popup-badge">
                    <i class="fas fa-calendar-alt"></i> Term One 2026
                </div>
                
                <h2>Senior One Admissions Now Open!</h2>
                <p>Limited spaces available for the 2026 academic year</p>
            </div>

            <!-- Content -->
            <div class="popup-content" id="popupContent">
                <!-- Features -->
                <div class="popup-feature">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Expert Faculty</h4>
                        <p>Qualified teachers dedicated to student success</p>
                    </div>
                </div>
                
                <div class="popup-feature">
                    <div class="feature-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Modern Facilities</h4>
                        <p>State-of-the-art labs, library & sports facilities</p>
                    </div>
                </div>
                
                <div class="popup-feature">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Holistic Education</h4>
                        <p>Academic, spiritual & extracurricular development</p>
                    </div>
                </div>

                <!-- Deadline Counter -->
                <div class="deadline-counter">
                    <h4>Applications Close In:</h4>
                    <div class="counter-numbers">
                        <div class="counter-item">
                            <span class="counter-number" id="popupDays">00</span>
                            <span class="counter-label">Days</span>
                        </div>
                        <div class="counter-item">
                            <span class="counter-number" id="popupHours">00</span>
                            <span class="counter-label">Hours</span>
                        </div>
                        <div class="counter-item">
                            <span class="counter-number" id="popupMinutes">00</span>
                            <span class="counter-label">Mins</span>
                        </div>
                    </div>
                    <p style="font-size: 0.9rem; opacity: 0.9;">
                        <i class="fas fa-exclamation-triangle"></i> Limited spaces remaining
                    </p>
                </div>

                <!-- Quick Application Form -->
                <div class="popup-form">
                    <h3 style="color: #2C5AA0; margin-bottom: 20px; font-size: 1.2rem;">
                        <i class="fas fa-file-alt"></i> Express Interest
                    </h3>
                    
                    <form id="quickApplicationForm">
                        <div class="form-group">
                            <label for="popupParentName"><i class="fas fa-user"></i> Parent Name *</label>
                            <input type="text" id="popupParentName" name="parent_name" required placeholder="Enter full name">
                        </div>
                        
                        <div class="form-group">
                            <label for="popupPhone"><i class="fas fa-phone"></i> Phone Number *</label>
                            <input type="tel" id="popupPhone" name="phone" required placeholder="07XXXXXXXX">
                        </div>
                        
                        <div class="form-group">
                            <label for="popupEmail"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" id="popupEmail" name="email" placeholder="parent@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="popupStudentName"><i class="fas fa-user-graduate"></i> Student Name</label>
                            <input type="text" id="popupStudentName" name="student_name" placeholder="Child's full name">
                        </div>
                        
                        <button type="submit" class="popup-submit-btn">
                            <i class="fas fa-paper-plane"></i> Submit Expression of Interest
                        </button>
                        
                        <p class="privacy-note">
                            <i class="fas fa-lock"></i> Your information is secure. We'll contact you within 24 hours.
                        </p>
                    </form>
                </div>
                
                <!-- Quick Contact -->
                <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">
                        <i class="fas fa-info-circle"></i> Need immediate assistance?
                    </p>
                    <a href="tel:+256392123456" style="display: inline-flex; align-items: center; gap: 8px; background: #f8f9fa; padding: 10px 20px; border-radius: 8px; text-decoration: none; color: #2C5AA0; font-weight: 600;">
                        <i class="fas fa-phone"></i> Call: +256 392 123 456
                    </a>
                </div>
            </div>

            <!-- Success Message (Hidden by default) -->
            <div class="success-message" id="successMessage">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h3>Thank You!</h3>
                <p>Your expression of interest has been submitted successfully. Our admissions team will contact you within 24 hours.</p>
                <p style="font-weight: 600; color: #2C5AA0;">
                    <i class="fas fa-envelope"></i> Check your email for confirmation
                </p>
                <button class="continue-btn" onclick="hideAdmissionsPopup()">
                    Continue Browsing
                </button>
            </div>
        </div>
    </div>

    <script>
        // Popup Functions
        function showAdmissionsPopup() {
            const popup = document.getElementById('admissionsPopup');
            popup.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
        
        function hideAdmissionsPopup() {
            const popup = document.getElementById('admissionsPopup');
            popup.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
        }
        
        // Close popup when clicking outside
        document.getElementById('admissionsPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                hideAdmissionsPopup();
            }
        });
        
        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideAdmissionsPopup();
            }
        });
        
        // Countdown Timer for Popup
        function updatePopupCountdown() {
            const deadline = new Date('February 10, 2026 23:59:59').getTime();
            const now = new Date().getTime();
            const timeLeft = deadline - now;
            
            if (timeLeft < 0) {
                document.querySelector('.counter-numbers').innerHTML = '<h4>Applications Closed</h4>';
                return;
            }
            
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            
            document.getElementById('popupDays').textContent = days.toString().padStart(2, '0');
            document.getElementById('popupHours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('popupMinutes').textContent = minutes.toString().padStart(2, '0');
        }
        
        // Form Submission
        document.getElementById('quickApplicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Show loading state
            const submitBtn = this.querySelector('.popup-submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            // Simulate API call (replace with actual submission)
            setTimeout(() => {
                // Show success message
                document.getElementById('popupContent').style.display = 'none';
                document.getElementById('successMessage').style.display = 'block';
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Reset form
                this.reset();
                
                // Send data to your WordPress backend (example)
                sendToWordPress(data);
                
                // Auto-close after 5 seconds
                setTimeout(() => {
                    hideAdmissionsPopup();
                }, 5000);
            }, 1500);
        });
        
        // Function to send data to WordPress
        function sendToWordPress(data) {
            // This is where you'd connect to your WordPress backend
            // Example using fetch:
            /*
            fetch('/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'save_admissions_interest',
                    'parent_name': data.parent_name,
                    'phone': data.phone,
                    'email': data.email,
                    'student_name': data.student_name
                })
            })
            .then(response => response.json())
            .then(result => {
                console.log('Success:', result);
            })
            .catch(error => {
                console.error('Error:', error);
            });
            */
            
            // For now, just log the data
            console.log('Form data to send to WordPress:', data);
        }
        
        // Auto-show popup after 5 seconds (optional)
        window.addEventListener('load', function() {
            // Check if already shown today
            const lastShown = localStorage.getItem('popupLastShown');
            const today = new Date().toDateString();
            
            if (lastShown !== today) {
                setTimeout(() => {
                    showAdmissionsPopup();
                    localStorage.setItem('popupLastShown', today);
                }, 5000); // Show after 5 seconds
            }
        });
        
        // Update countdown every minute
        setInterval(updatePopupCountdown, 60000);
        updatePopupCountdown();
        
        // Trigger button visibility on scroll
        let lastScrollTop = 0;
        const trigger = document.querySelector('.admissions-trigger');
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop) {
                // Scrolling down
                trigger.style.opacity = '0.7';
                trigger.style.transform = 'translateY(10px)';
            } else {
                // Scrolling up
                trigger.style.opacity = '1';
                trigger.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });
    </script>
</body>
</html>