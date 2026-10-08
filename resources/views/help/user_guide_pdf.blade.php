<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $acronym }} eBoard User Guide</title>
    <style>
        @page { margin: 70px 50px 60px 50px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10.5px; line-height: 1.55; color: #111111; }

        header { position: fixed; top: -48px; left: 0; right: 0; height: 30px; border-bottom: 1px solid #d1d5db; font-size: 9px; color: #6b7280; }
        header .r { float: right; }
        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 24px; border-top: 1px solid #d1d5db; font-size: 9px; color: #6b7280; padding-top: 6px; }
        footer .pg:after { content: "Page " counter(page); }

        .cover { text-align: center; padding-top: 140px; page-break-after: always; }
        .cover img { max-height: 90px; max-width: 320px; margin-bottom: 30px; }
        .cover h1 { font-size: 28px; color: #222222; margin: 0 0 8px; }
        .cover .sub { font-size: 14px; color: #374151; margin-bottom: 40px; }
        .cover .meta { font-size: 11px; color: #6b7280; }

        .toc { page-break-after: always; }
        .toc h2 { font-size: 18px; color: #222222; margin: 0 0 14px; }
        .toc .grp { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin: 12px 0 4px; }
        .toc a { display: block; color: #111111; text-decoration: none; padding: 3px 0; border-bottom: 1px dotted #e5e7eb; font-size: 11px; }

        /* Shared guide content (help.partials.guide_sections) */
        i.mdi { display: none; }
        .ug-sec { margin-bottom: 18px; }
        .ug-sec h3 { font-size: 15px; color: #191919; margin: 0 0 4px; padding-bottom: 5px; border-bottom: 2px solid #ececec; }
        .ug-sec h3 .num { display: inline-block; background: #ececec; color: #222222; font-size: 11px; padding: 1px 7px; border-radius: 4px; margin-right: 6px; }
        .ug-sec h5 { font-size: 12px; margin: 12px 0 5px; color: #0d0d0d; }
        .ug-sec p { margin: 0 0 7px; }
        .ug-sec ol, .ug-sec ul { margin: 0 0 8px; padding-left: 18px; }
        .ug-sec li { margin-bottom: 3px; }
        .ug-who { margin: 4px 0 8px; }
        .ug-role { display: inline-block; font-size: 8.5px; font-weight: bold; padding: 2px 7px; border-radius: 8px; background: #f7f7f7; border: 1px solid #e5e7eb; color: #374151; margin-right: 4px; }
        .ug-ui { font-weight: bold; color: #191919; }
        .ug-path { background: #f7f7f7; border: 1px solid #e5e7eb; border-radius: 3px; padding: 0 5px; }
        .ug-status { font-size: 9px; font-weight: bold; background: #f5f5f5; padding: 0 5px; border-radius: 3px; }
        .ug-tip, .ug-warn { padding: 7px 10px; margin: 8px 0; border-radius: 4px; page-break-inside: avoid; }
        .ug-tip { background: #f6f6f6; border-left: 3px solid #f00014; }
        .ug-warn { background: #fff8e6; border-left: 3px solid #f59e0b; }
        .ug-tip p, .ug-warn p { margin: 0; }
        .ug-table { width: 100%; border-collapse: collapse; margin: 6px 0 10px; }
        .ug-table th { background: #f7f7f7; text-align: left; padding: 5px 7px; border-bottom: 1px solid #d1d5db; font-size: 10px; }
        .ug-table td { padding: 5px 7px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .ug-table tr { page-break-inside: avoid; }
        .ug-flow a { display: inline-block; width: 30%; margin: 0 1% 6px 0; padding: 5px 7px; border: 1px solid #e5e7eb; border-radius: 4px; text-decoration: none; vertical-align: top; }
        .ug-flow .s { display: block; font-size: 8px; font-weight: bold; color: #f00014; text-transform: uppercase; }
        .ug-flow .t { display: block; font-weight: bold; color: #0d0d0d; font-size: 10.5px; }
        .ug-flow .r { display: block; font-size: 9px; color: #6b7280; }
        a { color: #222222; }
        code { font-size: 9.5px; }
        .text-muted { color: #6b7280; }
    </style>
</head>
<body>

<header>{{ $institutionName }}<span class="r">{{ $acronym }} eBoard User Guide</span></header>
<footer>Generated {{ $generatedOn }}<span class="pg" style="float:right"></span></footer>

<div class="cover">
    @php
        $logo = setting('logo_main') ?: setting('logo_letterhead');
        $logoPath = $logo ? public_path($logo) : null;
    @endphp
    @if($logoPath && file_exists($logoPath))
        <img src="{{ $logoPath }}" alt="{{ $acronym }}">
    @endif
    <h1>{{ $acronym }} eBoard User Guide</h1>
    <div class="sub">{{ $institutionName }}</div>
    <div class="meta">How to use the system from start to finish<br>Generated {{ $generatedOn }}</div>
</div>

<div class="toc">
    <h2>Contents</h2>
    <div class="grp">Getting started</div>
    <a href="#roles">Who does what</a>
    <a href="#login">Logging in</a>
    <a href="#navigating">Finding your way around</a>
    <a href="#lifecycle">The meeting lifecycle at a glance</a>
    <div class="grp">Setup &amp; administration</div>
    <a href="#setup">First-time setup &amp; system settings</a>
    <a href="#organisation">Organisation structure</a>
    <a href="#users">Users, roles &amp; permissions</a>
    <a href="#board-members">Board members &amp; committees</a>
    <div class="grp">Meeting workflow</div>
    <a href="#almanac">Almanac: the annual meeting calendar</a>
    <a href="#initiate">1. Initiate a meeting</a>
    <a href="#internal-notice">2–3. Internal notice &amp; CEO approval</a>
    <a href="#assign">4. Assign officers</a>
    <a href="#paper">5. Prepare a board paper</a>
    <a href="#director-review">6. Director review</a>
    <a href="#ceo-review">7. CEO paper review</a>
    <a href="#board-notice">Board notice to members</a>
    <a href="#prepare">8. Meeting preparation</a>
    <a href="#live">9. The live meeting</a>
    <a href="#minutes">10. Minutes</a>
    <div class="grp">Other</div>
    <a href="#portal">Board member portal</a>
    <a href="#audit">Audit committee meetings</a>
    <a href="#records">Records &amp; resolutions</a>
    <a href="#faq">Troubleshooting</a>
</div>

@include('help.partials.guide_sections')

</body>
</html>
