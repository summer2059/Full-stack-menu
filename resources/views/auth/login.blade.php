
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login | Restaurant Menu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ✅ Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --accent: #FF7043;
      --text-dark: #2C2C2C;
    }

    body, html {
      height: 100%;
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: #f8f9fa;
    }

    /* 🍕 Header Section */
    .header-section {
      background: url('https://i.ibb.co/N2wqnrJJ/129ffc3af9161580901c38f57428edd4.jpg') no-repeat center center/cover;
      color: white;
      text-align: center;
      padding: 4rem 1rem 6rem;
      position: relative;
    }

    .header-section::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
      z-index: 1;
    }

    .header-section .content {
      position: relative;
      z-index: 2;
    }

    .header-section h1 {
      font-weight: 700;
      letter-spacing: 1px;
      font-size: 2.5rem;
    }

    .header-section p {
      font-size: 1.2rem;
      margin-top: 0.5rem;
      margin-bottom: 2rem;
    }

    /* 📝 Login box */
    .login-box {
      background: #fff;
      border-radius: 16px;
      width: 100%;
      max-width: 550px;
      margin: -100px auto 2rem;
      padding: 3rem;
      box-shadow: 0 12px 35px rgba(0,0,0,0.2);
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      z-index: 2;
    }

    .login-box h3 {
      text-align: center;
      margin-bottom: 1.5rem;
      font-size: 2rem;
      font-weight: 600;
    }

    .login-box p {
      text-align: center;
      margin-bottom: 2rem;
      color: #666;
      font-size: 1rem;
    }

    .form-control {
      padding: 0.85rem;
      font-size: 1rem;
    }

    .btn-login {
      background: var(--accent);
      border: none;
      color: #fff;
      width: 100%;
      padding: 1rem;
      border-radius: 8px;
      font-weight: 600;
      transition: 0.3s ease;
      font-size: 1.05rem;
    }

    .btn-login:hover {
      opacity: 0.9;
    }

    .remember-forgot {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 0.5rem;
    }

    .remember-forgot a {
      text-decoration: none;
    }

    /* 📱 Tablet */
    @media (max-width: 992px) {
      .login-box {
        max-width: 450px;
        padding: 2.5rem;
        margin-top: -100px;
      }

      .header-section h1 {
        font-size: 2rem;
      }

      .header-section p {
        font-size: 1rem;
      }
    }

    /* 📱 Mobile */
    @media (max-width: 576px) {
      .header-section {
        padding: 2.5rem 1rem 4rem;
      }

      .header-section h1 {
        font-size: 1.5rem;
      }

      .header-section p {
        font-size: 1.2rem;
        margin-top: 1.5rem;;
      }

      .login-box {
        width: 95%;
        padding: 1.8rem;
        margin-top: -80px;
      }

      .login-box h3 {
        font-size: 1.5rem;
      }

      .form-control {
        padding: 0.7rem;
        font-size: 0.95rem;
      }

      .btn-login {
        padding: 0.85rem;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- 🍕 Header Section -->
  <section class="header-section">
    <div class="content">
      <h1>🍕 RESTAURANT ADMIN PANEL</h1>
      <p>Manage your restaurant menu and orders efficiently</p>
    </div>
  </section>

  <!-- 📝 Login Box -->
  <div class="login-box">
    <h3>Admin Login</h3>
    <p>Please login to access the admin dashboard</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Email/Username*</label>
        <input type="text" id="email" name="email" class="form-control" placeholder="admin@restaurant.com" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password*</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>

      <div class="remember-forgot">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="remember" name="remember">
          <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <a href="#">Forgot password?</a>
      </div>

      <button type="submit" class="btn btn-login">Login</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
