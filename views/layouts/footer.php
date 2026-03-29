  </main><!-- /content -->
</div><!-- /main-wrapper -->

<!-- Botão flutuante "Instalar App" (aparece só quando o browser oferece instalação) -->
<button id="btn-instalar" onclick="instalarApp()">
  📲 Instalar App
</button>

<script>
// Mobile menu
const menuBtn = document.getElementById('menu-btn');
if (window.innerWidth <= 768) {
  menuBtn.style.display = 'block';
}
window.addEventListener('resize', () => {
  menuBtn.style.display = window.innerWidth <= 768 ? 'block' : 'none';
});
document.addEventListener('click', (e) => {
  const sidebar = document.getElementById('sidebar');
  if (window.innerWidth <= 768 && !sidebar.contains(e.target) && e.target !== menuBtn) {
    sidebar.classList.remove('open');
  }
});

// Confirmação de exclusão
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', (e) => {
    if (!confirm(el.dataset.confirm || 'Confirma a exclusão?')) e.preventDefault();
  });
});

// ── PWA: captura evento de instalação ──────────────────────
var deferredPrompt = null;
var btnInstalar = document.getElementById('btn-instalar');

window.addEventListener('beforeinstallprompt', function(e) {
  e.preventDefault();
  deferredPrompt = e;
  btnInstalar.style.display = 'flex';
});

function instalarApp() {
  if (!deferredPrompt) return;
  deferredPrompt.prompt();
  deferredPrompt.userChoice.then(function(result) {
    if (result.outcome === 'accepted') {
      btnInstalar.style.display = 'none';
    }
    deferredPrompt = null;
  });
}

// Esconde botão se já instalado
window.addEventListener('appinstalled', function() {
  btnInstalar.style.display = 'none';
  deferredPrompt = null;
});

// Detecta se está rodando como PWA instalado
if (window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true) {
  // Está rodando como app instalado
  document.body.classList.add('pwa-mode');
  btnInstalar.style.display = 'none';
}
</script>
</body>
</html>
