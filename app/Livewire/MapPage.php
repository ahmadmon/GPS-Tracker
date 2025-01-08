<?php

namespace App\Livewire;

use App\Facades\Acl;
use App\Models\Device;
use App\Models\Geofence;
use App\Models\Trip;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

#[Title('نقشه')]
class MapPage extends Component
{
    use LivewireAlert;

    #[Url(as: 'q')]
    #[Validate('string', as: 'جستجو')]
    public string $search = '';

    #[Validate([
        'selected' => 'array|max:4',
        'selected.*' => ['integer', 'exists:devices,id']
    ], as: 'دستگاه انتخابی')]
    public array $selected = [];
    public $dateTimeRange;
    public array $deviceLocations = [];
    public $trips = [];
    public $geofences = [];

    public int $take = 10;

    public bool $onlineMode = false;

    public function rules(): array
    {
        return [
            'dateTimeRange' => 'required|date|before_or_equal:' . now()->format('Y-m-d')
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'dateTimeRange' => 'تاریخ',
        ];
    }

    public function mount(Request $request): void
    {
        Acl::authorize('show-map');

        $this->trips = collect([]);

        $this->search = $request->query('q') ?? '';
    }

    public function render(): View
    {
        $this->updateDeviceLocation();

        return view('livewire.map-page', [
            'devices' => $this->getDevices()
        ]);
    }

    protected function getDevices()
    {
        $user = auth()->user();
        $role = Acl::getRole();
        if ($role === 'user') {
            return $user->devices()
                ->with(['user', 'vehicle'])
                ->where('status', 1)
                ->when($this->search !== '', function ($q) {
                    $q->whereLike('name', "%{$this->search}%")
                        ->orWhereLike('serial', "%{$this->search}%");
                })
                ->orderByDesc('connected_at')
                ->take($this->take)
                ->cursor();

        } elseif ($role === 'manager') {
            return Device::whereIn('user_id', $user->subsets()->pluck('id')->merge([$user->id]))
                ->with(['user', 'vehicle'])
                ->where('status', 1)
                ->when($this->search !== '', function ($q) {
                    $q->whereLike('name', "%{$this->search}%")
                        ->orWhereLike('serial', "%{$this->search}%");
                })
                ->orderByDesc('connected_at')
                ->take($this->take)
                ->cursor();
        } else {
            return Device::with(['vehicle', 'user'])
                ->where('status', 1)
                ->when($this->search !== '', function ($q) {
                    $q->whereLike('name', "%{$this->search}%")->orWhereLike('serial', "%{$this->search}%");
                })
                ->orderByDesc('connected_at')
                ->take($this->take)
                ->cursor();
        }
    }

    public function refreshMap(): void
    {
        $this->updateDeviceLocation();
    }

    public function updatedSelected(): void
    {
        if (!empty($this->selected)) {
            $this->geofences = Geofence::whereIn('device_id', $this->selected)
                ->where('status', 1)->get();
            $this->dispatch('geo-fetched', $this->geofences);

            if (isset($this->dateTimeRange)) {
                $this->handleTrip();
            }

        } else {
            $this->reset('geofences', 'dateTimeRange', 'trips');
            $this->dispatch('geo-reset', $this->geofences);
            $this->dispatch('trips-reset', $this->geofences);
        }

        $this->updateDeviceLocation();
        $this->dispatch('locationUpdated');
    }

    private function updateDeviceLocation(): void
    {
        if (empty($this->selected)) {
            $this->deviceLocations = [];
            return;
        }

        $this->deviceLocations = Trip::whereIn('device_id', $this->selected)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('trips')
                    ->whereIn('device_id', $this->selected)
                    ->groupBy('device_id');
            })
            ->with(['device:id,name,serial,model', 'user:id,name,phone', 'vehicle:id,name,license_plate'])
            ->get()
            ->keyBy('device_id')
            ->toArray();

        $this->dispatch('locationUpdated');
    }


    public function handleTrip(): void
    {
        $range = explode('تا', $this->dateTimeRange);

        if (count($range) !== 2) {
            $this->addError('dateTimeRange', 'تاریخ نامعتبر می باشد.');
            return;
        }
        $this->resetErrorBag('dateTimeRange');

        $dateRange = [
            Jalalian::fromFormat('Y-m-d H:i', trim($range[0], ' '))->toCarbon(),
            Jalalian::fromFormat('Y-m-d H:i', trim($range[1], ' '))->toCarbon(),
        ];
        if (empty($this->selected)) {
            $this->addError('dateTimeRange', 'لطفا ابتدا دستگاه را انتخاب کنید.');
            return;
        };

        $this->trips = Trip::whereIn('device_id', $this->selected)
            ->whereBetween('created_at', $dateRange)
            ->with([
                'device:id,name,serial,model',
                'user:id,name,phone',
                'vehicle:id,name,license_plate'
            ])
            ->orderBy('device_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('device_id')
            ->map(function ($records) {
                // $first = $records->first();
                // $last = $records->last();

                // if ($first && $last) {
                //     $distance = calculateHaversineDistance(
                //         $first->lat,
                //         $first->long,
                //         $last->lat,
                //         $last->long
                //     );

                //     return $records->map(function ($record) use ($distance) {
                //         $record->distance = $distance;
                //         return $record;
                //     });
                // }

                return $records->map(function ($record) {
                    $record->distance = 0;
                    return $record;
                });
            });


        if ($this->trips->isEmpty()) {
            $this->alert('warning', 'سفری در این تاریخ یافت نشد!', [
                'position' => 'top',
                'timer' => 3000,
                'toast' => true,
                'customClass' => [
                    'popup' => 'colored-toast',
                    'icon' => 'white'
                ],
                'showCancelButton' => false,
                'showConfirmButton' => false
            ]);
        }

        $this->dispatch('trips-fetched', $this->trips);
    }

    public function loadMore(): void
    {
        $this->take += 10;
    }


    public function changeMode(): void
    {
        $this->onlineMode = !$this->onlineMode;

        $this->dispatch('mode-changed', $this->onlineMode);
    }
}
