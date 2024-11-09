<?php

namespace App\Livewire;

use App\Models\Device;
use App\Models\Trip;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
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

    public $trips = [];

    public function rules(): array
    {
        return [
            'selected' => 'array|max:3',
            'selected.*' => 'numeric|unique:devices,id'
        ];
    }

    public function mount(Request $request)
    {
        $this->search = $request->query('q') ?? '';


    }

    public function updatedSelected(): void
    {
        if (!empty($this->selected))
            $this->trips = Trip::where('device_id', $this->selected[0])->orderBy('created_at')->pluck('created_at', 'id')->toArray();
//        dd($this->trips);
    }

    public function render()
    {
        $devices = Device::with(['vehicle', 'user'])
            ->when($this->search !== '', function ($q) {
                $q->whereLike('name', "%{$this->search}%")->orWhereLike('serial', "%{$this->search}%");
            })
            ->orderByDesc('connected_at')
            ->cursor();

        $this->selected[] = $devices->first()->id;

        return view('livewire.map-page', [
            'devices' => $devices
        ]);
    }
}
