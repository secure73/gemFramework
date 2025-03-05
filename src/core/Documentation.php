<?php

namespace Gemvc\Core;

use Gemvc\Core\ApiDocGenerator;
use Gemvc\Http\JsonResponse;
use Gemvc\Http\Response;

class Documentation
{
    /**
     * @param array<string, array{description: string, endpoints: array<string, array{method: string, url: string, description: string, parameters?: array<string, array{type: string, required: bool}>, query_parameters?: array<string, array<string, array{type: string, required: bool}>>, response?: string|false}>}> $documentation
     */
    private function generateHtmlView(array $documentation): string
    {
        // Add these styles to the existing <style> section
        $additionalStyles = <<<CSS
            .service-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                cursor: pointer;
                padding: 15px 20px;
                background: #fff;
                border-radius: 8px;
                margin-bottom: 2px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .service-header h2 {
                margin: 0;
                font-size: 20px;
                color: #1976d2;
            }
            .service-content {
                display: none;
                padding: 20px;
                background: #fff;
                border-radius: 0 0 8px 8px;
                margin-bottom: 20px;
            }
            .service-content.active {
                display: block;
            }
            .accordion-icon {
                width: 24px;
                height: 24px;
                transition: transform 0.3s ease;
            }
            .service-header.active .accordion-icon {
                transform: rotate(180deg);
            }
            .service-section {
                margin-bottom: 20px;
                border-radius: 8px;
                overflow: hidden;
            }
        CSS;

        // Update the HTML template
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>API Documentation</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                    line-height: 1.6;
                    margin: 0;
                    padding: 20px;
                    background: #f5f5f5;
                }
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                }
                .endpoint {
                    background: white;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 20px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .endpoint-header {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 15px;
                }
                .method {
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-weight: bold;
                    font-size: 14px;
                    text-transform: uppercase;
                }
                .method-get { background: #e3f2fd; color: #1976d2; }
                .method-post { background: #e8f5e9; color: #2e7d32; }
                .method-put { background: #fff3e0; color: #f57c00; }
                .method-delete { background: #ffebee; color: #c62828; }
                .url {
                    font-family: monospace;
                    font-size: 16px;
                    color: #333;
                }
                .description {
                    color: #666;
                    margin-bottom: 20px;
                }
                .content-wrapper {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    align-items: start;
                }
                .main-content {
                    flex: 1;
                }
                .parameters {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 6px;
                    border-left: 4px solid #1976d2;
                }
                .parameters h3 {
                    margin-top: 0;
                    color: #1976d2;
                    font-size: 18px;
                    margin-bottom: 15px;
                }
                .parameter-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                    font-size: 14px;
                }
                .parameter-table th, .parameter-table td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #e0e0e0;
                }
                .parameter-table th {
                    background: #e3f2fd;
                    font-weight: 600;
                    color: #1976d2;
                }
                .required {
                    color: #c62828;
                    font-size: 12px;
                    margin-left: 4px;
                }
                .response-section {
                    margin-top: 0;
                }
                .response-header {
                    font-weight: 600;
                    margin-bottom: 10px;
                    color: #333;
                }
                .response-code {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 6px;
                    font-family: monospace;
                    white-space: pre-wrap;
                    overflow-x: auto;
                    border: 1px solid #e0e0e0;
                    height: 100%;
                }
                .response-code pre {
                    margin: 0;
                    font-size: 14px;
                    line-height: 1.5;
                }
                .response-code code {
                    display: block;
                    padding: 10px;
                    background: #1e1e1e;
                    color: #d4d4d4;
                    border-radius: 4px;
                    overflow-x: auto;
                }
                .response-code .json-key { color: #9cdcfe; }
                .response-code .json-string { color: #ce9178; }
                .response-code .json-number { color: #b5cea8; }
                .response-code .json-boolean { color: #569cd6; }
                .response-code .json-null { color: #808080; }
                .parameters h4 {
                    color: #1976d2;
                    font-size: 16px;
                    margin: 0 0 10px 0;
                }
                .parameters h5 {
                    color: #666;
                    font-size: 14px;
                    margin: 15px 0 8px 0;
                }
                {$additionalStyles}
            </style>
        </head>
        <body>
            <div class="container">
                <h1>API Documentation</h1>
        HTML;

        foreach ($documentation as $endpointName => $endpoint) {
            $html .= <<<HTML
            <div class="service-section">
                <div class="service-header" onclick="toggleAccordion(this)">
                    <h2>{$endpointName}</h2>
                    <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="#1976d2" stroke-width="2">
                        <path d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="service-content">
                    <p class="description">{$endpoint['description']}</p>
                    <div class="endpoints">
            HTML;

            foreach ($endpoint['endpoints'] as $methodName => $method) {
                $methodClass = strtolower($method['method']);
                $html .= <<<HTML
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method method-{$methodClass}">{$method['method']}</span>
                        <span class="url">{$method['url']}</span>
                    </div>
                    <div class="content-wrapper">
                        <div class="main-content">
                            <div class="response-section">
                                <div class="response-code">
                                    <pre><code>{$this->formatJson($method['response'] ?? null)}</code></pre>
                                </div>
                            </div>
                        </div>
                        <div class="parameters">
                            <h3>Parameters</h3>
                            {$this->generateParameterTable($method)}
                        </div>
                    </div>
                </div>
                HTML;
            }

            $html .= <<<HTML
                    </div>
                </div>
            </div>
            HTML;
        }

        // Add JavaScript for accordion functionality
        $html .= <<<HTML
            </div>
            <script>
            function toggleAccordion(header) {
                const content = header.nextElementSibling;
                const isActive = header.classList.contains('active');
                
                // Close all accordions
                document.querySelectorAll('.service-header').forEach(h => {
                    h.classList.remove('active');
                    h.nextElementSibling.classList.remove('active');
                    h.nextElementSibling.style.display = 'none';
                });
                
                // Open clicked accordion if it wasn't active
                if (!isActive) {
                    header.classList.add('active');
                    content.classList.add('active');
                    content.style.display = 'block';
                }
            }

            // Open first accordion by default
            document.addEventListener('DOMContentLoaded', function() {
                const firstHeader = document.querySelector('.service-header');
                if (firstHeader) {
                    toggleAccordion(firstHeader);
                }
            });
            </script>
        </body>
        </html>
        HTML;

        return $html;
    }

    /**
     * @param array{method: string, url: string, description: string, parameters?: array<string, array{type: string, required: bool}>, query_parameters?: array<string, array<string, array{type: string, required: bool}>>, response?: string|false} $method
     */
    private function generateParameterTable(array $method): string
    {
        $hasParams = isset($method['parameters']) && !empty($method['parameters']);
        $hasQueryParams = isset($method['query_parameters']) && !empty($method['query_parameters']);

        if (!$hasParams && !$hasQueryParams) {
            return '<p>No parameters required</p>';
        }

        $html = '';

        // Regular Parameters
        if ($hasParams && isset($method['parameters'])) {
            $html .= '<h4>Body Parameters</h4>';
            $html .= '<table class="parameter-table">';
            $html .= '<tr><th>Parameter</th><th>Type</th><th>Required</th></tr>';
            
            foreach ($method['parameters'] as $name => $param) {
                $required = $param['required'] ? '<span class="required">*</span>' : '';
                $html .= sprintf(
                    '<tr><td>%s%s</td><td>%s</td><td>%s</td></tr>',
                    htmlspecialchars($name),
                    $required,
                    htmlspecialchars($param['type']),
                    $param['required'] ? 'Yes' : 'No'
                );
            }
            
            $html .= '</table>';
        }

        // Query Parameters
        if ($hasQueryParams) {
            if ($hasParams) {
                $html .= '<div style="margin-top: 20px;"></div>';
            }
            $html .= '<h4>Query Parameters</h4>';
            
            // Handle filters
            if (!empty($method['query_parameters']['filters'])) {
                $html .= '<h5>Filters</h5>';
                $html .= $this->generateSubParameterTable($method['query_parameters']['filters']);
            }

            // Handle sort
            if (!empty($method['query_parameters']['sort'])) {
                $html .= '<h5>Sort</h5>';
                $html .= $this->generateSubParameterTable($method['query_parameters']['sort']);
            }

            // Handle search
            if (!empty($method['query_parameters']['search'])) {
                $html .= '<h5>Search</h5>';
                $html .= $this->generateSubParameterTable($method['query_parameters']['search']);
            }
        }

        return $html;
    }

    /**
     * @param array<string, array{type: string, required: bool}> $params
     */
    private function generateSubParameterTable(array $params): string
    {
        $html = '<table class="parameter-table">';
        $html .= '<tr><th>Parameter</th><th>Type</th><th>Required</th></tr>';
        
        foreach ($params as $name => $param) {
            $required = $param['required'] ? '<span class="required">*</span>' : '';
            $html .= sprintf(
                '<tr><td>%s%s</td><td>%s</td><td>%s</td></tr>',
                htmlspecialchars($name),
                $required,
                htmlspecialchars($param['type']),
                $param['required'] ? 'Yes' : 'No'
            );
        }
        
        $html .= '</table>';
        return $html;
    }

    /**
     * @param string|false|null $json
     */
    private function formatJson($json): string
    {
        if (empty($json)) {
            return 'No example response available';
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return (string)$json; // Cast to string to ensure type safety
        }

        // Remove any markdown code block markers and "Example Response:" text
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $json = (string)$json; // Cast to string to ensure type safety
        $json = str_replace(['```json', '```', 'Example Response:'], '', $json);
        return trim($json);
    }

    public function show(): JsonResponse
    {
        $generator = new ApiDocGenerator();
        $documentation = $generator->generate();
        return Response::success($documentation);
    }

    public function html(): never
    {
        $generator = new ApiDocGenerator();
        $documentation = $generator->generate();
        header('Content-Type: text/html');
        // Generate HTML view of the documentation
        echo $this->generateHtmlView($documentation);
        die();
    }
} 
