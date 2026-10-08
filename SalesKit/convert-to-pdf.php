<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

$env = new Environment([]);
$env->addExtension(new CommonMarkCoreExtension());
$env->addExtension(new TableExtension());
$converter = new MarkdownConverter($env);

$css = <<<'CSS'
@page {
    margin: 60px 50px 70px 50px;
}
body {
    font-family: 'Helvetica', 'Arial', sans-serif;
    font-size: 10pt;
    line-height: 1.5;
    color: #2C3E50;
    margin: 0;
    padding: 0;
}
/* Header bar */
.header-bar {
    background-color: #1B4F72;
    color: #FFFFFF;
    padding: 18px 24px;
    margin: -10px -0px 20px -0px;
    text-align: center;
}
.header-bar h1 {
    margin: 0;
    font-size: 20pt;
    color: #FFFFFF;
    border: none;
    padding: 0;
    letter-spacing: 0.5px;
}
.header-bar .subtitle {
    font-size: 9pt;
    color: #AED6F1;
    margin-top: 4px;
    letter-spacing: 1px;
    text-transform: uppercase;
}
h1 {
    font-size: 18pt;
    color: #1B4F72;
    border-bottom: 2px solid #AED6F1;
    padding-bottom: 6px;
    margin-top: 28px;
    margin-bottom: 12px;
    page-break-after: avoid;
}
h2 {
    font-size: 14pt;
    color: #1B4F72;
    border-bottom: 1px solid #D4E6F1;
    padding-bottom: 4px;
    margin-top: 24px;
    margin-bottom: 10px;
    page-break-after: avoid;
}
h3 {
    font-size: 12pt;
    color: #2E86C1;
    margin-top: 18px;
    margin-bottom: 8px;
    page-break-after: avoid;
}
h4 {
    font-size: 10.5pt;
    color: #2C3E50;
    margin-top: 14px;
    margin-bottom: 6px;
    font-weight: bold;
    page-break-after: avoid;
}
p {
    margin-bottom: 8px;
    text-align: justify;
}
strong {
    color: #1B4F72;
}
em {
    color: #555;
}
blockquote {
    border-left: 3px solid #2E86C1;
    margin: 12px 0;
    padding: 8px 16px;
    background: #EBF5FB;
    color: #2C3E50;
    font-style: italic;
}
blockquote p {
    margin: 0;
}
ul, ol {
    margin: 6px 0 10px 0;
    padding-left: 24px;
}
li {
    margin-bottom: 4px;
}
li > ul, li > ol {
    margin-top: 3px;
    margin-bottom: 3px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin: 12px 0 16px 0;
    font-size: 9pt;
    page-break-inside: auto;
}
thead {
    display: table-header-group;
}
tr {
    page-break-inside: avoid;
}
th {
    background-color: #1B4F72;
    color: #FFFFFF;
    font-weight: bold;
    text-align: left;
    padding: 7px 8px;
    border: 1px solid #1B4F72;
    font-size: 8.5pt;
}
td {
    padding: 6px 8px;
    border: 1px solid #D5D8DC;
    vertical-align: top;
}
tr:nth-child(even) td {
    background-color: #F8F9FA;
}
tr:nth-child(odd) td {
    background-color: #FFFFFF;
}
code {
    font-family: 'Courier New', monospace;
    font-size: 8.5pt;
    background: #F0F0F0;
    padding: 1px 4px;
    border-radius: 2px;
    color: #C0392B;
}
pre {
    background: #F4F4F4;
    border: 1px solid #DDD;
    padding: 10px 14px;
    font-family: 'Courier New', monospace;
    font-size: 8.5pt;
    overflow-x: auto;
    margin: 10px 0;
    line-height: 1.4;
}
pre code {
    background: none;
    padding: 0;
    color: #2C3E50;
}
hr {
    border: none;
    border-top: 1px solid #D4E6F1;
    margin: 20px 0;
}
/* Tags */
.tag-ready {
    display: inline;
    background: #27AE60;
    color: #FFF;
    font-size: 7pt;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 2px;
    letter-spacing: 0.3px;
}
.tag-input {
    display: inline;
    background: #E67E22;
    color: #FFF;
    font-size: 7pt;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 2px;
    letter-spacing: 0.3px;
}
.tag-capability {
    display: inline;
    background: #2980B9;
    color: #FFF;
    font-size: 7pt;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 2px;
    letter-spacing: 0.3px;
}
.tag-future {
    display: inline;
    background: #E74C3C;
    color: #FFF;
    font-size: 7pt;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 2px;
    letter-spacing: 0.3px;
}
.tag-config {
    display: inline;
    background: #8E44AD;
    color: #FFF;
    font-size: 7pt;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 2px;
    letter-spacing: 0.3px;
}
.footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 7.5pt;
    color: #999;
    border-top: 1px solid #EEE;
    padding-top: 6px;
}
.page-number:after {
    content: counter(page);
}
CSS;

function addTags(string $html): string
{
    $replacements = [
        '[READY TO USE]' => '<span class="tag-ready">READY TO USE</span>',
        '[REQUIRES LOSVINCA INPUT]' => '<span class="tag-input">REQUIRES LOSVINCA INPUT</span>',
        '[CURRENT PRODUCT CAPABILITY]' => '<span class="tag-capability">CURRENT PRODUCT CAPABILITY</span>',
        '[FUTURE ENHANCEMENT]' => '<span class="tag-future">FUTURE ENHANCEMENT</span>',
    ];
    $html = str_replace(array_keys($replacements), array_values($replacements), $html);
    $html = preg_replace(
        '/\[NOT CURRENTLY AVAILABLE[^\]]*\]/',
        '<span class="tag-future">NOT CURRENTLY AVAILABLE — POTENTIAL ENHANCEMENT</span>',
        $html
    );
    $html = preg_replace(
        '/\[AVAILABLE\s*—\s*CONFIGURATION REQUIRED\]/',
        '<span class="tag-config">AVAILABLE — CONFIGURATION REQUIRED</span>',
        $html
    );
    return $html;
}

function extractTitle(string $markdown): string
{
    if (preg_match('/^#\s+(.+)/m', $markdown, $m)) {
        return trim($m[1]);
    }
    return 'E-Board Sales Kit';
}

function removeFirstH1(string $html): string
{
    return preg_replace('/<h1>.*?<\/h1>/', '', $html, 1);
}

$titles = [
    '00' => 'Product Capability Audit',
    '01' => 'Product Positioning',
    '02' => 'Target Market Profiles',
    '03' => 'Buyer Personas',
    '04' => 'Core Value Proposition',
    '05' => 'Competitive Positioning',
    '06' => 'Sales Brochure',
    '07' => 'Corporation Secretary One-Pager',
    '08' => 'CEO One-Pager',
    '09' => 'ICT Director One-Pager',
    '10' => '15-Minute Demo Script',
    '11' => 'Discovery Questions',
    '12' => 'Objection Handling',
    '13' => 'Pilot Program',
    '14' => 'Pricing Strategy',
    '15' => 'Government Procurement Strategy',
    '16' => 'Sales Outreach Templates',
    '17' => 'Partner Strategy',
    '18' => 'Webinar Strategy',
    '19' => 'LinkedIn Content Plan',
    '20' => 'Case Study Template',
    '21' => 'Sales Presentation',
    '22' => 'Sales Funnel',
    '23' => '90-Day Sales Plan',
    '24' => 'Sales KPIs',
    '25' => 'Sales Assets Checklist',
    '26' => 'Final Commercial Positioning',
];

$partLabels = [
    '00' => 'FOUNDATION',
    '01' => 'PART 1',
    '02' => 'PART 2',
    '03' => 'PART 3',
    '04' => 'PART 4',
    '05' => 'PART 5',
    '06' => 'PART 6',
    '07' => 'PART 7',
    '08' => 'PART 8',
    '09' => 'PART 9',
    '10' => 'PART 10',
    '11' => 'PART 11',
    '12' => 'PART 12',
    '13' => 'PART 13',
    '14' => 'PART 14',
    '15' => 'PART 15',
    '16' => 'PART 16',
    '17' => 'PART 17',
    '18' => 'PART 18',
    '19' => 'PART 19',
    '20' => 'PART 20',
    '21' => 'PART 21',
    '22' => 'PART 22',
    '23' => 'PART 23',
    '24' => 'PART 24',
    '25' => 'PART 25',
    '26' => 'PART 26',
];

$salesKitDir = __DIR__;
$outputDir = $salesKitDir . DIRECTORY_SEPARATOR . 'pdf';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$files = glob($salesKitDir . DIRECTORY_SEPARATOR . '*.md');
sort($files);

$converted = 0;
$errors = [];

foreach ($files as $mdFile) {
    $basename = basename($mdFile, '.md');

    if (in_array($basename, ['README', 'convert-to-docx', 'convert-to-pdf'])) {
        continue;
    }

    $prefix = substr($basename, 0, 2);
    $title = $titles[$prefix] ?? extractTitle(file_get_contents($mdFile));
    $partLabel = $partLabels[$prefix] ?? '';

    $pdfFile = $outputDir . DIRECTORY_SEPARATOR . $basename . '.pdf';

    echo "Converting: {$basename}.md → PDF ... ";

    try {
        $markdown = file_get_contents($mdFile);
        $htmlBody = $converter->convert($markdown)->getContent();
        $htmlBody = removeFirstH1($htmlBody);
        $htmlBody = addTags($htmlBody);

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>{$css}</style>
</head>
<body>
<div class="header-bar">
    <div class="subtitle">{$partLabel} — E-BOARD GOVERNMENT &amp; INSTITUTIONAL SALES KIT</div>
    <h1>{$title}</h1>
    <div class="subtitle">LOSVINCA — DIGITAL BOARD GOVERNANCE PLATFORM</div>
</div>

{$htmlBody}

<div class="footer">
    CONFIDENTIAL — Losvinca E-Board Sales Kit — August 2026 — Page <span class="page-number"></span>
</div>
</body>
</html>
HTML;

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultMediaType', 'print');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        file_put_contents($pdfFile, $dompdf->output());

        $pages = $dompdf->getCanvas()->get_page_count();
        $sizeKB = round(filesize($pdfFile) / 1024);
        echo "OK ({$pages} pages, {$sizeKB} KB)\n";
        $converted++;
    } catch (\Exception $e) {
        $msg = $e->getMessage();
        echo "ERROR: {$msg}\n";
        $errors[] = "{$basename}: {$msg}";
    }
}

echo "\n========================================\n";
echo "Converted: {$converted} files\n";
echo "Output:    {$outputDir}\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $err) {
        echo "  - {$err}\n";
    }
}

echo "\nDone.\n";
