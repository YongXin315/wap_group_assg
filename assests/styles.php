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
        max-width: 1200px;
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
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .room-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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
</style>
