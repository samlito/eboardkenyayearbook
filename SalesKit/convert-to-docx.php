<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\ListItem;

class MarkdownToDocx
{
    private PhpWord $phpWord;
    private $section;
    private string $title;

    private array $colors = [
        'primary'   => '1B4F72',
        'secondary' => '2E86C1',
        'accent'    => '148F77',
        'dark'      => '2C3E50',
        'text'      => '333333',
        'lightbg'   => 'EBF5FB',
        'border'    => 'AED6F1',
        'taggreen'  => '27AE60',
        'tagblue'   => '2980B9',
        'tagorange' => 'E67E22',
    ];

    public function __construct()
    {
        $this->phpWord = new PhpWord();
        $this->setupStyles();
    }

    private function setupStyles(): void
    {
        $this->phpWord->setDefaultFontName('Calibri');
        $this->phpWord->setDefaultFontSize(11);

        $this->phpWord->addTitleStyle(1, [
            'bold' => true, 'size' => 22, 'color' => $this->colors['primary'],
            'spaceAfter' => 120,
        ], ['spaceBefore' => 240, 'spaceAfter' => 120]);

        $this->phpWord->addTitleStyle(2, [
            'bold' => true, 'size' => 16, 'color' => $this->colors['primary'],
        ], ['spaceBefore' => 240, 'spaceAfter' => 80, 'borderBottomSize' => 6, 'borderBottomColor' => $this->colors['border']]);

        $this->phpWord->addTitleStyle(3, [
            'bold' => true, 'size' => 13, 'color' => $this->colors['secondary'],
        ], ['spaceBefore' => 200, 'spaceAfter' => 60]);

        $this->phpWord->addTitleStyle(4, [
            'bold' => true, 'size' => 11, 'color' => $this->colors['dark'],
        ], ['spaceBefore' => 160, 'spaceAfter' => 40]);

        $this->phpWord->addParagraphStyle('Normal', [
            'spaceAfter' => 80, 'lineHeight' => 1.15,
        ]);

        $this->phpWord->addParagraphStyle('Quote', [
            'indentation' => ['left' => 480],
            'spaceAfter' => 80,
            'borderLeftSize' => 18,
            'borderLeftColor' => $this->colors['secondary'],
        ]);

        $this->phpWord->addParagraphStyle('ListBullet', [
            'spaceAfter' => 40, 'lineHeight' => 1.15,
            'indentation' => ['left' => 360, 'hanging' => 360],
        ]);
    }

    public function convert(string $mdFile, string $docxFile): void
    {
        $content = file_get_contents($mdFile);
        $lines = explode("\n", $content);

        $this->section = $this->phpWord->addSection([
            'marginTop'    => 1134,
            'marginBottom' => 1134,
            'marginLeft'   => 1134,
            'marginRight'  => 1134,
        ]);

        $this->addHeader();

        $inTable = false;
        $tableRows = [];
        $inCodeBlock = false;
        $codeLines = [];

        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];

            if (preg_match('/^```/', $line)) {
                if ($inCodeBlock) {
                    $this->addCodeBlock(implode("\n", $codeLines));
                    $codeLines = [];
                    $inCodeBlock = false;
                } else {
                    $inCodeBlock = true;
                }
                continue;
            }

            if ($inCodeBlock) {
                $codeLines[] = $line;
                continue;
            }

            if (preg_match('/^\|(.+)\|/', $line)) {
                if (preg_match('/^\|[\s\-\|:]+\|$/', $line)) {
                    continue;
                }
                $tableRows[] = $line;
                $nextLine = $lines[$i + 1] ?? '';
                $nextNextLine = $lines[$i + 2] ?? '';
                if (!preg_match('/^\|/', $nextLine) && !preg_match('/^\|[\s\-\|:]+\|$/', $nextLine)) {
                    $this->addTable($tableRows);
                    $tableRows = [];
                } elseif (preg_match('/^\|[\s\-\|:]+\|$/', $nextLine) && !preg_match('/^\|/', $nextNextLine)) {
                    $this->addTable($tableRows);
                    $tableRows = [];
                }
                continue;
            } else {
                if (!empty($tableRows)) {
                    $this->addTable($tableRows);
                    $tableRows = [];
                }
            }

            if (preg_match('/^(#{1,4})\s+(.+)/', $line, $m)) {
                $level = strlen($m[1]);
                $text = trim($m[2]);
                if ($level === 1 && !isset($this->title)) {
                    $this->title = $text;
                }
                $this->section->addTitle($this->cleanInline($text), $level);
                continue;
            }

            if (preg_match('/^>\s*(.*)/', $line, $m)) {
                $quoteText = trim($m[1]);
                $textRun = $this->section->addTextRun('Quote');
                $this->addFormattedText($textRun, $quoteText, ['italic' => true, 'color' => '555555']);
                continue;
            }

            if (preg_match('/^(\s*)[-*]\s+(.+)/', $line, $m)) {
                $depth = (int)(strlen($m[1]) / 2);
                $text = trim($m[2]);
                $textRun = $this->section->addListItemRun($depth, null, 'ListBullet');
                $this->addFormattedText($textRun, $text);
                continue;
            }

            if (preg_match('/^(\s*)\d+\.\s+(.+)/', $line, $m)) {
                $depth = (int)(strlen($m[1]) / 2);
                $text = trim($m[2]);
                $listStyle = ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER];
                $textRun = $this->section->addListItemRun($depth, $listStyle, 'ListBullet');
                $this->addFormattedText($textRun, $text);
                continue;
            }

            if (preg_match('/^---+$/', $line)) {
                continue;
            }

            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            $textRun = $this->section->addTextRun('Normal');
            $this->addFormattedText($textRun, $trimmed);
        }

        if (!empty($tableRows)) {
            $this->addTable($tableRows);
        }

        $this->addFooter();

        $writer = IOFactory::createWriter($this->phpWord, 'Word2007');
        $writer->save($docxFile);
    }

    private function addHeader(): void
    {
        $header = $this->section->addHeader();
        $header->addText('E-Board by Losvinca — Digital Board Governance Platform', [
            'size' => 8, 'color' => $this->colors['secondary'], 'italic' => true,
        ], ['alignment' => Jc::RIGHT]);
    }

    private function addFooter(): void
    {
        $footer = $this->section->addFooter();
        $footer->addPreserveText('CONFIDENTIAL | Losvinca E-Board Sales Kit | Page {PAGE} of {NUMPAGES}', [
            'size' => 8, 'color' => '999999',
        ], ['alignment' => Jc::CENTER]);
    }

    private function addTable(array $rows): void
    {
        if (empty($rows)) return;

        $tableStyle = [
            'borderSize'  => 4,
            'borderColor' => 'CCCCCC',
            'cellMargin'  => 60,
        ];

        $table = $this->section->addTable($tableStyle);

        foreach ($rows as $idx => $row) {
            $cells = array_map('trim', explode('|', trim($row, '|')));
            $tableRow = $table->addRow();

            foreach ($cells as $cell) {
                $cellStyle = [];
                $fontStyle = ['size' => 9, 'color' => $this->colors['text']];

                if ($idx === 0) {
                    $cellStyle['bgColor'] = $this->colors['primary'];
                    $fontStyle = ['bold' => true, 'size' => 9, 'color' => 'FFFFFF'];
                } elseif ($idx % 2 === 0) {
                    $cellStyle['bgColor'] = 'F8F9FA';
                }

                $cellStyle['valign'] = 'top';
                $cellObj = $tableRow->addCell(null, $cellStyle);
                $cellText = $this->cleanInline($cell);
                $cellObj->addText($cellText, $fontStyle, ['spaceAfter' => 20]);
            }
        }

        $this->section->addTextBreak(0);
    }

    private function addCodeBlock(string $code): void
    {
        $textRun = $this->section->addTextRun([
            'spaceAfter' => 80,
            'shading'    => ['fill' => 'F4F4F4'],
            'indentation' => ['left' => 240],
        ]);
        $textRun->addText(htmlspecialchars($code), [
            'name' => 'Consolas', 'size' => 9, 'color' => $this->colors['dark'],
        ]);
    }

    private function addFormattedText($textRun, string $text, array $baseStyle = []): void
    {
        $defaultStyle = array_merge([
            'size' => 11,
            'color' => $this->colors['text'],
        ], $baseStyle);

        $pattern = '/(\*\*\*(.+?)\*\*\*|\*\*(.+?)\*\*|\*(.+?)\*|`(.+?)`|\[READY TO USE\]|\[REQUIRES LOSVINCA INPUT\]|\[CURRENT PRODUCT CAPABILITY\]|\[FUTURE ENHANCEMENT\]|\[NOT CURRENTLY AVAILABLE[^\]]*\])/';

        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($parts as $part) {
            if ($part === '') continue;

            if (preg_match('/^\*\*\*(.+)\*\*\*$/', $part, $m)) {
                $style = array_merge($defaultStyle, ['bold' => true, 'italic' => true]);
                $textRun->addText($this->cleanInline($m[1]), $style);
            } elseif (preg_match('/^\*\*(.+)\*\*$/', $part, $m)) {
                $style = array_merge($defaultStyle, ['bold' => true, 'color' => $this->colors['dark']]);
                $textRun->addText($this->cleanInline($m[1]), $style);
            } elseif (preg_match('/^\*(.+)\*$/', $part, $m)) {
                $style = array_merge($defaultStyle, ['italic' => true]);
                $textRun->addText($this->cleanInline($m[1]), $style);
            } elseif (preg_match('/^`(.+)`$/', $part, $m)) {
                $textRun->addText($m[1], [
                    'name' => 'Consolas', 'size' => 9, 'color' => $this->colors['dark'],
                    'shading' => ['fill' => 'F0F0F0'],
                ]);
            } elseif ($part === '[READY TO USE]') {
                $textRun->addText(' READY TO USE ', [
                    'bold' => true, 'size' => 8, 'color' => 'FFFFFF',
                    'shading' => ['fill' => $this->colors['taggreen']],
                ]);
            } elseif ($part === '[REQUIRES LOSVINCA INPUT]') {
                $textRun->addText(' REQUIRES LOSVINCA INPUT ', [
                    'bold' => true, 'size' => 8, 'color' => 'FFFFFF',
                    'shading' => ['fill' => $this->colors['tagorange']],
                ]);
            } elseif ($part === '[CURRENT PRODUCT CAPABILITY]') {
                $textRun->addText(' CURRENT PRODUCT CAPABILITY ', [
                    'bold' => true, 'size' => 8, 'color' => 'FFFFFF',
                    'shading' => ['fill' => $this->colors['tagblue']],
                ]);
            } elseif (preg_match('/^\[FUTURE ENHANCEMENT\]$/', $part) || preg_match('/^\[NOT CURRENTLY AVAILABLE/', $part)) {
                $textRun->addText(' ' . trim($part, '[]') . ' ', [
                    'bold' => true, 'size' => 8, 'color' => 'FFFFFF',
                    'shading' => ['fill' => 'E74C3C'],
                ]);
            } else {
                $textRun->addText($this->cleanInline($part), $defaultStyle);
            }
        }
    }

    private function cleanInline(string $text): string
    {
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);
        $text = str_replace(['&amp;', '&lt;', '&gt;', '&mdash;', '&ndash;', '&rarr;'], ['&', '<', '>', '—', '–', '→'], $text);
        return $text;
    }
}

$salesKitDir = __DIR__;
$outputDir = $salesKitDir . DIRECTORY_SEPARATOR . 'docx';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$files = glob($salesKitDir . DIRECTORY_SEPARATOR . '*.md');
sort($files);

$converted = 0;
$errors = [];

foreach ($files as $mdFile) {
    $basename = basename($mdFile, '.md');

    if ($basename === 'README' || $basename === 'convert-to-docx') {
        continue;
    }

    $docxFile = $outputDir . DIRECTORY_SEPARATOR . $basename . '.docx';

    echo "Converting: {$basename}.md → {$basename}.docx ... ";

    try {
        $converter = new MarkdownToDocx();
        $converter->convert($mdFile, $docxFile);
        echo "OK\n";
        $converted++;
    } catch (\Exception $e) {
        $msg = $e->getMessage();
        echo "ERROR: {$msg}\n";
        $errors[] = "{$basename}: {$msg}";
    }
}

echo "\n";
echo "Converted: {$converted} files\n";
echo "Output: {$outputDir}\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $err) {
        echo "  - {$err}\n";
    }
}
