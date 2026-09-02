const API_BASE_AUTH = 'http://localhost/smeconnect/api/auth';

function checkSession() {
  fetch(`${API_BASE_AUTH}/check_session.php`, { credentials: 'same-origin' })
    .then(res => res.json())
    .then(data => {
      if (data.logged_in) {
        document.getElementById('loggedOutView').style.display = 'none';
        document.getElementById('loggedInView').style.display = 'flex';
        const initials = data.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        document.getElementById('userAvatarInitials').textContent = initials;
        document.getElementById('welcomeMsg').textContent = data.name;
        document.getElementById('dashboardLink').style.display = data.role === 'seller' ? 'inline' : 'none';
        document.getElementById('adminLink').style.display = data.role === 'admin' ? 'inline' : 'none';
      } else {
        document.getElementById('loggedOutView').style.display = 'flex';
        document.getElementById('loggedInView').style.display = 'none';
      }
    });
}

// Modal open/close
document.getElementById('showLoginBtn').addEventListener('click', () => {
  document.getElementById('authModal').style.display = 'flex';
  document.getElementById('loginForm').style.display = 'block';
  document.getElementById('registerForm').style.display = 'none';
});

document.getElementById('showRegisterBtn').addEventListener('click', () => {
  document.getElementById('authModal').style.display = 'flex';
  document.getElementById('loginForm').style.display = 'none';
  document.getElementById('registerForm').style.display = 'block';
});

document.getElementById('closeModalBtn').addEventListener('click', () => {
  document.getElementById('authModal').style.display = 'none';
});

document.getElementById('switchToRegister').addEventListener('click', () => {
  document.getElementById('loginForm').style.display = 'none';
  document.getElementById('registerForm').style.display = 'block';
});

document.getElementById('switchToLogin').addEventListener('click', () => {
  document.getElementById('registerForm').style.display = 'none';
  document.getElementById('loginForm').style.display = 'block';
});

// Login submit
document.getElementById('loginSubmitBtn').addEventListener('click', () => {
  const email = document.getElementById('loginEmail').value;
  const password = document.getElementById('loginPassword').value;

  fetch(`${API_BASE_AUTH}/login.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ email, password })
  })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        document.getElementById('authModal').style.display = 'none';
        checkSession();
      } else {
        document.getElementById('loginError').textContent = result.error;
      }
    });
});

// Register submit
document.getElementById('registerSubmitBtn').addEventListener('click', () => {
  const name = document.getElementById('regName').value;
  const email = document.getElementById('regEmail').value;
  const password = document.getElementById('regPassword').value;
  const role = document.getElementById('regRole').value;

  fetch(`${API_BASE_AUTH}/register.php`, {
    method: 'POST',
    credentials: 'same-origin',
    body: JSON.stringify({ name, email, password, role })
  })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        document.getElementById('authModal').style.display = 'none';
        checkSession();
      } else {
        document.getElementById('registerError').textContent = result.error;
      }
    });
});

// Logout
document.getElementById('logoutBtn').addEventListener('click', () => {
  fetch(`${API_BASE_AUTH}/logout.php`, { method: 'POST', credentials: 'same-origin' })
    .then(() => checkSession());
});

checkSession();

const searchForm = document.getElementById('searchForm');
if (searchForm) {
  searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const query = document.getElementById('searchInput').value.trim();
    if (query) {
      window.location.href = `/smeconnect/search.php?q=${encodeURIComponent(query)}`;
    }
  });
}