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
use App\Services\ModelService;
use Exception;
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
        private readonly ModelService $modelService,
        private readonly EventService $eventService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Event::query()->with(self::DEFAULT_LOADS);

        $query = $this->modelService->applyFilters($query, $request->query(QueryField::FILTER->value));
        $query = $this->modelService->applySorts($query, $request->query(QueryField::SORT->value));

        return $query->paginate(5);
    }

    public function unpaginatedIndex(Request $request): array
    {
        $query = Event::query()->with(self::DEFAULT_LOADS);

        $query = $this->modelService->applyFilters($query, $request->query(QueryField::FILTER->value));
        $query = $this->modelService->applySorts($query, $request->query(QueryField::SORT->value));

        return $query->get()->toArray();
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
}
