<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Slide\Background\Color as BgColor;
use PhpOffice\PhpPresentation\DocumentLayout;

$pptx = new PhpPresentation();

// Standard PowerPoint size: 10" x 7.5" (in EMU: 1 inch = 914400 EMU)
$pptx->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_4X3);

$pptx->getDocumentProperties()
    ->setCreator('ICT Authority Kenya')
    ->setCompany('ICT Authority')
    ->setTitle('E-Board Digital Board Governance Platform')
    ->setSubject('Digital Board Governance Solution for Government Institutions')
    ->setDescription('E-Board: A digital governance platform by ICT Authority Kenya for managing board operations in government institutions');

// ICTA Brand Colors (Kenya national colors)
$ictaGreen  = 'FF01A64E';
$deepGreen  = 'FF005A2A';
$darkGreen  = 'FF018A3F';
$red        = 'FFCC0000';
$gold       = 'FFE8A23A';
$black      = 'FF000000';
$darkText   = 'FF1A1A1A';
$white      = 'FFFFFFFF';
$lightBg    = 'FFFAFAF7';
$medBg      = 'FFEEEFEB';
$muted      = 'FF4D4D4D';
$ltGreen    = 'FFB8E8CC';

function slideBg($slide, $color) {
    $bg = new BgColor();
    $bg->setColor(new Color($color));
    $slide->setBackground($bg);
}

function addText($slide, $x, $y, $w, $h, $text, $size = 14, $color = 'FFFFFFFF', $bold = false, $align = Alignment::HORIZONTAL_LEFT) {
    $shape = $slide->createRichTextShape()
        ->setOffsetX($x)->setOffsetY($y)
        ->setWidth($w)->setHeight($h);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal($align);
    $textRun = $shape->createTextRun($text);
    $textRun->getFont()
        ->setName('Calibri')
        ->setSize($size)
        ->setColor(new Color($color))
        ->setBold($bold);
    return $shape;
}

function addMultiText($slide, $x, $y, $w, $h, $lines, $align = Alignment::HORIZONTAL_LEFT) {
    $shape = $slide->createRichTextShape()
        ->setOffsetX($x)->setOffsetY($y)
        ->setWidth($w)->setHeight($h);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal($align);
    foreach ($lines as $i => $line) {
        if ($i > 0) $shape->createBreak();
        $para = $i === 0 ? $shape->getActiveParagraph() : $shape->createParagraph();
        $para->getAlignment()->setHorizontal($align);
        $run = $para->createTextRun($line[0]);
        $run->getFont()->setName('Calibri')->setSize($line[1])->setColor(new Color($line[2]))->setBold($line[3] ?? false);
    }
    return $shape;
}

function addBullets($slide, $x, $y, $w, $h, $items, $size = 13, $color = 'FFFFFFFF') {
    $shape = $slide->createRichTextShape()
        ->setOffsetX($x)->setOffsetY($y)
        ->setWidth($w)->setHeight($h);
    foreach ($items as $i => $item) {
        if ($i > 0) $shape->createBreak();
        $para = $i === 0 ? $shape->getActiveParagraph() : $shape->createParagraph();
        $para->getAlignment()->setMarginLeft(20)->setIndent(-20);
        $run = $para->createTextRun("•  " . $item);
        $run->getFont()->setName('Calibri')->setSize($size)->setColor(new Color($color));
    }
    return $shape;
}

function addCard($slide, $x, $y, $w, $h, $title, $body, $onDark = true) {
    $cardBg     = $onDark ? 'FF003D1A' : 'FFFFFFFF';
    $borderClr  = $onDark ? 'FF1A5A3A' : 'FFDDE5DD';
    $titleColor = $onDark ? 'FFFFFFFF' : 'FF1A1A1A';
    $bodyColor  = $onDark ? 'FFB8E8CC' : 'FF4D4D4D';

    $shape = $slide->createRichTextShape()
        ->setOffsetX($x)->setOffsetY($y)
        ->setWidth($w)->setHeight($h);
    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($cardBg));
    $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setColor(new Color($borderClr))->setLineWidth(1);
    $shape->setInsetLeft(12)->setInsetTop(10)->setInsetRight(12)->setInsetBottom(10);

    $tRun = $shape->createTextRun($title);
    $tRun->getFont()->setName('Calibri')->setSize(12)->setBold(true)->setColor(new Color($titleColor));
    if ($body !== '') {
        $shape->createBreak();
        $bRun = $shape->createTextRun($body);
        $bRun->getFont()->setName('Calibri')->setSize(10)->setColor(new Color($bodyColor));
    }
    return $shape;
}

function addAccentBar($slide, $x, $y, $w, $h, $color) {
    $shape = $slide->createRichTextShape()
        ->setOffsetX($x)->setOffsetY($y)
        ->setWidth($w)->setHeight($h);
    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($color));
    return $shape;
}

// ════════════════════════════════════════════════════════════════
// SLIDE 1: COVER
// ════════════════════════════════════════════════════════════════
$slide = $pptx->getActiveSlide();
slideBg($slide, $deepGreen);
addAccentBar($slide, 0, 0, 916, 6, $gold);
addAccentBar($slide, 0, 480, 916, 6, $red);

addText($slide, 60, 30, 800, 30, 'ICT AUTHORITY KENYA', 11, $gold, true, Alignment::HORIZONTAL_CENTER);
addText($slide, 60, 80, 800, 60, 'E-BOARD', 48, $white, true, Alignment::HORIZONTAL_CENTER);
addText($slide, 60, 155, 800, 60, 'Digital Board Governance Platform', 28, $white, true, Alignment::HORIZONTAL_CENTER);

addText($slide, 60, 250, 800, 50, "A whole-of-government solution for managing board operations,\nmeeting workflows, and institutional accountability", 14, $ltGreen, false, Alignment::HORIZONTAL_CENTER);

addMultiText($slide, 60, 370, 800, 80, [
    ['Ministry of Information, Communications & The Digital Economy', 10, $ltGreen],
    ['ICT Authority  |  www.icta.go.ke', 10, $gold, true],
], Alignment::HORIZONTAL_CENTER);

// ════════════════════════════════════════════════════════════════
// SLIDE 2: ABOUT ICT AUTHORITY
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $white);
addAccentBar($slide, 0, 0, 916, 5, $ictaGreen);

addText($slide, 50, 20, 300, 25, 'ABOUT US', 9, $ictaGreen, true);
addText($slide, 50, 50, 820, 45, 'ICT Authority Kenya', 28, $darkText, true);
addText($slide, 50, 100, 780, 45, 'A State Corporation under the Ministry of Information, Communications & The Digital Economy, established in August 2013.', 13, $muted);

addCard($slide, 50,  160, 400, 60, 'Vision', '"A digitally empowered society."', false);
addCard($slide, 470, 160, 400, 60, 'Mission', '"To digitally empower society through the provision of quality ICT services, implementation of industry best practices, and by leveraging technology."', false);

addText($slide, 50, 240, 820, 25, 'Formed through the merger of:', 12, $darkText, true);
addBullets($slide, 50, 268, 400, 80, [
    'Kenya ICT Board',
    'Government IT Services (GITS)',
    'e-Government Directorate',
], 11, $muted);

addText($slide, 470, 240, 400, 25, 'Core Values:', 12, $darkText, true);
addBullets($slide, 470, 268, 400, 80, [
    'Innovation',
    'Citizen-Focus',
    'Transparency',
    'Agility',
], 11, $muted);

addText($slide, 50, 365, 820, 30, 'Guided by the National Digital Masterplan 2022–2032 and Strategic Plan 2024–2027', 11, $ictaGreen, true, Alignment::HORIZONTAL_CENTER);

// ════════════════════════════════════════════════════════════════
// SLIDE 3: ICTA MANDATE & FUNCTIONS
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $deepGreen);
addAccentBar($slide, 0, 0, 916, 5, $gold);

addText($slide, 50, 18, 300, 25, 'OUR MANDATE', 9, $gold, true);
addText($slide, 50, 48, 820, 45, 'Driving Digital Transformation in Government', 24, $white, true);

addCard($slide, 50,  110, 260, 90, 'ICT Standards', 'Setting and enforcing ICT standards for all public-sector entities across national and county government');
addCard($slide, 330, 110, 260, 90, 'e-Government Services', 'Championing the rollout of e-Government services and driving digital service delivery to citizens');
addCard($slide, 610, 110, 260, 90, 'ICT Infrastructure', 'Establishing and maintaining secure national ICT infrastructure including NOFBI backbone');

addCard($slide, 50,  215, 260, 90, 'Digital Literacy', 'Promoting ICT literacy and digital skills development across government and public institutions');
addCard($slide, 330, 215, 260, 90, 'Critical ICT Projects', 'Overseeing and supervising critical government ICT projects and digital transformation initiatives');
addCard($slide, 610, 215, 260, 90, 'eGov Stack', 'Operating the integrated whole-of-government platform hosting eCitizen and digital government services');

addText($slide, 50, 330, 820, 50, "E-Board is part of ICTA's mission to digitize government operations.\nBoard governance is a critical function in every state corporation and public institution.", 12, $ltGreen, false, Alignment::HORIZONTAL_CENTER);

// ════════════════════════════════════════════════════════════════
// SLIDE 4: THE GOVERNANCE CHALLENGE
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $white);
addAccentBar($slide, 0, 0, 916, 5, $ictaGreen);

addText($slide, 50, 18, 300, 25, 'THE CHALLENGE', 9, $ictaGreen, true);
addText($slide, 50, 48, 830, 50, "Board Governance in Government\nInstitutions Is Still Largely Manual", 24, $darkText, true);
addText($slide, 50, 108, 780, 35, 'Most state corporations and public institutions manage board operations through fragmented, manual processes that create governance risks.', 12, $muted);

addCard($slide, 50,  160, 200, 110, 'Email-Based Distribution', 'Board papers circulated as email attachments with no version control or access tracking', false);
addCard($slide, 265, 160, 200, 110, 'Physical Board Packs', 'Printed documents manually assembled, distributed, and often misplaced or leaked', false);
addCard($slide, 480, 160, 200, 110, 'WhatsApp Groups', 'Board discussions on personal messaging apps with no audit trail or institutional control', false);
addCard($slide, 695, 160, 200, 110, 'Scattered Records', 'Minutes, resolutions, and action items tracked in spreadsheets or filing cabinets', false);

addText($slide, 50, 290, 820, 80, "These challenges affect governance, compliance, and accountability across\nhundreds of government boards, state corporations, and public institutions in Kenya.", 12, $red, false, Alignment::HORIZONTAL_CENTER);

// ════════════════════════════════════════════════════════════════
// SLIDE 5: THE COST
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $deepGreen);
addAccentBar($slide, 0, 0, 916, 5, $red);

addText($slide, 50, 18, 300, 25, 'WHY IT MATTERS', 9, $gold, true);
addText($slide, 50, 48, 830, 40, 'The Cost of Fragmented Board Processes', 24, $white, true);

$stats = [['0%', 'Audit Trail on Email'], ['72hrs', 'Paper Distribution Time'], ['40%', 'Resolutions Not Tracked'], ['0', 'Version Control']];
foreach ($stats as $i => $s) {
    $sx = 50 + $i * 215;
    addText($slide, $sx, 105, 200, 50, $s[0], 36, $gold, true, Alignment::HORIZONTAL_CENTER);
    addText($slide, $sx, 155, 200, 25, $s[1], 9, $ltGreen, true, Alignment::HORIZONTAL_CENTER);
}

addCard($slide, 50,  200, 410, 95, 'Governance Risk', 'No centralized record of board decisions, conflict declarations, or attendance. Audit queries go unanswered. Mwongozo compliance becomes aspirational rather than operational.');
addCard($slide, 480, 200, 410, 95, 'Operational Inefficiency', 'Corporation Secretaries spend days assembling board packs. CEOs review papers without structured workflows. Follow-up on resolutions depends on personal memory.');

addText($slide, 50, 320, 820, 40, 'These inefficiencies are addressable through a purpose-built digital governance platform.', 12, $ltGreen, false, Alignment::HORIZONTAL_CENTER);

// ════════════════════════════════════════════════════════════════
// SLIDE 6: INTRODUCING E-BOARD
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $deepGreen);
addAccentBar($slide, 0, 0, 916, 5, $gold);

addText($slide, 50, 18, 300, 25, 'THE SOLUTION', 9, $gold, true);
addText($slide, 50, 55, 830, 55, 'E-Board', 42, $white, true);
addText($slide, 50, 120, 750, 65, 'A ready-to-deploy digital governance platform that manages the entire board meeting lifecycle — from annual planning to resolution tracking — in one secure, centralized system.', 14, $ltGreen);

addCard($slide, 50,  210, 270, 100, 'Purpose-Built for Governance', 'Designed specifically for board and committee operations in government institutions, not adapted from generic tools');
addCard($slide, 335, 210, 270, 100, 'Rapid Deployment', '6-step setup wizard configures the platform for any institution in under an hour, with full white-label branding');
addCard($slide, 620, 210, 270, 100, 'Built for Kenya', 'Kenyan fiscal year (July–June), 10-day notice compliance, 47 counties pre-loaded, Mwongozo-aligned');

addText($slide, 50, 335, 820, 40, 'Developed to support ICTA\'s mandate of digitizing government operations', 12, $gold, true, Alignment::HORIZONTAL_CENTER);

// ════════════════════════════════════════════════════════════════
// SLIDE 7: 10-STAGE WORKFLOW
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $white);
addAccentBar($slide, 0, 0, 916, 5, $ictaGreen);

addText($slide, 50, 15, 400, 25, 'CORE ARCHITECTURE', 9, $ictaGreen, true);
addText($slide, 50, 40, 830, 40, 'Complete 10-Stage Meeting Lifecycle', 24, $darkText, true);
addText($slide, 50, 82, 750, 28, 'Every board meeting follows a structured, auditable workflow from initiation to minutes.', 12, $muted);

$stages = ['1. Meeting Initiation', '2. Internal Notice', '3. CEO Approval', '4. Officer Assignments', '5. Paper Preparation', '6. Director Review', '7. CEO Review', '8. Agenda Building', '9. Live Meeting', '10. Minutes Capture'];
$stageColors = [$ictaGreen, $darkGreen, $gold, $ictaGreen, $darkGreen, $gold, $ictaGreen, $darkGreen, $red, $gold];
foreach ($stages as $i => $stage) {
    $col = $i % 5;
    $row = (int)($i / 5);
    $sx = 50 + $col * 172;
    $sy = 120 + $row * 52;

    $shape = $slide->createRichTextShape()
        ->setOffsetX($sx)->setOffsetY($sy)->setWidth(162)->setHeight(42);
    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFF5F5F0'));
    $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFDDE5DD'))->setLineWidth(1);
    $shape->setInsetLeft(6)->setInsetTop(4);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
    $run = $shape->createTextRun($stage);
    $run->getFont()->setName('Calibri')->setSize(10)->setBold(true)->setColor(new Color($stageColors[$i]));
}

addCard($slide, 50,  250, 410, 80, 'Multi-Stage Paper Review', 'Board papers flow through Officer, Director, then CEO with comments, annotations, and version tracking at each stage. Papers can be recalled and resubmitted.', false);
addCard($slide, 480, 250, 410, 80, 'Live Meeting Operations', 'Digital attendance register, OTP-verified conflict of interest declarations with e-signatures, real-time agenda management, and structured minutes capture.', false);

// ════════════════════════════════════════════════════════════════
// SLIDE 8: BOARD PAPER MANAGEMENT
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $lightBg);
addAccentBar($slide, 0, 0, 916, 5, $ictaGreen);

addText($slide, 50, 18, 300, 25, 'KEY CAPABILITY', 9, $ictaGreen, true);
addText($slide, 50, 48, 830, 40, 'Board Paper Management', 24, $darkText, true);
addText($slide, 50, 92, 700, 28, 'A structured workflow replaces email attachments with controlled, versioned, reviewable board papers.', 12, $muted);

addCard($slide, 50,  135, 270, 90, 'Structured Submission', 'Officers prepare papers with supporting annexures. Multiple file formats supported up to 20MB per document.', false);
addCard($slide, 335, 135, 270, 90, 'Three-Stage Review', 'Papers reviewed by Director, then CEO. Each reviewer can approve, reject with comments, or add annotations.', false);
addCard($slide, 620, 135, 270, 90, 'Version History', 'Every version of a paper is retained. Rejected papers are revised and resubmitted with a complete audit trail.', false);
addCard($slide, 50,  240, 410, 80, 'Automated Notifications', 'Email notifications at every status change — submission, review, approval, rejection. 33+ email templates for the complete workflow.', false);
addCard($slide, 480, 240, 410, 80, 'Controlled Access', 'Board members see only papers for their committees. Three visibility levels ensure confidential papers reach only authorized recipients.', false);

// ════════════════════════════════════════════════════════════════
// SLIDE 9: MEETING LIFECYCLE
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $white);
addAccentBar($slide, 0, 0, 916, 5, $ictaGreen);

addText($slide, 50, 18, 300, 25, 'KEY CAPABILITY', 9, $ictaGreen, true);
addText($slide, 50, 48, 830, 40, 'Meeting Lifecycle Management', 24, $darkText, true);

addBullets($slide, 50, 100, 420, 200, [
    'Almanac Planning — Annual meeting schedule tied to financial year with CEO approval',
    'Meeting Initiation — Create meetings from approved almanac with auto-populated details',
    '10-Day Board Notice — Automated notice generation with Kenyan governance compliance',
    'Agenda Building — Drag-and-drop agenda items, assign papers to agenda points',
    'Attendance Tracking — Digital register with PDF and CSV export',
    'Minutes Capture — Per-agenda-item minutes with Word document export',
    'Board Resolutions — Record, assign, set due dates, and track through completion',
], 10, $darkText);

addCard($slide, 490, 100, 400, 85, 'Conflict of Interest Register', 'Board members declare conflicts with OTP email verification and digital signature capture. Exportable as PDF or CSV for compliance records.', false);
addCard($slide, 490, 200, 400, 85, 'Resolution Tracking', 'Every resolution is assigned to a responsible officer with a due date. Status tracked: Pending, In Progress, Completed, or Overdue.', false);

// ════════════════════════════════════════════════════════════════
// SLIDE 10: BOARD PORTAL
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $deepGreen);
addAccentBar($slide, 0, 0, 916, 5, $gold);

addText($slide, 50, 15, 400, 25, 'BOARD MEMBER EXPERIENCE', 9, $gold, true);
addText($slide, 50, 40, 830, 40, 'iPad-Optimized Board Portal', 24, $white, true);
addText($slide, 50, 80, 700, 28, 'Board members access a dedicated, clean portal designed for tablet and desktop use.', 12, $ltGreen);

$portalItems = [
    ['Meeting Calendar', 'Visual calendar showing all scheduled meetings for the member\'s committees'],
    ['Board Papers', 'View approved papers with digital sticky notes for personal annotations'],
    ['Official Notices', 'Access meeting notices and download them directly from the portal'],
    ['My Committees', 'View committee memberships, details, and committee-specific documents'],
    ['Document Repository', 'Access categorized institutional documents with controlled visibility'],
    ['Active Meetings', 'Join live meetings, view agenda, submit conflict declarations in real time'],
];
foreach ($portalItems as $i => $item) {
    $col = $i % 3;
    $row = (int)($i / 3);
    addCard($slide, 50 + $col * 285, 120 + $row * 95, 270, 82, $item[0], $item[1]);
}

// ════════════════════════════════════════════════════════════════
// SLIDE 11: SECURITY & COMPLIANCE
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $lightBg);
addAccentBar($slide, 0, 0, 916, 5, $red);

addText($slide, 50, 15, 400, 25, 'SECURITY & COMPLIANCE', 9, $red, true);
addText($slide, 50, 40, 850, 40, 'Enterprise-Grade Security for Government', 22, $darkText, true);

$secItems = [
    ['Two-Factor Authentication', 'OTP-based 2FA via email on every login. 6-digit codes with 5-minute expiry, throttled to prevent brute force.'],
    ['Role-Based Access Control', '10 system roles with granular permissions. Each user sees only what their role allows — from CEO to Board Member.'],
    ['Account Protection', 'Automatic lockout after failed login attempts. Forced password change on first login. User status enforcement.'],
    ['Comprehensive Audit Trail', 'Every action logged — who, what, when, from where. Before-and-after values captured. IP address and browser recorded.'],
    ['Document Access Control', 'Three visibility levels: all board members, committee-only, or specific named individuals. Download tracking.'],
    ['CSRF & Encryption', 'Cross-site request forgery protection, encrypted cookies, secure session management, API token auth via Laravel Sanctum.'],
];
foreach ($secItems as $i => $item) {
    $col = $i % 2;
    $row = (int)($i / 2);
    addCard($slide, 50 + $col * 440, 85 + $row * 85, 420, 73, $item[0], $item[1], false);
}

// ════════════════════════════════════════════════════════════════
// SLIDE 12: DOCUMENT MANAGEMENT
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $deepGreen);
addAccentBar($slide, 0, 0, 916, 5, $gold);

addText($slide, 50, 15, 400, 25, 'INSTITUTIONAL MEMORY', 9, $gold, true);
addText($slide, 50, 42, 830, 40, 'Document Repository & Exports', 24, $white, true);

addText($slide, 50, 95, 410, 25, 'Centralized Document Repository', 13, $gold, true);
addBullets($slide, 50, 122, 410, 170, [
    'Organized by categories with icons and display ordering',
    'Supports PDF, Word, Excel, PowerPoint, CSV, images — up to 20MB',
    'Three access tiers: all members, committee-only, or named individuals',
    'Download tracking and access verification',
    'Accessible to board members through the portal',
], 11, $ltGreen);

addText($slide, 480, 95, 410, 25, 'Generated Reports & Exports', 13, $gold, true);
addBullets($slide, 480, 122, 410, 170, [
    'PDF: Almanac, board notices (with letterhead), attendance register, conflict register, audit logs',
    'CSV: Attendance register, conflict register (Excel-compatible)',
    'Word: Meeting minutes with structured formatting',
    'Digital Signatures: Signature pad capture and PDF document signing',
], 11, $ltGreen);

// ════════════════════════════════════════════════════════════════
// SLIDE 13: WHITE-LABEL & DEPLOYMENT
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $white);
addAccentBar($slide, 0, 0, 916, 5, $ictaGreen);

addText($slide, 50, 15, 400, 25, 'DEPLOYMENT', 9, $ictaGreen, true);
addText($slide, 50, 42, 830, 40, 'White-Label Platform with Rapid Setup', 22, $darkText, true);
addText($slide, 50, 85, 700, 28, 'E-Board adapts to any institution\'s identity and deploys in hours, not months.', 12, $muted);

addCard($slide, 50,  125, 270, 90, 'Full Branding Control', 'Institution logos, colors, letterhead, favicon, CEO signature image, login background — all configurable from the admin panel.', false);
addCard($slide, 335, 125, 270, 90, '6-Step Setup Wizard', 'Institution identity, branding, workflow settings, role labels, email configuration, admin account — guided setup.', false);
addCard($slide, 620, 125, 270, 90, 'Configurable Roles', 'Rename any of the 10 system roles to match your institution\'s terminology. Customize signatory titles.', false);

addText($slide, 50, 235, 410, 20, 'Technology Stack', 12, $darkText, true);
addBullets($slide, 50, 258, 410, 90, [
    'Backend: Laravel (PHP 8+), MySQL',
    'Frontend: Bootstrap 5, Alpine.js, Tailwind CSS',
    'PDF: DomPDF with institutional letterhead',
    'Documents: PHPWord for Word exports',
], 10, $muted);

addText($slide, 480, 235, 410, 20, 'Infrastructure Requirements', 12, $darkText, true);
addBullets($slide, 480, 258, 410, 90, [
    'Server: PHP 8.0+, MySQL 5.7+',
    'Web Server: Apache or Nginx',
    'Email: Any SMTP server',
    'Hosting: Self-hosted or cloud with SLA',
], 10, $muted);

// ════════════════════════════════════════════════════════════════
// SLIDE 14: COMPARISON TABLE
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $lightBg);
addAccentBar($slide, 0, 0, 916, 5, $ictaGreen);

addText($slide, 50, 15, 400, 25, 'COMPARISON', 9, $ictaGreen, true);
addText($slide, 50, 42, 830, 40, 'E-Board vs. Current Approaches', 22, $darkText, true);

$allRows = [
    ['Capability', 'E-Board', 'Email', 'WhatsApp', 'Shared Drives', 'Manual'],
    ['Board Paper Workflow', 'Full 3-stage', 'Attachments', 'No', 'Files only', 'No'],
    ['Meeting Lifecycle', '10-stage', 'No', 'No', 'No', 'Manual'],
    ['Agenda Management', 'Full', 'No', 'No', 'Documents', 'Paper'],
    ['Resolution Tracking', 'Full', 'No', 'No', 'No', 'Spreadsheet'],
    ['Audit Trail', 'Automatic', 'No', 'No', 'File logs', 'No'],
    ['Role-Based Access', '10 roles', 'No', 'No', 'Folders', 'No'],
    ['10-Day Notice', 'Automated', 'Manual', 'Manual', 'No', 'Manual'],
    ['Conflict of Interest', 'OTP + Sig', 'No', 'No', 'No', 'Paper forms'],
    ['Institutional Branding', 'Full', 'No', 'No', 'No', 'No'],
];

$numCols = 6;
$table = $slide->createTableShape($numCols);
$table->setOffsetX(40)->setOffsetY(90)->setWidth(840)->setHeight(260);

foreach ($allRows as $ri => $rowData) {
    $row = $table->createRow();
    $row->setHeight(24);
    for ($ci = 0; $ci < $numCols; $ci++) {
        $cell = $row->getCell($ci);
        $val = $rowData[$ci];

        if ($ri === 0) {
            $cell->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($deepGreen));
            $run = $cell->createTextRun($val);
            $run->getFont()->setName('Calibri')->setSize(8)->setBold(true)->setColor(new Color($white));
        } else {
            $bgC = $ri % 2 === 0 ? 'FFF5F5F0' : $white;
            $cell->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($bgC));
            $fontColor = $darkText;
            if ($ci === 1 && $val !== 'N/A') $fontColor = $ictaGreen;
            elseif ($val === 'No') $fontColor = $red;
            $run = $cell->createTextRun($val);
            $run->getFont()->setName('Calibri')->setSize(8)->setColor(new Color($fontColor));
            if ($ci === 0) $run->getFont()->setBold(true);
        }
    }
}

// ════════════════════════════════════════════════════════════════
// SLIDE 15: TARGET INSTITUTIONS
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $deepGreen);
addAccentBar($slide, 0, 0, 916, 5, $gold);

addText($slide, 50, 15, 400, 25, 'TARGET INSTITUTIONS', 9, $gold, true);
addText($slide, 50, 42, 830, 40, 'Built for Government & Public Bodies', 24, $white, true);

$audiences = [
    'Government Ministries & Agencies', 'State Corporations', 'County Government Entities',
    'Universities & Public Institutions', 'NGOs & Foundations', 'Regulatory Bodies',
    'Professional Associations', 'SAGAs & Parastatals', 'Multi-Entity Groups',
];
foreach ($audiences as $i => $aud) {
    $col = $i % 3;
    $row = (int)($i / 3);
    addCard($slide, 50 + $col * 285, 90 + $row * 48, 270, 38, $aud, '');
}

addCard($slide, 50,  245, 270, 80, '10 Configurable Roles', 'Superadmin, CEO, Corporation Secretary, Director, Division Head, DD Internal Audit, DD ICT, Board Member, Officer, Admin');
addCard($slide, 335, 245, 270, 80, 'Kenyan Compliance', 'July–June fiscal year, 10-day notice, conflict of interest register, 47 counties, Mwongozo-aligned workflows');
addCard($slide, 620, 245, 270, 80, 'Committee Structure', 'Unlimited boards and committees with audit committee designation. Role-specific access controls.');

// ════════════════════════════════════════════════════════════════
// SLIDE 16: IMPLEMENTATION
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $white);
addAccentBar($slide, 0, 0, 916, 5, $ictaGreen);

addText($slide, 50, 15, 400, 25, 'IMPLEMENTATION', 9, $ictaGreen, true);
addText($slide, 50, 42, 830, 40, 'From Setup to First Meeting in Days', 22, $darkText, true);

$phases = [
    ['Phase 1: Platform Setup (Day 1–2)', 'Run setup wizard, configure branding, set up SMTP email, define roles and organizational structure, create admin accounts.'],
    ['Phase 2: Data Migration (Day 3–5)', 'Onboard board members with profiles. Set up committees. Create financial year and almanac with meeting schedule.'],
    ['Phase 3: Training (Day 6–8)', 'Role-specific training for Corporation Secretary, CEO, Directors, and Board Members. Hands-on sessions.'],
    ['Phase 4: Go-Live (Day 9–10)', 'First meeting managed through E-Board. On-site support during initial board meeting. Handover to ongoing support.'],
];
foreach ($phases as $i => $p) {
    addCard($slide, 50, 90 + $i * 68, 450, 58, $p[0], $p[1], false);
}

addText($slide, 520, 90, 380, 25, 'Support Model', 13, $darkText, true);
addBullets($slide, 520, 118, 380, 120, [
    'Dedicated account manager',
    'Email and phone support',
    'Platform updates and maintenance',
    'User onboarding assistance',
    'Quarterly review meetings',
    'Data backup and recovery',
], 10, $muted);

addText($slide, 520, 260, 380, 25, 'Hosting Options', 12, $darkText, true);
addBullets($slide, 520, 285, 380, 50, [
    'On-Premise: Institution\'s own servers',
    'Cloud: Managed cloud hosting with SLA',
], 10, $muted);

// ════════════════════════════════════════════════════════════════
// SLIDE 17: NEXT STEPS / CTA
// ════════════════════════════════════════════════════════════════
$slide = $pptx->createSlide();
slideBg($slide, $deepGreen);
addAccentBar($slide, 0, 0, 916, 6, $gold);
addAccentBar($slide, 0, 480, 916, 6, $red);

addText($slide, 50, 18, 300, 25, 'NEXT STEPS', 9, $gold, true);
addText($slide, 50, 55, 820, 70, "Ready to Digitize\nBoard Governance?", 34, $white, true, Alignment::HORIZONTAL_CENTER);
addText($slide, 80, 145, 760, 45, 'We invite your institution to experience E-Board through a guided demonstration and pilot programme.', 14, $ltGreen, false, Alignment::HORIZONTAL_CENTER);

addCard($slide, 50,  210, 270, 80, '1. Live Demo', '45-minute walkthrough of E-Board configured for your institution\'s workflow');
addCard($slide, 335, 210, 270, 80, '2. 30-Day Pilot', 'Full platform access for one board or committee with training and dedicated support');
addCard($slide, 620, 210, 270, 80, '3. Go Live', 'Full deployment with institutional branding, data migration, and support SLA');

// CTA box
$cta = $slide->createRichTextShape()
    ->setOffsetX(180)->setOffsetY(315)->setWidth(560)->setHeight(60);
$cta->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF003D1A'));
$cta->getBorder()->setLineStyle(Border::LINE_SINGLE)->setColor(new Color($gold))->setLineWidth(2);
$cta->setInsetLeft(15)->setInsetTop(8);
$cta->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$r1 = $cta->createTextRun('ICT Authority Kenya');
$r1->getFont()->setName('Calibri')->setSize(16)->setBold(true)->setColor(new Color($gold));
$cta->createBreak();
$r2 = $cta->createTextRun('www.icta.go.ke  |  A Digitally Empowered Society');
$r2->getFont()->setName('Calibri')->setSize(11)->setColor(new Color($ltGreen));

// ── SAVE ──
$outputFile = __DIR__ . '/ICTA-E-Board-Presentation.pptx';
$writer = IOFactory::createWriter($pptx, 'PowerPoint2007');
$writer->save($outputFile);

$size = round(filesize($outputFile) / 1024);
echo "Done! PowerPoint saved: {$outputFile} ({$size} KB)\n";
echo "   Slides: 17\n";
echo "   Branding: ICT Authority Kenya\n";
echo "   Format: .pptx (PowerPoint 2007+)\n";
echo "   Layout: Standard 4:3 (10\" x 7.5\")\n";
