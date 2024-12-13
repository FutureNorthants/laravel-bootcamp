<?php

namespace App\Livewire;

use App\Models\Chirp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class MakeChirp extends Component
{
    use AuthorizesRequests;

    public string $message = '';

    public string $search = '';

    protected $rules = [
        'message' => 'required|min:3',
    ];

    public function mount()
    {
        $this->message = 'default text';
    }

    public function updatedMessage()
    {
        $this->validate();
    }

    public function save()
    {
        $this->authorize('create', Chirp::class);
        $this->validate();

        auth()->user()->chirps()->create([
            'message' => $this->message,
        ]);

        session()->flash('status', 'Chirp successfully created.');

        // return redirect()->route('dashboard');

        $this->message = '';
    }

    public function render()
    {
        return view('livewire.make-chirp', [
            'chirps' => Chirp::query()
                ->when($this->search, function (Builder $query) {
                    $query->where('message', 'like', "%{$this->search}%");
                })
                ->latest()
                ->get(),
        ]);
    }
}
