{{-- WHO DOES WHAT --}}
<section class="card ug-sec" id="roles" data-roles="all">
    <div class="card-body">
        <h3><i class="mdi mdi-account-group-outline"></i> Who does what</h3>
        <p>Every account has one role. Your role decides which menu items you see in the sidebar and which steps of the workflow you take part in.</p>
        <table class="ug-table">
            <thead><tr><th style="width:28%">Role</th><th>Main responsibilities</th></tr></thead>
            <tbody>
                <tr><td><strong>System Administrator</strong><br><small class="text-muted">Superadmin</small></td><td>Sets up the system, manages system settings, users, roles, permissions and financial years.</td></tr>
                <tr><td><strong>Corporation Secretary</strong></td><td>Runs the meeting process: builds the almanac, initiates meetings, drafts notices, assigns officers, prepares the agenda, runs the live meeting and records minutes. Also manages users, board members, committees, directorates and divisions.</td></tr>
                <tr><td><strong>CEO</strong></td><td>Approves the almanac, internal notices, board papers, board notices and agenda changes. Signs approved documents.</td></tr>
                <tr><td><strong>Director</strong></td><td>Reviews board papers written by officers in their directorate before they reach the CEO. Can also write papers when assigned.</td></tr>
                <tr><td><strong>Division Head / Officer</strong></td><td>Writes board papers for agenda items they are assigned.</td></tr>
                <tr><td><strong>DD Internal Audit</strong></td><td>Runs Audit Committee meetings end to end and approves their notices and papers in place of the CEO.</td></tr>
                <tr><td><strong>Board Member</strong></td><td>Reads notices and board papers in the Board Portal, declares conflicts of interest and attends meetings.</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- LOGIN --}}
<section class="card ug-sec" id="login" data-roles="all">
    <div class="card-body">
        <h3><i class="mdi mdi-login"></i> Logging in</h3>
        <div class="ug-who"><span class="ug-role">Everyone</span></div>

        <h5>Your first login</h5>
        <ol>
            <li>When your account is created you receive a <strong>welcome email</strong> with your email address and a temporary password.</li>
            <li>Open the login page, enter your <span class="ug-ui">Email address</span> and the temporary <span class="ug-ui">Password</span>, then click <span class="ug-ui">Login</span>.</li>
            <li>A 6-digit code (OTP) is emailed to you. Enter it on the <span class="ug-ui">OTP Verification</span> page and click <span class="ug-ui">Verify OTP</span>.</li>
            <li>You are taken to <span class="ug-ui">Change Password</span>. Enter the temporary password as <span class="ug-ui">Current Password</span>, choose a new one (at least 8 characters, mixing upper and lower case letters and a number), confirm it, and click <span class="ug-ui">Update Password</span>.</li>
        </ol>
        <p>You cannot use any other page until you have changed your temporary password.</p>

        <h5>Every login after that</h5>
        <ol>
            <li>Enter your email and password and click <span class="ug-ui">Login</span>.</li>
            <li>Enter the 6-digit code sent to your email. The code is valid for <strong>5 minutes</strong>. If it expires or doesn't arrive, click <span class="ug-ui">Resend OTP</span>; the old code then stops working.</li>
        </ol>

        <h5>Forgotten password</h5>
        <p>Click <span class="ug-ui">Forgot Password?</span> on the login page, enter your email and click <span class="ug-ui">Email Password Reset Link</span>. The link in the email is valid for 60 minutes.</p>

        <div class="ug-warn"><i class="mdi mdi-lock-alert-outline"></i>
            <p><strong>Account lockout:</strong> after 5 wrong passwords your account is locked. Resetting your password does not unlock it. Ask the System Administrator or Corporation Secretary to unlock it from <span class="ug-ui">All Users</span>.</p>
        </div>
        <p>If you see <strong>Access Denied</strong> after logging in, your account has been deactivated or suspended. Contact the system administrator.</p>
    </div>
</section>

{{-- NAVIGATING --}}
<section class="card ug-sec" id="navigating" data-roles="all">
    <div class="card-body">
        <h3><i class="mdi mdi-compass-outline"></i> Finding your way around</h3>
        <ul>
            <li><strong>Sidebar (left):</strong> everything you can do. It only shows items for your role. Red or yellow numbers next to an item count things waiting for you.</li>
            <li><strong>Dashboard:</strong> your starting point. It shows meeting counts, the workflow pipeline, upcoming meetings and recent activity. The CEO sees <span class="ug-ui">Items Requiring Your Action</span>; Directors see <span class="ug-ui">Board Papers Awaiting Your Review</span>. Board members land on the Board Portal dashboard instead.</li>
            <li><strong>Notifications</strong> (sidebar): every event that concerns you, such as a notice approved or a paper assigned. Filter by status, type or meeting; click <span class="ug-ui">Open</span> to jump to the item, or <span class="ug-ui">Mark All Read</span>.</li>
            <li><strong>Your account</strong> (your name, top right): <span class="ug-ui">My Account</span> to update your name, phone and photo; <span class="ug-ui">Change Password</span>; and <span class="ug-ui">Logout</span>.</li>
            <li><strong>Meeting page:</strong> each meeting has its own page showing all 9 stages, what has been done and what is next. Most actions start from here. Click <span class="ug-ui">Timeline</span> for a full history.</li>
        </ul>
        <div class="ug-tip"><i class="mdi mdi-lightbulb-on-outline"></i>
            <p>Use the sidebar <strong>Notifications</strong> page rather than the bell icon in the top bar. Emails tell you something is waiting; open the item from the sidebar.</p>
        </div>
        <p>You are logged out automatically after 2 hours of inactivity.</p>
    </div>
</section>

{{-- LIFECYCLE --}}
<section class="card ug-sec" id="lifecycle" data-roles="all">
    <div class="card-body">
        <h3><i class="mdi mdi-timeline-outline"></i> The meeting lifecycle at a glance</h3>
        <p>Every board or committee meeting follows the same path. Click a step to read about it.</p>
        <div class="ug-flow">
            <a href="#almanac"><span class="s">Before</span><span class="t">Almanac</span><span class="r">CS plans the year · CEO approves</span></a>
            <a href="#initiate"><span class="s">Stage 1</span><span class="t">Initiate meeting</span><span class="r">Corporation Secretary</span></a>
            <a href="#internal-notice"><span class="s">Stage 2–3</span><span class="t">Internal notice</span><span class="r">CS drafts · CEO approves</span></a>
            <a href="#assign"><span class="s">Stage 4</span><span class="t">Assign officers</span><span class="r">CS assigns agenda items</span></a>
            <a href="#paper"><span class="s">Stage 5</span><span class="t">Write board papers</span><span class="r">Assigned officers</span></a>
            <a href="#director-review"><span class="s">Stage 6</span><span class="t">Director review</span><span class="r">Directors</span></a>
            <a href="#ceo-review"><span class="s">Stage 7</span><span class="t">CEO review</span><span class="r">CEO approves &amp; signs</span></a>
            <a href="#board-notice"><span class="s">Alongside</span><span class="t">Board notice</span><span class="r">Sent to board members</span></a>
            <a href="#prepare"><span class="s">Stage 8</span><span class="t">Meeting preparation</span><span class="r">CS finalises agenda</span></a>
            <a href="#live"><span class="s">Stage 9</span><span class="t">Live meeting</span><span class="r">Declarations &amp; attendance</span></a>
            <a href="#minutes"><span class="s">Stage 10</span><span class="t">Minutes</span><span class="r">CS records &amp; exports</span></a>
        </div>
        <p class="mt-3 mb-0 text-muted" style="font-size:13.5px">For Audit Committee meetings, the DD Internal Audit takes the Corporation Secretary's and the CEO's place at most steps. See <a href="#audit">Audit committee meetings</a>.</p>
    </div>
</section>

{{-- SETUP --}}
<section class="card ug-sec" id="setup" data-roles="superadmin">
    <div class="card-body">
        <h3><i class="mdi mdi-cog-outline"></i> First-time setup &amp; system settings</h3>
        <div class="ug-who"><span class="ug-role" data-r="superadmin">System Administrator</span></div>

        <h5>Setup wizard</h5>
        <p>The first time the system runs, every page opens the setup wizard. Click <span class="ug-ui">Start Setup</span> and complete the six steps:</p>
        <ol>
            <li><strong>Institution Identity:</strong> full name, acronym, tagline and contact details.</li>
            <li><strong>Branding &amp; Appearance:</strong> main logo, sidebar logo, letterhead (used on PDFs), favicon, CEO signature image, login background and brand colours.</li>
            <li><strong>Workflow Configuration:</strong> reference number prefix, email subject prefix, default meeting venue and the titles printed under signatures.</li>
            <li><strong>Role Display Labels:</strong> what each role is called on screen (for example "Chief Executive Officer").</li>
            <li><strong>Email Configuration:</strong> the SMTP server used to send OTPs and notifications.</li>
            <li><strong>Admin Account:</strong> your own administrator login. Click <span class="ug-ui">Complete Setup</span>.</li>
        </ol>
        <div class="ug-warn"><i class="mdi mdi-alert-outline"></i>
            <p>Finish the wizard in one sitting. If you leave it idle for too long, the answers you entered are lost.</p>
        </div>

        <h5>Changing settings later</h5>
        <p><span class="ug-path"><i class="mdi mdi-cog-outline"></i> Sidebar › System Settings</span> has the same sections as tabs: Institution, Branding, Workflow, Role Labels, Email and Landing Page. Each tab has its own save button.</p>
        <div class="ug-tip"><i class="mdi mdi-email-check-outline"></i>
            <p>After setting up email, open the <strong>Email</strong> tab, enter your address under <span class="ug-ui">Test Configuration</span> and click <span class="ug-ui">Send Test</span>. Nobody can log in without working email, because the login code is sent by email.</p>
        </div>

        <h5>Financial years</h5>
        <p><span class="ug-path"><i class="mdi mdi-calendar-clock"></i> Sidebar › Financial Years</span>. Financial years run July to June. Add the year, tick <span class="ug-ui">Set as Active</span>, and click save. Only active years can be chosen when creating an almanac.</p>
    </div>
</section>

{{-- ORGANISATION --}}
<section class="card ug-sec" id="organisation" data-roles="superadmin cs">
    <div class="card-body">
        <h3><i class="mdi mdi-sitemap"></i> Organisation structure</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span></div>
        <p>The system uses the organisation structure to decide which Director reviews each officer's board paper. Set it up in this order:</p>
        <ol>
            <li><strong>Create the Director users</strong> first (see <a href="#users">Users</a>), with the role Director.</li>
            <li><span class="ug-path">Sidebar › Directorates</span>: click <span class="ug-ui">Add Directorate</span>, enter the name, and save. Then click <span class="ug-ui">Assign Director</span> on the directorate and pick its Director.</li>
            <li><span class="ug-path">Sidebar › Divisions</span>: click <span class="ug-ui">Add Division</span>, enter the name and choose its <span class="ug-ui">Directorate</span>. Leave the directorate blank if the division reports directly to the CEO.</li>
            <li><strong>Create the Division Head users</strong> and select their division.</li>
        </ol>
        <p>When an officer submits a paper, it goes to the Director of their division's directorate. If there is no Director above them, it goes straight to the CEO.</p>
    </div>
</section>

{{-- USERS --}}
<section class="card ug-sec" id="users" data-roles="superadmin cs">
    <div class="card-body">
        <h3><i class="mdi mdi-account-cog-outline"></i> Users, roles &amp; permissions</h3>
        <div class="ug-who"><span class="ug-role" data-r="superadmin">System Administrator</span><span class="ug-role" data-r="cs">Corporation Secretary</span></div>

        <h5>Add a staff user</h5>
        <ol>
            <li><span class="ug-path"><i class="mdi mdi-account-plus-outline"></i> Sidebar › Add User</span></li>
            <li>Fill in <span class="ug-ui">Full Name</span>, <span class="ug-ui">Email Address</span>, <span class="ug-ui">Phone Number</span>, <span class="ug-ui">Staff Number</span> and <span class="ug-ui">Role</span>.</li>
            <li>For a <strong>Director</strong>, choose the <span class="ug-ui">Directorate</span> they oversee. For a <strong>Division Head</strong>, choose their <span class="ug-ui">Division</span>.</li>
            <li>Click <span class="ug-ui">Create User</span>. A password is generated and emailed to the user, who must change it on first login.</li>
        </ol>
        <div class="ug-warn"><i class="mdi mdi-alert-outline"></i>
            <p>Don't add board members here. Use <a href="#board-members">Board Members</a> instead, so their Board Portal profile is created too.</p>
        </div>

        <h5>Manage existing users</h5>
        <p><span class="ug-path"><i class="mdi mdi-account-group-outline"></i> Sidebar › All Users</span> lists every user, with filters by status and role. The icons on each row let you:</p>
        <ul>
            <li><strong>Edit</strong> the user's details or role.</li>
            <li><strong>Manage Status:</strong> <span class="ug-status">Active</span> works normally; <span class="ug-status">Inactive</span> temporarily blocks access; <span class="ug-status">Suspended</span> blocks access pending review; <span class="ug-status">Disabled</span> blocks access permanently.</li>
            <li><strong>Unlock Account:</strong> shown only for accounts locked after 5 wrong passwords.</li>
        </ul>
        <p class="text-muted" style="font-size:13.5px">If the Edit or Manage Status icons are missing, your role needs the <code>user.create</code>, <code>user.edit</code> and <code>user.activate</code> permissions (see below).</p>

        <h5>Roles and permissions</h5>
        <ul>
            <li><span class="ug-path">Roles</span> lists system roles and lets you add custom ones. Don't rename the built-in system roles; the workflow depends on their names. To change how a role is <em>displayed</em>, use System Settings › Role Labels.</li>
            <li><span class="ug-path">All Permissions</span> (System Administrator) defines permissions and their groups.</li>
            <li><span class="ug-path">Role Permissions</span> grants permissions to a role: click <span class="ug-ui">Add Role Permission</span>, pick the role, tick permissions (or <span class="ug-ui">Check All</span>) and save.</li>
        </ul>
    </div>
</section>

{{-- BOARD MEMBERS --}}
<section class="card ug-sec" id="board-members" data-roles="cs">
    <div class="card-body">
        <h3><i class="mdi mdi-account-tie-outline"></i> Board members &amp; committees</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span></div>

        <h5>Add a board member</h5>
        <ol>
            <li><span class="ug-path">Sidebar › Board Members</span> › <span class="ug-ui">Add Board Member</span>.</li>
            <li>Work through the four tabs: <strong>Personal Info</strong> (name, email, phone, ID/passport), <strong>Role &amp; Address</strong> (designation, director type, appointment and end-of-term dates, county), <strong>Qualifications</strong> and <strong>Documents</strong> (photo, ID, CV, appointment letter, gazette notice, acceptance letter; 2 MB each).</li>
            <li>Click <span class="ug-ui">Save Board Member</span>. A login is created automatically and the member receives a welcome email.</li>
        </ol>
        <p>The list shows each member's term (members whose term ends within 90 days are flagged) and whether they have a login (<span class="ug-status">Linked</span> / <span class="ug-status">No Account</span>). Use the row icons to view, edit, create a missing login, or activate/deactivate a member. Deactivating a member also blocks their login.</p>

        <h5>Committees</h5>
        <ol>
            <li><span class="ug-path">Sidebar › Board Committees</span> › <span class="ug-ui">Add Committee</span>: enter the name and description and save.</li>
            <li>On the committee card click <span class="ug-ui">Manage Members</span>, tick the members, and click <span class="ug-ui">Save Assignments</span>.</li>
        </ol>
        <p><strong>Audit committee:</strong> when creating or editing a committee, switch on <span class="ug-ui">This is an Audit Committee</span> for the committee whose meetings are run by the DD Internal Audit (see <a href="#audit">Audit committee meetings</a>). Its card then shows an <span class="ug-status">Audit Committee</span> badge.</p>
        <div class="ug-warn"><i class="mdi mdi-alert-outline"></i>
            <p>Board members only see notices, papers, calendar entries and meetings for <strong>their committees</strong>. A member who isn't in any committee sees an empty portal.</p>
        </div>
    </div>
</section>

{{-- ALMANAC --}}
<section class="card ug-sec" id="almanac" data-roles="cs ceo superadmin">
    <div class="card-body">
        <h3><i class="mdi mdi-calendar-month-outline"></i> Almanac: the annual meeting calendar</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span><span class="ug-role" data-r="ceo">CEO (approval)</span></div>
        <p>The almanac is the plan of all board and committee meetings for a financial year, with a budget. Meetings can only be started from an <strong>approved</strong> almanac.</p>

        <h5>Corporation Secretary: create and submit</h5>
        <ol>
            <li><span class="ug-path">Sidebar › Almanac</span> › <span class="ug-ui">Add New Almanac</span>. Choose the <span class="ug-ui">Financial Year</span>, enter the total <span class="ug-ui">Budget</span> and save.</li>
            <li>On the almanac, click <span class="ug-ui">Add Schedule</span> once per meeting: choose the <span class="ug-ui">Board Committee</span>, <span class="ug-ui">Meeting Date</span>, <span class="ug-ui">Purpose</span> and <span class="ug-ui">Budget</span>.</li>
            <li>Review with <span class="ug-ui">View All Schedules</span> (edit or delete as needed; you'll be asked for a reason).</li>
            <li>Click <span class="ug-ui">Submit for Approval</span> › <span class="ug-ui">Yes, Submit</span>. Status becomes <span class="ug-status">Awaiting Approval</span>.</li>
        </ol>

        <h5>CEO: approve or reject</h5>
        <ol>
            <li><span class="ug-path">Sidebar › Almanac Approvals</span> › <span class="ug-ui">Review</span>.</li>
            <li>Check the schedules and budget. A warning appears if the schedule budgets add up to more than the almanac budget.</li>
            <li>Under <span class="ug-ui">Your Decision</span> choose <span class="ug-ui">Approve Almanac</span> or <span class="ug-ui">Reject Almanac</span> (with a comment), then <span class="ug-ui">Submit Decision</span>.</li>
        </ol>
        <div class="ug-tip"><i class="mdi mdi-information-outline"></i>
            <p>No email is sent for almanac decisions. Corporation Secretary: check the almanac list for the result. A rejected almanac appears under <span class="ug-ui">Amend Almanac</span> with the CEO's comment; fix it and submit again.</p>
        </div>

        <h5>After approval</h5>
        <ul>
            <li><span class="ug-ui">Download PDF</span> to print it for signing, then <span class="ug-ui">Upload Signed</span> to store the signed copy (PDF, up to 5 MB).</li>
            <li>To add a meeting to an approved almanac, use <span class="ug-ui">Additional Schedule</span> and attach the acceptance letter (PDF/image, up to 2 MB). It shows an <span class="ug-status">Added</span> badge.</li>
        </ul>
    </div>
</section>

{{-- STAGE 1 --}}
<section class="card ug-sec" id="initiate" data-roles="cs ddia superadmin">
    <div class="card-body">
        <h3><span class="num">1</span> Initiate a meeting</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span><span class="ug-role" data-r="ddia">DD Internal Audit (Audit Committee)</span></div>
        <ol>
            <li><span class="ug-path"><i class="mdi mdi-rocket-launch-outline"></i> Sidebar › Meeting Initiation</span>. Meetings from the approved almanac are grouped by committee. Date colours: <strong>blue</strong> upcoming, <strong>amber</strong> within 7 days, <strong>red</strong> past, <strong>green</strong> already initiated.</li>
            <li>Find the meeting and click <span class="ug-ui">Initiate</span>, then confirm.</li>
            <li>The meeting is created with a title (e.g. "The 3rd Finance Committee Meeting") and a reference number. The button changes to <span class="ug-ui">View Meeting</span>.</li>
        </ol>
        <p>The <strong>meeting page</strong> is where you'll do most of the following steps. It shows the stages, the days left until the meeting, and what's needed next.</p>
        <div class="ug-tip"><i class="mdi mdi-information-outline"></i>
            <p>If the page says <strong>No Active Almanac Found</strong>, the current almanac hasn't been approved by the CEO yet.</p>
        </div>
    </div>
</section>

{{-- STAGE 2-3 --}}
<section class="card ug-sec" id="internal-notice" data-roles="cs ceo ddia superadmin">
    <div class="card-body">
        <h3><span class="num">2</span> Internal notice &amp; CEO approval</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span><span class="ug-role" data-r="ceo">CEO</span></div>
        <p>The internal notice tells staff about the meeting and lists its agenda. The agenda items become the topics officers write papers for.</p>

        <h5>Corporation Secretary: draft and submit</h5>
        <ol>
            <li>On the meeting page click <span class="ug-ui">Create Internal Notice</span>.</li>
            <li>Fill in <span class="ug-ui">Reference Number</span>, <span class="ug-ui">Reference Date</span>, <span class="ug-ui">Subject</span> and the <span class="ug-ui">Body</span>. Venue, date and time are pre-filled from the meeting.</li>
            <li>Add each agenda item with <span class="ug-ui">Add Agenda Item</span>.</li>
            <li><span class="ug-ui">Save Draft</span> to continue later, or <span class="ug-ui">Submit to CEO</span>. The CEO is notified by email.</li>
        </ol>

        <h5>CEO: review</h5>
        <ol>
            <li><span class="ug-path">Sidebar › Internal Notices</span> (Pending tab) › open the notice, or use <span class="ug-ui">Items Requiring Your Action</span> on the dashboard.</li>
            <li>Click <span class="ug-ui">Approve Notice</span>: add optional comments, sign on the pad if you wish, and <span class="ug-ui">Confirm Approval</span>.</li>
            <li>Or click <span class="ug-ui">Reject / Request Changes</span>, give the reason, and <span class="ug-ui">Confirm Rejection</span>.</li>
        </ol>
        <p>The Corporation Secretary is emailed either way. After a rejection, the meeting page shows the feedback and a <span class="ug-ui">Revise &amp; Resubmit</span> button.</p>
        <div class="ug-warn"><i class="mdi mdi-alert-outline"></i>
            <p>A <strong>resubmitted</strong> notice doesn't send a new email to the CEO. After resubmitting, let the CEO know it's ready.</p>
        </div>

        <h5>Special approval (when the CEO is unavailable)</h5>
        <p>If a submitted notice can't wait for the CEO, the Corporation Secretary can click <span class="ug-ui">Special Approve</span> on the meeting page, enter a justification and confirm. The notice is marked as specially approved with your name and reason, and the CEO receives a copy for information.</p>
    </div>
</section>

{{-- STAGE 4 --}}
<section class="card ug-sec" id="assign" data-roles="cs ddia divhead director">
    <div class="card-body">
        <h3><span class="num">4</span> Assign officers</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span><span class="ug-role" data-r="divhead">Officers (receive)</span><span class="ug-role" data-r="director">Directors (receive)</span></div>

        <h5>Corporation Secretary: assign</h5>
        <ol>
            <li>Once the notice is approved, click <span class="ug-ui">Assign Officers</span> on the meeting page. The agenda items from the notice are filled in for you.</li>
            <li>For each item, choose who will write the paper under <span class="ug-ui">Assign To</span>. Directors are marked with a star (their papers go straight to the CEO).</li>
            <li>Use <span class="ug-ui">Add Another Assignment</span> for extra items, then click <span class="ug-ui">Assign &amp; Notify Officers</span>.</li>
        </ol>
        <p>Each officer is emailed, and so is their Director. The <span class="ug-ui">Assignment Dashboard</span> shows who has opened their assignment (read receipts) and who has submitted a paper. Use <span class="ug-ui">Assign More</span> to add assignments later.</p>

        <h5>Officers: see your assignments</h5>
        <p><span class="ug-path"><i class="mdi mdi-clipboard-account-outline"></i> Sidebar › My Assignments</span>. New assignments are marked <span class="ug-status">NEW</span>. Click <span class="ug-ui">View</span> to see the details and the internal notice, then <span class="ug-ui">Create Board Paper</span>.</p>
    </div>
</section>

{{-- STAGE 5 --}}
<section class="card ug-sec" id="paper" data-roles="divhead director cs ddia">
    <div class="card-body">
        <h3><span class="num">5</span> Prepare a board paper</h3>
        <div class="ug-who"><span class="ug-role" data-r="divhead">Division Head / Officer</span><span class="ug-role" data-r="director">Director</span></div>
        <ol>
            <li>From your assignment click <span class="ug-ui">Create Board Paper</span>.</li>
            <li><strong>Paper Identity:</strong> enter the <span class="ug-ui">Agenda Item No.</span> (e.g. 5.1) and <span class="ug-ui">Paper Title</span>, plus the department, reporting period and paper type if relevant.</li>
            <li><strong>Paper Content:</strong> fill in each section. All are required except 3.0:
                <ul>
                    <li>1.0 Background / Introduction</li>
                    <li>2.0 Executive Summary and 2.1 Body of the Board Paper</li>
                    <li>3.0 Challenges Faced (optional)</li>
                    <li>4.0 Items for the Board to Note / Decisions / Proposal</li>
                    <li>5.0 Request to the Committee</li>
                </ul>
            </li>
            <li><strong>Annexures:</strong> click <span class="ug-ui">Add Annexure</span>, give it a title and attach the file (PDF, Word, Excel, PowerPoint or image; up to 10 MB each).</li>
            <li>Click <span class="ug-ui">Save Draft</span> (or <span class="ug-ui">Submit for Review</span>, which also saves).</li>
            <li><strong>On the Board Paper page that opens, click <span class="ug-ui">Submit for Review</span> again.</strong></li>
        </ol>
        <div class="ug-warn"><i class="mdi mdi-alert-outline"></i>
            <p><strong>Important:</strong> the button on the form only saves your paper. It isn't sent for review until you click <span class="ug-ui">Submit for Review</span> on the Board Paper page. Check that the status no longer reads <span class="ug-status">Draft</span>.</p>
        </div>

        <h5>What happens next</h5>
        <ul>
            <li>If you report to a Director, the paper goes to them for review (stage 6).</li>
            <li>If you are the Director, or there's no Director above you, it goes straight to the CEO (stage 7).</li>
        </ul>

        <h5>Changing a submitted or rejected paper</h5>
        <ul>
            <li><span class="ug-ui">Recall Paper</span> pulls your paper back to draft, as long as the reviewer hasn't acted yet.</li>
            <li>If a paper is returned, the page shows the reviewer's feedback. Click <span class="ug-ui">Revise Paper</span>, make the changes, save, then click <span class="ug-ui">Resubmit for Review</span>.</li>
            <li>Once the CEO approves it, <span class="ug-ui">Download Approved PDF</span> gives you the signed copy.</li>
        </ul>

        <h5>Already-signed papers (Corporation Secretary / DD Internal Audit)</h5>
        <p>If you have a paper the CEO has already signed, upload it under <span class="ug-ui">Upload Signed Board Paper</span> (PDF/Word, up to 20 MB) and tick <span class="ug-ui">This paper is already signed by the CEO</span>. It is marked approved without going through review.</p>
    </div>
</section>

{{-- STAGE 6 --}}
<section class="card ug-sec" id="director-review" data-roles="director divhead">
    <div class="card-body">
        <h3><span class="num">6</span> Director review</h3>
        <div class="ug-who"><span class="ug-role" data-r="director">Director</span></div>
        <ol>
            <li><span class="ug-path"><i class="mdi mdi-file-document-outline"></i> Sidebar › Paper Reviews</span>. The badge shows how many papers are waiting. You can also start from the dashboard.</li>
            <li>Click <span class="ug-ui">Review</span> on a paper. Read it in the window (or <span class="ug-ui">Open Full View</span> / <span class="ug-ui">View Original PDF</span>).</li>
            <li>Either add optional comments and click <span class="ug-ui">Approve Paper</span>, or enter a reason and click <span class="ug-ui">Reject Paper</span>.</li>
        </ol>
        <ul>
            <li><strong>Approved:</strong> the paper goes to the CEO, and the author is emailed.</li>
            <li><strong>Rejected:</strong> the author is emailed with your reason and must revise and resubmit it to you.</li>
        </ul>
        <p>Changed your mind? Papers you approved appear under <span class="ug-ui">Forwarded to CEO — Can Be Recalled</span> until the CEO acts. Click <span class="ug-ui">Recall</span> to bring one back for review.</p>
    </div>
</section>

{{-- STAGE 7 --}}
<section class="card ug-sec" id="ceo-review" data-roles="ceo ddia superadmin">
    <div class="card-body">
        <h3><span class="num">7</span> CEO paper review</h3>
        <div class="ug-who"><span class="ug-role" data-r="ceo">CEO</span><span class="ug-role" data-r="ddia">DD Internal Audit (Audit Committee)</span></div>
        <ol>
            <li><span class="ug-path">Sidebar › Board Papers</span>. Meetings with papers waiting are listed. Click <span class="ug-ui">Review Papers</span>.</li>
            <li>Click <span class="ug-ui">Review</span> on a paper. The paper, the original document and the annexures are shown.</li>
            <li>Optional: add <strong>sticky notes</strong> (choose a colour, type the note, <span class="ug-ui">Add Sticky Note</span>). They are visible on the paper.</li>
            <li>Click <span class="ug-ui">Approve Paper</span>, add optional comments, <strong>sign on the signature pad</strong> (required for the CEO), and <span class="ug-ui">Confirm Approval</span>.</li>
            <li>Or click <span class="ug-ui">Reject Paper</span> with a reason. It goes back to the Director and then the author for revision.</li>
        </ol>
        <p>Approved papers get your signature on the PDF and become visible to board members in the Board Portal. <strong>When every paper for a meeting is approved</strong>, the meeting moves to stage 8 and the Corporation Secretary is emailed that it's ready for preparation.</p>
    </div>
</section>

{{-- BOARD NOTICE --}}
<section class="card ug-sec" id="board-notice" data-roles="cs ceo ddia director">
    <div class="card-body">
        <h3><i class="mdi mdi-bullhorn-outline"></i> Board notice to members</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span><span class="ug-role" data-r="ceo">CEO (approval)</span></div>
        <p>The board notice is the official notice of the meeting sent to board members, with the agenda, as a signed PDF on letterhead. It can be prepared while papers are being written.</p>
        <div class="ug-warn"><i class="mdi mdi-alert-outline"></i>
            <p>The meeting agenda used in <a href="#prepare">Meeting preparation</a> comes from the board notice, so it must be <strong>approved or sent</strong> before you can prepare the meeting.</p>
        </div>

        <h5>Corporation Secretary: create and submit</h5>
        <ol>
            <li><span class="ug-path"><i class="mdi mdi-bulletin-board"></i> Sidebar › Board Notices Hub</span>. Under <span class="ug-ui">Initiate a Board Notice</span>, click <span class="ug-ui">Create Notice</span> next to the meeting.</li>
            <li><strong>Notice Identity:</strong> check the reference number and date, and choose <span class="ug-ui">21-Day Notice</span> or <span class="ug-ui">7-Day Notice</span>.</li>
            <li><strong>Board Members:</strong> tick the addressees (<span class="ug-ui">Select All</span> is available).</li>
            <li><strong>Notice Content:</strong> the subject, opening paragraph and agenda are pre-filled. Edit as needed, drag agenda items to reorder, and <span class="ug-ui">Add Item</span> for more.</li>
            <li><strong>Closing &amp; CC:</strong> closing text and any CC recipients.</li>
            <li>Click <span class="ug-ui">Preview Notice</span> to check it, then <span class="ug-ui">Submit to CEO for Approval</span>.</li>
        </ol>

        <h5>CEO: approve and sign</h5>
        <ol>
            <li><span class="ug-path">Sidebar › Board Notices</span> › <span class="ug-ui">Review</span>.</li>
            <li>Choose <span class="ug-ui">Approve &amp; Sign</span>, confirm your name and title, sign on the pad, and <span class="ug-ui">Confirm Decision</span>. Or choose <span class="ug-ui">Reject with Comments</span>.</li>
        </ol>

        <h5>Corporation Secretary: send</h5>
        <ol>
            <li>Once approved, open the notice preview and click <span class="ug-ui">Send to Board Members</span> › <span class="ug-ui">Yes, Send Now</span>.</li>
            <li>Every member of the meeting's committee is emailed the signed PDF and sees it under Official Notices in the portal.</li>
        </ol>
        <p>After sending you can click <span class="ug-ui">Send Reminder</span> (re-sends the notice as a reminder) or <span class="ug-ui">Reschedule Notice</span> (creates a new version to edit and send). <span class="ug-ui">History</span> shows the notice's full audit trail.</p>
        <div class="ug-tip"><i class="mdi mdi-information-outline"></i>
            <p>The notice is sent to <strong>all members of the committee</strong>, not only those ticked as addressees. Keep committee membership up to date.</p>
        </div>
    </div>
</section>

{{-- STAGE 8 --}}
<section class="card ug-sec" id="prepare" data-roles="cs ceo ddia">
    <div class="card-body">
        <h3><span class="num">8</span> Meeting preparation</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span><span class="ug-role" data-r="ceo">CEO (approves changes)</span></div>
        <p>Here you finalise the agenda that board members will see and link each item to its approved paper.</p>

        <h5>Corporation Secretary</h5>
        <ol>
            <li>On the meeting page click <span class="ug-ui">Manage Preparations</span>. The agenda is filled in from the board notice, and approved papers are linked to matching items.</li>
            <li><strong>Reorder</strong> items by dragging them. The new order saves immediately.</li>
            <li>For an item without a paper, click <span class="ug-ui">Assign Board Paper</span> and pick from the approved papers.</li>
            <li>To add a late item, click <span class="ug-ui">New Agenda Item</span>: enter a number, title and description, and optionally attach the paper and annexures (up to 20 MB each). It needs CEO approval.</li>
            <li>To withdraw an item, click the <strong>pull-down</strong> (eye-off) icon and give a justification. It is hidden from board members once the CEO approves.</li>
            <li>When nothing is waiting for the CEO, click <span class="ug-ui">Finalize Preparation</span> › <span class="ug-ui">Yes, Finalize</span>.</li>
        </ol>
        <p>The meeting page shows a readiness checklist: all papers approved, board notice sent, preparation finalised.</p>

        <h5>CEO: approve agenda changes</h5>
        <p><span class="ug-path">Sidebar › Agenda Items</span> lists new items and pull-down requests. Click <span class="ug-ui">Review</span>, then <span class="ug-ui">Approve</span> (signing the attached paper for new items) or <span class="ug-ui">Reject</span> with a reason. The Corporation Secretary is notified in the system, not by email.</p>
    </div>
</section>

{{-- STAGE 9 --}}
<section class="card ug-sec" id="live" data-roles="cs ddia board ceo">
    <div class="card-body">
        <h3><span class="num">9</span> The live meeting</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span><span class="ug-role" data-r="board">Board Members</span></div>

        <h5>Corporation Secretary: start and run the meeting</h5>
        <ol>
            <li>On the meeting day, click <span class="ug-ui">Start Meeting</span> on the meeting page, then <span class="ug-ui">Enter Live Meeting</span>.</li>
            <li>The live page has tabs: <span class="ug-ui">Board Attendance</span>, <span class="ug-ui">Conflict Register</span>, <span class="ug-ui">Other Attendees</span>, <span class="ug-ui">Board Papers</span>, <span class="ug-ui">Minutes</span> and <span class="ug-ui">Timeline</span>.</li>
            <li><strong>Other Attendees:</strong> click <span class="ug-ui">Add Attendee</span> for staff, external guests or invitees and mark them present or absent.</li>
        </ol>

        <h5>Conflict of interest declarations &amp; attendance</h5>
        <p>Every board member must complete a signed declaration. <strong>A member is marked present only once their declaration is complete.</strong> Members can declare themselves in the Board Portal, or the Corporation Secretary can help them by clicking <span class="ug-ui">Declare</span> next to their name.</p>
        <ol>
            <li><strong>Declare:</strong> answer whether you have a conflict of interest in any agenda item. If yes, give the person's name, the nature of the interest and its value. Click <span class="ug-ui">Submit Declaration</span>.</li>
            <li><strong>Verify:</strong> a 6-digit code is emailed to the member. Enter it and click <span class="ug-ui">Verify OTP</span>. It is valid for 10 minutes; <span class="ug-ui">Resend OTP</span> is available after 60 seconds.</li>
            <li><strong>Sign:</strong> draw the signature on the pad and click <span class="ug-ui">Sign &amp; Complete</span> (portal: <span class="ug-ui">Sign Declaration</span>).</li>
        </ol>
        <p>The <span class="ug-ui">Board Attendance</span> tab shows who is present and who is still awaiting their declaration. The attendance register and the conflict register can each be exported as <span class="ug-ui">PDF</span> or <span class="ug-ui">Excel</span>.</p>
    </div>
</section>

{{-- STAGE 10 --}}
<section class="card ug-sec" id="minutes" data-roles="cs ddia">
    <div class="card-body">
        <h3><span class="num">10</span> Minutes</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span></div>
        <ol>
            <li>In the live meeting, open the <span class="ug-ui">Minutes</span> tab.</li>
            <li>Members present, absent with apology and in attendance are filled in automatically from the declarations and other attendees.</li>
            <li>Check the <span class="ug-ui">Minutes Title</span>, then for each agenda item record the <span class="ug-ui">Discussion</span>, <span class="ug-ui">Comments &amp; Recommendations</span> and <span class="ug-ui">Resolution</span>, and choose who <span class="ug-ui">Proposed By</span> and <span class="ug-ui">Seconded By</span>.</li>
            <li>Minutes <strong>save automatically</strong> as you type. Watch the save indicator.</li>
            <li>Click <span class="ug-ui">Download Word</span> for a formatted Word document with signature lines for the CEO / Secretary and the Chairperson.</li>
        </ol>
        <p>Follow-up actions from the meeting can be tracked in <a href="#records">Board Resolutions</a>.</p>
    </div>
</section>

{{-- PORTAL --}}
<section class="card ug-sec" id="portal" data-roles="board ceo">
    <div class="card-body">
        <h3><i class="mdi mdi-view-dashboard-variant-outline"></i> Board member portal</h3>
        <div class="ug-who"><span class="ug-role" data-r="board">Board Members</span><span class="ug-role" data-r="ceo">CEO</span></div>
        <p>After logging in, board members land on the portal dashboard. It shows upcoming and live meetings, papers to read, recent notices, a countdown to the next meeting and how complete your profile is. When a meeting is in progress, a red banner with <span class="ug-ui">Join Now</span> appears.</p>
        <table class="ug-table">
            <thead><tr><th style="width:28%">Menu</th><th>What you can do</th></tr></thead>
            <tbody>
                <tr><td><strong>Board Calendar</strong></td><td>See the year's scheduled meetings for your committees, by month.</td></tr>
                <tr><td><strong>My Committees</strong></td><td>See the committees you belong to and their members.</td></tr>
                <tr><td><strong>Official Notices</strong></td><td>Read meeting notices sent to you, search them and <span class="ug-ui">Download PDF</span>. Notices replaced by a newer version are marked <span class="ug-status">Superseded</span>.</td></tr>
                <tr><td><strong>Board Papers</strong></td><td>Read CEO-approved papers for upcoming meetings. Click <span class="ug-ui">Read</span> to open the paper with its annexures, download it, and add <strong>private notes</strong> that only you can see.</td></tr>
                <tr><td><strong>Active Meetings</strong></td><td>Join a meeting that has started. Click <span class="ug-ui">Join Meeting</span> and complete your conflict of interest declaration (see <a href="#live">stage 9</a>). After that you can see the agenda, papers, attendance and conflict register.</td></tr>
                <tr><td><strong>Document Repository</strong></td><td>Browse and download board documents by category.</td></tr>
                <tr><td><strong>Profile</strong> (Quick Actions)</td><td>Update your details, photo, CV, qualifications and signature specimen.</td></tr>
            </tbody>
        </table>
        <div class="ug-tip"><i class="mdi mdi-lightbulb-on-outline"></i>
            <p>If a meeting shows <strong>Not Started</strong>, wait for the Corporation Secretary to start it. If your portal is empty, ask the Corporation Secretary to add you to your committees.</p>
        </div>
    </div>
</section>

{{-- AUDIT --}}
<section class="card ug-sec" id="audit" data-roles="ddia cs ceo">
    <div class="card-body">
        <h3><i class="mdi mdi-shield-check-outline"></i> Audit committee meetings</h3>
        <div class="ug-who"><span class="ug-role" data-r="ddia">DD Internal Audit</span></div>
        <p>Audit Committee meetings follow the same steps, but the DD Internal Audit runs them and approves in place of the CEO, to keep audit independent:</p>
        <table class="ug-table">
            <thead><tr><th style="width:34%">Step</th><th>For Audit Committee meetings</th></tr></thead>
            <tbody>
                <tr><td>Initiate meeting</td><td>DD Internal Audit, via <span class="ug-ui">Meeting Initiation</span> (only audit meetings are shown).</td></tr>
                <tr><td>Internal notice approval</td><td>DD Internal Audit, via <span class="ug-ui">Notice Approvals</span>. The CEO receives a copy.</td></tr>
                <tr><td>Officer assignment</td><td>Internal Audit officers and Internal Audit Division Heads.</td></tr>
                <tr><td>Board paper approval</td><td>DD Internal Audit, via <span class="ug-ui">Paper Approvals</span> (signature optional). The CEO receives a copy.</td></tr>
                <tr><td>Board notice</td><td>DD Internal Audit approves and signs it directly with <span class="ug-ui">Approve &amp; Sign</span> (use the button on the preview page to capture the signature).</td></tr>
                <tr><td>Agenda changes</td><td>New items and pull-downs take effect immediately, with no CEO approval needed.</td></tr>
                <tr><td>Preparation, live meeting, minutes</td><td>DD Internal Audit, as described for the Corporation Secretary. A <span class="ug-ui">Preparation</span> menu item appears when an audit meeting reaches stage 8.</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- RECORDS --}}
<section class="card ug-sec" id="records" data-roles="cs ddia">
    <div class="card-body">
        <h3><i class="mdi mdi-archive-outline"></i> Records &amp; resolutions</h3>
        <div class="ug-who"><span class="ug-role" data-r="cs">Corporation Secretary</span><span class="ug-role" data-r="ddia">DD Internal Audit (Audit Logs)</span></div>
        <ul>
            <li><strong>Board Resolutions:</strong> <span class="ug-path">Sidebar › Board Resolutions</span> › <span class="ug-ui">Record New Resolution</span>. Choose the meeting, enter the resolution, the responsible officer and a due date. Update progress with the status dropdown: <span class="ug-status">Pending</span>, <span class="ug-status">In Progress</span>, <span class="ug-status">Completed</span>, <span class="ug-status">Overdue</span>.</li>
            <li><strong>Document Repository:</strong> store board documents by category for members to download.</li>
            <li><strong>Audit Logs:</strong> a record of who did what and when. Filter by action, user, table and dates, and download as PDF.</li>
            <li><strong>Meeting timeline:</strong> every meeting page has a <span class="ug-ui">Timeline</span> with its full history.</li>
        </ul>
    </div>
</section>

{{-- FAQ --}}
<section class="card ug-sec" id="faq" data-roles="all">
    <div class="card-body">
        <h3><i class="mdi mdi-help-circle-outline"></i> Troubleshooting</h3>
        <table class="ug-table">
            <thead><tr><th style="width:38%">Problem</th><th>What to do</th></tr></thead>
            <tbody>
                <tr><td>I didn't receive the login code</td><td>Check your spam folder, wait a minute, then click <span class="ug-ui">Resend OTP</span>. Codes expire after 5 minutes.</td></tr>
                <tr><td>"Session expired. Please login again."</td><td>Your login took too long. Go back to the login page and start again.</td></tr>
                <tr><td>"Your account is locked"</td><td>Ask the System Administrator or Corporation Secretary to unlock it from All Users.</td></tr>
                <tr><td>My board paper is still "Draft"</td><td>Open the paper and click <span class="ug-ui">Submit for Review</span> on the paper page. Saving the form alone doesn't submit it.</td></tr>
                <tr><td>Meeting Initiation shows "No Active Almanac Found"</td><td>The almanac must be approved by the CEO first.</td></tr>
                <tr><td>Meeting Preparation says "No agenda items available yet"</td><td>The board notice for the meeting must be approved or sent first.</td></tr>
                <tr><td>Can't finalise preparation</td><td>Some agenda items are still waiting for CEO approval. Ask the CEO to review them under Agenda Items.</td></tr>
                <tr><td>A board member isn't shown as present</td><td>Attendance is recorded only after the member's conflict of interest declaration is verified with the OTP and signed.</td></tr>
                <tr><td>A board member can't see notices or papers</td><td>Add them to the right committee (Board Committees › Manage Members). Papers appear only after CEO approval.</td></tr>
                <tr><td>A menu item I need is missing</td><td>The sidebar depends on your role. Ask the System Administrator to check your role.</td></tr>
            </tbody>
        </table>
    </div>
</section>
