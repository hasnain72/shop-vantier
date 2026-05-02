<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vantier API — Docs</title>
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui.css">
  <style>
    /* ── Brand bar ── */
    body { margin: 0; background: #fafafa; }
    #topbar-wrapper { display: none !important; }

    .vantier-bar {
      background: #1a1d2e;
      padding: .7rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .vantier-bar .brand {
      color: #fff;
      font-family: 'Segoe UI', system-ui, sans-serif;
      font-size: .85rem;
      font-weight: 700;
      letter-spacing: .16em;
      text-transform: uppercase;
    }
    .vantier-bar .badge {
      background: #5c6ac4;
      color: #fff;
      font-size: .65rem;
      padding: .2em .55em;
      border-radius: 20px;
      letter-spacing: .04em;
    }
    .vantier-bar a {
      margin-left: auto;
      color: #94a3b8;
      font-size: .78rem;
      text-decoration: none;
      font-family: system-ui;
    }
    .vantier-bar a:hover { color: #fff; }

    /* ── Swagger UI tweaks ── */
    .swagger-ui .topbar { display: none; }
    .swagger-ui .information-container { padding: 1rem 1.5rem .5rem; }
    .swagger-ui .info .title { font-size: 1.6rem; }
    .swagger-ui .scheme-container { padding: .5rem 1.5rem; }

    .swagger-ui .opblock-tag {
      font-size: .92rem;
      padding: .55rem 1rem;
    }
    .swagger-ui .opblock .opblock-summary-description {
      font-size: .82rem;
    }
    /* Prettier method colours */
    .swagger-ui .opblock.opblock-get    { border-color: #0d6efd; background: rgba(13,110,253,.04); }
    .swagger-ui .opblock.opblock-post   { border-color: #198754; background: rgba(25,135,84,.04); }
    .swagger-ui .opblock.opblock-put    { border-color: #fd7e14; background: rgba(253,126,20,.04); }
    .swagger-ui .opblock.opblock-delete { border-color: #dc3545; background: rgba(220,53,69,.04); }
    .swagger-ui .opblock.opblock-get    .opblock-summary-method { background: #0d6efd; }
    .swagger-ui .opblock.opblock-post   .opblock-summary-method { background: #198754; }
    .swagger-ui .opblock.opblock-put    .opblock-summary-method { background: #fd7e14; }
    .swagger-ui .opblock.opblock-delete .opblock-summary-method { background: #dc3545; }
  </style>
</head>
<body>

<div class="vantier-bar">
  <span class="brand">Vantier</span>
  <span class="badge">API v1</span>
  <a href="{{ route('admin.login') }}">&#8592; Admin login</a>
</div>

<div id="swagger-ui"></div>

<script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-bundle.js"></script>
<script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-standalone-preset.js"></script>
<script>
  SwaggerUIBundle({
    url:            "{{ route('api.docs.spec') }}",
    dom_id:         '#swagger-ui',
    presets:        [SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset],
    layout:         'StandaloneLayout',
    deepLinking:    true,
    displayRequestDuration: true,
    defaultModelsExpandDepth: 1,
    defaultModelExpandDepth: 1,
    docExpansion:   'list',
    filter:         true,
    tryItOutEnabled: false,
    persistAuthorization: true,
    requestInterceptor: (req) => {
      // Always add Accept: application/json so Laravel returns JSON errors
      req.headers['Accept'] = 'application/json';
      return req;
    },
  });
</script>
</body>
</html>
