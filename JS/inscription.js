document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('register-form');
  const toggle = document.getElementById('toggleRegisterPwd');
  const pwd = document.getElementById('registerPassword');

  if (toggle && pwd) {
    toggle.addEventListener('click', () => {
      const isHidden = pwd.type === 'password';
      pwd.type = isHidden ? 'text' : 'password';
      toggle.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
    });
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const fullName = document.getElementById('fullName');
    const email = document.getElementById('email');
    const password = document.getElementById('registerPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const fields = form.querySelectorAll('.field');

    fields.forEach((field) => {
      const error = field.querySelector('.error');
      if (error) error.textContent = '';
    });

    let valid = true;

    if (!fullName.value.trim()) {
      fullName.parentElement.querySelector('.error').textContent = 'Veuillez entrer votre nom complet.';
      valid = false;
    }

    if (!email.value.trim()) {
      email.parentElement.querySelector('.error').textContent = 'Veuillez entrer votre email.';
      valid = false;
    } else if (!email.value.includes('@')) {
      email.parentElement.querySelector('.error').textContent = 'Email invalide.';
      valid = false;
    }

    if (!password.value || password.value.length < 6) {
      password.parentElement.querySelector('.error').textContent = 'Le mot de passe doit contenir au moins 6 caractères.';
      valid = false;
    }

    if (!confirmPassword.value) {
      confirmPassword.parentElement.querySelector('.error').textContent = 'Veuillez confirmer le mot de passe.';
      valid = false;
    } else if (confirmPassword.value !== password.value) {
      confirmPassword.parentElement.querySelector('.error').textContent = 'Les mots de passe ne correspondent pas.';
      valid = false;
    }

    if (!valid) return;

    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Création...';

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
        const message = data.errors[0];
        fullName.parentElement.querySelector('.error').textContent = message;
      } else {
        alert('Erreur lors de l’inscription, veuillez réessayer.');
      }
    } catch (error) {
      alert('Erreur réseau. Merci de réessayer plus tard.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Créer mon compte';
    }
  });
});
