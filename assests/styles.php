<style>
    /* Reset and Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
    }

    /* Header Styles */
    .header {
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        background: #dc3545;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
    }

    .logo-text {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 2rem;
        align-items: center;
    }

    .nav-menu a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        transition: color 0.3s;
    }

    .nav-menu a:hover {
        color: #dc3545;
    }

    .btn-login, .btn-logout {
        background: #dc3545;
        color: white !important;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
    }

    .welcome {
        color: #666;
        font-size: 14px;
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1469&q=80');
        background-size: cover;
        background-position: center;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        margin-top: 70px;
    }

    .hero-content h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    .hero-content p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        max-width: 600px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    .cta-button {
        background: #dc3545;
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: 5px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .cta-button:hover {
        background: #c82333;
    }

    /* Search Section */
    .search-section {
        background: #f8f9fa;
        padding: 4rem 2rem;
    }

    .container {
        max-width: 100%;
        margin: 0 auto;
    }

    .search-section h2 {
        text-align: center;
        margin-bottom: 3rem;
        font-size: 2.5rem;
        color: #333;
    }

    .search-form {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #555;
    }

    .form-group select,
    .form-group input {
        padding: 12px;
        border: 2px solid #dee2e6;
        border-radius: 5px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-group select:focus,
    .form-group input:focus {
        outline: none;
        border-color: #dc3545;
    }

    .search-button {
        background: #dc3545;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        height: fit-content;
    }

    .search-button:hover {
        background: #c82333;
    }

    /* Available Rooms Section */
    .rooms-section {
        padding: 4rem 2rem;
        background: white;
    }

    .rooms-section h2 {
        text-align: center;
        margin-bottom: 3rem;
        font-size: 2.5rem;
        color: #333;
    }

    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .room-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        min-width: 280px;
        margin: 10px;
    }

    .room-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .room-card.available {
        border-left: 4px solid #10B981;
    }

    .room-card.occupied {
        border-left: 4px solid #EF4444;
    }

    .room-image {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: #6c757d;
    }

    .room-content {
        padding: 1.5rem;
    }

    .room-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .room-details {
        color: #666;
        margin-bottom: 1rem;
    }

    .room-availability {
        color: #28a745;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .book-button {
        background: #dc3545;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        width: 100%;
    }

    .book-button:hover {
        background: #c82333;
    }

    /* Footer */
    .footer {
        background: #333;
        color: white;
        text-align: center;
        padding: 2rem;
    }

    /* Signup Page Styles */
    .signup-wrapper {
        min-height: 100vh;
        padding: 100px ;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f8f8f8;
    }

    .signup-form-container {
    background-color: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    width: auto;
    min-width: 600px;
    max-width: 100%;
    }


    .signup-input {
        width: 100%;
        height: 48px;
        margin-bottom: 20px;
        padding: 12px 16px;
        border: 1px solid #a94442;
        border-radius: 10px;
        font-size: 1rem;
        font-family: inherit;
    }

    .signup-button {
        width: 100%;
        height: 40px;
        background-color: #c3272b;
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 20px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s;
    }

    .signup-button:hover {
        background-color: #a51e23;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .nav-container {
            padding: 1rem;
        }

        .nav-menu {
            gap: 1rem;
        }

        .hero-content h1 {
            font-size: 2.5rem;
        }

        .search-form {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .rooms-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .hero-content h1 {
            font-size: 2rem;
        }

        .hero-content p {
            font-size: 1rem;
        }
         
        .rooms-section h2 {
            font-size: 2rem;
        }
    }

    .login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 100px;
        background: #f8f8f8;
    }

    .login-form-container {
        max-width: 500px;
        width: 100%;
        padding: 40px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .login-title {
        font-size: 28px;
        font-weight: bold;
        color: #171212;
        margin-bottom: 12px;
    }

    .login-subtitle,
    .login-instruction {
        font-size: 16px;
        color: #876363;
        margin-bottom: 16px;
    }

    .login-form input {
        width: 100%;
        padding: 12px 16px;
        margin-bottom: 16px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }

    .login-button {
        width: 100%;
        padding: 12px;
        background: #C3272B;
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .login-button:hover {
        
    }

    .login-footer {
        margin-top: 20px;
        font-size: 14px;
        color: #876363;
    }

    .login-link,
    .login-register a {
        color: #C3272B;
        text-decoration: none;
        font-weight: bold;
    }

    .login-tabs {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .login-tabs .tab {
        font-size: 14px;
        font-weight: bold;
        padding-bottom: 5px;
        cursor: pointer;
        color: #876363;
        border-bottom: 3px solid #E5E8EB;
    }

    .login-tabs .tab.active {
        color: #C3272B;
        border-color: #C3272B;
    }

    /* ===== ROOM AVAILABILITY PAGE STYLES ===== */
    
    /* Main Container */
    .main-container {
        align-self: stretch;
        min-height: 800px;
        background: white;
        overflow: hidden;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: inline-flex;
    }

    .content-wrapper {
        align-self: stretch;
        padding-left: 160px;
        padding-right: 160px;
        padding-top: 20px;
        padding-bottom: 20px;
        justify-content: center;
        align-items: flex-start;
        display: inline-flex;
    }

    .content-container {
        flex: 1 1 0;
        max-width: 960px;
        overflow: hidden;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: inline-flex;
    }

    /* Header Section */
    .header-section {
        align-self: stretch;
        padding: 16px;
        justify-content: space-between;
        align-items: flex-start;
        display: inline-flex;
        flex-wrap: wrap;
        align-content: flex-start;
    }

    .header-left {
        width: 492px;
        min-width: 288px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 12px;
        display: inline-flex;
    }

    .title {
        color: #171212;
        font-size: 32px;
        font-weight: 700;
        line-height: 40px;
        word-wrap: break-word;
    }

    .datetime {
        align-self: stretch;
        color: black;
        font-size: 25px;
        font-weight: 700;
        line-height: 21px;
        word-wrap: break-word;
    }

    .date-picker-section {
        min-width: 160px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: inline-flex;
    }

    .date-picker-label {
        align-self: stretch;
        padding-bottom: 8px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: flex;
    }

    .date-picker-label-text {
        align-self: stretch;
        color: #1A0F0F;
        font-size: 16px;
        font-weight: 500;
        line-height: 24px;
        word-wrap: break-word;
    }

    .date-picker-input {
        width: 269px;
        height: 56px;
        padding: 15px;
        background: #FAFAFA;
        overflow: hidden;
        border-radius: 8px;
        outline: 1px #E5D1D1 solid;
        outline-offset: -1px;
        justify-content: flex-start;
        align-items: center;
        display: inline-flex;
        border: none;
        color: #915457;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
    }

    /* Statistics Section */
    .stats-section {
        align-self: stretch;
        padding: 16px;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 16px;
        display: inline-flex;
        flex-wrap: wrap;
        align-content: flex-start;
    }

    .stat-card {
        flex: 1 1 0;
        min-width: 158px;
        padding: 24px;
        border-radius: 12px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 8px;
        display: inline-flex;
    }

    .stat-card.available {
        background: #86BB8D;
    }

    .stat-card.occupied {
        background: #C3272B;
    }

    .stat-card.total, .stat-card.utilization {
        background: white;
        outline: 1px #E5D1D1 solid;
        outline-offset: -1px;
    }

    .stat-label {
        align-self: stretch;
        color: white;
        font-size: 16px;
        font-weight: 500;
        line-height: 24px;
        word-wrap: break-word;
    }

    .stat-card.total .stat-label, .stat-card.utilization .stat-label {
        color: #171212;
    }

    .stat-value {
        align-self: stretch;
        color: white;
        font-size: 24px;
        font-weight: 700;
        line-height: 30px;
        word-wrap: break-word;
    }

    .stat-card.total .stat-value, .stat-card.utilization .stat-value {
        color: #171212;
    }

    /* Section Title */
    .section-title {
        align-self: stretch;
        padding-top: 20px;
        padding-bottom: 12px;
        padding-left: 16px;
        padding-right: 16px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: flex;
    }

    .section-title-text {
        align-self: stretch;
        color: #171212;
        font-size: 22px;
        font-weight: 700;
        line-height: 28px;
        word-wrap: break-word;
    }

    /* Filter Tabs */
    .filter-tabs {
        align-self: stretch;
        padding-top: 12px;
        padding-bottom: 12px;
        padding-left: 12px;
        padding-right: 16px;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 12px;
        display: inline-flex;
        flex-wrap: wrap;
        align-content: flex-start;
    }

    .filter-tab {
        height: 32px;
        padding-left: 16px;
        padding-right: 16px;
        border-radius: 16px;
        justify-content: center;
        align-items: center;
        gap: 8px;
        display: flex;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-tab.active {
        background: #F5F0F0;
    }

    .filter-tab:not(.active) {
        background: white;
        outline: 1px #F5F0F0 solid;
        outline-offset: -1px;
    }

    .filter-tab-text {
        color: #171212;
        font-size: 14px;
        font-weight: 500;
        line-height: 21px;
        word-wrap: break-word;
    }

    /* Search Section */
    .search-section {
        align-self: stretch;
        padding-left: 16px;
        padding-right: 16px;
        padding-top: 12px;
        padding-bottom: 12px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: flex;
    }

    .search-container {
        align-self: stretch;
        height: 48px;
        min-width: 160px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: flex;
    }

    .search-box {
        align-self: stretch;
        flex: 1 1 0;
        border-radius: 12px;
        justify-content: flex-start;
        align-items: flex-start;
        display: inline-flex;
    }

    .search-icon {
        align-self: stretch;
        padding-left: 16px;
        background: #F5F0F0;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
        justify-content: center;
        align-items: center;
        display: flex;
    }

    .search-input {
        flex: 1 1 0;
        align-self: stretch;
        padding-top: 8px;
        padding-bottom: 8px;
        padding-left: 8px;
        padding-right: 16px;
        background: #F5F0F0;
        overflow: hidden;
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
        justify-content: flex-start;
        align-items: center;
        display: flex;
        border: none;
        outline: none;
        color: #876363;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
    }

    /* Room Type Filter */
    .room-type-filter {
        align-self: stretch;
        padding-top: 12px;
        padding-bottom: 12px;
        padding-left: 12px;
        padding-right: 16px;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 12px;
        display: inline-flex;
        flex-wrap: wrap;
        align-content: flex-start;
    }

    .room-type-tab {
        height: 32px;
        padding-left: 16px;
        padding-right: 16px;
        background: #F5F0F0;
        border-radius: 16px;
        justify-content: center;
        align-items: center;
        gap: 8px;
        display: flex;
        cursor: pointer;
    }

    .room-type-text {
        color: #171212;
        font-size: 14px;
        font-weight: 500;
        line-height: 21px;
        word-wrap: break-word;
    }

    /* Rooms Section */
    .rooms-section {
        align-self: stretch;
        padding-top: 12px;
        padding-bottom: 12px;
        padding-left: 1px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: flex;
    }

    .rooms-container {
        width: 943px;
        height: 194px;
        padding-top: 16px;
        padding-bottom: 16px;
        padding-left: 14px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 12px;
        display: flex;
    }

    .rooms-row {
        align-self: stretch;
        height: 159px;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 12px;
        display: inline-flex;
    }

    .room-card {
        flex: 1 1 0;
        align-self: stretch;
        padding: 16px;
        background: white;
        border-radius: 8px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 12px;
        display: inline-flex;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .room-card.available {
        outline: 1px #86BB8D solid;
        outline-offset: -1px;
    }

    .room-card.occupied {
        outline: 1px #C3272B solid;
        outline-offset: -1px;
    }

    .room-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .room-info {
        align-self: stretch;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 4px;
        display: flex;
    }

    .room-name {
        align-self: stretch;
        color: #171212;
        font-size: 16px;
        font-weight: 700;
        line-height: 20px;
        word-wrap: break-word;
    }

    .room-status {
        align-self: stretch;
    }

    .status-available {
        color: #86BB8D;
        font-size: 14px;
        font-weight: 700;
        line-height: 21px;
        word-wrap: break-word;
    }

    .status-occupied {
        color: #C3272B;
        font-size: 14px;
        font-weight: 700;
        line-height: 21px;
        word-wrap: break-word;
    }

    .status-details {
        color: #806B6B;
        font-size: 14px;
        font-weight: 400;
        line-height: 21px;
        word-wrap: break-word;
    }

    /* Footer */
    .footer {
        align-self: stretch;
        justify-content: center;
        align-items: flex-start;
        display: inline-flex;
    }

    .footer-content {
        flex: 1 1 0;
        max-width: 960px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        display: inline-flex;
    }

    .footer-text {
        align-self: stretch;
        flex: 1 1 0;
        padding-left: 20px;
        padding-right: 20px;
        padding-top: 40px;
        padding-bottom: 40px;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 24px;
        display: flex;
    }

    .copyright {
        align-self: stretch;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        display: flex;
    }

    .copyright-text {
        align-self: stretch;
        text-align: center;
        color: #915457;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
        word-wrap: break-word;
    }

    /* Logout Button */
    .logout-section {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .logout-button {
        padding: 10px 20px;
        background: #C3272B;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: background 0.3s ease;
    }

    .logout-button:hover {
        background: #A02024;
    }

    /* Responsive Design for Room Availability */
    @media (max-width: 768px) {
        .content-wrapper {
            padding-left: 20px;
            padding-right: 20px;
        }
        
        .header-section {
            flex-direction: column;
            gap: 16px;
        }
        
        .header-left {
            width: 100%;
        }
        
        .rooms-row {
            flex-direction: column;
            height: auto;
        }
        
        .room-card {
            min-height: 120px;
        }
    }

    /* No Rooms Message */
    .no-rooms-message {
        background: #F9FAFB;
        border: 1px dashed #D1D5DB;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        color: #6B7280;
        font-size: 16px;
        font-style: italic;
        margin: 20px 10px;
        min-width: 280px;
    }

</style>
