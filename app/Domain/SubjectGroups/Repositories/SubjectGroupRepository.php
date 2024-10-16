<?php

namespace App\Domain\SubjectGroups\Repositories;

use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Domain\SubjectGroups\Resources\SubjectGroupResource;
use App\Domain\SubjectGroups\Resources\TeacherSubjectGroupResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SubjectGroupRepository
{
    /**
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return SubjectGroup::query()
            ->orderByDesc('id')
            ->paginate();
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getOwnSubjectGroup($filter)
    {
        // Fetch the subject groups, paginate them, and sort
        $subjectGroups = SubjectGroup::query()
            ->Filter($filter)
            ->where('teacher_id', Auth::id())
            ->with('subject') // Eager load the subject relationship
            ->orderBy('subject_id') // Sort by subject name
            ->paginate(10); // Change 10 to your desired number of items per page

        // Group the paginated results
        $groupedSubjectGroups = $subjectGroups->getCollection()->groupBy(function ($item) {
            return $item->subject->name;
        })->map(function ($group) {
            return $group->groupBy('lesson') // Further group by the lesson enum
            ->map(function ($lessonGroup) {
                return TeacherSubjectGroupResource::collection($lessonGroup); // Transform each lesson group into a resource collection
            });
        });

        // Create a new paginator instance to return the paginated results with the grouped data
        $paginatedResults = new LengthAwarePaginator(
            $groupedSubjectGroups,
            $subjectGroups->total(),
            $subjectGroups->perPage(),
            $subjectGroups->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );

// Return the paginated results
        return $paginatedResults;
    }
}
