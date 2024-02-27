<?php

namespace App\Http\Controllers;

use App\Enums\JsonFieldNames;
use App\Enums\QueryField;
use App\Exceptions\Exceptions\FailActionOnModelException;
use App\Http\Requests\EventEngageRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;
use App\Services\UrlQuery\UrlQueries\Filters\EventsFilter;
use App\Services\UrlQuery\UrlQueries\Sorts\BaseSort;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EventController extends Controller
{
    const DEFAULT_LOADS = [
      'creator',
      'city',
      'categories',
      'engagingUsersTypes.user',
    ];

    public function __construct(
        private readonly EventsFilter $filterService,
        private readonly BaseSort $sortService,
        private readonly EventService $eventService,
    ) {}

    public function index(Request $request): LengthAwarePaginator
    {
        $perPage = $request->query(QueryField::PER_PAGE->value);

        return $this->indexData($request)->paginate($perPage);
    }

    public function unpaginatedIndex(Request $request): array
    {
        return $this->indexData($request)->get()->toArray();
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreEventRequest $request
     * @return Event
     * @throws Exception
     */
    public function store(StoreEventRequest $request): Event
    {
        return $this->eventService->createEvent($request->validated())->load(self::DEFAULT_LOADS);
    }

    public function show(Event $event): Event
    {
        return $event->load(self::DEFAULT_LOADS);
    }

    /**
     * @throws FailActionOnModelException
     */
    public function update(UpdateEventRequest $request, Event $event): Event
    {
        return $this->eventService->updateEvent($request->validated(), $event);
    }

    /**
     * @throws FailActionOnModelException
     */
    public function destroy(Event $event): array
    {
        $this->eventService->deleteEvent($event);

        return [JsonFieldNames::MESSAGE->value => "Deleted event with id: $event->id"];
    }

    /**
     * @throws FailActionOnModelException
     */
    public function engageEvent(EventEngageRequest $request, Event $event): Event
    {
        return $this->eventService
            ->updateEventEngagement($event, $request->validated())
            ->load(self::DEFAULT_LOADS);
    }

    private function indexData(Request $request): Builder
    {
        $query = Event::query()->with(self::DEFAULT_LOADS);
        $query = $this->filterService->applyFilters($query, $request->query(QueryField::FILTER->value));

        return $this->sortService->applySorts($query, $request->query(QueryField::SORT->value));
    }
}
