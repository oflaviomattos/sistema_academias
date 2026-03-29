// ============================================================
// SERVICE WORKER — Gestao de Academias PWA
// Estratégia: Network First (sempre tenta rede, usa cache se offline)
// ============================================================

var CACHE_NAME = 'academia-v1';
var CACHE_STATIC = [
    '/projetos/sistema_academias/public/css/app.css',
    '/projetos/sistema_academias/public/icons/icon-192.png',
    '/projetos/sistema_academias/public/icons/icon-512.png',
    '/projetos/sistema_academias/public/manifest.json',
];

// Instala e faz cache dos assets estáticos
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.addAll(CACHE_STATIC);
        })
    );
    self.skipWaiting();
});

// Ativa e limpa caches antigos
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(
                keys.filter(function(k) { return k !== CACHE_NAME; })
                    .map(function(k) { return caches.delete(k); })
            );
        })
    );
    self.clients.claim();
});

// Intercepta requisições
self.addEventListener('fetch', function(event) {
    var url = event.request.url;

    // Sempre busca na rede para páginas PHP (conteúdo dinâmico)
    if (url.indexOf('.php') !== -1 || url.indexOf('page=') !== -1) {
        event.respondWith(
            fetch(event.request).catch(function() {
                // Offline: retorna página de offline
                return new Response(
                    '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">'
                    + '<meta name="viewport" content="width=device-width,initial-scale=1">'
                    + '<title>Sem conexao</title>'
                    + '<style>body{font-family:system-ui;display:flex;align-items:center;justify-content:center;'
                    + 'min-height:100vh;margin:0;background:#1e2a3a;color:#fff;text-align:center;padding:20px}'
                    + '.icon{font-size:64px;margin-bottom:20px}'
                    + 'h1{font-size:22px;margin-bottom:12px}'
                    + 'p{color:#a8b8cc;font-size:14px;line-height:1.6}'
                    + '.btn{display:inline-block;margin-top:24px;background:#1a56db;color:#fff;'
                    + 'padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:600}'
                    + '</style></head><body>'
                    + '<div><div class="icon">📵</div>'
                    + '<h1>Sem conexao</h1>'
                    + '<p>Verifique sua conexao com a internet<br>e tente novamente.</p>'
                    + '<a class="btn" onclick="location.reload()">Tentar novamente</a>'
                    + '</div></body></html>',
                    { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                );
            })
        );
        return;
    }

    // Para assets estáticos: cache first
    event.respondWith(
        caches.match(event.request).then(function(cached) {
            return cached || fetch(event.request).then(function(response) {
                // Salva no cache se for asset estático válido
                if (response.status === 200 && event.request.method === 'GET') {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(event.request, clone);
                    });
                }
                return response;
            });
        })
    );
});
