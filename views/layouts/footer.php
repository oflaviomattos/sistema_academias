  </main>
</div><!-- /main-wrapper -->

<!-- Botão instalar PWA (aparece só quando disponível) -->
<button id="btn-instalar" onclick="instalarApp()">📲 Instalar App</button>

<!-- Overlay escuro fecha a sidebar no mobile -->
<div id="sidebar-overlay" onclick="fecharSidebar()"></div>

<script>
(function() {
  var sidebar  = document.getElementById('sidebar');
  var overlay  = document.getElementById('sidebar-overlay');
  var menuBtn  = document.getElementById('menu-btn');
  var aberta   = false;

  function abrirSidebar() {
    sidebar.classList.add('open');
    overlay.style.display = 'block';
    aberta = true;
  }

  function fecharSidebar() {
    sidebar.classList.remove('open');
    overlay.style.display = 'none';
    aberta = false;
  }

  // Expõe fecharSidebar globalmente (usado pelo onclick do overlay)
  window.fecharSidebar = fecharSidebar;

  if (menuBtn) {
    menuBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      aberta ? fecharSidebar() : abrirSidebar();
    });
  }

  // Fecha ao navegar (clique em link dentro da sidebar)
  if (sidebar) {
    sidebar.querySelectorAll('a.nav-item').forEach(function(link) {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 768) fecharSidebar();
      });
    });
  }

  // Fecha se tela crescer (rotação de dispositivo)
  window.addEventListener('resize', function() {
    if (window.innerWidth > 768) fecharSidebar();
  });

  // ── Confirmação de exclusão ────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(function(el) {
    el.addEventListener('click', function(e) {
      if (!confirm(el.dataset.confirm || 'Confirmar?')) e.preventDefault();
    });
  });

  // ── PWA ───────────────────────────────────────────────
  var deferredPrompt = null;
  var btnInstalar = document.getElementById('btn-instalar');

  window.addEventListener('beforeinstallprompt', function(e) {
    e.preventDefault();
    deferredPrompt = e;
    if (btnInstalar) btnInstalar.style.display = 'flex';
  });

  window.instalarApp = function() {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then(function(r) {
      if (r.outcome === 'accepted' && btnInstalar) btnInstalar.style.display = 'none';
      deferredPrompt = null;
    });
  };

  window.addEventListener('appinstalled', function() {
    if (btnInstalar) btnInstalar.style.display = 'none';
  });

  if (window.matchMedia('(display-mode: standalone)').matches || navigator.standalone) {
    document.body.classList.add('pwa-mode');
    if (btnInstalar) btnInstalar.style.display = 'none';
  }

  // ── Ocultar/Mostrar valores monetários ─────────────────────
  var valoresOcultos = localStorage.getItem('valores_ocultos') === '1';

  function aplicarMascara() {
    var btn = document.getElementById('btn-ocultar-valores');
    if (btn) btn.textContent = valoresOcultos ? '👁‍🗨' : '👁';

    document.querySelectorAll('.valor-monetario, .stat-card .value, td').forEach(function(el) {
      if (el.dataset.original === undefined) {
        var texto = el.textContent.trim();
        if (/R\$|^\d[\d.,]+$/.test(texto) && texto.length > 1) {
          el.dataset.original = texto;
          if (valoresOcultos) {
            el.dataset.masked = '1';
            el.textContent = 'R$ ****,**';
          }
        }
      } else {
        if (valoresOcultos && !el.dataset.masked) {
          el.dataset.masked = '1';
          el.textContent = 'R$ ****,**';
        } else if (!valoresOcultos && el.dataset.masked) {
          delete el.dataset.masked;
          el.textContent = el.dataset.original;
        }
      }
    });
  }

  window.toggleValores = function() {
    valoresOcultos = !valoresOcultos;
    localStorage.setItem('valores_ocultos', valoresOcultos ? '1' : '0');

    // Reseta para reaplicar
    document.querySelectorAll('[data-masked]').forEach(function(el) {
      delete el.dataset.masked;
      if (el.dataset.original !== undefined) {
        el.textContent = el.dataset.original;
      }
    });

    if (valoresOcultos) {
      aplicarMascara();
    } else {
      document.querySelectorAll('[data-original]').forEach(function(el) {
        el.textContent = el.dataset.original;
      });
      var btn = document.getElementById('btn-ocultar-valores');
      if (btn) btn.textContent = '👁';
    }
  };

  // Aplica ao carregar se estava oculto
  if (valoresOcultos) {
    aplicarMascara();
  }

  // Observa mudanças no DOM para aplicar em conteúdo dinâmico
  var observer = new MutationObserver(function() {
    if (valoresOcultos) aplicarMascara();
  });
  observer.observe(document.querySelector('.content') || document.body, { childList: true, subtree: true });
})();
</script>
</body>
</html>
