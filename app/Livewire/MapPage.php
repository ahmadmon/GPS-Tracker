<?php

namespace App\Livewire;

use App\Models\Device;
use App\Models\Geofence;
use App\Models\Trip;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

#[Title('نقشه')]
class MapPage extends Component
{
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
        $this->trips = collect([]);

        $this->search = $request->query('q') ?? '';
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
        } else {
            $this->reset('geofences');
            $this->dispatch('geo-reset', $this->geofences);
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
            ->with(['device:id,name,serial,model', 'user:id,name', 'vehicle:id,name,license_plate'])
            ->get()
            ->keyBy('device_id')
            ->toArray();
    }

    public function render(): View
    {
        $devices = Device::with(['vehicle', 'user'])
            ->where('status', 1)
            ->when($this->search !== '', function ($q) {
                $q->whereLike('name', "%{$this->search}%")->orWhereLike('serial', "%{$this->search}%");
            })
            ->orderByDesc('connected_at')
            ->cursor();

        $this->updateDeviceLocation();

        return view('livewire.map-page', [
            'devices' => $devices
        ]);
    }


    public function handleTrip(): void
    {
        $range = explode('تا', $this->dateTimeRange);
        $dateRange = [
            Jalalian::fromFormat('Y-m-d H:i', trim($range[0], ' '))->toCarbon(),
            Jalalian::fromFormat('Y-m-d H:i', trim($range[1], ' '))->toCarbon(),
        ];
        if (empty($this->selected)) {
            $this->addError('dateTimeRange', 'لطفا حداقل یک دستگاه را انتخاب کنید.');
            return;
        };

        $this->trips = Trip::whereIn('device_id', $this->selected)
            ->whereBetween('created_at', $dateRange)->get();

        dd($this->trips);

        $this->dispatch('trips-fetched', $this->trips);
    }
}
