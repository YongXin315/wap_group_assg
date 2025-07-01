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
        background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('images/Taylors.jpg');
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

    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.4);
  }
  .hero-container {
      position: relative;
      z-index: 1;
  }
  .hero h1 {
      color: white;
      font-size: 2.5rem;
      margin-bottom: 1rem;
  }
  .hero p {
      color: white;
      font-size: 1.2rem;
      margin-bottom: 2rem;
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
        width: 100%;
        min-height: 194px;
        height: auto;
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
        min-height: 159px;
        height: auto;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 12px;
        display: flex;
        flex-wrap: wrap;
    }

    .room-card {
        flex: 1 1 300px;
        min-width: 280px;
        max-width: 350px;
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

    .status-booked {
        color: #34C759;
        font-size: 14px;
        font-weight: 700;
        line-height: 21px;
        word-wrap: break-word;
    }

    .status-requested {
        color: #FF9500;
        font-size: 14px;
        font-weight: 600;
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

    /* ===== ROOM DETAILS PAGE STYLES ===== */
    
    /* Room Details Main Container */
    .main-container.room-details {
        min-height: 100vh;
        background: #F5F5F5;
        font-family: 'Inter', sans-serif;
    }

    .content-wrapper.room-details {
        display: flex;
        gap: 24px;
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px 24px;
    }

    .content-container.room-details {
        flex: 1;
        max-width: 920px;
    }

    /* Breadcrumb */
    .breadcrumb-section {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 16px;
        flex-wrap: wrap;
    }

    .breadcrumb-item {
        color: #876363;
        font-size: 16px;
        font-weight: 500;
        line-height: 24px;
    }

    .breadcrumb-item.current {
        color: #171212;
    }

    .breadcrumb-separator {
        color: #876363;
        font-size: 16px;
        font-weight: 500;
    }

    /* Room Title */
    .room-title-section {
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .room-title {
        color: #171212;
        font-size: 32px;
        font-weight: 700;
        line-height: 40px;
        max-width: 428px;
    }

    /* Section Title */
    .section-title {
        padding: 20px 16px 12px 16px;
    }

    .section-title-text {
        color: #171212;
        font-size: 22px;
        font-weight: 700;
        line-height: 28px;
    }

    /* Availability Status */
    .availability-status {
        height: 56px;
        min-height: 56px;
        padding: 0 16px;
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .status-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .status-icon {
        width: 40px;
        height: 40px;
        background: #F5F0F0;
        border-radius: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .icon-background {
        width: 24px;
        height: 24px;
        background: #171212;
        position: relative;
    }

    .status-label {
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
    }

    .status-label.available {
        color: #34C759;
    }

    .status-label.occupied {
        color: #EF4444;
    }

    .status-indicator {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .indicator-dot {
        width: 12px;
        height: 12px;
        border-radius: 6px;
    }

    .indicator-dot.available {
        background: #08875C;
    }

    .indicator-dot.occupied {
        background: #EF4444;
    }

    /* Room Details */
    .room-details {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .detail-row {
        height: 74px;
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 24px;
    }

    .detail-item {
        padding: 20px 0;
        border-top: 1px solid #E5E8EB;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
    }

    .detail-item:first-child {
        width: 167px;
    }

    .detail-item:nth-child(2) {
        flex: 1;
    }

    .detail-item.amenities {
        width: 309px;
    }

    .detail-label {
        color: #876363;
        font-size: 14px;
        font-weight: 400;
        line-height: 21px;
        margin-bottom: 8px;
    }

    .detail-value {
        color: #171212;
        font-size: 14px;
        font-weight: 400;
        line-height: 21px;
    }

    /* Schedule Container */
    .schedule-container {
        padding: 12px 16px;
    }

    .schedule-grid {
        background: white;
        border-radius: 12px;
        border: 1px solid #E5DBDB;
        overflow: hidden;
    }

    .schedule-header {
        display: flex;
        background: white;
    }

    .time-slot-header {
        padding: 12px 16px;
        color: #171212;
        font-size: 14px;
        font-weight: 500;
        line-height: 21px;
        text-align: center;
        min-width: 80px;
    }

    .schedule-status {
        display: flex;
        border-top: 1px solid #E5E8EB;
    }

    .status-cell {
        height: 72px;
        padding: 8px 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        font-size: 14px;
        font-weight: 400;
        line-height: 21px;
        min-width: 80px;
    }

    .status-cell.available {
        color: #876363;
    }

    .status-cell.booked {
        color: #FF3B30;
    }

    .status-cell.in-use {
        color: #007AFF;
    }

    /* Timeline Container */
    .timeline-container {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .timeline-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .timeline-icon {
        width: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .icon-dot {
        width: 24px;
        height: 24px;
        background: #171212;
        border-radius: 50%;
    }

    .timeline-line {
        width: 2px;
        height: 32px;
        background: #E5DBDB;
    }

    .timeline-content {
        flex: 1;
        padding: 12px 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .timeline-time {
        color: #171212;
        font-size: 16px;
        font-weight: 500;
        line-height: 24px;
    }

    .timeline-status {
        color: #876363;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
    }

    .timeline-status.available {
        color: #876363;
    }

    .timeline-status.booked {
        color: #FF3B30;
    }

    .timeline-status.in-use {
        color: #007AFF;
    }

    /* Book Button */
    .book-button-container {
        padding: 12px 16px;
        display: flex;
        justify-content: flex-end;
    }

    .book-room-button {
        height: 40px;
        max-width: 480px;
        min-width: 84px;
        padding: 0 16px;
        background: #E82933;
        border: none;
        border-radius: 20px;
        color: white;
        font-size: 14px;
        font-weight: 700;
        line-height: 21px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .book-room-button:hover {
        background: #C3272B;
    }

    /* Sidebar */
    .sidebar {
        width: 360px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Calendar */
    .calendar-container {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px;
    }

    .calendar-nav {
        width: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .nav-icon {
        width: 18px;
        height: 18px;
        background: #171212;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 12px;
        cursor: pointer;
    }

    .calendar-title {
        color: #171212;
        font-size: 16px;
        font-weight: 700;
        line-height: 20px;
        text-align: center;
    }

    .calendar-grid {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .calendar-days {
        display: flex;
    }

    .day-header {
        width: 47px;
        height: 48px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #171212;
        font-size: 13px;
        font-weight: 700;
        line-height: 20px;
    }

    .calendar-week {
        display: flex;
    }

    .calendar-day {
        width: 47px;
        height: 48px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: rgba(22.95, 17.85, 17.85, 0.45);
        font-size: 14px;
        font-weight: 500;
        line-height: 21px;
        border-radius: 24px;
    }

    .calendar-day.today {
        color: #171212;
    }

    .calendar-day.booked {
        background: #C3272B;
        color: white;
    }

    .calendar-day.empty {
        background: transparent;
    }

    .calendar-day.selectable {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .calendar-day.selectable:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #171212;
    }

    .calendar-day.selectable.selected {
        background: #dc3545;
        color: white;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .calendar-day.selectable.booked {
        cursor: not-allowed;
        background: #C3272B;
        color: white;
    }

    .calendar-day.selectable.booked:hover {
        background: #C3272B;
        color: white;
    }

    .calendar-day.selectable.disabled {
        cursor: not-allowed;
        opacity: 0.4;
        color: #999;
    }

    .calendar-day.selectable.disabled:hover {
        background: transparent;
        color: #999;
        opacity: 0.4;
    }

    /* Back Button */
    .back-button-container {
        padding: 12px 16px;
    }

    .back-button {
        width: 100%;
        height: 40px;
        padding: 0 16px;
        background: #C3272B;
        border: none;
        border-radius: 20px;
        color: white;
        font-size: 14px;
        font-weight: 700;
        line-height: 21px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .back-button:hover {
        background: #A02024;
    }

    /* Extended Schedule */
    .extended-schedule {
        padding: 0 16px 12px 40px;
    }

    .day-schedule-container,
    .evening-schedule-container {
        padding: 12px 0;
    }

    .bottom-book-button-container {
        padding: 12px 0;
        display: flex;
        justify-content: flex-end;
    }

    /* Responsive Design for Room Details */
    @media (max-width: 1200px) {
        .content-wrapper.room-details {
            flex-direction: column;
        }
        
        .sidebar {
            width: 100%;
            max-width: 920px;
        }
    }

    @media (max-width: 768px) {
        .content-wrapper.room-details {
            padding: 10px;
        }
        
        .room-title {
            font-size: 24px;
            line-height: 32px;
        }
        
        .detail-row {
            flex-direction: column;
            height: auto;
            gap: 16px;
        }
        
        .detail-item {
            width: 100% !important;
        }
        
        .schedule-header,
        .schedule-status {
            flex-wrap: wrap;
        }
        
        .time-slot-header,
        .status-cell {
            min-width: 60px;
            font-size: 12px;
        }
    }

    /* ===== BOOKING PAGE STYLES ===== */
    
    .booking-page {
        min-height: 100vh;
        background: #F5F5F5;
        font-family: 'Inter', sans-serif;
    }

    .booking-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .booking-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* Breadcrumb Links */
    .breadcrumb-link {
        color: #876363;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .breadcrumb-link:hover {
        color: #C3272B;
    }

    /* Page Title */
    .page-title-section {
        padding: 24px 24px 16px 24px;
        border-bottom: 1px solid #E5E8EB;
    }

    .page-title {
        color: #171212;
        font-size: 28px;
        font-weight: 700;
        line-height: 36px;
        margin: 0 0 8px 0;
    }

    .page-subtitle {
        color: #876363;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
        margin: 0;
    }

    /* Booking Form Container */
    .booking-form-container {
        padding: 24px;
    }

    /* Room Info Card */
    .room-info-card {
        background: #F9FAFB;
        border: 1px solid #E5E8EB;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .room-info-title {
        color: #171212;
        font-size: 18px;
        font-weight: 600;
        line-height: 24px;
        margin: 0 0 16px 0;
    }

    .room-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        color: #876363;
        font-size: 14px;
        font-weight: 500;
        line-height: 20px;
    }

    .info-value {
        color: #171212;
        font-size: 16px;
        font-weight: 600;
        line-height: 24px;
    }

    /* Messages */
    .success-message {
        background: #D1FAE5;
        border: 1px solid #10B981;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
        color: #065F46;
        font-size: 14px;
        font-weight: 500;
    }

    .error-message {
        background: #FEE2E2;
        border: 1px solid #EF4444;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
        color: #991B1B;
        font-size: 14px;
        font-weight: 500;
    }

    /* Booking Form */
    .booking-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        color: #171212;
        font-size: 14px;
        font-weight: 600;
        line-height: 20px;
    }

    .form-input,
    .form-select {
        padding: 12px 16px;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
        background: white;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #C3272B;
        box-shadow: 0 0 0 3px rgba(195, 39, 43, 0.1);
    }

    .form-textarea {
        padding: 12px 16px;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 400;
        line-height: 24px;
        background: white;
        min-height: 100px;
        resize: vertical;
        font-family: inherit;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #C3272B;
        box-shadow: 0 0 0 3px rgba(195, 39, 43, 0.1);
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 16px;
        justify-content: flex-end;
        padding-top: 16px;
        border-top: 1px solid #E5E8EB;
    }

    .btn-secondary {
        padding: 12px 24px;
        background: white;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        color: #171212;
        font-size: 14px;
        font-weight: 600;
        line-height: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #F9FAFB;
        border-color: #9CA3AF;
    }

    .btn-primary {
        padding: 12px 24px;
        background: #C3272B;
        border: none;
        border-radius: 8px;
        color: white;
        font-size: 14px;
        font-weight: 600;
        line-height: 20px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background: #A02024;
    }

    /* Responsive Design for Booking Page */
    @media (max-width: 768px) {
        .booking-wrapper {
            padding: 10px;
        }
        
        .page-title {
            font-size: 24px;
            line-height: 32px;
        }
        
        .booking-form-container {
            padding: 16px;
        }
        
        .room-info-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-secondary,
        .btn-primary {
            width: 100%;
        }
    }

    /* ===== CONFIRM BOOKING PAGE STYLES ===== */
    
    .confirm-booking {
        min-height: 100vh;
        background: #F5F5F5;
        font-family: 'Inter', sans-serif;
    }

    .content-wrapper.confirm-booking {
        max-width: 960px;
        margin: 0 auto;
        padding: 20px 160px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .booking-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Page Title */
    .page-title-section {
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .page-title {
        color: #171212;
        font-size: 32px;
        font-family: Inter;
        font-weight: 700;
        line-height: 40px;
        word-wrap: break-word;
        min-width: 288px;
    }

    /* Section Titles */
    .section-title {
        color: #171212;
        font-size: 18px;
        font-family: Inter;
        font-weight: 700;
        line-height: 23px;
        word-wrap: break-word;
        padding: 16px 16px 8px 16px;
    }

    /* Booking Summary Section */
    .summary-section {
        padding: 16px 0 8px 0;
    }

    .summary-content {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .summary-row {
        display: flex;
        gap: 24px;
        height: 94px;
    }

    .summary-item {
        padding: 20px 0;
        border-top: 1px #E5E8EB solid;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        flex: 1;
    }

    .summary-label {
        color: #876363;
        font-size: 14px;
        font-family: Inter;
        font-weight: 400;
        line-height: 21px;
        word-wrap: break-word;
        flex: 1;
    }

    .summary-value {
        color: #171212;
        font-size: 14px;
        font-family: Inter;
        font-weight: 400;
        line-height: 21px;
        word-wrap: break-word;
        flex: 1;
    }

    .summary-value-container {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .summary-value-container .calendar-preview {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        display: none;
        background: white;
        border: 1px solid #E5DBDB;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 300px;
        margin-top: 8px;
    }

    .summary-value-container:hover .calendar-preview,
    .summary-value-container .calendar-preview:hover {
        display: block;
    }

    /* Student Details Section */
    .student-details-section {
        padding: 16px 0 8px 0;
    }

    /* Booking Details Section */
    .booking-details-section {
        padding: 16px 0 8px 0;
        flex: 1;
    }

    /* Form Rows */
    .form-row {
        max-width: 480px;
        padding: 12px 16px;
        display: flex;
        justify-content: flex-start;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
        align-content: flex-end;
    }

    .form-group {
        flex: 1 1 0;
        min-width: 160px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
    }

    .form-label {
        color: #171212;
        font-size: 16px;
        font-family: Inter;
        font-weight: 500;
        line-height: 24px;
        word-wrap: break-word;
        padding-bottom: 8px;
        align-self: stretch;
    }

    .form-input {
        align-self: stretch;
        height: 56px;
        padding: 15px;
        background: white;
        overflow: hidden;
        border-radius: 12px;
        outline: 1px #E5DBDB solid;
        outline-offset: -1px;
        border: none;
        color: #876363;
        font-size: 16px;
        font-family: Inter;
        font-weight: 400;
        line-height: 24px;
        word-wrap: break-word;
        transition: outline-color 0.3s ease;
    }

    .form-input:focus {
        outline-color: #C3272B;
    }

    .form-input::placeholder {
        color: #876363;
    }

    /* Date Input Specific Styles */
    input[type="date"].form-input {
        position: relative;
    }

    input[type="date"].form-input::-webkit-calendar-picker-indicator {
        background: #876363;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Dynamic Schedule Updates */
    .schedule-container.updating {
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .timeline-container.updating {
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    /* Submit Button */
    .submit-section {
        padding: 12px 16px;
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
    }

    .submit-button {
        width: 453px;
        height: 40px;
        max-width: 480px;
        min-width: 84px;
        padding: 0 16px;
        background: #C3272B;
        overflow: hidden;
        border-radius: 20px;
        border: none;
        justify-content: center;
        align-items: center;
        display: flex;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .submit-button:hover {
        background: #A02024;
    }

    .submit-button span {
        text-align: center;
        color: white;
        font-size: 14px;
        font-family: Inter;
        font-weight: 700;
        line-height: 21px;
        word-wrap: break-word;
    }

    /* Cancel Link */
    .cancel-section {
        padding: 4px 16px 12px 16px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        height: 60px;
    }

    .cancel-link {
        color: #876363;
        font-size: 21px;
        font-family: Inter;
        font-weight: 700;
        line-height: 21px;
        word-wrap: break-word;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .cancel-link:hover {
        color: #171212;
    }

    /* Success/Error Messages */
    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 12px 16px;
        border-radius: 8px;
        margin: 16px;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 12px 16px;
        border-radius: 8px;
        margin: 16px;
        border: 1px solid #f5c6cb;
    }

    /* Responsive Design for Confirm Booking */
    @media (max-width: 1200px) {
        .content-wrapper.confirm-booking {
            padding: 20px 80px;
        }
    }

    @media (max-width: 768px) {
        .content-wrapper.confirm-booking {
            padding: 20px;
        }
        
        .page-title {
            font-size: 24px;
            line-height: 32px;
        }
        
        .summary-row {
            flex-direction: column;
            height: auto;
            gap: 16px;
        }
        
        .summary-item {
            width: 100%;
        }
        
        .form-row {
            max-width: 100%;
        }
        
        .form-group {
            min-width: 100%;
        }
        
        .submit-button {
            width: 100%;
        }
    }

    /* Date Selection Container */
    .date-selection-container {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .calendar-preview {
        background: white;
        border: 1px solid #E5DBDB;
        border-radius: 12px;
        padding: 16px;
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 300px;
    }

    .date-selection-container:hover .calendar-preview,
    .calendar-preview:hover {
        display: block;
    }

    /* Form Select Styles */
    .form-select {
        align-self: stretch;
        height: 56px;
        padding: 15px;
        background: white;
        overflow: hidden;
        border-radius: 12px;
        outline: 1px #E5DBDB solid;
        outline-offset: -1px;
        border: none;
        color: #876363;
        font-size: 16px;
        font-family: Inter;
        font-weight: 400;
        line-height: 24px;
        word-wrap: break-word;
        transition: outline-color 0.3s ease;
        cursor: pointer;
    }

    .form-select:focus {
        outline-color: #C3272B;
    }

    .form-select option {
        color: #171212;
        background: white;
        padding: 8px;
    }

    /* Availability Status Display */
    .availability-status-display {
        width: 100%;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #E5DBDB;
        background: #f8f9fa;
        margin-top: 8px;
    }

    .status-message {
        color: #876363;
        font-size: 14px;
        font-family: Inter;
        font-weight: 400;
        text-align: center;
    }

    .status-message.available {
        color: #155724;
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        padding: 8px;
    }

    .status-message.unavailable {
        color: #721c24;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 6px;
        padding: 8px;
    }

    .status-message.checking {
        color: #856404;
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 8px;
    }

    /* Calendar Styles for Booking Page */
    .calendar-preview .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px;
        margin-bottom: 8px;
    }

    .calendar-preview .calendar-nav {
        width: 30px;
        height: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #171212;
        color: white;
        font-size: 12px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .calendar-preview .calendar-nav:hover {
        background: #C3272B;
    }

    .calendar-preview .calendar-title {
        color: #171212;
        font-size: 14px;
        font-weight: 700;
        line-height: 20px;
        text-align: center;
    }

    .calendar-preview .calendar-grid {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .calendar-preview .calendar-days {
        display: flex;
    }

    .calendar-preview .day-header {
        width: 35px;
        height: 35px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #171212;
        font-size: 12px;
        font-weight: 700;
        line-height: 20px;
    }

    .calendar-preview .calendar-week {
        display: flex;
    }

    .calendar-preview .calendar-day {
        width: 35px;
        height: 35px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: rgba(22.95, 17.85, 17.85, 0.45);
        font-size: 12px;
        font-weight: 500;
        line-height: 21px;
        border-radius: 17.5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .calendar-preview .calendar-day:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #171212;
    }

    .calendar-preview .calendar-day.today {
        color: #171212;
        background: rgba(220, 53, 69, 0.2);
    }

    .calendar-preview .calendar-day.selected {
        background: #C3272B;
        color: white;
    }

    .calendar-preview .calendar-day.other-month {
        opacity: 0.3;
        cursor: default;
    }

    .calendar-preview .calendar-day.past {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .calendar-preview .calendar-day.past:hover {
        background: transparent;
        color: rgba(22.95, 17.85, 17.85, 0.45);
    }
</style>
