<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior One Admissions 2026 | St Cyprian High School Kyabakadde</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2C5AA0;
            --secondary: #FF6B35;
            --accent: #4ECDC4;
            --light: #F8F9FA;
            --dark: #212529;
            --success: #28a745;
            --warning: #ffc107;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background: white;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-img {
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .logo-text h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .logo-text span {
            color: var(--secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .cta-button {
            background: linear-gradient(135deg, var(--secondary), #FF8C42);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(44, 90, 160, 0.9), rgba(44, 90, 160, 0.8)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
            border-radius: 0 0 40px 40px;
        }
        
        .hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }
        
        .hero-badge {
            display: inline-block;
            background: var(--secondary);
            color: white;
            padding: 8px 25px;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Features Section */
        .features {
            padding: 80px 0;
            background: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background: var(--light);
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            transition: transform 0.3s ease;
            border: 2px solid transparent;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--accent);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), #4A7BC8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 32px;
        }
        
        .feature-card h3 {
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        /* Application Process */
        .process {
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .process-steps {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 50px;
            position: relative;
        }
        
        .process-steps::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255,255,255,0.3);
            z-index: 1;
        }
        
        .step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .step-number {
            width: 80px;
            height: 80px;
            background: var(--secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            margin: 0 auto 20px;
            border: 5px solid white;
        }
        
        /* Requirements */
        .requirements {
            padding: 80px 0;
            background: white;
        }
        
        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }
        
        .requirement-item {
            background: var(--light);
            padding: 25px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .req-icon {
            width: 60px;
            height: 60px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }
        
        /* Deadline Counter */
        .deadline {
            padding: 60px 0;
            background: linear-gradient(135deg, var(--primary), #1a3a7a);
            color: white;
            text-align: center;
        }
        
        .countdown {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        .countdown-item {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 15px;
            min-width: 100px;
            backdrop-filter: blur(10px);
        }
        
        .countdown-number {
            font-size: 2.5rem;
            font-weight: bold;
            display: block;
        }
        
        /* Application Form */
        .application-form {
            padding: 80px 0;
            background: var(--light);
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary);
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--secondary), #FF8C42);
            color: white;
            border: none;
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin: 40px auto 0;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.4);
        }
        
        /* FAQ Section */
        .faq {
            padding: 80px 0;
            background: white;
        }
        
        .faq-item {
            margin-bottom: 20px;
            border: 2px solid #e1e5eb;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .faq-question {
            padding: 25px;
            background: var(--light);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--primary);
        }
        
        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .faq-item.active .faq-answer {
            padding: 25px;
            max-height: 500px;
        }
        
        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-logo h3 {
            font-family: 'Montserrat', sans-serif;
            color: white;
            margin-bottom: 15px;
        }
        
        .contact-info p {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--secondary);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .process-steps {
                flex-direction: column;
                gap: 40px;
            }
            
            .process-steps::before {
                display: none;
            }
            
            .form-container {
                padding: 30px 20px;
            }
            
            .countdown {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <div class="logo-img">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="logo-text">
                    <h1>St Cyprian High School</h1>
                    <span>Kyabakadde - Excellence in Education</span>
                </div>
            </div>
            <a href="#apply-now" class="cta-button">
                <i class="fas fa-file-alt"></i> Apply Now
            </a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-badge">
                <i class="fas fa-calendar-alt"></i> Term One 2026 Admissions
            </div>
            <h1>Begin Your Journey to Excellence</h1>
            <p>Join St Cyprian High School Kyabakadde for Senior One 2026. Experience quality education, holistic development, and a foundation for lifelong success.</p>
            <a href="#apply-now" class="cta-button" style="background: white; color: var(--primary);">
                Start Your Application <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose St Cyprian?</h2>
                <p>We provide an educational experience that nurtures both academic excellence and personal growth</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Expert Faculty</h3>
                    <p>Qualified and experienced teachers dedicated to student success</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h3>Modern Facilities</h3>
                    <p>State-of-the-art laboratories, library, and sports facilities</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Holistic Development</h3>
                    <p>Focus on academic, spiritual, and extracurricular growth</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Application Process -->
    <section class="process">
        <div class="container">
            <div class="section-title">
                <h2>Simple Application Process</h2>
                <p>Follow these easy steps to secure your place</p>
            </div>
            <div class="process-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Complete Form</h3>
                    <p>Fill the online application form with required details</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Submit Documents</h3>
                    <p>Upload required academic records and certificates</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Interview</h3>
                    <p>Participate in student and parent interview session</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Admission</h3>
                    <p>Receive admission letter and begin your journey</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Requirements -->
    <section class="requirements">
        <div class="container">
            <div class="section-title">
                <h2>Admission Requirements</h2>
                <p>Ensure you meet the following criteria for Senior One admission</p>
            </div>
            <div class="requirements-grid">
                <div class="requirement-item">
                    <div class="req-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <h4>PLE Certificate</h4>
                        <p>Original Primary Leaving Examination results slip</p>
                    </div>
                </div>
                <div class="requirement-item">
                    <div class="req-icon">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <div>
                        <h4>Age Requirement</h4>
                        <p>Must be between 12-15 years by February 2026</p>
                    </div>
                </div>
                <div class="requirement-item">
                    <div class="req-icon">
                        <i class="fas fa-passport"></i>
                    </div>
                    <div>
                        <h4>Passport Photos</h4>
                        <p>4 recent passport-size photographs</p>
                    </div>
                </div>
                <div class="requirement-item">
                    <div class="req-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <h4>Medical Report</h4>
                        <p>Recent medical examination certificate</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Deadline Counter -->
    <section class="deadline">
        <div class="container">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Application Deadline</h2>
            <p style="font-size: 1.2rem; margin-bottom: 30px;">Applications close in:</p>
            <div class="countdown" id="countdown">
                <div class="countdown-item">
                    <span class="countdown-number" id="days">00</span>
                    <span>Days</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="hours">00</span>
                    <span>Hours</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="minutes">00</span>
                    <span>Minutes</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="seconds">00</span>
                    <span>Seconds</span>
                </div>
            </div>
            <p style="margin-top: 30px; font-size: 1.1rem;">
                <i class="fas fa-exclamation-triangle"></i> Late applications may not be considered
            </p>
        </div>
    </section>

    <!-- Application Form -->
    <section class="application-form" id="apply-now">
        <div class="container">
            <div class="section-title">
                <h2>Online Application Form</h2>
                <p>Fill all fields accurately. Fields marked with * are required.</p>
            </div>
            <div class="form-container">
                <form id="admissionForm">
                    <!-- Student Information -->
                    <h3 style="color: var(--primary); margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid var(--light);">
                        <i class="fas fa-user-graduate"></i> Student Information
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div class="form-group">
                            <label for="firstName"><i class="fas fa-user"></i> First Name *</label>
                            <input type="text" id="firstName" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName"><i class="fas fa-user"></i> Last Name *</label>
                            <input type="text" id="lastName" name="last_name" required>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div class="form-group">
                            <label for="gender"><i class="fas fa-venus-mars"></i> Gender *</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dob"><i class="fas fa-birthday-cake"></i> Date of Birth *</label>
                            <input type="date" id="dob" name="dob" required>
                        </div>
                    </div>
                    
                    <!-- Academic Information -->
                    <h3 style="color: var(--primary); margin: 40px 0 30px; padding-bottom: 15px; border-bottom: 2px solid var(--light);">
                        <i class="fas fa-graduation-cap"></i> Academic Information
                    </h3>
                    
                    <div class="form-group">
                        <label for="formerSchool"><i class="fas fa-school"></i> Former School *</label>
                        <input type="text" id="formerSchool" name="former_school" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pleResults"><i class="fas fa-chart-bar"></i> PLE Results *</label>
                        <input type="text" id="pleResults" name="ple_results" placeholder="e.g., Aggregate 10, Division 1" required>
                    </div>
                    
                    <!-- Parent/Guardian Information -->
                    <h3 style="color: var(--primary); margin: 40px 0 30px; padding-bottom: 15px; border-bottom: 2px solid var(--light);">
                        <i class="fas fa-users"></i> Parent/Guardian Information
                    </h3>
                    
                    <div class="form-group">
                        <label for="guardianName"><i class="fas fa-user-friends"></i> Parent/Guardian Name *</label>
                        <input type="text" id="guardianName" name="guardian_name" required>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div class="form-group">
                            <label for="guardianPhone"><i class="fas fa-phone"></i> Phone Number *</label>
                            <input type="tel" id="guardianPhone" name="guardian_phone" required>
                        </div>
                        <div class="form-group">
                            <label for="guardianEmail"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" id="guardianEmail" name="guardian_email">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                    
                    <p style="text-align: center; margin-top: 20px; color: #666;">
                        <i class="fas fa-lock"></i> Your information is secure and confidential
                    </p>
                </form>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about admissions</p>
            </div>
            
            <div class="faq-item active">
                <div class="faq-question">
                    <span>What are the school fees for Senior One?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>The complete fee structure will be provided upon admission. It includes tuition, boarding (if applicable), activity fees, and development levy. Payment plans are available.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>When does Term One 2026 begin?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Term One 2026 begins on February 3rd, 2026. Orientation for new students will be held on February 1st-2nd, 2026.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Is boarding accommodation available?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>Yes, we offer both day and boarding options. Our boarding facilities are secure, comfortable, and supervised by experienced house parents.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>What subjects are offered in Senior One?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>We offer a comprehensive curriculum including Mathematics, English, Science, Social Studies, Christian Religious Education, ICT, and local languages.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>St Cyprian High School</h3>
                    <p>Kyabakadde</p>
                    <p>Excellence in Education Since 1985</p>
                </div>
                
                <div class="contact-info">
                    <h4>Contact Information</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Kyabakadde, Luweero District, Uganda</p>
                    <p><i class="fas fa-phone"></i> +256 392 123 456</p>
                    <p><i class="fas fa-envelope"></i> admissions@stcyprianhskyabakadde.com</p>
                    <p><i class="fas fa-clock"></i> Mon-Fri: 8:00 AM - 5:00 PM</p>
                </div>
                
                <div class="quick-links">
                    <h4>Quick Links</h4>
                    <p><a href="#apply-now" style="color: white; text-decoration: none;">Apply Now</a></p>
                    <p><a href="#requirements" style="color: white; text-decoration: none;">Requirements</a></p>
                    <p><a href="#faq" style="color: white; text-decoration: none;">FAQ</a></p>
                    <p><a href="#contact" style="color: white; text-decoration: none;">Contact Us</a></p>
                </div>
            </div>
            
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 St Cyprian High School Kyabakadde. All rights reserved.</p>
                <p>Senior One Admissions - Term One 2026</p>
            </div>
        </div>
    </footer>

    <script>
        // Countdown Timer
        function updateCountdown() {
            const deadline = new Date('February 15, 2026 23:59:59').getTime();
            const now = new Date().getTime();
            const timeLeft = deadline - now;
            
            if (timeLeft < 0) {
                document.getElementById('countdown').innerHTML = '<h3>Applications Closed</h3>';
                return;
            }
            
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }
        
        setInterval(updateCountdown, 1000);
        updateCountdown();
        
        // FAQ Toggle
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                item.classList.toggle('active');
            });
        });
        
        // Form Submission
        document.getElementById('admissionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            // Simulate form submission (replace with actual submission)
            setTimeout(() => {
                alert('Application submitted successfully! We will contact you within 48 hours.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                this.reset();
                
                // Scroll to thank you message
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 2000);
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe elements for animation
        document.querySelectorAll('.feature-card, .step, .requirement-item, .faq-item').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>