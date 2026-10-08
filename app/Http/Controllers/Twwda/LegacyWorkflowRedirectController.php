<?php

namespace App\Http\Controllers\Twwda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Old notifications and emails linked to /twwda/... before the workflow moved to /workflow/...
 * Those links are stored in the database and sitting in people's inboxes, so map them to the current pages.
 */
class LegacyWorkflowRedirectController extends Controller
{
    /** Old paths that don't exist under /workflow with the same shape. */
    private const RENAMED = [
        '#^director/papers/(\d+)(?:/review)?$#'           => 'director/board-papers/$1',
        '#^ceo/papers/(\d+)/review$#'                     => 'ceo/meeting-papers/$1/review',
        '#^officer/papers/(\d+)$#'                        => 'board-papers/$1',
        '#^director/meetings/(\d+)/assignments$#'         => 'meetings/$1/assignment-dashboard',
    ];

    public function __invoke(Request $request, string $path = 'dashboard')
    {
        $path = trim($path, '/');

        // Board notice links sent to board members belong in the Board Portal
        if (preg_match('#^board-notices/(\d+)/preview$#', $path, $m) && $this->isBoardMemberOnly($request)) {
            return redirect()->route('board.portal.notice.show', $m[1]);
        }

        foreach (self::RENAMED as $pattern => $replacement) {
            if (preg_match($pattern, $path)) {
                $path = preg_replace($pattern, $replacement, $path);
                break;
            }
        }

        $query = $request->getQueryString();

        return redirect('/workflow/' . $path . ($query ? '?' . $query : ''), 301);
    }

    private function isBoardMemberOnly(Request $request): bool
    {
        $user = $request->user();

        return $user
            && $user->hasRole('Board Member')
            && !$user->hasAnyRole(['Superadmin', 'Corporation Secretary', 'CEO', 'Director', 'DD Internal Audit']);
    }
}
