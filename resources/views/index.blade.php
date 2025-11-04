<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <link rel="stylesheet" type="text/css" href="{{ $cdnUrl }}/swagger-ui.css" />
    <style>
        html { box-sizing: border-box; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
        .swagger-ui .topbar .download-url-wrapper {
            display: none !important;
        }
    </style>
</head>
<body>
<div id="swagger-ui"></div>

<script src="{{ $cdnUrl }}/swagger-ui-bundle.js" charset="UTF-8"></script>
<script src="{{ $cdnUrl }}/swagger-ui-standalone-preset.js" charset="UTF-8"></script>
<script>
    window.onload = function() {
        const ui = SwaggerUIBundle({
            url: "{{ $jsonUrl }}",
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout"
        });
        window.ui = ui;
    };
</script>
</body>
</html>