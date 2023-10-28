<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Notifications\StaffNotification;
use Livewire\Component;

class GetNotifications extends Component
{
    public User $user;

    public $notifications = [];

    public function mount()
    {
        $this->load();
    }

    public function hydrate()
    {
        $this->load();
    }

    protected function load()
    {
        $this->notifications = $this->user->unreadNotifications()->where('type', StaffNotification::class)->latest()->limit(50)->get();
    }

    public function render()
    {
        return view('livewire.get-notifications');
    }

    public function clearAll()
    {
        $this->user->unreadNotifications()->latest()->limit(50)->update(['read_at' => now()]);
    }

    public function clear($id) {
        $this->user->unreadNotifications()->where('id', $id)->update(['read_at' => now()]);
    }
}
