<!-- <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <style>
      /* CSS styles remain the same */
    </style>
  </head>
  <body>
    <div class="login-container">
      <h2>Login</h2>
      <form id="loginForm">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />

        <button type="submit">Login</button>
      </form>
      <div id="message"></div>
    </div>

    <script>
      const form = document.getElementById("loginForm");
      const messageDiv = document.getElementById("message");

      form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        const response = await fetch("login.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `email=${encodeURIComponent(
            email
          )}&password=${encodeURIComponent(password)}`,
        });

        const data = await response.json();

        if (response.ok) {
          // Login successful
          console.log(data.message);
          console.log(data.member);
          // Redirect to the appropriate page or perform other actions
          window.location.href = "success.php"; // Redirect to a new page
        } else {
          // Login failed
          messageDiv.textContent = data.message;
        }
      });
    </script>
  </body>
</html> -->
