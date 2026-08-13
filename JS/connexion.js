document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('login-form');
  const toggle = document.getElementById('togglePwd');
  const pwd = document.getElementById('password');

  if (toggle && pwd) {
    toggle.addEventListener('click', () => {
      if (pwd.type === 'password') {
        pwd.type = 'text';
        toggle.setAttribute('aria-label', 'Masquer le mot de passe');
      } else {
        pwd.type = 'password';
        toggle.setAttribute('aria-label', 'Afficher le mot de passe');
      }
    });
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('identifier');
    const password = document.getElementById('password');
    const errs = form.querySelectorAll('.error');
    errs.forEach(el => el.textContent = '');

    let valid = true;
    if (!id.value.trim()) {
      form.querySelector('#identifier + .error').textContent = 'Veuillez saisir votre email ou téléphone.';
      valid = false;
    }
    if (!password.value || password.value.length < 6) {
      form.querySelector('#password + .error').textContent = 'Le mot de passe doit contenir au moins 6 caractères.';
      valid = false;
    }

    if (!valid) return;

    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Connexion...';

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await response.json();
      if (data.success) {
        window.location.href = '../utilisateur/dashboard.php';
        return;
      }

      if (data.errors && data.errors.length) {
        form.querySelector('#identifier + .error').textContent = data.errors[0];
      } else {
        form.querySelector('#identifier + .error').textContent = 'Impossible de se connecter pour le moment.';
      }
    } catch (error) {
      form.querySelector('#identifier + .error').textContent = 'Erreur réseau. Merci de réessayer.';
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Se connecter';
    }
  });
});
