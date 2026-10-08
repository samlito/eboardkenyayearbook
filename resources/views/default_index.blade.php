{{-- resources/views/eboard/analytics.blade.php --}}
@extends('admin_dashboard')

@section('admin')

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;
    use Carbon\Carbon;
    use App\Helpers\AlmanacHelper;

    /* =========================
     |  INPUTS / CONTEXT
     * ========================*/
    $calendarYear  = (int) request('year', AlmanacHelper::currentCalendarYear());
    $financialYear = (string) request('fy', AlmanacHelper::currentFinancialYear());
    $committeeReq  = request('committee');

    $user  = Auth::user();
    $role  = ucfirst(optional($user->roles->first())->name ?? 'Dashboard');

    // Parse FY like "2024/2025"
    preg_match('/(\d{4}).*?(\d{4})/', (string) $financialYear, $fyParts);
    $fyStartYear = isset($fyParts[1]) ? (int) $fyParts[1] : (int) now()->format('Y');
    $fyEndYear   = isset($fyParts[2]) ? (int) $fyParts[2] : $fyStartYear + 1;

    // FY: Jul → Jun (adjust if yours differs)
    $fyStart = Carbon::create($fyStartYear, 7, 1, 0, 0, 0);
    $fyEnd   = Carbon::create($fyEndYear,   6, 30, 23, 59, 59);
    $quarter = AlmanacHelper::currentFinancialQuarter();

    // Meeting date/datetime column
    $meetingDateCol = collect(['date','meeting_date','meetingdate','scheduled_date','scheduled_at','start_date','start_at','start_time'])
        ->first(fn ($col) => Schema::hasColumn('meetings', $col)) ?? 'created_at';

    // Committee name source
    $hasCommitteesTable = Schema::hasTable('board_committees');
    $committeeFk = Schema::hasColumn('meetings','board_committee_id')
        ? 'board_committee_id'
        : (Schema::hasColumn('meetings','committee_id') ? 'committee_id' : null);
    $committeeNameCol = Schema::hasColumn('meetings','committee')
        ? 'committee'
        : (Schema::hasColumn('meetings','committee_name') ? 'committee_name' : null);

    /* =========================
     |  CORE DATA
     * ========================*/

    // Monthly submissions (selected calendar year)
    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $monthCounts = array_fill(1, 12, 0);
    $rows = DB::table('board_papers')
        ->selectRaw('MONTH(created_at) as m, COUNT(*) as c')
        ->whereYear('created_at', $calendarYear)
        ->groupBy('m')
        ->pluck('c','m');
    foreach ($rows as $m=>$c) { $monthCounts[(int)$m] = (int)$c; }
    $hcMonthlyCategories = $months;
    $hcMonthlyValues     = array_values($monthCounts);

    // Status breakdown (all-time)
    $statusRows = DB::table('board_papers')
        ->selectRaw('COALESCE(NULLIF(TRIM(status), ""), "Unknown") as s, COUNT(*) as c')
        ->groupBy('s')->orderByDesc('c')->get();
    $hcStatusBreakdown = $statusRows->map(fn ($r)=>['name'=>ucfirst($r->s), 'y'=>(int)$r->c])->values()->all();

    // Meetings per committee (this FY)
    $base = DB::table('board_papers as bp')
        ->join('meetings as m', 'm.id', '=', 'bp.meeting_id')
        ->whereBetween("m.$meetingDateCol", [$fyStart->toDateString(), $fyEnd->toDateString()]);
    if ($committeeReq && $committeeNameCol) { $base->where("m.$committeeNameCol", $committeeReq); }
    if ($hasCommitteesTable && $committeeFk) {
        $base->leftJoin('board_committees as bc', 'bc.id', '=', "m.$committeeFk")
             ->selectRaw('COALESCE(bc.name, "Unassigned") as committee, COUNT(*) as c')
             ->groupBy('committee')->orderByDesc('c');
    } elseif ($committeeNameCol) {
        $base->selectRaw("COALESCE(m.$committeeNameCol, 'Unassigned') as committee, COUNT(*) as c")
             ->groupBy('committee')->orderByDesc('c');
    } else {
        $base->selectRaw("'All Meetings' as committee, COUNT(*) as c");
    }
    $committeeRows = $base->get();
    $hcCommitteeCategories = $committeeRows->pluck('committee')->all();
    $hcCommitteeValues     = $committeeRows->pluck('c')->map(fn($v)=>(int)$v)->all();

    // Heatmap weekday/hour (last 90 days)
    $heatBase = DB::table('meetings')->whereBetween("meetings.$meetingDateCol", [now()->subDays(90)->startOfDay(), now()->endOfDay()]);
    if ($committeeReq && $committeeNameCol) { $heatBase->where("meetings.$committeeNameCol", $committeeReq); }
    $heat = $heatBase->selectRaw("DAYOFWEEK($meetingDateCol) as dw, HOUR($meetingDateCol) as hr, COUNT(*) as c")->groupBy('dw','hr')->get();
    $weekdayOrder = [2=>'Mon',3=>'Tue',4=>'Wed',5=>'Thu',6=>'Fri',7=>'Sat',1=>'Sun'];
    $hours = range(0,23);
    $heatGrid = [];
    foreach ($weekdayOrder as $dw=>$label) {
        $row = ['name'=>$label, 'data'=>[]];
        foreach ($hours as $h) {
            $found = $heat->firstWhere(fn($r)=> (int)$r->dw === $dw && (int)$r->hr === $h);
            $row['data'][] = ['x'=>sprintf('%02d:00',$h), 'y'=>(int)($found->c ?? 0)];
        }
        $heatGrid[] = $row;
    }

    /* =========================
     |  EXTRA ANALYTICS
     * ========================*/

    // Counts
    $totalPapers = (int) DB::table('board_papers')->count();
    $papersThisMonth  = (int) DB::table('board_papers')->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count();
    $papersLastMonth  = (int) DB::table('board_papers')->whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->count();
    $delta   = $papersThisMonth - $papersLastMonth;
    $momPct  = $papersLastMonth ? round($delta / $papersLastMonth * 100) : ($papersThisMonth ? 100 : 0);

    // Approval metrics
    $approvedCount = (int) DB::table('board_papers')->whereRaw("LOWER(status) = 'approved'")->count();
    $approvalRate  = $totalPapers ? round($approvedCount / $totalPapers * 100) : 0;

    // Average days to approval
    $hasApprovedAt = Schema::hasColumn('board_papers', 'approved_at');
    $avgDaysToApproval = (float) DB::table('board_papers')
        ->when($hasApprovedAt, fn($q)=>$q->whereNotNull('approved_at')->selectRaw('AVG(DATEDIFF(approved_at, created_at)) as d'))
        ->when(!$hasApprovedAt, fn($q)=>$q->whereRaw("LOWER(status)='approved'")->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as d'))
        ->value('d') ?? 0;
    $avgDaysToApproval = round($avgDaysToApproval, 1);

    // Last 30 days sparkline
    $sparkCounts = [];
    for ($i=29; $i>=0; $i--) {
        $d = now()->subDays($i)->toDateString();
        $sparkCounts[] = (int) DB::table('board_papers')->whereDate('created_at', $d)->count();
    }

    // Top submitters (users)
    $hasUsers = Schema::hasTable('users');
    $topSubmitters = collect();
    if ($hasUsers && Schema::hasColumn('board_papers','user_id')) {
        $topSubmitters = DB::table('board_papers as bp')
            ->join('users as u', 'u.id', '=', 'bp.user_id')
            ->selectRaw('u.name as author, COUNT(*) as c')
            ->groupBy('author')->orderByDesc('c')->limit(5)->get();
    }

    // Quarter progression
    $now = now();
    $yearPct = (int) round($now->dayOfYear/($now->isLeapYear()?366:365)*100);
    $fyTotalDays = $fyStart->diffInDays($fyEnd)+1;
    $fyElapsed   = max(0, min($fyTotalDays, $fyStart->diffInDays($now)+1));
    $fyPct       = (int) round($fyElapsed/$fyTotalDays*100);
    $currentMonth = $now->month;
    $quarterStartMonth = (($currentMonth-1) - (($currentMonth-1)%3)) + 1;
    $quarterStart = $now->copy()->month($quarterStartMonth)->startOfMonth();
    $quarterEnd   = $quarterStart->copy()->addMonths(2)->endOfMonth();
    $qTotalDays   = $quarterStart->diffInDays($quarterEnd)+1;
    $qElapsed     = max(0, min($qTotalDays, $quarterStart->diffInDays($now)+1));
    $qPct         = (int) round($qElapsed/$qTotalDays*100);

    // Recent items
    $recentPapers = DB::table('board_papers')->select('id','title','status','created_at')->latest('created_at')->limit(8)->get();

    // Year / FY options
    $yearOptions = DB::table('board_papers')->selectRaw('DISTINCT YEAR(created_at) as y')->orderByDesc('y')->pluck('y')->map(fn($v)=>(int)$v)->all();
    $minYear = (int) DB::table('board_papers')->min(DB::raw('YEAR(created_at)')) ?: now()->year;
    $maxYear = (int) DB::table('board_papers')->max(DB::raw('YEAR(created_at)')) ?: now()->year;
    $fyOptions = [];
    for ($y=$maxYear+1; $y>=$minYear; $y--) { $fyOptions[] = sprintf('%d/%d', $y-1, $y); }

    // Route existence (avoid RouteNotFoundException)
    $hasPapersRoute   = Route::has('board.papers.index');
    $hasMeetingsRoute = Route::has('meetings.index');
    $hasReportRoute   = Route::has('reports.download');
@endphp

<div class="app-offset"></div>

<div class="container-xxl twwda-analytics">

  {{-- Header  --}}
<div class="row mb-4">
  <div class="col-12">
    <div class="card hero-card border-0 overflow-hidden">
      <div class="hero-overlay"></div>

      <div class="card-body hero-body d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center">
          <div class="twwda-logo hero-logo me-3">
            <i class="bi bi-droplet-half"></i>
          </div>
          <div class="text-white">
            <h2 class="mb-1 fw-bold hero-title">E-Board Analytics</h2>
            <p class="mb-0 hero-sub">Welcome back, {{ $user->name }} · {{ $role }}</p>
            <div class="d-flex align-items-center gap-2 mt-2 small">
              <i class="bi bi-clock"></i>
              <span id="currentTime" class="fw-medium"></span>
            </div>
          </div>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
          <button id="themeToggle"
                  class="btn btn-sm btn-glass text-white d-flex align-items-center gap-2"
                  data-bs-toggle="tooltip" title="Toggle light/dark">
            <i class="bi bi-moon-stars"></i>
            <span class="d-none d-sm-inline">Theme</span>
          </button>

          <div class="d-flex flex-column text-end hero-kpis">
            <div class="chip chip-light">
              <span class="chip-label">Calendar Year</span>
              <span class="chip-value">{{ $calendarYear }}</span>
            </div>
            <div class="chip chip-info">
              <span class="chip-label">Financial Year</span>
              <span class="chip-value">{{ $financialYear }}</span>
            </div>
            <div class="chip chip-warn">
              <span class="chip-label">Quarter</span>
              <span class="chip-value">{{ $quarter }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="hero-ribbon"></div>
    </div>
  </div>
</div>


    {{-- Filters --}}
    <div class="card filter-card mb-4 shadow-sm">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Calendar Year</label>
                    <select class="form-select form-select-sm" name="year">
                        @foreach($yearOptions as $y)
                            <option value="{{ $y }}" @selected($y === $calendarYear)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Financial Year</label>
                    <select class="form-select form-select-sm" name="fy">
                        @foreach($fyOptions as $fy)
                            <option value="{{ $fy }}" @selected($fy === $financialYear)>{{ $fy }}</option>
                        @endforeach
                    </select>
                </div>
                @if($committeeNameCol)
                <div class="col-md-3">
                    <label class="form-label">Committee</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-people"></i></span>
                        <input type="text" name="committee" class="form-control" value="{{ $committeeReq ?? '' }}" placeholder="Finance, HR, ...">
                    </div>
                </div>
                @endif
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-gradient flex-fill"><i class="bi bi-funnel me-1"></i>Apply</button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary flex-fill">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- KPI Row 1 --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card card h-100 hover-raise glass">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="icon-chip bg-primary-soft"><i class="bi bi-files"></i></div>
                        <i class="bi bi-info-circle text-secondary" data-bs-toggle="tooltip" title="All papers ever submitted"></i>
                    </div>
                    <div class="kpi-title">Total Papers</div>
                    <div class="kpi-value text-primary">{{ number_format($totalPapers) }}</div>
                    <div id="sparkline" class="sparkline"></div>
                    <div class="progress progress-thin mt-2"><div class="progress-bar" style="width: 100%"></div></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card card h-100 hover-raise glass">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="icon-chip bg-success-soft"><i class="bi bi-graph-up-arrow"></i></div>
                        <span class="badge {{ $delta >= 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">{{ $delta >= 0 ? '+' : '' }}{{ $momPct }}%</span>
                    </div>
                    <div class="kpi-title">This Month</div>
                    <div class="kpi-value text-success">{{ $papersThisMonth }}</div>
                    <div class="small text-muted">vs last month ({{ $papersLastMonth }})</div>
                    <div class="progress progress-thin mt-2"><div class="progress-bar bg-success" style="width: {{ max(6,min(100,$papersThisMonth*8)) }}%"></div></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card card h-100 hover-raise glass">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="icon-chip bg-info-soft"><i class="bi bi-patch-check"></i></div>
                        <i class="bi bi-trophy text-warning"></i>
                    </div>
                    <div class="kpi-title">Approval Rate</div>
                    <div class="kpi-value text-info">{{ $approvalRate }}%</div>
                    <div class="small text-muted">{{ $approvedCount }} approved</div>
                    <div class="progress progress-thin mt-2"><div class="progress-bar bg-info" style="width: {{ $approvalRate }}%"></div></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="kpi-card card h-100 hover-raise glass">
                <div class="card-body">
                    <div class="icon-chip bg-warning-soft mb-1"><i class="bi bi-stopwatch"></i></div>
                    <div class="kpi-title">Avg Days to Approval</div>
                    <div class="kpi-value text-warning">{{ $avgDaysToApproval }}</div>
                    <div class="small text-muted">moving average</div>
                    <div class="progress progress-thin mt-2"><div class="progress-bar bg-warning" style="width: {{ min(100, $avgDaysToApproval*8) }}%"></div></div>
                </div>
            </div>
        </div>
    </div>

   <style>
    .progress-strip-card {
        background: rgba(200, 16, 46, 0.05);
        border: 1px solid rgba(200, 16, 46, 0.1);
        border-radius: 1rem;
        backdrop-filter: blur(6px);
        transition: all 0.3s ease;
    }

    .progress-strip-card:hover {
        box-shadow: 0 4px 16px rgba(200, 16, 46, 0.2);
    }

    .progress-label {
        font-weight: 600;
        color: #343a40;
        font-size: 0.9rem;
    }

    .progress {
        height: 10px;
        border-radius: 20px;
        background-color: rgba(200, 16, 46, 0.1);
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        border-radius: 20px;
        transition: width 0.6s ease-in-out;
    }

    .progress-bar-year {
        background-color: #1a1a1a;
    }

    .progress-bar-fy {
        background-color: #0cb547;
    }

    .progress-bar-quarter {
        background-color: #6c757d; /* Bootstrap secondary */
    }

    .progress-strip-value {
        font-weight: 600;
        color: #232323;
    }
</style>

{{-- Progress strip --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm progress-strip-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span class="progress-label">Year</span>
                    <span class="progress-strip-value">{{ $yearPct }}%</span>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-year" style="width: {{ $yearPct }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm progress-strip-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span class="progress-label">Financial Year</span>
                    <span class="progress-strip-value">{{ $fyPct }}%</span>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-fy" style="width: {{ $fyPct }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm progress-strip-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span class="progress-label">Quarter</span>
                    <span class="progress-strip-value">{{ $qPct }}%</span>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-quarter" style="width: {{ $qPct }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm hover-raise">
                <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-activity"></i> Board Papers Submissions ({{ $calendarYear }})</h6>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" data-action="png" data-target="#submissions-line"><i class="bi bi-image"></i></button>
                        <button class="btn btn-outline-secondary" data-action="svg" data-target="#submissions-line"><i class="bi bi-filetype-svg"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="submissions-line" class="chart-box"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm hover-raise">
                <div class="card-header bg-transparent border-0"><h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart"></i> Submission Status Split</h6></div>
                <div class="card-body">
                    <div id="status-donut" class="chart-box"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm hover-raise">
                <div class="card-header bg-transparent border-0"><h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart"></i> Meetings per Committee ({{ $financialYear }})</h6></div>
                <div class="card-body">
                    <div id="committee-bars" class="chart-box"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm hover-raise">
                <div class="card-header bg-transparent border-0"><h6 class="mb-0 fw-semibold"><i class="bi bi-award"></i> Top Submitters</h6></div>
                <div class="card-body">
                    @if($topSubmitters->count())
                        <div id="top-submitters" class="chart-box"></div>
                    @else
                        <div class="text-muted">No user data available.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Heatmap --}}
    <div class="row g-3 mb-5">
        <div class="col-12">
            <div class="card h-100 shadow-sm hover-raise">
                <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-grid-3x3-gap"></i> Meeting Activity Heatmap (last 90 days)</h6>
                    @if($committeeReq)<span class="badge bg-light text-dark">Committee: {{ $committeeReq }}</span>@endif
                </div>
                <div class="card-body">
                    <div id="heatmap" class="chart-box tall"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 hover-raise">
                <div class="card-body p-0">
                    <div class="juris-header d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-1 fw-semibold">{{ setting('institution_acronym', 'ORG') }} Area of Jurisdiction</h5>
                            <p class="mb-0 text-muted">{{ setting('institution_tagline', 'Board Management System') }}</p>
                        </div>
                        <div class="d-flex align-items-center gap-2 p-2 flex-wrap">
                            <div class="input-group input-group-sm" style="width:260px">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input id="location-search" type="search" class="form-control" placeholder="Search county or sub-county…">
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggle-markers" checked>
                                <label class="form-check-label small" for="toggle-markers">Markers</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggle-heat">
                                <label class="form-check-label small" for="toggle-heat">Heatmap</label>
                            </div>
                            <button id="locate-me" class="btn btn-sm btn-outline-primary"><i class="bi bi-geo-alt"></i> Locate</button>
                            <button id="reset-map" class="btn btn-sm btn-outline-secondary">Reset</button>
                        </div>
                    </div>

                    <div class="row g-0">
                        <div class="col-lg-7">
                            <div id="twwda-map" class="rounded-start"></div>
                        </div>
                        <div class="col-lg-5">
                            <div class="p-3">
                                <div class="accordion" id="jurisdictionAccordion">
                                    {{-- Embu --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="embuh">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#embu" aria-expanded="true" aria-controls="embu">
                                                Embu County
                                            </button>
                                        </h2>
                                        <div id="embu" class="accordion-collapse collapse show" aria-labelledby="embuh" data-bs-parent="#jurisdictionAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-unstyled mb-0 county-list" data-county="Embu County">
                                                    <li><button class="link-like subc" data-name="Embu West">Embu West</button></li>
                                                    <li><button class="link-like subc" data-name="Embu East">Embu East</button></li>
                                                    <li><button class="link-like subc" data-name="Embu North">Embu North</button></li>
                                                    <li><button class="link-like subc" data-name="Mbeere North">Mbeere North</button></li>
                                                    <li><button class="link-like subc" data-name="Mbeere South">Mbeere South</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Kirinyaga --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="kirih">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#kirinyaga" aria-expanded="false" aria-controls="kirinyaga">
                                                Kirinyaga County
                                            </button>
                                        </h2>
                                        <div id="kirinyaga" class="accordion-collapse collapse" aria-labelledby="kirih" data-bs-parent="#jurisdictionAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-unstyled mb-0 county-list" data-county="Kirinyaga County">
                                                    <li><button class="link-like subc" data-name="Kirinyaga Central">Kirinyaga Central</button></li>
                                                    <li><button class="link-like subc" data-name="Kirinyaga East">Kirinyaga East</button></li>
                                                    <li><button class="link-like subc" data-name="Kirinyaga West">Kirinyaga West</button></li>
                                                    <li><button class="link-like subc" data-name="Mwea East">Mwea East</button></li>
                                                    <li><button class="link-like subc" data-name="Mwea West">Mwea West</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Meru --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="meruh">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#meru" aria-expanded="false" aria-controls="meru">
                                                Meru County
                                            </button>
                                        </h2>
                                        <div id="meru" class="accordion-collapse collapse" aria-labelledby="meruh" data-bs-parent="#jurisdictionAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-unstyled mb-0 county-list" data-county="Meru County">
                                                    <li><button class="link-like subc" data-name="Buuri East">Buuri East</button></li>
                                                    <li><button class="link-like subc" data-name="Buuri West">Buuri West</button></li>
                                                    <li><button class="link-like subc" data-name="Meru Central">Meru Central</button></li>
                                                    <li><button class="link-like subc" data-name="Imenti North">Imenti North</button></li>
                                                    <li><button class="link-like subc" data-name="Imenti South">Imenti South</button></li>
                                                    <li><button class="link-like subc" data-name="Tigania Central">Tigania Central</button></li>
                                                    <li><button class="link-like subc" data-name="Tigania East">Tigania East</button></li>
                                                    <li><button class="link-like subc" data-name="Tigania West">Tigania West</button></li>
                                                    <li><button class="link-like subc" data-name="Igembe Central">Igembe Central</button></li>
                                                    <li><button class="link-like subc" data-name="Igembe North">Igembe North</button></li>
                                                    <li><button class="link-like subc" data-name="Igembe South">Igembe South</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Nyeri --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="nyerih">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#nyeri" aria-expanded="false" aria-controls="nyeri">
                                                Nyeri County
                                            </button>
                                        </h2>
                                        <div id="nyeri" class="accordion-collapse collapse" aria-labelledby="nyerih" data-bs-parent="#jurisdictionAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-unstyled mb-0 county-list" data-county="Nyeri County">
                                                    <li><button class="link-like subc" data-name="Nyeri Central">Nyeri Central</button></li>
                                                    <li><button class="link-like subc" data-name="Tetu">Tetu</button></li>
                                                    <li><button class="link-like subc" data-name="Nyeri South">Nyeri South</button></li>
                                                    <li><button class="link-like subc" data-name="Mukurweini">Mukurweini</button></li>
                                                    <li><button class="link-like subc" data-name="Mathira East">Mathira East</button></li>
                                                    <li><button class="link-like subc" data-name="Mathira West">Mathira West</button></li>
                                                    <li><button class="link-like subc" data-name="Kieni East">Kieni East</button></li>
                                                    <li><button class="link-like subc" data-name="Kieni West">Kieni West</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Tharaka Nithi --}}
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="tnh">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tn" aria-expanded="false" aria-controls="tn">
                                                TharakaNithi County
                                            </button>
                                        </h2>
                                        <div id="tn" class="accordion-collapse collapse" aria-labelledby="tnh" data-bs-parent="#jurisdictionAccordion">
                                            <div class="accordion-body">
                                                <ul class="list-unstyled mb-0 county-list" data-county="TharakaNithi County">
                                                    <li><button class="link-like subc" data-name="Igambang’ombe">Igambang’ombe</button></li>
                                                    <li><button class="link-like subc" data-name="Maara">Maara</button></li>
                                                    <li><button class="link-like subc" data-name="Meru South">Meru South</button></li>
                                                    <li><button class="link-like subc" data-name="Tharaka North">Tharaka North</button></li>
                                                    <li><button class="link-like subc" data-name="Tharaka South">Tharaka South</button></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="small text-muted mt-3">
                                    Tip: Click a county name to zoom. Click a sub-county to drop a marker and focus the map.
                                </div>
                            </div>
                        </div>
                    </div> {{-- /row --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Recent + Quick actions --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm hover-raise">
                <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clock"></i> Recent Papers</h6>
                    <button id="export-csv" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> CSV</button>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 recent-list" id="recent-list">
                        @forelse($recentPapers as $p)
                            <li class="py-2 border-bottom">
                                <div class="fw-semibold" title="{{ $p->title }}">{{ Str::limit($p->title, 80) }}</div>
                                <div class="small text-muted">
                                    <span class="badge rounded-pill bg-light text-secondary">{{ ucfirst($p->status ?? 'Unknown') }}</span>
                                    · {{ Carbon::parse($p->created_at)->diffForHumans() }}
                                </div>
                            </li>
                        @empty
                            <li class="text-muted">No recent items.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100 shadow-sm hover-raise">
                <div class="card-header bg-transparent border-0"><h6 class="mb-0 fw-semibold"><i class="bi bi-list-stars"></i> Quick Actions</h6></div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($hasPapersRoute)
                            <a href="{{ route('board.papers.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-journal-text me-1"></i> View All Papers
                            </a>
                        @else
                            <button class="btn btn-outline-primary" disabled data-bs-toggle="tooltip" title="Route 'board.papers.index' not defined">
                                <i class="bi bi-journal-text me-1"></i> View All Papers
                            </button>
                        @endif

                        @if($hasMeetingsRoute)
                            <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-people me-1"></i> View Meetings
                            </a>
                        @else
                            <button class="btn btn-outline-secondary" disabled data-bs-toggle="tooltip" title="Route 'meetings.index' not defined">
                                <i class="bi bi-people me-1"></i> View Meetings
                            </button>
                        @endif

                        @if($hasReportRoute)
                            <a href="{{ route('reports.download', ['fy'=>$financialYear]) }}" class="btn btn-outline-success">
                                <i class="bi bi-file-earmark-arrow-down me-1"></i> Download FY Report
                            </a>
                        @else
                            <button class="btn btn-outline-success" disabled data-bs-toggle="tooltip" title="Route 'reports.download' not defined">
                                <i class="bi bi-file-earmark-arrow-down me-1"></i> Download FY Report
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> {{-- /container --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  /* Brand */
  :root{
    --brand-ink:#232323;       /* deep indigo */
    --brand-blue:#d41323;      /* primary */
    --brand-cyan:#0fe85b;      /* accent */
    --ink:#0d0d0d;
    --muted:#6b7280;
    --card-bg:#ffffff;
    --glass-border:rgba(255,255,255,.28);
    --glass-bg:rgba(255,255,255,.16);
    --ring:rgba(212,19,35,.18);
    --shadow-lg:0 18px 34px rgba(13,13,13,.10);
  }

  /* Card base */
  .kpi-card.card{
    border:0;
    border-radius:16px;
    box-shadow:var(--shadow-lg);
    position:relative;
    overflow:hidden;
  }
  .kpi-card.card::before{
    /* subtle top gradient edge to echo the hero */
    content:"";
    position:absolute; inset:0 0 auto 0; height:4px;
    background:linear-gradient(90deg,var(--brand-cyan),var(--brand-blue),var(--brand-ink));
    opacity:.95;
  }

  /* “Glass” skin (already on your cards) */
  .glass{
    background:
      radial-gradient(1200px 1200px at 20% -20%, rgba(255,255,255,.25), transparent 40%),
      linear-gradient(180deg, rgba(255,255,255,.85), rgba(255,255,255,.92));
    backdrop-filter: blur(6px);
  }

  .hover-raise{ transition: transform .18s ease, box-shadow .18s ease; }
  .hover-raise:hover{ transform: translateY(-2px); box-shadow:0 22px 44px rgba(13,13,13,.14); }

  /* Icon chips that pick up brand tints */
  .icon-chip{
    width:46px;height:46px;border-radius:12px;
    display:grid;place-items:center;font-size:20px;
    border:1px solid rgba(13,13,13,.06);
  }
  .bg-primary-soft{
    background: linear-gradient(135deg, rgba(212,19,35,.12), rgba(15,232,91,.10));
    color: var(--brand-blue);
  }
  .bg-success-soft{
    background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(34,197,94,.10));
    color:#16a34a;
  }
  .bg-info-soft{
    background: linear-gradient(135deg, rgba(15,232,91,.12), rgba(244,15,34,.10));
    color:var(--brand-cyan);
  }
  .bg-warning-soft{
    background: linear-gradient(135deg, rgba(245,158,11,.14), rgba(251,191,36,.12));
    color:#f59e0b;
  }

  /* Titles & values */
  .kpi-title{ font-size:.9rem; letter-spacing:.02em; color:var(--muted); text-transform:uppercase; }
  .kpi-value{ font-size:2rem; font-weight:800; color:var(--ink); line-height:1; }

  /* Thin progress with brand gradient fill */
  .progress.progress-thin{ height:6px; background:#f2f2f2; border-radius:999px; overflow:hidden; }
  .progress.progress-thin .progress-bar{
    background: linear-gradient(90deg, var(--brand-ink), var(--brand-blue), var(--brand-cyan));
  }

  /* Sparkline holder */
  .sparkline{ height:42px; margin-top:.25rem; }

  /* Tooltips look cleaner on light cards */
  .tooltip-inner{ font-family:inherit; font-weight:500; }
</style>

<style>
  :root{
    --hero-grad: linear-gradient(rgba(200, 16, 46, 0.85), rgba(200, 16, 46, 0.90));
    --hero-img: url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1500&q=80');
    --hero-shadow: 0 18px 38px rgba(14, 14, 14, .25);
  }
  * { font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial; }

  .hero-card{
    position: relative;
    border-radius: 20px;
    background: #171717;
    box-shadow: var(--hero-shadow);
    min-height: 156px;
  }
  .hero-overlay{
    position: absolute; inset: 0;
    background-image: var(--hero-grad), var(--hero-img);
    background-size: cover; background-position: center;
    filter: saturate(1.05);
  }
  .hero-body{ position: relative; z-index: 2; padding: 28px 28px 34px 28px; }
  .hero-ribbon{
    position: absolute; left: 12px; right: 12px; bottom: 8px; height: 6px;
    border-radius: 999px;
    background: linear-gradient(90deg, #f82032 0%, #d41323 40%, #0fe85b 100%);
    box-shadow: 0 6px 14px rgba(212, 19, 35, .35);
    z-index: 2;
  }

  .hero-logo{
    width: 64px; height: 64px; border-radius: 16px;
    display: grid; place-items: center;
    background: rgba(255,255,255,.18);
    color: #fff; font-size: 28px;
    border: 1px solid rgba(255,255,255,.22);
    backdrop-filter: blur(6px);
  }
  .hero-title{ color:#fff; letter-spacing:.2px }
  .hero-sub{ color: rgba(255,255,255,.86); font-weight:500 }

  .btn-glass{
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.28);
    backdrop-filter: blur(6px);
    transition: transform .2s ease, background .2s ease;
  }
  .btn-glass:hover{ transform: translateY(-1px); background: rgba(255,255,255,.22) }

  .hero-kpis{ gap: 6px }
  .chip{
    display: inline-flex; align-items: center; justify-content: space-between;
    gap: 10px; min-width: 240px;
    padding: 6px 10px 6px 12px; border-radius: 12px;
    background: rgba(255,255,255,.16); color:#fff;
    border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(6px);
  }
  .chip .chip-label{ opacity:.85; font-weight:600 }
  .chip .chip-value{
    padding: 4px 10px; border-radius: 999px; font-weight:700;
    background: rgba(255,255,255,.24);
  }
  .chip-light .chip-value{ background: rgba(255,255,255,.28) }
  .chip-info  .chip-value{ background: rgba(61,243,125,.28) }
  .chip-warn  .chip-value{ background: rgba(245,158,11,.30) }

  /* Dark theme friendly tweaks (optional if you already handle) */
  [data-bs-theme="dark"] .hero-card{ box-shadow: none; }
</style>

{{-- Fonts --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

{{-- Styles --}}
<style>
    :root{
        --primary:#d41323; --primary-2:#0fe85b; --success:#22c55e; --warning:#f59e0b; --danger:#ef4444;
        --muted:#64748b; --bg:#f8f8f8; --card:#ffffff; --border:#e5e7eb; --text:#0d0d0d; --nav-h:64px;
        --glass-bg: rgba(255,255,255,.72); --glass-brd: rgba(13,13,13,.12); --shadow-soft:0 10px 30px rgba(13,13,13,.08);
    }
    [data-bs-theme="dark"]{
        color-scheme: dark;
        --bg:#0d0d0d; --card:#0d0d0d; --border:#111111; --text:#e9e9e9; --glass-bg: rgba(13,13,13,.7); --glass-brd: rgba(255,255,255,.06);
    }
    *{font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,"Noto Sans"}
    body{background:var(--bg)}
    .app-offset{height:var(--nav-h)}
    .card{background:var(--card); border:1px solid var(--border); border-radius:16px}
    .hover-raise{transition:transform .25s ease, box-shadow .25s ease}
    .hover-raise:hover{transform:translateY(-2px); box-shadow:var(--shadow-soft)}
    .glass{backdrop-filter:saturate(1.3) blur(6px); background:var(--glass-bg)!important; border-color:var(--glass-brd)!important}
    .btn-gradient{background:linear-gradient(135deg,var(--primary) 0%, var(--primary-2) 100%); color:#fff; border:0}
    .btn-gradient:hover{opacity:.95; color:#fff}
    .progress-thin{height:6px; border-radius:999px}
    .header-card{position:relative}
    .header-ribbon{position:absolute; inset:auto 0 0 0; height:4px; background:linear-gradient(90deg,var(--primary), var(--primary-2))}
    .twwda-logo{width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#f6f6f6,#ececec);color:#b81926;display:flex;align-items:center;justify-content:center;font-size:22px}
    .kpi-card .kpi-title{font-size:.85rem;color:#6b7280;font-weight:600}
    [data-bs-theme="dark"] .kpi-card .kpi-title{color:#9ca3af}
    .kpi-value{font-size:2.1rem;font-weight:800;line-height:1}
    .icon-chip{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px}
    .bg-primary-soft{background:rgba(212,19,35,.12);color:#d41323}
    .bg-success-soft{background:rgba(34,197,94,.12);color:#22c55e}
    .bg-warning-soft{background:rgba(245,158,11,.12);color:#f59e0b}
    .bg-info-soft{background:rgba(15,232,91,.12);color:#0fe85b}
    .tiny-kpis{display:grid;gap:6px;text-align:right}
    .tiny-kpis .tiny-label{color:#6b7280;margin-right:6px}
    [data-bs-theme="dark"] .tiny-kpis .tiny-label{color:#94a3b8}

    .chart-box{height:340px}
    .chart-box.tall{height:420px}
    .sparkline{height:46px}

    /* Map */
    #twwda-map{height:560px;width:100%;}
    .juris-header{padding:1rem 1rem 0 1rem}
    .accordion-button{font-weight:600}
    .county-list li{padding:.25rem 0}
    .link-like{border:0;background:transparent;padding:0;color:var(--primary);text-align:left;cursor:pointer}
    .link-like:hover{text-decoration:underline}
    .search-dim{opacity:.35}

    .text-secondary-emphasis{color:#475569!important}
    [data-bs-theme="dark"] .text-secondary-emphasis{color:#94a3b8!important}
    ::-webkit-scrollbar{height:8px;width:8px}
    ::-webkit-scrollbar-thumb{background:#dd3c49;border-radius:8px}
    [data-bs-theme="dark"] ::-webkit-scrollbar-thumb{background:#334155}
</style>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.fullscreen@1.6.0/Control.FullScreen.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@1.6.0/Control.FullScreen.css"/>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  // fallback demo data; replace with an API or controller output later if needed
  const sparkData = [6, 9, 7, 12, 10, 14, 16, 12, 18, 17, 19, 21];

  const el = document.querySelector('#sparkline');
  if(!el) return;

  const opts = {
    series: [{ data: sparkData }],
    chart: { type:'area', height:42, sparkline:{ enabled:true } },
    stroke: { width:2.5, curve:'smooth', colors:['#d41323'] },
    fill: {
      type:'gradient',
      gradient:{
        shadeIntensity:.8,
        opacityFrom:.35, opacityTo:0,
        stops:[0, 60, 100],
        colorStops:[
          { offset:0, color:'#0fe85b', opacity:.35 },
          { offset:60, color:'#d41323', opacity:.18 },
          { offset:100, color:'#d41323', opacity:0 }
        ]
      }
    },
    tooltip: { enabled:true },
  };
  new ApexCharts(el, opts).render();
});
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Live time (updates every second). Uses browser locale/timezone.
    const el = document.getElementById('currentTime');
    const fmt = new Intl.DateTimeFormat([], {
      weekday: 'short', month: 'short', day: '2-digit', year: 'numeric',
      hour: '2-digit', minute: '2-digit', second: '2-digit'
    });
    const tick = () => { el.textContent = fmt.format(new Date()); };
    tick(); setInterval(tick, 1000);

    // Keep your existing tooltip + theme code if present
    const btn = document.getElementById('themeToggle');
    btn?.addEventListener('click', () => {
      const root = document.documentElement;
      const current = root.getAttribute('data-bs-theme') || 'light';
      root.setAttribute('data-bs-theme', current === 'dark' ? 'light' : 'dark');
    });
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    /* Bootstrap tooltips */
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    /* Theme toggle */
    const themeBtn = document.getElementById('themeToggle');
    themeBtn?.addEventListener('click', () => {
        const root = document.documentElement;
        const current = root.getAttribute('data-bs-theme') || 'light';
        root.setAttribute('data-bs-theme', current === 'dark' ? 'light' : 'dark');
    });

    /* ApexCharts */
    new ApexCharts(document.querySelector('#sparkline'), {
        chart: { type:'area', height:46, sparkline:{enabled:true}, toolbar:{show:false} },
        stroke: { width:2, curve:'smooth' },
        fill: { opacity:.25 },
        series: [{ data: @json($sparkCounts) }],
        colors: ['#d41323']
    }).render();

    new ApexCharts(document.querySelector('#submissions-line'), {
        chart:{ type:'line', height:340, toolbar:{show:true, tools:{download:true}} },
        series:[{ name:'Papers', data: @json($hcMonthlyValues) }],
        xaxis:{ categories: @json($hcMonthlyCategories) },
        stroke:{ width:3, curve:'smooth' },
        colors:['#d41323'],
        dataLabels:{ enabled:false },
        markers:{ size:3 },
        grid:{ borderColor:'#e5e7eb', strokeDashArray:5 }
    }).render();

    const statusSeries = @json(collect($hcStatusBreakdown)->pluck('y'));
    const statusLabels = @json(collect($hcStatusBreakdown)->pluck('name'));
    new ApexCharts(document.querySelector('#status-donut'), {
        chart:{ type:'donut', height:340, toolbar:{show:true} },
        series: statusSeries,
        labels: statusLabels,
        colors:['#22c55e','#f59e0b','#d41323','#ef4444','#0fe85b','#94a3b8'],
        legend:{ position:'bottom' },
        dataLabels:{ enabled:false },
        plotOptions:{ pie:{ donut:{ size:'66%', labels:{ show:true, total:{ show:true, label:'Total', formatter:() => statusSeries.reduce((a,b)=>a+b,0) }}}}}
    }).render();

    new ApexCharts(document.querySelector('#committee-bars'), {
        chart:{ type:'bar', height:360, toolbar:{show:true} },
        series:[{ name:'Meetings', data: @json($hcCommitteeValues) }],
        xaxis:{ categories:@json($hcCommitteeCategories), labels:{ rotate:0 } },
        plotOptions:{ bar:{ columnWidth:'45%', borderRadius:8 } },
        colors:['#0fe85b'],
        dataLabels:{ enabled:false },
        grid:{ borderColor:'#e5e7eb', strokeDashArray:5 }
    }).render();

    @if($topSubmitters->count())
    new ApexCharts(document.querySelector('#top-submitters'), {
        chart:{ type:'bar', height:360, toolbar:{show:false} },
        series:[{ name:'Papers', data: @json($topSubmitters->pluck('c')) }],
        xaxis:{ categories: @json($topSubmitters->pluck('author')), labels:{ trim:false } },
        plotOptions:{ bar:{ horizontal:true, borderRadius:8 } },
        colors:['#10b981'],
        dataLabels:{ enabled:true }
    }).render();
    @endif

    new ApexCharts(document.querySelector('#heatmap'), {
        chart:{ type:'heatmap', height:420, toolbar:{show:false} },
        series: @json($heatGrid),
        dataLabels:{ enabled:false },
        colors:['#f2f2f2'],
        plotOptions:{
            heatmap:{
                shadeIntensity: .6,
                colorScale: {
                    ranges: [
                        { from: 0,  to: 0, color: '#f6f6f6', name: '0' },
                        { from: 1,  to: 2, color: '#dfdfdf', name: '1–2' },
                        { from: 3,  to: 5, color: '#ff1a2d', name: '3–5' },
                        { from: 6,  to: 8, color: '#ff1a2d', name: '6–8' },
                        { from: 9,  to: 999, color: '#d41323', name: '9+' },
                    ]
                }
            }
        },
        xaxis:{ type:'category' },
        legend:{ position:'right', offsetY: 40 }
    }).render();

    /* Leaflet Map */
    const map = L.map('twwda-map', { zoomControl: true, fullscreenControl: true }).setView([-0.25, 37.35], 8);
    const baseOSM = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(map);
    const baseToner = L.tileLayer('https://stamen-tiles.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; Stamen' });
    const baseLayers = { 'OSM': baseOSM, 'Toner Lite': baseToner };
    const clusters = L.markerClusterGroup({ showCoverageOnHover: false, spiderfyOnMaxZoom: true }).addTo(map);

    const countyData = {
        "Embu County": { center: [-0.53, 37.55], zoom: 10, subs: { "Embu West":[-0.55,37.43], "Embu East":[-0.51,37.58], "Embu North":[-0.35,37.57], "Mbeere North":[-0.78,37.63], "Mbeere South":[-0.90,37.61] } },
        "Kirinyaga County": { center: [-0.61, 37.30], zoom: 10, subs: { "Kirinyaga Central":[-0.59,37.28], "Kirinyaga East":[-0.62,37.35], "Kirinyaga West":[-0.66,37.24], "Mwea East":[-0.74,37.40], "Mwea West":[-0.78,37.32] } },
        "Meru County": { center: [0.05, 37.65], zoom: 9, subs: { "Buuri East":[0.07,37.58], "Buuri West":[0.02,37.52], "Meru Central":[0.05,37.65], "Imenti North":[0.17,37.67], "Imenti South":[-0.02,37.63], "Tigania Central":[0.17,37.82], "Tigania East":[0.26,37.96], "Tigania West":[0.14,37.75], "Igembe Central":[0.30,37.85], "Igembe North":[0.51,37.86], "Igembe South":[0.19,37.77] } },
        "Nyeri County": { center: [-0.42, 36.95], zoom: 10, subs: { "Nyeri Central":[-0.42,36.95], "Tetu":[-0.47,36.88], "Nyeri South":[-0.54,36.95], "Mukurweini":[-0.61,37.03], "Mathira East":[-0.34,37.07], "Mathira West":[-0.35,36.99], "Kieni East":[-0.15,37.07], "Kieni West":[-0.10,36.85] } },
        "TharakaNithi County": { center: [-0.29, 37.78], zoom: 10, subs: { "Igambang’ombe":[-0.27,37.63], "Maara":[-0.18,37.62], "Meru South":[-0.23,37.65], "Tharaka North":[-0.16,38.05], "Tharaka South":[-0.35,38.03] } }
    };

    const countyStyle = { color: '#d41323', weight: 2, opacity: 0.7, fillOpacity: 0.04 };
    Object.entries(countyData).forEach(([name, cfg]) => {
        const circle = L.circle(cfg.center, { radius: 25000, ...countyStyle }).addTo(map);
        circle.bindTooltip(name, { permanent: false, direction: 'top' });
        circle.on('click', () => map.setView(cfg.center, cfg.zoom));
    });

    const heatPoints = [];
    const subMarkers = [];
    Object.entries(countyData).forEach(([county, cfg]) => {
        Object.entries(cfg.subs).forEach(([sc, coords]) => {
            const marker = L.marker(coords || cfg.center, { title: sc });
            marker.bindPopup(`<h6 class="mb-1">${sc}</h6><div class="small text-muted">County: ${county}</div>`);
            marker.on('click', () => map.setView(marker.getLatLng(), Math.max(cfg.zoom + 1, 11)));
            clusters.addLayer(marker);
            subMarkers.push({ county, name: sc, marker, center: coords || cfg.center });
            heatPoints.push([coords[0], coords[1], 1]);
        });
    });

    const heat = L.heatLayer(heatPoints, { radius: 28, blur: 16, maxZoom: 12 });
    L.control.layers(baseLayers, { 'Markers': clusters, 'Heatmap': heat }, { position:'topright', collapsed:true }).addTo(map);

    document.querySelectorAll('.county-list').forEach(list => {
        const countyName = list.getAttribute('data-county');
        list.closest('.accordion-item')?.querySelector('.accordion-button')?.addEventListener('click', () => {
            const cfg = countyData[countyName];
            if (cfg) map.setView(cfg.center, cfg.zoom);
        });
    });

    document.querySelectorAll('.subc').forEach(btn => {
        btn.addEventListener('click', () => {
            const scName = btn.dataset.name;
            const hit = subMarkers.find(m => m.name === scName);
            if (hit) { hit.marker.openPopup(); map.setView(hit.center, 12); }
        });
    });

    const searchBox = document.getElementById('location-search');
    searchBox.addEventListener('input', () => {
        const q = searchBox.value.trim().toLowerCase();
        document.querySelectorAll('.county-list li').forEach(li => {
            const name = li.textContent.trim().toLowerCase();
            li.classList.toggle('search-dim', q && !name.includes(q));
        });
        document.querySelectorAll('.accordion-button').forEach(ab => {
            const label = ab.textContent.trim().toLowerCase();
            ab.classList.toggle('search-dim', q && !label.includes(q));
        });
        if (q.length >= 3) {
            const first = subMarkers.find(m => (m.name.toLowerCase().includes(q) || m.county.toLowerCase().includes(q)));
            if (first) { first.marker.openPopup(); map.setView(first.center, 12); }
        }
    });

    document.getElementById('toggle-heat').addEventListener('change', e => {
        if (e.target.checked) heat.addTo(map); else map.removeLayer(heat);
    });
    document.getElementById('toggle-markers').addEventListener('change', e => {
        if (e.target.checked) map.addLayer(clusters); else map.removeLayer(clusters);
    });

    document.getElementById('locate-me').addEventListener('click', () => map.locate({ setView:true, maxZoom: 12 }));
    map.on('locationfound', e => {
        L.circleMarker(e.latlng, { radius:7, color:'#d41323' }).addTo(map).bindTooltip('You are here').openTooltip();
        L.circle(e.latlng, { radius: e.accuracy, color:'#d41323', opacity:.2, fillOpacity:.05 }).addTo(map);
    });

    document.getElementById('reset-map').addEventListener('click', () => {
        map.setView([-0.25, 37.35], 8);
        searchBox.value = '';
        document.querySelectorAll('.search-dim').forEach(el => el.classList.remove('search-dim'));
    });

    /* CSV Export: Recent Papers */
    document.getElementById('export-csv')?.addEventListener('click', () => {
        const rows = @json($recentPapers->map(fn($p)=>['title'=>$p->title,'status'=>$p->status,'created_at'=> (string) $p->created_at]));
        let csv = 'Title,Status,Created At\n';
        rows.forEach(r => { csv += `"${(r.title||'').replace(/"/g,'""')}",${r.status||''},${r.created_at}\n`; });
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'recent_papers.csv'; a.click();
        URL.revokeObjectURL(url);
    });
});
</script>
@endsection
