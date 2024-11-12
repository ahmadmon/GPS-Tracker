<?php

namespace App\Livewire;

use App\Models\Device;
use App\Models\Trip;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('نقشه')]
class MapPage extends Component
{
    #[Url(as: 'q')]
    #[Validate('string')]
    public string $search = '';

    public array $selected = [];
    public array $deviceLocations = [];

    public $trips = [];

    public function rules(): array
    {
        return [
            'selected' => 'nullable|array|max:4',
            'selected.*' => 'nullable|numeric|unique:devices,id'
        ];
    }

    public function mount(Request $request): void
    {
        $this->search = $request->query('q') ?? '';
    }

    public function refreshMap(): void
    {
        $this->updateDeviceLocation();
    }

    public function updatedSelected(): void
    {
        $this->updateDeviceLocation();
        $this->dispatch('locationUpdated');
    }

    private function updateDeviceLocation(): void
    {
//        dd($this->selected);
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

        $this->selected[] = $devices->first()->id;

        $this->updateDeviceLocation();

        return view('livewire.map-page', [
            'devices' => $devices
        ]);
    }
}
