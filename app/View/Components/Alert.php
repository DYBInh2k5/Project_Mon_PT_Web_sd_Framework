<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $type;

    public string $message;

    /**
     * @var array<int, string>
     */
    public array $messages;

    /**
     * @param  string|int|float|null  $message
     * @param  array<int, string>|string|null  $messages
     */
    public function __construct(string $type = 'info', $message = '', $messages = [])
    {
        $this->type = $type;
        $this->message = (string) ($message ?? '');

        if (is_string($messages)) {
            $messages = [$messages];
        }

        $this->messages = array_values(array_filter(
            is_array($messages) ? $messages : [],
            fn ($item) => filled($item)
        ));
    }

    public function hasMessages(): bool
    {
        return $this->message !== '' || $this->messages !== [];
    }

    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}
