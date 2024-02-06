<?php

namespace App\Http\Controllers;

use App\Enums\EventEngagementType;
use App\Enums\JsonFieldNames;
use App\Enums\QueryField;
use App\Exceptions\Exceptions\FailActionOnModelException;
use App\Http\Requests\EventEngageRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\User;
use App\Services\EventService;
use App\Services\ModelService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
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
        $query = Event::query()->with(['categories']);

        $query = $this->modelService->applyFilters($query, $request->query(QueryField::FILTER->value));
        $query = $this->modelService->applySorts($query, $request->query(QueryField::SORT->value));

        return $query->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreEventRequest $request
     * @return Event
     * @throws Exception
     */
    public function store(StoreEventRequest $request): Event
    {
        return $this->eventService->createEvent($request->validated())->load(['categories']);
    }

    public function show(Event $event): Event
    {
        return $event->load(['creator', 'city', 'categories']);
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
            ->load('engagingUsersTypes.user');
    }
}
