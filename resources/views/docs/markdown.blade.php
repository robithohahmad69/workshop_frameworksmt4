<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Marked.js for Markdown parsing -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f8f9fa;
            padding: 20px 0;
        }

        .doc-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .doc-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .doc-header h1 {
            color: #667eea;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .doc-header .badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
        }

        #markdown-content {
            line-height: 1.8;
            font-size: 16px;
        }

        #markdown-content h1 {
            color: #667eea;
            font-size: 28px;
            font-weight: bold;
            margin-top: 40px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        #markdown-content h2 {
            color: #764ba2;
            font-size: 24px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        #markdown-content h3 {
            color: #495057;
            font-size: 20px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 12px;
        }

        #markdown-content h4 {
            color: #6c757d;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        #markdown-content p {
            margin-bottom: 15px;
            color: #495057;
        }

        #markdown-content ul, #markdown-content ol {
            margin-bottom: 20px;
            padding-left: 30px;
        }

        #markdown-content li {
            margin-bottom: 8px;
            color: #495057;
        }

        #markdown-content code {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #e83e8c;
        }

        #markdown-content pre {
            background: #212529;
            color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            overflow-x: auto;
            margin-bottom: 20px;
        }

        #markdown-content pre code {
            background: transparent;
            border: none;
            color: #f8f9fa;
            padding: 0;
        }

        #markdown-content blockquote {
            border-left: 4px solid #667eea;
            padding-left: 20px;
            margin: 20px 0;
            color: #6c757d;
            font-style: italic;
        }

        #markdown-content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        #markdown-content table th,
        #markdown-content table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        #markdown-content table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }

        #markdown-content a {
            color: #667eea;
            text-decoration: none;
        }

        #markdown-content a:hover {
            text-decoration: underline;
        }

        #markdown-content hr {
            border: none;
            border-top: 2px solid #e9ecef;
            margin: 30px 0;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.2s;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .doc-container {
                padding: 20px;
            }

            .doc-header h1 {
                font-size: 24px;
            }

            #markdown-content {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="doc-container">
            <a href="javascript:history.back()" class="btn-back">
                ← Kembali
            </a>

            <div class="doc-header">
                <h1>{{ $title }}</h1>
                <span class="badge">📚 Dokumentasi</span>
            </div>

            <div id="markdown-content">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat dokumentasi...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Parse markdown and render to HTML
        document.addEventListener('DOMContentLoaded', function() {
            const markdownContent = `{{ $content }}`;

            // Configure marked options
            marked.setOptions({
                breaks: true,
                gfm: true,
                headerIds: true,
                mangle: false
            });

            // Parse markdown to HTML
            const htmlContent = marked.parse(markdownContent);

            // Render to page
            document.getElementById('markdown-content').innerHTML = htmlContent;
        });
    </script>
</body>
</html>
